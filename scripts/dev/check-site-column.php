<?php
/**
 * Yerel deneme: kategori Excel'inde SİTE sutunu. Taslakta SİTE dolu gelir;
 * yazimlar (Hepsi, ad, anahtar, alan adi, Turkce/bosluk farki) taninir,
 * anlasilmayan deger satir hatasi; bos hucre degistirmez. Uygulanan
 * degisiklik urunu yazilmayan sitede gizler, yazilanda gosterir; geri alma
 * site ayarlarini (secim + istisnalar) birebir eski haline getirir.
 *
 *   docker compose exec -T wordpress php /scripts/dev/check-site-column.php [kategori]
 *
 * "dur" ikinci argumaniyla uygulamadan sonra durur (sayfa denemesi icin);
 * geri alma icin ayni komut "geri" ile calistirilir.
 */

define( 'WP_ADMIN', true );
define( 'WP_NETWORK_ADMIN', true );
$_SERVER['HTTP_HOST']   = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/wp-admin/network/admin.php';

require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/admin.php';

$fail = 0;
function check( string $label, bool $ok, string $got = '' ): void {
	global $fail;
	echo ( $ok ? 'OK   ' : 'FAIL ' ) . $label . ( $ok || '' === $got ? '' : '  (' . $got . ')' ) . "\n";
	$fail += $ok ? 0 : 1;
}

// Eklentinin kendi kodunda uyari/notice olmasin.
$notices = array();
set_error_handler(
	static function ( int $no, string $message, string $file, int $line ) use ( &$notices ): bool {
		// @ ile susturulanlar (xlsx okuyucunun istege bagli sharedStrings denemesi) sayilmaz.
		if ( ( error_reporting() & $no ) && str_contains( $file, 'network-content-studio' ) ) {
			$notices[] = "$message @ " . basename( $file ) . ":$line";
		}

		return false;
	},
	E_ALL
);

wp_set_current_user( get_user_by( 'login', get_super_admins()[0] )->ID );

$slug  = $argv[1] ?? 'kedi-yuvalari';
$phase = $argv[2] ?? 'hepsi';
$sites = nwcs_sync_sites();
$state = '/tmp/nwcs-site-column.json';

/** Site ayarlarinin ham hali (secim + istisnalar). */
function site_options(): array {
	$out = array();

	foreach ( array_keys( nwcs_sync_sites() ) as $blog_id ) {
		$out[ $blog_id ] = array(
			'mode'      => get_blog_option( $blog_id, NWCS_OPTION_MODE ),
			'selected'  => get_blog_option( $blog_id, NWCS_OPTION_SELECTED ),
			'overrides' => get_blog_option( $blog_id, NWCS_OPTION_OVERRIDES ),
		);
	}

	return $out;
}

/** Taslagi indirir, satirlari $edit ile degistirip yeni dosyaya yazar. */
function edited_template( string $slug, callable $edit ): string {
	$built = nwcs_sync_build_template( $slug );
	$rows  = nwcs_xlsx_read_rows( $built['path'], NWCS_SYNC_SHEET )['rows'];
	$info  = nwcs_xlsx_read_rows( $built['path'], NWCS_SYNC_INFO )['rows'];
	wp_delete_file( $built['path'] );

	$path = nwcs_import_temp_xlsx();
	nwcs_xlsx_write(
		$path,
		array(
			array( 'name' => NWCS_SYNC_SHEET, 'rows' => $edit( $rows ) ),
			array( 'name' => NWCS_SYNC_INFO, 'rows' => $info, 'hidden' => true, 'header' => false ),
		)
	);

	return $path;
}

$by_key = array();
foreach ( nwcs_catalog_sites() as $blog_id => $site ) {
	$by_key[ $site['site_key'] ] = (int) $blog_id;
}
$k  = $by_key['kocist'] ?? 0;
$wk = $by_key['woodkocist'] ?? 0;

