<?php
/**
 * Yerel deneme: Fotograf Kutusu akisi (uygula, iki kez uygula, eski sekme,
 * yarida kesilen uygulama + geri alma, supurme, Medya Havuzu'nda gizleme,
 * vazgec). Dosyalar tarayici yerine media_handle_sideload ile partiye konur
 * (yukleme ucu tarayicidan ayrica denenir). Olusturdugu her seyi geri alir.
 *
 *   docker compose exec -T wordpress php /scripts/dev/check-photo-flow.php
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
class Nwcs_Stop extends Exception {}

add_filter(
	'wp_redirect',
	static function ( $location ) {
		throw new Nwcs_Redirect( $location );
	}
);

$fail = 0;
function check( string $label, bool $ok, string $got = '' ): void {
	global $fail;
	echo ( $ok ? 'OK   ' : 'FAIL ' ) . $label . ( $ok || '' === $got ? '' : '  (' . $got . ')' ) . "\n";
	$fail += $ok ? 0 : 1;
}

wp_set_current_user( get_user_by( 'login', get_super_admins()[0] )->ID );

/** Iki urun: kategoride (kamelya) ilk ikisi; kodlari ve galerileri saklanir. */
$pool = nwcs_sync_category_products( 'kamelyalar' );
$pool = array_filter( $pool, static fn( array $p ): bool => '' !== $p['code'] );
$ids  = array_slice( array_keys( $pool ), 0, 2 );
[ $a, $b ] = $ids;
$codes = array( $a => $pool[ $a ]['code'], $b => $pool[ $b ]['code'] );

switch_to_blog( nwcs_pool_blog_id() );
$keep = array();
foreach ( $ids as $id ) {
	$keep[ $id ] = array( get_post_meta( $id, '_nwcs_gallery', true ), (int) get_post_thumbnail_id( $id ) );
}
restore_current_blog();
$history_before = get_site_option( NWCS_HISTORY );

function make_jpg(): string {
	$img = imagecreatetruecolor( 640, 857 );
	imagefill( $img, 0, 0, imagecolorallocate( $img, random_int( 0, 255 ), 120, 60 ) );
	$path = tempnam( sys_get_temp_dir(), 'nwcs' ) . '.jpg';
	imagejpeg( $img, $path, 85 );

	return $path;
}

/** Bir parti kurar: [ad, ...] -> ek kimlikleri; transient yazilir. */
function make_batch( array $names ): array {
	$pending = nwcs_photos_start( 'kamelyalar' );

	switch_to_blog( nwcs_pool_blog_id() );
	foreach ( $names as $name ) {
		$id = media_handle_sideload( array( 'name' => $name, 'tmp_name' => make_jpg() ), 0 );
		update_post_meta( $id, NWCS_PHOTO_META, $pending['id'] );
		update_post_meta( $id, '_nwcs_photo_name', $name );
		update_post_meta( $id, '_nwcs_photo_cat', 'kamelyalar' );
	}
	restore_current_blog();

	return $pending;
}

function post_apply( string $batch, string $mode = 'append' ): string {
	$_POST = $_REQUEST = array( 'parti' => $batch, 'kategori' => 'kamelyalar', 'mod' => $mode, '_wpnonce' => wp_create_nonce( 'nwcs_photos_apply' ) );
	try {
		nwcs_handle_photos_apply();
	} catch ( Nwcs_Redirect $r ) {
		return $r->getMessage();
	}

	return '';
}

function gallery( int $id ): array {
	switch_to_blog( nwcs_pool_blog_id() );
	$g = nwcs_photo_current_gallery( $id );
	restore_current_blog();

	return $g;
}

