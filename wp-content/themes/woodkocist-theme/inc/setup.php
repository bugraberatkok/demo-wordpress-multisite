<?php
/**
 * Kurulum: tema bir siteye atandiginda sayfalari ve ayarlari kurar.
 * Idempotent; var olan sayfanin metnine dokunmaz. Urun secimi panelden yapilir
 * (eklenti yeni sitede urun kipini 'selected' ve bos liste olarak acar).
 *
 * Sayfa adresleri woodkocist.com.tr ile ayni (/magaza/, /sepet/, /odeme/,
 * /sirketimiz/, /sss/, /iletisim/, /kvkk/ ...): canliya gecince eski
 * baglantilar ve arama motoru kayitlari aynen calisir.
 */

defined( 'ABSPATH' ) || exit;

const WK_SETUP_VERSION = '3';

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

/**
 * Sitenin sayfalari: kisaltma => baslik. Metni sablon ya da panel cizer;
 * yasal sayfalarin ilk metni content/pages.php.
 */
function wk_site_pages(): array {
	return array(
		'anasayfa'                => 'Anasayfa',
		'magaza'                  => 'Mağaza',
		'sepet'                   => 'Sepet',
		'odeme'                   => 'Ödeme',
		'sirketimiz'              => 'Şirketimiz',
		'sss'                     => 'Sıkça Sorulan Sorular',
		'iletisim'                => 'Çözüm Merkezi',
		'ozel-uretim-talep-formu' => 'Özel Üretim',
	);
}

function wk_setup_site(): void {
	if ( WK_SETUP_VERSION === get_option( 'wk_setup_version' ) || get_transient( 'wk_setup_lock' ) ) {
		return;
	}

	set_transient( 'wk_setup_lock', 1, MINUTE_IN_SECONDS );

	// Eski yerel deneme /hakkimizda/ idi; gercek sitedeki adres /sirketimiz/.
	$old = get_page_by_path( 'hakkimizda' );

	if ( $old && ! get_page_by_path( 'sirketimiz' ) ) {
		wp_update_post(
			array(
				'ID'         => $old->ID,
				'post_name'  => 'sirketimiz',
				'post_title' => 'Şirketimiz',
			)
		);
	}

	foreach ( wk_site_pages() as $slug => $title ) {
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

	// Yasal ve kurumsal sayfalar: yoksa acilir; varsa ve bossa ilk metin yazilir.
	foreach ( (array) require get_theme_file_path( 'content/pages.php' ) as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		$data     = array(
			'post_type'    => 'page',
			'post_name'    => $slug,
			'post_title'   => $page['title'],
			'post_excerpt' => $page['lead'],
			'post_content' => wk_setup_links( $page['html'] ),
			'post_status'  => 'publish',
		);

		if ( ! $existing ) {
			wp_insert_post( $data );
		} elseif ( '' === trim( $existing->post_content ) ) {
			wp_update_post( array( 'ID' => $existing->ID ) + $data );
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
	update_option( 'blogdescription', 'Orman Ürünleri: ahşap bahçe mobilyası, ev ürünleri ve evcil hayvan yuvaları' );

	if ( in_array( 'tr_TR', get_available_languages(), true ) ) {
		update_option( 'WPLANG', 'tr_TR' );
	}

	if ( '/%postname%/' !== (string) get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}

	// /urun/<slug>/ kurali eklentiden, /urun-kategori/ temadan; kurallar yeniden yazilir.
	flush_rewrite_rules( false );
	update_option( 'wk_setup_version', WK_SETUP_VERSION );
	delete_transient( 'wk_setup_lock' );
}

/**
 * Ilk metindeki goreli site ici baglantilar (/kvkk/) sitenin adresine cevrilir
 * (yerelde site bir alt klasorde durur).
 */
function wk_setup_links( string $html ): string {
	return (string) preg_replace_callback(
		'#href="(/[^"]*)"#',
		static fn( array $m ): string => 'href="' . esc_url( home_url( $m[1] ) ) . '"',
		$html
	);
}
