<?php
/**
 * ahsapkasa temasi (Kocist Orman Urunleri yeniden tasarimi).
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
		return array( 'intro', 'family', 'ctaband' );
	}
}

add_action( 'after_setup_theme', 'ahsapkasa_setup' );
function ahsapkasa_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script', 'search-form' ) );
}

add_action( 'wp_enqueue_scripts', 'ahsapkasa_assets' );
function ahsapkasa_assets(): void {
	// Surum = tema surumu + dosya degisim zamani: dosya her derlendiginde adres
	// degisir, tarayici ve onbellek eski CSS/JS'i gostermez.
	$ver = static fn( string $file ): string => wp_get_theme()->get( 'Version' ) . '.' . (int) @filemtime( get_theme_file_path( $file ) );

	wp_enqueue_style(
		'ahsapkasa-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,300..800&family=Literata:opsz,wght@7..72,400..600&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'ahsapkasa-tailwind',
		get_theme_file_uri( 'assets/tailwind.css' ),
		array( 'ahsapkasa-fonts' ),
		$ver( 'assets/tailwind.css' )
	);

	// style.css yalnizca tema basligini tasir; kuyruga alinmasi WordPress adeti.
	wp_enqueue_style( 'ahsapkasa-style', get_stylesheet_uri(), array( 'ahsapkasa-tailwind' ), $ver( 'style.css' ) );

	wp_enqueue_script( 'ahsapkasa-nav', get_theme_file_uri( 'assets/nav.js' ), array(), $ver( 'assets/nav.js' ), true );
	wp_enqueue_script( 'ahsapkasa-media', get_theme_file_uri( 'assets/media.js' ), array(), $ver( 'assets/media.js' ), true );
}

add_action( 'wp_head', 'ahsapkasa_preconnect', 1 );
function ahsapkasa_preconnect(): void {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}

/**
 * Bos baglantilari '#' yapar, koke gore yazilmis yollari site adresine baglar.
 * Alt dizin multisite'ta '/iletisim/' ag kokune gider; home_url ile duzeltiyoruz.
 */
