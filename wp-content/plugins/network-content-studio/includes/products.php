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

// Anahtar surumlu: urun verisinin sekli degistiginde eski onbellek kendiliginden
// gecersiz olur, elle temizlemek gerekmez.
const NWCS_POOL_CACHE_KEY = 'nwcs_pool_products_v2';

/**
 * Havuz onbellegini temizler. Urun/gorsel degisikliklerinden sonra cagrilir.
 */
function nwcs_pool_flush_cache(): void {
	switch_to_blog( nwcs_pool_blog_id() );
	delete_transient( NWCS_POOL_CACHE_KEY );
	restore_current_blog();

	nwcs_pool_products( true );
}

/**
 * Havuzdaki tum urunler (siralama: menu_order, sonra baslik).
 *
 * Her sayfa ciziminde havuza gecmemek icin sonuc hem istek ici degiskende hem
 * de transient'ta tutulur; yazma islemleri onbellegi temizler.
 *
 * @return array<int, array<string, mixed>>
 */
function nwcs_pool_products( bool $reset = false ): array {
	static $cache = null;

	if ( $reset ) {
		$cache = null;

		return array();
	}

	if ( null !== $cache ) {
		return $cache;
	}

	switch_to_blog( nwcs_pool_blog_id() );

	$stored = get_transient( NWCS_POOL_CACHE_KEY );

	if ( is_array( $stored ) ) {
		restore_current_blog();
		$cache = $stored;

		return $cache;
	}

	$posts = get_posts(
		array(
			'post_type'      => NWCS_PRODUCT_TYPE,
			'post_status'    => 'publish',
			// Sinirsiz: sinir asilinca fazlasi hicbir sitede gorunmuyor, secimlerden de dusuyordu.
			'posts_per_page' => -1,
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

	set_transient( NWCS_POOL_CACHE_KEY, $products, HOUR_IN_SECONDS );

	restore_current_blog();

	$cache = $products;

	return $products;
}

/**
 * Yonetim listesi icin filtrelenmis/sayfalanmis sorgu.
 *
 * @return array{items: array<int, array>, total: int, pages: int, page: int}
 */
function nwcs_pool_query( array $args = array() ): array {
	$search   = trim( (string) ( $args['search'] ?? '' ) );
	$category = trim( (string) ( $args['category'] ?? '' ) );
	$page     = max( 1, (int) ( $args['page'] ?? 1 ) );
	$per_page = max( 1, (int) ( $args['per_page'] ?? 20 ) );

	$all = nwcs_pool_products();

	$filtered = array_filter(
		$all,
		static function ( array $product ) use ( $search, $category ): bool {
			if ( '' !== $category && ! isset( $product['categories'][ $category ] ) ) {
				return false;
			}

			if ( '' === $search ) {
				return true;
			}

			$haystack = mb_strtolower( $product['title'] . ' ' . $product['short'] . ' ' . $product['spec'] . ' ' . implode( ' ', $product['categories'] ) );

			return str_contains( $haystack, mb_strtolower( $search ) );
		}
	);

	$total = count( $filtered );
	$pages = max( 1, (int) ceil( $total / $per_page ) );
	$page  = min( $page, $pages );

	return array(
		'items' => array_slice( $filtered, ( $page - 1 ) * $per_page, $per_page, true ),
		'total' => $total,
		'pages' => $pages,
		'page'  => $page,
	);
}

/**
 * Havuzdaki kategoriler (slug => [name, count]).
 */
function nwcs_pool_categories(): array {
	switch_to_blog( nwcs_pool_blog_id() );

	$terms = get_terms(
		array(
			'taxonomy'   => NWCS_PRODUCT_TAX,
			'hide_empty' => false,
			'orderby'    => 'name',
		)
	);

	restore_current_blog();

	$out = array();

	if ( is_wp_error( $terms ) ) {
		return $out;
	}

	foreach ( $terms as $term ) {
		$out[ $term->slug ] = array(
			'id'    => (int) $term->term_id,
			'name'  => $term->name,
			'count' => (int) $term->count,
		);
	}

	return $out;
}

/**
 * Tek urunun havuzdaki hali. Havuz baglaminda cagrilmalidir.
 */
function nwcs_pool_product_data( WP_Post $post ): array {
	// Galeri: sirali ek kimlikleri. Ilki kart gorseli olarak kullanilir.
	$gallery = get_post_meta( $post->ID, '_nwcs_gallery', true );
	$gallery = is_array( $gallery ) ? array_values( array_filter( array_map( 'absint', $gallery ) ) ) : array();

	// Eski kayitlarla uyum: galeri bossa one cikan gorsel kullanilir.
	if ( ! $gallery ) {
		$thumb = (int) get_post_thumbnail_id( $post->ID );

		if ( $thumb ) {
			$gallery = array( $thumb );
		}
	}

	$images = array();
	foreach ( $gallery as $attachment_id ) {
		$images[] = nwcs_image_by_id( $attachment_id, 'medium_large' );
	}

	$categories = array();
	foreach ( wp_get_object_terms( $post->ID, NWCS_PRODUCT_TAX ) as $term ) {
		$categories[ $term->slug ] = $term->name;
	}

	return array(
		'id'          => (int) $post->ID,
		'slug'        => $post->post_name,
		'code'        => (string) get_post_meta( $post->ID, '_nwcs_code', true ),
		'title'       => $post->post_title,
		'short'       => (string) get_post_meta( $post->ID, '_nwcs_short', true ),
		'body'        => $post->post_content,
		'price'       => (string) get_post_meta( $post->ID, '_nwcs_price', true ),
		'spec'        => (string) get_post_meta( $post->ID, '_nwcs_spec', true ),
		'image_id'    => $gallery ? (int) $gallery[0] : 0,
		'image'       => $images ? $images[0] : nwcs_image_by_id( 0 ),
		'gallery_ids' => $gallery,
		'images'      => $images,
		'categories'  => $categories,
		'tables'      => nwcs_sanitize_product_tables( (array) get_post_meta( $post->ID, '_nwcs_tables', true ) ),
		'order'       => (int) $post->menu_order,
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
/**
 * Sitenin temasi havuz urunlerini basabiliyor mu?
 *
 * Manifestte 'products' turunde bir alan yoksa o sitede urunleri gosterecek
 * hicbir yer yoktur; havuz ozetlerinde "gorunur" saymak yaniltici olur.
 */
/**
 * Urun kodunu bicime sokar: buyuk harf, rakam ve tire.
 */
function nwcs_normalize_product_code( string $code ): string {
	$code = strtoupper( trim( $code ) );
	$code = preg_replace( '/[^A-Z0-9\-_.]/', '-', $code ) ?? '';
	$code = preg_replace( '/-+/', '-', $code ) ?? '';

	return trim( $code, '-' );
}

/**
 * Bu kod baska bir urunde kullaniliyor mu? Kullaniliyorsa o urunun kimligi.
 * Havuz sitesi baglaminda cagrilmalidir.
 */
function nwcs_product_id_by_code( string $code, int $ignore_id = 0 ): int {
	$code = nwcs_normalize_product_code( $code );

	if ( '' === $code ) {
		return 0;
	}

	$found = get_posts(
		array(
			'post_type'      => NWCS_PRODUCT_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'exclude'        => $ignore_id ? array( $ignore_id ) : array(),
			'meta_key'       => '_nwcs_code', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $code, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);

	return $found ? (int) $found[0] : 0;
}

/**
 * Urunun kodunu dondurur; yoksa kimliginden bir tane uretip kaydeder.
 * Her urunun kendine ait, degismeyen bir kodu olsun diye.
 */
function nwcs_ensure_product_code( int $id ): string {
	$code = nwcs_normalize_product_code( (string) get_post_meta( $id, '_nwcs_code', true ) );

	if ( '' !== $code ) {
		return $code;
	}

	$code = 'URN-' . str_pad( (string) $id, 4, '0', STR_PAD_LEFT );
	update_post_meta( $id, '_nwcs_code', $code );

	return $code;
}

function nwcs_site_supports_products( int $blog_id ): bool {
	static $cache = array();

	if ( isset( $cache[ $blog_id ] ) ) {
		return $cache[ $blog_id ];
	}

	$supports = false;
	$manifest = nwcs_manifest_for_blog( $blog_id );

	foreach ( $manifest['pages'] ?? array() as $page ) {
		foreach ( $page['components'] ?? array() as $component ) {
			foreach ( $component['fields'] ?? array() as $field ) {
				if ( 'products' === ( $field['type'] ?? '' ) ) {
					$supports = true;
					break 3;
				}
			}
		}
	}

	$cache[ $blog_id ] = $supports;

	return $supports;
}

function nwcs_site_product_settings( ?int $blog_id = null ): array {
	$read = static function ( string $option, $default ) use ( $blog_id ) {
		return null === $blog_id
			? get_option( $option, $default )
			: get_blog_option( $blog_id, $option, $default );
	};

	// Kayit yoksa 'selected' kabul edilir: hic secim yapilmamis bir site,
	// kimse istemeden havuzdaki her urunu gostermeye baslamasin.
	$mode = $read( NWCS_OPTION_MODE, 'selected' );

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

/**
 * Havuzda bu kategoride (adiyla) olan urunlerin kimlikleri, havuz sirasiyla.
 *
 * @return int[]
 */
function nwcs_pool_ids_in_category( string $category ): array {
	$ids = array();

	foreach ( nwcs_pool_products() as $id => $product ) {
		if ( in_array( $category, (array) $product['categories'], true ) ) {
			$ids[] = (int) $id;
		}
	}

	return $ids;
}

/**
 * Tek kategorinin urun listesi kaydedilirken (panelde kategori sayfasi)
 * sitenin geri kalan secimi korunur: diger urunlerin secimi, sirasi ve
 * istisnalari degismez; kategorinin urunleri listedeki yerlerinde yeni
 * sirayla durur, yeni isaretlenenler sona eklenir.
 *
 * "Hepsi" kipindeki site, kategori listesi olduğu gibi kaydedilirse o kipte
 * kalir; bir urun cikarilir ya da sira degisirse "Secilenler" kipine gecer
 * (havuzun geri kalani secili kalir).
 *
 * @param array  $current nwcs_site_product_settings()
 * @param array  $posted  nwcs_posted_products()
 * @return array Kaydedilecek ayarlar (nwcs_save_site_product_settings'e).
 */
function nwcs_merge_scoped_products( array $current, array $posted, string $category ): array {
	$scope  = nwcs_pool_ids_in_category( $category );
	$picked = array();

	foreach ( (array) ( $posted['selected'] ?? array() ) as $id ) {
		$id = absint( $id );

		if ( in_array( $id, $scope, true ) && ! in_array( $id, $picked, true ) ) {
			$picked[] = $id;
		}
	}

	$base = 'all' === $current['mode'] ? array_map( 'intval', array_keys( nwcs_pool_products() ) ) : $current['selected'];

	// Kategorinin mevcut sirasi (tabandaki yerleriyle).
	$before = array_values( array_filter( $base, static fn( int $id ): bool => in_array( $id, $scope, true ) ) );
	$mode   = ( 'all' === $current['mode'] && $picked === $before ) ? 'all' : 'selected';

	$queue    = $picked;
	$selected = array();

	foreach ( $base as $id ) {
		if ( ! in_array( $id, $scope, true ) ) {
			$selected[] = $id;
		} elseif ( $queue ) {
			$selected[] = array_shift( $queue );
		}
	}

	$selected = array_merge( $selected, $queue );

	// Istisnalar: kategori disindakiler aynen, kategoridekiler formdan.
	$overrides = array();

	foreach ( $current['overrides'] as $id => $row ) {
		if ( ! in_array( (int) $id, $scope, true ) ) {
			$overrides[ $id ] = $row;
		}
	}

	foreach ( (array) ( $posted['overrides'] ?? array() ) as $id => $row ) {
		if ( in_array( absint( $id ), $scope, true ) ) {
			$overrides[ absint( $id ) ] = $row;
		}
	}

	return array(
		'mode'      => $mode,
		'selected'  => 'all' === $mode ? $current['selected'] : $selected,
		'overrides' => $overrides,
	);
}

/* ------------------------------------------------------------------ */
/* Sitede gosterilecek urunler                                          */
/* ------------------------------------------------------------------ */

/**
 * Sitenin temasi urun tablolarini gosteriyor mu (manifestte 'product_tables').
 * Urun Havuzu'ndaki "Ürün Tabloları" bolumu yalnizca bu sitelerde gorunen
 * urunler icin acilir.
 */
function nwcs_site_supports_product_tables( int $blog_id ): bool {
	return ! empty( nwcs_manifest_for_blog( $blog_id )['product_tables'] );
}

/**
 * Urun tablolarini temizler (kayit ve okuma icin ayni kural).
 *
 * Her tablo: title, head (sutun basliklari), rows (hucre dizileri). Tamamen
 * bos satirlar ve basligi da hucreleri de bos sutunlar atilir; hicbir
 * icerigi kalmayan tablo dusurulur. Sinirlar asiri buyuk girisi keser.
 *
 * @return array<int, array{title:string, head:string[], rows:array<int, string[]>}>
 */
function nwcs_sanitize_product_tables( array $raw ): array {
	$clean = static fn( $cell ): string => is_scalar( $cell ) ? trim( sanitize_text_field( (string) $cell ) ) : '';
	$out   = array();

	foreach ( array_slice( $raw, 0, 20 ) as $table ) {
		if ( ! is_array( $table ) ) {
			continue;
		}

		$title = $clean( $table['title'] ?? '' );
		$head  = array_map( $clean, array_slice( array_values( (array) ( $table['head'] ?? array() ) ), 0, 30 ) );
		$rows  = array();

		foreach ( array_slice( (array) ( $table['rows'] ?? array() ), 0, 500 ) as $row ) {
			$cells = array_map( $clean, array_slice( array_values( (array) $row ), 0, 30 ) );

			if ( '' !== implode( '', $cells ) ) {
				$rows[] = $cells;
			}
		}

		$cols = max( array_merge( array( count( $head ) ), array_map( 'count', $rows ) ) );
		$head = array_pad( $head, $cols, '' );
		$rows = array_map( static fn( array $cells ): array => array_pad( $cells, $cols, '' ), $rows );

		// Bos sutunlari sagdan sola atar (indeksler kaymasin).
		for ( $col = $cols - 1; $col >= 0; $col-- ) {
			$used = '' !== $head[ $col ];

			foreach ( $rows as $cells ) {
				$used = $used || '' !== $cells[ $col ];
			}

			if ( ! $used ) {
				array_splice( $head, $col, 1 );

				foreach ( $rows as &$cells ) {
					array_splice( $cells, $col, 1 );
				}
				unset( $cells );
			}
		}

		if ( '' === $title && ! $rows && '' === implode( '', $head ) ) {
			continue;
		}

		$out[] = array(
			'title' => $title,
			'head'  => $head,
			'rows'  => $rows,
		);
	}

	return $out;
}

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

		// Site istisnasi varsa o gorsel one gecer; galerinin kalani korunur.
		$images = $product['images'] ?? array();

		if ( ! empty( $override['image'] ) ) {
			switch_to_blog( nwcs_pool_blog_id() );
			$override_image = nwcs_image_by_id( (int) $override['image'], 'medium_large' );
			restore_current_blog();

			$images = array_merge(
				array( $override_image ),
				array_values(
					array_filter(
						$product['images'] ?? array(),
						static fn( array $item ): bool => (int) $item["id"] !== (int) $override["image"]
					)
				)
			);
		}

		$image = $images ? $images[0] : nwcs_image_by_id( 0 );

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
			'images'      => $images,
			'url'         => nwcs_product_url( $product['slug'], $blog_id ),
			'categories'  => $product['categories'],
			'tables'      => $product['tables'] ?? array(),
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

		// Eski adresler icin yonlendirme listesi (ornegin kaldirilan urun):
		// liste yalnizca bulunamayan sayfada calisir, burada ilk kez 404 oldu.
		// Kural varsa yonlendirip cikar ya da durumu 410 yapar.
		if ( function_exists( 'nwcs_redirects_apply' ) ) {
			nwcs_redirects_apply();
		}

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

/**
 * Aga yeni bir site eklendiginde urun ayarlarini acikca yazar; boylece
 * "kayit yok" hali hic olusmaz ve site sahibi ne sectigini bilerek baslar.
 */
add_action( 'wp_initialize_site', 'nwcs_seed_new_site_product_settings', 20, 1 );
function nwcs_seed_new_site_product_settings( $site ): void {
	$blog_id = (int) ( is_object( $site ) ? $site->blog_id : $site );

	if ( ! $blog_id ) {
		return;
	}

	add_blog_option( $blog_id, NWCS_OPTION_MODE, 'selected' );
	add_blog_option( $blog_id, NWCS_OPTION_SELECTED, array() );
	add_blog_option( $blog_id, NWCS_OPTION_OVERRIDES, array() );
}
