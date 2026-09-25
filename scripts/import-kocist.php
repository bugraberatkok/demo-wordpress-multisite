<?php
/**
 * kocist.com.tr urunlerini Urun Havuzu'na aktarir ve Kocist sitesinde secer (YEREL).
 *
 * Calistirma (havuz agin ana sitesinde durur):
 *   wp eval-file /scripts/import-kocist.php --url=http://localhost:8080/            (yazar)
 *   wp eval-file /scripts/import-kocist.php --url=http://localhost:8080/ -- kuru     (yalnizca rapor)
 *
 * Kaynak: scripts/data/kocist-products.json — kocist.com.tr'nin herkese acik
 * sayfalari (menu, sitemap.xml, kategori ve urun sayfalari), 25 Eylul 2026.
 *
 * Kurallar:
 * - Ayni urun iki kez eklenmez. Havuzda ayni urun varsa (urun kodu; WOOD KOCIST
 *   kodlari "W-" onekiyle: W-DOG-M01 = DOG-M01; yoksa ayni ad) o urun kullanilir.
 * - WOOD KOCIST urunlerine dokunulmaz: kategori eklenmez, metin degismez
 *   (WOOD KOCIST menusu havuz kategorilerinden kuruluyor). Kocist'te dogru
 *   sayfaya dusmeleri manifestteki eslemeyle olur.
 * - Diger ortak urunlere (Euro Palet gibi) yalnizca Kocist kategorisi eklenir.
 * - Gorsel alinmaz. Fiyat yalnizca sitede yaziyorsa. Metinler sitedeki gibi.
 * - Tekrar calisirsa daha once aktarilan urune DOKUNMAZ (panelde yapilan
 *   duzeltmeler korunur); yalnizca yeni urunleri ekler. Kaynaktan yeniden yazmak
 *   icin: -- guncelle (bos alanlar doldurulur, durum ve kategoriler korunur).
 * - Sitenin mevcut secimi ve sirasi korunur; yeni urunler sona eklenir.
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 'Yalnizca WP-CLI ile calistirilir.' );
}

if ( ! function_exists( 'nwcs_pool_blog_id' ) || get_current_blog_id() !== nwcs_pool_blog_id() ) {
	WP_CLI::error( 'Havuz sitesinde calistirin: --url=<ag ana adresi>' );
}

$dry     = in_array( 'kuru', (array) ( $args ?? array() ), true );
$refresh = in_array( 'guncelle', (array) ( $args ?? array() ), true );
$data    = json_decode( (string) file_get_contents( __DIR__ . '/data/kocist-products.json' ), true );

if ( ! is_array( $data ) || empty( $data['products'] ) ) {
	WP_CLI::error( 'Veri dosyasi okunamadi.' );
}

/*
 * kocist.com.tr alt kategori adresi -> Kocist temasindaki menu capasi.
 * Siralar birebir ayni; adlar kocist.com.tr menusundeki gibi.
 */
