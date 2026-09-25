<?php
/**
 * Kategori sayfalari ve site ici gezinme.
 *
 * Kategori agaci ayri bir yerde tutulmaz; panelde zaten duzenlenen ust menuden
 * turetilir. Alt menusu olan ve alt ogelerinden en az biri yalin capa
 * (#ahsap-kalas) olan menu ogesi bir urun grubudur (Kereste, Ambalaj...);
 * alt ogeleri o grubun kategorileridir. Panelde menuye eklenen her alt oge
 * kendi sayfasini kendiliginden alir.
 *
 * Adresler:
 *   /kategoriler/                      tum gruplar
 *   /kategoriler/<grup>/               grubun butun urunleri
 *   /kategoriler/<grup>/<kategori>/    tek kategori
 *
 * Urunler merkezi havuzdan gelir. Hangi havuz kategorisinin hangi sayfaya
 * dustugu manifestteki kategori sayfalarinda yazilidir (kat-<kategori>,
 * grp-<grup>: 'products' alaninin 'category' degeri, havuzdaki kategori adi).
 * Orada yoksa eski kural: havuzdaki kategori adres adi (slug) menudeki capayla
 * ayniysa (ahsap-kalas) urun o kategoriye, grubun adiyla ayniysa (kereste)
 * dogrudan gruba duser.
 */

defined( 'ABSPATH' ) || exit;

/*
 * Rewrite kurallari degistiginde surum artirilir; kurallar her sitede bir kez,
 * ilk istekte yeniden yazilir.
 */
const KOCIST_CATALOG_REWRITE_VERSION = '1';

add_action( 'init', 'kocist_catalog_rewrite' );
function kocist_catalog_rewrite(): void {
	add_rewrite_rule( '^kategoriler/?$', 'index.php?kocist_catalog=1', 'top' );
	add_rewrite_rule( '^kategoriler/([^/]+)/?$', 'index.php?kocist_catalog=1&kocist_group=$matches[1]', 'top' );
	add_rewrite_rule( '^kategoriler/([^/]+)/([^/]+)/?$', 'index.php?kocist_catalog=1&kocist_group=$matches[1]&kocist_sub=$matches[2]', 'top' );
}

add_action( 'init', 'kocist_catalog_maybe_flush', 99 );
function kocist_catalog_maybe_flush(): void {
	if ( KOCIST_CATALOG_REWRITE_VERSION === get_option( 'kocist_catalog_rewrite' ) ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'kocist_catalog_rewrite', KOCIST_CATALOG_REWRITE_VERSION );
}

add_filter( 'query_vars', 'kocist_catalog_query_vars' );
function kocist_catalog_query_vars( array $vars ): array {
	return array_merge( $vars, array( 'kocist_catalog', 'kocist_group', 'kocist_sub' ) );
}

/**
 * Kategori sayfasi mi goruntuleniyor?
 */
function kocist_is_catalog_request(): bool {
	return (bool) get_query_var( 'kocist_catalog' );
}

/* ------------------------------------------------------------------ */
/* Kategori agaci                                                       */
/* ------------------------------------------------------------------ */

/**
 * Menuden turetilen urun gruplari.
 *
 * @return array<string, array{slug:string, name:string, url:string, image:array, subs:array<string, array{slug:string, name:string, url:string, link:string}>}>
 */
