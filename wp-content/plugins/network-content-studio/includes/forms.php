<?php
/**
 * Form bildirimleri.
 *
 * Temalarin iletisim / teklif formlari gonderimi kendi CPT'lerine ozel
 * kayit olarak yazar (ak_quote, ip_quote, kr_quote, ik_message, sp_message,
 * aas_quote). Bu dosya yeni kayit olusunca site sahibine duz metin e-posta
 * gonderir. Temalara dokunmaz: tek baglanti noktasi wp_after_insert_post.
 *
 * Kurallar:
 *   - Yalnizca yeni kayit, yalnizca listedeki turler, kayit basina bir kez
 *     (_nwcs_notified meta: pending | sent | failed).
 *   - Gonderim shutdown'a ertelenir; yonlendirme ziyaretciye once ulasir.
 *     wp_mail hatasi formun basari durumunu degistirmez, yalnizca error_log.
 *   - Alici: nwcs_form_recipient filtresi; varsayilan SEO sirket e-postasi,
 *     gecersizse admin_email.
 *   - Basliklara giden ad ve adreslerden satir sonlari temizlenir.
 */

defined( 'ABSPATH' ) || exit;

const NWCS_FORM_NOTIFIED_META = '_nwcs_notified';

/**
 * Bildirim gonderilen form turleri: post_type => meta on eki.
 * Filtre duz liste de dondurebilir; on ek "_<ilk parca>_" kabul edilir.
 */
function nwcs_form_post_types(): array {
	$defaults = array(
		'ak_quote'   => '_ak_',   // ahsapkasa-theme
		'ip_quote'   => '_ip_',   // istanbulpaletci-theme
		'kr_quote'   => '_kr_',   // kereste-base (ithalkeresteci, kavakkeresteci)
		'ik_message' => '_ik_',   // istanbul-keresteci-theme
		'sp_message' => '_sp_',   // sanayi-palet-theme
		'aas_quote'  => '_aas_',  // ahsapambalaj-theme
		'pc_quote'   => '_pc_',   // istanbulpaletcivi-theme
	);

	$types = apply_filters( 'nwcs_form_post_types', $defaults );
	$clean = array();

	foreach ( (array) $types as $key => $value ) {
		$type = is_int( $key ) ? sanitize_key( (string) $value ) : sanitize_key( (string) $key );

		if ( '' === $type ) {
			continue;
		}

		$prefix = is_int( $key ) ? '' : (string) $value;

		if ( '' === $prefix ) {
			$prefix = '_' . strtok( $type, '_' ) . '_';
		}

		$clean[ $type ] = $prefix;
	}

	return $clean;
}

/**
 * Bilinen alan adlari icin Turkce etiketler; sira e-postadaki siradir.
 */
function nwcs_form_field_labels(): array {
	return array(
		'name'    => 'Ad Soyad',
		'company' => 'Firma',
		'email'   => 'E-posta',
		'phone'   => 'Telefon',
		'subject' => 'Konu',
		'product' => 'Ürün',
		'size'    => 'Ölçü ve adet',
		'message' => 'Mesaj',
	);
}

/**
 * Istek icindeki bekleyen bildirimler. $add verilirse kuyruga ekler,
 * $take true ise kuyrugu bosaltip dondurur.
 */
function nwcs_form_queue( ?array $add = null, bool $take = false ): array {
	static $queue = array();

	if ( null !== $add ) {
		$queue[ $add['blog_id'] . ':' . $add['post_id'] ] = $add;
	}

	if ( $take ) {
		$items = array_values( $queue );
		$queue = array();

		return $items;
	}

	return $queue;
}

add_action( 'wp_after_insert_post', 'nwcs_form_on_insert', 10, 3 );
function nwcs_form_on_insert( int $post_id, WP_Post $post, bool $update ): void {
	if ( $update || ( defined( 'WP_IMPORTING' ) && WP_IMPORTING ) ) {
		return;
	}

	if ( ! isset( nwcs_form_post_types()[ $post->post_type ] ) || in_array( $post->post_status, array( 'auto-draft', 'trash' ), true ) ) {
		return;
	}

	// Kayit basina tek bildirim: meta zaten varsa (baska bir istek islediyse) atla.
	if ( ! add_post_meta( $post_id, NWCS_FORM_NOTIFIED_META, 'pending', true ) ) {
		return;
	}

	nwcs_form_queue(
		array(
			'blog_id' => get_current_blog_id(),
			'post_id' => $post_id,
		)
	);

	if ( ! has_action( 'shutdown', 'nwcs_form_flush' ) ) {
		add_action( 'shutdown', 'nwcs_form_flush', 20 );
	}
}