$site_to_theme = array(
	'kalas'                        => 'ahsap-kalas',
	'cita'                         => 'ahsap-cita',
	'ahsap-rabita'                 => 'ahsap-rabita',
	'osb'                          => 'osb-plaka-levha',
	'kontrplak'                    => 'kontrplak-levha',
	'plywood'                      => 'plywood-levha',
	'maden-diregi'                 => 'maden-diregi-kereste',
	'takoz'                        => 'ahsap-takoz',
	'insaatlik-catilik-kereste'    => 'insaatlik-catilik-kereste',
	'ahsap-palet'                  => 'ahsap-palet',
	'ahsap-sandik'                 => 'ahsap-sandik',
	'ahsap-kafes'                  => 'ahsap-kafes',
	'triplex-bariyer'              => 'triplex-bariyer',
	'ahsap-cam-tasima-sehpasi'     => 'cam-tasima-sehpasi',
	'ahsap-ev'                     => 'ahsap-ev',
	'kamelya'                      => 'kamelya',
	'cardak'                       => 'cardak',
	'ahsap-oturma-grubu'           => 'ahsap-bank',
	'ahsap-saksi'                  => 'saksi-sebze-yatagi',
	'ahsap-salincak'               => 'ahsap-salincak',
	'ahsap-pergola'                => 'ahsap-pergola',
	'ahsapsezlong'                 => 'ahsap-sezlong',
	'ahsap-tahterevalli'           => 'ahsap-tahterevalli',
	'ahsap-yatak'                  => 'ahsap-yatak',
	'ahsap-ayak-alti'              => 'ahsap-ayak-alti',
	'hayvan-barinaklari'           => 'hayvan-barinaklari',
	'adirondack'                   => 'adirondack-sandalye',
	'celikyapi'                    => 'celik-yapi',
	'civiler'                      => 'civiler',
	'zimba-igneleri'               => 'zimba-telleri',
	'klipsler'                     => 'klipsler',
	'teller'                       => 'baglama-telleri',
	'tabancalar'                   => 'civi-tabancalari',
	'sartlandiricilar'             => 'sartlandiricilar',
	'kompresorler'                 => 'kompresorler',
	'somun-sokme'                  => 'somun-sokme-aletleri',
	'yuzey-gelistirme-diskleri'    => 'yuzey-gelistirme-diskleri',
	'maket-bicagi-ve-civili-krose' => 'maket-bicagi',
	'kulplar'                      => 'kulplar',
	'menteseler'                   => 'menteseler',
	'surguler'                     => 'surguler',
	'gonyeler'                     => 'gonyeler',
	'askilar'                      => 'aski-sabitleme',
	'vida-grubu'                   => 'vida-grubu',
	'civata-grubu'                 => 'civata-grubu',
	'mandallar'                    => 'mandallar',
	'pergola-ayaklari'             => 'pergola-ayaklari',
	'uclar'                        => 'matkap-uclari',
	'purmuz-gazli-aletler'         => 'purmuzler',
	'shingle-cati-kaplamalari'     => 'shingle-cati',
	'likit-membran-surme-yalitim'  => 'likit-membran',
	'su-yalitim-membranlari'       => 'su-yalitim-membranlari',
	// Menude olmayan, urun sayfasindaki konum yolundan gelenler.
	'civatagrubu'                  => 'civata-grubu',
	'hayvanlar-alemi'              => 'hayvan-barinaklari',
);

// Temanin menu capasi -> havuz kategori adi (Kocist manifestindeki kategori sayfalarindan).
$theme_to_pool = array();
foreach ( nwcs_manifest_for_theme( 'kocist-theme' )['pages'] ?? array() as $page ) {
	$sub  = (string) ( $page['catalog']['sub'] ?? '' );
	$pool = (string) ( $page['components']['products']['fields']['pool']['category'] ?? '' );

	if ( '' !== $sub && '' !== $pool ) {
		$theme_to_pool[ $sub ] = $pool;
	}
}

$fold = static function ( string $text ): string {
	$text = mb_strtolower( $text, 'UTF-8' );
	$text = strtr( $text, array( 'ı' => 'i', 'ğ' => 'g', 'ü' => 'u', 'ş' => 's', 'ö' => 'o', 'ç' => 'c', 'â' => 'a', 'î' => 'i', 'û' => 'u' ) );

	return trim( preg_replace( '/[^a-z0-9]+/', ' ', $text ) );
};

// Havuzun dizini: kod (W- oneki atilmis) ve ad.
$pool_by_code  = array();
$pool_by_title = array();
$pool_is_wood  = array();

foreach ( nwcs_pool_products() as $id => $product ) {
	$code = strtoupper( (string) $product['code'] );

	if ( '' !== $code ) {
		$pool_by_code[ preg_replace( '/^W-/', '', $code ) ] = (int) $id;
	}

	$pool_by_title[ $fold( preg_replace( '/^W-[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*\s+/u', '', $product['title'] ) ) ] = (int) $id;
	$pool_is_wood[ (int) $id ] = str_starts_with( $code, 'W-' );
}

// Kaynak adresiyle daha once aktarilmis urunler.
$by_source = array();
// Cope atilanlar da: panelde silinen urun tekrar calistirinca geri gelmesin.
foreach ( get_posts( array( 'post_type' => NWCS_PRODUCT_TYPE, 'post_status' => array( 'publish', 'draft', 'pending', 'private', 'trash' ), 'numberposts' => -1, 'meta_key' => '_nwcs_source', 'fields' => 'ids' ) ) as $post_id ) {
	$by_source[ (string) get_post_meta( $post_id, '_nwcs_source', true ) ] = (int) $post_id;
}