function kocist_catalog_groups(): array {
	static $groups = null;

	if ( null !== $groups ) {
		return $groups;
	}

	$groups = array();

	foreach ( nwcs_rows( 'global', 'header', 'menu' ) as $row_index => $row ) {
		$label   = trim( (string) ( $row['label'] ?? '' ) );
		$submenu = trim( (string) ( $row['submenu'] ?? '' ) );

		if ( '' === $label || '' === $submenu ) {
			continue;
		}

		$children = nwcs_rows( 'global', $submenu, 'items' );
		$is_group = false;

		foreach ( $children as $child ) {
			if ( kocist_is_dead_anchor( $child['url'] ?? '' ) ) {
				$is_group = true;
				break;
			}
		}

		// Kurumsal gibi alt ogeleri gercek sayfalara giden menuler grup degil.
		if ( ! $is_group ) {
			continue;
		}

		$slug = sanitize_title( $label );
		$subs = array();

		foreach ( $children as $child_index => $child ) {
			$name = trim( (string) ( $child['label'] ?? '' ) );
			$url  = trim( (string) ( $child['url'] ?? '' ) );

			if ( '' === $name ) {
				continue;
			}

			// Capa adres adi olur (#ahsap-kalas -> ahsap-kalas); capa yoksa addan.
			$sub_slug = kocist_is_dead_anchor( $url ) ? sanitize_title( substr( $url, 1 ) ) : sanitize_title( $name );

			if ( '' === $sub_slug || isset( $subs[ $sub_slug ] ) ) {
				continue;
			}

			$sub_url = home_url( "/kategoriler/{$slug}/{$sub_slug}/" );

			$subs[ $sub_slug ] = array(
				'slug' => $sub_slug,
				'name' => $name,
				'url'  => $sub_url,
				// Menude gidilecek yer: panelde gercek bir adres yazildiysa o
				// (Hayvan Barinaklari -> /urun/), yalin capaysa kategori sayfasi.
				'link' => kocist_is_dead_anchor( $url ) ? $sub_url : kocist_link( $url ),
				// Paneldeki satir: onizlemede adina tiklaninca menunun o satiri acilir.
				'edit' => array( $submenu, (int) $child_index ),
			);
		}

		$groups[ $slug ] = array(
			'slug'  => $slug,
			'name'  => $label,
			'url'   => home_url( "/kategoriler/{$slug}/" ),
			'image'    => kocist_catalog_group_image( $slug, $label ),
			'subs'     => $subs,
			'menu_row' => (int) $row_index,
		);
	}

	return $groups;
}

/**
 * Grubun fotografi: ana sayfadaki urun grubu kartinda secilen gorsel, yoksa
 * temadaki grup fotografi.
 */
function kocist_catalog_group_image( string $slug, string $label ): array {
	$files = array(
		'kereste'    => 's2-kereste.jpg',
		'ambalaj'    => 's2-ambalaj.jpg',
		'dekorasyon' => 's2-dekorasyon.jpg',
		'hirdavat'   => 's2-hirdavat.jpg',
	);

	$file = $files[ $slug ] ?? 'atolye.jpg';

	foreach ( nwcs_rows( 'home', 'catalog', 'items' ) as $item ) {
		if ( str_contains( sanitize_title( (string) ( $item['title'] ?? '' ) ), $slug ) ) {
			return kocist_image_or_default( nwcs_image_by_id( (int) ( $item['image'] ?? 0 ), 'large' ), $file, $label );
		}
	}

	return kocist_image_or_default( array(), $file, $label );
}

/**
 * Bu siteye secilmis urunler, grup ve kategori bilgisiyle.
 *
 * Her urune 'group' ve 'sub' anahtarlari eklenir (bos olabilir).
 *
 * @return array<int, array>
 */
function kocist_catalog_products(): array {
	static $products = null;

	if ( null !== $products ) {
		return $products;
	}

	$products = array();

	if ( ! function_exists( 'nwcs_site_products' ) ) {
		return $products;
	}

	$groups = kocist_catalog_groups();
	$map    = kocist_catalog_pool_map();

	foreach ( nwcs_site_products() as $product ) {
		$product['group'] = '';
		$product['sub']   = '';

		// Once manifestteki eslesme (havuz kategori adi -> grup/kategori);
		// alt kategori, gruba dogrudan baglanan kategoriden once gelir.
		foreach ( (array) ( $product['categories'] ?? array() ) as $name ) {
			$place = $map[ (string) $name ] ?? null;

			if ( ! $place || ! isset( $groups[ $place[0] ] ) ) {
				continue;
			}

			if ( '' !== $place[1] && isset( $groups[ $place[0] ]['subs'][ $place[1] ] ) ) {
				$product['group'] = $place[0];
				$product['sub']   = $place[1];
				break;
			}

			if ( '' === $place[1] ) {
				$product['group'] = $place[0];
			}
		}

		// Manifest bir yer verdiyse (alt kategori ya da yalnizca grup) eski kurala bakilmaz.
		if ( '' !== $product['group'] ) {
			$products[] = $product;
			continue;
		}

		foreach ( array_keys( (array) ( $product['categories'] ?? array() ) ) as $term ) {
			foreach ( $groups as $group ) {
				if ( isset( $group['subs'][ $term ] ) ) {
					$product['group'] = $group['slug'];
					$product['sub']   = $term;
					break 2;
				}

				if ( $term === $group['slug'] && '' === $product['group'] ) {
					$product['group'] = $group['slug'];
				}
			}
		}

		$products[] = $product;
	}

	return $products;
}

