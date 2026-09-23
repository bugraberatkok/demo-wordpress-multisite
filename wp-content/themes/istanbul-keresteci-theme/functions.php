<?php
/**
 * Istanbul Keresteci temasi.
 *
 * Gorunur metinler sabit yazilmaz; degerler Network Content Studio veri
 * katmanindan okunur. Eklenti yoksa yedek fonksiyonlar manifest
 * varsayilanlarini dondurur; site yine eksiksiz gorunur.
 *
 * Tema kendi basina calisir: etkinlestirilince sayfalari, ana sayfa
 * ayarini ve ornek blog yazilarini kurar (inc/setup.php); panelde gorsel
 * secilmemisse assets/img icindeki fotograflar kullanilir.
 */

defined( 'ABSPATH' ) || exit;

require_once get_theme_file_path( 'inc/setup.php' );

/**
 * Manifest dizisi (content-manifest.php), istek basina bir kez okunur.
 */
function ik_manifest(): array {
	static $manifest = null;

	if ( null === $manifest ) {
		$manifest = (array) include get_theme_file_path( 'content-manifest.php' );
	}

	return $manifest;
}

/**
 * Alanin manifestteki varsayilan degeri.
 */
function ik_manifest_default( string $page, string $component, string $field ) {
	return ik_manifest()['pages'][ $page ]['components'][ $component ]['fields'][ $field ]['default'] ?? '';
}

if ( ! function_exists( 'nwcs_field' ) ) {
	function nwcs_field( $page, $component, $field, $fallback = null ) {
		return null === $fallback ? ik_manifest_default( $page, $component, $field ) : $fallback;
	}
	function nwcs_rows( $page, $component, $field ) {
		$rows = ik_manifest_default( $page, $component, $field );

		return is_array( $rows ) ? array_values( $rows ) : array();
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
		return ik_manifest()['pages'][ $page ]['sortable_sections'] ?? array();
	}
}

add_action( 'after_setup_theme', 'ik_setup' );
function ik_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script' ) );
}

add_action( 'wp_enqueue_scripts', 'ik_assets' );
function ik_assets(): void {
	$version = wp_get_theme()->get( 'Version' );

	/*
	 * Archivo: tek aile. Basliklar dar genislikte (wdth 62-75) ve agir,
	 * govde normal genislikte. Surum null: Google Fonts onbellegi bozulmasin.
	 */
	wp_enqueue_style(
		'ik-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,400..900&display=swap',
		array(),
		null
	);

	// Renkler tek dosyada: tokens.css. Diger butun stiller ona baglidir.
	wp_enqueue_style( 'ik-tokens', get_theme_file_uri( 'assets/css/tokens.css' ), array( 'ik-fonts' ), $version );
	wp_enqueue_style( 'ik-style', get_stylesheet_uri(), array( 'ik-tokens' ), $version );
	wp_enqueue_style( 'ik-base', get_theme_file_uri( 'assets/css/base.css' ), array( 'ik-style' ), $version );
	wp_enqueue_style( 'ik-header', get_theme_file_uri( 'assets/css/header.css' ), array( 'ik-base' ), $version );
	wp_enqueue_style( 'ik-footer', get_theme_file_uri( 'assets/css/footer.css' ), array( 'ik-base' ), $version );

	// Urun istifi, blog kartlari ve fiyat seridi ic sayfalarda da kullanilir.
	wp_enqueue_style( 'ik-home', get_theme_file_uri( 'assets/css/home.css' ), array( 'ik-base' ), $version );

	if ( ! is_front_page() ) {
		wp_enqueue_style( 'ik-pages', get_theme_file_uri( 'assets/css/pages.css' ), array( 'ik-home' ), $version );
	}

	wp_enqueue_script( 'ik-nav', get_theme_file_uri( 'assets/js/nav.js' ), array(), $version, true );

	if ( is_front_page() || is_page( 'hakkimizda' ) ) {
		wp_enqueue_script( 'ik-process', get_theme_file_uri( 'assets/js/process.js' ), array(), $version, true );
	}
}

/* ======================================================================
 * Urunler
 *
 * Tek kaynak: products.catalog.items. Her satirin sayfasi
 * /urunlerimiz/<slug>/ adresinde, tema kurulumunun (inc/setup.php) olusturdugu
 * alt sayfadir.
 * ====================================================================== */

/**
 * Urun satirlari; adres adi bos olanlar atlanir.
 */
