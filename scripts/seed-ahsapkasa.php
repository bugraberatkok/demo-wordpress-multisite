<?php
/**
 * ahsapkasa alt sitesinin icerik seed'i.
 *
 * Calistirma:
 *   wp eval-file /scripts/seed-ahsapkasa.php --url=http://localhost:8080/ahsapkasa/
 *
 * Tekrar calistirilabilir: ayni gorseli ikinci kez yuklemez, alan degerlerini
 * manifest varsayilanlarina geri yazar. Gorseller firmanin kendi sitesinden
 * gelen gercek uretim fotograflaridir; yer tutucu uretilmez.
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

if ( ! function_exists( 'nwcs_manifest' ) ) {
	WP_CLI::error( 'Network Content Studio eklentisi etkin degil.' );
}

$manifest = nwcs_manifest();

if ( ( $manifest['site_key'] ?? '' ) !== 'ahsapkasa' ) {
	WP_CLI::error( 'Bu betik yalnizca ahsapkasa temasi etkinken calisir (aktif tema: ' . get_option( 'stylesheet' ) . ').' );
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

/**
 * Sayfayi olusturur veya mevcut olani dondurur.
 */
function ahsapkasa_seed_page( string $slug, string $title ): int {
	$page = get_page_by_path( $slug );

	if ( $page ) {
		return (int) $page->ID;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_name'   => $slug,
			'post_title'  => $title,
			'post_status' => 'publish',
		)
	);

	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * /resources altindaki dosyayi medya kutuphanesine bir kez ekler.
 * Ayni slug ile daha once eklendiyse mevcut kimligi dondurur.
 */
function ahsapkasa_seed_media( string $file, string $slug, string $title, string $alt ): int {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'name'           => $slug,
			'posts_per_page' => 1,
			'post_status'    => 'inherit',
			'fields'         => 'ids',
		)
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	$source = '/resources/' . $file;

	if ( ! file_exists( $source ) ) {
		WP_CLI::warning( 'Kaynak dosya bulunamadi: ' . $source );

		return 0;
	}

	$extension = pathinfo( $file, PATHINFO_EXTENSION );
	$upload    = wp_upload_bits( $slug . '.' . $extension, null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	if ( ! empty( $upload['error'] ) ) {
		WP_CLI::warning( 'Yuklenemedi: ' . $file . ' - ' . $upload['error'] );

		return 0;
	}

	$id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => $title,
			'post_name'      => $slug,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $id ) || ! $id ) {
		WP_CLI::warning( 'Medya kaydi olusturulamadi: ' . $file );

		return 0;
	}

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );

	return (int) $id;
}

/* ---------------------------------------------------------------- *
 * 1. Sayfalar
 * ---------------------------------------------------------------- */
$pages = array(
	'hakkimizda'    => 'Hakkımızda',
	'hizmetlerimiz' => 'Hizmetlerimiz',
	'iletisim'      => 'İletişim',
);

foreach ( $pages as $slug => $title ) {
	$id = ahsapkasa_seed_page( $slug, $title );
	WP_CLI::log( sprintf( 'Sayfa: /%s/ (#%d)', $slug, $id ) );
}

// Ana sayfa blog listesi degil, front-page.php olsun.
update_option( 'show_on_front', 'posts' );

/* ---------------------------------------------------------------- *
 * 2. Gorseller
 * ---------------------------------------------------------------- */
