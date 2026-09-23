<?php
/**
 * WordPress Abilities API kayitlari (WordPress 6.9+ cekirdegi).
 *
 * Amac: Claude Code'un MCP uzerinden bu demoyu yonetebilmesi icin **en az
 * yetkili** yuzeyi acmak. Cekirdek yalnizca uc okuma yetenegi kaydeder
 * (core/get-site-info, core/get-user-info, core/get-environment-info);
 * icerik yazma yetenegi yoktur. Asagidakiler bu demo icin bilincli olarak
 * tanimlanmistir:
 *
 *   nwcs/list-sites     (okuma)  demo sitelerini listeler
 *   nwcs/describe-site  (okuma)  bir sitenin duzenlenebilir alanlarini verir
 *   nwcs/get-field      (okuma)  tek bir alanin degerini verir
 *   nwcs/update-field   (yazma)  tek bir alani gunceller
 *
 * Yazma korumalari:
 *   - yalnizca `manage_network_options` yetkisi
 *   - yalnizca ag icindeki, manifesti olan demo siteleri
 *   - yalnizca yerel adresler (localhost / 127.0.0.1 / .test / .local)
 *   - yalnizca manifestte tanimli alanlar; gorsel alanlari disarida
 *   - deger, panelle ayni tur bazli temizlemeden gecer
 */

defined( 'ABSPATH' ) || exit;

const NWCS_ABILITY_CATEGORY = 'nwcs-content';

add_action( 'wp_abilities_api_categories_init', 'nwcs_register_ability_category' );
function nwcs_register_ability_category(): void {
	wp_register_ability_category(
		NWCS_ABILITY_CATEGORY,
		array(
			'label'       => 'İçerik Stüdyosu',
			'description' => 'Ağdaki demo sitelerin düzenlenebilir içerik alanları.',
		)
	);
}

/**
 * Yalnizca yerel demo adresleri; canli site adreslerine yazma yapilmaz.
 */
function nwcs_is_local_site( int $blog_id ): bool {
	$host = wp_parse_url( get_home_url( $blog_id, '/' ), PHP_URL_HOST );

	if ( ! $host ) {
		return false;
	}

	return 'localhost' === $host
		|| '127.0.0.1' === $host
		|| str_ends_with( $host, '.test' )
		|| str_ends_with( $host, '.local' )
		|| str_ends_with( $host, '.localhost' );
}

/**
 * Site kimligini cozer: blog_id veya yol adi ("ahsapkasa", "/istanbulpaletci/").
 */
function nwcs_resolve_site( $site ) {
	$sites = nwcs_editable_sites();

	if ( is_numeric( $site ) ) {
		$blog_id = (int) $site;

		return isset( $sites[ $blog_id ] ) ? $blog_id : new WP_Error( 'nwcs_site', 'Bu kimlikte bir demo sitesi yok.' );
	}

	$needle = trim( strtolower( (string) $site ), '/ ' );

	foreach ( $sites as $blog_id => $data ) {
		if ( trim( strtolower( $data['path'] ), '/' ) === $needle ) {
			return (int) $blog_id;
		}

		if ( strtolower( $data['manifest']['site_key'] ?? '' ) === $needle ) {
			return (int) $blog_id;
		}
	}

	return new WP_Error( 'nwcs_site', 'Site bulunamadı: ' . $needle );
}

/**
 * Yetki kontrolu: tum yeteneklerde ayni.
 */
function nwcs_ability_permission(): bool {
	return current_user_can( NWCS_CAPABILITY );
}

