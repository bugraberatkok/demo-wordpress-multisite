<?php
/**
 * Kocist temasi.
 *
 * Tema gorunur metinleri sabit yazmaz; tum degerler Network Content Studio
 * veri katmanindan (nwcs_*) okunur. Eklenti devre disi kalirsa sayfa fatal
 * vermesin diye asagida guvenli yedek fonksiyonlar tanimlanir.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'nwcs_field' ) ) {
	function nwcs_field( $page, $component, $field, $fallback = null ) {
		return null === $fallback ? '' : $fallback;
	}
	function nwcs_rows( $page, $component, $field ) {
		return array();
	}
	function nwcs_image( $page, $component, $field, $size = 'large' ) {
		return array( 'id' => 0, 'url' => '', 'alt' => '' );
	}
	function nwcs_image_by_id( $id, $size = 'large' ) {
		return array( 'id' => 0, 'url' => '', 'alt' => '' );
	}
	function nwcs_the_icon( $key, $class = '', $size = 24 ) {}
	function nwcs_edit_attr( $page, $component, $field = '', $row = null, $sub = '' ) {}
	function nwcs_section_order( $page = 'home' ) {
		return array( 'catalog', 'latest', 'process', 'products', 'blog' );
	}
}

require_once __DIR__ . '/inc/blog.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/katalog.php';

add_action( 'after_setup_theme', 'kocist_setup' );
function kocist_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script' ) );
}

/*
 * Temanin sablonla cizdigi sayfalar (page.php slug'a bakiyor). Kurulum
 * betigi bunlari olusturmasa da site eksiksiz acilsin diye tema kendisi
 * acar. Surum secenegi sayesinde her istekte sorgu yapilmaz; var olan bir
 * sayfaya dokunulmaz, kullanici silerse ayni surumde geri getirilmez.
 *
 * Surum 2: Insan Kaynaklari ve Blog sayfalari, yazilar sayfasi ayari ve
 * ilk kurulum blog yazilari (inc/blog.php).
 * Surum 4: Banka Bilgilerimiz sayfasi.
 */
const KOCIST_PAGES_VERSION = '4';

add_action( 'after_switch_theme', 'kocist_ensure_pages' );
add_action( 'admin_init', 'kocist_maybe_ensure_pages' );

function kocist_maybe_ensure_pages(): void {
	// Yedek yol yalnizca yonetici ya da WP-CLI icin: admin_init anonim
	// admin-post.php (form) isteklerinde de tetiklenir; ziyaretci kurulumu
	// (sayfa acma, kalici baglanti, rewrite) baslatamasin.
	if ( ! current_user_can( 'manage_options' ) && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	if ( KOCIST_PAGES_VERSION !== get_option( 'kocist_pages_version' ) ) {
		kocist_ensure_pages();
	}
}

function kocist_ensure_pages(): void {
	// Ayni istekte iki kanca birden tetiklenirse ikinci kez calisma.
	static $done = false;

	if ( $done ) {
		return;
	}

	$done = true;

	$pages = array(
		'kurumsal'         => 'Kurumsal',
		'urun'             => 'Ahşap Tavuk Kümesi',
		'iletisim'         => 'İletişim',
		'insan-kaynaklari' => 'İnsan Kaynakları',
		'blog'             => 'Blog – Haberler',
		'katalog'          => 'Katalog',
		'banka-bilgilerimiz' => 'Banka Bilgilerimiz',
	);

	$ids = array();

	foreach ( $pages as $slug => $title ) {
		$ids[ $slug ] = kocist_ensure_page( $slug, $title );
	}

	/*
	 * Yazilar sayfasi yalnizca statik on sayfa secildiginde calisir. On sayfa
	 * hic secilmemisse (yeni site) bos bir "Ana Sayfa" acilip secilir;
	 * front-page.php her iki durumda da ayni ana sayfayi cizer. Kullanicinin
	 * yaptigi secim ezilmez.
	 */
	if ( 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' ) ) {
		$front = kocist_ensure_page( 'ana-sayfa', 'Ana Sayfa' );

		if ( $front && ! get_option( 'page_on_front' ) ) {
			update_option( 'page_on_front', $front );
		}

		if ( get_option( 'page_on_front' ) ) {
			update_option( 'show_on_front', 'page' );
		}
	}

	if ( ! get_option( 'page_for_posts' ) && $ids['blog'] ) {
		update_option( 'page_for_posts', $ids['blog'] );
	}

	kocist_seed_blog_posts();

	update_option( 'kocist_pages_version', KOCIST_PAGES_VERSION );
}

/**
 * Sayfayi acar; ayni adres adinda sayfa varsa ona dokunmadan kimligini dondurur.
 */
function kocist_ensure_page( string $slug, string $title ): int {
	$page = get_page_by_path( $slug, OBJECT, 'page' );

	if ( $page ) {
		return 'trash' === $page->post_status ? 0 : (int) $page->ID;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_name'   => $slug,
			'post_title'  => $title,
		)
	);

	return is_wp_error( $id ) ? 0 : (int) $id;
}

