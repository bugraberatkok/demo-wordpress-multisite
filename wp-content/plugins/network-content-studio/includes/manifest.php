<?php
/**
 * Alan manifesti kesfi.
 *
 * Multisite'da switch_to_blog() baska sitenin tema kodunu yuklemez. Bu yuzden
 * manifestleri tema dizininden dogrudan dosya sistemi uzerinden okuyoruz:
 * her tema kokunde, hicbir WordPress fonksiyonuna bagimli olmayan, sadece dizi
 * donduren bir content-manifest.php bulunur.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tema slug'ina gore manifesti yukler. Tema aktif olmak zorunda degildir.
 */
function nwcs_manifest_for_theme( string $theme_slug ): array {
	static $cache = array();

	if ( isset( $cache[ $theme_slug ] ) ) {
		return $cache[ $theme_slug ];
	}

	$slug = sanitize_file_name( $theme_slug );
	$file = trailingslashit( WP_CONTENT_DIR ) . 'themes/' . $slug . '/content-manifest.php';

	$manifest = array();
	if ( $slug && file_exists( $file ) ) {
		$loaded = include $file;
		if ( is_array( $loaded ) ) {
			$manifest = $loaded;
		}
	}

	$cache[ $theme_slug ] = $manifest;

	return $manifest;
}

/**
 * Belirtilen sitenin manifesti. Site baglamina gecmeden calisir.
 */
function nwcs_manifest_for_blog( int $blog_id ): array {
	$theme = get_blog_option( $blog_id, 'stylesheet', '' );

	return $theme ? nwcs_manifest_for_theme( (string) $theme ) : array();
}

/**
 * Aktif sitenin manifesti (tema tarafinda kullanilir).
 */
function nwcs_manifest(): array {
	return nwcs_manifest_for_theme( (string) get_option( 'stylesheet' ) );
}

/**
 * Manifestten tek bir alan tanimi getirir.
 */
function nwcs_field_def( array $manifest, string $page, string $component, string $field ): ?array {
	return $manifest['pages'][ $page ]['components'][ $component ]['fields'][ $field ] ?? null;
}

/**
 * Manifestteki varsayilan degeri getirir.
 */
function nwcs_field_default( array $manifest, string $page, string $component, string $field ) {
	$def = nwcs_field_def( $manifest, $page, $component, $field );

	return $def['default'] ?? '';
}

/**
 * Manifestte tanimli, siralanabilir bolum anahtarlari.
 */
function nwcs_sortable_sections( array $manifest, string $page = 'home' ): array {
	$sections = $manifest['pages'][ $page ]['sortable_sections'] ?? array();

	return is_array( $sections ) ? $sections : array();
}
