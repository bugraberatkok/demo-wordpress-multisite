<?php
/**
 * Kucuk .xlsx okuyucu.
 *
 * Bir .xlsx dosyasi, icinde XML dosyalari bulunan bir ZIP paketidir. PHP'de
 * hazir bulunan ZipArchive ve XMLReader ile okundugundan disaridan hicbir
 * kitaplik gerekmez. Dosyanin ilk sayfasi okunur.
 *
 * Kapsam disi (bu kullanim icin gerekmiyor):
 *   - Tarih bicimleri: hucre ham degeriyle (Excel seri numarasi) okunur.
 *   - Formul iceren hucrelerde Excel'in kaydettigi son deger okunur.
 *   - Birden cok sayfa: yalnizca ilk sayfa islenir.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tek seferde okunacak en fazla satir. Bunun ustu icin parcali bir mimari
 * gerekir; simdilik acikca sinirlayip kullaniciya soyluyoruz.
 */
const NWCS_XLSX_MAX_ROWS = 20000;

/**
 * "BC" gibi bir sutun harfini 0 tabanli sutun sirasina cevirir.
 */
function nwcs_xlsx_column_index( string $ref ): int {
	$letters = strtoupper( preg_replace( '/[^A-Za-z]/', '', $ref ) );
	$index   = 0;

	$length = strlen( $letters );
	for ( $i = 0; $i < $length; $i++ ) {
		$index = $index * 26 + ( ord( $letters[ $i ] ) - 64 );
	}

	return max( 0, $index - 1 );
}

/**
 * Paketin ilk sayfasinin ic yolunu bulur (orn. xl/worksheets/sheet1.xml).
 */
function nwcs_xlsx_first_sheet_path( ZipArchive $zip ): string {
	$workbook = $zip->getFromName( 'xl/workbook.xml' );
	$rels     = $zip->getFromName( 'xl/_rels/workbook.xml.rels' );

	if ( false === $workbook || false === $rels ) {
		return '';
	}

	$previous = libxml_use_internal_errors( true );
	$book     = simplexml_load_string( $workbook );
	$relation = simplexml_load_string( $rels );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	if ( ! $book || ! $relation || ! isset( $book->sheets->sheet[0] ) ) {
		return '';
	}

	$attributes = $book->sheets->sheet[0]->attributes( 'http://schemas.openxmlformats.org/officeDocument/2006/relationships' );
	$id         = isset( $attributes['id'] ) ? (string) $attributes['id'] : '';

	foreach ( $relation->Relationship as $item ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		if ( (string) $item['Id'] !== $id ) {
			continue;
		}

		$target = ltrim( (string) $item['Target'], '/' );

		// Hedef yol "worksheets/sheet1.xml" ya da "xl/worksheets/sheet1.xml" olabilir.
		return str_starts_with( $target, 'xl/' ) ? $target : 'xl/' . $target;
	}

	return '';
}

/**
 * Paylasilan metin tablosunu okur. Hucreler bu tabloya sira numarasiyla atifta
 * bulunur; tablo buyuk olabilecegi icin akisli okunur.
 */
function nwcs_xlsx_shared_strings( string $path ): array {
	$strings = array();
	$xml     = new XMLReader();

	if ( ! @$xml->open( 'zip://' . $path . '#xl/sharedStrings.xml' ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
		return $strings;
	}

	// Ilk <si> ogesine kadar ilerle.
	while ( $xml->read() && 'si' !== $xml->name ) {
		continue;
	}

	// Bundan sonrasinda yalnizca next() ile ilerliyoruz; disarida ayrica read()
	// cagirmak her ikinci ogeyi atlar.
	while ( XMLReader::ELEMENT === $xml->nodeType && 'si' === $xml->name ) {
		$node = $xml->readOuterXml();

		// Bir <si> birden cok <t> parcasina bolunmus olabilir (bicimli metin).
		if ( preg_match_all( '#<t[^>]*>(.*?)</t>#s', $node, $matches ) ) {
			$strings[] = html_entity_decode( implode( '', $matches[1] ), ENT_QUOTES | ENT_XML1, 'UTF-8' );
		} else {
			$strings[] = '';
		}

		$xml->next( 'si' );
	}

	$xml->close();

	return $strings;
}

/**
 * Dosyayi satir dizisine cevirir. Her satir, sutun sirasina gore dizilmis
 * metinlerden olusur; bos hucreler '' olarak doldurulur.
 *
 * @return array{rows: array<int, array<int, string>>, truncated: bool}|WP_Error
 */
function nwcs_xlsx_read_rows( string $path ) {
	if ( ! class_exists( 'ZipArchive' ) ) {
		return new WP_Error( 'nwcs_xlsx_zip', 'Sunucuda ZipArchive eklentisi yok; .xlsx okunamıyor.' );
	}

	$zip = new ZipArchive();

	if ( true !== $zip->open( $path ) ) {
		return new WP_Error( 'nwcs_xlsx_open', 'Dosya açılamadı. Geçerli bir .xlsx dosyası olduğundan emin olun.' );
	}

	$sheet = nwcs_xlsx_first_sheet_path( $zip );
	$zip->close();

	if ( '' === $sheet ) {
		return new WP_Error( 'nwcs_xlsx_sheet', 'Dosyada okunabilir bir sayfa bulunamadı.' );
	}

	$shared = nwcs_xlsx_shared_strings( $path );

	$xml = new XMLReader();

	if ( ! @$xml->open( 'zip://' . $path . '#' . $sheet ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
		return new WP_Error( 'nwcs_xlsx_sheet', 'Sayfa okunamadı.' );
	}

	$rows      = array();
	$truncated = false;

	// Ilk <row> ogesine kadar ilerle; sonrasi yalnizca next() ile yurur.
	while ( $xml->read() && 'row' !== $xml->name ) {
		continue;
	}

	while ( XMLReader::ELEMENT === $xml->nodeType && 'row' === $xml->name ) {
		if ( count( $rows ) >= NWCS_XLSX_MAX_ROWS ) {
			$truncated = true;
			break;
		}

		$row   = array();
		$node  = $xml->readOuterXml();
		$cells = new SimpleXMLElement( $node );

		foreach ( $cells->c as $cell ) {
			$index = nwcs_xlsx_column_index( (string) $cell['r'] );
			$type  = (string) $cell['t'];
			$value = '';

			if ( 'inlineStr' === $type ) {
				$value = isset( $cell->is->t ) ? (string) $cell->is->t : '';
			} elseif ( isset( $cell->v ) ) {
				$raw = (string) $cell->v;

				if ( 's' === $type ) {
					$value = $shared[ (int) $raw ] ?? '';
				} elseif ( 'b' === $type ) {
					$value = '1' === $raw ? 'EVET' : 'HAYIR';
				} else {
					$value = $raw;
				}
			}

			$row[ $index ] = trim( $value );
		}

		if ( $row ) {
			// Atlanan hucreleri bosla doldur, sirayi koru.
			$width = max( array_keys( $row ) ) + 1;
			$full  = array();

			for ( $i = 0; $i < $width; $i++ ) {
				$full[ $i ] = $row[ $i ] ?? '';
			}

			$rows[] = $full;
		} else {
			$rows[] = array();
		}

		$xml->next( 'row' );
	}

	$xml->close();

	// Sondaki tamamen bos satirlari at.
	while ( $rows && '' === trim( implode( '', end( $rows ) ) ) ) {
		array_pop( $rows );
	}

	return array(
		'rows'      => $rows,
		'truncated' => $truncated,
	);
}