if ( 'geri' === $phase ) {
	$saved  = json_decode( (string) file_get_contents( $state ), true );
	$undo   = nwcs_history_undo( (string) $saved['record'] );
	check( 'geri al', ! is_wp_error( $undo ), is_wp_error( $undo ) ? $undo->get_error_message() : '' );
	check( 'geri al: site ayarları birebir eski hâlinde', site_options() == $saved['options'] );
	switch_to_blog( nwcs_pool_blog_id() );
	wp_delete_post( (int) $saved['new'], true );
	restore_current_blog();
	update_site_option( NWCS_HISTORY, $saved['history'] );
	nwcs_pool_flush_cache();
	wp_delete_file( $state );
	echo $notices ? "\nNOTICE:\n" . implode( "\n", $notices ) . "\n" : "\nE_ALL: eklentide uyarı yok\n";
	echo $fail ? "\n$fail hata\n" : "\nHepsi gecti\n";
	exit( $fail ? 1 : 0 );
}

/* ---------- Okuma ---------- */
check( 'siteler: Koçist ve WOOD KOCIST', $k && $wk && 2 === count( $sites ), wp_json_encode( $sites, JSON_UNESCAPED_UNICODE ) );

$cases = array(
	'WOOD KOCIST'                   => array( $wk ),
	'wood kocist'                   => array( $wk ),
	'woodkocist'                    => array( $wk ),
	'https://www.woodkocist.com.tr/' => array( $wk ),
	'KOÇİST'                        => array( $k ),
	'kocist, woodkocist'            => array( $k, $wk ),
	'WOOD KOCIST; Koçist'           => array( $k, $wk ),
	'Hepsi'                         => array( $k, $wk ),
	'HEPSİ'                         => array( $k, $wk ),
	'tümü'                          => array( $k, $wk ),
	''                              => null,
	' , '                           => null,
);
foreach ( $cases as $value => $want ) {
	$got = nwcs_sync_parse_sites( (string) $value );
	check( "okuma: '$value'", ! is_wp_error( $got ) && $got === $want, is_wp_error( $got ) ? $got->get_error_message() : wp_json_encode( $got ) );
}
$bad = nwcs_sync_parse_sites( 'kocis' );
check( 'okuma: bilinmeyen değer hata', is_wp_error( $bad ) && 'SİTE değeri anlaşılamadı: “kocis”. Yazılabilecekler: Hepsi, Koçist, WOOD KOCIST (virgülle birden fazla).' === $bad->get_error_message(), is_wp_error( $bad ) ? $bad->get_error_message() : '' );
check( 'ekler', 'Koçist’ten' === nwcs_ablative( 'Koçist' ) && 'WOOD KOCIST’e' === nwcs_dative( 'WOOD KOCIST' ) && 'Ahşap Kasa’ya' === nwcs_dative( 'Ahşap Kasa' ) );

/* ---------- Taslak ---------- */
$built  = nwcs_sync_build_template( $slug );
$rows   = nwcs_xlsx_read_rows( $built['path'], NWCS_SYNC_SHEET )['rows'];
wp_delete_file( $built['path'] );
check( 'taslak: 4. sütun SİTE', 'SİTE' === $rows[0][3], (string) $rows[0][3] );
check( 'taslak: ÜRÜN KODU yine 2. sütun', 'ÜRÜN KODU *' === $rows[0][1] );
$texts = array();
foreach ( array_slice( $rows, 1 ) as $row ) {
	$texts[ (int) $row[0] ] = (string) $row[3];
	check( 'taslak: SİTE değeri görünürlükle aynı (' . $row[2] . ')', $row[3] === nwcs_sync_sites_text( nwcs_sync_product_sites( (int) $row[0] ) ), (string) $row[3] );
}
echo '     taslak SİTE değerleri: ' . wp_json_encode( array_count_values( $texts ), JSON_UNESCAPED_UNICODE ) . "\n";
check( 'Nasıl kullanılır: SİTE satırı', (bool) preg_grep( '/^SİTE: ürünün görüneceği site\. Hepsi ya da site adları virgülle: Koçist, WOOD KOCIST\. Boş bırakırsanız değişmez/u', array_column( nwcs_sync_help_rows( 'X' ), 0 ) ) );

