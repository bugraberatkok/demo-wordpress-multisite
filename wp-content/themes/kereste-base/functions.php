<?php
/**
 * kereste-base: kuzen kereste sitelerinin (ithalkeresteci, kavakkeresteci)
 * ortak ana temasi.
 *
 * Sablonlar, bolumler ve teklif formu burada; cocuk temalar
 * yalnizca renk (style.css) ve icerik manifestini (content-manifest.php)
 * tasir. Gorunen metinler Network Content Studio veri katmanindan okunur;
 * eklenti kapaliysa sayfa fatal vermesin diye yedek fonksiyonlar var.
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
		return array( 'products', 'cousin', 'faq', 'quote' );
	}
}

add_action( 'after_setup_theme', 'kr_setup' );
function kr_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script', 'search-form' ) );
}

add_action( 'wp_enqueue_scripts', 'kr_assets' );
function kr_assets(): void {
	$version = wp_get_theme( get_template() )->get( 'Version' );

	wp_enqueue_style(
		'kr-fonts',
		'https://fonts.googleapis.com/css2?family=Saira+Stencil+One&family=Source+Sans+3:wght@400;600;700&family=Zilla+Slab:wght@600;700&display=swap',
		array(),
		null
	);

	// Ortak stil ana temadan; cocuk temanin style.css'i sonra gelir ve yalnizca
	// parti boyasini (vurgu rengi) tanimlar.
	wp_enqueue_style( 'kr-tailwind', get_template_directory_uri() . '/assets/tailwind.css', array( 'kr-fonts' ), $version );
	wp_enqueue_style( 'kr-site', get_stylesheet_uri(), array( 'kr-tailwind' ), wp_get_theme()->get( 'Version' ) );

	wp_enqueue_script( 'kr-site', get_template_directory_uri() . '/assets/site.js', array(), $version, true );
}

add_action( 'wp_head', 'kr_preconnect', 1 );
function kr_preconnect(): void {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}

/* ====================================================================== *
 * Genel yardimcilar
 * ====================================================================== */

/**
 * Koke gore yazilmis yolu site adresine baglar; bos baglanti '#'.
 */
function kr_link( $url ): string {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '#';
	}

	if ( str_starts_with( $url, '/' ) && ! str_starts_with( $url, '//' ) ) {
		return home_url( $url );
	}

	return $url;
}

function kr_request_path(): string {
	$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

	return untrailingslashit( (string) wp_parse_url( $request, PHP_URL_PATH ) );
}

function kr_link_path( string $url ): string {
	return untrailingslashit( (string) wp_parse_url( kr_link( $url ), PHP_URL_PATH ) );
}

function kr_is_current( string $url ): bool {
	return kr_link_path( $url ) === kr_request_path();
}

/**
 * Bulunulan sayfa bu baglantinin altinda mi (urun sayfasinda "Urunler").
 */
function kr_is_within( string $url ): bool {
	$target = kr_link_path( $url );

	if ( $target === untrailingslashit( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ) ) ) {
		return false;
	}

	return str_starts_with( kr_request_path() . '/', $target . '/' );
}

/**
 * Gorsel etiketi; gorsel yoksa sessiz yer tutucu.
 */
function kr_image_tag( array $image, string $class = '', string $alt_fallback = '', bool $eager = false ): string {
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
		'<span class="%1$s flex items-center justify-center bg-stone text-sm text-muted" role="img" aria-label="%2$s">%2$s</span>',
		esc_attr( $class ),
		esc_html( $alt_fallback ?: 'Görsel eklenmedi' )
	);
}

function kr_multiline( string $value ): string {
	return nl2br( esc_html( $value ) );
}

/**
 * Bos satirla ayrilmis metni paragraflara boler.
 */
function kr_paragraphs( string $value, string $class = '' ): string {
	$html = '';

	foreach ( preg_split( '/\R\s*\R/u', trim( $value ) ) ?: array() as $block ) {
		$block = trim( $block );

		if ( '' !== $block ) {
			$html .= sprintf( '<p%s>%s</p>', $class ? ' class="' . esc_attr( $class ) . '"' : '', kr_multiline( $block ) );
		}
	}

	return $html;
}

function kr_section( string $key ): void {
	$file = get_theme_file_path( "template-parts/sections/{$key}.php" );

	if ( file_exists( $file ) ) {
		include $file;
	}
}

