<?php
/**
 * Seed betiklerinin paylastigi yardimcilar: ornek gorsel uretimi.
 *
 * Gorseller disaridan indirilmez; GD ile uretilir ve uzerlerinde
 * "ORNEK GORSEL" yazar. Boylece kullanim hakki sorunu olmaz ve hicbir gorsel
 * gercek marka varligi sanilmaz.
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

if ( ! function_exists( 'nwcs_seed_text' ) ) {
	/**
	 * Metni kucuk bir tuvale cizip buyuterek hedef gorselin ortasina kopyalar.
	 */
	function nwcs_seed_text( $image, string $text, int $canvas_width, int $baseline_y, int $font, float $scale ): void {
		$scale = max( 1.0, min( 6.0, $scale ) );

		$text_w = imagefontwidth( $font ) * strlen( $text );
		$text_h = imagefontheight( $font );

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
	}
}

if ( ! function_exists( 'nwcs_seed_image' ) ) {
	/**
	 * Ornek gorsel uretir ve aktif sitenin medya kitapligina ekler.
	 * Ayni anahtar icin ikinci kez calistirildiginda mevcut kaydi dondurur.
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

		for ( $x = -$height; $x < $width; $x += 26 ) {
			imagefilledpolygon(
				$image,
				array( $x, 0, $x + 13, 0, $x + 13 + $height, $height, $x + $height, $height ),
				$stripe
			);
		}

		$label_scale = max( 1.0, min( 6.0, $width / 300 ) );
		$tag_scale   = max( 1.0, min( 4.0, $width / 520 ) );
		$label_h     = (int) round( imagefontheight( 5 ) * $label_scale );
		$tag_h       = (int) round( imagefontheight( 3 ) * $tag_scale );
		$mid         = (int) ( $height / 2 );
		$label_y     = $mid - $label_h - 6;
		$tag_y       = $mid + 8;

		imagefilledrectangle( $image, 0, $label_y - 14, $width, $tag_y + $tag_h + 14, $bg );

		nwcs_seed_text( $image, strtoupper( $ascii_label ), $width, $label_y, 5, $label_scale );
		nwcs_seed_text( $image, 'ORNEK GORSEL', $width, $tag_y, 3, $tag_scale );

		unset( $ink );

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
}
