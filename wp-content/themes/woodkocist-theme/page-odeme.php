<?php
/**
 * Odeme (/odeme/): bilgiler + teslimat + Havale/EFT; siparis verilince ayni
 * adres onay sayfasi olur (?siparis=<no>&anahtar=<gizli>). Isleyici:
 * inc/checkout.php. Sepet bossa sepete yonlenir.
 */

defined( 'ABSPATH' ) || exit;

$order = wk_order_from_request();

if ( $order ) {
	wk_part( 'order-received', array( 'order' => $order ) );
	return;
}

$cart = wk_cart();

if ( ! $cart['lines'] ) {
	wp_safe_redirect( home_url( '/sepet/' ) );
	exit;
}

$totals = wk_cart_totals( $cart );
$state  = wk_checkout_state();
$errors = $state['errors'];
$v      = $state['values'] + array( 'delivery' => 'adres', 'city' => 'İstanbul' );
$pickup = trim( (string) nwcs_field( 'global', 'shop', 'pickup_address' ) );

// Gorunen yazilar: Tum Sayfalar -> Odeme Sayfasi (global.checkout).
$text = static fn( string $name ): string => (string) nwcs_field( 'global', 'checkout', $name );
$attr = static function ( string $name ): void {
	nwcs_edit_attr( 'global', 'checkout', $name );
};

$describe = static function ( string $key ) use ( $errors ): string {
	return isset( $errors[ $key ] ) ? 'aria-invalid="true" aria-describedby="wk-' . esc_attr( $key ) . '-err"' : '';
};

$error = static function ( string $key ) use ( $errors ): void {
	if ( isset( $errors[ $key ] ) ) {
		printf( '<p class="wk-field__error" id="wk-%s-err"%s>%s</p>', esc_attr( $key ), wk_error_attr( 'checkout', (string) $errors[ $key ] ), esc_html( $errors[ $key ] ) ); // phpcs:ignore WordPress.Security.EscapingOutput -- nitelik eklentide kacirilir.
	}
};

// $label, 'hint' ve 'placeholder': global.checkout alan adlari.
$input = static function ( string $key, string $label, array $o = array() ) use ( $v, $describe, $error, $text, $attr ): void {
	$o += array( 'type' => 'text', 'auto' => '', 'required' => true, 'wide' => false, 'hint' => '', 'placeholder' => '', 'inputmode' => '' );
	?>
	<div class="wk-field<?php echo $o['wide'] ? ' wk-field--wide' : ''; ?>">
		<label for="wk-<?php echo esc_attr( $key ); ?>" <?php $attr( $label ); ?>><?php echo esc_html( $text( $label ) ); ?><?php if ( ! $o['required'] ) : ?> <span class="wk-optional" <?php $attr( 'optional' ); ?>><?php echo esc_html( $text( 'optional' ) ); ?></span><?php endif; ?></label>
		<input id="wk-<?php echo esc_attr( $key ); ?>" name="wk_<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $o['type'] ); ?>"
			value="<?php echo esc_attr( (string) ( $v[ $key ] ?? '' ) ); ?>"
			<?php echo $o['auto'] ? 'autocomplete="' . esc_attr( $o['auto'] ) . '"' : ''; ?>
			<?php echo $o['placeholder'] ? 'placeholder="' . esc_attr( $text( $o['placeholder'] ) ) . '"' : ''; ?>
			<?php $o['placeholder'] && $attr( $o['placeholder'] ); ?>
			<?php echo $o['inputmode'] ? 'inputmode="' . esc_attr( $o['inputmode'] ) . '"' : ''; ?>
			<?php echo $o['required'] ? 'required' : ''; ?>
			<?php echo $describe( $key ); // phpcs:ignore WordPress.Security.EscapingOutput ?> />
		<?php if ( $o['hint'] ) : ?>
			<p class="wk-field__hint" <?php $attr( $o['hint'] ); ?>><?php echo esc_html( $text( $o['hint'] ) ); ?></p>
		<?php endif; ?>
		<?php $error( $key ); ?>
	</div>
	<?php
};

get_header();
?>

<section class="wk-pagehead wk-pagehead--compact">
	<div class="wk-wrap">
		<?php wk_part( 'checkout-steps', array( 'step' => 2 ) ); ?>
		<h1 class="wk-hero__title" <?php $attr( 'heading' ); ?>><?php echo esc_html( $text( 'heading' ) ); ?></h1>
	</div>