/**
 * Sitenin manifesti (cocuk temanin content-manifest.php dosyasi).
 */
function kr_manifest(): array {
	if ( function_exists( 'nwcs_manifest' ) ) {
		return nwcs_manifest();
	}

	static $manifest = null;

	if ( null === $manifest ) {
		$file     = get_stylesheet_directory() . '/content-manifest.php';
		$loaded   = file_exists( $file ) ? include $file : array();
		$manifest = is_array( $loaded ) ? $loaded : array();
	}

	return $manifest;
}

function kr_page_path( string $key, string $fallback = '/' ): string {
	return (string) ( kr_manifest()['pages'][ $key ]['path'] ?? $fallback );
}

/* ====================================================================== *
 * Urunler: tek kaynak manifest ('template' => 'product' isaretli sayfalar)
 * ====================================================================== */

function kr_products(): array {
	static $products = null;

	if ( null !== $products ) {
		return $products;
	}

	$products = array();

	foreach ( kr_manifest()['pages'] ?? array() as $key => $page ) {
		if ( 'product' !== ( $page['template'] ?? '' ) ) {
			continue;
		}

		$products[ $key ] = array(
			'key'   => $key,
			'path'  => (string) $page['path'],
			'url'   => kr_link( $page['path'] ),
			'name'  => (string) nwcs_field( $key, 'card', 'name' ),
			'short' => (string) nwcs_field( $key, 'card', 'short' ),
			'mark'  => (string) nwcs_field( $key, 'card', 'mark' ),
			'image' => nwcs_image( $key, 'card', 'image', 'large' ),
		);
	}

	return $products;
}

function kr_current_product_key(): string {
	foreach ( kr_products() as $key => $product ) {
		if ( kr_link_path( $product['path'] ) === kr_request_path() ) {
			return $key;
		}
	}

	return '';
}

/**
 * Urun galerisi: kart gorseli basta, sonra galeri satirlari.
 */
function kr_product_gallery( string $key ): array {
	$ids = array( (int) nwcs_field( $key, 'card', 'image', 0 ) );

	foreach ( nwcs_rows( $key, 'detail', 'gallery' ) as $row ) {
		$ids[] = (int) ( $row['image'] ?? 0 );
	}

	$gallery = array();
	$name    = (string) nwcs_field( $key, 'card', 'name' );

	foreach ( array_unique( array_filter( $ids ) ) as $id ) {
		$large = nwcs_image_by_id( $id, 'large' );

		if ( '' !== $large['url'] ) {
			$gallery[] = array(
				'url'   => $large['url'],
				'alt'   => $large['alt'] ?: $name,
				'thumb' => nwcs_image_by_id( $id, 'medium' )['url'] ?: $large['url'],
			);
		}
	}

	return $gallery;
}

/**
 * Teklif dugmesinin JavaScript'siz calisan yedegi: iletisim sayfasindaki
 * form, urunu adresten okuyup secili getirir.
 */
function kr_quote_fallback_url( string $product = '' ): string {
	$url = kr_link( kr_page_path( 'contact', '/iletisim/' ) );

	if ( '' !== $product ) {
		$url = add_query_arg( 'urun', rawurlencode( $product ), $url );
	}

	return $url . '#teklif';
}

/* ====================================================================== *
 * Teklif formu
 *
 * Her sayfada bir teklif penceresi var (template-parts/quote-dialog.php);
 * Iletisim sayfasinda ayni form sayfanin icinde. Gonderim gercekten
 * kaydedilir: yonetimde "Teklif Istekleri". Sahte basari ekrani yok.
 * ====================================================================== */