add_action( 'wp_abilities_api_init', 'nwcs_register_abilities' );
function nwcs_register_abilities(): void {

	wp_register_ability(
		'nwcs/list-sites',
		array(
			'label'               => 'Demo sitelerini listele',
			'description'         => 'Ağdaki düzenlenebilir demo sitelerini (kimlik, ad, adres) döndürür.',
			'category'            => NWCS_ABILITY_CATEGORY,
			'permission_callback' => 'nwcs_ability_permission',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(),
			),
			'output_schema'       => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'id'    => array( 'type' => 'integer' ),
						'key'   => array( 'type' => 'string' ),
						'label' => array( 'type' => 'string' ),
						'url'   => array( 'type' => 'string' ),
					),
				),
			),
			'meta'                => array( 'readonly' => true ),
			'execute_callback'    => static function (): array {
				$out = array();

				foreach ( nwcs_editable_sites() as $blog_id => $site ) {
					$out[] = array(
						'id'    => (int) $blog_id,
						'key'   => (string) ( $site['manifest']['site_key'] ?? trim( $site['path'], '/' ) ),
						'label' => (string) $site['label'],
						'url'   => (string) $site['url'],
					);
				}

				return $out;
			},
		)
	);

	wp_register_ability(
		'nwcs/describe-site',
		array(
			'label'               => 'Sitenin düzenlenebilir alanlarını listele',
			'description'         => 'Seçili sitenin sayfa → bileşen → alan yapısını, Türkçe etiketleri ve alan türleriyle döndürür.',
			'category'            => NWCS_ABILITY_CATEGORY,
			'permission_callback' => 'nwcs_ability_permission',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'site' => array(
						'type'        => 'string',
						'description' => 'Site anahtarı ("ahsapkasa", "istanbulpaletci") veya sayısal kimlik.',
					),
				),
				'required'   => array( 'site' ),
			),
			'output_schema'       => array( 'type' => 'object' ),
			'meta'                => array( 'readonly' => true ),
			'execute_callback'    => static function ( array $input ) {
				$blog_id = nwcs_resolve_site( $input['site'] ?? '' );

				if ( is_wp_error( $blog_id ) ) {
					return $blog_id;
				}

				$manifest = nwcs_manifest_for_blog( $blog_id );
				$pages    = array();

				foreach ( $manifest['pages'] as $page_key => $page ) {
					$components = array();

					foreach ( $page['components'] as $component_key => $component ) {
						$fields = array();

						foreach ( $component['fields'] as $field_key => $definition ) {
							$entry = array(
								'label' => $definition['label'],
								'type'  => $definition['type'] ?? 'text',
							);

							if ( 'repeater' === $entry['type'] ) {
								$entry['row_fields'] = array_map(
									static fn( $sub ) => array(
										'label' => $sub['label'] ?? '',
										'type'  => $sub['type'] ?? 'text',
									),
									$definition['fields'] ?? array()
								);
							}

							$fields[ $field_key ] = $entry;
						}

						$components[ $component_key ] = array(
							'label'  => $component['label'],
							'fields' => $fields,
						);
					}

					$pages[ $page_key ] = array(
						'label'             => $page['label'],
						'path'              => $page['path'] ?? '/',
						'sortable_sections' => nwcs_sortable_sections( $manifest, $page_key ),
						'components'        => $components,
					);
				}

				return array(
					'site'  => array(
						'id'    => $blog_id,
						'key'   => $manifest['site_key'] ?? '',
						'label' => $manifest['site_label'] ?? '',
						'url'   => get_home_url( $blog_id, '/' ),
					),
					'pages' => $pages,
				);
			},
		)
	);

	wp_register_ability(
		'nwcs/get-field',
		array(
			'label'               => 'Alan değerini oku',
			'description'         => 'Belirtilen sitedeki bir içerik alanının güncel değerini döndürür.',
			'category'            => NWCS_ABILITY_CATEGORY,
			'permission_callback' => 'nwcs_ability_permission',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'site'      => array( 'type' => 'string' ),
					'page'      => array( 'type' => 'string' ),
					'component' => array( 'type' => 'string' ),
					'field'     => array( 'type' => 'string' ),
					'row'       => array(
						'type'        => 'integer',
						'description' => 'Tekrarlı alanlarda satır numarası (0 tabanlı).',
					),
					'row_field' => array( 'type' => 'string' ),
				),
				'required'   => array( 'site', 'page', 'component', 'field' ),
			),
			'output_schema'       => array( 'type' => 'object' ),
			'meta'                => array( 'readonly' => true ),
			'execute_callback'    => static function ( array $input ) {
				$target = nwcs_ability_target( $input );

				if ( is_wp_error( $target ) ) {
					return $target;
				}

				return array(
					'site'      => $target['blog_id'],
					'page'      => $target['page'],
					'component' => $target['component'],
					'field'     => $target['field'],
					'label'     => $target['definition']['label'],
					'type'      => $target['definition']['type'] ?? 'text',
					'value'     => $target['value'],
				);
			},
		)
	);

	wp_register_ability(
		'nwcs/update-field',
		array(
			'label'               => 'Alan değerini güncelle',
			'description'         => 'Yerel demo sitelerinde, manifestte tanımlı tek bir metin/bağlantı/ikon alanını günceller ve hemen yayınlar.',
			'category'            => NWCS_ABILITY_CATEGORY,
			'permission_callback' => 'nwcs_ability_permission',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'site'      => array( 'type' => 'string' ),
					'page'      => array( 'type' => 'string' ),
					'component' => array( 'type' => 'string' ),
					'field'     => array( 'type' => 'string' ),
					'value'     => array( 'type' => 'string' ),
					'row'       => array( 'type' => 'integer' ),
					'row_field' => array( 'type' => 'string' ),
				),
				'required'   => array( 'site', 'page', 'component', 'field', 'value' ),
			),
			'output_schema'       => array( 'type' => 'object' ),
			'meta'                => array(
				'readonly'    => false,
				'destructive' => false,
			),
			'execute_callback'    => 'nwcs_ability_update_field',
		)
	);
}

/**
 * Girdiyi manifeste gore cozer ve mevcut degeri doner.
 *
 * @return array{blog_id:int,page:string,component:string,field:string,row:?int,row_field:string,definition:array,value:mixed}|WP_Error
 */