function ik_products(): array {
	static $products = null;

	if ( null !== $products ) {
		return $products;
	}

	$products = array();

	foreach ( nwcs_rows( 'products', 'catalog', 'items' ) as $index => $row ) {
		$slug = sanitize_title( (string) ( $row['slug'] ?? '' ) );

		if ( '' === $slug ) {
			continue;
		}

		$products[] = array(
			'index' => $index,
			'slug'  => $slug,
			'title' => (string) ( $row['title'] ?? '' ),
			'short' => (string) ( $row['short'] ?? '' ),
			'body'  => (string) ( $row['body'] ?? '' ),
			'specs' => (string) ( $row['specs'] ?? '' ),
			'image' => (int) ( $row['image'] ?? 0 ),
			'file'  => ik_product_file( $slug ),
			'url'   => ik_link( '/urunlerimiz/' . $slug . '/' ),
		);
	}

	return $products;
}

/**
 * Urunun tema icindeki yedek fotografi (assets/img/urun-<slug>.jpg).
 * Citanin kendi fotografi yok; kalas fotografi kullanilir.
 */
function ik_product_file( string $slug ): string {
	$slug = 'cita' === $slug ? 'kalas' : $slug;

	return file_exists( get_theme_file_path( 'assets/img/urun-' . $slug . '.jpg' ) ) ? 'urun-' . $slug . '.jpg' : 'urun-kereste.jpg';
}

/**
 * Urun gorseli: panelde secilen, yoksa temanin fotografi.
 */
function ik_product_image( array $product, string $size = 'medium_large' ): array {
	return ik_image_or_default( nwcs_image_by_id( (int) $product['image'], $size ), $product['file'], $product['title'] );
}

/**
 * Adres adina gore urun; yoksa bos dizi.
 */
function ik_product( string $slug ): array {
	foreach ( ik_products() as $product ) {
		if ( $product['slug'] === $slug ) {
			return $product;
		}
	}

	return array();
}

/**
 * Su anki sayfa bir urun detayi mi? /urunlerimiz/ altindaki alt sayfalar.
 */
function ik_current_product(): array {
	if ( ! is_page() ) {
		return array();
	}

	$page   = get_queried_object();
	$parent = $page instanceof WP_Post && $page->post_parent ? get_post( $page->post_parent ) : null;

	if ( ! $parent || 'urunlerimiz' !== $parent->post_name ) {
		return array();
	}

	return ik_product( $page->post_name );
}

/**
 * "Ad: deger" satirlarini ciftlere boler.
 */
function ik_specs( string $text ): array {
	$specs = array();

	foreach ( preg_split( '/\R/u', trim( $text ) ) as $line ) {
		$parts = explode( ':', $line, 2 );

		if ( 2 === count( $parts ) && '' !== trim( $parts[0] ) ) {
			$specs[] = array( trim( $parts[0] ), trim( $parts[1] ) );
		}
	}

	return $specs;
}

/* ======================================================================
 * Menu
 * ====================================================================== */

/**
 * Menu satirlari ve acilir listeleri.
 *
 * Paneldeki "submenu" alani "urunler" ise alt ogeler urun listesinden
 * uretilir; menu ayrica bakim istemez.
 */
function ik_menu(): array {
	$items = array();

	foreach ( nwcs_rows( 'global', 'header', 'menu' ) as $index => $row ) {
		$children = array();

		if ( 'urunler' === sanitize_key( (string) ( $row['submenu'] ?? '' ) ) ) {
			foreach ( ik_products() as $product ) {
				$children[] = array(
					'label'   => $product['title'],
					'url'     => $product['url'],
					'product' => $product,
				);
			}
		}

		$items[] = array(
			'index'    => $index,
			'label'    => (string) ( $row['label'] ?? '' ),
			'url'      => (string) ( $row['url'] ?? '' ),
			'children' => $children,
		);
	}

	return $items;
}

/**
 * Menu ogesi su an goruntulenen sayfayi mi isaret ediyor?
 *
 * Yalnizca sayfa yolu karsilastirilir. Blog yazisi acikken Blog, urun
 * detayi acikken Urunlerimiz aktif sayilir.
 */
