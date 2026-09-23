<?php
/**
 * SEO ve GEO: sitenin arama motorlarina ve yapay zeka aramalarina verdigi
 * bilgi.
 *
 * Temalar bu konuda hicbir sey yapmaz; eklenti manifestli her sitede sunlari
 * uretir:
 *   - belge basligi, meta aciklama, paylasim onizlemesi (Open Graph, X)
 *   - yapilandirilmis veri (JSON-LD): firma, web sitesi, sayfa, konum yolu,
 *     urun ve blog yazisi
 *   - /llms.txt: yapay zeka motorlari icin sitenin duz metin ozeti
 *   - arsiv sayfalarinda noindex, site haritasindan kullanici listesi cikarilir
 *
 * Her deger once panelde girilen ozel degerden, yoksa sayfanin kendi
 * iceriginden gelir. Site sahibi hicbir sey girmese de her sayfa baslik,
 * aciklama ve gorselle yayina cikar.
 */

defined( 'ABSPATH' ) || exit;

/* ====================================================================== *
 * Yardimcilar
 * ====================================================================== */

/**
 * Metni tek satira indirir; istenirse kelime sinirinda kisaltir.
 */
function nwcs_seo_clean( $text, int $limit = 0 ): string {
	$text = trim( (string) preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $text ) ) );

	if ( $limit > 0 && mb_strlen( $text ) > $limit ) {
		$cut  = mb_substr( $text, 0, $limit - 1 );
		$cut  = preg_replace( '/\s+\S*$/u', '', $cut );
		$text = rtrim( (string) $cut, " ,.;:–-" ) . '…';
	}

	return $text;
}

/**
 * "bilesen.alan" yolundan aktif sitenin degerini okur.
 */
function nwcs_seo_path_value( string $page_key, string $path ) {
	$parts = explode( '.', $path, 2 );

	if ( 2 !== count( $parts ) ) {
		return '';
	}

	return nwcs_field( $page_key, $parts[0], $parts[1] );
}

/**
 * Deger bir gorsel kimligi ya da gorselli tekrarli satirlarsa ilk gorsel.
 */
function nwcs_seo_first_image( $value ): int {
	if ( is_array( $value ) ) {
		foreach ( $value as $row ) {
			foreach ( (array) $row as $cell ) {
				if ( is_numeric( $cell ) && (int) $cell > 0 && wp_attachment_is_image( (int) $cell ) ) {
					return (int) $cell;
				}
			}
		}

		return 0;
	}

	return is_numeric( $value ) ? (int) $value : 0;
}

/**
 * Gorsel kimliginden paylasim icin boyutlu bilgi.
 */
function nwcs_seo_image( int $attachment_id ): array {
	if ( $attachment_id <= 0 ) {
		return array();
	}

	$src = wp_get_attachment_image_src( $attachment_id, 'large' );

	if ( ! $src ) {
		return array();
	}

	return array(
		'id'     => $attachment_id,
		'url'    => $src[0],
		'width'  => (int) $src[1],
		'height' => (int) $src[2],
		'alt'    => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
	);
}

/**
 * Manifestteki, kendi adresi olan sayfalar (ortak ve gizli sayfalar haric).
 */
function nwcs_seo_pages( ?array $manifest = null ): array {
	$manifest = $manifest ?? nwcs_manifest();
	$pages    = array();

	foreach ( nwcs_visible_pages( $manifest ) as $key => $page ) {
		if ( 'global' !== $key && isset( $page['path'] ) ) {
			$pages[ $key ] = $page;
		}
	}

	return $pages;
}

/**
 * Site adi: firma bilgisindeki kisa ad, yoksa WordPress site basligi.
 */
function nwcs_seo_site_name(): string {
	$name = nwcs_seo_clean( nwcs_field( NWCS_SEO_SITE_PAGE, 'org', 'name' ) );

	return '' !== $name ? $name : nwcs_seo_clean( get_bloginfo( 'name' ) );
}

/* ====================================================================== *
 * Sayfanin otomatik degerleri
 * ====================================================================== */

