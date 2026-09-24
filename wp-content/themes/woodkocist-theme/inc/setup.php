<?php
/**
 * Kurulum: tema bir siteye atandiginda sayfalari ve ayarlari kurar.
 * Idempotent; var olan sayfaya dokunmaz. Urun secimi panelden yapilir
 * (eklenti yeni sitede urun kipini 'selected' ve bos liste olarak acar).
 */

defined( 'ABSPATH' ) || exit;

const WK_SETUP_VERSION = '2';

add_action( 'after_switch_theme', 'wk_setup_site' );
add_action( 'admin_init', 'wk_maybe_setup_site' );

function wk_maybe_setup_site(): void {
	if ( ! current_user_can( 'manage_options' ) && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	if ( get_option( 'wk_setup_version' ) !== WK_SETUP_VERSION ) {
		wk_setup_site();
	}
}

function wk_setup_site(): void {
	if ( WK_SETUP_VERSION === get_option( 'wk_setup_version' ) || get_transient( 'wk_setup_lock' ) ) {
		return;
	}

	set_transient( 'wk_setup_lock', 1, MINUTE_IN_SECONDS );

	foreach ( array( 'anasayfa' => 'Anasayfa', 'hakkimizda' => 'Hakkımızda', 'iletisim' => 'İletişim' ) as $slug => $title ) {
		if ( ! get_page_by_path( $slug ) ) {
			wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_name'   => $slug,
					'post_title'  => $title,
					'post_status' => 'publish',
				)
			);
		}
	}

	// WordPress'in ornek yazi ve sayfasi (duzenlenmemisse).
	foreach ( array( 'post' => array( 'hello-world', 'merhaba-dunya' ), 'page' => array( 'sample-page', 'ornek-sayfa' ) ) as $type => $slugs ) {
		foreach ( $slugs as $slug ) {
			$post = get_page_by_path( $slug, OBJECT, $type );

			if ( $post && $post->post_date === $post->post_modified ) {
				wp_delete_post( $post->ID, true );
			}
		}
	}

	// On sayfa bir sayfaya baglanir (front-page.php cizer); boylece ana sayfa
	// site haritasinda da yer alir.
	$front = get_page_by_path( 'anasayfa' );

	if ( $front ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $front->ID );
	}
	update_option( 'blogdescription', 'Ahşap bahçe mobilyası, ev ürünleri ve evcil hayvan yuvaları' );

	if ( in_array( 'tr_TR', get_available_languages(), true ) ) {
		update_option( 'WPLANG', 'tr_TR' );
	}

	if ( '/%postname%/' !== (string) get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}

	// /urun/<slug>/ kurali eklentiden gelir; kurallar yeniden yazilir.
	flush_rewrite_rules( false );
	update_option( 'wk_setup_version', WK_SETUP_VERSION );
	delete_transient( 'wk_setup_lock' );
}
