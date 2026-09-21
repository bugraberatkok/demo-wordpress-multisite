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
		return array( 'capabilities', 'catalog', 'references', 'ctaband' );
	}
}

add_action( 'after_setup_theme', 'kocist_setup' );
function kocist_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script' ) );
}

add_action( 'wp_enqueue_scripts', 'kocist_assets' );
function kocist_assets(): void {
	wp_enqueue_style( 'kocist-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
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
 * Bos baglantilari '#' yapar; cikti her zaman esc_url ile basilir.
 */
function kocist_link( $url ): string {
	$url = trim( (string) $url );

	return '' === $url ? '#' : $url;
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
