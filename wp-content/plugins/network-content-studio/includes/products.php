<?php
/**
 * Merkezi urun havuzu.
 *
 * Urunler agin ana sitesinde (Ag Yonetimi'nin bagli oldugu site) tek bir yerde
 * tutulur; alt siteler onlari kopyalamaz, referansla gosterir. Gorseller de ayni
 * yerin WordPress medya kitapliginda durur.
 *
 * Her alt site kendi option'inda yalnizca su bilgiyi tutar:
 *   nwcs_products_mode      'all' | 'selected'
 *   nwcs_products_selected  [ urun_id, ... ]   (sira = gosterim sirasi)
 *   nwcs_products_overrides [ urun_id => [ title, short, price, image, hidden ] ]
 *
 * Fiyat kurali: fiyat serbest metindir. Bos birakilirsa site fiyat yerine
 * "Teklif al" gosterir. Kural hem havuz degeri hem site istisnasi icin gecerlidir.
 */

defined( 'ABSPATH' ) || exit;

const NWCS_PRODUCT_TYPE     = 'nwcs_product';
const NWCS_PRODUCT_TAX      = 'nwcs_product_cat';
const NWCS_OPTION_MODE      = 'nwcs_products_mode';
const NWCS_OPTION_SELECTED  = 'nwcs_products_selected';
const NWCS_OPTION_OVERRIDES = 'nwcs_products_overrides';

/**
 * Havuzun bulundugu site (agin ana sitesi).
 */
function nwcs_pool_blog_id(): int {
	return (int) get_main_site_id();
}

add_action( 'init', 'nwcs_register_product_type' );
function nwcs_register_product_type(): void {
	// Kayit her sitede yapilir; kayitlar yalnizca havuz sitesinde bulunur.
	// Boylece switch_to_blog ile havuza gecildiginde sorgu calisir.
	register_post_type(
		NWCS_PRODUCT_TYPE,
		array(
			'labels'          => array(
				'name'          => 'Ürünler',
				'singular_name' => 'Ürün',
			),
			'public'          => false,
			'show_ui'         => false,
			'show_in_rest'    => false,
			'hierarchical'    => false,
			'supports'        => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'has_archive'     => false,
			'rewrite'         => false,
			'capability_type' => 'post',
		)
	);

	register_taxonomy(
		NWCS_PRODUCT_TAX,
		NWCS_PRODUCT_TYPE,
		array(
			'labels'       => array(
				'name'          => 'Ürün Kategorileri',
				'singular_name' => 'Ürün Kategorisi',
			),
			'public'       => false,
			'show_ui'      => false,
			'hierarchical' => true,
			'rewrite'      => false,
		)
	);
}

/* ------------------------------------------------------------------ */
/* Havuz okuma                                                          */
/* ------------------------------------------------------------------ */

/**
 * Havuzdaki tum urunler (siralama: menu_order, sonra baslik).
 *
 * @return array<int, array<string, mixed>>
 */
function nwcs_pool_products(): array {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	switch_to_blog( nwcs_pool_blog_id() );

	$posts = get_posts(
		array(
			'post_type'      => NWCS_PRODUCT_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);

	$products = array();

	foreach ( $posts as $post ) {
		$products[ (int) $post->ID ] = nwcs_pool_product_data( $post );
	}

	restore_current_blog();

	$cache = $products;

	return $products;
}

/**
 * Tek urunun havuzdaki hali. Havuz baglaminda cagrilmalidir.
 */
function nwcs_pool_product_data( WP_Post $post ): array {
	$image_id = (int) get_post_thumbnail_id( $post->ID );
	$image    = nwcs_image_by_id( $image_id, 'medium_large' );

	$categories = array();
	foreach ( wp_get_object_terms( $post->ID, NWCS_PRODUCT_TAX ) as $term ) {
		$categories[ $term->slug ] = $term->name;
	}

	return array(
		'id'         => (int) $post->ID,
		'slug'       => $post->post_name,
		'title'      => $post->post_title,
		'short'      => (string) get_post_meta( $post->ID, '_nwcs_short', true ),
		'body'       => $post->post_content,
		'price'      => (string) get_post_meta( $post->ID, '_nwcs_price', true ),
		'spec'       => (string) get_post_meta( $post->ID, '_nwcs_spec', true ),
		'image_id'   => $image_id,
		'image'      => $image,
		'categories' => $categories,
		'order'      => (int) $post->menu_order,
	);
}

/**
 * Havuzdaki gorselleri (medya kitapligi) listeler.
 */
function nwcs_pool_media(): array {
	switch_to_blog( nwcs_pool_blog_id() );

	$attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'posts_per_page' => 200,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$media = array();

	foreach ( $attachments as $attachment ) {
		$src = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );

		$media[ (int) $attachment->ID ] = array(
			'id'    => (int) $attachment->ID,
			'title' => $attachment->post_title ? $attachment->post_title : ( '#' . $attachment->ID ),
			'thumb' => $src ? $src[0] : '',
			'alt'   => (string) get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		);
	}

	restore_current_blog();

	return $media;
}

