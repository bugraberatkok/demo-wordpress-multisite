<?php
/**
 * woodkocist temasi (WOOD KOCIST, YEREL DENEME).
 *
 * Urunler merkezi Urun Havuzu'ndan gelir (nwcs_site_products): panelde bu
 * site icin secilen urunler, secim sirasiyla. Urun eklenince kart eklenir,
 * cikarilinca kaybolur; gorsel eklenince kod plakasinin yerini fotograf alir.
 * Urun detay sayfasi /urun/<slug>/ (eklenti yonlendirir, bu tema cizer).
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'nwcs_field' ) ) {
	function nwcs_field( $page, $component, $field, $fallback = null ) {
		return null === $fallback ? '' : $fallback;
	}
	function nwcs_rows( $page, $component, $field ) {
		return array();
	}
	function nwcs_edit_attr( $page, $component, $field = '', $row = null, $sub = '' ) {}
}

require_once get_theme_file_path( 'inc/setup.php' );
require_once get_theme_file_path( 'inc/requests.php' );
require_once get_theme_file_path( 'inc/checkout.php' );
require_once get_theme_file_path( 'inc/catalog.php' );
require_once get_theme_file_path( 'inc/cart.php' );
require_once get_theme_file_path( 'inc/nav.php' );

add_action( 'after_setup_theme', 'wk_setup' );
function wk_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'style', 'script', 'search-form', 'gallery', 'caption' ) );
	// Kurumsal sayfalarin ozeti sayfa basinda ve arama aciklamasinda kullanilir.
	add_post_type_support( 'page', 'excerpt' );
}

add_action( 'wp_enqueue_scripts', 'wk_assets' );
function wk_assets(): void {
	$ver = static fn( string $file ): string => wp_get_theme()->get( 'Version' ) . '.' . (int) @filemtime( get_theme_file_path( $file ) );

	wp_enqueue_style( 'wk-fonts', 'https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,300..900&display=swap', array(), null );
	wp_enqueue_style( 'wk-site', get_theme_file_uri( 'assets/site.css' ), array( 'wk-fonts' ), $ver( 'assets/site.css' ) );
	wp_enqueue_script( 'wk-site', get_theme_file_uri( 'assets/site.js' ), array(), $ver( 'assets/site.js' ), true );
}

add_action( 'wp_head', 'wk_preconnect', 1 );
function wk_preconnect(): void {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}

/* ====================================================================== *
 * Yardimcilar
 * ====================================================================== */

function wk_link( $url ): string {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '#';
	}

	if ( str_starts_with( $url, '/' ) && ! str_starts_with( $url, '//' ) ) {
		return home_url( $url );
	}

	return $url;
}

function wk_multiline( string $value ): string {
	return nl2br( esc_html( $value ) );
}

function wk_paragraphs( string $value ): string {
	$html = '';

	foreach ( preg_split( '/\R\s*\R/u', trim( $value ) ) ?: array() as $block ) {
		if ( '' !== trim( $block ) ) {
			$html .= '<p>' . wk_multiline( trim( $block ) ) . '</p>';
		}
	}

	return $html;
}

function wk_part( string $name, array $args = array() ): void {
	get_template_part( 'template-parts/' . $name, null, $args );
}

/**
 * WhatsApp baglantisi; $text verilirse mesaj hazir gelir.
 */
function wk_whatsapp( string $text = '' ): string {
	$url = trim( (string) nwcs_field( 'global', 'header', 'whatsapp_url' ) );

	return ( '' === $url || '' === $text ) ? $url : add_query_arg( 'text', rawurlencode( $text ), $url );
}

/**
 * Marka: gercek sitedeki logo gibi harf karolari ("WOOD" buyuk, "KOCIST"
 * kucuk). Yazi paneldeki logo_text'ten; tek kelimeyse duz yazi kalir.
 * Karolar gorsel; ekran okuyucu tam adi okur.
 */
function wk_brand_mark(): string {
	$text  = trim( (string) nwcs_field( 'global', 'header', 'logo_text' ) );
	$words = preg_split( '/\s+/u', $text, 2 ) ?: array();

	if ( count( $words ) < 2 ) {
		return esc_html( $text );
	}

	$tiles = static function ( string $word, string $class ): string {
		$html = '';

		foreach ( mb_str_split( $word ) as $char ) {
			$html .= '<span>' . esc_html( $char ) . '</span>';
		}

		return '<span class="' . $class . '" aria-hidden="true">' . $html . '</span>';
	};

	return '<span class="wk-sr">' . esc_html( $text ) . '</span>'
		. $tiles( $words[0], 'wk-mark__top' )
		. $tiles( $words[1], 'wk-mark__sub' );
}

/**
 * Panel onizlemesi: urune ait metin/gorsel tiklaninca Urun Havuzu'ndaki urun
 * acilir (eklentinin nwcs_product_attr'i; eklenti eskiyse hicbir sey basmaz).
 */
