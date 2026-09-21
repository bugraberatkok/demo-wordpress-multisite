<?php
/**
 * Demo icerik seed'i.
 *
 * Calistirma:
 *   wp eval-file /scripts/seed.php --url=http://localhost:8080/kocist/
 *
 * Tekrar calistirilabilir: ayni gorselleri yeniden uretmez, alan degerlerini
 * manifest varsayilanlarina geri yazar. Tum degerler ilgili alt sitenin
 * WordPress option kaydina yazilir (sabit PHP metni degil).
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

if ( ! function_exists( 'nwcs_manifest' ) ) {
	WP_CLI::error( 'Network Content Studio eklentisi etkin degil.' );
}

$manifest = nwcs_manifest();

if ( empty( $manifest['pages'] ) ) {
	WP_CLI::error( 'Bu site icin alan manifesti bulunamadi (tema: ' . get_option( 'stylesheet' ) . ').' );
}

$site_key = (string) ( $manifest['site_key'] ?? '' );

/**
 * Metni kucuk bir tuvale cizip buyuterek hedef gorselin ortasina kopyalar.
 * Boylece GD'nin gomulu fontlari buyuk gorsellerde de okunur kalir.
 */
function nwcs_seed_text( $image, string $text, int $canvas_width, int $baseline_y, int $color, int $font, float $scale ): void {
	$scale = max( 1.0, min( 6.0, $scale ) );

	$text_w = imagefontwidth( $font ) * strlen( $text );
	$text_h = imagefontheight( $font );

	// Uzun etiketler tuvali tasmasin.
	if ( $text_w * $scale > $canvas_width - 24 ) {
		$scale = max( 1.0, ( $canvas_width - 24 ) / $text_w );
	}

	$layer = imagecreatetruecolor( $text_w, $text_h );
	imagesavealpha( $layer, true );
	imagefill( $layer, 0, 0, imagecolorallocatealpha( $layer, 0, 0, 0, 127 ) );
	imagestring( $layer, $font, 0, 0, $text, imagecolorallocate( $layer, 255, 255, 255 ) );

	$out_w = (int) round( $text_w * $scale );
	$out_h = (int) round( $text_h * $scale );
	$dst_x = max( 6, (int) round( ( $canvas_width - $out_w ) / 2 ) );

	imagecopyresampled( $image, $layer, $dst_x, $baseline_y, 0, 0, $out_w, $out_h, $text_w, $text_h );
	imagedestroy( $layer );

	unset( $color );
}

/**
 * Ornek gorsel uretir ve medya kaydi olusturur. Ayni anahtar icin ikinci kez
 * calistirildiginda mevcut kaydi dondurur.
 */
