<?php
/**
 * istanbulpaletci temasi (Istanbul Paletci yeniden tasarimi).
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
		return array( 'products', 'process', 'export', 'blog', 'quote' );
	}
}

add_action( 'after_setup_theme', 'ip_setup' );
function ip_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script', 'search-form', 'gallery', 'caption' ) );
}

add_action( 'wp_enqueue_scripts', 'ip_assets' );
function ip_assets(): void {
	// Surum = tema surumu + dosya degisim zamani: dosya her derlendiginde adres
	// degisir, tarayici ve onbellek eski CSS/JS'i gostermez.
	$ver = static fn( string $file ): string => wp_get_theme()->get( 'Version' ) . '.' . (int) @filemtime( get_theme_file_path( $file ) );

	wp_enqueue_style(
		'ip-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,300..900&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'ip-tailwind', get_theme_file_uri( 'assets/tailwind.css' ), array( 'ip-fonts' ), $ver( 'assets/tailwind.css' ) );

	// style.css yalnizca tema basligini tasir; kuyruga alinmasi WordPress adeti.
	wp_enqueue_style( 'ip-style', get_stylesheet_uri(), array( 'ip-tailwind' ), $ver( 'style.css' ) );

	wp_enqueue_script( 'ip-nav', get_theme_file_uri( 'assets/nav.js' ), array(), $ver( 'assets/nav.js' ), true );
	wp_enqueue_script( 'ip-media', get_theme_file_uri( 'assets/media.js' ), array(), $ver( 'assets/media.js' ), true );
}

add_action( 'wp_head', 'ip_preconnect', 1 );
function ip_preconnect(): void {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}

/* ====================================================================== *
 * Genel yardimcilar
 * ====================================================================== */

/**
 * Bos baglantilari '#' yapar, koke gore yazilmis yollari site adresine baglar.
 * Alt dizin multisite'ta '/iletisim/' ag kokune gider; home_url ile duzeltiyoruz.
 */
function ip_link( $url ): string {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '#';
	}

	if ( str_starts_with( $url, '/' ) && ! str_starts_with( $url, '//' ) ) {
		return home_url( $url );
	}

	return $url;
}

/**
 * Istek adresinin yolu (sorgu ve capa olmadan), sonda egik cizgi yok.
 */
function ip_request_path(): string {
	$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

	return untrailingslashit( (string) wp_parse_url( $request, PHP_URL_PATH ) );
}

/**
 * Baglantinin yolu; ip_request_path() ile ayni bicimde.
 */
function ip_link_path( string $url ): string {
	return untrailingslashit( (string) wp_parse_url( ip_link( $url ), PHP_URL_PATH ) );
}

/**
 * Menudeki baglanti bulunulan sayfa mi?
 */
function ip_is_current( string $url ): bool {
	return ip_link_path( $url ) === ip_request_path();
}

/**
 * Bulunulan sayfa bu baglantinin altinda mi? (Urun sayfasindayken
 * "Urunlerimiz" de isaretli kalsin diye.) Ana sayfa her seyin ustu
 * oldugu icin haric tutulur.
 */
function ip_is_within( string $url ): bool {
	$target = ip_link_path( $url );

	if ( $target === untrailingslashit( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ) ) ) {
		return false;
	}

	return str_starts_with( ip_request_path() . '/', $target . '/' );
}

/**
 * Gorsel alani icin img etiketi; deger yoksa sessiz bir yer tutucu.
 */
function ip_image_tag( array $image, string $class = '', string $alt_fallback = '', bool $eager = false ): string {
	if ( ! empty( $image['url'] ) ) {
		return sprintf(
			'<img src="%1$s" alt="%2$s" class="%3$s" %4$s decoding="async" />',
			esc_url( $image['url'] ),
			esc_attr( $image['alt'] ?: $alt_fallback ),
			esc_attr( $class ),
			$eager ? 'fetchpriority="high"' : 'loading="lazy"'
		);
	}

	return sprintf(
		'<span class="%1$s flex items-center justify-center bg-mist text-steel text-sm" role="img" aria-label="%2$s">%2$s</span>',
		esc_attr( $class ),
		esc_html( $alt_fallback ?: 'Görsel eklenmedi' )
	);
}

/**
 * Satir sonlari korunarak basilan metin.
 */
function ip_multiline( string $value ): string {
	return nl2br( esc_html( $value ) );
}

/**
 * Bos satirla ayrilmis metni paragraflara boler.
 */
function ip_paragraphs( string $value, string $class = '' ): string {
	$blocks = preg_split( '/\R\s*\R/u', trim( $value ) ) ?: array();
	$html   = '';

	foreach ( $blocks as $block ) {
		$block = trim( $block );

		if ( '' !== $block ) {
			$html .= sprintf( '<p%s>%s</p>', $class ? ' class="' . esc_attr( $class ) . '"' : '', ip_multiline( $block ) );
		}
	}

	return $html;
}

/**
 * Ana sayfa bolum dosyasini basar.
 */