</section>

<div class="wk-wrap wk-section" id="odeme-formu">
	<?php if ( ! empty( $errors['form'] ) ) : ?>
		<div class="wk-notice wk-notice--err" role="alert" tabindex="-1" data-focus><p <?php echo wk_error_attr( 'checkout', (string) $errors['form'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>><?php echo esc_html( $errors['form'] ); ?></p></div>
	<?php elseif ( $errors ) : ?>
		<div class="wk-notice wk-notice--err" role="alert" tabindex="-1" data-focus>
			<p <?php $attr( 'errors_count' ); ?>><strong <?php $attr( 'errors_title' ); ?>><?php echo esc_html( $text( 'errors_title' ) ); ?></strong> <?php echo esc_html( wk_text( 'global', 'checkout', 'errors_count', array( 'sayi' => count( $errors ) ) ) ); ?></p>
		</div>
	<?php endif; ?>

	<form class="wk-checkout" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-checkout>
		<input type="hidden" name="action" value="wk_checkout" />
		<input type="hidden" name="wk_seen_total" value="<?php echo esc_attr( (string) $totals['total'] ); ?>" />
		<?php wp_nonce_field( 'wk_checkout', 'wk_checkout_nonce' ); ?>
		<div class="wk-hp" aria-hidden="true">
			<label for="wk-website">Bu alanı boş bırakın</label>
			<input id="wk-website" name="wk_website" type="text" tabindex="-1" autocomplete="off" />
		</div>

		<div class="wk-checkout__main">
			<fieldset class="wk-panel">
				<legend class="wk-panel__title" <?php $attr( 'contact_title' ); ?>><span class="wk-panel__num">1</span> <?php echo esc_html( $text( 'contact_title' ) ); ?></legend>
				<p class="wk-panel__lead" <?php $attr( 'contact_desc' ); ?>><?php echo esc_html( $text( 'contact_desc' ) ); ?></p>
				<div class="wk-grid2">
					<?php
					$input( 'first', 'first_label', array( 'auto' => 'given-name' ) );
					$input( 'last', 'last_label', array( 'auto' => 'family-name' ) );
					$input( 'phone', 'phone_label', array( 'type' => 'tel', 'auto' => 'tel', 'placeholder' => 'phone_hint' ) );
					$input( 'email', 'email_label', array( 'type' => 'email', 'auto' => 'email', 'hint' => 'email_help' ) );
					?>
				</div>
			</fieldset>

			<fieldset class="wk-panel">
				<legend class="wk-panel__title" <?php $attr( 'delivery_title' ); ?>><span class="wk-panel__num">2</span> <?php echo esc_html( $text( 'delivery_title' ) ); ?></legend>
				<div class="wk-options" role="radiogroup" aria-label="Teslimat şekli">
					<label class="wk-option">
						<input type="radio" name="wk_delivery" value="adres" <?php checked( $v['delivery'], 'adres' ); ?> data-toggle-target="adres" />
						<span class="wk-option__body">
							<strong <?php $attr( 'ship_option' ); ?>><?php echo esc_html( $text( 'ship_option' ) ); ?></strong>
							<span <?php nwcs_edit_attr( 'global', 'shop', 'shipping_note' ); ?>><?php echo esc_html( nwcs_field( 'global', 'shop', 'shipping_note' ) ); ?></span>
						</span>
						<span class="wk-option__price" <?php nwcs_edit_attr( 'global', 'shop', 'shipping_label' ); ?>><?php echo esc_html( nwcs_field( 'global', 'shop', 'shipping_label' ) ); ?></span>
					</label>
					<?php if ( '' !== $pickup ) : ?>
						<label class="wk-option">
							<input type="radio" name="wk_delivery" value="fabrika" <?php checked( $v['delivery'], 'fabrika' ); ?> data-toggle-target="fabrika" />
							<span class="wk-option__body">
								<strong <?php $attr( 'pickup_option' ); ?>><?php echo esc_html( $text( 'pickup_option' ) ); ?></strong>
								<span <?php nwcs_edit_attr( 'global', 'shop', 'pickup_address' ); ?>><?php echo esc_html( $pickup ); ?></span>
							</span>
							<span class="wk-option__price" <?php $attr( 'pickup_price' ); ?>><?php echo esc_html( $text( 'pickup_price' ) ); ?></span>
						</label>
					<?php endif; ?>
				</div>

				<div class="wk-grid2" data-show-when="delivery=adres">
					<div class="wk-field">
						<label for="wk-city" <?php $attr( 'city_label' ); ?>><?php echo esc_html( $text( 'city_label' ) ); ?></label>
						<select id="wk-city" name="wk_city" autocomplete="address-level1" <?php echo $describe( 'city' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> <?php $attr( 'city_label' ); ?>>
							<option value="" <?php $attr( 'choose' ); ?>><?php echo esc_html( $text( 'choose' ) ); ?></option>
							<?php foreach ( wk_provinces() as $province ) : ?>
								<option <?php selected( $v['city'], $province ); ?>><?php echo esc_html( $province ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php $error( 'city' ); ?>
					</div>
					<?php $input( 'district', 'district_label', array( 'auto' => 'address-level2' ) ); ?>
					<div class="wk-field wk-field--wide">
						<label for="wk-address" <?php $attr( 'address_label' ); ?>><?php echo esc_html( $text( 'address_label' ) ); ?></label>
						<textarea id="wk-address" name="wk_address" rows="2" autocomplete="street-address" placeholder="<?php echo esc_attr( $text( 'address_hint' ) ); ?>" <?php $attr( 'address_hint' ); ?> <?php echo $describe( 'address' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>><?php echo esc_textarea( (string) ( $v['address'] ?? '' ) ); ?></textarea>
						<?php $error( 'address' ); ?>
					</div>
					<?php $input( 'postcode', 'postcode_label', array( 'required' => false, 'auto' => 'postal-code', 'inputmode' => 'numeric' ) ); ?>
				</div>
			</fieldset>

			<fieldset class="wk-panel">
				<legend class="wk-panel__title" <?php $attr( 'invoice_title' ); ?>><span class="wk-panel__num">3</span> <?php echo esc_html( $text( 'invoice_title' ) ); ?></legend>
				<div class="wk-check">
					<input id="wk-corporate" name="wk_corporate" type="checkbox" value="1" <?php checked( ! empty( $v['corporate'] ) ); ?> data-toggle-target="corporate" />
					<label for="wk-corporate" <?php $attr( 'corporate_check' ); ?>><?php echo esc_html( $text( 'corporate_check' ) ); ?></label>
				</div>
				<div class="wk-grid2" data-show-when="corporate">
					<?php
					$input( 'company', 'company_label', array( 'auto' => 'organization', 'wide' => true ) );
					$input( 'tax_office', 'tax_office_label' );
					$input( 'tax_no', 'tax_no_label', array( 'inputmode' => 'numeric' ) );
					?>
				</div>
				<div class="wk-check" data-show-when="delivery=adres">
					<input id="wk-bill-diff" name="wk_bill_diff" type="checkbox" value="1" <?php checked( ! empty( $v['bill_diff'] ) ); ?> data-toggle-target="billdiff" />
					<label for="wk-bill-diff" <?php $attr( 'bill_diff_check' ); ?>><?php echo esc_html( $text( 'bill_diff_check' ) ); ?></label>
				</div>
				<div data-show-when="billdiff|delivery=fabrika+corporate">
					<div class="wk-field">
						<label for="wk-bill-addr" <?php $attr( 'bill_addr_label' ); ?>><?php echo esc_html( $text( 'bill_addr_label' ) ); ?></label>
						<textarea id="wk-bill-addr" name="wk_bill_addr" rows="2" <?php echo $describe( 'bill_addr' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>><?php echo esc_textarea( (string) ( $v['bill_addr'] ?? '' ) ); ?></textarea>
						<?php $error( 'bill_addr' ); ?>
					</div>
				</div>
			</fieldset>

			<fieldset class="wk-panel">
				<legend class="wk-panel__title" <?php $attr( 'payment_title' ); ?>><span class="wk-panel__num">4</span> <?php echo esc_html( $text( 'payment_title' ) ); ?></legend>
				<div class="wk-options">
					<label class="wk-option is-only">
						<input type="radio" name="wk_payment" value="havale" checked />
						<span class="wk-option__body">
							<strong <?php $attr( 'payment_option' ); ?>><?php echo esc_html( $text( 'payment_option' ) ); ?></strong>
							<span <?php nwcs_edit_attr( 'global', 'shop', 'payment_text' ); ?>><?php echo esc_html( nwcs_field( 'global', 'shop', 'payment_text' ) ); ?></span>
						</span>
					</label>
				</div>
				<?php $bank_field = wk_bank_lines() ? 'bank_next' : 'bank_later'; ?>
				<p class="wk-panel__lead" <?php $attr( $bank_field ); ?>><?php echo esc_html( $text( $bank_field ) ); ?></p>

				<div class="wk-field">
					<label for="wk-note" <?php $attr( 'order_note_label' ); ?>><?php echo esc_html( $text( 'order_note_label' ) ); ?> <span class="wk-optional" <?php $attr( 'optional' ); ?>><?php echo esc_html( $text( 'optional' ) ); ?></span></label>
					<textarea id="wk-note" name="wk_note" rows="2" placeholder="<?php echo esc_attr( $text( 'order_note_hint' ) ); ?>" <?php $attr( 'order_note_hint' ); ?>><?php echo esc_textarea( (string) ( $v['note'] ?? '' ) ); ?></textarea>
				</div>
			</fieldset>
		</div>

		<aside class="wk-summary wk-checkout__side" aria-labelledby="wk-summary-title">
			<h2 id="wk-summary-title" class="wk-summary__title" <?php nwcs_edit_attr( 'global', 'cart', 'summary_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'cart', 'summary_title' ) ); ?></h2>
			<ul class="wk-mini">
				<?php foreach ( $cart['lines'] as $line ) : ?>
					<li>
						<span class="wk-mini__qty wk-num"><?php echo (int) $line['qty']; ?>×</span>
						<span class="wk-mini__name" <?php wk_product_src( $line['product'], 'Ürün adı' ); ?>><span class="wk-cart__code"><?php echo esc_html( $line['product']['code'] ); ?></span><?php echo esc_html( $line['product']['title'] ); ?></span>
						<span class="wk-num"><?php echo esc_html( wk_money( $line['total'] ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<a class="wk-summary__edit" href="<?php echo esc_url( home_url( '/sepet/' ) ); ?>" <?php $attr( 'edit_cart' ); ?>><?php echo esc_html( $text( 'edit_cart' ) ); ?></a>
			<?php wk_part( 'order-totals', array( 'totals' => $totals, 'count' => $cart['count'] ) ); ?>

			<div class="wk-check wk-terms">
				<input id="wk-terms" name="wk_terms" type="checkbox" value="1" required <?php checked( ! empty( $v['terms'] ) ); ?> <?php echo $describe( 'terms' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> />
				<?php
				$terms_html = wk_text_html(
					'global',
					'checkout',
					'terms',
					array(
						'sozlesme' => '<a href="' . esc_url( wk_page_url( 'mesafeli-satis-sozlesmesi' ) ) . '" target="_blank"' . wk_attr_string( 'global', 'checkout', 'terms_contract' ) . '>' . esc_html( $text( 'terms_contract' ) ) . '</a>',
						'iade'     => '<a href="' . esc_url( wk_page_url( 'iptal-iade-kosullari' ) ) . '" target="_blank"' . wk_attr_string( 'global', 'checkout', 'terms_returns' ) . '>' . esc_html( $text( 'terms_returns' ) ) . '</a>',
					)
				);
				?>
				<label for="wk-terms" <?php $attr( 'terms' ); ?>><?php echo $terms_html; // phpcs:ignore WordPress.Security.EscapingOutput -- metin kacirildi, baglantilar kacirilmis parcalardan. ?></label>
				<?php $error( 'terms' ); ?>
			</div>
			<p class="wk-form__note" <?php $attr( 'kvkk_notice' ); ?>><?php echo wk_text_html( 'global', 'checkout', 'kvkk_notice', array( 'kvkk' => '<a href="' . esc_url( wk_page_url( 'kvkk' ) ) . '" target="_blank"' . wk_attr_string( 'global', 'checkout', 'kvkk_link' ) . '>' . esc_html( $text( 'kvkk_link' ) ) . '</a>' ) ); // phpcs:ignore WordPress.Security.EscapingOutput -- metin kacirildi. ?></p>

			<button type="submit" class="wk-btn wk-btn--primary wk-btn--lg wk-btn--block" <?php $attr( 'submit' ); ?>><?php echo esc_html( $text( 'submit' ) ); ?></button>
			<p class="wk-form__note wk-center" <?php $attr( 'submit_note' ); ?>><?php echo esc_html( $text( 'submit_note' ) ); ?></p>
		</aside>
	</form>
</div>

<?php
get_footer();