function nwcs_seed_image( string $key, string $ascii_label, string $alt_text, int $width, int $height, array $rgb ): int {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_nwcs_seed_key',
			'meta_value'     => $key,
		)
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	if ( ! function_exists( 'imagecreatetruecolor' ) ) {
		WP_CLI::warning( 'GD eklentisi yok; gorsel uretilemedi: ' . $key );

		return 0;
	}

	$image = imagecreatetruecolor( $width, $height );

	$bg     = imagecolorallocate( $image, $rgb[0], $rgb[1], $rgb[2] );
	$stripe = imagecolorallocate(
		$image,
		max( 0, $rgb[0] - 18 ),
		max( 0, $rgb[1] - 18 ),
		max( 0, $rgb[2] - 18 )
	);
	$ink    = imagecolorallocate( $image, 255, 255, 255 );

	imagefilledrectangle( $image, 0, 0, $width, $height, $bg );

	// Ahsap dokusunu andiran capraz seritler.
	for ( $x = -$height; $x < $width; $x += 26 ) {
		imagefilledpolygon(
			$image,
			array( $x, 0, $x + 13, 0, $x + 13 + $height, $height, $x + $height, $height ),
			$stripe
		);
	}

	// Yazi olculeri once hesaplanir; serit yaziyi kapsayacak kadar yuksek olur.
	$label_scale = max( 1.0, min( 6.0, $width / 300 ) );
	$tag_scale   = max( 1.0, min( 4.0, $width / 520 ) );
	$label_h     = (int) round( imagefontheight( 5 ) * $label_scale );
	$tag_h       = (int) round( imagefontheight( 3 ) * $tag_scale );
	$mid         = (int) ( $height / 2 );
	$label_y     = $mid - $label_h - 6;
	$tag_y       = $mid + 8;

	$band_top    = $label_y - 14;
	$band_bottom = $tag_y + $tag_h + 14;

	imagefilledrectangle( $image, 0, $band_top, $width, $band_bottom, $bg );

	// GD'nin gomulu fontlari kucuk kalir; yaziyi kucuk bir tuvale cizip
	// buyuterek kopyaliyoruz ki yer tutucu oldugu net okunsun.
	nwcs_seed_text( $image, strtoupper( $ascii_label ), $width, $label_y, $ink, 5, $label_scale );
	nwcs_seed_text( $image, 'ORNEK GORSEL', $width, $tag_y, $ink, 3, $tag_scale );

	ob_start();
	imagepng( $image );
	$binary = ob_get_clean();
	imagedestroy( $image );

	$upload = wp_upload_bits( 'ornek-' . sanitize_file_name( $key ) . '.png', null, $binary );

	if ( ! empty( $upload['error'] ) ) {
		WP_CLI::warning( 'Yukleme hatasi (' . $key . '): ' . $upload['error'] );

		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/png',
			'post_title'     => $alt_text,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		WP_CLI::warning( 'Medya kaydi olusturulamadi: ' . $key );

		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );

	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt_text );
	update_post_meta( $attachment_id, '_nwcs_seed_key', $key );

	return (int) $attachment_id;
}

/**
 * Sayfayi olusturur veya mevcut olani dondurur.
 */
function nwcs_seed_page( string $slug, string $title ): int {
	$page = get_page_by_path( $slug );

	if ( $page ) {
		return (int) $page->ID;
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_content' => '',
		)
	);

	return is_wp_error( $id ) ? 0 : (int) $id;
}

/* ------------------------------------------------------------------ */
/* 1) Manifest varsayilanlarini gercek veri olarak yaz                  */
/* ------------------------------------------------------------------ */

$written = 0;

foreach ( $manifest['pages'] as $page_key => $page ) {
	foreach ( $page['components'] as $component_key => $component ) {
		foreach ( $component['fields'] as $field_key => $definition ) {
			$value = $definition['default'] ?? '';
			nwcs_update_field( $page_key, $component_key, $field_key, $value, $manifest );
			++$written;
		}
	}
}

/* ------------------------------------------------------------------ */
/* 2) Ornek gorseller ve siteye ozel ayarlar                            */
/* ------------------------------------------------------------------ */

$content = get_option( NWCS_OPTION_CONTENT, array() );