function wk_product_src( array $product, string $label ): void {
	if ( function_exists( 'nwcs_product_attr' ) ) {
		nwcs_product_attr( (int) $product['id'], $label );
	}
}

function wk_icon( string $name ): string {
	$paths = array(
		'whatsapp' => '<path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M8.8 8.5c.3-.6.6-.6.9-.6h.6c.2 0 .4 0 .6.5l.8 1.9c.1.2 0 .4-.1.6l-.5.6c-.1.2-.2.3 0 .6a7 7 0 0 0 3 2.6c.3.1.4.1.6-.1l.7-.8c.2-.2.4-.2.6-.1l1.8.9c.3.1.4.2.4.4 0 .6-.2 1.3-.9 1.7-.7.4-1.6.5-3.2-.1a10 10 0 0 1-4.5-4c-.8-1.3-.9-2.6-.4-3.4z" fill="currentColor"/>',
		'phone'    => '<path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
		'cart'     => '<path d="M3 4h2.2l2.1 10.2a1.5 1.5 0 0 0 1.5 1.2h8.4a1.5 1.5 0 0 0 1.5-1.1L20.5 8H6.1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9.5" cy="19.5" r="1.5" fill="currentColor"/><circle cx="17" cy="19.5" r="1.5" fill="currentColor"/>',
		'search'   => '<circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
		'menu'     => '<path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
		'close'    => '<path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
		'chevron'  => '<path d="m7 10 5 5 5-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		'prev'     => '<path d="m14 6-6 6 6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		'next'     => '<path d="m10 6 6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		'check'    => '<path d="m5 12.5 4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		'truck'    => '<path d="M3 6h11v9H3zM14 9h4l3 3.5V15h-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="7" cy="17.5" r="1.8" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="17.5" r="1.8" fill="none" stroke="currentColor" stroke-width="2"/>',
		'shield'   => '<path d="M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="m9 12 2.2 2.2L15.5 10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		'ruler'    => '<path d="M3 16 16 3l5 5L8 21z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="m7 12 2 2M10 9l2 2M13 6l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
		'trash'    => '<path d="M5 7h14M10 7V4.5h4V7M7 7l1 13h8l1-13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
	);

	return isset( $paths[ $name ] ) ? '<svg class="wk-icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>' : '';
}

/* ====================================================================== *
 * Urunler (Urun Havuzu)
 * ====================================================================== */

/**
 * Sitede gosterilen urunler; her satira havuzdaki urun kodu eklenir.
 *
 * @return array<int, array<string, mixed>>
 */
function wk_products(): array {
	static $products = null;

	if ( null !== $products ) {
		return $products;
	}

	$products = array();

	if ( ! function_exists( 'nwcs_site_products' ) ) {
		return $products;
	}

	$pool = nwcs_pool_products();

	foreach ( nwcs_site_products() as $product ) {
		$product['code'] = (string) ( $pool[ $product['id'] ]['code'] ?? '' );
		$products[]      = $product;
	}

	return $products;
}

/**
 * Seriler (panelde duzenlenir): etiket, havuz kategori adi, kisa aciklama ve
 * o seride kac urun oldugu.
 *
 * @return array<int, array{label:string, category:string, text:string, slug:string, count:int}>
 */
function wk_lines(): array {
	$lines = array();

	foreach ( nwcs_rows( 'home', 'catalog', 'lines' ) as $row ) {
		$category = trim( (string) ( $row['category'] ?? '' ) );

		if ( '' === $category ) {
			continue;
		}

		// Kisaltma havuzdaki kategoriden: ad "Şezlonglar" iken kisaltma
		// "ahsap-sezlonglar" olabilir; suzgec kartlardaki kisaltmayla calisir.
		$slug = sanitize_title( $category );

		foreach ( wk_products() as $product ) {
			$found = array_search( $category, $product['categories'], true );

			if ( false !== $found ) {
				$slug = (string) $found;
				break;
			}
		}

		$lines[] = array(
			'label'    => (string) ( $row['label'] ?? $category ),
			'category' => $category,
			'text'     => (string) ( $row['text'] ?? '' ),
			'slug'     => $slug,
			'count'    => count( array_filter( wk_products(), static fn( array $p ): bool => in_array( $category, $p['categories'], true ) ) ),
		);
	}

	return $lines;
}

/**
 * Urun turleri (seri olmayan kategoriler), urun sayisina gore.
 *
 * @return array<string, array{label:string, count:int}>
 */
function wk_types(): array {
	$line_names = wp_list_pluck( wk_lines(), 'category' );
	$types      = array();

	foreach ( wk_products() as $product ) {
		foreach ( $product['categories'] as $slug => $name ) {
			if ( in_array( $name, $line_names, true ) ) {
				continue;
			}

			$types[ $slug ] = array(
				'label' => $name,
				'count' => ( $types[ $slug ]['count'] ?? 0 ) + 1,
			);
		}
	}

	return $types;
}

