<?php
/**
 * Tema kurulumu: tema etkinlestirildiginde siteyi bir kez hazirlar.
 *
 * Yeni bir alt site acilip bu tema secildiginde, seed betigi olmadan da site
 * eksiksiz gorunsun diye:
 *   1. Sayfalar: anasayfa, urunler, hakkimizda, blog, iletisim (slug varsa dokunulmaz).
 *   2. Okuma ayarlari: statik on sayfa + /blog/ yazi sayfasi.
 *   3. Kalici baglantilar: duz (?p=) ise /%postname%/ yapilir; menu bunlara baglanir.
 *   4. Blog yazilari: sitede baska yazi yoksa uc "palet rehberi" yazisi, kapak
 *      gorselleri temanin assets/img klasorunden medya kitapligina eklenir.
 *
 * Metinler ve ana sayfa/sayfa gorselleri veritabanina yazilmaz: eklenti kayit
 * yokken manifest varsayilanini, tema da gorsel yoksa kendi gorselini gosterir
 * (sanayi_palet_image_or_default). Panelden yapilan her degisiklik bunlarin onune gecer.
 *
 * Idempotent: surum option'i tutulur, bir kez calisir. Kullanici bir sayfayi
 * ya da yaziyi silerse geri getirilmez.
 */

defined( 'ABSPATH' ) || exit;

const SANAYI_PALET_SETUP_VERSION = '1';

add_action( 'after_switch_theme', 'sanayi_palet_setup_site' );
add_action( 'admin_init', 'sanayi_palet_maybe_setup_site' );

/**
 * Yonetim paneline ilk giriste, kurulum hic calismadiysa calistir.
 * (after_switch_theme her ortamda tetiklenmeyebilir; ornegin tema WP-CLI ile atanip
 * ilk istek yonetimden gelirse.)
 */
function sanayi_palet_maybe_setup_site(): void {
	// Yedek yol yalnizca yonetici ya da WP-CLI icin: admin_init anonim
	// admin-post.php (form) isteklerinde de tetiklenir; ziyaretci kurulumu
	// (sayfa acma, kalici baglanti, rewrite) baslatamasin.
	if ( ! current_user_can( 'manage_options' ) && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	if ( SANAYI_PALET_SETUP_VERSION !== get_option( 'sanayi_palet_setup_version' ) ) {
		sanayi_palet_setup_site();
	}
}

function sanayi_palet_setup_site(): void {
	if ( SANAYI_PALET_SETUP_VERSION === get_option( 'sanayi_palet_setup_version' ) ) {
		return;
	}

	// Ayni anda iki istek gelirse ikinci kez calismasin.
	if ( get_transient( 'sanayi_palet_setup_lock' ) ) {
		return;
	}

	set_transient( 'sanayi_palet_setup_lock', 1, MINUTE_IN_SECONDS );

	$pages = sanayi_palet_setup_pages();

	if ( ! empty( $pages['anasayfa'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $pages['anasayfa'] );
	}

	if ( ! empty( $pages['blog'] ) ) {
		update_option( 'page_for_posts', $pages['blog'] );
	}

	sanayi_palet_setup_posts();

	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}

	flush_rewrite_rules( false );

	update_option( 'sanayi_palet_setup_version', SANAYI_PALET_SETUP_VERSION );
	delete_transient( 'sanayi_palet_setup_lock' );
}

/**
 * Menu sayfalarini olusturur; slug zaten varsa (cop dahil) dokunmaz.
 *
 * @return array<string,int> slug => sayfa kimligi
 */
function sanayi_palet_setup_pages(): array {
	$pages = array(
		'anasayfa'   => 'Anasayfa',
		'urunler'    => 'Ürünler',
		'hakkimizda' => 'Hakkımızda',
		'blog'       => 'Blog',
		'iletisim'   => 'İletişim',
	);

	$ids = array();

	foreach ( $pages as $slug => $title ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );

		if ( $existing ) {
			$ids[ $slug ] = (int) $existing->ID;
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_name'   => $slug,
				'post_title'  => $title,
				'post_status' => 'publish',
			)
		);

		$ids[ $slug ] = is_wp_error( $id ) ? 0 : (int) $id;
	}

	return $ids;
}

/**
 * Blog yazilari: yalnizca sitede WordPress'in "Merhaba dunya" yazisi disinda
 * yazi yoksa olusturulur. Yazilar isaretlenir (_sanayi_palet_seed); kurulum
 * bir kez calistigi icin silinen yazi geri gelmez.
 */
