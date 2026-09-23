<?php
/**
 * istanbulpaletci alt sitesinin icerik seed'i.
 *
 * Calistirma:
 *   wp eval-file /scripts/seed-istanbulpaletci.php --url=http://localhost:8080/istanbulpaletci/
 *
 * Tekrar calistirilabilir: ayni gorseli, sayfayi ve yaziyi ikinci kez
 * olusturmaz; alan degerlerini manifest varsayilanlarina geri yazar.
 *
 * Gorseller firmanin kendi sitesindeki fotograflardir (/resources). Ana sayfa
 * slaytlari kolaj oldugu icin (tek gorselde 3-8 fotograf) icinden tek tek
 * fotograf kirpilarak eklenir. Tema demosundan kalan stok gorseller
 * (home1-welcome, home1-contact-pic) ve kardes siteyle ayni manzara
 * fotograflari kullanilmaz.
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

if ( ! function_exists( 'nwcs_manifest' ) ) {
	WP_CLI::error( 'Network Content Studio eklentisi etkin degil.' );
}

$manifest = nwcs_manifest();

if ( ( $manifest['site_key'] ?? '' ) !== 'istanbulpaletci' ) {
	WP_CLI::error( 'Bu betik yalnizca istanbulpaletci temasi etkinken calisir (aktif tema: ' . get_option( 'stylesheet' ) . ').' );
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

/**
 * Sayfayi olusturur veya mevcut olani dondurur. Alt sayfalar icin $parent.
 */
function ip_seed_page( string $slug, string $title, int $parent = 0 ): int {
	$path = $slug;

	if ( $parent ) {
		$path = get_page_uri( $parent ) . '/' . $slug;
	}

	$page = get_page_by_path( $path );

	if ( $page ) {
		return (int) $page->ID;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_name'   => $slug,
			'post_title'  => $title,
			'post_status' => 'publish',
			'post_parent' => $parent,
		)
	);

	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * /resources altindaki dosyayi medya kutuphanesine bir kez ekler.
 *
 * $crop verilirse [x, y, genislik, yukseklik] alani kirpilir; kolajlarin
 * icinden tek fotograf cikarmak icin. Genis kirpimlar 1800 piksele indirilir.
 */
function ip_seed_media( string $file, string $slug, string $title, string $alt, ?array $crop = null ): int {
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

	$extension = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
	$binary    = file_get_contents( $source ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	if ( $crop ) {
		$image = imagecreatefromstring( $binary );
		$part  = $image ? imagecrop( $image, array( 'x' => $crop[0], 'y' => $crop[1], 'width' => $crop[2], 'height' => $crop[3] ) ) : false;

		if ( ! $part ) {
			WP_CLI::warning( 'Kirpilamadi: ' . $file );

			return 0;
		}

		if ( imagesx( $part ) > 1800 ) {
			$part = imagescale( $part, 1800 );
		}

		ob_start();
		imagejpeg( $part, null, 86 );
		$binary    = ob_get_clean();
		$extension = 'jpg';
	}

	$upload = wp_upload_bits( $slug . '.' . $extension, null, $binary );

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
 * 1. Kalici baglantilar ve WordPress'in ornek icerigi
 * ---------------------------------------------------------------- */
global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );

// Yeni acilan sitenin "Merhaba dunya" yazisi ve ornek sayfasi.
foreach ( array( 'post' => array( 'hello-world', 'merhaba-dunya' ), 'page' => array( 'sample-page', 'ornek-sayfa' ) ) as $type => $slugs ) {
	foreach ( $slugs as $slug ) {
		$default = get_page_by_path( $slug, OBJECT, $type );

		if ( $default ) {
			wp_delete_post( $default->ID, true );
			WP_CLI::log( sprintf( 'WordPress ornegi silindi: %s /%s/', $type, $slug ) );
		}
	}
}

/* ---------------------------------------------------------------- *
 * 2. Sayfalar: 10 sayfa, canli sitedeki adreslerle ayni
 * ---------------------------------------------------------------- */
$front = ip_seed_page( 'anasayfa', 'Anasayfa' );
$hub   = ip_seed_page( 'urunlerimiz', 'Ürünlerimiz' );
$blog  = ip_seed_page( 'blog', 'Blog' );

ip_seed_page( 'hakkimizda', 'Hakkımızda' );
ip_seed_page( 'iletisim', 'İletişim' );

// Urun sayfalari manifestteki yollardan turetilir; adres tek yerde yazili.
foreach ( $manifest['pages'] as $page ) {
	if ( 'product' !== ( $page['template'] ?? '' ) ) {
		continue;
	}

	$slug = basename( untrailingslashit( $page['path'] ) );
	ip_seed_page( $slug, $page['components']['card']['fields']['name']['default'], $hub );
}

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $front );
update_option( 'page_for_posts', $blog );