$clean_title = static function ( array $item ): string {
	$name = trim( (string) $item['name'] );
	// "Ad | KOÇİST ..." gibi sayfa basligi eki atilir.
	$name = trim( (string) preg_replace( '/\s+\|\s+.*$/u', '', $name ) );

	// Birkac sayfada ad alanina aciklama yapismis: ilk virgul ya da ilk cumleye kadar.
	if ( mb_strlen( $name ) > 90 && preg_match( '/^(.{10,80}?)(?:,| Profesyonel | Model Serisi )/u', $name, $m ) ) {
		$name = trim( $m[1] );
	}

	return $name;
};

$short_text = static function ( array $item ): string {
	foreach ( array( 'meta_description', 'short_description' ) as $key ) {
		$text = trim( preg_replace( '/\s+/u', ' ', (string) ( $item[ $key ] ?? '' ) ) );

		if ( '' !== $text ) {
			return mb_strlen( $text ) > 220 ? rtrim( mb_substr( $text, 0, 217 ), " ,.;:" ) . '…' : $text;
		}
	}

	return '';
};

$body_html = static function ( array $item ): string {
	$html = '';

	foreach ( preg_split( '/\n\s*\n/u', trim( (string) ( $item['description'] ?? '' ) ) ) as $para ) {
		$para = trim( $para );

		if ( '' !== $para ) {
			$html .= '<p>' . nl2br( esc_html( $para ) ) . "</p>\n";
		}
	}

	foreach ( (array) ( $item['spec_tables'] ?? array() ) as $table ) {
		$columns = (array) ( $table['columns'] ?? array() );
		$rows    = (array) ( $table['rows'] ?? array() );

		if ( ! $rows ) {
			continue;
		}

		$html .= "<div class=\"k-table-scroll\"><table>\n";

		if ( $columns ) {
			$html .= '<thead><tr>' . implode( '', array_map( static fn( $c ) => '<th>' . esc_html( (string) $c ) . '</th>', $columns ) ) . "</tr></thead>\n";
		}

		$html .= '<tbody>';
		foreach ( $rows as $row ) {
			$html .= '<tr>' . implode( '', array_map( static fn( $c ) => '<td>' . esc_html( (string) $c ) . '</td>', (array) $row ) ) . '</tr>';
		}
		$html .= "</tbody>\n</table></div>\n";
	}

	return $html;
};

$spec_line = static function ( array $item ): string {
	$pairs = array();

	foreach ( array_merge( (array) ( $item['specs'] ?? array() ), (array) ( $item['specs_from_description'] ?? array() ) ) as $key => $value ) {
		$key   = trim( (string) $key );
		$value = trim( preg_replace( '/\s+/u', ' ', (string) $value ) );

		if ( '' !== $key && '' !== $value && ! isset( $pairs[ $key ] ) ) {
			$pairs[ $key ] = $key . ': ' . str_replace( ';', ',', $value );
		}
	}

	return implode( '; ', $pairs );
};

$item_codes = static function ( array $item ): array {
	$codes = array_merge( array( (string) ( $item['product_code'] ?? '' ) ), (array) ( $item['product_codes'] ?? array() ) );

	if ( preg_match( '/^([A-Z]{2,4}-[A-Z0-9]+(?:-[A-Z0-9]+)*)\s/u', (string) $item['name'], $m ) ) {
		$codes[] = $m[1];
	}

	$out = array();
	foreach ( $codes as $code ) {
		$code = strtoupper( trim( (string) $code ) );

		if ( '' !== $code ) {
			$out[] = preg_replace( '/^W-/', '', $code );
		}
	}

	return array_values( array_unique( $out ) );
};

