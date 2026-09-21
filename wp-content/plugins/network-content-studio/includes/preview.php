<?php
/**
 * Panel icindeki canli onizleme modu.
 *
 * Site, panelden gelen iframe icinde `?nwcs_preview=1` ile acilir. Mod yalnizca
 * yetkili kullanicilar icin acilir; ziyaretciye hicbir isaret veya admin araci
 * sizmaz. Tema sablonlari nwcs_edit_attr() ile alan isaretlerini basar.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Onizleme modunda miyiz?
 *
 * Yetki kontrolu her istekte yapilir; parametrenin tek basina olmasi yetmez.
 */
function nwcs_is_preview(): bool {
	static $is_preview = null;

	if ( null !== $is_preview ) {
		return $is_preview;
	}

	$value = isset( $_GET['nwcs_preview'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- salt gorunum modu, yazma yok.
		&& is_user_logged_in()
		&& current_user_can( 'manage_network_options' );

	// Kullanici 'init' oncesinde henuz belirlenmemis olabilir; o asamada
	// sonucu onbellege almiyoruz ki yanlis deger sabitlenmesin.
	if ( did_action( 'init' ) ) {
		$is_preview = $value;
	}

	return $value;
}

// Admin cubugu 'init' sirasinda karara baglanir; filtre bu yuzden erken eklenir.
add_filter( 'show_admin_bar', 'nwcs_preview_hide_admin_bar', 99 );
function nwcs_preview_hide_admin_bar( $show ) {
	return nwcs_is_preview() ? false : $show;
}

add_action( 'template_redirect', 'nwcs_preview_setup' );
function nwcs_preview_setup(): void {
	if ( ! nwcs_is_preview() ) {
		return;
	}

	add_filter( 'body_class', 'nwcs_preview_body_class' );
	add_action( 'wp_enqueue_scripts', 'nwcs_preview_assets', 20 );
}

function nwcs_preview_body_class( array $classes ): array {
	$classes[] = 'nwcs-preview-mode';

	return $classes;
}

function nwcs_preview_assets(): void {
	wp_enqueue_style( 'nwcs-preview', NWCS_URL . 'assets/preview.css', array(), NWCS_VERSION );
	wp_enqueue_script( 'nwcs-preview', NWCS_URL . 'assets/preview.js', array(), NWCS_VERSION, true );
	wp_localize_script(
		'nwcs-preview',
		'nwcsPreview',
		array(
			'blogId' => get_current_blog_id(),
			'origin' => untrailingslashit( network_site_url() ),
			'labels' => nwcs_preview_labels(),
		)
	);
}

/**
 * Isaretlenen alanlarin Turkce etiketleri (ipucu balonu icin).
 */
function nwcs_preview_labels(): array {
	$manifest = nwcs_manifest();
	$labels   = array();

	foreach ( $manifest['pages'] ?? array() as $page_key => $page ) {
		foreach ( $page['components'] as $component_key => $component ) {
			$labels[ $page_key . '.' . $component_key ] = $component['label'];

			foreach ( $component['fields'] as $field_key => $definition ) {
				$labels[ $page_key . '.' . $component_key . '.' . $field_key ] = $definition['label'];

				foreach ( $definition['fields'] ?? array() as $sub_key => $sub_def ) {
					$labels[ $page_key . '.' . $component_key . '.' . $field_key . '.' . $sub_key ] = $sub_def['label'];
				}
			}
		}
	}

	return $labels;
}

/**
 * Tema sablonlarinda kullanilir: duzenlenebilir alani isaretler.
 *
 * Ornek: <h1 <?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>>
 *
 * Tekrarli satirlarda $row ve $sub verilir:
 *   nwcs_edit_attr( 'home', 'products', 'items', $index, 'title' )
 */
function nwcs_edit_attr( string $page, string $component, string $field = '', ?int $row = null, string $sub = '' ): void {
	if ( ! nwcs_is_preview() ) {
		return;
	}

	$parts = array( $page, $component );

	if ( '' !== $field ) {
		$parts[] = $field;
	}

	$target = implode( '.', $parts );

	if ( null !== $row && '' !== $sub ) {
		$target .= '.' . $sub;
	}

	printf(
		' data-nwcs-edit="%s"%s data-nwcs-component="%s"',
		esc_attr( $target ),
		null !== $row ? ' data-nwcs-row="' . esc_attr( (string) $row ) . '"' : '',
		esc_attr( $page . '.' . $component )
	);
}