/* 1) Uygula + iki kez uygula. */
$p1   = make_batch( array( $codes[ $a ] . '-2.jpg', $codes[ $a ] . '-1.jpg', $codes[ $b ] . '.jpg', $codes[ $b ] . '-1.jpg', 'genel.jpg' ) );
$to   = post_apply( $p1['id'] );
$rec  = nwcs_history_top();
check( 'uygula: sonuc sayfasina doner', str_contains( $to, 'fotograf=uygulandi' ), $to );
check( 'uygula: kayit fotograflar, 4 fotograf 2 urun', NWCS_PHOTO_KIND === $rec['kind'] && 4 === $rec['counts']['photos'] && 2 === $rec['counts']['products'], wp_json_encode( $rec['counts'] ) );
$added_a = $rec['products'][ $a ]['added'];
check( 'uygula: sira dosya numarasiyla (-1 once)', gallery( $a ) === array_merge( (array) $rec['products'][ $a ]['before'], $added_a ) && str_contains( (string) get_blog_post( nwcs_pool_blog_id(), $added_a[0] )->post_title, 'görsel' ) );
switch_to_blog( nwcs_pool_blog_id() );
check( 'uygula: -1 dosyasi galeride ilk yeni', '-1.jpg' === substr( (string) get_post_meta( $added_a[0], '_wp_attached_file', true ), -6 ) || str_contains( (string) get_post_meta( $added_a[0], '_wp_attached_file', true ), '-1' ) );
restore_current_blog();
check( 'uygula: parti isaretli ek kalmadi', ! nwcs_photos_batch_ids( $p1['id'] ) );

$to2 = post_apply( $p1['id'] );
check( 'iki kez uygula: "zaten uygulandi"', str_contains( $to2, 'fotograf=uygulandi' ) && str_contains( (string) get_site_transient( nwcs_photos_pending_key() . '_hata' ), 'zaten uygulandı' ), $to2 );
delete_site_transient( nwcs_photos_pending_key() . '_hata' );
check( 'iki kez uygula: tek kayit', 1 === count( array_filter( nwcs_history_all(), static fn( $r ) => ( $r['batch'] ?? '' ) === $p1['id'] ) ) );

/* 2) Eski sekme: baska parti basladiktan sonra eski onizlemeden Uygula. */
$p2  = make_batch( array( $codes[ $a ] . '-9.jpg' ) );
$p3  = make_batch( array( $codes[ $b ] . '-9.jpg' ) ); // p2'nin ekleri silinir
check( 'yeni parti eski partinin eklerini siler', ! nwcs_photos_batch_ids( $p2['id'] ) );
$to3 = post_apply( $p2['id'] );
check( 'eski sekmeden Uygula: suresi doldu hatasi', str_contains( $to3, 'fotograf=hata' ) && str_contains( (string) get_site_transient( nwcs_photos_pending_key() . '_hata' ), 'başka bir yükleme' ), $to3 );
delete_site_transient( nwcs_photos_pending_key() . '_hata' );

/* 3) Vazgec. */
$_POST = $_REQUEST = array( 'kategori' => 'kamelyalar', '_wpnonce' => wp_create_nonce( 'nwcs_photos_cancel' ) );
try { nwcs_handle_photos_cancel(); } catch ( Nwcs_Redirect $r ) {} // phpcs:ignore
check( 'vazgec: ekler silindi, bekleyen yok', ! nwcs_photos_batch_ids( $p3['id'] ) && ! nwcs_photos_pending() );

/* 4) Medya Havuzu bekleyen partiyi gostermez; supurme. */
$p4    = make_batch( array( 'bekleyen-' . wp_generate_password( 6, false ) . '.jpg' ) );
$wait  = nwcs_photos_batch_ids( $p4['id'] );
$media = wp_list_pluck( nwcs_media_query( '', 1 )['items'], 'id' );
check( 'Medya Havuzu: bekleyen dosya listede yok', ! array_intersect( $wait, $media ) );
check( 'kitaplik aramasi: bekleyen dosya yok', ! array_intersect( $wait, wp_list_pluck( nwcs_media_find( 'bekleyen' ), 'id' ) ) );
check( 'supurme: 24 saatten yeni dosyaya dokunmaz', 0 === nwcs_photos_sweep() && nwcs_photos_batch_ids( $p4['id'] ) );
add_filter( 'nwcs_photos_sweep_age', static fn() => -60 );
check( 'supurme: eski parti silinir', nwcs_photos_sweep() >= 1 && ! nwcs_photos_batch_ids( $p4['id'] ) );
remove_all_filters( 'nwcs_photos_sweep_age' );
delete_site_transient( nwcs_photos_pending_key() );

