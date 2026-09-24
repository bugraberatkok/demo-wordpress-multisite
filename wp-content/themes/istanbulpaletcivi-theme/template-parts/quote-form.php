<?php
/**
 * Siparis ve fiyat formu (ana sayfa ve Iletisim). Isleyici: inc/quote.php.
 * Urun sayfasindan ?urun=<ad> ile gelinirse urun secili gelir.
 */

defined( 'ABSPATH' ) || exit;

$state    = pc_quote_state();
$errors   = $state['errors'];
$values   = $state['values'];
$selected = $values['product'] ?? ( isset( $_GET['urun'] ) ? sanitize_text_field( wp_unslash( $_GET['urun'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$field = static function ( string $key, string $label, string $type = 'text', array $extra = array() ) use ( $errors, $values ): void {
	$id    = 'pc-' . $key;
	$error = $errors[ $key ] ?? '';
	?>
	<div class="pc-field<?php echo ! empty( $extra['wide'] ) ? ' pc-field--wide' : ''; ?>">
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<?php if ( 'textarea' === $type ) : ?>
			<textarea id="<?php echo esc_attr( $id ); ?>" name="pc_<?php echo esc_attr( $key ); ?>" rows="4"
				<?php echo $error ? 'aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '-err"' : ''; ?>
				<?php echo ! empty( $extra['placeholder'] ) ? 'placeholder="' . esc_attr( $extra['placeholder'] ) . '"' : ''; ?>><?php echo esc_textarea( $values[ $key ] ?? '' ); ?></textarea>
		<?php else : ?>
			<input id="<?php echo esc_attr( $id ); ?>" name="pc_<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $type ); ?>"
				value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>"
				<?php echo ! empty( $extra['autocomplete'] ) ? 'autocomplete="' . esc_attr( $extra['autocomplete'] ) . '"' : ''; ?>
				<?php echo $error ? 'aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '-err"' : ''; ?> />
		<?php endif; ?>
		<?php if ( $error ) : ?>
			<p class="pc-field__error" id="<?php echo esc_attr( $id ); ?>-err"><?php echo esc_html( $error ); ?></p>
		<?php endif; ?>
	</div>
	<?php
};
?>
<div id="siparis" class="pc-form-wrap">
	<?php if ( $state['success'] ) : ?>
		<div class="pc-notice pc-notice--ok" role="status" tabindex="-1" data-focus>
			<p><strong>İsteğiniz bize ulaştı.</strong> En kısa sürede sizi arayacağız ya da e-postayla dönüş yapacağız. Acil siparişte telefonla ulaşın.</p>
		</div>
	<?php else : ?>
		<?php if ( ! empty( $errors['form'] ) ) : ?>
			<div class="pc-notice pc-notice--err" role="alert"><p><?php echo esc_html( $errors['form'] ); ?></p></div>
		<?php elseif ( $errors ) : ?>
			<div class="pc-notice pc-notice--err" role="alert" tabindex="-1" data-focus><p>Formda eksik ya da hatalı alanlar var; aşağıda işaretli.</p></div>
		<?php endif; ?>

		<form class="pc-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
			<input type="hidden" name="action" value="pc_quote" />
			<?php wp_nonce_field( 'pc_quote', 'pc_quote_nonce' ); ?>
			<div class="pc-hp" aria-hidden="true">
				<label for="pc-website">Bu alanı boş bırakın</label>
				<input id="pc-website" name="pc_website" type="text" tabindex="-1" autocomplete="off" />
			</div>

			<?php
			$field( 'name', 'Ad soyad', 'text', array( 'autocomplete' => 'name' ) );
			$field( 'company', 'Firma (isteğe bağlı)', 'text', array( 'autocomplete' => 'organization' ) );
			$field( 'phone', 'Telefon', 'tel', array( 'autocomplete' => 'tel' ) );
			$field( 'email', 'E-posta', 'email', array( 'autocomplete' => 'email' ) );
			?>

			<div class="pc-field pc-field--wide">
				<label for="pc-product">Çivi tipi</label>
				<select id="pc-product" name="pc_product">
					<option value="">Emin değilim</option>
					<?php foreach ( pc_products() as $product ) : ?>
						<option value="<?php echo esc_attr( $product['name'] ); ?>" <?php selected( $selected, $product['name'] ); ?>>
							<?php echo esc_html( $product['name'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<?php
			$field( 'size', 'Ölçü ve adet', 'textarea', array( 'wide' => true, 'placeholder' => 'Örneğin: boy, kalınlık, kaç kutu ya da kilo; tabancanızın modeli' ) );
			$field( 'message', 'Mesajınız (isteğe bağlı)', 'textarea', array( 'wide' => true ) );
			?>

			<p class="pc-form__note pc-field--wide">Telefon ya da e-postadan birini yazmanız yeterli.</p>

			<div class="pc-field--wide">
				<button type="submit" class="pc-btn pc-btn--navy pc-btn--lg">İsteği gönder</button>
			</div>
		</form>
	<?php endif; ?>
</div>