function ahsapkasa_link( $url ): string {
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
 * Menudeki baglantinin bulunulan sayfa olup olmadigini soyler.
 */
function ahsapkasa_is_current( string $url ): bool {
	$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

	$target  = untrailingslashit( (string) wp_parse_url( ahsapkasa_link( $url ), PHP_URL_PATH ) );
	$current = untrailingslashit( (string) wp_parse_url( $request, PHP_URL_PATH ) );

	return $target === $current;
}

/**
 * Gorsel alani icin img etiketi; deger yoksa sessiz bir yer tutucu.
 */
function ahsapkasa_image_tag( array $image, string $class = '', string $alt_fallback = '' ): string {
	if ( ! empty( $image['url'] ) ) {
		return sprintf(
			'<img src="%1$s" alt="%2$s" class="%3$s" loading="lazy" decoding="async" />',
			esc_url( $image['url'] ),
			esc_attr( $image['alt'] ?: $alt_fallback ),
			esc_attr( $class )
		);
	}

	return sprintf(
		'<div class="%1$s flex items-center justify-center bg-dust text-moss font-display text-xs tracking-[0.14em]" role="img" aria-label="%2$s">%2$s</div>',
		esc_attr( $class ),
		esc_html( $alt_fallback ?: 'Görsel eklenmedi' )
	);
}

/**
 * Satirlari korunarak basilan cok satirli metin.
 */
function ahsapkasa_multiline( string $value ): string {
	return nl2br( esc_html( $value ) );
}

/**
 * Bolum dosyasini basar.
 */
function ahsapkasa_section( string $key, array $args = array() ): void {
	$file = get_theme_file_path( "template-parts/sections/{$key}.php" );

	if ( file_exists( $file ) ) {
		include $file;
	}
}

/* ====================================================================== *
 * Teklif formu
 *
 * Demo sahte basari ekrani gostermez: gonderilen form gercekten kaydedilir,
 * yonetimde "Teklif İstekleri" altinda gorulur.
 * ====================================================================== */

add_action( 'init', 'ahsapkasa_register_quote_cpt' );
function ahsapkasa_register_quote_cpt(): void {
	register_post_type(
		'ak_quote',
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

add_action( 'admin_post_ahsapkasa_quote', 'ahsapkasa_handle_quote' );
add_action( 'admin_post_nopriv_ahsapkasa_quote', 'ahsapkasa_handle_quote' );
function ahsapkasa_handle_quote(): void {
	// Form yalnizca iletisim sayfasinda bulunur; referer bos gelirse oraya doneriz.
	$fallback = home_url( '/iletisim/' );
	$referer  = wp_get_referer();
	$redirect = $referer ? wp_validate_redirect( $referer, $fallback ) : $fallback;

	if ( '' === $redirect ) {
		$redirect = $fallback;
	}

	// Bot tuzagi: gorunmeyen alan dolmussa sessizce geri don.
	if ( ! empty( $_POST['ahsapkasa_website'] ) ) {
		wp_safe_redirect( $redirect );
		exit;
	}

	$values = array(
		'name'    => sanitize_text_field( wp_unslash( $_POST['ak_name'] ?? '' ) ),
		'company' => sanitize_text_field( wp_unslash( $_POST['ak_company'] ?? '' ) ),
		'email'   => sanitize_email( wp_unslash( $_POST['ak_email'] ?? '' ) ),
		'phone'   => sanitize_text_field( wp_unslash( $_POST['ak_phone'] ?? '' ) ),
		'product' => sanitize_text_field( wp_unslash( $_POST['ak_product'] ?? '' ) ),
		'size'    => sanitize_textarea_field( wp_unslash( $_POST['ak_size'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $_POST['ak_message'] ?? '' ) ),
	);

	$errors = array();

	if ( ! isset( $_POST['ahsapkasa_quote_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ahsapkasa_quote_nonce'] ), 'ahsapkasa_quote' ) ) {
		$errors['form'] = 'Form oturumu zaman aşımına uğradı. Lütfen tekrar gönderin.';
	}

	if ( '' === $values['name'] ) {
		$errors['name'] = 'Adınızı yazın.';
	}

	if ( '' === $values['email'] || ! is_email( $values['email'] ) ) {
		$errors['email'] = 'Geçerli bir e-posta adresi yazın.';
	}

	if ( '' === $values['size'] ) {
		$errors['size'] = 'En, boy ve yükseklik bilgisini yazın.';
	}

	// Kucuk harfli onaltilik: okurken sanitize_key() buyuk harfi kucultur.
	// Karisik harfli anahtar yerelde (harf duyarsiz MySQL) calisir ama harf
	// duyarli nesne onbelleginde (LiteSpeed, Redis) bulunamaz.
	$token = bin2hex( random_bytes( 10 ) );

	if ( $errors ) {
		set_transient( 'ahsapkasa_quote_' . $token, array( 'errors' => $errors, 'values' => $values ), 10 * MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( 'ak', $token, $redirect ) . '#teklif' );
		exit;
	}

	// Urun secenekleri manifestten gelir; elle gonderilen baska bir deger kabul edilmez.
	$allowed_products = array();
	foreach ( nwcs_rows( 'services', 'grid', 'items' ) as $row ) {
		if ( ! empty( $row['title'] ) ) {
			$allowed_products[] = $row['title'];
		}
	}

	if ( ! in_array( $values['product'], $allowed_products, true ) ) {
		$values['product'] = 'Belirtilmedi';
	}

	$body = sprintf(
		"Firma: %s\nE-posta: %s\nTelefon: %s\nÜrün: %s\n\nÖlçüler ve adet:\n%s\n\nMesaj:\n%s",
		$values['company'] ?: '—',
		$values['email'],
		$values['phone'] ?: '—',
		$values['product'],
		$values['size'],
		$values['message'] ?: '—'
	);

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'ak_quote',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s — %s', $values['name'], $values['product'] ),
			'post_content' => $body,
			'meta_input'   => array(
				'_ak_name'    => $values['name'],
				'_ak_company' => $values['company'],
				'_ak_email'   => $values['email'],
				'_ak_phone'   => $values['phone'],
				'_ak_product' => $values['product'],
				'_ak_size'    => $values['size'],
				'_ak_message' => $values['message'],
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		set_transient(
			'ahsapkasa_quote_' . $token,
			array( 'errors' => array( 'form' => 'Kayıt sırasında bir sorun oldu. Lütfen telefonla ulaşın.' ), 'values' => $values ),
			10 * MINUTE_IN_SECONDS
		);
		wp_safe_redirect( add_query_arg( 'ak', $token, $redirect ) . '#teklif' );
		exit;
	}

	set_transient( 'ahsapkasa_quote_' . $token, array( 'success' => true ), 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( add_query_arg( 'ak', $token, $redirect ) . '#teklif' );
	exit;
}

/**
 * Yonlendirmeden sonra formun durumunu (hata / deger / basari) getirir.
 */
function ahsapkasa_quote_state(): array {
	$token = isset( $_GET['ak'] ) ? sanitize_key( wp_unslash( $_GET['ak'] ) ) : '';

	if ( ! preg_match( '/^[a-f0-9]{20}$/', $token ) ) {
		return array( 'errors' => array(), 'values' => array(), 'success' => false );
	}

	$state = get_transient( 'ahsapkasa_quote_' . $token );

	if ( ! is_array( $state ) ) {
		return array( 'errors' => array(), 'values' => array(), 'success' => false );
	}

	return array(
		'errors'  => $state['errors'] ?? array(),
		'values'  => $state['values'] ?? array(),
		'success' => ! empty( $state['success'] ),
	);
}
