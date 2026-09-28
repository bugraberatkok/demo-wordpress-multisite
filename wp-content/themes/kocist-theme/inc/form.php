<?php
/**
 * Iletisim / teklif formu gonderimi.
 *
 * Gonderilen form kaydedilir (yonetimde "Teklif Talepleri"), Network Content
 * Studio bildirim e-postasini gonderir (includes/forms.php; alici
 * info@kocist.com.tr, kocist_form_recipient). Urunden gelinmisse ?urun= slug'i
 * gizli alanla tasinir ve sunucuda urun listesinde aranir; bulunamazsa
 * yok sayilir.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'kocist_register_quote_cpt' );
function kocist_register_quote_cpt(): void {
	register_post_type(
		'kc_quote',
		array(
			'labels'          => array(
				'name'          => 'Teklif Talepleri',
				'singular_name' => 'Teklif Talebi',
				'menu_name'     => 'Teklif Talepleri',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
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

// Bildirim e-postasi: eklentinin form listesine bu tema da eklenir.
add_filter(
	'nwcs_form_post_types',
	static function ( $types ) {
		$types['kc_quote'] = '_kc_';

		return $types;
	}
);

// Bildirim alicisi her sitede ortak adres; paneldeki SEO firma e-postasindan bagimsiz.
add_filter( 'nwcs_form_recipient', 'kocist_form_recipient' );
function kocist_form_recipient(): string {
	return 'info@kocist.com.tr';
}

/**
 * Yonlendirme sonrasi durumu tasiyan gecici kayit. Anahtar kucuk harfli
 * onaltilik (harf duyarli nesne onbelleginde kaybolmasin).
 */
function kocist_quote_redirect( string $redirect, array $state ): void {
	$token = bin2hex( random_bytes( 10 ) );

	set_transient( 'kc_quote_' . $token, $state, 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( add_query_arg( 'kc', $token, $redirect ) . '#teklif-formu' );
	exit;
}

add_action( 'admin_post_kc_quote', 'kocist_handle_quote' );
add_action( 'admin_post_nopriv_kc_quote', 'kocist_handle_quote' );
function kocist_handle_quote(): void {
	$fallback = home_url( '/iletisim/' );
	$referer  = wp_get_referer();
	$redirect = $referer ? wp_validate_redirect( $referer, $fallback ) : $fallback;
	$redirect = strtok( remove_query_arg( 'kc', '' !== $redirect ? $redirect : $fallback ), '#' );

	// Bot tuzagi: gorunmeyen alan dolmussa sessizce geri don.
	if ( ! empty( $_POST['kc_website'] ) ) {
		wp_safe_redirect( $redirect );
		exit;
	}

	$values = array(
		'name'    => sanitize_text_field( wp_unslash( $_POST['kc-name'] ?? '' ) ),
		'phone'   => sanitize_text_field( wp_unslash( $_POST['kc-phone'] ?? '' ) ),
		// Ham deger: gecersiz adres sessizce silinmesin; asagida is_email ile reddedilir.
		'email'   => trim( sanitize_text_field( wp_unslash( $_POST['kc-email'] ?? '' ) ) ),
		'subject' => sanitize_text_field( wp_unslash( $_POST['kc-subject'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $_POST['kc-detail'] ?? '' ) ),
	);

	$errors = array();

	if ( ! isset( $_POST['kc_quote_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['kc_quote_nonce'] ), 'kc_quote' ) ) {
		$errors['form'] = 'Form oturumu zaman aşımına uğradı. Lütfen tekrar gönderin.';
	}

	if ( '' === $values['name'] ) {
		$errors['name'] = 'Adınızı yazın.';
	}

	// Donus yapabilmek icin telefon ya da e-posta: ikisinden biri yeterli.
	if ( '' !== $values['email'] && ! is_email( $values['email'] ) ) {
		$errors['email'] = 'E-posta adresi geçerli görünmüyor.';
	} elseif ( '' === $values['email'] && '' === $values['phone'] ) {
		$errors['phone'] = 'Size dönebilmemiz için telefon ya da e-posta yazın.';
	}

	if ( '' === $values['message'] ) {
		$errors['message'] = 'Aradığınız ürünü veya sorunuzu yazın.';
	}

	if ( $errors ) {
		kocist_quote_redirect( $redirect, array( 'errors' => $errors, 'values' => $values ) );
	}

	// Urun: yalnizca sitedeki urun listesinde bulunan slug kabul edilir.
	$product = '';
	$slug    = sanitize_title( wp_unslash( $_POST['kc_product'] ?? '' ) );

	if ( '' !== $slug && function_exists( 'kocist_catalog_products' ) ) {
		foreach ( kocist_catalog_products() as $candidate ) {
			if ( (string) ( $candidate['slug'] ?? '' ) === $slug ) {
				$context = kocist_product_context( $candidate );
				$product = trim( $context['name'] . ( '' !== $context['category'] ? ' (' . $context['category'] . ')' : '' ) );
				break;
			}
		}
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'kc_quote',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s — %s', $values['name'], '' !== $product ? $product : ( $values['subject'] ?: 'İletişim formu' ) ),
			'post_content' => sprintf(
				"Telefon: %s\nE-posta: %s\nKonu: %s\nÜrün: %s\n\nMesaj:\n%s",
				$values['phone'] ?: '—',
				$values['email'] ?: '—',
				$values['subject'] ?: '—',
				$product ?: '—',
				$values['message']
			),
			'meta_input'   => array(
				'_kc_name'    => $values['name'],
				'_kc_phone'   => $values['phone'],
				'_kc_email'   => $values['email'],
				'_kc_subject' => $values['subject'],
				'_kc_product' => $product,
				'_kc_message' => $values['message'],
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		kocist_quote_redirect(
			$redirect,
			array(
				'errors' => array( 'form' => 'Kayıt sırasında bir sorun oldu. Lütfen telefonla ulaşın.' ),
				'values' => $values,
			)
		);
	}

	kocist_quote_redirect( $redirect, array( 'success' => true ) );
}

/**
 * Yonlendirme sonrasi form durumu: hatalar, girilen degerler, basari.
 */
function kocist_quote_state(): array {
	$empty = array( 'errors' => array(), 'values' => array(), 'success' => false );
	$token = isset( $_GET['kc'] ) ? sanitize_key( wp_unslash( $_GET['kc'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! preg_match( '/^[a-f0-9]{20}$/', $token ) ) {
		return $empty;
	}

	$state = get_transient( 'kc_quote_' . $token );

	if ( ! is_array( $state ) ) {
		return $empty;
	}

	return array(
		'errors'  => $state['errors'] ?? array(),
		'values'  => $state['values'] ?? array(),
		'success' => ! empty( $state['success'] ),
	);
}
