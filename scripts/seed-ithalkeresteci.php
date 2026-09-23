<?php
/**
 * ithalkeresteci alt sitesinin icerik seed'i.
 *
 *   wp eval-file /scripts/seed-ithalkeresteci.php --url=http://localhost:8080/ithalkeresteci/
 *
 * Fotograflar firmanin kendi sitesinden (kocist.com.tr) alinan gercek urun ve
 * sevkiyat fotograflaridir (/resources/kereste). Ortalarinda firmanin kendi
 * filigrani var; filigransiz asillari gelince panelden degistirilir.
 *
 * Hero fotografi (ornek-hero-ithal.jpg) yapay zekayla uretilmis ORNEK
 * gorseldir; sitede "Ornek gorsel" notuyla gorunur.
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

require_once __DIR__ . '/seed-kereste-lib.php';

if ( ! function_exists( 'nwcs_manifest' ) ) {
	WP_CLI::error( 'Network Content Studio eklentisi etkin degil.' );
}

$manifest = nwcs_manifest();

if ( 'ithalkeresteci' !== ( $manifest['site_key'] ?? '' ) ) {
	WP_CLI::error( 'Bu betik yalnizca ithalkeresteci temasi etkinken calisir (aktif tema: ' . get_option( 'stylesheet' ) . ').' );
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
	'hero'       => kr_seed_media( 'kereste/ornek-hero-ithal.jpg', 'ik-hero-ornek', 'Örnek görsel: kereste paketlerinin uçları', 'Üst üste istiflenmiş kereste paketlerinin boyayla işaretlenmiş uçları' ),
	'tir'        => kr_seed_media( 'kereste/ithal-kereste-thal_Kereste__4_.jpg', 'ik-tir', 'Tırda ithal kereste', 'Kayışlarla bağlanmış ithal kereste paketleri, tır kasasında' ),
	'tir_2'      => kr_seed_media( 'kereste/ithal-kereste-thal_Kereste__1_.jpg', 'ik-tir-2', 'Tıra yüklenen kereste', 'Açık kasalı tıra yüklenmiş kereste' ),
	'tir_3'      => kr_seed_media( 'kereste/ithal-kereste-thal_Kereste__2_.jpg', 'ik-tir-3', 'Kereste yükü', 'Tır kasasında istiflenmiş kereste yükü' ),
	'tir_4'      => kr_seed_media( 'kereste/ithal-kereste-thal_Kereste__3_.jpg', 'ik-tir-4', 'Sevkiyata hazır kereste', 'Depo sahasında sevkiyata hazır kereste yüklü tır' ),
	'tir_5'      => kr_seed_media( 'kereste/ithal-kereste-thal_Kereste__6_.jpg', 'ik-tir-5', 'Tırda kereste paketleri', 'Uzun tır kasasında kereste paketleri' ),
	'tir_6'      => kr_seed_media( 'kereste/ithal-kereste-thal_Kereste__8_.jpg', 'ik-tir-6', 'Yüklenmiş kereste', 'Kayışlarla sabitlenmiş kereste yükü' ),
	'forklift'   => kr_seed_media( 'kereste/ithal-kereste-thal_Kereste.jpg', 'ik-forklift', 'Forkliftle yükleme', 'Kereste paketlerini tıra yükleyen forklift' ),
	'kis'        => kr_seed_media( 'kereste/ithal-kereste-thal_Kereste__7_.jpg', 'ik-kis', 'Kışın sevkiyat', 'Karlı sahada kereste yüklü tır' ),
	'ekip'       => kr_seed_media( 'kereste/ithal-kereste-cover.jpg', 'ik-ekip', 'Kereste sevkiyatında ekip', 'Kereste yüklü tırın önünde firma çalışanları' ),
	'insaat'     => kr_seed_media( 'kereste/insaatlik-ve-catilik-kereste-Kereste_10x10.webp', 'ik-insaat', 'İnşaatlık kereste 10 × 10', 'İstiflenmiş 10 × 10 inşaatlık kereste paketleri' ),
	'insaat_2'   => kr_seed_media( 'kereste/insaatlik-ve-catilik-kereste-Kereste_10x10__2_.webp', 'ik-insaat-2', 'İnşaatlık kereste paketi', '10 × 10 kesitli kereste paketi' ),
	'insaat_3'   => kr_seed_media( 'kereste/insaatlik-ve-catilik-kereste-Kereste_10x5__1_.webp', 'ik-insaat-3', 'Çatılık kereste 5 × 10', 'Üst üste istiflenmiş 5 × 10 kereste' ),
	'insaat_4'   => kr_seed_media( 'kereste/insaatlik-ve-catilik-kereste-Kereste_10x5__2_.webp', 'ik-insaat-4', 'Kereste istifi', 'Yüksek kereste istifleri' ),
	'insaat_5'   => kr_seed_media( 'kereste/insaatlik-ve-catilik-kereste-Kereste__1_.webp', 'ik-insaat-5', 'Kereste paketi', 'Bağlanmış kereste paketi' ),
	'kalas'      => kr_seed_media( 'kereste/kalas-Kalas.webp', 'ik-kalas', 'Kalas', 'Aralıklı istiflenmiş çam kalaslar' ),
	'plywood'    => kr_seed_media( 'kereste/playwood-Playwood.jpg', 'ik-plywood', 'Plywood paketleri', 'Huş ve çam plywood levha paketleri' ),
	'plywood_2'  => kr_seed_media( 'kereste/playwood-Playwood__1_.jpg', 'ik-plywood-2', 'Film kaplı plywood', 'Film kaplı plywood levha paketi' ),
	'plywood_3'  => kr_seed_media( 'kereste/playwood-Playwood__2_.jpg', 'ik-plywood-3', 'Plywood paketi', 'Kayışla bağlanmış plywood paketi' ),
	'kontrplak'  => kr_seed_media( 'kereste/kontrplak-kapak-resmi.jpg', 'ik-kontrplak', 'Kontrplak katmanları', 'Kontrplak levhaların katmanlı kenarları' ),
	'kocist'     => kr_seed_media( 'logo coreldraw.png', 'kocist-logo', 'Koçist Orman Ürünleri logosu', 'Koçist Orman Ürünleri' ),
);

foreach ( $m as $key => $id ) {
	WP_CLI::log( sprintf( 'Görsel: %-10s #%d', $key, $id ) );
}

kr_seed_content(
	$manifest,
	array(
		'global'               => array( 'header' => array( 'parent_logo' => $m['kocist'] ) ),
		'home'                 => array(
			'hero'   => array( 'image' => $m['hero'] ),
			'supply' => array( 'photos' => array( $m['forklift'], $m['tir_4'], $m['kis'] ) ),
		),
		'about'                => array( 'story' => array( 'image' => $m['ekip'] ) ),
		'urun_ithal_kereste'   => array(
			'card'   => array( 'image' => $m['tir'] ),
			'detail' => array( 'gallery' => array( $m['tir_2'], $m['tir_3'], $m['tir_5'], $m['tir_6'] ) ),
		),
		'urun_insaatlik'       => array(
			'card'   => array( 'image' => $m['insaat'] ),
			'detail' => array( 'gallery' => array( $m['insaat_2'], $m['insaat_3'], $m['insaat_4'], $m['insaat_5'] ) ),
		),
		'urun_kalas'           => array( 'card' => array( 'image' => $m['kalas'] ) ),
		'urun_plywood'         => array(
			'card'   => array( 'image' => $m['plywood'] ),
			'detail' => array( 'gallery' => array( $m['plywood_2'], $m['plywood_3'], $m['kontrplak'] ) ),
		),
	)
);

// Kuzen site once kurulduysa onun ithalkeresteci baglantisi da yerele donsun.
kr_localize_site( 'kavakkeresteci' );

global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
flush_rewrite_rules( true );

WP_CLI::success( 'ithalkeresteci icerigi yazildi.' );