/**
 * Manifestteki kategori sayfalarindan: havuz kategori adi => array( grup, kategori ).
 *
 * Grup sayfasi (grp-<grup>) kategori bos doner; o havuz kategorisindeki urun
 * dogrudan gruba duser.
 *
 * @return array<string, array{0:string, 1:string}>
 */
function kocist_catalog_pool_map(): array {
	static $map = null;

	if ( null !== $map ) {
		return $map;
	}

	$map   = array();
	$pages = function_exists( 'nwcs_manifest' ) ? ( nwcs_manifest()['pages'] ?? array() ) : array();

	foreach ( $pages as $key => $page ) {
		$category = trim( (string) ( $page['components']['products']['fields']['pool']['category'] ?? '' ) );
		$place    = $page['catalog'] ?? null;

		if ( '' === $category || ! is_array( $place ) ) {
			continue;
		}

		// Ek adlar: baska sitenin kategorisindeki ortak urunler (WOODPets gibi).
		foreach ( array_merge( array( $category ), (array) ( $place['aliases'] ?? array() ) ) as $name ) {
			$map[ (string) $name ] ??= array( (string) ( $place['group'] ?? '' ), (string) ( $place['sub'] ?? '' ) );
		}
	}

	return $map;
}

/**
 * Kategori ya da grubun paneldeki sayfa anahtari; yoksa bos.
 */
function kocist_catalog_page_key( string $group, string $sub = '' ): string {
	$pages = function_exists( 'nwcs_manifest' ) ? ( nwcs_manifest()['pages'] ?? array() ) : array();
	$key   = '' !== $sub ? 'kat-' . $sub : 'grp-' . $group;

	return isset( $pages[ $key ] ) ? $key : '';
}

/**
 * Grup ya da kategoriye dusen urunler.
 */
function kocist_catalog_products_in( string $group, string $sub = '' ): array {
	return array_values(
		array_filter(
			kocist_catalog_products(),
			static fn( array $product ): bool => $product['group'] === $group && ( '' === $sub || $product['sub'] === $sub )
		)
	);
}

/**
 * Kategori basina urun sayisi (grup => [kategori => sayi, '' => toplam]).
 */
function kocist_catalog_counts(): array {
	$counts = array();

	foreach ( kocist_catalog_products() as $product ) {
		if ( '' === $product['group'] ) {
			continue;
		}

		$counts[ $product['group'] ][''] = ( $counts[ $product['group'] ][''] ?? 0 ) + 1;

		if ( '' !== $product['sub'] ) {
			$counts[ $product['group'] ][ $product['sub'] ] = ( $counts[ $product['group'] ][ $product['sub'] ] ?? 0 ) + 1;
		}
	}

	return $counts;
}

/**
 * Serbest bir metne (slayt basligi gibi) en uygun kategori ya da grup adresi.
 *
 * Kategorinin adindaki anlamli kelimelerin hepsi metinde geciyorsa kategori
 * ("Plywood ve Kontrplak Levha" -> Plywood Levha); yoksa grup adi geciyorsa
 * grup ("Bahce ve Dekorasyon Urunleri" -> Dekorasyon). Bulunamazsa bos.
 */
function kocist_catalog_url_for_text( string $text ): string {
	$haystack = '-' . sanitize_title( $text ) . '-';
	$best     = '';
	$score    = 0;

	// "Ahsap" neredeyse her adda geciyor; eslesmeyi belirlemesin.
	$ignore = array( 'ahsap', 've', 'grubu', 'urunleri', 'levha' );

	foreach ( kocist_catalog_groups() as $group ) {
		foreach ( $group['subs'] as $sub ) {
			$words = array_diff( explode( '-', sanitize_title( $sub['name'] ) ), $ignore );
			$words = array_filter( $words, static fn( string $word ): bool => strlen( $word ) > 2 );

			if ( ! $words ) {
				continue;
			}

			foreach ( $words as $word ) {
				if ( ! str_contains( $haystack, '-' . $word . '-' ) ) {
					continue 2;
				}
			}

			if ( count( $words ) > $score ) {
				$score = count( $words );
				$best  = $sub['url'];
			}
		}
	}

	if ( '' !== $best ) {
		return $best;
	}

	foreach ( kocist_catalog_groups() as $group ) {
		if ( str_contains( $haystack, '-' . $group['slug'] . '-' ) ) {
			return $group['url'];
		}
	}

	return '';
}

