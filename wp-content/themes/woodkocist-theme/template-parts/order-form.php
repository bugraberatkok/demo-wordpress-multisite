<?php
/**
 * Siparis formu (ana sayfa ve Iletisim). Isleyici: inc/order.php.
 * Urun sayfasindan ?urun=<kod ad> ile gelinirse urun secili gelir.
 */

defined( 'ABSPATH' ) || exit;

$state    = wk_order_state();
$errors   = $state['errors'];
$values   = $state['values'];
$selected = $values['product'] ?? ( isset( $_GET['urun'] ) ? sanitize_text_field( wp_unslash( $_GET['urun'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$input = static function ( string $key, string $label, string $type = 'text', string $auto = '' ) use ( $errors, $values ): void {
	$id    = 'wk-' . $key;
	$error = $errors[ $key ] ?? '';
	?>
	<div class="wk-field">
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input id="<?php echo esc_attr( $id ); ?>" name="wk_<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $type ); ?>"
			value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>"
			<?php echo $auto ? 'autocomplete="' . esc_attr( $auto ) . '"' : ''; ?>
			<?php echo $error ? 'aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '-err"' : ''; ?> />
		<?php if ( $error ) : ?>
			<p class="wk-field__error" id="<?php echo esc_attr( $id ); ?>-err"><?php echo esc_html( $error ); ?></p>
		<?php endif; ?>
	</div>
	<?php
};
?>
<div id="siparis" class="wk-form-wrap">
	<?php if ( $state['success'] ) : ?>
		<div class="wk-notice wk-notice--ok" role="status" tabindex="-1" data-focus>
			<p><strong>İsteğiniz bize ulaştı.</strong> Fiyat ve teslim bilgisiyle size dönüş yapacağız.</p>
		</div>
	<?php else : ?>
		<?php if ( ! empty( $errors['form'] ) ) : ?>
			<div class="wk-notice wk-notice--err" role="alert"><p><?php echo esc_html( $errors['form'] ); ?></p></div>
		<?php elseif ( $errors ) : ?>
			<div class="wk-notice wk-notice--err" role="alert" tabindex="-1" data-focus><p>Formda eksik alanlar var; aşağıda işaretli.</p></div>
		<?php endif; ?>

		<form class="wk-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
			<input type="hidden" name="action" value="wk_order" />
			<?php wp_nonce_field( 'wk_order', 'wk_order_nonce' ); ?>
			<div class="wk-hp" aria-hidden="true">
				<label for="wk-website">Bu alanı boş bırakın</label>
				<input id="wk-website" name="wk_website" type="text" tabindex="-1" autocomplete="off" />
			</div>

			<?php
			$input( 'name', 'Ad soyad', 'text', 'name' );
			$input( 'phone', 'Telefon', 'tel', 'tel' );
			$input( 'email', 'E-posta (isteğe bağlı)', 'email', 'email' );
			?>

			<div class="wk-field wk-field--wide">
				<label for="wk-product">Ürün</label>
				<select id="wk-product" name="wk_product">
					<option value="">Henüz seçmedim</option>
					<?php foreach ( wk_products() as $product ) :
						$value = trim( $product['code'] . ' ' . $product['title'] );
						?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected, $value ); ?>><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<?php $input( 'size', 'Adet ve teslim ili' ); ?>

			<div class="wk-field wk-field--wide">
				<label for="wk-message">Mesajınız (isteğe bağlı)</label>
				<textarea id="wk-message" name="wk_message" rows="3"><?php echo esc_textarea( $values['message'] ?? '' ); ?></textarea>
			</div>

			<p class="wk-form__note wk-field--wide">Telefon ya da e-postadan birini yazmanız yeterli.</p>
			<div class="wk-field--wide"><button type="submit" class="wk-btn wk-btn--primary wk-btn--lg">Sipariş isteğini gönder</button></div>
		</form>
	<?php endif; ?>
</div>
