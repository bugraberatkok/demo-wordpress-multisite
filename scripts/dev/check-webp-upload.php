<?php
/**
 * Yerel deneme: panelden yuklenen JPG/PNG WebP'ye cevriliyor mu, buyuk
 * fotograf kucultuluyor mu, ziyaretci yuklemesine dokunulmuyor mu, Medya
 * Havuzu yukleme bildirimleri nedeni soyluyor mu. Olusturdugu ekleri siler.
 *
 *   docker compose cp scripts/dev/check-webp-upload.php wordpress:/tmp/
 *   docker compose exec -T wordpress php /tmp/check-webp-upload.php
 */

define( 'WP_ADMIN', true );
define( 'WP_NETWORK_ADMIN', true );
$_SERVER['HTTP_HOST']   = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/wp-admin/network/admin.php';

require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/admin.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

class Nwcs_Redirect extends Exception {}

add_filter(
	'wp_redirect',
	static function ( $location ) {
		throw new Nwcs_Redirect( $location );
	}
);

$fail = 0;
function check( string $label, bool $ok ): void {
	global $fail;
	echo ( $ok ? 'OK   ' : 'FAIL ' ) . $label . "\n";
	$fail += $ok ? 0 : 1;
}

/** GD ile deneme gorseli uretir. */
function make_image( string $type, int $w, int $h ): string {
	$img = imagecreatetruecolor( $w, $h );
	imagesavealpha( $img, true );
	imagefill( $img, 0, 0, imagecolorallocatealpha( $img, 30, 120, 60, 'png' === $type ? 80 : 0 ) );
	for ( $i = 0; $i < 400; $i++ ) {
		imagefilledellipse( $img, random_int( 0, $w ), random_int( 0, $h ), 90, 90, imagecolorallocate( $img, random_int( 0, 255 ), random_int( 0, 255 ), random_int( 0, 255 ) ) );
	}
	$path = tempnam( sys_get_temp_dir(), 'nwcs' ) . '.' . $type;
	'png' === $type ? imagepng( $img, $path ) : imagejpeg( $img, $path, 95 );

	return $path;
}

function sideload( string $path, string $name ): int|WP_Error {
	return media_handle_sideload( array( 'name' => $name, 'tmp_name' => $path ), 0 );
}

$created = array();
switch_to_blog( nwcs_pool_blog_id() );

// 1) Super yonetici: buyuk JPG -> WebP, en fazla 2560.
wp_set_current_user( get_user_by( 'login', get_super_admins()[0] )->ID );
check( 'sunucu WebP yazabiliyor', nwcs_image_webp_supported() );

$src       = make_image( 'jpg', 4000, 3000 );
$src_size  = filesize( $src );
$id        = sideload( $src, 'zz-deneme-buyuk.jpg' );
$created[] = $id;
$file      = get_attached_file( $id );
$meta      = wp_get_attachment_metadata( $id );
check( 'buyuk JPG: ek olustu', ! is_wp_error( $id ) );
check( 'buyuk JPG: dosya .webp', str_ends_with( $file, '.webp' ) && file_exists( $file ) );
check( 'buyuk JPG: tur image/webp', 'image/webp' === get_post_mime_type( $id ) );
check( 'buyuk JPG: 2560x1920', 2560 === (int) $meta['width'] && 1920 === (int) $meta['height'] );
check( 'buyuk JPG: -scaled kopya yok', empty( $meta['original_image'] ) );
check( 'buyuk JPG: ara boyutlar webp', $meta['sizes'] && ! array_filter( $meta['sizes'], static fn( $s ) => 'image/webp' !== $s['mime-type'] ) );
check( 'buyuk JPG: orijinal jpg silindi', ! glob( dirname( $file ) . '/zz-deneme-buyuk*.jpg' ) );
check( 'buyuk JPG: full adresi webp', str_ends_with( (string) wp_get_attachment_image_url( $id, 'full' ), '.webp' ) );
printf( "     boyut: %s -> %s\n", size_format( $src_size ), size_format( filesize( $file ) ) );

// 2) Seffaf PNG -> WebP (kucultme yok).
$id        = sideload( make_image( 'png', 800, 600 ), 'zz-deneme-logo.png' );
$created[] = $id;
$meta      = wp_get_attachment_metadata( $id );
check( 'PNG: webp ve olcu korunmus', 'image/webp' === get_post_mime_type( $id ) && 800 === (int) $meta['width'] );

// 3) Ziyaretci (yetkisiz) yuklemesi: dokunulmaz.
wp_set_current_user( 0 );
$id        = sideload( make_image( 'jpg', 1200, 900 ), 'zz-deneme-form.jpg' );
$created[] = $id;
check( 'ziyaretci: jpg olarak kaldi', 'image/jpeg' === get_post_mime_type( $id ) );
wp_set_current_user( get_user_by( 'login', get_super_admins()[0] )->ID );

restore_current_blog();

// 4) Medya Havuzu yukleme bildirimleri.
$limit                 = wp_max_upload_size();
$_FILES['files']       = array(
	'name'     => array( 'buyuk.jpg', 'bozuk.jpg' ),
	'type'     => array( 'image/jpeg', 'image/jpeg' ),
	'tmp_name' => array( '/tmp/yok1', '/tmp/yok2' ),
	'error'    => array( UPLOAD_ERR_OK, UPLOAD_ERR_OK ),
	'size'     => array( $limit + 1, 1000 ),
);
$_POST['_wpnonce']     = wp_create_nonce( 'nwcs_media_upload' );
$_REQUEST['_wpnonce']  = $_POST['_wpnonce'];
try {
	nwcs_handle_media_upload();
	$to = '';
} catch ( Nwcs_Redirect $r ) {
	$to = $r->getMessage();
}
check( 'bildirim: buyuk=1 hatali=1 failed', str_contains( $to, 'nwcs_media=failed' ) && str_contains( $to, 'buyuk=1' ) && str_contains( $to, 'hatali=1' ) );

$_GET = array( 'nwcs_media' => 'failed', 'buyuk' => '1', 'hatali' => '1' );
ob_start();
nwcs_render_media_notices();
$notice = ob_get_clean();
check( 'bildirim metni nedeni soyluyor', str_contains( $notice, 'çok büyük' ) && str_contains( $notice, 'desteklenmiyor' ) );

$_FILES = array();
try {
	nwcs_handle_media_upload();
	$to = '';
} catch ( Nwcs_Redirect $r ) {
	$to = $r->getMessage();
}
check( 'bos $_FILES: too_big_total', str_contains( $to, 'too_big_total' ) );

// Temizlik.
switch_to_blog( nwcs_pool_blog_id() );
foreach ( $created as $id ) {
	if ( is_int( $id ) ) {
		wp_delete_attachment( $id, true );
	}
}
restore_current_blog();

echo $fail ? "\n$fail hata\n" : "\nHepsi gecti\n";
