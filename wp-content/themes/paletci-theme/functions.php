<?php
/**
 * Paletci temasi.
 *
 * Gorunur metinler sabit yazilmaz; degerler Network Content Studio veri
 * katmanindan okunur. Eklenti yoksa sayfa fatal vermesin diye yedek
 * fonksiyonlar tanimlanir.
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
	function nwcs_section_order( $page = 'home' ) {
		return array( 'products', 'process', 'why', 'quote' );
	}
}

add_action( 'after_setup_theme', 'paletci_setup' );
function paletci_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script' ) );
}

add_action( 'wp_enqueue_scripts', 'paletci_assets' );
function paletci_assets(): void {
	wp_enqueue_style( 'paletci-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}

/**
 * Ana sayfa bolumunu kayitli siraya gore basar.
 */
function paletci_section( string $key ): void {
	$file = get_theme_file_path( "template-parts/sections/{$key}.php" );

	if ( file_exists( $file ) ) {
		include $file;
	}
}

function paletci_link( $url ): string {
	$url = trim( (string) $url );

	return '' === $url ? '#' : $url;
}

function paletci_image_tag( array $image, string $class = '', string $placeholder = 'Örnek görsel' ): string {
	if ( ! empty( $image['url'] ) ) {
		return sprintf(
			'<img src="%1$s" alt="%2$s" class="%3$s" loading="lazy" decoding="async" />',
			esc_url( $image['url'] ),
			esc_attr( $image['alt'] ),
			esc_attr( $class )
		);
	}

	return sprintf(
		'<div class="p-placeholder %1$s" role="img" aria-label="%2$s">%2$s</div>',
		esc_attr( $class ),
		esc_html( $placeholder )
	);
}