foreach ( get_pages() as $page ) {
	WP_CLI::log( sprintf( 'Sayfa: /%s/ (#%d)', get_page_uri( $page ), $page->ID ) );
}

/* ---------------------------------------------------------------- *
 * 3. Gorseller
 * ---------------------------------------------------------------- */
$media = array(
	'logo'        => ip_seed_media( 'istanbul_paletci.png', 'ip-logo', 'İstanbul Paletçi logosu', 'İstanbul Paletçi' ),
	'logo_white'  => ip_seed_media( 'istanbul_paletci_logow.png', 'ip-logo-beyaz', 'İstanbul Paletçi logosu (koyu zemin)', 'İstanbul Paletçi' ),
	'palet1'      => ip_seed_media( 'palet_1.jpg', 'ip-palet-1', 'Ahşap palet', 'Beyaz zeminde ahşap palet' ),
	'palet2'      => ip_seed_media( 'palet_2.jpg', 'ip-palet-2', 'İkinci el ahşap palet', 'Kullanılmış, yıpranmış ahşap palet' ),
	'palet3'      => ip_seed_media( 'palet_3.jpg', 'ip-palet-3', 'Damgalı ahşap palet', 'Ayağında damga bulunan ahşap palet' ),
	// Hero arka plani: kuzen Sanayi Palet'in hero fotografi (musteri onayli).
	'saha'        => ip_seed_media( 'palet-duvari.jpg', 'ip-palet-duvari', 'Palet duvarı', 'Üst üste istiflenmiş, farklı renklerde yüzlerce ahşap palet' ),
	'ahsap_palet' => ip_seed_media( 'ahsap_palet.jpg', 'ip-ahsap-palet', 'Ahşap palet', 'Ahşap palet, üstten görünüm' ),
	'euro_palet'  => ip_seed_media( 'euro_palet.jpg', 'ip-euro-palet', 'Euro palet', 'EPAL damgalı Euro palet' ),
	'kafes'       => ip_seed_media( 'ahsap_kafes.jpg', 'ip-ahsap-kafes', 'Ahşap kafes', 'Çapraz destekli ahşap kafes' ),
	'kafes2'      => ip_seed_media( '4.jpg', 'ip-ahsap-kafes-2', 'Ahşap kafes', 'Makine parçası için üretilmiş ahşap kafes' ),
	'sandik'      => ip_seed_media( 'featured02.jpg', 'ip-ahsap-sandik', 'Ahşap sandık', 'OSB kaplı kapalı ahşap sandık' ),
	'yard'        => ip_seed_media( 'home_slide04.jpg', 'ip-saha-sandik', 'Sahada ahşap sandık', 'Kereste deposunda üretilmiş OSB kaplı uzun ahşap sandık' ),
	// Kolajlardan kirpilan tek fotograflar. Koordinatlar beyaz ayirici
	// cizgilerin olculmesiyle bulundu (2560 x 1440 kaynak).
	'shop'        => ip_seed_media( 'home_slide02.jpg', 'ip-dukkan-onu', 'Dükkân önünde ahşap sandık', 'Kereste dükkânının önünde üretilmiş büyük ahşap sandık', array( 806, 0, 1754, 1440 ) ),
	'workshop'    => ip_seed_media( 'home_slide03.jpg', 'ip-atolye', 'Atölyede ahşap sandıklar', 'Atölyede istiflenmiş farklı boylarda ahşap sandıklar', array( 802, 0, 1758, 1440 ) ),
	'stack'       => ip_seed_media( 'home_slide01.jpg', 'ip-istif', 'İstiflenmiş ahşap kasalar', 'Açık alanda üst üste istiflenmiş uzun ahşap kasalar', array( 926, 600, 896, 560 ) ),
);