$place_of = static function ( array $item ) use ( $site_to_theme ): string {
	foreach ( (array) ( $item['category_paths'] ?? array() ) as $path ) {
		foreach ( array_reverse( (array) ( $path['slugs'] ?? array() ) ) as $slug ) {
			if ( isset( $site_to_theme[ $slug ] ) ) {
				return $site_to_theme[ $slug ];
			}
		}
	}

	$crumb = $item['breadcrumb_category'] ?? '';
	$crumb = is_array( $crumb ) ? (string) ( $crumb['slug'] ?? '' ) : (string) $crumb;

	if ( isset( $site_to_theme[ $crumb ] ) ) {
		return $site_to_theme[ $crumb ];
	}

	// Konum yolu hic olmayan cam tasima sehpasi.
	if ( str_contains( (string) $item['slug'], 'cam-tasima-sehpa' ) ) {
		return 'cam-tasima-sehpasi';
	}

	return '';
};

// Kaynak icindeki kopya: ayni ad, ya da ayni aciklama ve "Ahşap" disinda ayni ad.
$seen_desc  = array();
$seen_title = array();

$report   = array( 'yeni' => 0, 'guncel' => 0, 'ortak-wood' => 0, 'ortak-diger' => 0, 'kopya' => 0, 'yersiz' => 0 );
$selected = array();
$lines    = array();

foreach ( $data['products'] as $index => $item ) {
	$title = $clean_title( $item );
	$place = $place_of( $item );
	$pool  = $theme_to_pool[ $place ] ?? '';
	$url   = (string) $item['url'];

	if ( '' === $place ) {
		// ISPM-15 gibi menude yeri olmayan: ambalaj grubuna (havuzda "Ahşap Ambalaj").
		$pool = 'Ahşap Ambalaj';
		++$report['yersiz'];
		$lines[] = "YERSIZ  {$title} -> Ahşap Ambalaj (grup)";
	}

	$desc_key  = $fold( (string) $item['description'] );
	$title_key = trim( preg_replace( '/\bahsap\b/', '', $fold( $title ) ) );
	$title_key = preg_replace( '/\s+/', '', $title_key );
	$same      = $seen_title[ $title_key ] ?? null;

	if ( ! $same && strlen( $desc_key ) > 120 && isset( $seen_desc[ $desc_key ] ) && $seen_desc[ $desc_key ][2] === $title_key ) {
		$same = $seen_desc[ $desc_key ];
	}

	if ( $same ) {
		++$report['kopya'];
		$lines[] = "KOPYA   {$title} = {$same[1]}";

		if ( $same[0] ) {
			$selected[] = $same[0];
		}
		continue;
	}

	// Havuzdaki ayni urun: once kod, sonra ad.
	$match = 0;
	foreach ( $item_codes( $item ) as $code ) {
		if ( isset( $pool_by_code[ $code ] ) ) {
			$match = $pool_by_code[ $code ];
			break;
		}
	}

	if ( ! $match ) {
		$match = $pool_by_title[ $fold( $title ) ] ?? 0;
	}

	if ( $match && ! isset( $by_source[ $url ] ) ) {
		if ( $pool_is_wood[ $match ] ?? false ) {
			++$report['ortak-wood'];
			$lines[] = "ORTAK   {$title} = havuz #{$match} (WOOD KOCIST, dokunulmadi)";
		} else {
			// WOOD KOCIST disindaki ortak urun (Euro Palet gibi): yalnizca Kocist kategorisi eklenir.
			++$report['ortak-diger'];
			$lines[] = "ORTAK   {$title} = havuz #{$match} (+ kategori {$pool})";

			if ( ! $dry && '' !== $pool ) {
				$term = get_term_by( 'name', $pool, NWCS_PRODUCT_TAX );
				if ( $term ) {
					wp_set_object_terms( $match, array( (int) $term->term_id ), NWCS_PRODUCT_TAX, true );
				}
			}
		}

		$selected[] = $match;
		$seen_desc[ $desc_key ] = $seen_title[ $title_key ] = array( $match, $title, $title_key );
		continue;
	}

	$id = $by_source[ $url ] ?? 0;
	++$report[ $id ? 'guncel' : 'yeni' ];

	// Daha once aktarilmis: panelde duzeltilmis olabilir, dokunulmaz.
	if ( $id && ! $refresh ) {
		if ( 'trash' !== get_post_status( $id ) ) {
			$selected[] = $id;
		}
		$seen_desc[ $desc_key ] = $seen_title[ $title_key ] = array( $id, $title, $title_key );
		continue;
	}

	if ( $dry ) {
		$seen_desc[ $desc_key ] = $seen_title[ $title_key ] = array( 0, $title, $title_key );
		continue;
	}

	$postarr = array(
		'post_type'    => NWCS_PRODUCT_TYPE,
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_name'    => sanitize_title( (string) $item['slug'] ),
		'post_content' => wp_kses_post( $body_html( $item ) ),
		'menu_order'   => 1000 + (int) $index,
	);

	if ( $id ) {
		// guncelle: metin kaynaktan yeniden yazilir; yayin durumu ve sira korunur.
		unset( $postarr['post_status'], $postarr['menu_order'] );
		$postarr['ID'] = $id;
		wp_update_post( wp_slash( $postarr ) );
	} else {
		$id = (int) wp_insert_post( wp_slash( $postarr ) );
	}

	if ( ! $id ) {
		WP_CLI::warning( 'Kaydedilemedi: ' . $title );
		continue;
	}

	// Yalnizca sitede tek ve acik yazan kod (DOG-V06, FAL3); sitenin sira numarasi kod degildir.
	$code = nwcs_normalize_product_code( (string) ( $item['product_code'] ?? '' ) );

	// Kod baska bir urunde varsa (havuzda tekil olmali) yazilmaz.
	if ( '' !== $code && nwcs_product_id_by_code( $code, $id ) ) {
		$code = '';
	}

	// Bos olan alanlar doldurulur; panelde yazilmis deger ezilmez.
	$meta = array(
		'_nwcs_source' => $url,
		'_nwcs_code'   => $code,
		'_nwcs_short'  => sanitize_textarea_field( $short_text( $item ) ),
		'_nwcs_price'  => sanitize_text_field( (string) ( $item['price'] ?? '' ) ),
		'_nwcs_spec'   => sanitize_text_field( $spec_line( $item ) ),
	);

	foreach ( $meta as $key => $value ) {
		if ( '' === (string) get_post_meta( $id, $key, true ) ) {
			update_post_meta( $id, $key, wp_slash( $value ) );
		}
	}

	if ( '' !== $pool ) {
		$term = get_term_by( 'name', $pool, NWCS_PRODUCT_TAX );

		if ( ! $term ) {
			$made = wp_insert_term( $pool, NWCS_PRODUCT_TAX, array( 'slug' => '' !== $place ? $place : sanitize_title( $pool ) ) );
			$term = is_wp_error( $made ) ? null : get_term( (int) $made['term_id'], NWCS_PRODUCT_TAX );
		}

		if ( $term && ! is_wp_error( $term ) ) {
			wp_set_object_terms( $id, array( (int) $term->term_id ), NWCS_PRODUCT_TAX, true );
		}
	}

	$selected[] = $id;
	$seen_desc[ $desc_key ] = $seen_title[ $title_key ] = array( $id, $title, $title_key );
}

