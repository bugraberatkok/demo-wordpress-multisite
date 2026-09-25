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

	// Metinler content/blog-posts.php'de; panel de ayni dosyadan okur.
	foreach ( (array) include get_theme_file_path( 'content/blog-posts.php' ) as $post ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_name'    => $post['slug'],
				'post_title'   => $post['title'],
				'post_excerpt' => $post['excerpt'],
				'post_content' => sanayi_palet_setup_paragraphs( $post['body'] ),
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
 * Bos satirla ayrilmis metni <p> paragraflarina cevirir.
 */
function sanayi_palet_setup_paragraphs( string $text ): string {
	$blocks = array_filter( array_map( 'trim', preg_split( '/\R\s*\R/u', trim( $text ) ) ) );

	return implode( "\n", array_map( static fn( string $block ): string => '<p>' . esc_html( $block ) . '</p>', $blocks ) );
}