/* ---------- Plan ---------- */
$ids = array_keys( $texts );
[ $p_wk, $p_both, $p_bad, $p_empty ] = array_slice( $ids, 0, 4 );

// Baslangic: $p_both yalnizca Koçist'te olsun (gizle), digerleri oldugu gibi.
$history = get_site_option( NWCS_HISTORY );
$options = site_options();

$path = edited_template(
	$slug,
	static function ( array $rows ) use ( $p_wk, $p_both, $p_bad, $p_empty ): array {
		$width = count( $rows[0] );
		foreach ( $rows as $i => $row ) {
			$id = (int) $row[0];
			if ( $id === $p_wk ) {
				$rows[ $i ][3] = 'WOOD KOCIST';
			} elseif ( $id === $p_both ) {
				$rows[ $i ][3] = 'kocist,woodkocist';
			} elseif ( $id === $p_bad ) {
				$rows[ $i ][3] = 'kocis';
			} elseif ( $id === $p_empty ) {
				$rows[ $i ][3] = '';
			}
		}
		$new    = array_fill( 0, $width, '' );
		$new[1] = 'ZZ-SITE-01';
		$new[2] = 'Deneme SİTE ürünü';
		$new[3] = 'WOOD KOCIST';
		// Zorunlu basliklar: ilk satirdan kopyala.
		foreach ( range( 6, $width - 1 ) as $col ) {
			$new[ $col ] = $rows[1][ $col ];
		}
		$rows[] = $new;

		return $rows;
	}
);

$plan = nwcs_sync_plan( $path, 'site-deneme.xlsx' );
wp_delete_file( $path );
check( 'plan okundu', ! is_wp_error( $plan ), is_wp_error( $plan ) ? $plan->get_error_message() : '' );

$errors = array_column( (array) $plan['errors'], 'message', 'line' );
check( 'bilinmeyen SİTE: satır hatası', 1 === count( $errors ) && str_starts_with( (string) reset( $errors ), 'SİTE değeri anlaşılamadı: “kocis”' ), wp_json_encode( $errors, JSON_UNESCAPED_UNICODE ) );
check( 'hatalı satırın ürünü çöpe gitmez', ! $plan['trash'] );

$update = array_column( (array) $plan['update'], null, 'id' );
check( 'WOOD KOCIST yazılan ürün: güncellenecek', isset( $update[ $p_wk ] ) );
check( 'önizleme: “Koçist’ten kalkacak”', 'Koçist’ten kalkacak' === ( $update[ $p_wk ]['moves'] ?? '' ), (string) ( $update[ $p_wk ]['moves'] ?? '' ) );
check( 'önizleme: Site satırı', in_array( array( 'Site', 'Hepsi', 'WOOD KOCIST', 'site' ), (array) ( $update[ $p_wk ]['changes'] ?? array() ), true ) );
check( 'iki sitede olan ve iki site yazılan ürün: değişmez', ! isset( $update[ $p_both ] ) );
check( 'boş SİTE: değişmez', ! isset( $update[ $p_empty ] ) );
check( 'yeni ürün: sitesi WOOD KOCIST', array( $wk ) === ( $plan['create'][0]['data']['sites'] ?? null ) );

ob_start();
nwcs_render_sync_preview( $plan );
$html = ob_get_clean();
check( 'önizleme ekranı: kalkacak yazısı', str_contains( $html, 'Koçist’ten kalkacak' ) );
check( 'önizleme ekranı: yeni üründe Site', str_contains( $html, 'Site: WOOD KOCIST' ) );