function ip_section( string $key, array $args = array() ): void {
	$file = get_theme_file_path( "template-parts/sections/{$key}.php" );

	if ( file_exists( $file ) ) {
		include $file;
	}
}

/**
 * Temanin manifesti. Eklenti kapaliyken de urun listesi kurulabilsin diye
 * dosyadan dogrudan okunabilir.
 */
function ip_manifest(): array {
	if ( function_exists( 'nwcs_manifest' ) ) {
		return nwcs_manifest();
	}

	static $manifest = null;

	if ( null === $manifest ) {
		$loaded   = include get_theme_file_path( 'content-manifest.php' );
		$manifest = is_array( $loaded ) ? $loaded : array();
	}

	return $manifest;
}

/* ====================================================================== *
 * Urunler
 *
 * Urun listesinin tek kaynagi manifesttir: 'template' => 'product' isaretli
 * sayfalar. Ust menunun acilir paneli, ana sayfa izgarasi, Urunlerimiz
 * listesi, alt bilgi, form secenekleri ve "diger urunler" hep buradan beslenir.
 * ====================================================================== */

/**
 * @return array<string, array{key:string, path:string, url:string, name:string, short:string, size:string, image:array}>
 */
function ip_products(): array {
	static $products = null;

	if ( null !== $products ) {
		return $products;
	}

	$products = array();

	foreach ( ip_manifest()['pages'] ?? array() as $key => $page ) {
		if ( 'product' !== ( $page['template'] ?? '' ) ) {
			continue;
		}

		$products[ $key ] = array(
			'key'   => $key,
			'path'  => (string) ( $page['path'] ?? '/' ),
			'url'   => ip_link( $page['path'] ?? '/' ),
			'name'  => (string) nwcs_field( $key, 'card', 'name' ),
			'short' => (string) nwcs_field( $key, 'card', 'short' ),
			'size'  => (string) nwcs_field( $key, 'card', 'size' ),
			'image' => nwcs_image( $key, 'card', 'image', 'large' ),
		);
	}

	return $products;
}

/**
 * Bulunulan sayfa bir urun sayfasiysa onun manifest anahtari.
 */
function ip_current_product_key(): string {
	$current = ip_request_path();

	foreach ( ip_products() as $key => $product ) {
		if ( ip_link_path( $product['path'] ) === $current ) {
			return $key;
		}
	}

	return '';
}

/**
 * Urunun buyutulebilir galerisi: kart gorseli en basta, ardindan galeri
 * satirlari. Ayni gorsel iki kez eklenmez.
 *
 * @return array<int, array{url:string, alt:string, thumb:string}>
 */
function ip_product_gallery( string $key ): array {
	$ids = array( (int) nwcs_field( $key, 'card', 'image', 0 ) );

	foreach ( nwcs_rows( $key, 'detail', 'gallery' ) as $row ) {
		$ids[] = (int) ( $row['image'] ?? 0 );
	}

	$gallery = array();
	$name    = (string) nwcs_field( $key, 'card', 'name' );

	foreach ( array_unique( array_filter( $ids ) ) as $id ) {
		$large = nwcs_image_by_id( $id, 'large' );
		$thumb = nwcs_image_by_id( $id, 'medium' );

		if ( '' === $large['url'] ) {
			continue;
		}

		$gallery[] = array(
			'url'   => $large['url'],
			'alt'   => $large['alt'] ?: $name,
			'thumb' => $thumb['url'] ?: $large['url'],
		);
	}

	return $gallery;
}

/**
 * Urun icin teklif formuna giden adres: form urunu secili getirir.
 */
function ip_quote_url( string $product_name ): string {
	$base = ip_link( nwcs_field( 'products', 'shared', 'quote_url' ) );

	return add_query_arg( 'urun', rawurlencode( $product_name ), $base ) . '#teklif';
}

/* ====================================================================== *
 * Teklif formu
 *
 * Sahte basari ekrani yok: gonderilen form gercekten kaydedilir, yonetimde
 * "Teklif Istekleri" altinda gorulur. Ana sayfa ve Iletisim ayni formu
 * (template-parts/quote-form.php) ve ayni isleyiciyi kullanir.
 * ====================================================================== */