add_action( 'init', 'kr_register_quote_cpt' );
function kr_register_quote_cpt(): void {
	register_post_type(
		'kr_quote',
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
 * Yonlendirmeden sonra form durumunu tasiyan anahtar: kucuk harfli
 * onaltilik; harf duyarli nesne onbelleginde de bulunur (DECISIONS.md).
 */
function kr_quote_redirect( string $redirect, array $state ): void {
	$token = bin2hex( random_bytes( 10 ) );

	set_transient( 'kr_quote_' . $token, $state, 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( add_query_arg( 'teklif', $token, $redirect ) . '#teklif' );
	exit;
}

add_action( 'admin_post_kr_quote', 'kr_handle_quote' );
add_action( 'admin_post_nopriv_kr_quote', 'kr_handle_quote' );
function kr_handle_quote(): void {
	$fallback = home_url( '/' );
	$referer  = wp_get_referer();
	$redirect = $referer ? wp_validate_redirect( $referer, $fallback ) : $fallback;
	$redirect = strtok( remove_query_arg( 'teklif', '' !== $redirect ? $redirect : $fallback ), '#' );

	// Bot tuzagi.
	if ( ! empty( $_POST['kr_website'] ) ) {
		wp_safe_redirect( $redirect );
		exit;
	}

	$values = array(
		'name'    => sanitize_text_field( wp_unslash( $_POST['kr_name'] ?? '' ) ),
		'company' => sanitize_text_field( wp_unslash( $_POST['kr_company'] ?? '' ) ),
		'phone'   => sanitize_text_field( wp_unslash( $_POST['kr_phone'] ?? '' ) ),
		'email'   => sanitize_email( wp_unslash( $_POST['kr_email'] ?? '' ) ),
		'product' => sanitize_text_field( wp_unslash( $_POST['kr_product'] ?? '' ) ),
		'size'    => sanitize_textarea_field( wp_unslash( $_POST['kr_size'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $_POST['kr_message'] ?? '' ) ),
	);

	$errors = array();

	if ( ! isset( $_POST['kr_quote_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['kr_quote_nonce'] ), 'kr_quote' ) ) {
		$errors['form'] = 'Form oturumu zaman aşımına uğradı. Lütfen tekrar gönderin.';
	}

	if ( '' === $values['name'] ) {
		$errors['name'] = 'Adınızı yazın.';
	}

	if ( '' !== $values['email'] && ! is_email( $values['email'] ) ) {
		$errors['email'] = 'E-posta adresi geçerli görünmüyor.';
	} elseif ( '' === $values['email'] && '' === $values['phone'] ) {
		$errors['phone'] = 'Size dönebilmemiz için telefon ya da e-posta yazın.';
	}

	if ( '' === $values['size'] ) {
		$errors['size'] = 'Ölçü ve adet bilgisini yazın.';
	}

	if ( $errors ) {
		kr_quote_redirect( $redirect, array( 'errors' => $errors, 'values' => $values ) );
	}

	if ( ! in_array( $values['product'], wp_list_pluck( kr_products(), 'name' ), true ) ) {
		$values['product'] = 'Belirtilmedi';
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'kr_quote',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s — %s', $values['name'], $values['product'] ),
			'post_content' => sprintf(
				"Firma: %s\nTelefon: %s\nE-posta: %s\nÜrün: %s\n\nÖlçü ve adet:\n%s\n\nMesaj:\n%s",
				$values['company'] ?: '—',
				$values['phone'] ?: '—',
				$values['email'] ?: '—',
				$values['product'],
				$values['size'],
				$values['message'] ?: '—'
			),
			'meta_input'   => array(
				'_kr_name'    => $values['name'],
				'_kr_company' => $values['company'],
				'_kr_phone'   => $values['phone'],
				'_kr_email'   => $values['email'],
				'_kr_product' => $values['product'],
				'_kr_size'    => $values['size'],
				'_kr_message' => $values['message'],
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		kr_quote_redirect(
			$redirect,
			array(
				'errors' => array( 'form' => 'Kayıt sırasında bir sorun oldu. Lütfen telefonla ulaşın.' ),
				'values' => $values,
			)
		);
	}

	kr_quote_redirect( $redirect, array( 'success' => true ) );
}

function kr_quote_state(): array {
	$empty = array( 'errors' => array(), 'values' => array(), 'success' => false, 'active' => false );
	$token = isset( $_GET['teklif'] ) ? sanitize_key( wp_unslash( $_GET['teklif'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! preg_match( '/^[a-f0-9]{20}$/', $token ) ) {
		return $empty;
	}

	$state = get_transient( 'kr_quote_' . $token );

	if ( ! is_array( $state ) ) {
		return $empty;
	}

	return array(
		'errors'  => $state['errors'] ?? array(),
		'values'  => $state['values'] ?? array(),
		'success' => ! empty( $state['success'] ),
		'active'  => true,
	);
}
