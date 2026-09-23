<?php
/**
 * Kuzen kereste sitelerinin (ithalkeresteci, kavakkeresteci) seed
 * betiklerinin ortak adimlari.
 *
 *   kr_seed_pages()   manifestteki adreslerden WordPress sayfalarini acar
 *   kr_seed_media()   /resources altindaki fotografi medya kitapligina ekler
 *   kr_seed_content() alanlari manifest varsayilanlariyla doldurur, gorselleri
 *                     yerlestirir, kardes site baglantilarini yerele cevirir
 *   kr_localize_site() baska bir sitenin kardes baglantilarini yerele cevirir
 *
 * Tekrar calistirilabilir: ayni sayfa ve gorseli ikinci kez olusturmaz.
 * Alan degerlerini varsayilana GERI YAZAR; icerik girildikten sonra
 * calistirilmamalidir.
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

/**
 * Manifestteki her adresli sayfa icin WordPress sayfasi. Ic ice adresler
 * (/urunler/kalas/) ust sayfanin altina acilir. Ana sayfa "anasayfa" olur.
 */
function kr_seed_pages( array $manifest ): void {
	$pages = array();

	foreach ( $manifest['pages'] as $key => $page ) {
		if ( 'global' === $key || ! empty( $page['hidden'] ) || empty( $page['path'] ) ) {
			continue;
		}

		$pages[ $key ] = $page;
	}

	// Kisa adresler once: ust sayfa alt sayfadan once olussun.
	uasort( $pages, static fn( $a, $b ): int => strlen( $a['path'] ) <=> strlen( $b['path'] ) );

	foreach ( $pages as $key => $page ) {
		$path  = trim( $page['path'], '/' );
		$title = $page['components']['card']['fields']['name']['default']
			?? $page['components']['head']['fields']['title']['default']
			?? preg_replace( '/^[^:]+:\s*/u', '', $page['label'] );

		if ( '' === $path ) {
			$path  = 'anasayfa';
			$title = 'Anasayfa';
		}

		$existing = get_page_by_path( $path );

		if ( $existing ) {
			$id = (int) $existing->ID;
		} else {
			$parent = 0;

			if ( str_contains( $path, '/' ) ) {
				$parent_page = get_page_by_path( dirname( $path ) );
				$parent      = $parent_page ? (int) $parent_page->ID : 0;
			}

			$id = (int) wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_name'   => basename( $path ),
					'post_title'  => $title,
					'post_parent' => $parent,
				)
			);
		}

		if ( 'anasayfa' === $path ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $id );
		}

		WP_CLI::log( sprintf( 'Sayfa: /%s/ (#%d)', $path, $id ) );
	}
}

/**
 * /resources altindaki dosyayi medya kitapligina bir kez ekler.
 */
function kr_seed_media( string $file, string $slug, string $title, string $alt ): int {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'name'           => $slug,
			'posts_per_page' => 1,
			'post_status'    => 'inherit',
			'fields'         => 'ids',
		)
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	$source = '/resources/' . $file;

	if ( ! file_exists( $source ) ) {
		WP_CLI::warning( 'Kaynak dosya bulunamadi: ' . $source );

		return 0;
	}

	$upload = wp_upload_bits( $slug . '.' . strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ), null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	if ( ! empty( $upload['error'] ) ) {
		WP_CLI::warning( 'Yuklenemedi: ' . $file . ' - ' . $upload['error'] );

		return 0;
	}

	$id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => $title,
			'post_name'      => $slug,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $id ) || ! $id ) {
		WP_CLI::warning( 'Medya kaydi olusturulamadi: ' . $file );

		return 0;
	}

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );

	return (int) $id;
}

/**
 * Canli alan adi => yerel alt site. Yerel agda kardes site baglantilari
 * canli siteye degil, buradaki kopyasina gitsin (yalnizca site varsa).
 */
function kr_local_link_map(): array {
	$slugs = array(
		'https://ithalkeresteci.com'  => 'ithalkeresteci',
		'https://kavakkeresteci.com'  => 'kavakkeresteci',
		'https://istanbulpaletci.com' => 'istanbulpaletci',
		'https://ahsapkasa.com'       => 'ahsapkasa',
	);
	$map   = array();

	foreach ( get_sites( array( 'number' => 50 ) ) as $site ) {
		$slug = trim( $site->path, '/' );
		$live = array_search( $slug, $slugs, true );

		if ( false !== $live ) {
			$map[ $live ] = get_home_url( (int) $site->blog_id, '/' );
		}
	}

	return $map;
}

/**
 * Kuzen koprusu ve alt bilgideki kardes siteler: canli adresleri yerele cevirir.
 */
function kr_localize_links( array $content, array $map ): array {
	$local = static fn( $url ) => $map[ rtrim( (string) $url, '/' ) ] ?? $url;

	if ( isset( $content['home']['cousin']['button_url'] ) ) {
		$content['home']['cousin']['button_url'] = $local( $content['home']['cousin']['button_url'] );
	}

	foreach ( $content['global']['footer']['family'] ?? array() as $i => $row ) {
		$content['global']['footer']['family'][ $i ]['url'] = $local( $row['url'] ?? '' );
	}

	return $content;
}

/**
 * Baska bir kereste sitesinin (ornegin kuzen sonradan kurulunca) kardes
 * baglantilarini yerele cevirir. Diger alanlara dokunmaz.
 */
function kr_localize_site( string $slug ): void {
	foreach ( get_sites( array( 'path' => '/' . $slug . '/', 'number' => 1 ) ) as $site ) {
		switch_to_blog( (int) $site->blog_id );
		$content = get_option( 'nwcs_content' );

		if ( is_array( $content ) ) {
			update_option( 'nwcs_content', kr_localize_links( $content, kr_local_link_map() ) );
			WP_CLI::log( '/' . $slug . '/ kardes baglantilari yerele cevrildi.' );
		}

		restore_current_blog();
	}
}

/**
 * Alanlari manifest varsayilanlariyla doldurur, sonra $images ile verilen
 * gorselleri yerlestirir: [ sayfa => [ bilesen => [ alan => kimlik | [kimlik, ...] ] ] ].
 * Liste verilen alanlar tekrarli satir olarak yazilir ('image' alt alani).
 */
function kr_seed_content( array $manifest, array $images ): void {
	$content = array();

	foreach ( $manifest['pages'] as $page_key => $page ) {
		foreach ( $page['components'] as $component_key => $component ) {
			foreach ( $component['fields'] as $field_key => $definition ) {
				$content[ $page_key ][ $component_key ][ $field_key ] = $definition['default'] ?? '';
			}
		}
	}

	foreach ( $images as $page_key => $components ) {
		foreach ( $components as $component_key => $fields ) {
			foreach ( $fields as $field_key => $value ) {
				$content[ $page_key ][ $component_key ][ $field_key ] = is_array( $value )
					? array_map( static fn( int $id ): array => array( 'image' => $id ), array_values( array_filter( $value ) ) )
					: (int) $value;
			}
		}
	}

	update_option( 'nwcs_content', kr_localize_links( $content, kr_local_link_map() ) );
	update_option( 'nwcs_section_order', array( 'home' => $manifest['pages']['home']['sortable_sections'] ?? array() ) );
}