if ( 'kocist' === $site_key ) {
	update_option( 'blogname', 'Koçist' );
	update_option( 'blogdescription', 'Kereste, ahşap ambalaj, dekorasyon ve hırdavat (yerel demo)' );

	$palette = array( 44, 62, 80 );

	$content['global']['header']['logo_image'] = nwcs_seed_image( 'kocist-logo', 'K', 'Örnek logo yer tutucusu (Koçist demo)', 200, 200, array( 32, 45, 60 ) );
	$content['home']['hero']['image']          = nwcs_seed_image( 'kocist-hero', 'Kereste stok alani', 'Örnek görsel: kereste stok alanı (demo yer tutucusu)', 1200, 900, $palette );
	$content['inner']['story']['image']        = nwcs_seed_image( 'kocist-kurumsal', 'Uretim tesisi', 'Örnek görsel: üretim tesisi (demo yer tutucusu)', 900, 1200, array( 52, 70, 88 ) );

	$catalog_images = array(
		array( 'kocist-kereste', 'Kereste', 'Örnek görsel: kereste' ),
		array( 'kocist-ambalaj', 'Ahsap ambalaj', 'Örnek görsel: ahşap ambalaj' ),
		array( 'kocist-dekorasyon', 'Dekorasyon', 'Örnek görsel: ahşap dekorasyon' ),
		array( 'kocist-hirdavat', 'Hirdavat', 'Örnek görsel: hırdavat grubu' ),
	);

	foreach ( $catalog_images as $index => $item ) {
		if ( isset( $content['home']['catalog']['items'][ $index ] ) ) {
			$content['home']['catalog']['items'][ $index ]['image'] = nwcs_seed_image(
				$item[0],
				$item[1],
				$item[2] . ' (demo yer tutucusu)',
				800,
				500,
				array( 62 + $index * 9, 78 + $index * 6, 94 + $index * 4 )
			);
		}
	}

	nwcs_seed_page( 'kurumsal', 'Kurumsal' );
	$front_id = nwcs_seed_page( 'ana-sayfa', 'Ana Sayfa' );
} elseif ( 'paletci' === $site_key ) {
	update_option( 'blogname', 'İstanbul Paletçi' );
	update_option( 'blogdescription', 'Ahşap palet, sandık ve kafes üretimi (yerel demo)' );

	$content['global']['header']['logo_image'] = nwcs_seed_image( 'paletci-logo', 'P', 'Örnek logo yer tutucusu (İstanbul Paletçi demo)', 200, 200, array( 31, 64, 51 ) );
	$content['home']['hero']['image']          = nwcs_seed_image( 'paletci-hero', 'Palet uretim atolyesi', 'Örnek görsel: palet üretim atölyesi (demo yer tutucusu)', 1400, 600, array( 31, 64, 51 ) );
	$content['home']['why']['image']           = nwcs_seed_image( 'paletci-atolye', 'Atolye', 'Örnek görsel: atölye (demo yer tutucusu)', 1000, 800, array( 44, 87, 68 ) );
	$content['inner']['intro']['image']        = nwcs_seed_image( 'paletci-urunler', 'Urun grubu', 'Örnek görsel: ürün grubu (demo yer tutucusu)', 1000, 750, array( 122, 79, 52 ) );

	$product_images = array(
		array( 'paletci-ahsap-palet', 'Ahsap palet', 'Örnek görsel: ahşap palet' ),
		array( 'paletci-euro-palet', 'Euro palet', 'Örnek görsel: euro palet' ),
		array( 'paletci-kafes', 'Ahsap kafes', 'Örnek görsel: ahşap kafes' ),
		array( 'paletci-sandik', 'Ahsap sandik', 'Örnek görsel: ahşap sandık' ),
		array( 'paletci-ikinci-el', 'Ikinci el palet', 'Örnek görsel: ikinci el palet' ),
	);

	foreach ( $product_images as $index => $item ) {
		if ( isset( $content['home']['products']['items'][ $index ] ) ) {
			$content['home']['products']['items'][ $index ]['image'] = nwcs_seed_image(
				$item[0],
				$item[1],
				$item[2] . ' (demo yer tutucusu)',
				800,
				600,
				array( 122 + $index * 6, 79 + $index * 8, 52 + $index * 5 )
			);
		}
	}

	nwcs_seed_page( 'urunlerimiz', 'Ürünlerimiz' );
	$front_id = nwcs_seed_page( 'anasayfa', 'Anasayfa' );
} else {
	WP_CLI::error( 'Bilinmeyen site anahtari: ' . $site_key );
}

update_option( NWCS_OPTION_CONTENT, $content );

/* ------------------------------------------------------------------ */
/* 3) Bolum sirasi ve sayfa ayarlari                                     */
/* ------------------------------------------------------------------ */

nwcs_set_section_order( 'home', nwcs_sortable_sections( $manifest, 'home' ), $manifest );

if ( ! empty( $front_id ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front_id );
}

update_option( 'timezone_string', 'Europe/Istanbul' );

WP_CLI::success(
	sprintf(
		'%s seed tamamlandi: %d alan yazildi, ornek gorseller medya kitapligina eklendi.',
		$manifest['site_label'] ?? $site_key,
		$written
	)
);
