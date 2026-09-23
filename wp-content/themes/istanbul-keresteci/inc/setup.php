<?php
/**
 * Tema kurulumu: tema etkinlestirilince sitenin eksiksiz acilmasi icin
 * gereken her seyi bir kez yapar. Ayri bir seed betigi gerekmez.
 *
 * - Sayfalar: Anasayfa, Hakkimizda, Urunlerimiz (ve her urun icin alt
 *   sayfa), Blog, Iletisim. Adres adi varsa dokunulmaz.
 * - Okuma ayarlari: ana sayfa Anasayfa, yazilar sayfasi Blog. Yalnizca
 *   henuz ayarlanmamissa yazilir; kullanicinin secimi ezilmez.
 * - Ornek blog yazilari ve kapak gorselleri: yalnizca ilk kurulumda, bir
 *   kez. Kullanici silerse geri gelmez (ik_demo_posts_done).
 *
 * Tetikleme: after_switch_theme (etkinlestirmeden sonraki ilk istekte,
 * sorgudan once) ve admin_init. Surum option'i (ik_setup_version) sayesinde
 * ayni surum ikinci kez calismaz.
 */

defined( 'ABSPATH' ) || exit;

const IK_SETUP_VERSION = 1;

add_action( 'after_switch_theme', 'ik_run_setup' );
add_action( 'admin_init', 'ik_maybe_setup' );

function ik_maybe_setup(): void {
	if ( (int) get_option( 'ik_setup_version' ) < IK_SETUP_VERSION ) {
		ik_run_setup();
	}
}

function ik_run_setup(): void {
	// Ayni istekte iki kanca birden tetiklenirse ikinci kez calisma.
	static $done = false;

	if ( $done ) {
		return;
	}

	$done = true;

	$pages = array(
		'anasayfa'    => 'Anasayfa',
		'hakkimizda'  => 'Hakkımızda',
		'urunlerimiz' => 'Ürünlerimiz',
		'blog'        => 'Blog',
		'iletisim'    => 'İletişim',
	);

	$ids = array();

	foreach ( $pages as $slug => $title ) {
		$ids[ $slug ] = ik_setup_page( $slug, $title );
	}

	if ( $ids['urunlerimiz'] ) {
		foreach ( nwcs_rows( 'products', 'catalog', 'items' ) as $row ) {
			$slug = sanitize_title( (string) ( $row['slug'] ?? '' ) );

			if ( '' !== $slug ) {
				ik_setup_page( $slug, (string) ( $row['title'] ?? $slug ), $ids['urunlerimiz'] );
			}
		}
	}

	// Okuma ayarlari: yalnizca henuz statik sayfa secilmemisse.
	if ( 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' ) ) {
		if ( $ids['anasayfa'] ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $ids['anasayfa'] );
		}
	}

	if ( ! get_option( 'page_for_posts' ) && $ids['blog'] ) {
		update_option( 'page_for_posts', $ids['blog'] );
	}

	if ( ! get_option( 'ik_demo_posts_done' ) ) {
		ik_setup_demo_posts();
		update_option( 'ik_demo_posts_done', 1 );
	}

	update_option( 'ik_setup_version', IK_SETUP_VERSION );
}

/**
 * Sayfayi olusturur; ayni yolda sayfa varsa (cop kutusu dahil) dokunmaz.
 */
function ik_setup_page( string $slug, string $title, int $parent = 0 ): int {
	$path = $parent ? get_page_uri( $parent ) . '/' . $slug : $slug;
	$page = get_page_by_path( $path, OBJECT, 'page' );

	if ( $page ) {
		return 'trash' === $page->post_status ? 0 : (int) $page->ID;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_name'   => $slug,
			'post_title'  => $title,
			'post_parent' => $parent,
			'post_status' => 'publish',
		)
	);

	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * Ornek blog yazilari (basliklar ve ozetler eski siteden) ve kapaklari.
 *
 * Adres adi zaten varsa yazi yeniden olusturulmaz. WordPress'in kendi
 * "Merhaba dunya" yazisi, listede gorunmesin diye taslaga alinir.
 */
