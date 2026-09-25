<?php
/**
 * Odeme adimi ve siparisler.
 *
 * woodkocist.com.tr'deki gibi: uyeliksiz siparis, teslimat "adrese gonderim"
 * ya da "fabrikadan teslim", tek odeme yolu Banka Havalesi / EFT. Kart ile
 * odeme yok (sanal POS sozlesmesi gerekir; sahte odeme adimi gosterilmez).
 *
 * Fiyat ve tutar her zaman sunucuda Urun Havuzu'ndan hesaplanir (inc/cart.php).
 * Siparis yonetimde "Siparisler" altina duser; Network Content Studio firma
 * e-postasina bildirim gonderir (_wk_ alanlari). Ayrintilar _wko_ metasinda.
 */

defined( 'ABSPATH' ) || exit;

const WK_ORDER_STATUSES = array(
	'odeme-bekleniyor' => 'Ödeme bekleniyor',
	'uretimde'         => 'Ödeme alındı, üretimde',
	'sevkiyatta'       => 'Sevkiyatta',
	'tamamlandi'       => 'Teslim edildi',
	'iptal'            => 'İptal edildi',
);

add_action( 'init', 'wk_register_order_cpt' );
function wk_register_order_cpt(): void {
	register_post_type(
		'wk_order',
		array(
			'labels'          => array(
				'name'          => 'Siparişler',
				'singular_name' => 'Sipariş',
				'menu_name'     => 'Siparişler',
				'all_items'     => 'Tüm siparişler',
				'edit_item'     => 'Siparişi görüntüle',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-cart',
			'menu_position'   => 26,
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'has_archive'     => false,
			'rewrite'         => false,
			'query_var'       => false,
		)
	);
}

/** Odeme formu uyarisi (Tum Sayfalar -> Odeme Sayfasi). */
function wk_checkout_error( string $field ): string {
	return (string) nwcs_field( 'global', 'checkout', $field );
}

function wk_provinces(): array {
	return array( 'Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Aksaray', 'Amasya', 'Ankara', 'Antalya', 'Ardahan', 'Artvin', 'Aydın', 'Balıkesir', 'Bartın', 'Batman', 'Bayburt', 'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa', 'Çanakkale', 'Çankırı', 'Çorum', 'Denizli', 'Diyarbakır', 'Düzce', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir', 'Gaziantep', 'Giresun', 'Gümüşhane', 'Hakkâri', 'Hatay', 'Iğdır', 'Isparta', 'İstanbul', 'İzmir', 'Kahramanmaraş', 'Karabük', 'Karaman', 'Kars', 'Kastamonu', 'Kayseri', 'Kilis', 'Kırıkkale', 'Kırklareli', 'Kırşehir', 'Kocaeli', 'Konya', 'Kütahya', 'Malatya', 'Manisa', 'Mardin', 'Mersin', 'Muğla', 'Muş', 'Nevşehir', 'Niğde', 'Ordu', 'Osmaniye', 'Rize', 'Sakarya', 'Samsun', 'Şanlıurfa', 'Siirt', 'Sinop', 'Sivas', 'Şırnak', 'Tekirdağ', 'Tokat', 'Trabzon', 'Tunceli', 'Uşak', 'Van', 'Yalova', 'Yozgat', 'Zonguldak' );
}

function wk_checkout_redirect( array $state ): void {
	$token = bin2hex( random_bytes( 10 ) );

	set_transient( 'wk_checkout_' . $token, $state, 20 * MINUTE_IN_SECONDS );
	wp_safe_redirect( add_query_arg( 'wk', $token, home_url( '/odeme/' ) ) . '#odeme-formu' );
	exit;
}

function wk_checkout_state(): array {
	$empty = array( 'errors' => array(), 'values' => array() );
	$token = isset( $_GET['wk'] ) ? sanitize_key( wp_unslash( $_GET['wk'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! preg_match( '/^[a-f0-9]{20}$/', $token ) ) {
		return $empty;
	}

	$state = get_transient( 'wk_checkout_' . $token );

	return is_array( $state ) ? array_merge( $empty, $state ) : $empty;
}

add_action( 'admin_post_wk_checkout', 'wk_handle_checkout' );
add_action( 'admin_post_nopriv_wk_checkout', 'wk_handle_checkout' );
function wk_handle_checkout(): void {
	if ( ! empty( $_POST['wk_website'] ) ) {
		wp_safe_redirect( home_url( '/sepet/' ) );
		exit;
	}

	$text = static fn( string $key ): string => sanitize_text_field( wp_unslash( $_POST[ $key ] ?? '' ) );

	$v = array(
		'first'     => $text( 'wk_first' ),
		'last'      => $text( 'wk_last' ),
		'phone'     => $text( 'wk_phone' ),
		'email'     => sanitize_email( wp_unslash( $_POST['wk_email'] ?? '' ) ),
		'delivery'  => 'fabrika' === $text( 'wk_delivery' ) ? 'fabrika' : 'adres',
		'city'      => $text( 'wk_city' ),
		'district'  => $text( 'wk_district' ),
		'address'   => sanitize_textarea_field( wp_unslash( $_POST['wk_address'] ?? '' ) ),
		'postcode'  => $text( 'wk_postcode' ),
		'corporate' => ! empty( $_POST['wk_corporate'] ),
		'company'   => $text( 'wk_company' ),
		'tax_office' => $text( 'wk_tax_office' ),
		'tax_no'    => $text( 'wk_tax_no' ),
		'bill_diff' => ! empty( $_POST['wk_bill_diff'] ),
		'bill_addr' => sanitize_textarea_field( wp_unslash( $_POST['wk_bill_addr'] ?? '' ) ),
		'note'      => sanitize_textarea_field( wp_unslash( $_POST['wk_note'] ?? '' ) ),
		'terms'     => ! empty( $_POST['wk_terms'] ),
	);

	$errors = array();

	if ( ! isset( $_POST['wk_checkout_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wk_checkout_nonce'] ), 'wk_checkout' ) ) {
		$errors['form'] = wk_checkout_error( 'err_nonce' );
	}

	$cart = wk_cart();

	if ( ! $cart['lines'] ) {
		wp_safe_redirect( home_url( '/sepet/' ) );
		exit;
	}

	// Sepetin ziyaretcinin gordugu hali: tutar degistiyse siparis olusmaz, yeni tutar gosterilir.
	$seen = (string) ( $_POST['wk_seen_total'] ?? '' );

	if ( '' !== $seen && abs( (float) $seen - wk_cart_totals( $cart )['total'] ) > 0.009 ) {
		$errors['form'] = wk_checkout_error( 'err_price' );
	}

	$required = array(
		'first' => wk_checkout_error( 'err_first' ),
		'last'  => wk_checkout_error( 'err_last' ),
	);

	foreach ( $required as $key => $message ) {
		if ( '' === $v[ $key ] ) {
			$errors[ $key ] = $message;
		}
	}

	if ( strlen( preg_replace( '/\D/', '', $v['phone'] ) ) < 10 ) {
		$errors['phone'] = wk_checkout_error( 'err_phone' );
	}

	if ( ! is_email( $v['email'] ) ) {
		$errors['email'] = wk_checkout_error( '' === $v['email'] ? 'err_email' : 'err_email_bad' );
	}

	if ( 'adres' === $v['delivery'] ) {
		if ( ! in_array( $v['city'], wk_provinces(), true ) ) {
			$errors['city'] = wk_checkout_error( 'err_city' );
		}
		if ( '' === $v['district'] ) {
			$errors['district'] = wk_checkout_error( 'err_district' );
		}
		if ( mb_strlen( trim( $v['address'] ) ) < 10 ) {
			$errors['address'] = wk_checkout_error( 'err_address' );
		}
	}

	if ( $v['corporate'] ) {
		if ( '' === $v['company'] ) {
			$errors['company'] = wk_checkout_error( 'err_company' );
		}
		if ( '' === $v['tax_office'] ) {
			$errors['tax_office'] = wk_checkout_error( 'err_tax_office' );
		}
		if ( ! preg_match( '/^\d{10,11}$/', preg_replace( '/\s/', '', $v['tax_no'] ) ) ) {
			$errors['tax_no'] = wk_checkout_error( 'err_tax_no' );
		}
	}

	if ( ( $v['bill_diff'] || 'fabrika' === $v['delivery'] && $v['corporate'] ) && mb_strlen( trim( $v['bill_addr'] ) ) < 10 ) {
		$errors['bill_addr'] = wk_checkout_error( 'err_bill_addr' );
	}

	if ( ! $v['terms'] ) {
		$errors['terms'] = wk_checkout_error( 'err_terms' );
	}

	// Ayni adresten kisa surede cok siparis: bot korumasi.
	// Cloudflare arkasinda REMOTE_ADDR Cloudflare'in adresidir; gercek ziyaretci
	// CF-Connecting-IP'de. (Yalnizca hiz siniri; baska bir yetki buna dayanmaz.)
	$ip     = (string) ( $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '' );
	$ip_key = 'wk_orders_' . md5( $ip );
	$recent = (int) get_transient( $ip_key );

	if ( ! $errors && $recent >= 5 ) {
		$errors['form'] = wk_checkout_error( 'err_rate' );
	}

	if ( $errors ) {
		wk_checkout_redirect( array( 'errors' => $errors, 'values' => isset( $errors['form'] ) && ! $v['first'] ? array() : $v ) );
	}

	set_transient( $ip_key, $recent + 1, 10 * MINUTE_IN_SECONDS );

	$totals = wk_cart_totals( $cart );
	$name   = trim( $v['first'] . ' ' . $v['last'] );
	$items  = array();
	$lines  = array();

	foreach ( $cart['lines'] as $line ) {
		$items[] = array(
			'id'    => (int) $line['product']['id'],
			'code'  => (string) $line['product']['code'],
			'title' => (string) $line['product']['title'],
			'qty'   => (int) $line['qty'],
			'unit'  => (float) $line['unit'],
			'total' => (float) $line['total'],
		);
		$lines[] = sprintf( '%d × %s %s — %s', $line['qty'], $line['product']['code'], $line['product']['title'], wk_money( $line['total'] ) );
	}

	$delivery = 'fabrika' === $v['delivery']
		? 'Fabrikadan teslim: ' . trim( (string) nwcs_field( 'global', 'shop', 'pickup_address' ) )
		: sprintf( "Adrese gönderim:\n%s\n%s / %s%s", $v['address'], $v['district'], $v['city'], $v['postcode'] ? ' ' . $v['postcode'] : '' );

	$invoice = $v['corporate']
		? sprintf( 'Kurumsal fatura: %s, %s VD, %s', $v['company'], $v['tax_office'], $v['tax_no'] )
		: 'Bireysel fatura: ' . $name;

	if ( $v['bill_diff'] || ( 'fabrika' === $v['delivery'] && $v['corporate'] ) ) {
		$invoice .= "\nFatura adresi: " . $v['bill_addr'];
	}

	$summary = implode( "\n", $lines )
		. "\n\nAra toplam: " . wk_money( $totals['subtotal'] )
		. ( $totals['vat_included'] ? '' : "\nKDV: " . wk_money( $totals['vat'] ) )
		. "\nKargo: " . nwcs_field( 'global', 'shop', 'shipping_label' )
		. "\nToplam: " . wk_money( $totals['total'] ) . ( $totals['vat_included'] ? ' (KDV dahil)' : '' )
		. "\nÖdeme: Banka Havalesi / EFT"
		. "\n\n" . $delivery
		. "\n\n" . $invoice
		. ( $v['note'] ? "\n\nSipariş notu:\n" . $v['note'] : '' );

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'wk_order',
			'post_status'  => 'private',
			'post_title'   => sprintf( 'Yeni sipariş — %s — %s', $name, wk_money( $totals['total'] ) ),
			'post_content' => "Telefon: {$v['phone']}\nE-posta: {$v['email']}\n\n" . $summary,
			'meta_input'   => array(
				// meta_input kayittan once wp_unslash uygular: JSON'daki kacis
				// isaretleri (tirnak, satir sonu) bozulmasin diye wp_slash.
				'_wko_items'    => wp_slash( wp_json_encode( $items, JSON_UNESCAPED_UNICODE ) ),
				'_wko_total'    => $totals['total'],
				'_wko_subtotal' => $totals['subtotal'],
				'_wko_vat'      => $totals['vat'],
				'_wko_delivery' => $v['delivery'],
				'_wko_address'  => wp_slash( wp_json_encode( array_intersect_key( $v, array_flip( array( 'city', 'district', 'address', 'postcode', 'corporate', 'company', 'tax_office', 'tax_no', 'bill_diff', 'bill_addr' ) ) ), JSON_UNESCAPED_UNICODE ) ),
				'_wko_status'   => 'odeme-bekleniyor',
				'_wko_key'      => bin2hex( random_bytes( 12 ) ),
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		wk_checkout_redirect( array( 'errors' => array( 'form' => wk_checkout_error( 'err_save' ) ), 'values' => $v ) );
	}

	$number = wk_order_number( (int) $post_id );

	// Bildirim e-postasi (eklenti) bu alanlardan yazilir; numara basliga girer.
	// Meta, yazi eklendikten sonra yazilir: eklenti bildirimi istegin sonunda gonderir.
	wp_update_post(
		array(
			'ID'         => $post_id,
			'post_title' => sprintf( '%s — %s — %s', $number, $name, wk_money( $totals['total'] ) ),
		)
	);
	update_post_meta( $post_id, '_wko_number', $number );
	update_post_meta( $post_id, '_wk_name', $name );
	update_post_meta( $post_id, '_wk_email', $v['email'] );
	update_post_meta( $post_id, '_wk_phone', $v['phone'] );
	update_post_meta( $post_id, '_wk_subject', 'Yeni sipariş ' . $number );
	update_post_meta( $post_id, '_wk_message', $summary );

	wk_order_mail_customer( (int) $post_id );
	wk_cart_save( array() );

	wp_safe_redirect( wk_order_url( (int) $post_id ) );
	exit;
}

function wk_order_number( int $post_id ): string {
	return 'WK' . get_post_time( 'ymd', false, $post_id ) . '-' . $post_id;
}

function wk_order_url( int $post_id ): string {
	return add_query_arg(
		array(
			'siparis' => $post_id,
			'anahtar' => (string) get_post_meta( $post_id, '_wko_key', true ),
		),
		home_url( '/odeme/' )
	);
}

/**
 * Onay sayfasindaki siparis: numara + gizli anahtar eslesmeli, 30 gunden eski olmamali.
 */
function wk_order_from_request(): ?WP_Post {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$id  = absint( $_GET['siparis'] ?? 0 );
	$key = sanitize_key( wp_unslash( $_GET['anahtar'] ?? '' ) );
	// phpcs:enable

	if ( ! $id || ! preg_match( '/^[a-f0-9]{24}$/', $key ) ) {
		return null;
	}

	$post = get_post( $id );

	if ( ! $post || 'wk_order' !== $post->post_type || ! hash_equals( (string) get_post_meta( $id, '_wko_key', true ), $key ) ) {
		return null;
	}

	if ( time() - (int) get_post_time( 'U', true, $post ) > 30 * DAY_IN_SECONDS ) {
		return null;
	}

	return $post;
}

function wk_order_items( int $post_id ): array {
	$items = json_decode( (string) get_post_meta( $post_id, '_wko_items', true ), true );

	return is_array( $items ) ? $items : array();
}

function wk_bank_lines(): array {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/u', (string) nwcs_field( 'global', 'shop', 'bank_accounts' ) ) ?: array() ) ) );
}

/**
 * Musteriye siparis ozeti. Gonderilemezse siparis yine gecerlidir; onay
 * sayfasi e-posta gonderildigini iddia etmez.
 */
function wk_order_mail_customer( int $post_id ): void {
	$email = (string) get_post_meta( $post_id, '_wk_email', true );

	if ( ! is_email( $email ) ) {
		return;
	}

	add_action(
		'shutdown',
		static function () use ( $post_id, $email ): void {
			$number = wk_order_number( $post_id );
			$bank   = wk_bank_lines();
			$body   = sprintf( "Merhaba %s,\n\nSiparişiniz bize ulaştı. Sipariş numaranız: %s\n\n", get_post_meta( $post_id, '_wk_name', true ), $number )
				. get_post_meta( $post_id, '_wk_message', true )
				. "\n\n" . trim( (string) nwcs_field( 'global', 'shop', 'payment_text' ) )
				. ( $bank ? "\n\n" . implode( "\n", $bank ) : "\n\nBanka hesap bilgilerini size ayrıca ileteceğiz." )
				. "\n\nSiparişinizin durumu: " . wk_order_url( $post_id )
				. "\n\n" . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . ' — ' . home_url( '/' );

			// Musteri "Yanitla" deyince magazaya yazsin (varsayilan gonderen adresi okunmaz).
			$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
			$shop    = function_exists( 'nwcs_field' ) && defined( 'NWCS_SEO_SITE_PAGE' ) ? sanitize_email( (string) nwcs_field( NWCS_SEO_SITE_PAGE, 'org', 'email' ) ) : '';

			if ( $shop ) {
				$headers[] = 'Reply-To: ' . $shop;
			}

			wp_mail( $email, sprintf( 'Siparişiniz alındı: %s', $number ), $body, $headers );
		},
		30
	);
}

/* ---------------------------------------------------------------------- */
/* Yonetim: liste sutunlari ve durum kutusu                                */
/* ---------------------------------------------------------------------- */

add_filter(
	'manage_wk_order_posts_columns',
	static function ( array $columns ): array {
		return array(
			'cb'        => $columns['cb'] ?? '',
			'title'     => 'Sipariş',
			'wk_total'  => 'Tutar',
			'wk_status' => 'Durum',
			'wk_ship'   => 'Teslimat',
			'date'      => 'Tarih',
		);
	}
);

add_action(
	'manage_wk_order_posts_custom_column',
	static function ( string $column, int $post_id ): void {
		if ( 'wk_total' === $column ) {
			echo esc_html( wk_money( (float) get_post_meta( $post_id, '_wko_total', true ) ) );
		} elseif ( 'wk_status' === $column ) {
			$status = (string) get_post_meta( $post_id, '_wko_status', true );
			echo esc_html( WK_ORDER_STATUSES[ $status ] ?? '—' );
		} elseif ( 'wk_ship' === $column ) {
			echo esc_html( 'fabrika' === get_post_meta( $post_id, '_wko_delivery', true ) ? 'Fabrikadan teslim' : 'Adrese gönderim' );
		}
	},
	10,
	2
);

add_action(
	'add_meta_boxes_wk_order',
	static function (): void {
		add_meta_box( 'wk-order-status', 'Sipariş durumu', 'wk_order_status_box', 'wk_order', 'side', 'high' );
	}
);

function wk_order_status_box( WP_Post $post ): void {
	$status = (string) get_post_meta( $post->ID, '_wko_status', true );
	wp_nonce_field( 'wk_order_status', 'wk_order_status_nonce' );
	?>
	<p><strong><?php echo esc_html( (string) get_post_meta( $post->ID, '_wko_number', true ) ); ?></strong></p>
	<label for="wk-order-status" class="screen-reader-text">Durum</label>
	<select id="wk-order-status" name="wk_order_status" style="width:100%">
		<?php foreach ( WK_ORDER_STATUSES as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<p class="description">Müşteri onay bağlantısında bu durumu görür.</p>
	<?php
}

add_action(
	'save_post_wk_order',
	static function ( int $post_id ): void {
		if ( ! isset( $_POST['wk_order_status_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wk_order_status_nonce'] ), 'wk_order_status' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$status = sanitize_key( wp_unslash( $_POST['wk_order_status'] ?? '' ) );

		if ( isset( WK_ORDER_STATUSES[ $status ] ) ) {
			update_post_meta( $post_id, '_wko_status', $status );
		}
	}
);
