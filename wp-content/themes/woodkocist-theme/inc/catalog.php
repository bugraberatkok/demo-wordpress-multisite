<?php
/**
 * Katalog: seriler ve alt kategoriler, kategori adresleri, magaza suzgeci.
 *
 * Kategoriler Urun Havuzu'ndaki urunlerden turetilir: seri (WOODGarden...)
 * panelde tanimli; alt kategori (Kamelyalar...) o serinin urunlerinde gecen
 * diger kategoriler. Havuza yeni kategori eklenince menude ve magazada
 * kendiliginden gorunur.
 *
 * Adresler gercek sitedeki gibi: /urun-kategori/woodgarden/kamelyalar/
 * (canliya gecerken eski adresler aynen calisir). Sayfayi page-magaza.php cizer.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'wk_catalog_rewrite' );
function wk_catalog_rewrite(): void {
	add_rewrite_rule( '^urun-kategori/(?:[^/]+/)*([^/]+)/?$', 'index.php?pagename=magaza&wk_kat=$matches[1]', 'top' );
}

add_filter(
	'query_vars',
	static function ( array $vars ): array {
		$vars[] = 'wk_kat';

		return $vars;
	}
);

/* Bilinmeyen kategori adresi 404 verir (bos magaza sayfasi degil). */
add_action( 'template_redirect', 'wk_catalog_404', 4 );
function wk_catalog_404(): void {
	$slug = (string) get_query_var( 'wk_kat' );

	if ( '' !== $slug && ! wk_category( sanitize_title( $slug ) ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}

// WordPress 404'te benzer adrese yonlendirmeyi dener (/magaza/); kategori adreslerinde dogru cevap 404.
add_filter(
	'redirect_canonical',
	static fn( $url ) => '' !== (string) get_query_var( 'wk_kat' ) ? false : $url
);

/**
 * Seri agaci: her seri ve altindaki kategoriler (urun sayisiyla).
 *
 * @return array<int, array{slug:string, label:string, text:string, count:int, url:string, children:array<int, array{slug:string, label:string, count:int, url:string}>}>
 */
function wk_category_tree(): array {
	static $tree = null;

	if ( null !== $tree ) {
		return $tree;
	}

	$tree = array();
	$seen = array();

	foreach ( wk_lines() as $line ) {
		$children = array();

		foreach ( wk_products() as $product ) {
			if ( ! isset( $product['categories'][ $line['slug'] ] ) ) {
				continue;
			}

			foreach ( $product['categories'] as $slug => $name ) {
				if ( $slug === $line['slug'] || isset( $seen[ $slug ] ) && $seen[ $slug ] !== $line['slug'] ) {
					continue;
				}

				$seen[ $slug ]       = $line['slug'];
				$children[ $slug ] ??= array( 'slug' => (string) $slug, 'label' => (string) $name, 'count' => 0 );
				++$children[ $slug ]['count'];
			}
		}

		foreach ( $children as $slug => $child ) {
			$children[ $slug ]['url'] = home_url( '/urun-kategori/' . $line['slug'] . '/' . $slug . '/' );

			// Panelde kategori sekmesinde verilen ad (yalnizca bu sitede); bossa havuzdaki ad.
			$page = wk_category_page_key( (string) $slug );
			$name = $page ? trim( (string) nwcs_field( $page, 'head', 'name' ) ) : '';

			if ( '' !== $name ) {
				$children[ $slug ]['label'] = $name;
			}
		}

		$tree[] = array(
			'slug'     => $line['slug'],
			'label'    => $line['label'],
			'text'     => $line['text'],
			'count'    => $line['count'],
			'url'      => home_url( '/urun-kategori/' . $line['slug'] . '/' ),
			'children' => array_values( $children ),
		);
	}

	return $tree;
}

/**
 * Kisaltmaya gore kategori (seri ya da alt kategori) ve bagli oldugu seri.
 *
 * @return array{slug:string, label:string, url:string, count:int, parent:?array}|null
 */
function wk_category( string $slug ): ?array {
	foreach ( wk_category_tree() as $line ) {
		if ( $line['slug'] === $slug ) {
			return $line + array( 'parent' => null );
		}

		foreach ( $line['children'] as $child ) {
			if ( $child['slug'] === $slug ) {
				return $child + array( 'parent' => $line );
			}
		}
	}

	return null;
}

function wk_category_url( string $slug ): string {
	$category = wk_category( $slug );

	return $category ? $category['url'] : home_url( '/magaza/' );
}

/**
 * Kategorinin panel sayfasi (manifestte 'kat-<kisaltma>'); yoksa bos.
 */
function wk_category_page_key( string $slug ): string {
	$key = 'kat-' . $slug;

	return function_exists( 'nwcs_manifest' ) && isset( nwcs_manifest()['pages'][ $key ] ) ? $key : '';
}

function wk_shop_url(): string {
	return home_url( '/magaza/' );
}

/**
 * Urunun serisi (ilk eslesen) ve alt kategorisi.
 *
 * @return array{line:?array, child:?array}
 */
function wk_product_place( array $product ): array {
	foreach ( wk_category_tree() as $line ) {
		if ( ! isset( $product['categories'][ $line['slug'] ] ) ) {
			continue;
		}

		foreach ( $line['children'] as $child ) {
			if ( isset( $product['categories'][ $child['slug'] ] ) ) {
				return array( 'line' => $line, 'child' => $child );
			}
		}

		return array( 'line' => $line, 'child' => null );
	}

	return array( 'line' => null, 'child' => null );
}

/**
 * Magaza suzgeci: kategori, arama ve siralama.
 *
 * @return array<int, array<string, mixed>>
 */
function wk_filter_products( string $category = '', string $search = '', string $sort = '' ): array {
	$products = wk_products();

	if ( '' !== $category ) {
		$products = array_filter( $products, static fn( array $p ): bool => isset( $p['categories'][ $category ] ) );
	}

	$search = trim( $search );

	if ( '' !== $search ) {
		$needle   = mb_strtolower( $search );
		$products = array_filter(
			$products,
			static fn( array $p ): bool => str_contains( mb_strtolower( $p['code'] . ' ' . $p['title'] . ' ' . implode( ' ', $p['categories'] ) ), $needle )
		);
	}

	$products = array_values( $products );

	if ( 'fiyat-artan' === $sort || 'fiyat-azalan' === $sort ) {
		// Fiyati olmayanlar her iki siralamada da sonda kalir.
		usort(
			$products,
			static function ( array $a, array $b ) use ( $sort ): int {
				$pa = wk_price_number( (string) $a['price'] );
				$pb = wk_price_number( (string) $b['price'] );

				if ( ! $pa || ! $pb ) {
					return $pa ? -1 : ( $pb ? 1 : 0 );
				}

				return 'fiyat-artan' === $sort ? $pa <=> $pb : $pb <=> $pa;
			}
		);
	} elseif ( 'ad' === $sort ) {
		usort( $products, static fn( array $a, array $b ): int => strcmp( $a['title'], $b['title'] ) );
	}

	return $products;
}

function wk_sort_options(): array {
	return array(
		''             => 'Önerilen sıra',
		'fiyat-artan'  => 'Fiyat: düşükten yükseğe',
		'fiyat-azalan' => 'Fiyat: yüksekten düşüğe',
		'ad'           => 'Ürün adına göre',
	);
}

/**
 * Tutar yazimi: 10250 -> "10.250 ₺", 1299.9 -> "1.299,90 ₺".
 */
function wk_money( float $amount ): string {
	$decimals = abs( $amount - round( $amount ) ) > 0.004 ? 2 : 0;

	return number_format( $amount, $decimals, ',', '.' ) . ' ₺';
}

/* SEO: kategori sayfalari eklentiye bildirilir (baslik, aciklama, canonical, site haritasi). */
add_filter( 'nwcs_seo_extra_pages', 'wk_seo_category_pages' );
function wk_seo_category_pages( array $pages ): array {
	foreach ( wk_category_tree() as $line ) {
		$pages[] = array(
			'url'         => $line['url'],
			'name'        => $line['label'] . ' Serisi',
			'description' => trim( sprintf( '%s serisi: %s. %d ürün; fiyat, ölçü ve ahşap cinsi ürün sayfalarında.', $line['label'], implode( ', ', wp_list_pluck( $line['children'], 'label' ) ), $line['count'] ) ),
			'sitemap'     => true,
		);

		foreach ( $line['children'] as $child ) {
			$pages[] = array(
				'url'         => $child['url'],
				'name'        => sprintf( 'Ahşap %s', $child['label'] ),
				'description' => sprintf( 'Ahşap %s: %s serisinde %d model. Ölçü, ahşap cinsi ve KDV dahil fiyatlarıyla; sepete ekleyip üyeliksiz sipariş verin.', mb_strtolower( $child['label'] ), $line['label'], $child['count'] ),
				'sitemap'     => true,
			);
		}
	}

	return $pages;
}
