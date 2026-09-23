<?php
/**
 * Teklif formu. Ana sayfa ve Iletisim ayni parcayi kullanir; gonderim
 * ip_handle_quote() tarafindan dogrulanip "Teklif Istekleri" olarak kaydedilir.
 *
 * Urun secenekleri urun sayfalarindan gelir. "Bu urun icin teklif alin"
 * dugmesi urun adini ?urun= ile tasir; hatali gonderimden sonra ise
 * kullanicinin kendi secimi oncelikli.
 */

defined( 'ABSPATH' ) || exit;

$heading = tag_escape( $args['heading'] ?? 'h3' );
$state   = ip_quote_state();
$errors  = $state['errors'];
$values  = $state['values'];

$requested = isset( $_GET['urun'] ) ? sanitize_text_field( wp_unslash( $_GET['urun'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$picked    = '' !== ( $values['product'] ?? '' ) ? $values['product'] : $requested;

$input  = 'w-full rounded-[3px] border bg-sheet px-4 py-3 text-base text-ink transition-colors duration-150 placeholder:text-steel/70 focus:outline-none focus:ring-2';
$ok     = $input . ' border-ink/20 focus:border-indigo focus:ring-indigo/25';
$broken = $input . ' border-alert bg-alert-soft focus:border-alert focus:ring-alert/25';
$label  = 'mb-2 block text-[0.9375rem] font-medium text-ink';

/**
 * Tek satirlik alan kurucusu: etiket + girdi + hata.
 */
$field = static function ( string $key, string $type, bool $required, string $autocomplete = '' ) use ( $values, $errors, $ok, $broken, $label ): void {
	$id    = 'ip_' . $key;
	$error = $errors[ $key ] ?? '';
	?>
	<div>
		<label for="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', $key . '_label' ); ?>>
			<?php echo esc_html( nwcs_field( 'contact', 'form', $key . '_label' ) ); ?>
			<?php if ( $required ) : ?><span class="text-alert" aria-hidden="true">*</span><?php endif; ?>
		</label>
		<input type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>"
			value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>"
			<?php echo $autocomplete ? 'autocomplete="' . esc_attr( $autocomplete ) . '"' : ''; ?>
			<?php echo $required ? 'required aria-required="true"' : ''; ?>
			<?php echo $error ? 'aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '_err"' : ''; ?>
			class="<?php echo esc_attr( $error ? $broken : $ok ); ?>" />
		<?php if ( $error ) : ?>
			<p id="<?php echo esc_attr( $id ); ?>_err" class="mt-2 text-sm text-alert"><?php echo esc_html( $error ); ?></p>
		<?php endif; ?>
	</div>
	<?php
};
?>
<?php if ( $state['success'] ) : ?>

	<div role="status">
		<span class="flex h-12 w-12 items-center justify-center rounded-[3px] bg-indigo text-sheet" aria-hidden="true">
			<?php nwcs_the_icon( 'check', '', 24 ); ?>
		</span>

		<<?php echo $heading; // phpcs:ignore ?> class="mt-6 font-display text-3xl font-semibold" <?php nwcs_edit_attr( 'contact', 'form', 'success_title' ); ?>>
			<?php echo esc_html( nwcs_field( 'contact', 'form', 'success_title' ) ); ?>
		</<?php echo $heading; // phpcs:ignore ?>>

		<p class="mt-3 max-w-[32rem] text-lg leading-relaxed text-steel" <?php nwcs_edit_attr( 'contact', 'form', 'success_text' ); ?>>
			<?php echo esc_html( nwcs_field( 'contact', 'form', 'success_text' ) ); ?>
		</p>

		<a href="<?php echo esc_url( remove_query_arg( array( 'ip', 'urun' ) ) . '#teklif' ); ?>" class="btn btn--md btn--outline mt-8">
			Yeni bir istek gönderin
		</a>
	</div>

<?php else : ?>

	<<?php echo $heading; // phpcs:ignore ?> class="font-display text-3xl font-semibold" <?php nwcs_edit_attr( 'contact', 'form', 'title' ); ?>>
		<?php echo esc_html( nwcs_field( 'contact', 'form', 'title' ) ); ?>
	</<?php echo $heading; // phpcs:ignore ?>>

	<p class="mt-2 text-steel" <?php nwcs_edit_attr( 'contact', 'form', 'note' ); ?>>
		<?php echo esc_html( nwcs_field( 'contact', 'form', 'note' ) ); ?>
	</p>

	<?php if ( ! empty( $errors['form'] ) ) : ?>
		<p class="mt-6 rounded-[3px] border border-alert bg-alert-soft px-4 py-3 text-alert" role="alert">
			<?php echo esc_html( $errors['form'] ); ?>
		</p>
	<?php elseif ( $errors ) : ?>
		<p class="mt-6 rounded-[3px] border border-alert bg-alert-soft px-4 py-3 text-alert" role="alert">
			Formda eksik ya da hatalı alanlar var; işaretli alanları düzeltip tekrar gönderin.
		</p>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mt-8 grid gap-5 sm:grid-cols-2" novalidate>
		<input type="hidden" name="action" value="ip_quote" />
		<?php wp_nonce_field( 'ip_quote', 'ip_quote_nonce' ); ?>

		<p class="hidden" aria-hidden="true">
			<label>Bu alanı boş bırakın
				<input type="text" name="ip_website" tabindex="-1" autocomplete="off" />
			</label>
		</p>

		<?php
		$field( 'name', 'text', true, 'name' );
		$field( 'company', 'text', false, 'organization' );
		$field( 'email', 'email', false, 'email' );
		$field( 'phone', 'tel', false, 'tel' );
		?>

		<div class="sm:col-span-2">
			<label for="ip_product" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'product_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'contact', 'form', 'product_label' ) ); ?>
			</label>
			<select id="ip_product" name="ip_product" class="<?php echo esc_attr( $ok ); ?>">
				<option value="">Seçin</option>
				<?php foreach ( ip_products() as $product ) : ?>
					<option value="<?php echo esc_attr( $product['name'] ); ?>" <?php selected( $picked, $product['name'] ); ?>>
						<?php echo esc_html( $product['name'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="sm:col-span-2">
			<label for="ip_size" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'size_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'contact', 'form', 'size_label' ) ); ?>
				<span class="text-alert" aria-hidden="true">*</span>
			</label>
			<textarea id="ip_size" name="ip_size" rows="2" required aria-required="true"
				placeholder="<?php echo esc_attr( nwcs_field( 'contact', 'form', 'size_hint' ) ); ?>"
				<?php echo isset( $errors['size'] ) ? 'aria-invalid="true" aria-describedby="ip_size_err"' : ''; ?>
				class="<?php echo esc_attr( isset( $errors['size'] ) ? $broken : $ok ); ?>"><?php echo esc_textarea( $values['size'] ?? '' ); ?></textarea>
			<?php if ( isset( $errors['size'] ) ) : ?>
				<p id="ip_size_err" class="mt-2 text-sm text-alert"><?php echo esc_html( $errors['size'] ); ?></p>
			<?php endif; ?>
		</div>

		<div class="sm:col-span-2">
			<label for="ip_message" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'message_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'contact', 'form', 'message_label' ) ); ?>
			</label>
			<textarea id="ip_message" name="ip_message" rows="4" class="<?php echo esc_attr( $ok ); ?>"><?php echo esc_textarea( $values['message'] ?? '' ); ?></textarea>
		</div>

		<div class="flex flex-col gap-4 pt-2 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
			<button type="submit" class="btn btn--lg btn--solid shrink-0" <?php nwcs_edit_attr( 'contact', 'form', 'submit_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'contact', 'form', 'submit_label' ) ); ?>
			</button>

			<p class="max-w-[20rem] text-sm text-steel" <?php nwcs_edit_attr( 'contact', 'form', 'privacy_note' ); ?>>
				<?php echo esc_html( nwcs_field( 'contact', 'form', 'privacy_note' ) ); ?>
			</p>
		</div>
	</form>

<?php endif; ?>
