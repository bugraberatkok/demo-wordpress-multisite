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
		return array( 'catalog', 'products', 'capabilities', 'references', 'ctaband' );
	}
}

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
 */
const KOCIST_PAGES_VERSION = '1';

add_action( 'after_switch_theme', 'kocist_ensure_pages' );
add_action( 'init', 'kocist_maybe_ensure_pages' );

function kocist_maybe_ensure_pages(): void {
	if ( KOCIST_PAGES_VERSION !== get_option( 'kocist_pages_version' ) ) {
		kocist_ensure_pages();
	}
}

function kocist_ensure_pages(): void {
	$pages = array(
		'kurumsal' => 'Kurumsal',
		'urun'     => 'Ahşap Tavuk Kümesi',
		'iletisim' => 'İletişim',
	);

	foreach ( $pages as $slug => $title ) {
		if ( get_page_by_path( $slug, OBJECT, 'page' ) ) {
			continue;
		}

		wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_name'   => $slug,
				'post_title'  => $title,
			)
		);
	}

	update_option( 'kocist_pages_version', KOCIST_PAGES_VERSION );
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

	wp_enqueue_style( 'kocist-style', get_stylesheet_uri(), array( 'kocist-fonts' ), wp_get_theme()->get( 'Version' ) );

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
		wp_get_theme()->get( 'Version' ),
		true
	);

	// Hero ve urun gamlari yalnizca ana sayfada; varliklari da orada yuklensin.
	if ( is_front_page() ) {
		wp_enqueue_style(
			'kocist-hero',
			get_theme_file_uri( 'assets/css/hero.css' ),
			array( 'kocist-style' ),
			wp_get_theme()->get( 'Version' )
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

	// Urun sayfasi varliklari yalnizca o sayfada.
	if ( is_page( 'urun' ) ) {
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
	$menu = array();

	foreach ( nwcs_rows( 'global', 'header', 'menu' ) as $row ) {
		$label = trim( (string) ( $row['label'] ?? '' ) );

		if ( '' === $label ) {
			continue;
		}

		$children = array();
		$submenu  = trim( (string) ( $row['submenu'] ?? '' ) );

		if ( '' !== $submenu ) {
			foreach ( nwcs_rows( 'global', $submenu, 'items' ) as $child ) {
				$child_label = trim( (string) ( $child['label'] ?? '' ) );

				if ( '' === $child_label ) {
					continue;
				}

				$children[] = array(
					'label' => $child_label,
					'url'   => (string) ( $child['url'] ?? '' ),
				);
			}
		}

		$menu[] = array(
			'label'    => $label,
			'url'      => (string) ( $row['url'] ?? '' ),
			'children' => $children,
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
 * Ana sayfa bolumunu kayitli siraya gore basar.
 */
function kocist_section( string $key ): void {
	$file = get_theme_file_path( "template-parts/sections/{$key}.php" );

	if ( file_exists( $file ) ) {
		include $file;
	}
}

/**
 * Menu ogesi su an goruntulenen sayfayi mi isaret ediyor?
 *
 * Yalnizca sayfa yolu karsilastirilir. Capa baglantilari (#kereste) ve dis
 * adresler hicbir zaman aktif sayilmaz; kayan gosterge bunlara demirlenmez.
 */
function kocist_is_current_menu_item( string $url ): bool {
	$url = trim( $url );

	if ( '' === $url || str_starts_with( $url, '#' ) ) {
		return false;
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );

	// Baska bir alan adina gidiyorsa aktif olamaz.
	if ( $host && $host !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
		return false;
	}

	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	$path = trim( $path, '/' );

	if ( '' === $path ) {
		return is_front_page();
	}

	$current = trim( (string) wp_parse_url( add_query_arg( array() ), PHP_URL_PATH ), '/' );

	return $path === $current;
}

/**
 * Bos baglantilari '#' yapar; cikti her zaman esc_url ile basilir.
 */
function kocist_link( $url ): string {
	$url = trim( (string) $url );

	return '' === $url ? '#' : $url;
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
function kocist_image_or_default( array $image, string $file, string $alt = '' ): array {
	$is_placeholder = false !== mb_stripos( (string) ( $image['alt'] ?? '' ), 'yer tutucu' );

	if ( $is_placeholder ) {
		$image = array( 'id' => 0, 'url' => '', 'alt' => '' );
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
 * Gorsel alani icin img etiketi; deger yoksa isaretli yer tutucu.
 */
function kocist_image_tag( array $image, string $class = '', string $placeholder = 'Örnek görsel' ): string {
	if ( ! empty( $image['url'] ) ) {
		return sprintf(
			'<img src="%1$s" alt="%2$s" class="%3$s" loading="lazy" decoding="async" />',
			esc_url( $image['url'] ),
			esc_attr( $image['alt'] ),
			esc_attr( $class )
		);
	}

	return sprintf(
		'<div class="k-placeholder %1$s" role="img" aria-label="%2$s">%2$s</div>',
		esc_attr( $class ),
		esc_html( $placeholder )
	);
}
