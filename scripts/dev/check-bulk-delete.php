<?php
/**
 * Yerel deneme: Urun Havuzu toplu cope atma, cop kutusu toplu geri getir /
 * kalici sil, Medya Havuzu toplu silme. Kendi olusturdugu deneme urun ve
 * gorsellerini sonunda siler.
 *
 *   docker compose cp scripts/dev/check-bulk-delete.php wordpress:/tmp/
 *   docker compose exec -T wordpress php /tmp/check-bulk-delete.php
 */

define( 'WP_ADMIN', true );
define( 'WP_NETWORK_ADMIN', true );
$_SERVER['HTTP_HOST']   = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/wp-admin/network/admin.php';

require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/admin.php';

class Nwcs_Redirect extends Exception {}

add_filter(
	'wp_redirect',
	static function ( $location ) {
		throw new Nwcs_Redirect( $location );
	}
);

$admins = get_super_admins();
wp_set_current_user( get_user_by( 'login', $admins[0] )->ID );

$fail = 0;
function check( string $label, bool $ok ): void {
	global $fail;
	echo ( $ok ? 'OK   ' : 'FAIL ' ) . $label . "\n";
	$fail += $ok ? 0 : 1;
}

function post_as( string $handler, string $nonce, array $post ): string {
	$_POST                = $post;
	$_POST['_wpnonce']    = wp_create_nonce( $nonce );
	$_REQUEST['_wpnonce'] = $_POST['_wpnonce'];

	try {
		$handler();
	} catch ( Nwcs_Redirect $redirect ) {
		return $redirect->getMessage();
	}

	return '';
}

// Deneme verisi: 3 urun, 3 gorsel. A -> urun 1, B -> urun 3, C bos.
switch_to_blog( nwcs_pool_blog_id() );

$product = array();
foreach ( array( 1, 2, 3 ) as $n ) {
	$product[ $n ] = wp_insert_post(
		array(
			'post_type'   => NWCS_PRODUCT_TYPE,
			'post_status' => 'publish',
			'post_title'  => "ZZ deneme toplu silme $n",
		)
	);
}

$image = array();
foreach ( array( 'a', 'b', 'c' ) as $key ) {
	$image[ $key ] = wp_insert_attachment(
		array(
			'post_title'     => "zz-deneme-$key",
			'post_mime_type' => 'image/jpeg',
			'post_status'    => 'inherit',
		)
	);
}

update_post_meta( $product[1], '_nwcs_gallery', array( $image['a'] ) );
update_post_meta( $product[3], '_nwcs_gallery', array( $image['b'] ) );

restore_current_blog();
nwcs_pool_flush_cache();

// 1) Toplu cope atma: urun 1 ve 2.
$to = post_as( 'nwcs_handle_pool_bulk', 'nwcs_pool_bulk', array( 'urunler' => array( $product[1], $product[2] ), 'bulk_action' => 'trash:', 'ara' => 'ZZ deneme' ) );
switch_to_blog( nwcs_pool_blog_id() );
check( 'toplu cop: urun 1 copte', 'trash' === get_post_status( $product[1] ) );
check( 'toplu cop: urun 2 copte', 'trash' === get_post_status( $product[2] ) );
check( 'toplu cop: urun 3 yayinda', 'publish' === get_post_status( $product[3] ) );
restore_current_blog();
check( 'toplu cop: bildirim bulk_trashed adet=2', str_contains( $to, 'nwcs_pool=bulk_trashed' ) && str_contains( $to, 'adet=2' ) );
check( 'toplu cop: arama suzgeci korundu', str_contains( $to, 'ara=ZZ' ) );

// 2) Medya toplu silme: A (copteki urunde) ve B (yayindaki urunde) atlanir, C silinir.
$to = post_as( 'nwcs_handle_media_bulk_delete', 'nwcs_media_bulk_delete', array( 'gorseller' => array_values( $image ) ) );
check( 'medya: A korundu (copteki urunde)', 'attachment' === get_blog_post( nwcs_pool_blog_id(), $image['a'] )?->post_type );
check( 'medya: B korundu (yayindaki urunde)', 'attachment' === get_blog_post( nwcs_pool_blog_id(), $image['b'] )?->post_type );
check( 'medya: C silindi', null === get_blog_post( nwcs_pool_blog_id(), $image['c'] ) );
check( 'medya: bildirim adet=1 atlanan=2', str_contains( $to, 'adet=1' ) && str_contains( $to, 'atlanan=2' ) );