/**
 * Panelde yazilmis baglanti hala varsayilan "katalog" capasi mi?
 * Oyleyse tema onu uygun kategori sayfasina baglar; panelde baska bir adres
 * yazildiysa her zaman o gecerlidir.
 */
function kocist_is_catalog_placeholder( $url ): bool {
	return in_array( trim( (string) $url ), array( '', '#', '#katalog', '/#katalog' ), true );
}

/* ------------------------------------------------------------------ */
/* Konum yolu (breadcrumb)                                              */
/* ------------------------------------------------------------------ */

/**
 * Kategori ya da urun icin konum yolu.
 *
 * @return array<int, array{name:string, url:string}> Son oge su anki sayfadir.
 */
function kocist_catalog_trail( string $group = '', string $sub = '', string $product = '', int $product_id = 0 ): array {
	$groups = kocist_catalog_groups();
	$trail  = array(
		array( 'name' => nwcs_field( 'global', 'texts', 'home_crumb' ), 'url' => home_url( '/' ), 'edit' => array( 'global', 'texts', 'home_crumb' ) ),
		array( 'name' => nwcs_field( 'kategoriler', 'index', 'crumb' ), 'url' => home_url( '/kategoriler/' ), 'edit' => array( 'kategoriler', 'index', 'crumb' ) ),
	);

	if ( isset( $groups[ $group ] ) ) {
		$trail[] = array(
			'name' => $groups[ $group ]['name'],
			'url'  => $groups[ $group ]['url'],
			'edit' => array( 'global', 'header', 'menu', $groups[ $group ]['menu_row'], 'label' ),
		);

		if ( isset( $groups[ $group ]['subs'][ $sub ] ) ) {
			$item    = $groups[ $group ]['subs'][ $sub ];
			$trail[] = array(
				'name' => $item['name'],
				'url'  => $item['url'],
				'edit' => array( 'global', $item['edit'][0], 'items', $item['edit'][1], 'label' ),
			);
		}
	}

	if ( '' !== $product ) {
		$trail[] = array( 'name' => $product, 'url' => '', 'product' => $product_id );
	}

	return $trail;
}

/**
 * Konum yolunu basar. Son oge baglanti degil, aria-current="page".
 */
