<?php
/**
 * Menu verisi. Ust menu panelden (global.header.menu) gelir; iki ogeye acilir
 * menu kendiliginden eklenir:
 *   - adresi /magaza/ olan oge: serilerin ve alt kategorilerin buyuk menusu
 *     (Urun Havuzu'ndaki kategorilerden),
 *   - adresi /sirketimiz/ olan oge: kurumsal sayfalar (wk_corporate_pages).
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<int, array{label:string, url:string, kind:string, current:bool}>
 */
function wk_nav(): array {
	$items   = array();
	$request = untrailingslashit( (string) wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH ) );

	foreach ( nwcs_rows( 'global', 'header', 'menu' ) as $row ) {
		$url  = wk_link( $row['url'] ?? '' );
		$path = untrailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) );
		$home = untrailingslashit( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ) );
		$kind = '';

		if ( str_ends_with( $path, '/magaza' ) ) {
			$kind = 'shop';
		} elseif ( str_ends_with( $path, '/sirketimiz' ) ) {
			$kind = 'about';
		}

		$current = $path === $request
			|| ( 'shop' === $kind && ( str_contains( $request, '/urun-kategori/' ) || str_contains( $request, '/urun/' ) ) )
			|| ( 'about' === $kind && in_array( basename( $request ), wp_list_pluck( wk_corporate_pages(), 'slug' ), true ) );

		$items[] = array(
			'label'   => (string) ( $row['label'] ?? '' ),
			'url'     => $url,
			'kind'    => $kind,
			'current' => $current && ( $path !== $home || $request === $home ),
		);
	}

	return $items;
}

/**
 * Kurumsal sayfalar (Hakkimizda acilir menusu, alt bilgideki Kurumsal).
 *
 * @return array<int, array{row:int, slug:string, label:string, url:string}>
 */
function wk_corporate_pages(): array {
	$out = array();

	// Panelden: Tum Sayfalar -> Ust Menu -> Hakkimizda acilir menusu.
	foreach ( nwcs_rows( 'global', 'header', 'about_menu' ) as $index => $row ) {
		$label = trim( (string) ( $row['label'] ?? '' ) );
		$url   = wk_link( $row['url'] ?? '' );

		if ( '' === $label || '#' === $url ) {
			continue;
		}

		$out[] = array(
			'row'   => (int) $index, // Panel onizlemesinde tiklaninca bu satir acilir.
			'slug'  => basename( untrailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) ) ),
			'label' => $label,
			'url'   => $url,
		);
	}

	return $out;
}

/**
 * Alt bilgideki yasal sayfalar.
 */
function wk_legal_pages(): array {
	// Kisaltma => panel alani (Tum Sayfalar -> Yasal Sayfa Adlari).
	$fields = array(
		'odeme-teslimat'            => 'odeme_teslimat',
		'iptal-iade-kosullari'      => 'iptal_iade',
		'mesafeli-satis-sozlesmesi' => 'mesafeli',
		'kvkk'                      => 'kvkk',
		'gizlilik-politikasi'       => 'gizlilik',
		'cookies'                   => 'cookies',
		'kullanim-kosullari'        => 'kullanim',
	);

	$pages = wk_existing_pages( array_map( static fn( string $field ): string => (string) nwcs_field( 'global', 'legal', $field ), $fields ) );

	foreach ( $pages as $i => $page ) {
		$pages[ $i ]['field'] = $fields[ $page['slug'] ];
	}

	return $pages;
}

/**
 * Onizlemede menu ogesinin panel alani: kurumsal sayfalar Hakkimizda acilir
 * menusunun satiri, yasal sayfalar Yasal Sayfa Adlari'ndaki alan.
 */
function wk_page_link_attr( array $page ): void {
	if ( isset( $page['row'] ) ) {
		nwcs_edit_attr( 'global', 'header', 'about_menu', (int) $page['row'], 'label' );
	} elseif ( isset( $page['field'] ) ) {
		nwcs_edit_attr( 'global', 'legal', $page['field'] );
	}
}

function wk_existing_pages( array $wanted ): array {
	$out = array();

	foreach ( $wanted as $slug => $label ) {
		$page = get_page_by_path( $slug );

		if ( $page && 'publish' === $page->post_status ) {
			$out[] = array(
				'slug'  => $slug,
				'label' => $label,
				'url'   => (string) get_permalink( $page ),
			);
		}
	}

	return $out;
}

function wk_page_url( string $slug ): string {
	$page = get_page_by_path( $slug );

	return $page ? (string) get_permalink( $page ) : home_url( '/' . $slug . '/' );
}