function sanayi_palet_setup_posts(): void {
	$hello_slugs = array( 'merhaba-dunya', 'hello-world' );

	// WP_Query'nin post_name__not_in parametresi burada dikkate alinmiyor;
	// ornek yazi slug'a bakilarak PHP tarafinda ayiklanir.
	$existing = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => 5,
		)
	);

	foreach ( $existing as $post ) {
		if ( ! in_array( $post->post_name, $hello_slugs, true ) ) {
			return;
		}
	}

	// Yeni sitenin ornek yazisi listede gorunmesin; silinmez, taslaga alinir.
	foreach ( $hello_slugs as $slug ) {
		$hello = get_page_by_path( $slug, OBJECT, 'post' );

		if ( $hello && 'publish' === $hello->post_status ) {
			wp_update_post( array( 'ID' => $hello->ID, 'post_status' => 'draft' ) );
		}
	}

	foreach ( sanayi_palet_seed_posts() as $post ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_name'    => $post['slug'],
				'post_title'   => $post['title'],
				'post_excerpt' => $post['excerpt'],
				'post_content' => $post['content'],
				'post_status'  => 'publish',
				'post_date'    => $post['date'],
				'meta_input'   => array( '_sanayi_palet_seed' => $post['slug'] ),
			),
			true
		);

		if ( is_wp_error( $id ) ) {
			continue;
		}

		$media = sanayi_palet_theme_attachment( $post['image'], $post['image_title'], $post['image_alt'] );

		if ( $media ) {
			set_post_thumbnail( $id, $media );
		}
	}
}

/**
 * Temanin assets/img dosyasini medya kitapligina bir kez ekler.
 * Ayni dosya daha once eklendiyse (_sanayi_palet_asset) mevcut kimligi dondurur.
 */
function sanayi_palet_theme_attachment( string $file, string $title, string $alt ): int {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_sanayi_palet_asset', // phpcs:ignore WordPress.DB.SlowDBQuery -- tek seferlik kurulum.
			'meta_value'     => $file, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	$source = get_theme_file_path( 'assets/img/' . $file );

	if ( ! file_exists( $source ) ) {
		return 0;
	}

	$upload = wp_upload_bits( $file, null, (string) file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_sanayi_palet_asset', $file );

	return (int) $id;
}

/**
 * Uc palet rehberi yazisi. Basliklar sanayipalet.com'dan, metinler bu tema icin yazildi.
 */
function sanayi_palet_seed_posts(): array {
	return array(
		array(
			'slug'        => 'ikinci-el-palet-nedir',
			'title'       => 'İkinci El Palet Nedir?',
			'excerpt'     => 'Daha önce kullanılmış, kontrol edilip gerekirse onarılarak yeniden sevkiyata hazırlanan paletler. Tek yönlü gönderimlerde maliyeti düşürür.',
			'content'     => "<p>İkinci el palet, daha önce bir sevkiyatta kullanılmış ve kontrolden geçirildikten sonra yeniden kullanıma sunulan palettir. Kırık tahta ve bloklar değiştirilir, taşıma kapasitesini etkileyen hasarlı paletler ayrılır.</p>\n<p>Geri dönmeyecek, tek yönlü gönderimlerde yeni palete göre belirgin bir maliyet avantajı sağlar. Ürününüzün ağırlığını ve ölçüsünü bildirirseniz uygun paleti birlikte seçeriz.</p>",
			'date'        => '2022-04-19 10:00:00',
			'image'       => 'kullanilmis-palet.jpg',
			'image_title' => 'Kullanılmış euro palet',
			'image_alt'   => 'Beton zeminde duran, kullanılmış EUR damgalı ahşap palet',
		),
		array(
			'slug'        => 'euro-palet-nedir',
			'title'       => 'Euro Palet Nedir?',
			'excerpt'     => '1200 × 800 mm ölçülerindeki standart palet. Avrupa’daki yük taşıma sistemleri bu ölçüye göre kurulduğu için ihracatta en çok tercih edilen tiptir.',
			'content'     => "<p>Euro palet 1200 × 800 mm ölçülerinde, standart bir yapıyla üretilen ahşap palettir. Avrupa’daki kamyon, raf ve forklift sistemleri bu ölçüye göre kurulduğu için ihracatta en yaygın kullanılan tiptir.</p>\n<p>İhracatta kullanılan ahşap paletlerin ISPM 15’e göre ısıl işlemden geçmiş ve damgalanmış olması gerekir. Paletlerimize bu damgayı Tarım ve Orman Bakanlığı’nın verdiği izinle basıyoruz.</p>",
			'date'        => '2022-04-19 11:00:00',
			'image'       => 'epal-palet-yigini.jpg',
			'image_title' => 'EPAL euro palet yığını',
			'image_alt'   => 'Üst üste dizilmiş EPAL ve EUR damgalı euro paletler',
		),
		array(
			'slug'        => 'ahsap-palet-nedir',
			'title'       => 'Ahşap Palet Nedir?',
			'excerpt'     => 'Yüklerin forklift ve transpalet ile taşınmasını, istiflenmesini ve depolanmasını sağlayan ahşap taşıma platformu.',
			'content'     => "<p>Ahşap palet, ürünlerin forklift ya da transpaletle kaldırılıp taşınabilmesi için üzerine yüklendiği taşıma platformudur. Depolamada istiflemeyi kolaylaştırır, sevkiyatta yükü yerden keser.</p>\n<p>Standart ölçülerin yanında istediğiniz ebatta özel palet üretiyoruz. Paletlerimiz 1.500 kg statik yük taşıyacak şekilde yapılır.</p>",
			'date'        => '2022-04-19 12:00:00',
			'image'       => 'ahsap-palet.jpg',
			'image_title' => 'Ahşap palet',
			'image_alt'   => 'Beyaz zemin üzerinde yeni ahşap palet',
		),
	);
}