foreach ( $lines as $line ) {
	WP_CLI::log( $line );
}

WP_CLI::log( sprintf( 'Rapor: %s', wp_json_encode( $report ) ) );

if ( $dry ) {
	WP_CLI::success( 'Kuru calisma: hicbir sey yazilmadi.' );
	return;
}

nwcs_pool_flush_cache();

$site = get_site_by_path( get_network()->domain, get_network()->path . 'kocist/' );

if ( $site && '/' !== $site->path ) {
	switch_to_blog( (int) $site->blog_id );
	$current = nwcs_site_product_settings();

	// Panelde yapilan secim ve sira korunur; yalnizca ilk kez gelen urunler sona eklenir.
	$known = array_map( 'intval', (array) get_option( 'kocist_import_seen', array() ) );
	$new   = array_values( array_diff( array_unique( $selected ), $current['selected'], $known ) );

	nwcs_save_site_product_settings(
		array(
			'mode'      => 'selected',
			'selected'  => array_merge( $current['selected'], $new ),
			'overrides' => $current['overrides'],
		)
	);
	update_option( 'kocist_import_seen', array_values( array_unique( array_merge( $known, $selected ) ) ), false );
	restore_current_blog();
	WP_CLI::log( sprintf( '/kocist/ sitesine %d yeni urun eklendi (toplam %d).', count( $new ), count( $current['selected'] ) + count( $new ) ) );
}

WP_CLI::success( 'Tamam.' );