/* ------------------------------------------------------------------ */
/* Site ayarlari                                                        */
/* ------------------------------------------------------------------ */

/**
 * Sitenin urun ayarlari.
 *
 * @return array{mode:string, selected:int[], overrides:array<int, array>}
 */
function nwcs_site_product_settings( ?int $blog_id = null ): array {
	$read = static function ( string $option, $default ) use ( $blog_id ) {
		return null === $blog_id
			? get_option( $option, $default )
			: get_blog_option( $blog_id, $option, $default );
	};

	$mode = $read( NWCS_OPTION_MODE, 'all' );

	$selected = $read( NWCS_OPTION_SELECTED, array() );
	$selected = is_array( $selected ) ? array_values( array_map( 'absint', $selected ) ) : array();

	$overrides = $read( NWCS_OPTION_OVERRIDES, array() );
	$overrides = is_array( $overrides ) ? $overrides : array();

	return array(
		'mode'      => in_array( $mode, array( 'all', 'selected' ), true ) ? $mode : 'all',
		'selected'  => $selected,
		'overrides' => $overrides,
	);
}

/**
 * Site ayarlarini yazar. Aktif site baglaminda cagrilir.
 */
function nwcs_save_site_product_settings( array $settings ): void {
	$pool = nwcs_pool_products();

	$mode = in_array( $settings['mode'] ?? 'all', array( 'all', 'selected' ), true ) ? $settings['mode'] : 'all';

	$selected = array();
	foreach ( (array) ( $settings['selected'] ?? array() ) as $id ) {
		$id = absint( $id );
		if ( $id && isset( $pool[ $id ] ) && ! in_array( $id, $selected, true ) ) {
			$selected[] = $id;
		}
	}

	$overrides = array();
	foreach ( (array) ( $settings['overrides'] ?? array() ) as $id => $row ) {
		$id = absint( $id );

		if ( ! $id || ! isset( $pool[ $id ] ) || ! is_array( $row ) ) {
			continue;
		}

		$clean = array(
			'title'  => sanitize_text_field( (string) ( $row['title'] ?? '' ) ),
			'short'  => sanitize_textarea_field( (string) ( $row['short'] ?? '' ) ),
			'price'  => sanitize_text_field( (string) ( $row['price'] ?? '' ) ),
			'image'  => absint( $row['image'] ?? 0 ),
			'hidden' => ! empty( $row['hidden'] ) ? 1 : 0,
			// Fiyatin bilincli olarak bos birakildigini, "hic dokunulmadi"dan
			// ayirt edebilmek icin ayri bir bayrak tutuyoruz.
			'price_override' => ! empty( $row['price_override'] ) ? 1 : 0,
		);

		if ( '' === $clean['title'] && '' === $clean['short'] && '' === $clean['price']
			&& 0 === $clean['image'] && 0 === $clean['hidden'] && 0 === $clean['price_override'] ) {
			continue; // Bos istisna saklanmaz.
		}

		$overrides[ $id ] = $clean;
	}

	update_option( NWCS_OPTION_MODE, $mode );
	update_option( NWCS_OPTION_SELECTED, $selected );
	update_option( NWCS_OPTION_OVERRIDES, $overrides );
}

/* ------------------------------------------------------------------ */
/* Sitede gosterilecek urunler                                          */
/* ------------------------------------------------------------------ */

/**
 * Sitenin gosterecegi urunler: kip + secim sirasi + site istisnalari uygulanmis.
 *
 * Her satir: id, title, short, price, price_label, has_price, spec, image, url
 */