/**
 * Bir sayfanin icerikten turetilen adi, aciklamasi ve gorseli.
 *
 * Tema manifestte 'seo_source' verdiyse oradan; vermediyse bilesenlerde
 * yaygin alan adlari aranir (baslik: title/name, aciklama: lead/short/...).
 *
 * @return array{name:string, description:string, image:int}
 */
function nwcs_seo_page_auto( string $page_key, ?array $manifest = null ): array {
	$manifest   = $manifest ?? nwcs_manifest();
	$page       = $manifest['pages'][ $page_key ] ?? array();
	$source     = (array) ( $page['seo_source'] ?? array() );
	$components = array_diff_key( (array) ( $page['components'] ?? array() ), array( NWCS_SEO_COMPONENT => true ) );

	// Belirtilen alan adlarindan, bilesen sirasiyla ilk dolu metin.
	$find_text = static function ( array $names ) use ( $components, $page_key ): string {
		foreach ( $names as $name ) {
			foreach ( $components as $component_key => $component ) {
				$type = $component['fields'][ $name ]['type'] ?? '';

				if ( 'text' === $type || 'textarea' === $type ) {
					$value = nwcs_seo_clean( nwcs_field( $page_key, $component_key, $name ) );

					if ( '' !== $value ) {
						return $value;
					}
				}
			}
		}

		return '';
	};

	$find_image = static function () use ( $components, $page_key ): int {
		foreach ( $components as $component_key => $component ) {
			foreach ( $component['fields'] as $name => $definition ) {
				$type = $definition['type'] ?? '';

				if ( 'image' !== $type && 'repeater' !== $type ) {
					continue;
				}

				$id = nwcs_seo_first_image( nwcs_field( $page_key, $component_key, $name ) );

				if ( $id ) {
					return $id;
				}
			}
		}

		return 0;
	};

	$name = isset( $source['title'] )
		? nwcs_seo_clean( nwcs_seo_path_value( $page_key, $source['title'] ) )
		: $find_text( array( 'title', 'name' ) );

	$description = isset( $source['description'] )
		? nwcs_seo_clean( nwcs_seo_path_value( $page_key, $source['description'] ) )
		: $find_text( array( 'lead', 'short', 'intro', 'text', 'tagline', 'note' ) );

	$image = isset( $source['image'] )
		? nwcs_seo_first_image( nwcs_seo_path_value( $page_key, $source['image'] ) )
		: $find_image();

	if ( '' === $name ) {
		$name = nwcs_seo_clean( preg_replace( '/^[^:]+:\s*/u', '', (string) ( $page['label'] ?? $page_key ) ) );
	}

	return array(
		'name'        => $name,
		'description' => nwcs_seo_clean( $description, 160 ),
		'image'       => $image,
	);
}

/**
 * Sayfanin arama sonucunda gorunecek hali: ozel deger varsa o, yoksa
 * otomatik. Panel onizlemesi ve on yuz ayni fonksiyonu kullanir.
 *
 * @return array{name:string, title:string, description:string, image:int, custom:array}
 */
function nwcs_seo_page_resolved( string $page_key, ?array $manifest = null ): array {
	$auto   = nwcs_seo_page_auto( $page_key, $manifest );
	$site   = nwcs_seo_site_name();
	$custom = array(
		'title'       => nwcs_seo_clean( nwcs_field( $page_key, NWCS_SEO_COMPONENT, 'title' ) ),
		'description' => nwcs_seo_clean( nwcs_field( $page_key, NWCS_SEO_COMPONENT, 'description' ) ),
		'image'       => (int) nwcs_field( $page_key, NWCS_SEO_COMPONENT, 'image', 0 ),
	);

	// Ana sayfada marka once gelir; ic sayfalarda sayfa adi.
	$auto_title = 'home' === $page_key
		? $site . ' – ' . $auto['name']
		: $auto['name'] . ' – ' . $site;

	if ( $auto['name'] === $site ) {
		$auto_title = $site;
	}

	return array(
		'name'        => $auto['name'],
		'title'       => '' !== $custom['title'] ? $custom['title'] : $auto_title,
		'description' => '' !== $custom['description'] ? $custom['description'] : $auto['description'],
		'image'       => $custom['image'] ? $custom['image'] : $auto['image'],
		'auto'        => array( 'title' => $auto_title ) + $auto,
		'custom'      => $custom,
	);
}

