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
 * Kurumsal sayfalar (Hakkimizda acilir menusu). Yalnizca var olanlar.
 *
 * @return array<int, array{slug:string, label:string, url:string}>
 */
function wk_corporate_pages(): array {
	return wk_existing_pages(
		array(
			'sirketimiz'              => 'Hikayemiz',
			'surdurulebilirlik'       => 'Sürdürülebilirlik',
			'politikalar'             => 'Kurumsal Politikalar',
			'sss'                     => 'Sıkça Sorulan Sorular',
			'iletisim'                => 'Çözüm Merkezi',
			'ozel-uretim-talep-formu' => 'Özel Üretim',
		)
	);
}

/**
 * Alt bilgideki yasal sayfalar.
 */
function wk_legal_pages(): array {
	return wk_existing_pages(
		array(
			'odeme-teslimat'            => 'Ödeme ve Teslimat',
			'iptal-iade-kosullari'      => 'İptal ve İade Koşulları',
			'mesafeli-satis-sozlesmesi' => 'Mesafeli Satış Sözleşmesi',
			'kvkk'                      => 'KVKK Aydınlatma Metni',
			'gizlilik-politikasi'       => 'Gizlilik Politikası',
			'cookies'                   => 'Çerez Politikası',
			'kullanim-kosullari'        => 'Koşullar ve Fikri Mülkiyet',
		)
	);
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