/* 5) Yarida kesilen uygulama: ilk urunden sonra durur. */
$p5 = make_batch( array( $codes[ $a ] . '-5.jpg', $codes[ $b ] . '-5.jpg' ) );
add_action( 'nwcs_photos_applied_product', static function () { throw new Nwcs_Stop( 'kesildi' ); } );
try {
	post_apply( $p5['id'], 'replace' );
} catch ( Nwcs_Stop $e ) {} // phpcs:ignore
remove_all_actions( 'nwcs_photos_applied_product' );
restore_current_blog(); // kesilen istek havuzda kalmisti
$cut = nwcs_history_top();
check( 'kesilen: kayit yarida (complete=false), 1 urun', false === $cut['complete'] && 1 === count( $cut['products'] ), wp_json_encode( array( $cut['complete'], count( $cut['products'] ) ) ) );
$first = (int) array_key_first( $cut['products'] );
check( 'kesilen: ilk urunun galerisi degisti (degistir kipi)', gallery( $first ) === $cut['products'][ $first ]['added'] );
$undo = nwcs_history_undo( $cut['id'] );
check( 'kesilen: geri alma yapilani geri alir', ! is_wp_error( $undo ) && 1 === $undo['report']['restored'] && gallery( $first ) === array_map( 'intval', (array) $cut['products'][ $first ]['before'] ), is_wp_error( $undo ) ? $undo->get_error_message() : wp_json_encode( $undo['report'] ) );
nwcs_photos_delete_batch( $p5['id'] ); // kesilen partinin kalan (ikinci urun) ekleri
delete_site_transient( nwcs_photos_pending_key() );

/* 6) Ilk partiyi geri al: elle duzenlenen urun "kept". */
switch_to_blog( nwcs_pool_blog_id() );
$manual = array_reverse( nwcs_photo_current_gallery( $b ) );
update_post_meta( $b, '_nwcs_gallery', $manual );
restore_current_blog();
$top  = nwcs_history_top();
$undo = nwcs_history_undo( $top['id'] );
check( 'geri al: ' . $codes[ $a ] . ' eski haline', ! is_wp_error( $undo ) && gallery( $a ) === array_map( 'intval', (array) $top['products'][ $a ]['before'] ) );
check( 'geri al: elle duzenlenen urun kept', ! is_wp_error( $undo ) && 1 === count( $undo['report']['kept'] ), is_wp_error( $undo ) ? '' : wp_json_encode( $undo['report'] ) );
check( 'geri al: eklenen dosyalar silindi (kept haric)', ! is_wp_error( $undo ) && 2 === $undo['report']['deleted_photos'] );
check( 'describe', str_contains( nwcs_history_describe( $top )['facts'], '4 fotoğraf, 2 ürün (sona eklendi)' ), nwcs_history_describe( $top )['facts'] );

/* Temizlik: urunler eski galerisine, kalan ekler silinir, gecmis eski haline. */
switch_to_blog( nwcs_pool_blog_id() );
foreach ( $top['products'][ $b ]['added'] as $att ) {
	wp_delete_attachment( (int) $att, true );
}
foreach ( $keep as $id => [ $g, $t ] ) {
	'' === $g ? delete_post_meta( $id, '_nwcs_gallery' ) : update_post_meta( $id, '_nwcs_gallery', $g );
	$t ? set_post_thumbnail( $id, $t ) : delete_post_thumbnail( $id );
}
restore_current_blog();
update_site_option( NWCS_HISTORY, $history_before );
delete_site_transient( nwcs_photos_pending_key() );
nwcs_pool_flush_cache();

echo $fail ? "\n$fail hata\n" : "\nHepsi gecti\n";
