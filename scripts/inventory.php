<?php
/**
 * Alan manifestinden Markdown envanteri uretir.
 *
 *   wp eval-file /scripts/inventory.php --url=http://localhost:8080/kocist/ --quiet
 *
 * Cikti CONTENT-INVENTORY.md dosyasina eklenir; boylece envanter elle degil
 * dogrudan koddan uretilir.
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

$manifest = nwcs_manifest();

if ( empty( $manifest['pages'] ) ) {
	WP_CLI::error( 'Manifest bulunamadi.' );
}

$types = array(
	'text'     => 'tek satır metin',
	'textarea' => 'çok satırlı metin',
	'url'      => 'bağlantı',
	'image'    => 'görsel (medya kaydı + alt metin)',
	'icon'     => 'ikon (sınırlı listeden)',
	'repeater' => 'tekrarlı satırlar',
	'products' => 'merkezî ürün havuzundan seçim (kip + site istisnaları)',
);

$out   = array();
$out[] = '## ' . $manifest['site_label'] . ' (`' . $manifest['site_key'] . '`)';
$out[] = '';

$total = 0;

foreach ( $manifest['pages'] as $page_key => $page ) {
	$out[] = '### Sayfa: ' . $page['label'] . '  `' . $page_key . '`';
	$out[] = '';

	$sortable = nwcs_sortable_sections( $manifest, $page_key );

	if ( $sortable ) {
		$out[] = 'Sıralanabilir bölümler: `' . implode( '`, `', $sortable ) . '` (header ve footer sabittir).';
		$out[] = '';
	}

	foreach ( $page['components'] as $component_key => $component ) {
		$out[] = '**' . $component['label'] . '** — `' . $page_key . ' / ' . $component_key . '`';
		$out[] = '';
		$out[] = '| Alan anahtarı | Türkçe etiket | Tür |';
		$out[] = '| --- | --- | --- |';

		foreach ( $component['fields'] as $field_key => $definition ) {
			++$total;
			$type  = $definition['type'] ?? 'text';
			$label = $types[ $type ] ?? $type;

			if ( 'repeater' === $type ) {
				$sub = array();
				foreach ( $definition['fields'] ?? array() as $sub_key => $sub_def ) {
					++$total;
					$sub[] = '`' . $sub_key . '` (' . ( $types[ $sub_def['type'] ?? 'text' ] ?? '' ) . ')';
				}
				$label .= ' → ' . implode( ', ', $sub );
			}

			$out[] = '| `' . $field_key . '` | ' . $definition['label'] . ' | ' . $label . ' |';
		}

		$out[] = '';
	}
}

$out[] = '_Toplam düzenlenebilir alan (tekrarlı satır alanları dahil): ' . $total . '._';
$out[] = '';

WP_CLI::line( implode( "\n", $out ) );
