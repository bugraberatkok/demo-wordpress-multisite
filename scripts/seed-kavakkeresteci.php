<?php
/**
 * kavakkeresteci alt sitesinin icerik seed'i.
 *
 *   wp eval-file /scripts/seed-kavakkeresteci.php --url=http://localhost:8080/kavakkeresteci/
 *
 * Fotograflar firmanin kendi sitesinden (kocist.com.tr) alinan gercek urun
 * fotograflaridir (/resources/kereste). Altlarinda firmanin kendi filigrani
 * var; filigransiz asillari gelince panelden degistirilir.
 *
 * Hero fotografi (ornek-hero-kavak.jpg) yapay zekayla uretilmis ORNEK
 * gorseldir; sitede "Ornek gorsel" notuyla gorunur.
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

require_once __DIR__ . '/seed-kereste-lib.php';

if ( ! function_exists( 'nwcs_manifest' ) ) {
	WP_CLI::error( 'Network Content Studio eklentisi etkin degil.' );
}

$manifest = nwcs_manifest();

if ( 'kavakkeresteci' !== ( $manifest['site_key'] ?? '' ) ) {
	WP_CLI::error( 'Bu betik yalnizca kavakkeresteci temasi etkinken calisir (aktif tema: ' . get_option( 'stylesheet' ) . ').' );
}

// WordPress'in ornek yazisi ve sayfasi.
foreach ( array( 'post' => 'hello-world', 'page' => 'sample-page' ) as $type => $slug ) {
	$default = get_page_by_path( $slug, OBJECT, $type );
	if ( $default ) {
		wp_delete_post( $default->ID, true );
	}
}

kr_seed_pages( $manifest );

$m = array(
	'kavak'    => kr_seed_media( 'kereste/kavak-kereste-Kavak_Kereste.jpg', 'kk-kavak', 'Kavak kereste paketi', 'Üst üste istiflenmiş kavak kereste paketi' ),
	'kavak_2'  => kr_seed_media( 'kereste/kavak-kereste-Kavak_Kereste__2_.webp', 'kk-kavak-2', 'Kavak kereste istifleri', 'Yan yana istiflenmiş kavak kereste paketleri' ),
	'kavak_3'  => kr_seed_media( 'kereste/kavak-kereste-Kavak_Kereste__1_.webp', 'kk-kavak-3', 'Kavak kereste istifi', 'Çapraz istiflenmiş kavak kereste' ),
	'paletlik' => kr_seed_media( 'kereste/kereste-Kereste_Paletlik.webp', 'kk-paletlik', 'Paletlik kereste', 'Palet üretimi için istiflenmiş ince kereste' ),
	'cita'     => kr_seed_media( 'kereste/kavak-kereste-Kavak_Kereste__20_.webp', 'kk-cita', 'Kavak çıta', 'Beyaz zeminde yan yana kavak çıtalar' ),
	'cita_2'   => kr_seed_media( 'kereste/kavak-kereste-Kereste.webp', 'kk-cita-2', 'Çıta paketi', 'İstiflenmiş açık renkli çıtalar' ),
	'takoz'    => kr_seed_media( 'kereste/takoz-Ahsap_Takoz__15_.webp', 'kk-takoz', 'Ahşap takozlar', 'Palet üzerinde dizili masif ahşap takozlar' ),
	'takoz_2'  => kr_seed_media( 'kereste/takoz-Ahsap_Takoz__10_.webp', 'kk-takoz-2', 'Kama takozlar', 'Farklı boylarda kama biçimli ahşap takozlar' ),
	'takoz_3'  => kr_seed_media( 'kereste/takoz-Ahsap_Takoz__12_.webp', 'kk-takoz-3', 'Geniş kama takozlar', 'Yan yana dizili geniş kama takozlar' ),
	'takoz_4'  => kr_seed_media( 'kereste/takoz-Ahsap_Takoz__31_.webp', 'kk-takoz-4', 'Üçgen takoz', 'Üçgen kesitli masif ahşap takoz' ),
	'osb'      => kr_seed_media( 'kereste/osb-OSB__1_.jpg', 'kk-osb', 'OSB levha paketi', 'Üst üste istiflenmiş OSB levhalar' ),
	'osb_2'    => kr_seed_media( 'kereste/osb-OSB.jpg', 'kk-osb-2', 'OSB levhalar', 'Paket halinde OSB levhalar' ),
	'osb_3'    => kr_seed_media( 'kereste/osb-OSB__2_.jpg', 'kk-osb-3', 'OSB yüzeyi', 'Yakından OSB levha yüzeyi ve kenarları' ),
	'osb_4'    => kr_seed_media( 'kereste/osb-OSB__3_.jpg', 'kk-osb-4', 'OSB istifi', 'Düzgün istiflenmiş OSB levha paketi' ),
	'hero'     => kr_seed_media( 'kereste/ornek-hero-kavak.jpg', 'kk-hero-ornek', 'Örnek görsel: kavak kereste istifi', 'Açık renkli kavak kereste istifi, uçlarında yeşil boya işaretleri' ),
);

foreach ( $m as $key => $id ) {
	WP_CLI::log( sprintf( 'Görsel: %-10s #%d', $key, $id ) );
}

kr_seed_content(
	$manifest,
	array(
		'home'       => array( 'hero' => array( 'image' => $m['hero'] ) ),
		'about'      => array( 'story' => array( 'image' => $m['kavak_2'] ) ),
		'urun_kavak' => array(
			'card'   => array( 'image' => $m['kavak_2'] ),
			'detail' => array( 'gallery' => array( $m['kavak'], $m['kavak_3'], $m['paletlik'] ) ),
		),
		'urun_cita'  => array(
			'card'   => array( 'image' => $m['cita'] ),
			'detail' => array( 'gallery' => array( $m['cita_2'] ) ),
		),
		'urun_takoz' => array(
			'card'   => array( 'image' => $m['takoz'] ),
			'detail' => array( 'gallery' => array( $m['takoz_2'], $m['takoz_3'], $m['takoz_4'] ) ),
		),
		'urun_osb'   => array(
			'card'   => array( 'image' => $m['osb'] ),
			'detail' => array( 'gallery' => array( $m['osb_2'], $m['osb_3'], $m['osb_4'] ) ),
		),
	)
);

// Kuzen site once kurulduysa onun kavakkeresteci baglantisi da yerele donsun.
kr_localize_site( 'ithalkeresteci' );

global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
flush_rewrite_rules( true );

WP_CLI::success( 'kavakkeresteci icerigi yazildi.' );