/* ---------- Uygula ---------- */
$record = nwcs_sync_apply( $plan, false );
$new    = (int) ( $record['created'][0] ?? 0 );
check( 'uygula: yeni ürün eklendi', $new > 0 );
check( 'uygula: ürün yalnızca WOOD KOCIST’te', array( $wk ) === nwcs_sync_product_sites( $p_wk ) );
check( 'uygula: yeni ürün yalnızca WOOD KOCIST’te', array( $wk ) === nwcs_sync_product_sites( $new ) );
check( 'uygula: boş SİTE ürünü aynı', nwcs_sync_product_sites( $p_empty ) === array( $k, $wk ) );
check( 'uygula: Koçist listesinde yok', ! in_array( $p_wk, array_map( static fn( $p ) => (int) $p['id'], nwcs_site_products( $k ) ), true ) );
check( 'uygula: WOOD KOCIST listesinde var', in_array( $p_wk, array_map( static fn( $p ) => (int) $p['id'], nwcs_site_products( $wk ) ), true ) );
check( 'kayıt: 2 ürünün sitesi değişti', 2 === ( $record['counts']['sites'] ?? 0 ), (string) ( $record['counts']['sites'] ?? '' ) );
check( 'Son işlemler özeti', str_contains( nwcs_history_describe( nwcs_history_latest( NWCS_SYNC_KIND ) )['facts'], '2 ürünün sitesi değişti' ) );

$pool = nwcs_pool_products();
echo '     Koçist adresi: ' . nwcs_product_url( nwcs_product_site_slug( $p_wk, (string) $pool[ $p_wk ]['slug'], $k ), $k ) . "\n";
echo '     WK adresi:     ' . nwcs_product_url( nwcs_product_site_slug( $p_wk, (string) $pool[ $p_wk ]['slug'], $wk ), $wk ) . "\n";

if ( 'dur' === $phase ) {
	file_put_contents( $state, wp_json_encode( array( 'record' => $record['id'], 'options' => $options, 'history' => $history, 'new' => $new ) ) );
	echo $notices ? "\nNOTICE:\n" . implode( "\n", $notices ) . "\n" : "\nE_ALL: eklentide uyarı yok\n";
	echo $fail ? "\n$fail hata\n" : "\nUygulandı; geri almak için: ... $slug geri\n";
	exit( $fail ? 1 : 0 );
}

/* ---------- Ikinci tur: Hepsi geri getirir ---------- */
$path  = edited_template(
	$slug,
	static function ( array $rows ) use ( $p_wk ): array {
		foreach ( $rows as $i => $row ) {
			if ( (int) $row[0] === $p_wk ) {
				$rows[ $i ][3] = 'Hepsi';
			}
		}

		return $rows;
	}
);
$plan2 = nwcs_sync_plan( $path, 'site-deneme-2.xlsx' );
wp_delete_file( $path );
$up2 = array_column( (array) $plan2['update'], null, 'id' );
check( 'ikinci tur: “Koçist’e eklenecek”', 'Koçist’e eklenecek' === ( $up2[ $p_wk ]['moves'] ?? '' ), (string) ( $up2[ $p_wk ]['moves'] ?? '' ) );
$record2 = nwcs_sync_apply( $plan2, false );
check( 'ikinci tur: Hepsi -> iki sitede', array( $k, $wk ) === nwcs_sync_product_sites( $p_wk ) );
$undo2 = nwcs_history_undo( (string) $record2['id'] );
check( 'ikinci tur geri: yine yalnızca WOOD KOCIST', ! is_wp_error( $undo2 ) && array( $wk ) === nwcs_sync_product_sites( $p_wk ) );

/* ---------- Geri al ---------- */
$undo = nwcs_history_undo( (string) $record['id'] );
check( 'geri al', ! is_wp_error( $undo ), is_wp_error( $undo ) ? $undo->get_error_message() : '' );
check( 'geri al: site ayarları birebir eski hâlinde', site_options() == $options );
check( 'geri al: ürün yine iki sitede', array( $k, $wk ) === nwcs_sync_product_sites( $p_wk ) );

switch_to_blog( nwcs_pool_blog_id() );
wp_delete_post( $new, true );
restore_current_blog();
update_site_option( NWCS_HISTORY, $history );
nwcs_pool_flush_cache();

echo $notices ? "\nNOTICE:\n" . implode( "\n", $notices ) . "\n" : "\nE_ALL: eklentide uyarı yok\n";
echo $fail ? "\n$fail hata\n" : "\nHepsi gecti\n";