/* ====================================================================== *
 * Istegin baglami
 * ====================================================================== */

/**
 * Istek adresinin yolu (sorgu ve capa olmadan), sonda egik cizgi yok.
 */
function nwcs_seo_request_path(): string {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

	return untrailingslashit( (string) wp_parse_url( $uri, PHP_URL_PATH ) );
}

/**
 * Adresi verilen yola denk gelen manifest sayfasi.
 */
function nwcs_seo_page_for_path( string $path ): string {
	foreach ( nwcs_seo_pages() as $key => $page ) {
		if ( untrailingslashit( (string) wp_parse_url( home_url( $page['path'] ), PHP_URL_PATH ) ) === $path ) {
			return $key;
		}
	}

	return '';
}

/**
 * Bu istek icin SEO bilgisi. Manifest sayfasi ya da blog yazisi degilse bos;
 * o zaman WordPress'in varsayilanlari gecerli kalir.
 */
function nwcs_seo_context(): array {
	static $context = null;

	if ( null !== $context ) {
		return $context;
	}

	$context = array();

	if ( is_admin() || is_404() || empty( nwcs_manifest()['pages'] ) ) {
		return $context;
	}

	$site = nwcs_seo_site_name();

	if ( is_singular( 'post' ) ) {
		$post    = get_queried_object();
		$name    = nwcs_seo_clean( get_the_title( $post ) );
		$excerpt = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;

		$context = array(
			'kind'        => 'post',
			'page_key'    => '',
			'post'        => $post,
			'name'        => $name,
			'title'       => $name . ' – ' . $site,
			'description' => nwcs_seo_clean( $excerpt, 160 ),
			'image'       => (int) get_post_thumbnail_id( $post ),
			'url'         => (string) get_permalink( $post ),
		);
	} else {
		$page_key = nwcs_seo_page_for_path( nwcs_seo_request_path() );

		if ( '' === $page_key ) {
			return $context;
		}

		$resolved = nwcs_seo_page_resolved( $page_key );
		$page     = nwcs_manifest()['pages'][ $page_key ];

		$context = array(
			'kind'        => 'page',
			'page_key'    => $page_key,
			'post'        => null,
			'name'        => $resolved['name'],
			'title'       => $resolved['title'],
			'description' => $resolved['description'],
			'image'       => $resolved['image'],
			'url'         => home_url( $page['path'] ),
		);
	}

	// Gorsel yoksa: site geneli paylasim gorseli, o da yoksa logo.
	if ( ! $context['image'] ) {
		$context['image'] = (int) nwcs_field( NWCS_SEO_SITE_PAGE, 'defaults', 'share_image', 0 )
			?: nwcs_seo_logo_id();
	}

	$context['image'] = nwcs_seo_image( (int) $context['image'] );

	return $context;
}

/**
 * Firma logosu: SEO firma bilgisindeki, yoksa temanin ust menu logosu.
 */
function nwcs_seo_logo_id(): int {
	$logo = (int) nwcs_field( NWCS_SEO_SITE_PAGE, 'org', 'logo', 0 );

	if ( ! $logo && null !== nwcs_field_def( nwcs_manifest(), 'global', 'header', 'logo_image' ) ) {
		$logo = (int) nwcs_field( 'global', 'header', 'logo_image', 0 );
	}

	return $logo;
}

/* ====================================================================== *
 * Belge basligi, meta etiketleri
 * ====================================================================== */

add_filter( 'pre_get_document_title', 'nwcs_seo_document_title', 20 );
function nwcs_seo_document_title( $title ) {
	$context = nwcs_seo_context();

	return $context['title'] ?? $title;
}