function kocist_the_trail( array $trail, string $class = '' ): void {
	$last = count( $trail ) - 1;
	?>
	<nav class="k-crumbs <?php echo esc_attr( $class ); ?>" aria-label="Konum">
		<ol class="k-crumbs__list">
			<?php foreach ( $trail as $index => $step ) : ?>
				<li class="k-crumbs__item">
					<?php
					// Onizlemede: adin geldigi yer (menu satiri, ortak metin ya da havuzdaki urun).
					ob_start();
					if ( ! empty( $step['product'] ) ) {
						kocist_product_attr( array( 'id' => $step['product'] ), 'Ürün adı' );
					} elseif ( ! empty( $step['edit'] ) ) {
						nwcs_edit_attr( ...$step['edit'] );
					}
					$attr = ob_get_clean();
					?>
					<?php if ( $index === $last ) : ?>
						<span aria-current="page" <?php echo $attr; // phpcs:ignore WordPress.Security.EscapingOutput -- nwcs_*_attr kacirir. ?>><?php echo esc_html( $step['name'] ); ?></span>
					<?php else : ?>
						<a href="<?php echo esc_url( $step['url'] ); ?>" <?php echo $attr; // phpcs:ignore WordPress.Security.EscapingOutput ?>><?php echo esc_html( $step['name'] ); ?></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/* ------------------------------------------------------------------ */
/* Sayfanin cizilmesi                                                   */
/* ------------------------------------------------------------------ */

/**
 * Istenen grup ve kategori; bilinmeyen adres icin null.
 *
 * @return array{group:?array, sub:?array}|null
 */
function kocist_catalog_current(): ?array {
	$groups = kocist_catalog_groups();
	$group  = sanitize_title( (string) get_query_var( 'kocist_group' ) );
	$sub    = sanitize_title( (string) get_query_var( 'kocist_sub' ) );

	if ( '' === $group ) {
		return array( 'group' => null, 'sub' => null );
	}

	if ( ! isset( $groups[ $group ] ) ) {
		return null;
	}

	if ( '' === $sub ) {
		return array( 'group' => $groups[ $group ], 'sub' => null );
	}

	if ( ! isset( $groups[ $group ]['subs'][ $sub ] ) ) {
		return null;
	}

	return array( 'group' => $groups[ $group ], 'sub' => $groups[ $group ]['subs'][ $sub ] );
}

// Eklentinin urun detayi (oncelik 5) ile ayni yontem: sablon basilip cikilir.
add_action( 'template_redirect', 'kocist_catalog_template', 6 );
function kocist_catalog_template(): void {
	if ( ! kocist_is_catalog_request() ) {
		return;
	}

	$current = kocist_catalog_current();

	if ( null === $current ) {
		global $wp_query;

		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();

		include get_query_template( '404' ) ?: get_index_template();
		exit;
	}

	status_header( 200 );

	get_template_part( 'template-parts/category-page', null, $current );
	exit;
}

/**
 * Sekme basligi: kategori adi | site adi. SEO eklentisi kendi basligini
 * ek sayfa listesinden (asagida) verir; eklenti yoksa bu gecerli olur.
 */
add_filter( 'document_title_parts', 'kocist_catalog_title_parts' );
function kocist_catalog_title_parts( array $parts ): array {
	if ( ! kocist_is_catalog_request() ) {
		return $parts;
	}

	$current = kocist_catalog_current();

	if ( ! $current ) {
		return $parts;
	}

	$parts['title'] = $current['sub']['name'] ?? $current['group']['name'] ?? 'Kategoriler';
	$parts['site']  = get_bloginfo( 'name', 'display' );
	unset( $parts['tagline'] );

	return $parts;
}

/**
 * SEO eklentisine kategori ve urun sayfalarini bildirir: baslik, aciklama,
 * canonical, paylasim gorseli ve llms.txt bunlardan uretilir.
 */
add_filter( 'nwcs_seo_extra_pages', 'kocist_catalog_seo_pages' );
function kocist_catalog_seo_pages( array $pages ): array {
	$counts = kocist_catalog_counts();
	$groups = kocist_catalog_groups();

	if ( ! $groups ) {
		return $pages;
	}

	$pages[] = array(
		'url'         => home_url( '/kategoriler/' ),
		'name'        => 'Ürün Kategorileri',
		'description' => sprintf(
			'%s ürün gruplarımız ve alt kategorileri.',
			implode( ', ', wp_list_pluck( $groups, 'name' ) )
		),
	);

	foreach ( $groups as $group ) {
		$pages[] = array(
			'url'         => $group['url'],
			'name'        => $group['name'],
			'description' => sprintf(
				'%s grubundaki ürünler: %s.',
				$group['name'],
				implode( ', ', array_slice( wp_list_pluck( $group['subs'], 'name' ), 0, 8 ) )
			),
			'image'       => $group['image']['url'] ? array( 'url' => $group['image']['url'], 'alt' => $group['image']['alt'] ) : 0,
		);

		foreach ( $group['subs'] as $sub ) {
			$count   = (int) ( $counts[ $group['slug'] ][ $sub['slug'] ] ?? 0 );
			$pages[] = array(
				'url'         => $sub['url'],
				'name'        => $sub['name'],
				'description' => $count
					? sprintf( '%s kategorisinde %d ürün. Ölçü ve adede göre fiyat için bize ulaşın.', $sub['name'], $count )
					: sprintf( '%s için ölçü ve adede göre fiyat teklifi alın.', $sub['name'] ),
			);
		}
	}

	// Urun detay sayfalari: WordPress sorgusu bunlari yazi listesi sanmasin.
	foreach ( kocist_catalog_products() as $product ) {
		if ( '' === trim( (string) $product['body'] ) ) {
			continue;
		}

		$image   = kocist_product_image( $product );
		$pages[] = array(
			'url'         => $product['url'],
			'name'        => $product['title'],
			'description' => $product['short'] ?: wp_strip_all_tags( $product['body'] ),
			'image'       => $image['url'] ? array( 'url' => $image['url'], 'alt' => $image['alt'] ) : 0,
			'type'        => 'Product',
			'properties'  => $product['spec'] ? array( array( 'name' => 'Ölçü', 'value' => $product['spec'] ) ) : array(),
		);
	}

	return $pages;
}
