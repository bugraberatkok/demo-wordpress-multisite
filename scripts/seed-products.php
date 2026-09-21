<?php
/**
 * Merkezi urun havuzunu doldurur (yalnizca agin ana sitesinde calisir).
 *
 *   wp eval-file /scripts/seed-products.php --url=http://localhost:8080/
 *
 * Tekrar calistirilabilir: ayni urunu ikinci kez olusturmaz, alanlarini
 * gunceller. Gorseller havuz sitesinin medya kitapligina uretilir.
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

require_once __DIR__ . '/seed-lib.php';

if ( ! function_exists( 'nwcs_pool_products' ) ) {
	WP_CLI::error( 'Network Content Studio eklentisi etkin degil.' );
}

if ( get_current_blog_id() !== nwcs_pool_blog_id() ) {
	WP_CLI::error( 'Bu betik agin ana sitesinde calistirilmalidir (--url=http://localhost:8080/).' );
}

/**
 * Havuza urun ekler veya gunceller. Slug anahtardir.
 */
function nwcs_seed_product( array $data ): int {
	$existing = get_posts(
		array(
			'post_type'      => NWCS_PRODUCT_TYPE,
			'post_status'    => 'any',
			'name'           => $data['slug'],
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	$postarr = array(
		'post_type'    => NWCS_PRODUCT_TYPE,
		'post_status'  => 'publish',
		'post_title'   => $data['title'],
		'post_name'    => $data['slug'],
		'post_content' => $data['body'] ?? '',
		'menu_order'   => $data['order'] ?? 0,
	);

	if ( $existing ) {
		$postarr['ID'] = (int) $existing[0];
		$id            = (int) wp_update_post( $postarr );
	} else {
		$id = (int) wp_insert_post( $postarr );
	}

	if ( ! $id ) {
		WP_CLI::warning( 'Urun olusturulamadi: ' . $data['slug'] );

		return 0;
	}

	update_post_meta( $id, '_nwcs_short', $data['short'] ?? '' );
	update_post_meta( $id, '_nwcs_price', $data['price'] ?? '' );
	update_post_meta( $id, '_nwcs_spec', $data['spec'] ?? '' );

	wp_set_object_terms( $id, $data['categories'] ?? array(), NWCS_PRODUCT_TAX, false );

	$image_id = nwcs_seed_image(
		'urun-' . $data['slug'],
		$data['image_label'],
		'Örnek görsel: ' . $data['title'] . ' (demo yer tutucusu)',
		900,
		700,
		$data['rgb']
	);

	if ( $image_id ) {
		set_post_thumbnail( $id, $image_id );
	}

	return $id;
}

/* ------------------------------------------------------------------ */
/* Demo urunleri                                                        */
/* ------------------------------------------------------------------ */

$products = array(
	array(
		'slug'        => 'ahsap-palet',
		'title'       => 'Ahşap Palet',
		'short'       => 'Taşıma ve depolama için standart ölçülerde ahşap palet.',
		'price'       => '',
		'spec'        => 'Ölçü: talebe göre',
		'categories'  => array( 'Ahşap Ambalaj' ),
		'image_label' => 'Ahsap palet',
		'rgb'         => array( 122, 79, 52 ),
		'order'       => 10,
	),
	array(
		'slug'        => 'euro-palet',
		'title'       => 'Euro Palet',
		'short'       => 'Avrupa standardı ölçülerde palet; ihracat sevkiyatına uygun.',
		'price'       => '450 TL',
		'spec'        => '80 × 120 cm',
		'categories'  => array( 'Ahşap Ambalaj' ),
		'image_label' => 'Euro palet',
		'rgb'         => array( 128, 86, 57 ),
		'order'       => 20,
		'body'        => "Euro palet, Avrupa'da yaygın kullanılan 80 × 120 cm ölçüsündeki standart paletlerdir. İhracat sevkiyatlarında talep edilen ölçü genellikle budur.\n\nIsıl işlem (ISPM-15) gerektiren gönderiler için işlem görmüş ürün temin edilir. Stok durumuna göre aynı hafta içinde teslim yapılabilir.\n\nBu metin demo amaçlıdır; gerçek ürün açıklaması sizden geldiğinde Ürün Havuzu'ndan düzenlenir.",
	),
	array(
		'slug'        => 'ahsap-kafes',
		'title'       => 'Ahşap Kafes',
		'short'       => 'Hacimli ürünlerin taşınması için kafes tipi ambalaj.',
		'price'       => '',
		'spec'        => 'Özel ölçü',
		'categories'  => array( 'Ahşap Ambalaj' ),
		'image_label' => 'Ahsap kafes',
		'rgb'         => array( 134, 93, 62 ),
		'order'       => 30,
	),
	array(
		'slug'        => 'ahsap-sandik',
		'title'       => 'Ahşap Sandık',
		'short'       => 'Makine ve yedek parça sevkiyatı için kapalı sandık.',
		'price'       => '1.250 TL’den başlayan',
		'spec'        => 'Özel ölçü',
		'categories'  => array( 'Ahşap Ambalaj' ),
		'image_label' => 'Ahsap sandik',
		'rgb'         => array( 140, 100, 67 ),
		'order'       => 40,
	),
	array(
		'slug'        => 'ikinci-el-palet',
		'title'       => 'İkinci El Palet',
		'short'       => 'Kontrol edilip onarılmış, yeniden kullanıma uygun paletler.',
		'price'       => '180 TL',
		'spec'        => 'Stok durumuna göre',
		'categories'  => array( 'Ahşap Ambalaj', 'Geri Dönüşüm' ),
		'image_label' => 'Ikinci el palet',
		'rgb'         => array( 146, 107, 72 ),
		'order'       => 50,
	),
	array(
		'slug'        => 'kereste',
		'title'       => 'Kereste',
		'short'       => 'Çam ve karaçam kereste, sunta ve kontrplak levha.',
		'price'       => '',
		'spec'        => 'Ölçüye göre kesim',
		'categories'  => array( 'Kereste' ),
		'image_label' => 'Kereste',
		'rgb'         => array( 62, 78, 94 ),
		'order'       => 60,
	),
	array(
		'slug'        => 'ahsap-dekorasyon',
		'title'       => 'Ahşap Dekorasyon',
		'short'       => 'Kamelya, salıncak, pergola ve bahçe ürünleri.',
		'price'       => '',
		'spec'        => 'Projeye göre',
		'categories'  => array( 'Dekorasyon' ),
		'image_label' => 'Dekorasyon',
		'rgb'         => array( 80, 96, 110 ),
		'order'       => 70,
	),
	array(
		'slug'        => 'hirdavat-grubu',
		'title'       => 'Hırdavat Grubu',
		'short'       => 'Bağlantı elemanları ve tamamlayıcı hırdavat grubu.',
		'price'       => '',
		'spec'        => 'Adet bazlı',
		'categories'  => array( 'Hırdavat' ),
		'image_label' => 'Hirdavat',
		'rgb'         => array( 98, 112, 126 ),
		'order'       => 80,
	),
);

$created = array();

foreach ( $products as $data ) {
	$id = nwcs_seed_product( $data );

	if ( $id ) {
		$created[ $data['slug'] ] = $id;
	}
}

WP_CLI::success( sprintf( 'Ürün havuzu hazır: %d ürün.', count( $created ) ) );

foreach ( $created as $slug => $id ) {
	WP_CLI::line( sprintf( '  #%d  %s', $id, $slug ) );
}
