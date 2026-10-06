<?php
/**
 * Yuklenen fotograflar WebP'ye cevrilir.
 *
 * Musteri telefondan ne cikiyorsa (JPG, PNG, iPhone HEIC) onu yukler; dosya
 * yukleme aninda en fazla NWCS_IMAGE_MAX_EDGE piksele kucultulur, yonu
 * duzeltilir ve WebP olarak kaydedilir. Orijinal saklanmaz: temalar pek cok
 * yerde "full" boyutu kullaniyor (hero, yakinlastirma); o da WebP olsun.
 * Ara boyutlar WebP asildan uretildigi icin kendiliginden WebP olur.
 *
 * Yalnizca panelden (upload_files yetkisiyle) yuklenenler cevrilir.
 * EXIF (konum dahil) donusumde duser. GIF (hareketli olabilir) ve SVG'ye
 * dokunulmaz. Sunucu WebP yazamiyorsa ya da donusum basarisizsa dosya
 * oldugu gibi kalir; yukleme bozulmaz.
 */

defined( 'ABSPATH' ) || exit;

const NWCS_IMAGE_MAX_EDGE = 2560;

/**
 * WebP kalitesi. WordPress varsayilani 86 (JPEG 82); 86'da buyuk fotograflarin
 * WebP'si bazen JPG'den buyuk cikiyordu. 82'de fark gozle secilmez.
 */
const NWCS_IMAGE_WEBP_QUALITY = 82;

/**
 * WebP'ye cevrilen turler.
 *
 * @return string[]
 */
function nwcs_image_convertible_types(): array {
	return array( 'image/jpeg', 'image/png', 'image/heic', 'image/heif' );
}

/**
 * Sunucu WebP yazabiliyor mu (istek basina bir kez sorulur).
 */
function nwcs_image_webp_supported(): bool {
	static $supported = null;

	if ( null === $supported ) {
		$supported = wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );
	}

	return $supported;
}

add_filter( 'wp_handle_upload', 'nwcs_image_upload_to_webp', 10, 2 );
function nwcs_image_upload_to_webp( array $upload, string $context = 'upload' ): array {
	if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) || ! in_array( $upload['type'] ?? '', nwcs_image_convertible_types(), true ) ) {
		return $upload;
	}

	// Yalnizca panelden yuklenenler: ziyaretci formlarinin ekleri (WOOD KOCIST talep formu) oldugu gibi kalir.
	if ( ! current_user_can( 'upload_files' ) || ! nwcs_image_webp_supported() ) {
		return $upload;
	}

	$converted = nwcs_image_convert_file( $upload['file'] );

	if ( ! $converted ) {
		return $upload;
	}

	return array(
		'file' => $converted,
		'url'  => trailingslashit( dirname( $upload['url'] ) ) . rawurlencode( wp_basename( $converted ) ),
		'type' => 'image/webp',
	);
}

/**
 * Dosyayi kucultup WebP olarak ayni klasore yazar, orijinali siler.
 *
 * @return string|null Yeni dosyanin yolu; basarisizsa null (orijinal yerinde kalir).
 */
function nwcs_image_convert_file( string $file ): ?string {
	$editor = wp_get_image_editor( $file );

	if ( is_wp_error( $editor ) ) {
		return null;
	}

	// Telefon fotograflarinda yon EXIF'te durur; EXIF duseceginden once uygulanir.
	if ( method_exists( $editor, 'maybe_exif_rotate' ) ) {
		$editor->maybe_exif_rotate();
	}

	$size    = $editor->get_size();
	$resized = max( (int) $size['width'], (int) $size['height'] ) > NWCS_IMAGE_MAX_EDGE;

	if ( $resized ) {
		$editor->resize( NWCS_IMAGE_MAX_EDGE, NWCS_IMAGE_MAX_EDGE, false );
	}

	$dir    = dirname( $file );
	$name   = wp_unique_filename( $dir, pathinfo( $file, PATHINFO_FILENAME ) . '.webp' );
	$target = trailingslashit( $dir ) . $name;
	$saved  = $editor->save( $target, 'image/webp' );

	if ( is_wp_error( $saved ) || empty( $saved['path'] ) || ! file_exists( $saved['path'] ) ) {
		return null;
	}

	// Kucultulmeyen ve WebP'si daha buyuk cikan (zaten iyi sikistirilmis) dosya oldugu gibi kalir.
	// Bilerek image_editor_output_format kullanilmiyor: o, boyle bir JPG icin cekirdege daha buyuk
	// bir tam boy WebP kopyasi urettirip asil dosya yapiyordu.
	if ( ! $resized && filesize( $saved['path'] ) >= filesize( $file ) ) {
		wp_delete_file( $saved['path'] );

		return null;
	}

	wp_delete_file( $file );

	return $saved['path'];
}

add_filter(
	'wp_editor_set_quality',
	static fn( $quality, $mime_type = '' ) => 'image/webp' === $mime_type ? NWCS_IMAGE_WEBP_QUALITY : $quality,
	10,
	2
);

/**
 * Asil zaten kucultuldu; WordPress ayrica "-scaled" kopya uretmesin.
 */
add_filter(
	'big_image_size_threshold',
	static fn( $threshold ) => $threshold ? max( (int) $threshold, NWCS_IMAGE_MAX_EDGE ) : $threshold
);