// 3) Cop kutusu: urun 1 geri gelir; kalici silmede urun 2 gider, yayindaki urun 3 atlanir.
$to = post_as( 'nwcs_handle_pool_trash_bulk', 'nwcs_pool_trash_bulk', array( 'urunler' => array( $product[1] ), 'trash_action' => 'restore' ) );
check( 'cop: urun 1 geri geldi (yayinda)', 'publish' === get_blog_status_for( $product[1] ) );
check( 'cop: bildirim bulk_restored adet=1', str_contains( $to, 'bulk_restored' ) && str_contains( $to, 'adet=1' ) );

// Urun 2 bir sitenin seciminde ve istisnalarinda olsun: kalici silmede ikisinden de cikmali.
$site_id = array_key_first( nwcs_editable_sites() );
switch_to_blog( $site_id );
$saved_selected  = get_option( NWCS_OPTION_SELECTED );
$saved_overrides = get_option( NWCS_OPTION_OVERRIDES );
update_option( NWCS_OPTION_SELECTED, array_merge( (array) $saved_selected, array( $product[2] ) ) );
update_option( NWCS_OPTION_OVERRIDES, (array) $saved_overrides + array( $product[2] => array( 'hidden' => 1 ) ) );
restore_current_blog();

$to = post_as( 'nwcs_handle_pool_trash_bulk', 'nwcs_pool_trash_bulk', array( 'urunler' => array( $product[2], $product[3] ), 'trash_action' => 'purge' ) );

switch_to_blog( $site_id );
$after_selected  = (array) get_option( NWCS_OPTION_SELECTED );
$after_overrides = (array) get_option( NWCS_OPTION_OVERRIDES );
check( 'cop: kalici silinen urun site seciminden cikti', ! in_array( $product[2], array_map( 'intval', $after_selected ), true ) );
check( 'cop: kalici silinen urun istisnalardan cikti', ! isset( $after_overrides[ $product[2] ] ) );
check( 'cop: diger secimler korundu', count( $after_selected ) === count( (array) $saved_selected ) );
foreach ( array( NWCS_OPTION_SELECTED => $saved_selected, NWCS_OPTION_OVERRIDES => $saved_overrides ) as $option => $value ) {
	false === $value ? delete_option( $option ) : update_option( $option, $value );
}
restore_current_blog();
check( 'cop: silme kancasi geri takildi', false !== has_action( 'deleted_post', 'nwcs_forget_deleted_product' ) );
check( 'cop: urun 2 kalici silindi', null === get_blog_post( nwcs_pool_blog_id(), $product[2] ) );
check( 'cop: yayindaki urun 3 silinmedi', 'publish' === get_blog_status_for( $product[3] ) );
check( 'cop: bildirim bulk_purged adet=1', str_contains( $to, 'bulk_purged' ) && str_contains( $to, 'adet=1' ) );

// 4) Bos secim.
$to = post_as( 'nwcs_handle_pool_trash_bulk', 'nwcs_pool_trash_bulk', array( 'trash_action' => 'purge' ) );
check( 'cop: bos secim trash_empty', str_contains( $to, 'trash_empty' ) );
$to = post_as( 'nwcs_handle_media_bulk_delete', 'nwcs_media_bulk_delete', array() );
check( 'medya: bos secim bulk_empty', str_contains( $to, 'bulk_empty' ) );

// 5) Sayfalar ciziliyor mu.
$_GET = array();
ob_start();
nwcs_render_media();
$html = ob_get_clean();
check( 'medya sayfasi: toplu form var', str_contains( $html, 'id="nwcs-media-bulk"' ) );
check( 'medya sayfasi: kullanimda urun baglantisi', str_contains( $html, 'urun=' . $product[1] ) );

switch_to_blog( nwcs_pool_blog_id() );
wp_trash_post( $product[3] );
restore_current_blog();
nwcs_pool_flush_cache();
ob_start();
nwcs_render_pool_trash();
$html = ob_get_clean();
check( 'cop sayfasi: toplu form ve kutu var', str_contains( $html, 'id="nwcs-trash-bulk"' ) && str_contains( $html, 'form="nwcs-trash-bulk"' ) );

// Temizlik.
switch_to_blog( nwcs_pool_blog_id() );
foreach ( $product as $id ) {
	wp_delete_post( $id, true );
}
foreach ( $image as $id ) {
	wp_delete_attachment( $id, true );
}
restore_current_blog();
nwcs_pool_flush_cache();

function get_blog_status_for( int $id ): string {
	switch_to_blog( nwcs_pool_blog_id() );
	$status = (string) get_post_status( $id );
	restore_current_blog();

	return $status;
}

echo $fail ? "\n$fail hata\n" : "\nHepsi gecti\n";
