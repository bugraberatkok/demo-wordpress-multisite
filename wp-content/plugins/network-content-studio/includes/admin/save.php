<?php
/**
 * Panel kaydetme islemi.
 *
 * Guvenlik zinciri: yetki -> nonce -> hedef sitenin ag icinde ve manifestli
 * oldugunun dogrulanmasi -> switch_to_blog -> tur bazli temizleme -> tek
 * option yazimi -> restore_current_blog.
 *
 * Yalnizca manifestte tanimli alanlar yazilir; POST'tan gelen fazlalik
 * anahtarlar sessizce yok sayilir.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_post_nwcs_save', 'nwcs_handle_save' );
function nwcs_handle_save(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	$blog_id       = isset( $_POST['site'] ) ? absint( $_POST['site'] ) : 0;
	$page_key      = isset( $_POST['content_page'] ) ? sanitize_key( wp_unslash( $_POST['content_page'] ) ) : '';
	$component_key = isset( $_POST['component'] ) ? sanitize_key( wp_unslash( $_POST['component'] ) ) : '';

	check_admin_referer( 'nwcs_save_' . $blog_id . '_' . $page_key . '_' . $component_key );

	$sites = nwcs_editable_sites();

	if ( ! isset( $sites[ $blog_id ] ) ) {
		nwcs_redirect_error( $blog_id, $page_key, $component_key, 'Geçersiz site seçimi.' );
	}

	$manifest = $sites[ $blog_id ]['manifest'];
	$fields   = $manifest['pages'][ $page_key ]['components'][ $component_key ]['fields'] ?? null;

	if ( null === $fields ) {
		nwcs_redirect_error( $blog_id, $page_key, $component_key, 'Bu bileşen manifestte bulunamadı.' );
	}

	$posted = isset( $_POST['fields'] ) && is_array( $_POST['fields'] )
		? wp_unslash( $_POST['fields'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- tur bazli temizleme asagida.
		: array();

	switch_to_blog( $blog_id );

	$media_updates = 0;
	$data          = nwcs_get_all();

	foreach ( $fields as $field_key => $definition ) {
		$type = $definition['type'] ?? 'text';

		if ( 'repeater' === $type ) {
			$value = nwcs_collect_repeater( $field_key, $definition, $posted[ $field_key ] ?? array(), $media_updates );
		} elseif ( 'image' === $type ) {
			$value = nwcs_collect_image( $field_key, (int) ( $posted[ $field_key ] ?? 0 ), $media_updates );
		} else {
			$value = $posted[ $field_key ] ?? '';
		}

		$data[ $page_key ][ $component_key ][ $field_key ] = nwcs_sanitize_value( $value, $definition );
	}

	update_option( NWCS_OPTION_CONTENT, $data );

	restore_current_blog();

	$args = array(
		'nwcs_saved' => count( $fields ),
		'nwcs_media' => $media_updates,
	);

	$errors = nwcs_add_save_error( '' );

	if ( $errors ) {
		$args['nwcs_error'] = rawurlencode( implode( ' ', $errors ) );
	}

	wp_safe_redirect( add_query_arg( $args, nwcs_panel_url( $blog_id, $page_key, $component_key ) ) );
	exit;
}

/**
 * Tekrarli alan satirlarini toplar: _sort'a gore siralar, gorselleri isler.
 *
 * Satir anahtarlari kullanici tarafindan belirlenmez; yalnizca siralama ve
 * alt alan degerleri kullanilir, cikti her zaman yeniden indekslenir.
 */
function nwcs_collect_repeater( string $field_key, array $definition, $posted_rows, int &$media_updates ): array {
	if ( ! is_array( $posted_rows ) ) {
		return array();
	}

	$sub_defs = $definition['fields'] ?? array();
	$rows     = array();

	foreach ( $posted_rows as $row_id => $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$sort  = isset( $row['_sort'] ) ? (int) $row['_sort'] : count( $rows );
		$clean = array();

		foreach ( $sub_defs as $sub_key => $sub_def ) {
			if ( 'image' === ( $sub_def['type'] ?? '' ) ) {
				$clean[ $sub_key ] = nwcs_collect_image(
					nwcs_field_path( $field_key, (string) $row_id, $sub_key ),
					(int) ( $row[ $sub_key ] ?? 0 ),
					$media_updates
				);
			} else {
				$clean[ $sub_key ] = $row[ $sub_key ] ?? '';
			}
		}

		// Tamamen bos satirlar kaydedilmez (kullanici satir ekleyip doldurmazsa).
		$has_value = false;
		foreach ( $clean as $sub_value ) {
			if ( '' !== $sub_value && 0 !== $sub_value && '0' !== $sub_value ) {
				$has_value = true;
				break;
			}
		}

		if ( ! $has_value ) {
			continue;
		}

		$rows[] = array(
			'_sort' => $sort,
			'row'   => $clean,
		);
	}

	usort(
		$rows,
		static function ( array $a, array $b ): int {
			return $a['_sort'] <=> $b['_sort'];
		}
	);

	return array_column( $rows, 'row' );
}

/**
 * Gorsel alani: yeni dosya varsa yukler, yoksa secilen kaydi kullanir.
 * Alt metin her durumda medya kaydina yazilir.
 *
 * Aktif site baglami cagiran tarafindan (switch_to_blog) saglanir; boylece
 * yukleme dogru alt sitenin uploads klasorune gider.
 */
function nwcs_collect_image( string $path, int $selected_id, int &$media_updates ): int {
	$upload_key = 'img_upload__' . $path;
	$alt_key    = 'img_alt__' . $path;

	$attachment_id = $selected_id;

	if ( isset( $_FILES[ $upload_key ] ) && is_array( $_FILES[ $upload_key ] ) && UPLOAD_ERR_NO_FILE !== (int) ( $_FILES[ $upload_key ]['error'] ?? UPLOAD_ERR_NO_FILE ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$uploaded = media_handle_upload( $upload_key, 0 );

		if ( is_wp_error( $uploaded ) ) {
			// Yukleme basarisizsa mevcut secim korunur ve kullaniciya soylenir;
			// sahte basari gosterilmez.
			nwcs_add_save_error( 'Görsel yüklenemedi: ' . $uploaded->get_error_message() );
		} else {
			$attachment_id = (int) $uploaded;
			++$media_updates;
		}
	}

	if ( $attachment_id > 0 && isset( $_POST[ $alt_key ] ) ) {
		$alt = sanitize_text_field( wp_unslash( $_POST[ $alt_key ] ) );

		if ( $alt !== (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
			++$media_updates;
		}
	}

	return $attachment_id;
}

/**
 * Kaydetme sirasinda olusan hatalari biriktirir (or. basarisiz gorsel yukleme).
 */
function nwcs_add_save_error( string $message ): array {
	static $errors = array();

	if ( '' !== $message ) {
		$errors[] = $message;
	}

	return $errors;
}

/**
 * Hata ile panele geri doner.
 */
function nwcs_redirect_error( int $blog_id, string $page_key, string $component_key, string $message ): void {
	wp_safe_redirect(
		add_query_arg(
			array( 'nwcs_error' => rawurlencode( $message ) ),
			nwcs_panel_url( $blog_id, $page_key, $component_key )
		)
	);
	exit;
}