add_action( 'wp_head', 'nwcs_seo_head', 1 );
function nwcs_seo_head(): void {
	$context = nwcs_seo_context();

	if ( ! $context ) {
		return;
	}

	$is_post = 'post' === $context['kind'];
	$image   = $context['image'];
	$meta    = array();

	if ( '' !== $context['description'] ) {
		$meta[] = array( 'name', 'description', $context['description'] );
	}

	$meta[] = array( 'property', 'og:locale', get_locale() );
	$meta[] = array( 'property', 'og:type', $is_post ? 'article' : 'website' );
	$meta[] = array( 'property', 'og:site_name', nwcs_seo_site_name() );
	$meta[] = array( 'property', 'og:title', $context['title'] );

	if ( '' !== $context['description'] ) {
		$meta[] = array( 'property', 'og:description', $context['description'] );
	}

	$meta[] = array( 'property', 'og:url', $context['url'] );

	if ( $image ) {
		$meta[] = array( 'property', 'og:image', $image['url'] );
		$meta[] = array( 'property', 'og:image:width', (string) $image['width'] );
		$meta[] = array( 'property', 'og:image:height', (string) $image['height'] );

		if ( '' !== $image['alt'] ) {
			$meta[] = array( 'property', 'og:image:alt', $image['alt'] );
		}
	}

	if ( $is_post ) {
		$meta[] = array( 'property', 'article:published_time', get_the_date( 'c', $context['post'] ) );
		$meta[] = array( 'property', 'article:modified_time', get_the_modified_date( 'c', $context['post'] ) );
	}

	$meta[] = array( 'name', 'twitter:card', $image ? 'summary_large_image' : 'summary' );

	echo "\n<!-- Network Content Studio: SEO ve GEO -->\n";

	foreach ( $meta as $tag ) {
		printf( '<meta %s="%s" content="%s" />' . "\n", esc_attr( $tag[0] ), esc_attr( $tag[1] ), esc_attr( $tag[2] ) );
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( nwcs_seo_graph( $context ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG )
	);
}

/* ====================================================================== *
 * Yapilandirilmis veri (JSON-LD)
 * ====================================================================== */

/**
 * Firma bilgisi: panelde girilen degerler; bos olanlar yazilmaz.
 */
function nwcs_seo_org_data(): array {
	$keys = array( 'name', 'legal_name', 'description', 'phone', 'email', 'street', 'district', 'city', 'postal_code', 'country', 'latitude', 'longitude' );
	$org  = array();

	foreach ( $keys as $key ) {
		$org[ $key ] = nwcs_seo_clean( nwcs_field( NWCS_SEO_SITE_PAGE, 'org', $key ) );
	}

	$org['name'] = nwcs_seo_site_name();

	$org['same_as'] = array_values(
		array_filter(
			array_map(
				static fn( $row ): string => esc_url_raw( (string) ( $row['url'] ?? '' ) ),
				nwcs_rows( NWCS_SEO_SITE_PAGE, 'org', 'same_as' )
			)
		)
	);

	return $org;
}

/**
 * Sayfanin @graph dizisi.
 */
function nwcs_seo_graph( array $context ): array {
	$home     = home_url( '/' );
	$org      = nwcs_seo_org_data();
	$org_id   = $home . '#organization';
	$site_id  = $home . '#website';
	$page_id  = $context['url'] . '#webpage';
	$language = str_replace( '_', '-', get_locale() );

	// Adresi ve telefonu olan firma yerel isletmedir; Haritalar eslesmesi icin.
	$has_address = '' !== $org['street'] || '' !== $org['city'];
	$business    = array_filter(
		array(
			'@type'       => $has_address && '' !== $org['phone'] ? 'LocalBusiness' : 'Organization',
			'@id'         => $org_id,
			'name'        => $org['name'],
			'legalName'   => $org['legal_name'],
			'description' => $org['description'],
			'url'         => $home,
			'telephone'   => $org['phone'],
			'email'       => $org['email'],
			'sameAs'      => $org['same_as'],
		)
	);

	$logo = nwcs_seo_image( nwcs_seo_logo_id() );

	if ( $logo ) {
		$business['logo'] = array(
			'@type'  => 'ImageObject',
			'url'    => $logo['url'],
			'width'  => $logo['width'],
			'height' => $logo['height'],
		);
	}

	if ( $has_address ) {
		$business['address'] = array_filter(
			array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $org['street'],
				'addressLocality' => $org['district'],
				'addressRegion'   => $org['city'],
				'postalCode'      => $org['postal_code'],
				'addressCountry'  => $org['country'],
			)
		);
	}

	if ( is_numeric( str_replace( ',', '.', $org['latitude'] ) ) && is_numeric( str_replace( ',', '.', $org['longitude'] ) ) ) {
		$business['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) str_replace( ',', '.', $org['latitude'] ),
			'longitude' => (float) str_replace( ',', '.', $org['longitude'] ),
		);
	}

	$graph = array(
		$business,
		array(
			'@type'      => 'WebSite',
			'@id'        => $site_id,
			'url'        => $home,
			'name'       => $org['name'],
			'inLanguage' => $language,
			'publisher'  => array( '@id' => $org_id ),
		),
	);

	$webpage = array_filter(
		array(
			'@type'       => 'WebPage',
			'@id'         => $page_id,
			'url'         => $context['url'],
			'name'        => $context['title'],
			'description' => $context['description'],
			'inLanguage'  => $language,
			'isPartOf'    => array( '@id' => $site_id ),
			'about'       => array( '@id' => $org_id ),
		)
	);

	if ( $context['image'] ) {
		$webpage['primaryImageOfPage'] = array( '@type' => 'ImageObject', 'url' => $context['image']['url'] );
	}

	$crumbs = nwcs_seo_breadcrumbs( $context );

	if ( count( $crumbs ) > 1 ) {
		$webpage['breadcrumb'] = array( '@id' => $context['url'] . '#breadcrumb' );

		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $context['url'] . '#breadcrumb',
			'itemListElement' => array_map(
				static fn( array $crumb, int $index ): array => array(
					'@type'    => 'ListItem',
					'position' => $index + 1,
					'name'     => $crumb['name'],
					'item'     => $crumb['url'],
				),
				$crumbs,
				array_keys( $crumbs )
			),
		);
	}

	$graph[] = $webpage;

	if ( 'post' === $context['kind'] ) {
		$graph[] = nwcs_seo_article( $context, $org_id, $page_id, $language );
	} elseif ( 'Product' === ( nwcs_manifest()['pages'][ $context['page_key'] ]['seo_source']['type'] ?? '' ) ) {
		$graph[] = nwcs_seo_product( $context, $org_id, $page_id );
	}

	return array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);
}

