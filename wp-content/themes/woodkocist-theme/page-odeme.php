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

$describe = static function ( string $key ) use ( $errors ): string {
	return isset( $errors[ $key ] ) ? 'aria-invalid="true" aria-describedby="wk-' . esc_attr( $key ) . '-err"' : '';
};

$error = static function ( string $key ) use ( $errors ): void {
	if ( isset( $errors[ $key ] ) ) {
		printf( '<p class="wk-field__error" id="wk-%s-err">%s</p>', esc_attr( $key ), esc_html( $errors[ $key ] ) );
	}
};

$input = static function ( string $key, string $label, array $o = array() ) use ( $v, $describe, $error ): void {
	$o += array( 'type' => 'text', 'auto' => '', 'required' => true, 'wide' => false, 'hint' => '', 'placeholder' => '', 'inputmode' => '' );
	?>
	<div class="wk-field<?php echo $o['wide'] ? ' wk-field--wide' : ''; ?>">
		<label for="wk-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?><?php echo $o['required'] ? '' : ' <span class="wk-optional">(isteğe bağlı)</span>'; ?></label>
		<input id="wk-<?php echo esc_attr( $key ); ?>" name="wk_<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $o['type'] ); ?>"
			value="<?php echo esc_attr( (string) ( $v[ $key ] ?? '' ) ); ?>"
			<?php echo $o['auto'] ? 'autocomplete="' . esc_attr( $o['auto'] ) . '"' : ''; ?>
			<?php echo $o['placeholder'] ? 'placeholder="' . esc_attr( $o['placeholder'] ) . '"' : ''; ?>
			<?php echo $o['inputmode'] ? 'inputmode="' . esc_attr( $o['inputmode'] ) . '"' : ''; ?>
			<?php echo $o['required'] ? 'required' : ''; ?>
			<?php echo $describe( $key ); // phpcs:ignore WordPress.Security.EscapingOutput ?> />
		<?php if ( $o['hint'] ) : ?>
			<p class="wk-field__hint"><?php echo esc_html( $o['hint'] ); ?></p>
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
		<h1 class="wk-hero__title">Bilgiler ve ödeme</h1>
	</div>
</section>

<div class="wk-wrap wk-section" id="odeme-formu">
	<?php if ( ! empty( $errors['form'] ) ) : ?>
		<div class="wk-notice wk-notice--err" role="alert" tabindex="-1" data-focus><p><?php echo esc_html( $errors['form'] ); ?></p></div>
	<?php elseif ( $errors ) : ?>
		<div class="wk-notice wk-notice--err" role="alert" tabindex="-1" data-focus>
			<p><strong>Siparişiniz henüz verilmedi.</strong> Düzeltilmesi gereken <?php echo count( $errors ); ?> alan var; aşağıda kırmızıyla işaretli.</p>
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
				<legend class="wk-panel__title"><span class="wk-panel__num">1</span> İletişim bilgileri</legend>
				<p class="wk-panel__lead">Üyelik gerekmez. Teslimat günü için sizi bu numaradan arayacağız.</p>
				<div class="wk-grid2">
					<?php
					$input( 'first', 'Ad', array( 'auto' => 'given-name' ) );
					$input( 'last', 'Soyad', array( 'auto' => 'family-name' ) );
					$input( 'phone', 'Cep telefonu', array( 'type' => 'tel', 'auto' => 'tel', 'placeholder' => '05XX XXX XX XX' ) );
					$input( 'email', 'E-posta', array( 'type' => 'email', 'auto' => 'email', 'hint' => 'Sipariş numaranızı ve ödeme bilgisini buraya göndeririz.' ) );
					?>
				</div>
			</fieldset>

			<fieldset class="wk-panel">
				<legend class="wk-panel__title"><span class="wk-panel__num">2</span> Teslimat</legend>
				<div class="wk-options" role="radiogroup" aria-label="Teslimat şekli">
					<label class="wk-option">
						<input type="radio" name="wk_delivery" value="adres" <?php checked( $v['delivery'], 'adres' ); ?> data-toggle-target="adres" />
						<span class="wk-option__body">
							<strong>Adrese gönderim</strong>
							<span><?php echo esc_html( nwcs_field( 'global', 'shop', 'shipping_note' ) ); ?></span>
						</span>
						<span class="wk-option__price"><?php echo esc_html( nwcs_field( 'global', 'shop', 'shipping_label' ) ); ?></span>
					</label>
					<?php if ( '' !== $pickup ) : ?>
						<label class="wk-option">
							<input type="radio" name="wk_delivery" value="fabrika" <?php checked( $v['delivery'], 'fabrika' ); ?> data-toggle-target="fabrika" />
							<span class="wk-option__body">
								<strong>Fabrikadan teslim alırım</strong>
								<span><?php echo esc_html( $pickup ); ?></span>
							</span>
							<span class="wk-option__price">Ücretsiz</span>
						</label>
					<?php endif; ?>
				</div>

				<div class="wk-grid2" data-show-when="delivery=adres">
					<div class="wk-field">
						<label for="wk-city">İl</label>
						<select id="wk-city" name="wk_city" autocomplete="address-level1" <?php echo $describe( 'city' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>>
							<option value="">Seçin</option>
							<?php foreach ( wk_provinces() as $province ) : ?>
								<option <?php selected( $v['city'], $province ); ?>><?php echo esc_html( $province ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php $error( 'city' ); ?>
					</div>
					<?php $input( 'district', 'İlçe', array( 'auto' => 'address-level2' ) ); ?>
					<div class="wk-field wk-field--wide">
						<label for="wk-address">Açık adres</label>
						<textarea id="wk-address" name="wk_address" rows="2" autocomplete="street-address" placeholder="Mahalle, cadde/sokak, bina ve daire no" <?php echo $describe( 'address' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>><?php echo esc_textarea( (string) ( $v['address'] ?? '' ) ); ?></textarea>
						<?php $error( 'address' ); ?>
					</div>
					<?php $input( 'postcode', 'Posta kodu', array( 'required' => false, 'auto' => 'postal-code', 'inputmode' => 'numeric' ) ); ?>
				</div>
			</fieldset>

			<fieldset class="wk-panel">
				<legend class="wk-panel__title"><span class="wk-panel__num">3</span> Fatura</legend>
				<div class="wk-check">
					<input id="wk-corporate" name="wk_corporate" type="checkbox" value="1" <?php checked( ! empty( $v['corporate'] ) ); ?> data-toggle-target="corporate" />
					<label for="wk-corporate">Kurumsal fatura istiyorum</label>
				</div>
				<div class="wk-grid2" data-show-when="corporate">
					<?php
					$input( 'company', 'Firma unvanı', array( 'auto' => 'organization', 'wide' => true ) );
					$input( 'tax_office', 'Vergi dairesi' );
					$input( 'tax_no', 'Vergi / TC kimlik no', array( 'inputmode' => 'numeric' ) );
					?>
				</div>
				<div class="wk-check" data-show-when="delivery=adres">
					<input id="wk-bill-diff" name="wk_bill_diff" type="checkbox" value="1" <?php checked( ! empty( $v['bill_diff'] ) ); ?> data-toggle-target="billdiff" />
					<label for="wk-bill-diff">Fatura adresim teslimat adresinden farklı</label>
				</div>
				<div data-show-when="billdiff|delivery=fabrika+corporate">
					<div class="wk-field">
						<label for="wk-bill-addr">Fatura adresi</label>
						<textarea id="wk-bill-addr" name="wk_bill_addr" rows="2" <?php echo $describe( 'bill_addr' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>><?php echo esc_textarea( (string) ( $v['bill_addr'] ?? '' ) ); ?></textarea>
						<?php $error( 'bill_addr' ); ?>
					</div>
				</div>
			</fieldset>

			<fieldset class="wk-panel">
				<legend class="wk-panel__title"><span class="wk-panel__num">4</span> Ödeme</legend>
				<div class="wk-options">
					<label class="wk-option is-only">
						<input type="radio" name="wk_payment" value="havale" checked />
						<span class="wk-option__body">
							<strong>Banka Havalesi / EFT</strong>
							<span><?php echo esc_html( nwcs_field( 'global', 'shop', 'payment_text' ) ); ?></span>
						</span>
					</label>
				</div>
				<p class="wk-panel__lead"><?php echo wk_bank_lines() ? 'Banka hesap bilgileri sipariş numaranızla birlikte bir sonraki sayfada görünür.' : 'Sipariş numaranız bir sonraki sayfada görünür; banka hesap bilgilerini size ayrıca iletiriz.'; ?></p>

				<div class="wk-field">
					<label for="wk-note">Sipariş notu <span class="wk-optional">(isteğe bağlı)</span></label>
					<textarea id="wk-note" name="wk_note" rows="2" placeholder="Teslimat için uygun gün/saat, site giriş bilgisi, renk tercihi…"><?php echo esc_textarea( (string) ( $v['note'] ?? '' ) ); ?></textarea>
				</div>
			</fieldset>
		</div>

		<aside class="wk-summary wk-checkout__side" aria-labelledby="wk-summary-title">
			<h2 id="wk-summary-title" class="wk-summary__title">Sipariş özeti</h2>
			<ul class="wk-mini">
				<?php foreach ( $cart['lines'] as $line ) : ?>
					<li>
						<span class="wk-mini__qty wk-num"><?php echo (int) $line['qty']; ?>×</span>
						<span class="wk-mini__name"><span class="wk-cart__code"><?php echo esc_html( $line['product']['code'] ); ?></span><?php echo esc_html( $line['product']['title'] ); ?></span>
						<span class="wk-num"><?php echo esc_html( wk_money( $line['total'] ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<a class="wk-summary__edit" href="<?php echo esc_url( home_url( '/sepet/' ) ); ?>">Sepeti düzenle</a>
			<?php wk_part( 'order-totals', array( 'totals' => $totals, 'count' => $cart['count'] ) ); ?>

			<div class="wk-check wk-terms">
				<input id="wk-terms" name="wk_terms" type="checkbox" value="1" required <?php checked( ! empty( $v['terms'] ) ); ?> <?php echo $describe( 'terms' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> />
				<label for="wk-terms"><a href="<?php echo esc_url( wk_page_url( 'mesafeli-satis-sozlesmesi' ) ); ?>" target="_blank">Mesafeli Satış Sözleşmesi</a>’ni ve <a href="<?php echo esc_url( wk_page_url( 'iptal-iade-kosullari' ) ); ?>" target="_blank">iptal ve iade koşullarını</a> okudum, kabul ediyorum.</label>
				<?php $error( 'terms' ); ?>
			</div>
			<p class="wk-form__note">Kişisel verileriniz <a href="<?php echo esc_url( wk_page_url( 'kvkk' ) ); ?>" target="_blank">KVKK Aydınlatma Metni</a> kapsamında yalnızca bu sipariş için işlenir.</p>

			<button type="submit" class="wk-btn wk-btn--primary wk-btn--lg wk-btn--block">Siparişi ver</button>
			<p class="wk-form__note wk-center">Ödemeyi sipariş sonrasında havale/EFT ile yaparsınız; bu adımda kart bilgisi istenmez.</p>
		</aside>
	</form>
</div>

<?php
get_footer();