add_action( 'wp_enqueue_scripts', 'kocist_assets' );
function kocist_assets(): void {
	/*
	 * Tailwind tarayici derlemesi (CDN). Tasarim asamasi icin; derleme adimi yok.
	 * Footer'a degil head'e aliniyor: JIT tarayicisi sayfa boyanmadan calismali,
	 * aksi halde stilsiz an (FOUC) uzuyor. Surum null -> CDN URL'ine ?ver eklenmez.
	 */
	wp_enqueue_script(
		'tailwind-browser',
		'https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4',
		array(),
		null,
		false
	);

	/*
	 * Yazi tipi: Archivo. Saglam govdeli, endustriyel bir grotesk; basliklarda
	 * sikca dizilir, govdede rahat okunur. Tek aile: baslik ve govde arasinda
	 * kopukluk olmaz. Google Fonts latin-ext alt kumesini unicode-range ile verir.
	 * Surum null: URL'e ?ver eklenmesin, aksi halde onbellek anahtari bozulur.
	 */
	wp_enqueue_style(
		'kocist-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wght@400..800&display=swap',
		array(),
		null
	);

	// Surum dosyanin degisim zamani: stil degisince tarayici eski kopyayi tutmasin.
	wp_enqueue_style( 'kocist-style', get_stylesheet_uri(), array( 'kocist-fonts' ), (string) filemtime( get_stylesheet_directory() . '/style.css' ) );

	// Header kendi dosyasinda: style.css bolum bolum Tailwind'e tasinirken
	// kucultulecek, header kurallari onunla birlikte dagilmasin.
	wp_enqueue_style(
		'kocist-header',
		get_theme_file_uri( 'assets/css/header.css' ),
		array( 'kocist-style' ),
		wp_get_theme()->get( 'Version' )
	);

	// Footer ve ustundeki hareketli serit her sayfada.
	wp_enqueue_style(
		'kocist-footer',
		get_theme_file_uri( 'assets/css/footer.css' ),
		array( 'kocist-style' ),
		wp_get_theme()->get( 'Version' )
	);

	// Kayan menu gostergesi, acilir alt menuler ve mobil menu.
	wp_enqueue_script(
		'kocist-nav',
		get_theme_file_uri( 'assets/js/nav.js' ),
		array(),
		(string) filemtime( get_theme_file_path( 'assets/js/nav.js' ) ),
		true
	);

	// Hero ve urun gamlari yalnizca ana sayfada; varliklari da orada yuklensin.
	if ( is_front_page() ) {
		wp_enqueue_style(
			'kocist-hero',
			get_theme_file_uri( 'assets/css/hero.css' ),
			array( 'kocist-style' ),
			(string) filemtime( get_theme_file_path( 'assets/css/hero.css' ) )
		);

		wp_enqueue_style(
			'kocist-catalog',
			get_theme_file_uri( 'assets/css/catalog.css' ),
			array( 'kocist-style' ),
			wp_get_theme()->get( 'Version' )
		);

		wp_enqueue_script(
			'kocist-hero',
			get_theme_file_uri( 'assets/js/hero.js' ),
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);

		wp_enqueue_script(
			'kocist-catalog',
			get_theme_file_uri( 'assets/js/catalog.js' ),
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);

		// Siparis sureci: cizgi animasyonu bolum gorunume girince baslar.
		wp_enqueue_style(
			'kocist-process',
			get_theme_file_uri( 'assets/css/process.css' ),
			array( 'kocist-style' ),
			(string) filemtime( get_theme_file_path( 'assets/css/process.css' ) )
		);

		wp_enqueue_script(
			'kocist-process',
			get_theme_file_uri( 'assets/js/process.js' ),
			array(),
			(string) filemtime( get_theme_file_path( 'assets/js/process.js' ) ),
			true
		);

		// Son eklenen urunler seridi. Surum dosya zamanindan: degisiklik
		// tarayici onbelleginde takilmasin.
		wp_enqueue_style(
			'kocist-latest',
			get_theme_file_uri( 'assets/css/latest.css' ),
			array( 'kocist-style' ),
			(string) filemtime( get_theme_file_path( 'assets/css/latest.css' ) )
		);

		wp_enqueue_script(
			'kocist-latest',
			get_theme_file_uri( 'assets/js/latest.js' ),
			array(),
			(string) filemtime( get_theme_file_path( 'assets/js/latest.js' ) ),
			true
		);
	}

	// Iletisim sayfasi varliklari yalnizca o sayfada. Betik yok: form demo,
	// harita kendi iframe'i icinde calisiyor.
	if ( is_page( 'iletisim' ) ) {
		wp_enqueue_style(
			'kocist-contact',
			get_theme_file_uri( 'assets/css/contact.css' ),
			array( 'kocist-style' ),
			wp_get_theme()->get( 'Version' )
		);
	}

	// Katalog: belge ve sertifika sekmeleri, sertifika buyuk gorunumu.
	if ( is_page( 'katalog' ) ) {
		wp_enqueue_style(
			'kocist-katalog',
			get_theme_file_uri( 'assets/css/katalog.css' ),
			array( 'kocist-style' ),
			(string) filemtime( get_theme_file_path( 'assets/css/katalog.css' ) )
		);

		wp_enqueue_script(
			'kocist-katalog',
			get_theme_file_uri( 'assets/js/katalog.js' ),
			array(),
			(string) filemtime( get_theme_file_path( 'assets/js/katalog.js' ) ),
			true
		);
	}

	// Banka Bilgilerimiz: hesap kartlari ve IBAN kopyalama.
	if ( is_page( 'banka-bilgilerimiz' ) ) {
		wp_enqueue_style(
			'kocist-banka',
			get_theme_file_uri( 'assets/css/banka.css' ),
			array( 'kocist-style' ),
			(string) filemtime( get_theme_file_path( 'assets/css/banka.css' ) )
		);

		wp_enqueue_script(
			'kocist-banka',
			get_theme_file_uri( 'assets/js/banka.js' ),
			array(),
			(string) filemtime( get_theme_file_path( 'assets/js/banka.js' ) ),
			true
		);
	}

	// Kurumsal ve Insan Kaynaklari sayfalari.
	if ( is_page( array( 'kurumsal', 'insan-kaynaklari' ) ) ) {
		wp_enqueue_style(
			'kocist-pages',
			get_theme_file_uri( 'assets/css/pages.css' ),
			array( 'kocist-style' ),
			wp_get_theme()->get( 'Version' )
		);
	}

	// Blog listesi, arsivler, tekil yazi ve ana sayfadaki son yazilar. Kategori
	// ve urun detayi WordPress'e yazi listesi gibi gorunur; onlarda gerekmez.
	$is_blog_like = ( is_home() || is_archive() ) && ! kocist_is_catalog_request() && ! get_query_var( 'nwcs_product' );

	if ( $is_blog_like || is_singular( 'post' ) || is_front_page() ) {
		wp_enqueue_style(
			'kocist-blog',
			get_theme_file_uri( 'assets/css/blog.css' ),
			array( 'kocist-style' ),
			wp_get_theme()->get( 'Version' )
		);
	}

	// Kategori sayfalari, konum yolu ve urun detayindaki "ayni kategoride" seridi.
	$is_product_detail = (bool) get_query_var( 'nwcs_product' );

	if ( kocist_is_catalog_request() || $is_product_detail ) {
		wp_enqueue_style(
			'kocist-category',
			get_theme_file_uri( 'assets/css/category.css' ),
			array( 'kocist-style' ),
			(string) filemtime( get_theme_file_path( 'assets/css/category.css' ) )
		);
	}

	// Kategori sayfasinin bos durum butonlari urun sayfasinin buton stilini kullanir.
	if ( kocist_is_catalog_request() ) {
		wp_enqueue_style(
			'kocist-product',
			get_theme_file_uri( 'assets/css/product.css' ),
			array( 'kocist-style' ),
			wp_get_theme()->get( 'Version' )
		);
	}

	// Urun sayfasi varliklari: ornek urun sayfasi ve havuz urunu detayi.
	if ( is_page( 'urun' ) || $is_product_detail ) {
		wp_enqueue_style(
			'kocist-product',
			get_theme_file_uri( 'assets/css/product.css' ),
			array( 'kocist-style' ),
			wp_get_theme()->get( 'Version' )
		);

		wp_enqueue_script(
			'kocist-product',
			get_theme_file_uri( 'assets/js/product.js' ),
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
}

/**
 * Ana menuyu alt menuleriyle birlikte kurar.
 *
 * Ust seviye 'global.header.menu' repeater'indan gelir. Bir satirin 'submenu'
 * degeri bos degilse, o deger bir bilesen anahtari olarak okunur ve alt menu
 * ogeleri 'global.<anahtar>.items' repeater'indan alinir.
 *
 * Neden ayri bilesenler: sema bir repeater'a en fazla 50 satir veriyor
 * (schema.php, NWCS_REPEATER_MAX_CAP) ve repeater icinde repeater'i
 * reddediyor. Tum menu tek listeye sigmiyor; ayrica 60 satirlik tek form
 * panelde duzenlenebilir olmaktan cikardi.
 *
 * 'submenu' degeri panelden gelen serbest metindir, ama manifestte karsiligi
 * olmayan bir anahtar nwcs_rows() tarafindan bos dizi olarak doner; uydurma
 * bir deger menuyu bozmaz, yalnizca acilir menu olusmaz.
 *
 * @return array Her oge: array{ label: string, url: string, children: array }
 */
function kocist_menu_items(): array {
	$menu   = array();
	$groups = kocist_catalog_groups();

	foreach ( nwcs_rows( 'global', 'header', 'menu' ) as $row ) {
		$label = trim( (string) ( $row['label'] ?? '' ) );

		if ( '' === $label ) {
			continue;
		}

		$children = array();
		$submenu  = trim( (string) ( $row['submenu'] ?? '' ) );
		$url      = (string) ( $row['url'] ?? '' );

		// Urun grubuysa (inc/catalog.php) alt ogeler kategori sayfalarina,
		// ust oge de panelde hala varsayilan katalog capasi yaziyorsa grup
		// sayfasina gider.
		$group = $groups[ sanitize_title( $label ) ] ?? null;

		if ( $group && kocist_is_catalog_placeholder( $url ) ) {
			$url = $group['url'];
		}

		if ( '' !== $submenu ) {
			foreach ( nwcs_rows( 'global', $submenu, 'items' ) as $child ) {
				$child_label = trim( (string) ( $child['label'] ?? '' ) );

				if ( '' === $child_label ) {
					continue;
				}

				$child_url = (string) ( $child['url'] ?? '' );

				if ( $group && kocist_is_dead_anchor( $child_url ) ) {
					$child_url = $group['subs'][ sanitize_title( substr( trim( $child_url ), 1 ) ) ]['url'] ?? $child_url;
				}

				$children[] = array(
					'label' => $child_label,
					'url'   => $child_url,
				);
			}
		}

		$menu[] = array(
			'label'    => $label,
			'url'      => $url,
			'children' => $children,
			'group'    => $group,
		);
	}

	return $menu;
}

/**
 * Acilir menu kac sutuna bolunsun.
 *
 * Hirdavat'ta 24, Dekorasyon'da 14 oge var; tek sutunda panel ekrani asiyor.
 * Sutun sayisi oge sayisindan turetilir, elle ayar gerekmez.
 */
function kocist_menu_columns( int $count ): int {
	if ( $count > 16 ) {
		return 3;
	}

	if ( $count > 7 ) {
		return 2;
	}

	return 1;
}

/**
 * Havuz urunu detay sayfasinda (/urun/<slug>/) sekme basligi urunun adi olsun.
 *
 * Detay sayfasi eklentinin sorgu degiskeniyle aciliyor; WordPress'in kendi
 * sorgusunda bir yazi olmadigindan baslik yalnizca site adina dusuyordu.
 */
add_filter( 'document_title_parts', 'kocist_product_title_parts' );
function kocist_product_title_parts( array $parts ): array {
	$slug = get_query_var( 'nwcs_product' );

	if ( ! $slug || ! function_exists( 'nwcs_site_product_by_slug' ) ) {
		return $parts;
	}

	$product = nwcs_site_product_by_slug( sanitize_title( (string) $slug ) );

	if ( ! $product || '' === trim( (string) ( $product['title'] ?? '' ) ) ) {
		return $parts;
	}

	$parts['title'] = $product['title'];
	$parts['site']  = get_bloginfo( 'name', 'display' );
	unset( $parts['tagline'] );

	return $parts;
}

/**
 * Favicon: kocist.com.tr'nin kendi yaprak ikonu (42x42, canli siteden).
 * Panelde (Ozellestir > Site Kimligi) site ikonu secilirse WordPress'inki
 * gecerli olur; tema kendi ikonunu basmaz.
 */
add_action( 'wp_head', 'kocist_favicon', 2 );
add_action( 'login_head', 'kocist_favicon' );
function kocist_favicon(): void {
	if ( has_site_icon() ) {
		return;
	}

	printf(
		'<link rel="icon" type="image/png" href="%s" />' . "
",
		esc_url( get_theme_file_uri( 'assets/img/favicon.png' ) )
	);
}

/**
 * Ana sayfa bolum sirasi.
 *
 * Panel, kayitli siraya sonradan eklenen bolumu en sona koyuyor. Sonradan
 * eklenen bolum kayitli sirada yoksa (kullanici henuz yerini secmemis)
 * tasarlandigi yere alinir; panelde sira kaydedildiyse o gecerlidir:
 * son eklenen urunler urun gruplarinin, siparis sureci son eklenenlerin altina.
 */
function kocist_home_sections(): array {
	$order  = nwcs_section_order( 'home' );
	$stored = get_option( 'nwcs_section_order', array() );
	$saved  = is_array( $stored ) && isset( $stored['home'] ) && is_array( $stored['home'] ) ? $stored['home'] : array();

	$placements = array(
		'latest'  => 'catalog',
		'process' => 'latest',
	);

	foreach ( $placements as $key => $after ) {
		if ( in_array( $key, $saved, true ) || ! in_array( $key, $order, true ) || ! in_array( $after, $order, true ) ) {
			continue;
		}

		$order = array_values( array_diff( $order, array( $key ) ) );
		array_splice( $order, (int) array_search( $after, $order, true ) + 1, 0, array( $key ) );
	}

	return $order;
}

/**
 * Blog listesi ve arsivlerde sayfa basina 12 yazi: uc sutunlu izgarayi
 * tam satirlarla doldurur.
 */
add_action( 'pre_get_posts', 'kocist_blog_per_page' );
function kocist_blog_per_page( WP_Query $query ): void {
	if ( ! is_admin() && $query->is_main_query() && ( $query->is_home() || $query->is_archive() ) ) {
		$query->set( 'posts_per_page', 12 );
	}
}

/**
 * Bolum sablonunu basar. $args sablonun icinde ayni adla okunur
 * (ornegin page-head.php hangi sayfanin basligini cizecegini buradan alir).
 */
function kocist_section( string $key, array $args = array() ): void {
	$file = get_theme_file_path( "template-parts/sections/{$key}.php" );

	if ( file_exists( $file ) ) {
		include $file;
	}
}

/**
 * Menu ogesi su an goruntulenen sayfayi mi isaret ediyor?
 *
 * Yalnizca sayfa yolu karsilastirilir. Capali baglantilar (/#katalog, #kereste)
 * ve dis adresler hicbir zaman aktif sayilmaz; kayan gosterge bunlara
 * demirlenmez. Yol, kocist_link() ile cozulmus tam adresten alinir; alt dizin
 * kurulumunda (/kocist/kurumsal/) istek yoluyla bire bir karsilastirilabilsin.
 */
function kocist_is_current_menu_item( string $url ): bool {
	$url = trim( $url );

	if ( '' === $url || str_contains( $url, '#' ) ) {
		return false;
	}

	$url  = kocist_link( $url );
	$host = wp_parse_url( $url, PHP_URL_HOST );

	// Baska bir alan adina gidiyorsa aktif olamaz.
	if ( $host && $host !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
		return false;
	}

	$path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );

	return $path === kocist_current_path();
}

/**
 * Menu karsilastirmasi icin su anki yol.
 *
 * Blog listesi, arsivler ve tekil yazilar menude "Blog" ogesine baglidir;
 * bu sayfalarda yol, yazilar sayfasinin yolu sayilir.
 */
function kocist_current_path(): string {
	// Havuz urunu detayi (/urun/<slug>/) ve kategori sayfalari WordPress
	// sorgusunda yazi listesi gibi gorunur; blog sayilmasin.
	$is_blog = ( is_home() && ! is_front_page() ) || is_archive() || is_singular( 'post' );

	if ( $is_blog && ! get_query_var( 'nwcs_product' ) && ! kocist_is_catalog_request() ) {
		return trim( (string) wp_parse_url( kocist_blog_url(), PHP_URL_PATH ), '/' );
	}

	return trim( (string) wp_parse_url( add_query_arg( array() ), PHP_URL_PATH ), '/' );
}

/**
 * Ust menu ogesi aktif mi: kendi adresi ya da alt ogelerinden biri su anki
 * sayfa ise (Insan Kaynaklari acikken "Kurumsal" isaretli kalir).
 */
function kocist_is_current_menu_branch( array $item ): bool {
	if ( kocist_is_current_menu_item( (string) $item['url'] ) ) {
		return true;
	}

	foreach ( $item['children'] ?? array() as $child ) {
		if ( kocist_is_current_menu_item( kocist_link( $child['url'], (string) $item['url'] ) ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Sayfalarda gercekten var olan capalar ve bulunduklari yol.
 *
 * Panelde '#katalog' gibi yalin capa yazilabiliyor; bu capa yalnizca ana
 * sayfada karsiligi olan bir bolumu isaret eder. Baska sayfadan tiklandiginda
 * da dogru yere gitsin diye yalin capa buradaki yola baglanir.
 */
function kocist_anchor_targets(): array {
	return array(
		'katalog'          => '/#katalog',
		'urunler'          => '/#urunler',
		'teklif'           => '/iletisim/',
		'harita'           => '/iletisim/#harita',

		// Panelde eski demo menusunden kalan capalar; artik gercek sayfalari var.
		'insan-kaynaklari' => '/insan-kaynaklari/',
		'blog'             => '/blog/',
		'referanslar'      => '/kurumsal/',
		'belgeler'         => '/katalog/?tab=belgeler',
		'banka-bilgileri'  => '/banka-bilgilerimiz/',
	);
}

/**
 * Hedefi olmayan yalin capa mi (#instagram, #kereste)?
 */
function kocist_is_dead_anchor( $url ): bool {
	$url = trim( (string) $url );

	if ( ! str_starts_with( $url, '#' ) ) {
		return false;
	}

	return ! isset( kocist_anchor_targets()[ substr( $url, 1 ) ] );
}

/**
 * Panelden gelen baglantiyi siteye gore cozer; cikti her zaman esc_url ile basilir.
 *
 * - Bos deger '#' olur.
 * - '/' ile baslayan yol alt sitenin adresine baglanir: ag alt dizinle
 *   kuruldugunda '/kurumsal/' ana siteye degil '/kocist/kurumsal/'a gider.
 * - Bilinen yalin capa (#katalog) bulundugu sayfanin tam adresine baglanir.
 * - Hedefi olmayan yalin capa $fallback verilmisse ona duser (menude ust
 *   ogenin adresi, slaytta katalog); verilmemisse oldugu gibi kalir.
 * - Tam adres, tel:, mailto: dokunulmadan doner.
 */
function kocist_link( $url, string $fallback = '' ): string {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '#';
	}

	if ( str_starts_with( $url, '#' ) && '#' !== $url ) {
		$targets = kocist_anchor_targets();
		$anchor  = substr( $url, 1 );

		if ( isset( $targets[ $anchor ] ) ) {
			return home_url( $targets[ $anchor ] );
		}

		return '' !== trim( $fallback ) ? kocist_link( $fallback ) : $url;
	}

	if ( str_starts_with( $url, '/' ) && ! str_starts_with( $url, '//' ) ) {
		return home_url( $url );
	}

	return $url;
}

/**
 * Footer sosyal ikonu basar.
 *
 * Once eklentinin ikon kutuphanesine sorulur. Eklentide olmayan sosyal ag
 * isaretleri (instagram, facebook, linkedin, youtube) icin temadaki sabit
 * listeye dusulur; eklentiye dokunmadan footer bos kalmaz. Eklenti ileride
 * bu anahtarlari eklerse onun cizimi kullanilir.
 *
 * Panel, kutuphanesinde olmayan ikon anahtarini kayitta bosaltir; bu yuzden
 * ikon bos gelirse satirin adindan (Instagram -> instagram) tahmin edilir.
 */
function kocist_social_icon( string $key, string $label = '', string $class = 'k-footer__social-icon', int $size = 18 ): void {
	if ( '' === trim( $key ) ) {
		$key = $label;
	}

	if ( function_exists( 'nwcs_icon_svg' ) ) {
		$svg = nwcs_icon_svg( $key, $class, $size );

		if ( '' !== $svg ) {
			echo $svg; // phpcs:ignore WordPress.Security.EscapingOutput -- eklentinin sabit ikon listesi.
			return;
		}
	}

	$paths = array(
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="16.8" cy="7.2" r="1"/>',
		'facebook'  => '<path d="M15 3h-2.2A3.8 3.8 0 0 0 9 6.8V10H6.5v3.5H9V21h3.5v-7.5H15l.6-3.5h-3.1V6.8a.4.4 0 0 1 .4-.4H15z"/>',
		'linkedin'  => '<rect x="3" y="3" width="18" height="18" rx="4"/><path d="M8 10.5V17"/><path d="M8 7.4v.2"/><path d="M12 17v-3.6a2.1 2.1 0 0 1 4.2 0V17"/><path d="M12 10.5V17"/>',
		'youtube'   => '<rect x="2.5" y="6" width="19" height="12" rx="3.6"/><path d="m10.4 9.6 5.1 2.4-5.1 2.4z"/>',
	);

	$key = sanitize_key( $key );

	if ( ! isset( $paths[ $key ] ) ) {
		return;
	}

	printf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $class ),
		$size,
		$paths[ $key ] // phpcs:ignore WordPress.Security.EscapingOutput -- sabit liste.
	);
}

/**
 * Panelde gorsel secilmemisse temanin kendi gorselini dondurur.
 *
 * Gercek marka gorselleri tema icinde (assets/img) duruyor; kurulum betigi
 * onlari medyaya yuklemese de site yer tutucuyla acilmasin. Panelden bir
 * gorsel secildiginde her zaman o kullanilir.
 *
 * Ortak seed betigi medyaya "demo yer tutucusu" alt metinli uretilmis
 * gorseller koyuyor; bunlar da bos sayilir ki gercek fotograf gorunsun.
 */
function kocist_image_or_default( array $image, string $file, string $alt = '', bool $theme_fallback = false ): array {
	if ( kocist_is_placeholder_image( $image ) ) {
		$image = array( 'id' => 0, 'url' => '', 'alt' => '' );
	}

	/*
	 * Tema fotograflari simdilik kullanilmiyor: gercek fotograflar gelene
	 * kadar her gorsel alaninda "gorsel yakinda" alani gorunur. Yalnizca
	 * logo ile acikca istenen yerler (ana sayfa hero'sunun sol karti)
	 * temadaki dosyaya duser.
	 */
	if ( ! $theme_fallback && 'logo.png' !== $file ) {
		$file = '';
	}

	if ( ! empty( $image['url'] ) || '' === $file || ! file_exists( get_theme_file_path( 'assets/img/' . $file ) ) ) {
		return $image;
	}

	return array(
		'id'  => 0,
		'url' => get_theme_file_uri( 'assets/img/' . $file ),
		'alt' => '' !== ( $image['alt'] ?? '' ) ? $image['alt'] : $alt,
	);
}

/**
 * Seed betiginin urettigi yer tutucu gorsel mi? Alt metninden anlasilir.
 */
function kocist_is_placeholder_image( array $image ): bool {
	$alt = (string) ( $image['alt'] ?? '' );

	if ( false !== mb_stripos( $alt, 'yer tutucu' ) || false !== mb_stripos( $alt, 'örnek' ) ) {
		return true;
	}

	/*
	 * Temadaki demo fotograflarin medyaya yuklenmis kopyalari da yer tutucu
	 * sayilir (ornek: uploads/.../playwood-300x200.webp). Ayni adli dosya
	 * assets/img icinde varsa gercek urun fotografi degildir.
	 */
	$path = (string) wp_parse_url( (string) ( $image['url'] ?? '' ), PHP_URL_PATH );

	if ( '' === $path || ! str_contains( $path, '/uploads/' ) ) {
		return false;
	}

	$name = preg_replace( '/-\d+x\d+(?=\.\w+$)/', '', basename( $path ) );

	return file_exists( get_theme_file_path( 'assets/img/' . $name ) );
}

/**
 * Havuz urunu icin gosterilecek gorsel.
 *
 * Havuzdaki gorsel gercekse o kullanilir. Bos ya da yer tutucuysa urunun
 * adina bakilip temadaki en yakin grup fotografina dusulur.
 */
function kocist_product_image( array $product ): array {
	$key = sanitize_title( ( $product['slug'] ?? '' ) . ' ' . ( $product['title'] ?? '' ) );
	$map = array(
		'kereste'    => 's2-kereste.jpg',
		'dekorasyon' => 's2-dekorasyon.jpg',
		'hirdavat'   => 's2-hirdavat.jpg',
		'palet'      => 's2-ambalaj.jpg',
		'kafes'      => 's2-ambalaj.jpg',
		'sandik'     => 's2-ambalaj.jpg',
	);
	// Adindan cikmiyorsa urunun grubunun fotografi (inc/catalog.php), en son atolye.
	$by_group = array(
		'kereste'    => 's2-kereste.jpg',
		'ambalaj'    => 's2-ambalaj.jpg',
		'dekorasyon' => 's2-dekorasyon.jpg',
		'hirdavat'   => 's2-hirdavat.jpg',
	);
	$file     = $by_group[ $product['group'] ?? '' ] ?? 'atolye.jpg';

	foreach ( $map as $needle => $candidate ) {
		if ( str_contains( $key, $needle ) ) {
			$file = $candidate;
			break;
		}
	}

	return kocist_image_or_default( (array) ( $product['image'] ?? array() ), $file, (string) ( $product['title'] ?? '' ) );
}

/**
 * Panelden gelen cok paragrafli metni basar: bos satirlar paragraf ayirir.
 */
function kocist_paragraphs( $text, string $class = '' ): string {
	$parts = preg_split( '/\R\s*\R/u', trim( (string) $text ) );
	$out   = '';

	foreach ( $parts as $part ) {
		$part = trim( $part );

		if ( '' !== $part ) {
			$out .= sprintf( '<p%s>%s</p>', $class ? ' class="' . esc_attr( $class ) . '"' : '', nl2br( esc_html( $part ) ) );
		}
	}

	return $out;
}

/**
 * Gorsel alani icin img etiketi; deger yoksa isaretli yer tutucu.
 */
function kocist_image_tag( array $image, string $class = '', string $placeholder = 'Örnek görsel' ): string {
	if ( ! empty( $image['url'] ) && ! kocist_is_placeholder_image( $image ) ) {
		return sprintf(
			'<img src="%1$s" alt="%2$s" class="%3$s" loading="lazy" decoding="async" />',
			esc_url( $image['url'] ),
			esc_attr( $image['alt'] ),
			esc_attr( $class )
		);
	}

	return kocist_placeholder( $class, $placeholder );
}

/**
 * Gorseli henuz gelmemis alan: ahsap kesitini andiran yillik halkalar,
 * ortada kamera rozeti ve "Gorsel yakinda" notu.
 *
 * Halkalarin merkezi her alanda biraz kayar; yan yana dizilen kartlar
 * birbirinin kopyasi gibi durmasin. Renkler bulundugu yere gore CSS'ten
 * gelir (acik kartlarda acik, koyu kartlarda koyu zemin).
 */
function kocist_placeholder( string $class = '', string $label = '' ): string {
	static $count = 0;

	$centers = array( array( 12, 108 ), array( 96, -14 ), array( 30, -22 ), array( 104, 92 ) );
	list( $cx, $cy ) = $centers[ $count++ % count( $centers ) ];

	$rings = '';

	// Duz daire yerine hafif dalgali halkalar: gercek kesitteki gibi.
	for ( $r = 9; $r <= 150; $r += 7 + ( $r % 5 ) ) {
		$points = array();

		for ( $a = 0; $a < 360; $a += 12 ) {
			$rad      = deg2rad( $a );
			$wobble   = 1 + 0.035 * sin( 3 * $rad + $r ) + 0.02 * sin( 7 * $rad );
			$points[] = round( $cx + cos( $rad ) * $r * 1.25 * $wobble, 1 ) . ',' . round( $cy + sin( $rad ) * $r * $wobble, 1 );
		}

		$rings .= '<polygon points="' . implode( ' ', $points ) . '" />';
	}

	$label = '' !== trim( $label ) && ! kocist_is_placeholder_image( array( 'alt' => $label ) ) ? 'Görsel yakında: ' . $label : 'Görsel yakında';

	return sprintf(
		'<div class="k-placeholder %1$s" role="img" aria-label="%2$s">'
			. '<svg class="k-placeholder__rings" viewBox="0 0 120 90" preserveAspectRatio="xMidYMid slice" fill="none" aria-hidden="true">%3$s</svg>'
			. '<span class="k-placeholder__badge" aria-hidden="true">'
				. '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">'
					. '<path d="M4 8.5A1.5 1.5 0 0 1 5.5 7h2.2l1.5-2.2h5.6L16.3 7h2.2A1.5 1.5 0 0 1 20 8.5v9A1.5 1.5 0 0 1 18.5 19h-13A1.5 1.5 0 0 1 4 17.5z" />'
					. '<circle cx="12" cy="12.8" r="3.3" />'
				. '</svg>'
			. '</span>'
			. '<span class="k-placeholder__text" aria-hidden="true">Görsel yakında</span>'
		. '</div>',
		esc_attr( $class ),
		esc_attr( $label ),
		$rings
	);
}

/**
 * SEO ve GEO: gorseller tema icinde oldugundan, gorseli olmayan sayfalar
 * paylasilinca ve arama sonucunda hero fotografi gorunur.
 */
add_filter(
	'nwcs_seo_default_image',
	static fn() => function_exists( 'nwcs_seo_theme_file_image' ) ? nwcs_seo_theme_file_image( 'assets/img/atolye.jpg', 'Ahşap atölyesinde el aletleri ve talaş' ) : 0
);
add_filter(
	'nwcs_seo_default_logo',
	static fn() => function_exists( 'nwcs_seo_theme_file_image' ) ? nwcs_seo_theme_file_image( 'assets/img/logo.png', 'Koçist Orman Ürünleri' ) : 0
);