/**
 * Konum yolu: Anasayfa > ust sayfalar > bu sayfa.
 *
 * @return array<int, array{name:string, url:string}>
 */
function nwcs_seo_breadcrumbs( array $context ): array {
	$crumbs = array(
		array(
			'name' => 'Anasayfa',
			'url'  => home_url( '/' ),
		),
	);

	if ( 'page' === $context['kind'] && 'home' === $context['page_key'] ) {
		return $crumbs;
	}

	$path_of = static fn( string $url ): string => untrailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) );
	$home    = $path_of( home_url( '/' ) );

	// Sayfanin ustleri adres yolundan bulunur (/urunlerimiz/euro-palet/ ->
	// /urunlerimiz/). Blog yazisinin adresi kokte oldugu icin ustu, blog
	// listesi sayfasi ve onun ustleridir.
	$anchor    = $path_of( $context['url'] );
	$inclusive = false;

	if ( 'post' === $context['kind'] && get_option( 'page_for_posts' ) ) {
		$anchor    = $path_of( (string) get_permalink( (int) get_option( 'page_for_posts' ) ) );
		$inclusive = true;
	}

	$ancestors = array();

	foreach ( nwcs_seo_pages() as $key => $page ) {
		$path     = $path_of( home_url( $page['path'] ) );
		$is_upper = $inclusive ? $path === $anchor || str_starts_with( $anchor . '/', $path . '/' ) : $path !== $anchor && str_starts_with( $anchor . '/', $path . '/' );

		if ( $path !== $home && $is_upper ) {
			$ancestors[ strlen( $path ) ] = array(
				'name' => nwcs_seo_page_auto( $key )['name'],
				'url'  => home_url( $page['path'] ),
			);
		}
	}

	ksort( $ancestors );

	return array_merge(
		$crumbs,
		array_values( $ancestors ),
		array(
			array(
				'name' => $context['name'],
				'url'  => $context['url'],
			),
		)
	);
}

