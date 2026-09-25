<?php
/**
 * Siparis formu. Sahte basari yok: kayit yonetimde "Siparis Istekleri"
 * altina duser, Network Content Studio bildirim e-postasini gonderir.
 * Urun secenekleri sitede gosterilen havuz urunlerinden gelir.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'wk_register_order_cpt' );
function wk_register_order_cpt(): void {
	register_post_type(
		'wk_order',
		array(
			'labels'          => array(
				'name'          => 'Sipariş İstekleri',
				'singular_name' => 'Sipariş İsteği',
				'menu_name'     => 'Sipariş İstekleri',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-cart',
			'menu_position'   => 26,
			'supports'        => array( 'title', 'editor', 'custom-fields' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'has_archive'     => false,
			'rewrite'         => false,
			'query_var'       => false,
		)
	);
}

add_filter(
	'nwcs_form_post_types',
	static function ( $types ) {
		$types['wk_order'] = '_wk_';

		return $types;
	}
);

function wk_order_redirect( string $redirect, array $state ): void {
	$token = bin2hex( random_bytes( 10 ) );

	set_transient( 'wk_order_' . $token, $state, 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( add_query_arg( 'wk', $token, $redirect ) . '#siparis' );
	exit;
}

add_action( 'admin_post_wk_order', 'wk_handle_order' );
add_action( 'admin_post_nopriv_wk_order', 'wk_handle_order' );
function wk_handle_order(): void {
	$fallback = home_url( '/iletisim/' );
	$referer  = wp_get_referer();
	$redirect = $referer ? wp_validate_redirect( $referer, $fallback ) : $fallback;
	$redirect = strtok( remove_query_arg( 'wk', '' !== $redirect ? $redirect : $fallback ), '#' );

	if ( ! empty( $_POST['wk_website'] ) ) {
		wp_safe_redirect( $redirect );
		exit;
	}

	$values = array(
		'name'    => sanitize_text_field( wp_unslash( $_POST['wk_name'] ?? '' ) ),
		'phone'   => sanitize_text_field( wp_unslash( $_POST['wk_phone'] ?? '' ) ),
		'email'   => sanitize_email( wp_unslash( $_POST['wk_email'] ?? '' ) ),
		'product' => sanitize_text_field( wp_unslash( $_POST['wk_product'] ?? '' ) ),
		'size'    => sanitize_text_field( wp_unslash( $_POST['wk_size'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $_POST['wk_message'] ?? '' ) ),
	);

	$errors = array();

	if ( ! isset( $_POST['wk_order_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wk_order_nonce'] ), 'wk_order' ) ) {
		$errors['form'] = 'Form oturumu zaman aşımına uğradı. Lütfen tekrar gönderin.';
	}

	if ( '' === $values['name'] ) {
		$errors['name'] = 'Adınızı yazın.';
	}

	if ( '' !== $values['email'] && ! is_email( $values['email'] ) ) {
		$errors['email'] = 'E-posta adresi geçerli görünmüyor.';
	} elseif ( '' === $values['email'] && '' === $values['phone'] ) {
		$errors['phone'] = 'Size dönebilmemiz için telefon ya da e-posta yazın.';
	}

	if ( $errors ) {
		// Oturum (nonce) hatasinda ziyaretci verisi saklanmaz: botlarin her
		// istegi gecici kayit olarak birikmesin.
		wk_order_redirect( $redirect, array( 'errors' => $errors, 'values' => isset( $errors['form'] ) ? array() : $values ) );
	}

	// Urun: yalnizca sitede gosterilen urunlerden biri ("KOD Ad").
	$allowed = array_map( static fn( array $p ): string => trim( $p['code'] . ' ' . $p['title'] ), wk_products() );

	if ( ! in_array( $values['product'], $allowed, true ) ) {
		$values['product'] = 'Belirtilmedi';
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'wk_order',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s — %s', $values['name'], $values['product'] ),
			'post_content' => sprintf(
				"Telefon: %s\nE-posta: %s\nÜrün: %s\nAdet ve teslim ili: %s\n\nMesaj:\n%s",
				$values['phone'] ?: '—',
				$values['email'] ?: '—',
				$values['product'],
				$values['size'] ?: '—',
				$values['message'] ?: '—'
			),
			'meta_input'   => array(
				'_wk_name'    => $values['name'],
				'_wk_phone'   => $values['phone'],
				'_wk_email'   => $values['email'],
				'_wk_product' => $values['product'],
				'_wk_size'    => $values['size'],
				'_wk_message' => $values['message'],
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		wk_order_redirect( $redirect, array( 'errors' => array( 'form' => 'Kayıt sırasında bir sorun oldu. Lütfen WhatsApp’tan ulaşın.' ), 'values' => $values ) );
	}

	wk_order_redirect( $redirect, array( 'success' => true ) );
}

function wk_order_state(): array {
	$empty = array( 'errors' => array(), 'values' => array(), 'success' => false );
	$token = isset( $_GET['wk'] ) ? sanitize_key( wp_unslash( $_GET['wk'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! preg_match( '/^[a-f0-9]{20}$/', $token ) ) {
		return $empty;
	}

	$state = get_transient( 'wk_order_' . $token );

	return is_array( $state ) ? array_merge( $empty, $state, array( 'success' => ! empty( $state['success'] ) ) ) : $empty;
}