function nwcs_site_products( ?int $blog_id = null ): array {
	$blog_id  = $blog_id ?? get_current_blog_id();
	$pool     = nwcs_pool_products();
	$settings = nwcs_site_product_settings( $blog_id );

	$ids = 'selected' === $settings['mode']
		? $settings['selected']
		: array_keys( $pool );

	$out = array();

	foreach ( $ids as $id ) {
		if ( ! isset( $pool[ $id ] ) ) {
			continue;
		}

		$product  = $pool[ $id ];
		$override = $settings['overrides'][ $id ] ?? array();

		if ( ! empty( $override['hidden'] ) ) {
			continue;
		}

		$title = '' !== ( $override['title'] ?? '' ) ? $override['title'] : $product['title'];
		$short = '' !== ( $override['short'] ?? '' ) ? $override['short'] : $product['short'];

		// Fiyat: istisna isaretliyse (bos olsa bile) istisna kazanir.
		$price = ! empty( $override['price_override'] )
			? (string) ( $override['price'] ?? '' )
			: $product['price'];

		$image = $product['image'];
		if ( ! empty( $override['image'] ) ) {
			switch_to_blog( nwcs_pool_blog_id() );
			$image = nwcs_image_by_id( (int) $override['image'], 'medium_large' );
			restore_current_blog();
		}

		$out[] = array(
			'id'          => $product['id'],
			'slug'        => $product['slug'],
			'title'       => $title,
			'short'       => $short,
			'body'        => $product['body'],
			'spec'        => $product['spec'],
			'price'       => $price,
			'has_price'   => '' !== trim( $price ),
			'price_label' => '' !== trim( $price ) ? $price : 'Teklif al',
			'image'       => $image,
			'url'         => nwcs_product_url( $product['slug'], $blog_id ),
			'categories'  => $product['categories'],
		);
	}

	return $out;
}

/**
 * Urun detay sayfasinin adresi (alt sitede /urun/<slug>/).
 */
function nwcs_product_url( string $slug, ?int $blog_id = null ): string {
	if ( '' === $slug ) {
		return '';
	}

	return get_home_url( $blog_id ?? get_current_blog_id(), '/urun/' . $slug . '/' );
}

/**
 * Slug'a gore tek urun (site istisnalari uygulanmis).
 */
function nwcs_site_product_by_slug( string $slug, ?int $blog_id = null ): ?array {
	foreach ( nwcs_site_products( $blog_id ) as $product ) {
		if ( $product['slug'] === $slug ) {
			return $product;
		}
	}

	return null;
}

/* ------------------------------------------------------------------ */
/* Detay sayfasi yonlendirmesi                                          */
/* ------------------------------------------------------------------ */

add_action( 'init', 'nwcs_product_rewrite' );
function nwcs_product_rewrite(): void {
	add_rewrite_rule( '^urun/([^/]+)/?$', 'index.php?nwcs_product=$matches[1]', 'top' );
}

add_filter( 'query_vars', 'nwcs_product_query_var' );
function nwcs_product_query_var( array $vars ): array {
	$vars[] = 'nwcs_product';

	return $vars;
}

add_action( 'template_redirect', 'nwcs_product_template', 5 );
function nwcs_product_template(): void {
	$slug = get_query_var( 'nwcs_product' );

	if ( ! $slug ) {
		return;
	}

	$product = nwcs_site_product_by_slug( sanitize_title( (string) $slug ) );

	// Bu sitede gosterilmeyen ya da detay metni girilmemis urunun sayfasi yoktur.
	// Aksi halde /urun/<slug>/ adresleri bos sayfa ya da ana sayfa dondururdu.
	if ( ! $product || '' === trim( (string) $product['body'] ) ) {
		global $wp_query;

		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();

		// Tema 404.php vermiyorsa index.php'ye dus.
		$not_found = get_query_template( '404' );

		if ( ! $not_found ) {
			$not_found = get_index_template();
		}

		if ( $not_found ) {
			include $not_found;
		}

		exit;
	}

	status_header( 200 );

	// Tema kendi detay sablonunu verebilir; yoksa eklentinin sadesi kullanilir.
	$template = locate_template( 'template-parts/product-detail.php' );

	if ( ! $template ) {
		$template = NWCS_DIR . 'templates/product-detail.php';
	}

	// $product sablonda kullanilir.
	include $template;
	exit;
}
