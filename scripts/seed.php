<?php
/**
 * Demo icerik seed'i (alt site basina).
 *
 * Calistirma:
 *   wp eval-file /scripts/seed.php --url=http://localhost:8080/kocist/
 *
 * Tekrar calistirilabilir: ayni gorselleri yeniden uretmez, alan degerlerini
 * manifest varsayilanlarina geri yazar ve urun havuzu secimini demo haline
 * dondurur. Tum degerler ilgili alt sitenin WordPress kaydina yazilir.
 *
 * Urunlerin kendisi bu betikte olusturulmaz; onlar merkezi havuzdadir:
 *   wp eval-file /scripts/seed-products.php --url=http://localhost:8080/
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

require_once __DIR__ . '/seed-lib.php';

if ( ! function_exists( 'nwcs_manifest' ) ) {
	WP_CLI::error( 'Network Content Studio eklentisi etkin degil.' );
}

$manifest = nwcs_manifest();

if ( empty( $manifest['pages'] ) ) {
	WP_CLI::error( 'Bu site icin alan manifesti bulunamadi (tema: ' . get_option( 'stylesheet' ) . ').' );
}

$site_key = (string) ( $manifest['site_key'] ?? '' );

/**
 * Sayfayi olusturur veya mevcut olani dondurur.
 */
function nwcs_seed_page( string $slug, string $title ): int {
	$page = get_page_by_path( $slug );

	if ( $page ) {
		return (int) $page->ID;
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_content' => '',
		)
	);

	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * Havuzdaki urunun kimligini slug ile bulur.
 */
function nwcs_seed_product_id( string $slug ): int {
	foreach ( nwcs_pool_products() as $id => $product ) {
		if ( $product['slug'] === $slug ) {
			return (int) $id;
		}
	}

	return 0;
}

/* ------------------------------------------------------------------ */
/* 1) Manifest varsayilanlarini gercek veri olarak yaz                  */
/* ------------------------------------------------------------------ */

$written = 0;

foreach ( $manifest['pages'] as $page_key => $page ) {
	foreach ( $page['components'] as $component_key => $component ) {
		foreach ( $component['fields'] as $field_key => $definition ) {
			// Urun havuzu alani icerik kaydinda tutulmaz.
			if ( 'products' === ( $definition['type'] ?? '' ) ) {
				continue;
			}

			nwcs_update_field( $page_key, $component_key, $field_key, $definition['default'] ?? '', $manifest );
			++$written;
		}
	}
}

/* ------------------------------------------------------------------ */
/* 2) Ornek gorseller ve siteye ozel ayarlar                            */
/* ------------------------------------------------------------------ */

$content = get_option( NWCS_OPTION_CONTENT, array() );

if ( 'kocist' === $site_key ) {
	update_option( 'blogname', 'Koçist' );
	update_option( 'blogdescription', 'Kereste, ahşap ambalaj, dekorasyon ve hırdavat (yerel demo)' );

	$content['global']['header']['logo_image'] = nwcs_seed_image( 'kocist-logo', 'K', 'Örnek logo yer tutucusu (Koçist demo)', 200, 200, array( 32, 45, 60 ) );
	$content['home']['hero']['image']          = nwcs_seed_image( 'kocist-hero', 'Kereste stok alani', 'Örnek görsel: kereste stok alanı (demo yer tutucusu)', 1200, 900, array( 44, 62, 80 ) );
	$content['inner']['story']['image']        = nwcs_seed_image( 'kocist-kurumsal', 'Uretim tesisi', 'Örnek görsel: üretim tesisi (demo yer tutucusu)', 900, 1200, array( 52, 70, 88 ) );

	nwcs_seed_page( 'kurumsal', 'Kurumsal' );
	$front_id = nwcs_seed_page( 'ana-sayfa', 'Ana Sayfa' );

	// Koçist havuzun tamamını gösterir: yeni ürün eklenince otomatik çıkar.
	nwcs_save_site_product_settings(
		array(
			'mode'      => 'all',
			'selected'  => array(),
			'overrides' => array(),
		)
	);
} elseif ( 'paletci' === $site_key ) {
	update_option( 'blogname', 'İstanbul Paletçi' );
	update_option( 'blogdescription', 'Ahşap palet, sandık ve kafes üretimi (yerel demo)' );

	$content['global']['header']['logo_image'] = nwcs_seed_image( 'paletci-logo', 'P', 'Örnek logo yer tutucusu (İstanbul Paletçi demo)', 200, 200, array( 31, 64, 51 ) );
	$content['home']['hero']['image']          = nwcs_seed_image( 'paletci-hero', 'Palet uretim atolyesi', 'Örnek görsel: palet üretim atölyesi (demo yer tutucusu)', 1400, 600, array( 31, 64, 51 ) );
	$content['home']['why']['image']           = nwcs_seed_image( 'paletci-atolye', 'Atolye', 'Örnek görsel: atölye (demo yer tutucusu)', 1000, 800, array( 44, 87, 68 ) );
	$content['inner']['intro']['image']        = nwcs_seed_image( 'paletci-urunler', 'Urun grubu', 'Örnek görsel: ürün grubu (demo yer tutucusu)', 1000, 750, array( 122, 79, 52 ) );

	nwcs_seed_page( 'urunlerimiz', 'Ürünlerimiz' );
	$front_id = nwcs_seed_page( 'anasayfa', 'Anasayfa' );

	// Paletçi havuzdan yalnızca palet grubunu seçer ve iki istisna kullanır:
	//  - "Euro Palet" bu sitede farklı adla görünür
	//  - "İkinci El Palet" fiyatı bu sitede boş bırakılır -> "Teklif al"
	$selected = array_values(
		array_filter(
			array_map(
				'nwcs_seed_product_id',
				array( 'ahsap-palet', 'euro-palet', 'ahsap-kafes', 'ahsap-sandik', 'ikinci-el-palet' )
			)
		)
	);

	$overrides = array();

	$euro = nwcs_seed_product_id( 'euro-palet' );
	if ( $euro ) {
		$overrides[ $euro ] = array(
			'title'          => 'Euro Palet (ihracat)',
			'short'          => 'İhracat sevkiyatları için 80 × 120 cm standart palet.',
			'price'          => '',
			'image'          => 0,
			'hidden'         => 0,
			'price_override' => 0,
		);
	}

	$used = nwcs_seed_product_id( 'ikinci-el-palet' );
	if ( $used ) {
		$overrides[ $used ] = array(
			'title'          => '',
			'short'          => '',
			'price'          => '',
			'image'          => 0,
			'hidden'         => 0,
			'price_override' => 1,
		);
	}

	nwcs_save_site_product_settings(
		array(
			'mode'      => 'selected',
			'selected'  => $selected,
			'overrides' => $overrides,
		)
	);
} else {
	WP_CLI::error( 'Bilinmeyen site anahtari: ' . $site_key );
}

update_option( NWCS_OPTION_CONTENT, $content );

/* ------------------------------------------------------------------ */
/* 3) Bolum sirasi, sayfa ayarlari, kalici baglantilar                   */
/* ------------------------------------------------------------------ */

nwcs_set_section_order( 'home', nwcs_sortable_sections( $manifest, 'home' ), $manifest );

if ( ! empty( $front_id ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front_id );
}

update_option( 'timezone_string', 'Europe/Istanbul' );

// /urun/<slug>/ kuralinin devreye girmesi icin.
flush_rewrite_rules( false );

WP_CLI::success(
	sprintf(
		'%s seed tamamlandi: %d alan yazildi, urun secimi ayarlandi.',
		$manifest['site_label'] ?? $site_key,
		$written
	)
);