$media = array(
	'logo'    => ahsapkasa_seed_media( 'logo coreldraw.png', 'kocist-logo', 'Koçist Orman Ürünleri logosu', 'Koçist Orman Ürünleri logosu' ),
	'atolye'  => ahsapkasa_seed_media( 'home_slide04.jpg', 'kocist-atolye', 'Atölyede ahşap sandık üretimi', 'İkitelli atölyesinde ölçüye göre üretilen ahşap sandık' ),
	'dag'     => ahsapkasa_seed_media( 'background01.jpg', 'kocist-dag', 'Sisli dağ manzarası', 'Sisler arasında dağ yamacı' ),
	'slide1'  => ahsapkasa_seed_media( 'home_slide01.jpg', 'kocist-slide-1', 'Ahşap sandık ve kasa üretimi', 'Farklı ölçülerde üretilmiş ahşap sandık ve kasalar' ),
	'slide2'  => ahsapkasa_seed_media( 'home_slide02.jpg', 'kocist-slide-2', 'Sevkiyata hazır ahşap sandıklar', 'Tesis önünde sevkiyata hazır bekleyen ahşap sandıklar' ),
	'slide3'  => ahsapkasa_seed_media( 'home_slide03.jpg', 'kocist-slide-3', 'Atölyede ahşap kasa üretimi', 'Atölyede istiflenmiş ahşap kasa ve sandıklar' ),
	'palet'   => ahsapkasa_seed_media( 'featured01.jpg', 'kocist-palet', 'Ahşap paletler', 'Üst üste dizilmiş ahşap paletler' ),
	'sandik'  => ahsapkasa_seed_media( 'featured02.jpg', 'kocist-sandik', 'Ahşap sandık', 'Sevkiyata hazır kapalı ahşap sandık' ),
	'kafes'   => ahsapkasa_seed_media( '4.jpg', 'kocist-kafes', 'Ahşap kafes', 'Ölçüye göre üretilmiş ahşap kafes' ),
);

foreach ( $media as $key => $id ) {
	WP_CLI::log( sprintf( 'Görsel: %-7s #%d', $key, $id ) );
}

/* ---------------------------------------------------------------- *
 * 3. Alan degerleri: once manifest varsayilanlari
 * ---------------------------------------------------------------- */
$content = array();

foreach ( $manifest['pages'] as $page_key => $page ) {
	foreach ( $page['components'] as $component_key => $component ) {
		foreach ( $component['fields'] as $field_key => $definition ) {
			$content[ $page_key ][ $component_key ][ $field_key ] = $definition['default'] ?? '';
		}
	}
}

/* ---------------------------------------------------------------- *
 * 4. Gercek gorselleri yerlestir
 * ---------------------------------------------------------------- */
$content['global']['header']['logo_image'] = $media['logo'];
$content['about']['head']['image']         = $media['dag'];

// Hero slayt gosterisi: uc fotograf.
$content['home']['hero']['slides'] = array(
	array( 'image' => $media['slide1'] ),
	array( 'image' => $media['slide2'] ),
	array( 'image' => $media['slide3'] ),
);

// Her urunun kapak gorseli ve buyutuldugunde gezilecek ek gorselleri.
// Demoda elimizdeki fotograflardan derlenmistir.
$galleries = array(
	array( $media['palet'],  $media['slide2'], $media['slide3'] ),
	array( $media['sandik'], $media['slide1'], $media['atolye'] ),
	array( $media['kafes'],  $media['slide3'], $media['slide1'] ),
	array( $media['atolye'], $media['slide2'], $media['slide1'] ),
);

foreach ( $content['services']['grid']['items'] as $index => $item ) {
	$set = $galleries[ $index ] ?? array();

	$content['services']['grid']['items'][ $index ]['image']   = $set[0] ?? 0;
	$content['services']['grid']['items'][ $index ]['image_2'] = $set[1] ?? 0;
	$content['services']['grid']['items'][ $index ]['image_3'] = $set[2] ?? 0;
	$content['services']['grid']['items'][ $index ]['image_4'] = $set[3] ?? 0;
}

foreach ( $content['home']['family']['items'] as $index => $item ) {
	$content['home']['family']['items'][ $index ]['image'] = $galleries[ $index ][0] ?? 0;
}

update_option( 'nwcs_content', $content );
update_option( 'nwcs_section_order', array( 'home' => array( 'intro', 'family', 'ctaband' ) ) );

WP_CLI::success( 'ahsapkasa icerigi yazildi.' );