/**
 * Havuzdaki tek satirlik ozellik metni ("Ad: deger; Ad: deger") -> ciftler.
 *
 * @return array<int, array{0:string, 1:string}>
 */
function wk_specs( string $spec ): array {
	$pairs = array();

	foreach ( preg_split( '/\s*;\s*/u', trim( $spec ) ) ?: array() as $part ) {
		$split = explode( ':', $part, 2 );

		if ( 2 === count( $split ) && '' !== trim( $split[0] ) && '' !== trim( $split[1] ) ) {
			$pairs[] = array( trim( $split[0] ), trim( $split[1] ) );
		}
	}

	return $pairs;
}

/**
 * Karttaki kisa ozellikler: urunu ayirt eden birkac deger.
 */
function wk_card_specs( array $product ): array {
	$wanted = array( 'Boyut Sınıfı', 'Kapasite', 'Zemin Ölçüsü', 'Ahşap Cinsi' );
	$out    = array();

	foreach ( wk_specs( (string) $product['spec'] ) as $pair ) {
		if ( in_array( $pair[0], $wanted, true ) && count( $out ) < 2 ) {
			$out[] = $pair;
		}
	}

	return $out;
}

function wk_image( array $product ): array {
	foreach ( (array) ( $product['images'] ?? array() ) as $image ) {
		if ( ! empty( $image['url'] ) ) {
			return $image;
		}
	}

	return array( 'url' => '', 'alt' => '' );
}

/**
 * Havuzdaki fiyat metni ("10.250 ₺", "1.299,90 ₺") -> sayi; okunamazsa 0
 * (0 ise yapilandirilmis veride teklif uretilmez).
 */
function wk_price_number( string $price ): float {
	$digits = preg_replace( '/[^0-9,]/', '', $price );

	if ( '' === $digits || null === $digits ) {
		return 0.0;
	}

	return (float) str_replace( ',', '.', $digits );
}

function wk_order_text( array $product ): string {
	return trim( sprintf( 'Merhaba, %s %s hakkında bilgi almak ve sipariş vermek istiyorum.', $product['code'], $product['title'] ) );
}

/* ====================================================================== *
 * SEO ve GEO: havuz urunlerinin sayfalari (/urun/<slug>/) eklentiye
 * bildirilir; baslik, aciklama, canonical, Product verisi ve llms.txt.
 * ====================================================================== */

add_filter( 'nwcs_seo_extra_pages', 'wk_seo_product_pages' );
function wk_seo_product_pages( array $pages ): array {
	foreach ( wk_products() as $product ) {
		if ( '' === trim( (string) $product['body'] ) ) {
			continue; // Detay metni olmayan urunun sayfasi yok (eklenti 404 verir).
		}

		$image = wk_image( $product );

		$pages[] = array(
			'url'         => $product['url'],
			'name'        => $product['title'],
			'description' => '' !== trim( (string) $product['short'] ) ? $product['short'] : wp_strip_all_tags( (string) $product['body'] ),
			'image'       => ! empty( $image['id'] ) ? (int) $image['id'] : 0,
			'type'        => 'Product',
			'sitemap'     => true,
			'sku'         => $product['code'],
			'price'       => wk_price_number( (string) $product['price'] ),
			'currency'    => 'TRY',
			'properties'  => array_map(
				static fn( array $pair ): array => array( 'name' => $pair[0], 'value' => $pair[1] ),
				array_merge( array( array( 'Ürün kodu', $product['code'] ) ), wk_specs( (string) $product['spec'] ) )
			),
		);
	}

	return $pages;
}

/**
 * Favicon: SVG (modern tarayicilar), 32px PNG yedegi ve iOS icin 180px ikon.
 * Panelde (Ozellestir > Site Kimligi) site ikonu secilirse WordPress'inki
 * gecerli olur; tema kendi ikonunu basmaz.
 */
add_action( 'wp_head', 'wk_favicon', 2 );
add_action( 'login_head', 'wk_favicon' );
function wk_favicon(): void {
	if ( has_site_icon() ) {
		return;
	}

	printf(
		'<link rel="icon" type="image/png" sizes="32x32" href="%s" />' . "\n" .
		'<link rel="icon" type="image/svg+xml" href="%s" />' . "\n" .
		'<link rel="apple-touch-icon" href="%s" />' . "\n",
		esc_url( get_theme_file_uri( 'assets/favicon-32.png' ) ),
		esc_url( get_theme_file_uri( 'assets/favicon.svg' ) ),
		esc_url( get_theme_file_uri( 'assets/apple-touch-icon.png' ) )
	);
}