/**
 * Urun sayfasi: ad, aciklama, gorsel ve sartname satirlari.
 * Fiyat, puan ve yorum uydurulmaz; fiyat yoksa 'offers' hic yazilmaz.
 */
function nwcs_seo_product( array $context, string $org_id, string $page_id ): array {
	$source     = nwcs_manifest()['pages'][ $context['page_key'] ]['seo_source'] ?? array();
	$properties = array();

	if ( ! empty( $source['properties'] ) ) {
		foreach ( (array) nwcs_seo_path_value( $context['page_key'], $source['properties'] ) as $row ) {
			$name  = nwcs_seo_clean( $row['label'] ?? '' );
			$value = nwcs_seo_clean( $row['value'] ?? '' );

			if ( '' !== $name && '' !== $value ) {
				$properties[] = array(
					'@type' => 'PropertyValue',
					'name'  => $name,
					'value' => $value,
				);
			}
		}
	}

	return array_filter(
		array(
			'@type'              => 'Product',
			'@id'                => $context['url'] . '#product',
			'name'               => $context['name'],
			'description'        => $context['description'],
			'image'              => $context['image']['url'] ?? '',
			'url'                => $context['url'],
			'brand'              => array( '@id' => $org_id ),
			'manufacturer'       => array( '@id' => $org_id ),
			'mainEntityOfPage'   => array( '@id' => $page_id ),
			'additionalProperty' => $properties,
		)
	);
}

/**
 * Blog yazisi.
 */
function nwcs_seo_article( array $context, string $org_id, string $page_id, string $language ): array {
	$post = $context['post'];

	return array_filter(
		array(
			'@type'            => 'BlogPosting',
			'@id'              => $context['url'] . '#article',
			'headline'         => $context['name'],
			'description'      => $context['description'],
			'image'            => $context['image']['url'] ?? '',
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'inLanguage'       => $language,
			'author'           => array( '@id' => $org_id ),
			'publisher'        => array( '@id' => $org_id ),
			'mainEntityOfPage' => array( '@id' => $page_id ),
		)
	);
}

/* ====================================================================== *
 * Dizinleme kurallari ve site haritasi
 * ====================================================================== */

/**
 * Icerigi olmayan arsivler dizine girmesin: yazar, tarih, arama, ek sayfasi.
 */
add_filter( 'wp_robots', 'nwcs_seo_robots' );
function nwcs_seo_robots( array $robots ): array {
	if ( empty( nwcs_manifest()['pages'] ) ) {
		return $robots;
	}

	if ( is_author() || is_date() || is_search() || is_attachment() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}

	return $robots;
}

/**
 * Site haritasindan kullanici listesi cikarilir: yonetici kullanici adini
 * herkese acik bir dosyada listelemek gereksiz bir guvenlik acigi.
 */
add_filter( 'wp_sitemaps_add_provider', 'nwcs_seo_sitemap_providers', 10, 2 );
function nwcs_seo_sitemap_providers( $provider, string $name ) {
	return 'users' === $name ? false : $provider;
}

/* ====================================================================== *
 * /llms.txt
 *
 * Yapay zeka motorlari icin sitenin duz metin ozeti (llmstxt.org bicimi):
 * firma, sayfalar ve kisa aciklamalari, urunlerin sartname bilgileri,
 * blog yazilari ve iletisim. Icerikten her istekte uretilir; elle
 * guncellenmesi gerekmez.
 * ====================================================================== */