function ik_is_current( string $url ): bool {
	$url = trim( $url );

	if ( '' === $url || str_starts_with( $url, '#' ) ) {
		return false;
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );

	if ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) {
		return false;
	}

	// Kalici baglantilar kapaliysa yol yerine sayfa kimligi karsilastirilir.
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		$link = ik_link( $url );

		if ( untrailingslashit( $link ) === untrailingslashit( home_url( '/' ) ) ) {
			return is_front_page();
		}

		$id = url_to_postid( $link );

		if ( ! $id ) {
			return false;
		}

		if ( (int) get_option( 'page_for_posts' ) === $id && ( is_home() || is_singular( 'post' ) ) ) {
			return true;
		}

		$queried = (int) get_queried_object_id();

		return $queried && ( $id === $queried || in_array( $id, get_post_ancestors( $queried ), true ) );
	}

	$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );

	// Alt dizin kurulumunda /istanbul-keresteci/ onekini at.
	$relative = static function ( string $path ) use ( $home ): string {
		$path = trim( $path, '/' );

		if ( '' !== $home && ( $path === $home || str_starts_with( $path, $home . '/' ) ) ) {
			$path = trim( substr( $path, strlen( $home ) ), '/' );
		}

		return $path;
	};

	$path    = $relative( (string) wp_parse_url( ik_link( $url ), PHP_URL_PATH ) );
	$current = $relative( (string) wp_parse_url( add_query_arg( array() ), PHP_URL_PATH ) );

	if ( '' === $path ) {
		return is_front_page();
	}

	if ( 'blog' === $path && is_singular( 'post' ) ) {
		return true;
	}

	return $path === $current || str_starts_with( $current, $path . '/' );
}

/* ======================================================================
 * Yardimcilar
 * ====================================================================== */

/**
 * Ana sayfa bolumunu basar.
 */
function ik_section( string $key, array $args = array() ): void {
	$file = get_theme_file_path( "template-parts/sections/{$key}.php" );

	if ( file_exists( $file ) ) {
		include $file;
	}
}

/**
 * Panelden gelen baglantiyi adrese cevirir; cikti her zaman esc_url ile basilir.
 *
 * Kok-goreli yollar sitenin kendi adresine baglanir: site alt dizinde
 * (/istanbul-keresteci/) calistigi icin dogrudan basilsa ag kokune giderdi.
 * Kalici baglantilar kapaliysa (?page_id=) yol, o yoldaki sayfanin
 * adresine cevrilir; /urunlerimiz/kereste/ gibi alt sayfalar da calisir.
 */
function ik_link( $url ): string {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '#';
	}

	if ( ! str_starts_with( $url, '/' ) || str_starts_with( $url, '//' ) ) {
		return $url;
	}

	if ( '' !== (string) get_option( 'permalink_structure' ) ) {
		return home_url( $url );
	}

	$parts = wp_parse_url( $url );
	$path  = trim( (string) ( $parts['path'] ?? '' ), '/' );

	if ( '' === $path ) {
		$link = home_url( '/' );
	} elseif ( 'blog' === $path && get_option( 'page_for_posts' ) ) {
		$link = get_permalink( (int) get_option( 'page_for_posts' ) );
	} else {
		$page = get_page_by_path( $path );
		$link = $page ? get_permalink( $page ) : home_url( $url );
	}

	if ( ! empty( $parts['query'] ) ) {
		parse_str( $parts['query'], $query );
		$link = add_query_arg( $query, $link );
	}

	return $link . ( isset( $parts['fragment'] ) ? '#' . $parts['fragment'] : '' );
}

/**
 * Panelde gorsel secilmemisse temanin kendi fotografini dondurur.
 *
 * Gercek fotograflar tema icinde (assets/img) duruyor; site medya
 * kutuphanesi bos da olsa yer tutucuyla acilmaz. Panelden bir gorsel
 * secildiginde her zaman o kullanilir. (kocist_image_or_default ile ayni mantik.)
 */