function ik_setup_demo_posts(): void {
	$hello = get_page_by_path( 'merhaba-dunya', OBJECT, 'post' ) ?: get_page_by_path( 'hello-world', OBJECT, 'post' );

	if ( $hello && 'publish' === $hello->post_status ) {
		wp_update_post( array( 'ID' => $hello->ID, 'post_status' => 'draft' ) );
	}

	$posts = array(
		array(
			'slug'    => 'mese-agaci-nedir',
			'title'   => 'Meşe Ağacı Nedir? Meşe Ağacı Özellikleri Nelerdir?',
			'excerpt' => 'Meşe ağacı, genel olarak esmer ve kirli sarı rengindedir. Dış kısmı odunsu sarımsı kirli beyaz, göbek kısmı ise koyu sarı renkten oluşur.',
			'content' => "<p>Meşe ağacı, genel olarak esmer ve kirli sarı rengindedir. Dış kısmı odunsu sarımsı kirli beyaz renkte, göbek kısmı ise koyu sarı renkten oluşmaktadır.</p>\n<p>Birçok farklı türü bulunan meşe ağacı, açık pembe veya açık kahverengi tonlarında da olabilir. Sert veya yumuşak olarak iki farklı türde meşe ağacı vardır. Bu durum yetiştiği yerin veya bölgenin özelliğine göre değişir.</p>",
			'date'    => '2022-05-07 10:00:00',
			'image'   => array( 'blog-mese.jpg', 'Meşe kereste', 'Kurumaya dizilmiş, aralarına çıta konmuş meşe kereste istifi' ),
		),
		array(
			'slug'    => 'istanbul-kereste-fiyatlari',
			'title'   => 'İstanbul Kereste Fiyatları Nedir?',
			'excerpt' => 'Kereste tüm Türkiye genelinde oldukça yaygın kullanım alanına sahip bir üründür. İstanbul’da kereste fiyatları hakkında güncel bilgileri bu yazıda derledik.',
			'content' => "<p>Kereste tüm Türkiye genelinde oldukça yaygın kullanım alanına sahip bir üründür. İstanbul içerisinde kereste fiyatları hakkında güncel bilgileri derlediğimiz bu yazımızda sizlere güncel kereste fiyatları hakkında bilgiler derledik.</p>\n<p>Kereste pek çok alanda kullanıma uygun bir ürün olmasından ötürü kolay işleme alınarak hazırlanabilmektedir. Uygun fiyatlı olan bu keresteler mobilya, dekorasyon ve inşaat sektörlerinin kullanım alanlarında önemli bir yere sahiptir.</p>",
			'date'    => '2022-05-07 11:00:00',
			'image'   => array( 'blog-kereste.jpg', 'Kereste kesitleri', 'Üst üste dizilmiş kerestelerin kesim yüzleri' ),
		),
		array(
			'slug'    => 'ahsap-insaatlik-kereste-nedir',
			'title'   => 'Ahşap İnşaatlık Kereste Nedir?',
			'excerpt' => 'İnşaat yapı malzemesi alanlarında kullanımı oldukça yaygın olan ahşap inşaatlık keresteler son dönemlerde sıkça tercih edilmeye başlanmıştır.',
			'content' => "<p>İnşaat yapı malzeme alanlarında kullanımı oldukça yaygın olan ahşap inşaatlık keresteler son dönemlerde sıkça tercih edilmeye başlanmıştır. Performans bakımından oldukça avantajlı olan bu ürünler, kaliteli ve oldukça sağlam kerestelerdir.</p>\n<p>Ahşap inşaatlık keresteler kolay işlenebilir yapıya sahiptir ve oldukça dayanıklıdır. Kullanım alanı inşaat olmakla birlikte; çatı, palet ve ambalaj imalatlarında kullanıma oldukça uygun ürünlerdir.</p>",
			'date'    => '2022-05-07 12:00:00',
			'image'   => array( 'blog-insaatlik.jpg', 'İnşaatlık kereste', 'Kademeli istiflenmiş açık renkli inşaatlık kereste' ),
		),
	);

	foreach ( $posts as $post ) {
		$existing = get_posts(
			array(
				'name'           => $post['slug'],
				'post_type'      => 'post',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( $existing ) {
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_name'    => $post['slug'],
				'post_title'   => $post['title'],
				'post_excerpt' => $post['excerpt'],
				'post_content' => $post['content'],
				'post_status'  => 'publish',
				'post_date'    => $post['date'],
				'meta_input'   => array( '_ik_demo' => 1 ),
			)
		);

		if ( is_wp_error( $id ) || ! $id ) {
			continue;
		}

		$media = ik_setup_media( ...$post['image'] );

		if ( $media ) {
			set_post_thumbnail( $id, $media );
		}
	}
}

/**
 * Tema icindeki fotografi medya kutuphanesine bir kez ekler.
 *
 * Ayni dosya ikinci kez yuklenmez: ek kaydi _ik_asset meta anahtariyla
 * isaretlenir.
 */
function ik_setup_media( string $file, string $title, string $alt ): int {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_ik_asset', // phpcs:ignore WordPress.DB.SlowDBQuery
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

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_upload_bits( 'ik-' . $file, null, (string) file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_ik_asset', $file );

	return (int) $id;
}