add_action( 'parse_request', 'nwcs_seo_maybe_llms' );
function nwcs_seo_maybe_llms( WP $wp ): void {
	if ( 'llms.txt' !== $wp->request || empty( nwcs_manifest()['pages'] ) ) {
		return;
	}

	// Arama motorlarina kapali sitede yapay zeka ozeti de verilmez.
	if ( ! get_option( 'blog_public' ) ) {
		status_header( 404 );
		exit;
	}

	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	header( 'Cache-Control: public, max-age=3600' );

	echo nwcs_seo_llms_text(); // phpcs:ignore WordPress.Security.EscapingOutput -- duz metin; Markdown.
	exit;
}

/**
 * llms.txt icerigi.
 */
function nwcs_seo_llms_text(): string {
	$org   = nwcs_seo_org_data();
	$lines = array( '# ' . $org['name'], '' );

	$summary = '' !== $org['description'] ? $org['description'] : nwcs_seo_clean( get_bloginfo( 'description' ) );

	if ( '' !== $summary ) {
		$lines[] = '> ' . $summary;
		$lines[] = '';
	}

	$facts = array_filter(
		array(
			'' !== $org['legal_name'] ? 'Resmî unvan: ' . $org['legal_name'] : '',
			'' !== $org['street'] ? 'Adres: ' . implode( ', ', array_filter( array( $org['street'], $org['district'], $org['city'], $org['postal_code'] ) ) ) : '',
			'' !== $org['phone'] ? 'Telefon: ' . $org['phone'] : '',
			'' !== $org['email'] ? 'E-posta: ' . $org['email'] : '',
		)
	);

	foreach ( $facts as $fact ) {
		$lines[] = '- ' . $fact;
	}

	if ( $facts ) {
		$lines[] = '';
	}

	$lines[] = '## Sayfalar';
	$lines[] = '';

	foreach ( nwcs_seo_pages() as $key => $page ) {
		$resolved = nwcs_seo_page_resolved( $key );
		$line     = sprintf( '- [%s](%s)', $resolved['name'], home_url( $page['path'] ) );

		if ( '' !== $resolved['description'] ) {
			$line .= ': ' . $resolved['description'];
		}

		// Urunlerde sartname duz metin olarak: olculer gorselde degil yazida.
		$source = (array) ( $page['seo_source'] ?? array() );

		if ( 'Product' === ( $source['type'] ?? '' ) && ! empty( $source['properties'] ) ) {
			$specs = array();

			foreach ( (array) nwcs_seo_path_value( $key, $source['properties'] ) as $row ) {
				$name  = nwcs_seo_clean( $row['label'] ?? '' );
				$value = nwcs_seo_clean( $row['value'] ?? '' );

				if ( '' !== $name && '' !== $value ) {
					$specs[] = $name . ': ' . $value;
				}
			}

			if ( $specs ) {
				$line .= ' (' . implode( '; ', $specs ) . ')';
			}
		}

		$lines[] = $line;
	}

	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 30,
		)
	);

	if ( $posts ) {
		$lines[] = '';
		$lines[] = '## Blog';
		$lines[] = '';

		foreach ( $posts as $post ) {
			$excerpt = nwcs_seo_clean( has_excerpt( $post ) ? $post->post_excerpt : $post->post_content, 140 );
			$lines[] = sprintf( '- [%s](%s): %s', nwcs_seo_clean( get_the_title( $post ) ), get_permalink( $post ), $excerpt );
		}
	}

	$lines[] = '';
	$lines[] = '## Ek';
	$lines[] = '';
	$lines[] = sprintf( '- [Site haritası](%s)', home_url( '/wp-sitemap.xml' ) );

	foreach ( $org['same_as'] as $url ) {
		$lines[] = sprintf( '- [Firmanın diğer adresi](%s)', $url );
	}

	return implode( "\n", $lines ) . "\n";
}