/**
 * Kuyruktaki bildirimleri gonderir. Once yaniti ziyaretciye kapatir
 * (PHP-FPM / LiteSpeed), sonra e-postayi gonderir; SMTP gecikmesi
 * yonlendirmeyi bekletmez.
 */
function nwcs_form_flush(): void {
	$items = nwcs_form_queue( null, true );

	if ( ! $items ) {
		return;
	}

	if ( 'cli' !== PHP_SAPI ) {
		ignore_user_abort( true );

		if ( function_exists( 'fastcgi_finish_request' ) ) {
			fastcgi_finish_request();
		} elseif ( function_exists( 'litespeed_finish_request' ) ) {
			litespeed_finish_request();
		}
	}

	foreach ( $items as $item ) {
		$switched = is_multisite() && (int) $item['blog_id'] !== get_current_blog_id();

		if ( $switched ) {
			switch_to_blog( (int) $item['blog_id'] );
		}

		try {
			nwcs_form_send_notification( (int) $item['post_id'] );
		} catch ( Throwable $e ) {
			error_log( sprintf( '[nwcs-forms] Bildirim hatasi (site %d, kayit %d): %s', (int) $item['blog_id'], (int) $item['post_id'], $e->getMessage() ) );
		}

		if ( $switched ) {
			restore_current_blog();
		}
	}
}

/**
 * Tek satir: satir sonu ve kontrol karakterleri bosluga cevrilir.
 */