function ik_image_or_default( array $image, string $file, string $alt = '' ): array {
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
 * Bos satirla ayrilmis metni paragraflara boler.
 */
function ik_paragraphs( string $text, string $class = '' ): void {
	foreach ( preg_split( '/\R\s*\R/u', trim( $text ) ) as $block ) {
		$block = trim( $block );

		if ( '' !== $block ) {
			printf( '<p%s>%s</p>', $class ? ' class="' . esc_attr( $class ) . '"' : '', nl2br( esc_html( $block ) ) );
		}
	}
}

/**
 * Gorsel icin img etiketi; deger yoksa isaretli yer tutucu.
 */
function ik_image_tag( array $image, string $class = '', ?string $alt = null, string $loading = 'lazy' ): string {
	if ( ! empty( $image['url'] ) ) {
		return sprintf(
			'<img src="%1$s" alt="%2$s" class="%3$s" loading="%4$s" decoding="async" />',
			esc_url( $image['url'] ),
			esc_attr( null === $alt ? $image['alt'] : $alt ),
			esc_attr( $class ),
			esc_attr( $loading )
		);
	}

	return sprintf( '<div class="ik-placeholder %s" aria-hidden="true"></div>', esc_attr( $class ) );
}

/**
 * Blog yazisinin kapak gorseli; yoksa bos dizi.
 */
function ik_post_image( WP_Post $post, string $size = 'medium_large' ): array {
	$id = (int) get_post_thumbnail_id( $post );

	$image = $id ? nwcs_image_by_id( $id, $size ) : array();

	// Kapak secilmemis yazilarda da gercek fotograf gorunsun.
	return ik_image_or_default( $image ?: array( 'id' => 0, 'url' => '', 'alt' => '' ), 'blog-kereste.jpg' );
}

/**
 * Yazi tarihini Turkce basar ("19 Nisan 2022").
 *
 * Demo sitelerinin dili Ingilizce kurulu; Turkce dil paketi olmadan ay
 * adlari dogru ciksin diye elle cevrilir.
 */
function ik_date( WP_Post $post ): string {
	$months = array( 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık' );
	$time   = get_post_timestamp( $post );

	if ( ! $time ) {
		return '';
	}

	return wp_date( 'j', $time ) . ' ' . $months[ (int) wp_date( 'n', $time ) - 1 ] . ' ' . wp_date( 'Y', $time );
}

/**
 * Ikon basar: Phosphor (MIT, assets/icons/LICENSE), kalin agirlik.
 *
 * Paneldeki ikon alanlari eklentinin sabit 16 anahtarini kullanir; tema bu
 * anahtarlari ayni anlamdaki Phosphor cizimine esler. Ek olarak sosyal ag
 * ikonlari (instagram, facebook) temanin kendi cizimleridir.
 */
function ik_icon( string $key, int $size = 24, string $class = 'ik-icon' ): void {
	static $cache = array();

	$map = array(
		'truck'     => 'truck',
		'box'       => 'package',
		'factory'   => 'factory',
		'tools'     => 'wrench',
		'tree'      => 'tree',
		'recycle'   => 'recycle',
		'shield'    => 'shield-check',
		'ruler'     => 'ruler',
		'clock'     => 'clock',
		'phone'     => 'phone',
		'whatsapp'  => 'whatsapp-logo',
		'mail'      => 'envelope-simple',
		'pin'       => 'map-pin',
		'check'     => 'check-circle',
		'star'      => 'star',
		'arrow'     => 'arrow-right',
		'instagram' => 'instagram-logo',
		'facebook'  => 'facebook-logo',
		'caret'     => 'caret-down',
	);

	$key = sanitize_key( $key );

	if ( ! isset( $map[ $key ] ) ) {
		return;
	}

	if ( ! isset( $cache[ $key ] ) ) {
		$file          = get_theme_file_path( 'assets/icons/' . $map[ $key ] . '.svg' );
		$cache[ $key ] = file_exists( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions
	}

	if ( '' === $cache[ $key ] ) {
		return;
	}

	echo str_replace( // phpcs:ignore WordPress.Security.EscapingOutput -- temanin kendi sabit SVG dosyalari.
		'<svg ',
		sprintf( '<svg class="%s" width="%d" height="%d" aria-hidden="true" focusable="false" ', esc_attr( $class ), $size, $size ),
		$cache[ $key ]
	);
}

/**
 * Logo: panelde gorsel varsa o, yoksa agac isareti ve iki satir yazi.
 *
 * Musterinin elindeki PNG logo 146 piksel genisliginde; retina ekranda
 * bulaniklasmasin diye isaret SVG olarak cizildi, yazi Archivo ile basilir.
 */
function ik_logo( string $modifier = '' ): void {
	$logo   = nwcs_image( 'global', 'header', 'logo_image', 'medium' );
	$top    = nwcs_field( 'global', 'header', 'logo_top' );
	$bottom = nwcs_field( 'global', 'header', 'logo_bottom' );
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ik-logo <?php echo esc_attr( $modifier ); ?>">
		<?php if ( ! empty( $logo['url'] ) ) : ?>
			<img class="ik-logo__image" src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( trim( $top . ' ' . $bottom ) ); ?>" />
		<?php else : ?>
			<svg class="ik-logo__mark" viewBox="0 0 32 40" width="32" height="40" aria-hidden="true" focusable="false">
				<path d="M16 2 7 13h5l-8 10h6L3 33h26l-7-10h6l-8-10h5z" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round" />
				<path d="M16 33v5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" />
			</svg>
			<span class="ik-logo__text">
				<span class="ik-logo__top" <?php nwcs_edit_attr( 'global', 'header', 'logo_top' ); ?>><?php echo esc_html( $top ); ?></span>
				<span class="ik-logo__bottom" <?php nwcs_edit_attr( 'global', 'header', 'logo_bottom' ); ?>><?php echo esc_html( $bottom ); ?></span>
			</span>
		<?php endif; ?>
	</a>
	<?php
}

/* ======================================================================
 * Iletisim formu
 *
 * Sahte basari ekrani yok: gonderilen mesaj gercekten kaydedilir, yonetimde
 * "İletişim Mesajları" altinda gorulur. Demoda e-posta gonderilmez.
 * ====================================================================== */

add_action( 'init', 'ik_register_message_cpt' );
function ik_register_message_cpt(): void {
	register_post_type(
		'ik_message',
		array(
			'labels'          => array(
				'name'          => 'İletişim Mesajları',
				'singular_name' => 'İletişim Mesajı',
				'menu_name'     => 'İletişim Mesajları',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 26,
			'supports'        => array( 'title', 'editor', 'custom-fields' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'has_archive'     => false,
			'rewrite'         => false,
			'query_var'       => false,
		)
	);
}

add_action( 'admin_post_ik_message', 'ik_handle_message' );
add_action( 'admin_post_nopriv_ik_message', 'ik_handle_message' );
function ik_handle_message(): void {
	$redirect = home_url( '/iletisim/' );

	// Bot tuzagi: gorunmeyen alan dolmussa sessizce geri don.
	if ( ! empty( $_POST['ik_website'] ) ) {
		wp_safe_redirect( $redirect );
		exit;
	}

	$values = array(
		'name'    => sanitize_text_field( wp_unslash( $_POST['ik_name'] ?? '' ) ),
		'phone'   => sanitize_text_field( wp_unslash( $_POST['ik_phone'] ?? '' ) ),
		'email'   => sanitize_email( wp_unslash( $_POST['ik_email'] ?? '' ) ),
		'product' => sanitize_text_field( wp_unslash( $_POST['ik_product'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $_POST['ik_message'] ?? '' ) ),
	);

	$errors = array();

	if ( ! isset( $_POST['ik_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ik_nonce'] ), 'ik_message' ) ) {
		$errors['form'] = 'Formun süresi dolmuş. Sayfayı yenileyip tekrar gönderin.';
	}

	if ( '' === $values['name'] ) {
		$errors['name'] = 'Adınızı ve soyadınızı yazın.';
	}

	// Kerestede musteri cogunlukla telefonla doner; ikisinden biri yeterli.
	if ( '' === $values['phone'] && '' === $values['email'] ) {
		$errors['phone'] = 'Size dönebilmemiz için telefon ya da e-posta yazın.';
	}

	if ( '' !== $values['email'] && ! is_email( $values['email'] ) ) {
		$errors['email'] = 'E-posta adresini kontrol edin; örnek: ad@firma.com';
	}

	if ( '' === $values['message'] ) {
		$errors['message'] = 'Ölçüyü ve adedi yazın.';
	}

	// Kucuk harf onaltilik: sanitize_key() ile okununca degismesin.
	$token = bin2hex( random_bytes( 8 ) );
	$back  = add_query_arg( 'mesaj', $token, $redirect ) . '#form';

	if ( ! $errors ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'ik_message',
				'post_status'  => 'private',
				'post_title'   => sprintf( '%s — %s', $values['name'], $values['product'] ?: 'Ürün belirtilmedi' ),
				'post_content' => sprintf(
					"Telefon: %s\nE-posta: %s\nÜrün: %s\n\n%s",
					$values['phone'] ?: '—',
					$values['email'] ?: '—',
					$values['product'] ?: '—',
					$values['message']
				),
				'meta_input'   => array(
					'_ik_name'    => $values['name'],
					'_ik_phone'   => $values['phone'],
					'_ik_email'   => $values['email'],
					'_ik_product' => $values['product'],
				),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			$errors['form'] = 'Mesaj kaydedilemedi. Lütfen telefonla ya da WhatsApp’tan ulaşın.';
		}
	}

	$state = $errors ? array( 'errors' => $errors, 'values' => $values ) : array( 'success' => true );

	set_transient( 'ik_msg_' . $token, $state, 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( $back );
	exit;
}

/**
 * Yonlendirmeden sonra formun durumu (hatalar, girilen degerler, basari).
 */
function ik_message_state(): array {
	$empty = array( 'errors' => array(), 'values' => array(), 'success' => false );
	$token = isset( $_GET['mesaj'] ) ? sanitize_key( wp_unslash( $_GET['mesaj'] ) ) : '';

	if ( '' === $token ) {
		return $empty;
	}

	$state = get_transient( 'ik_msg_' . $token );

	if ( ! is_array( $state ) ) {
		return $empty;
	}

	return array(
		'errors'  => $state['errors'] ?? array(),
		'values'  => $state['values'] ?? array(),
		'success' => ! empty( $state['success'] ),
	);
}