function nwcs_ability_target( array $input ) {
	$blog_id = nwcs_resolve_site( $input['site'] ?? '' );

	if ( is_wp_error( $blog_id ) ) {
		return $blog_id;
	}

	$manifest  = nwcs_manifest_for_blog( $blog_id );
	$page      = sanitize_key( (string) ( $input['page'] ?? '' ) );
	$component = sanitize_key( (string) ( $input['component'] ?? '' ) );
	$field     = sanitize_key( (string) ( $input['field'] ?? '' ) );

	$definition = nwcs_field_def( $manifest, $page, $component, $field );

	if ( null === $definition ) {
		return new WP_Error(
			'nwcs_field',
			sprintf( 'Alan manifestte yok: %s / %s / %s. nwcs/describe-site ile listeyi görebilirsiniz.', $page, $component, $field )
		);
	}

	$row       = isset( $input['row'] ) ? (int) $input['row'] : null;
	$row_field = isset( $input['row_field'] ) ? sanitize_key( (string) $input['row_field'] ) : '';

	switch_to_blog( $blog_id );
	$stored = nwcs_raw( $page, $component, $field );
	restore_current_blog();

	$value = $stored ?? ( $definition['default'] ?? '' );

	if ( 'repeater' === ( $definition['type'] ?? '' ) ) {
		if ( null === $row || '' === $row_field ) {
			// Satir belirtilmediyse tum satirlar dondurulur.
			return array(
				'blog_id'    => $blog_id,
				'page'       => $page,
				'component'  => $component,
				'field'      => $field,
				'row'        => null,
				'row_field'  => '',
				'definition' => $definition,
				'value'      => $value,
			);
		}

		if ( ! isset( $definition['fields'][ $row_field ] ) ) {
			return new WP_Error( 'nwcs_row_field', 'Satır alanı manifestte yok: ' . $row_field );
		}

		if ( ! is_array( $value ) || ! isset( $value[ $row ] ) ) {
			return new WP_Error( 'nwcs_row', 'Bu satır yok: ' . $row );
		}

		return array(
			'blog_id'    => $blog_id,
			'page'       => $page,
			'component'  => $component,
			'field'      => $field,
			'row'        => $row,
			'row_field'  => $row_field,
			'definition' => $definition['fields'][ $row_field ],
			'value'      => $value[ $row ][ $row_field ] ?? '',
		);
	}

	return array(
		'blog_id'    => $blog_id,
		'page'       => $page,
		'component'  => $component,
		'field'      => $field,
		'row'        => null,
		'row_field'  => '',
		'definition' => $definition,
		'value'      => $value,
	);
}

/**
 * nwcs/update-field calisma govdesi.
 */
function nwcs_ability_update_field( array $input ) {
	$target = nwcs_ability_target( $input );

	if ( is_wp_error( $target ) ) {
		return $target;
	}

	$blog_id = $target['blog_id'];

	if ( ! nwcs_is_local_site( $blog_id ) ) {
		return new WP_Error( 'nwcs_not_local', 'Bu yetenek yalnızca yerel demo siteleri için çalışır.' );
	}

	$type = $target['definition']['type'] ?? 'text';

	if ( in_array( $type, array( 'image', 'repeater' ), true ) ) {
		return new WP_Error(
			'nwcs_type',
			'image ve repeater alanları bu yetenekle güncellenmez; tekrarlı alanlarda row ve row_field verin, görselleri panelden yükleyin.'
		);
	}

	$manifest = nwcs_manifest_for_blog( $blog_id );

	switch_to_blog( $blog_id );

	$data     = nwcs_get_all();
	$previous = $target['value'];
	$clean    = nwcs_sanitize_value( (string) $input['value'], $target['definition'] );

	if ( null === $target['row'] ) {
		$data[ $target['page'] ][ $target['component'] ][ $target['field'] ] = $clean;
	} else {
		$rows = $data[ $target['page'] ][ $target['component'] ][ $target['field'] ] ?? array();

		if ( ! isset( $rows[ $target['row'] ] ) ) {
			restore_current_blog();

			return new WP_Error( 'nwcs_row', 'Bu satır yok: ' . $target['row'] );
		}

		$rows[ $target['row'] ][ $target['row_field'] ] = $clean;

		$data[ $target['page'] ][ $target['component'] ][ $target['field'] ] = $rows;
	}

	update_option( NWCS_OPTION_CONTENT, $data );

	$preview = get_home_url( $blog_id, $manifest['pages'][ $target['page'] ]['path'] ?? '/' );

	restore_current_blog();

	return array(
		'ok'        => true,
		'site'      => $blog_id,
		'page'      => $target['page'],
		'component' => $target['component'],
		'field'     => $target['field'],
		'row'       => $target['row'],
		'row_field' => $target['row_field'],
		'previous'  => $previous,
		'current'   => $clean,
		'url'       => $preview,
		'note'      => 'Değişiklik yayınlandı; sayfayı yenileyince görünür.',
	);
}