function nwcs_form_single_line( string $value ): string {
	return trim( preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', $value ) ?? '' );
}

/**
 * Adres basligindaki ad icin: tek satir, adres ayiraclari da atilir.
 */
function nwcs_form_header_text( string $value ): string {
	return trim( str_replace( array( '<', '>', '"', ',', ';', ':' ), '', nwcs_form_single_line( $value ) ) );
}

/**
 * Tek bir e-posta adresini dogrular; gecersizse bos dizge.
 */
function nwcs_form_clean_email( $value ): string {
	$email = sanitize_email( nwcs_form_header_text( (string) $value ) );

	return ( '' !== $email && is_email( $email ) ) ? $email : '';
}

/**
 * Bildirim alicilari. Filtre dizge (virgulle ayrilmis olabilir) ya da dizi
 * dondurebilir; gecersiz adresler atilir.
 */
function nwcs_form_recipients( WP_Post $post ): array {
	$default = '';

	if ( function_exists( 'nwcs_field' ) ) {
		$default = nwcs_form_clean_email( nwcs_field( NWCS_SEO_SITE_PAGE, 'org', 'email', '' ) );
	}

	if ( '' === $default ) {
		$default = nwcs_form_clean_email( get_option( 'admin_email' ) );
	}

	$raw  = apply_filters( 'nwcs_form_recipient', $default, $post );
	$list = is_array( $raw ) ? $raw : explode( ',', (string) $raw );
	$out  = array();

	foreach ( $list as $item ) {
		$email = nwcs_form_clean_email( $item );

		if ( '' !== $email ) {
			$out[ strtolower( $email ) ] = $email;
		}
	}

	return array_values( $out );
}

/**
 * Kaydin alanlari: [ alan => [ etiket, deger ] ]. Bilinen alanlar sabit
 * sirada, digerleri meta anahtariyla alfabetik.
 */
function nwcs_form_fields( WP_Post $post, string $prefix ): array {
	$labels = nwcs_form_field_labels();
	$known  = array();
	$other  = array();

	foreach ( get_post_meta( $post->ID ) as $key => $values ) {
		$key = (string) $key;

		if ( ! str_starts_with( $key, $prefix ) || ! isset( $values[0] ) || ! is_scalar( $values[0] ) ) {
			continue;
		}

		$field = substr( $key, strlen( $prefix ) );
		$value = trim( sanitize_textarea_field( (string) $values[0] ) );

		if ( isset( $labels[ $field ] ) ) {
			$known[ $field ] = array( $labels[ $field ], $value );
		} elseif ( '' !== $value ) {
			$other[ $key ] = array( $key, $value );
		}
	}

	$ordered = array();

	foreach ( array_keys( $labels ) as $field ) {
		if ( isset( $known[ $field ] ) ) {
			$ordered[ $field ] = $known[ $field ];
		}
	}

	ksort( $other );

	return $ordered + $other;
}

/**
 * Tek kaydin bildirimini gonderir. Sonuc _nwcs_notified metasina yazilir.
 */
function nwcs_form_send_notification( int $post_id ): bool {
	$post  = get_post( $post_id );
	$types = nwcs_form_post_types();

	if ( ! $post instanceof WP_Post || ! isset( $types[ $post->post_type ] ) ) {
		return false;
	}

	if ( 'pending' !== get_post_meta( $post_id, NWCS_FORM_NOTIFIED_META, true ) ) {
		return false;
	}

	$recipients = nwcs_form_recipients( $post );

	if ( ! $recipients ) {
		update_post_meta( $post_id, NWCS_FORM_NOTIFIED_META, 'failed' );
		error_log( sprintf( '[nwcs-forms] Gecerli alici yok (site %d, kayit %d).', get_current_blog_id(), $post_id ) );

		return false;
	}

	$fields    = nwcs_form_fields( $post, $types[ $post->post_type ] );
	$site_name = nwcs_form_single_line( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	$kind      = str_ends_with( $post->post_type, '_quote' ) ? 'Yeni teklif isteği' : 'Yeni iletişim mesajı';
	$subject   = sprintf( '%s: %s', $kind, '' !== $site_name ? $site_name : wp_parse_url( home_url(), PHP_URL_HOST ) );

	$lines = array( $subject, '' );

	foreach ( $fields as $field ) {
		$value = '' !== $field[1] ? $field[1] : '—';

		if ( str_contains( $value, "\n" ) ) {
			$lines[] = $field[0] . ':';
			$lines[] = $value;
			$lines[] = '';
		} else {
			$lines[] = $field[0] . ': ' . $value;
		}
	}

	// Mesaj metasi olmayan turlerde (ik_message, sp_message) mesaj yalnizca
	// kayit metninde durur; metnin tamami eklenir.
	if ( ! isset( $fields['message'] ) ) {
		$content = trim( sanitize_textarea_field( $post->post_content ) );

		if ( '' !== $content ) {
			$lines[] = '';
			$lines[] = 'Kayıt metni:';
			$lines[] = $content;
		}
	}

	$lines[] = '';
	$lines[] = 'Gönderim zamanı: ' . wp_date( 'd.m.Y H:i', (int) get_post_timestamp( $post ) );
	$lines[] = 'Kaydı yönetim panelinde açın: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' );
	$lines[] = 'Tüm kayıtlar: ' . admin_url( 'edit.php?post_type=' . $post->post_type );
	$lines[] = '';
	$lines[] = sprintf( 'Bu e-posta %s adresindeki formdan otomatik gönderildi.', home_url( '/' ) );

	$headers  = array( 'Content-Type: text/plain; charset=UTF-8' );
	$reply_to = nwcs_form_clean_email( $fields['email'][1] ?? '' );

	if ( '' !== $reply_to ) {
		$name      = nwcs_form_header_text( $fields['name'][1] ?? '' );
		$headers[] = 'Reply-To: ' . ( '' !== $name ? $name . ' <' . $reply_to . '>' : $reply_to );
		$lines[]   = 'Yanıtla dediğinizde e-posta doğrudan müşteriye gider.';
	}

	$error  = '';
	$listen = static function ( WP_Error $wp_error ) use ( &$error ): void {
		$error = $wp_error->get_error_message();
	};

	add_action( 'wp_mail_failed', $listen );

	try {
		$sent = (bool) wp_mail( $recipients, $subject, implode( "\n", $lines ) . "\n", $headers );
	} catch ( Throwable $e ) {
		$sent  = false;
		$error = $e->getMessage();
	}

	remove_action( 'wp_mail_failed', $listen );

	update_post_meta( $post_id, NWCS_FORM_NOTIFIED_META, $sent ? 'sent' : 'failed' );

	if ( ! $sent ) {
		error_log( sprintf( '[nwcs-forms] E-posta gonderilemedi (site %d, kayit %d): %s', get_current_blog_id(), $post_id, '' !== $error ? $error : 'wp_mail false dondu' ) );
	}

	return $sent;
}