foreach ( $media as $key => $id ) {
	WP_CLI::log( sprintf( 'Görsel: %-11s #%d', $key, $id ) );
}

/* ---------------------------------------------------------------- *
 * 4. Blog yazilari
 * ---------------------------------------------------------------- */
foreach ( require __DIR__ . '/data/istanbulpaletci-posts.php' as $post ) {
	$existing = get_page_by_path( $post['slug'], OBJECT, 'post' );

	$id = $existing ? (int) $existing->ID : (int) wp_insert_post(
		array(
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_name'     => $post['slug'],
			'post_title'    => $post['title'],
			'post_content'  => $post['content'],
			'post_date'     => $post['date'],
			'post_date_gmt' => get_gmt_from_date( $post['date'] ),
		)
	);

	if ( $id && ! empty( $media[ $post['image'] ] ) ) {
		set_post_thumbnail( $id, $media[ $post['image'] ] );
	}

	WP_CLI::log( sprintf( 'Yazı: /%s/ (#%d)', $post['slug'], $id ) );
}

/* ---------------------------------------------------------------- *
 * 5. Alan degerleri: once manifest varsayilanlari, sonra gorseller
 * ---------------------------------------------------------------- */
$content = array();

foreach ( $manifest['pages'] as $page_key => $page ) {
	foreach ( $page['components'] as $component_key => $component ) {
		foreach ( $component['fields'] as $field_key => $definition ) {
			$content[ $page_key ][ $component_key ][ $field_key ] = $definition['default'] ?? '';
		}
	}
}

$content['global']['header']['logo_image'] = $media['logo'];
$content['global']['footer']['logo_image'] = $media['logo_white'];
$content['home']['hero']['sheet_image']    = $media['palet3'];
$content['home']['hero']['bg_image']       = $media['saha'];
$content['home']['export']['image']        = $media['yard'];
$content['about']['head']['image']         = $media['shop'];
$content['about']['values']['image']       = $media['workshop'];

// Urun basina kart gorseli ve buyutuldugunde gezilecek ek gorseller.
$products = array(
	'urun_ahsap_palet'     => array( 'ahsap_palet', array( 'palet1', 'palet3' ) ),
	'urun_euro_palet'      => array( 'euro_palet', array() ),
	'urun_ahsap_kafes'     => array( 'kafes', array( 'kafes2' ) ),
	'urun_ahsap_sandik'    => array( 'sandik', array( 'shop', 'yard', 'stack' ) ),
	'urun_ikinci_el_palet' => array( 'palet2', array() ),
);

foreach ( $products as $key => $set ) {
	$content[ $key ]['card']['image']     = $media[ $set[0] ];
	$content[ $key ]['detail']['gallery'] = array_map(
		static fn( string $image ): array => array( 'image' => $media[ $image ] ),
		$set[1]
	);
}

update_option( 'nwcs_content', $content );
update_option( 'nwcs_section_order', array( 'home' => array( 'products', 'process', 'export', 'blog', 'quote' ) ) );

flush_rewrite_rules( true );

WP_CLI::success( 'istanbulpaletci icerigi yazildi.' );