add_action( 'init', 'ip_register_quote_cpt' );
function ip_register_quote_cpt(): void {
	register_post_type(
		'ip_quote',
		array(
			'labels'          => array(
				'name'          => 'Teklif İstekleri',
				'singular_name' => 'Teklif İsteği',
				'menu_name'     => 'Teklif İstekleri',
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

/**
 * Yonlendirmeden sonra formun durumunu tasiyan gecici kaydin anahtari.
 * Kucuk harfli onaltilik: sanitize_key() buyuk harfi kucultur; karisik
 * harfli anahtar, harf duyarli nesne onbelleginde (LiteSpeed, Redis)
 * bulunamaz ve form hatasi/basari mesaji kaybolur.
 */
function ip_quote_token(): string {
	return bin2hex( random_bytes( 10 ) );
}

function ip_quote_redirect( string $redirect, string $token, array $state ): void {
	set_transient( 'ip_quote_' . $token, $state, 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( add_query_arg( 'ip', $token, $redirect ) . '#teklif' );
	exit;
}

add_action( 'admin_post_ip_quote', 'ip_handle_quote' );
add_action( 'admin_post_nopriv_ip_quote', 'ip_handle_quote' );
function ip_handle_quote(): void {
	// Referer bos ya da yabanciysa iletisim sayfasina doneriz. Sorgudaki eski
	// durum anahtari tasinmasin diye temizlenir.
	$fallback = home_url( '/iletisim/' );
	$referer  = wp_get_referer();
	$redirect = $referer ? wp_validate_redirect( $referer, $fallback ) : $fallback;
	$redirect = remove_query_arg( 'ip', '' !== $redirect ? $redirect : $fallback );
	$redirect = strtok( $redirect, '#' );

	// Bot tuzagi: gorunmeyen alan dolmussa sessizce geri don.
	if ( ! empty( $_POST['ip_website'] ) ) {
		wp_safe_redirect( $redirect );
		exit;
	}

	$values = array(
		'name'    => sanitize_text_field( wp_unslash( $_POST['ip_name'] ?? '' ) ),
		'company' => sanitize_text_field( wp_unslash( $_POST['ip_company'] ?? '' ) ),
		'email'   => sanitize_email( wp_unslash( $_POST['ip_email'] ?? '' ) ),
		'phone'   => sanitize_text_field( wp_unslash( $_POST['ip_phone'] ?? '' ) ),
		'product' => sanitize_text_field( wp_unslash( $_POST['ip_product'] ?? '' ) ),
		'size'    => sanitize_textarea_field( wp_unslash( $_POST['ip_size'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $_POST['ip_message'] ?? '' ) ),
	);

	$errors = array();
	$token  = ip_quote_token();

	if ( ! isset( $_POST['ip_quote_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ip_quote_nonce'] ), 'ip_quote' ) ) {
		$errors['form'] = 'Form oturumu zaman aşımına uğradı. Lütfen tekrar gönderin.';
	}

	if ( '' === $values['name'] ) {
		$errors['name'] = 'Adınızı yazın.';
	}

	// E-posta ya da telefon: ikisinden biri yeterli, donus yapabilelim.
	if ( '' !== $values['email'] && ! is_email( $values['email'] ) ) {
		$errors['email'] = 'E-posta adresi geçerli görünmüyor.';
	} elseif ( '' === $values['email'] && '' === $values['phone'] ) {
		$errors['email'] = 'Size dönebilmemiz için e-posta ya da telefon yazın.';
	}

	if ( '' === $values['size'] ) {
		$errors['size'] = 'Ölçü ve adet bilgisini yazın.';
	}

	if ( $errors ) {
		ip_quote_redirect( $redirect, $token, array( 'errors' => $errors, 'values' => $values ) );
	}

	// Urun secenekleri manifestten gelir; baska bir deger kabul edilmez.
	$allowed = wp_list_pluck( ip_products(), 'name' );

	if ( ! in_array( $values['product'], $allowed, true ) ) {
		$values['product'] = 'Belirtilmedi';
	}

	$body = sprintf(
		"Firma: %s\nE-posta: %s\nTelefon: %s\nÜrün: %s\n\nÖlçüler ve adet:\n%s\n\nMesaj:\n%s",
		$values['company'] ?: '—',
		$values['email'] ?: '—',
		$values['phone'] ?: '—',
		$values['product'],
		$values['size'],
		$values['message'] ?: '—'
	);

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'ip_quote',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s — %s', $values['name'], $values['product'] ),
			'post_content' => $body,
			'meta_input'   => array(
				'_ip_name'    => $values['name'],
				'_ip_company' => $values['company'],
				'_ip_email'   => $values['email'],
				'_ip_phone'   => $values['phone'],
				'_ip_product' => $values['product'],
				'_ip_size'    => $values['size'],
				'_ip_message' => $values['message'],
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		ip_quote_redirect(
			$redirect,
			$token,
			array(
				'errors' => array( 'form' => 'Kayıt sırasında bir sorun oldu. Lütfen telefonla ulaşın.' ),
				'values' => $values,
			)
		);
	}

	ip_quote_redirect( $redirect, $token, array( 'success' => true ) );
}

/**
 * Yonlendirmeden sonra formun durumunu (hata / deger / basari) getirir.
 */
function ip_quote_state(): array {
	$empty = array( 'errors' => array(), 'values' => array(), 'success' => false );
	$token = isset( $_GET['ip'] ) ? sanitize_key( wp_unslash( $_GET['ip'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! preg_match( '/^[a-f0-9]{20}$/', $token ) ) {
		return $empty;
	}

	$state = get_transient( 'ip_quote_' . $token );

	if ( ! is_array( $state ) ) {
		return $empty;
	}

	return array(
		'errors'  => $state['errors'] ?? array(),
		'values'  => $state['values'] ?? array(),
		'success' => ! empty( $state['success'] ),
	);
}
