<?php
/**
 * woodkocist.com.tr urunlerini Urun Havuzu'na aktarir (YEREL DENEME).
 *
 * Calistirma (havuz agin ana sitesinde durur):
 *   wp eval-file /scripts/import-woodkocist.php --url=http://localhost:8080/
 *
 * Kaynak: scripts/data/woodkocist-products.json — sitenin herkese acik
 * WooCommerce Store API ciktisi (/wp-json/wc/store/v1/products?per_page=100),
 * 24 Eylul 2026'da alindi. Giris yetkisi kullanilmadi.
 *
 * - Anahtar urun kodu (SKU): tekrar calisirsa ayni urunu gunceller, kopya acmaz.
 * - Gorsel ALINMAZ (kullanici karari: simdilik bos). Kartlar kod plakasiyla cizilir.
 * - Fiyat: tek fiyatli urunde "10.250 ₺"; olcu secenekli (variable) ve fiyati 0
 *   gorunen urunde bos (sitede "Fiyat icin sorun").
 * - Ozellikler (Ahsap Cinsi, Boyut Sinifi...) tek satir: "Ad: deger; Ad: deger".
 * - Kategoriler WooCommerce'teki adlarla (WOODGarden, Adirondack...).
 * - /woodkocist/ sitesi varsa urunler o sitede sirasiyla secili gelir.
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 'Yalnizca WP-CLI ile calistirilir.' );
}

if ( ! function_exists( 'nwcs_pool_blog_id' ) ) {
	WP_CLI::error( 'Network Content Studio etkin degil.' );
}

if ( get_current_blog_id() !== nwcs_pool_blog_id() ) {
	WP_CLI::error( 'Havuz sitesinde calistirin: --url=<ag ana adresi>' );
}

$file  = '/scripts/data/woodkocist-products.json';
$items = json_decode( (string) file_get_contents( $file ), true );

if ( ! is_array( $items ) ) {
	WP_CLI::error( 'Veri dosyasi okunamadi: ' . $file );
}

/**
 * Store API fiyatini (kurus) "10.250 ₺" bicimine cevirir.
 */
$format_price = static function ( array $prices ): string {
	$minor = (int) ( $prices['currency_minor_unit'] ?? 2 );
	$raw   = (int) ( $prices['price'] ?? 0 );

	if ( $raw <= 0 ) {
		return '';
	}

	$value    = $raw / ( 10 ** $minor );
	$decimals = ( $raw % ( 10 ** $minor ) ) ? $minor : 0;

	return number_format( $value, $decimals, ',', '.' ) . ' ₺';
};

$clean_text = static fn( string $html ): string => trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) ) );

$ids     = array();
$created = 0;
$updated = 0;

foreach ( $items as $index => $item ) {
	$code = nwcs_normalize_product_code( (string) ( $item['sku'] ?? '' ) );

	if ( '' === $code ) {
		WP_CLI::warning( 'Kodsuz urun atlandi: ' . ( $item['name'] ?? '?' ) );
		continue;
	}

	$name = html_entity_decode( (string) $item['name'], ENT_QUOTES, 'UTF-8' );

	// Ad kodla basliyorsa ("W-ADR-FB01 Klasik Seri...") kod ayri alanda durur.
	// Kaynakta ad baska bir kodla basliyor olabilir (W-ADR-KL02-1 / W-ADR-KL03):
	// bastaki her urun kodu kalibi ayiklanir.
	$title = trim( preg_replace( '/^W-[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*\s+/u', '', $name ) );

	$spec = array();
	foreach ( (array) ( $item['attributes'] ?? array() ) as $attribute ) {
		$terms = array_map( static fn( $term ) => html_entity_decode( (string) $term['name'], ENT_QUOTES, 'UTF-8' ), (array) ( $attribute['terms'] ?? array() ) );

		if ( $terms ) {
			$spec[] = html_entity_decode( (string) $attribute['name'], ENT_QUOTES, 'UTF-8' ) . ': ' . implode( ', ', $terms );
		}
	}

	$postarr = array(
		'post_type'    => NWCS_PRODUCT_TYPE,
		'post_status'  => 'publish',
		'post_title'   => $title ?: $name,
		'post_name'    => sanitize_title( (string) ( $item['slug'] ?? $title ) ),
		'post_content' => wp_kses_post( (string) ( $item['description'] ?? '' ) ),
		'menu_order'   => (int) $index,
	);

	$id = nwcs_product_id_by_code( $code );

	if ( $id ) {
		$postarr['ID'] = $id;
		wp_update_post( $postarr );
		++$updated;
	} else {
		$id = (int) wp_insert_post( $postarr );
		++$created;
	}

	if ( ! $id ) {
		WP_CLI::warning( 'Kaydedilemedi: ' . $code );
		continue;
	}

	update_post_meta( $id, '_nwcs_code', $code );
	update_post_meta( $id, '_nwcs_short', sanitize_textarea_field( $clean_text( (string) ( $item['short_description'] ?? '' ) ) ) );
	update_post_meta( $id, '_nwcs_price', sanitize_text_field( $format_price( (array) ( $item['prices'] ?? array() ) ) ) );
	update_post_meta( $id, '_nwcs_spec', sanitize_text_field( implode( '; ', $spec ) ) );

	$term_ids = array();
	foreach ( (array) ( $item['categories'] ?? array() ) as $category ) {
		$label = html_entity_decode( (string) $category['name'], ENT_QUOTES, 'UTF-8' );
		$term  = get_term_by( 'name', $label, NWCS_PRODUCT_TAX );

		if ( ! $term ) {
			$made = wp_insert_term( $label, NWCS_PRODUCT_TAX, array( 'slug' => sanitize_title( (string) ( $category['slug'] ?? $label ) ) ) );
			$term = is_wp_error( $made ) ? null : get_term( (int) $made['term_id'], NWCS_PRODUCT_TAX );
		}

		if ( $term && ! is_wp_error( $term ) ) {
			$term_ids[] = (int) $term->term_id;
		}
	}
	wp_set_object_terms( $id, $term_ids, NWCS_PRODUCT_TAX, false );

	$ids[] = $id;
}

nwcs_pool_flush_cache();

WP_CLI::log( sprintf( 'Havuz: %d yeni, %d guncellendi (%d urun).', $created, $updated, count( $ids ) ) );

// Deneme sitesi varsa urunler orada secili gelsin (sira = magazadaki sira).
$site = get_site_by_path( get_network()->domain, get_network()->path . 'woodkocist/' );

if ( $site && '/' !== $site->path ) {
	switch_to_blog( (int) $site->blog_id );
	nwcs_save_site_product_settings(
		array(
			'mode'      => 'selected',
			'selected'  => $ids,
			'overrides' => nwcs_site_product_settings()['overrides'],
		)
	);
	restore_current_blog();
	WP_CLI::log( sprintf( '/woodkocist/ sitesinde %d urun secildi.', count( $ids ) ) );
}

WP_CLI::success( 'Tamam.' );
