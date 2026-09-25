<?php
/**
 * Teklif formu. Teklif penceresinde ve Iletisim sayfasinda ayni parca.
 *
 * $args:
 *   prefix  kimlik on eki (sayfada iki form olabilir)
 *   heading baslik etiketi (h2 / h3)
 *
 * Hesaplayicidan "teklif al" denirse olcu alanini site.js doldurur. Urun,
 * dugmenin tasidigi addan ya da ?urun= adresinden secili gelir.
 */

defined( 'ABSPATH' ) || exit;

$prefix  = sanitize_html_class( $args['prefix'] ?? 'kr' );
$heading = tag_escape( $args['heading'] ?? 'h2' );
$state   = kr_quote_state();
$errors  = $state['errors'];
$values  = $state['values'];

$requested = isset( $_GET['urun'] ) ? sanitize_text_field( wp_unslash( $_GET['urun'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$picked    = '' !== ( $values['product'] ?? '' ) ? $values['product'] : $requested;

$input  = 'w-full border bg-paper px-3.5 py-2.5 text-base text-ink placeholder:text-muted/70 focus:outline-none focus:ring-2';
$ok     = $input . ' border-ink/25 focus:border-mark focus:ring-mark/25';
$broken = $input . ' border-alert bg-alert-soft focus:border-alert focus:ring-alert/25';
$label  = 'mb-1.5 block text-[0.9375rem] font-semibold';

// $title: alan adinin panel alani (contact.form.label_*).
$field = static function ( string $key, string $title, string $type, bool $required, string $autocomplete ) use ( $prefix, $values, $errors, $ok, $broken, $label ): void {
	$id                     = $prefix . '-' . $key;
	list( $error, $err_at ) = kr_quote_error( (string) ( $errors[ $key ] ?? '' ) );
	?>
	<div>
		<label for="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $label ); ?>">
			<span <?php nwcs_edit_attr( 'contact', 'form', $title ); ?>><?php echo esc_html( nwcs_field( 'contact', 'form', $title ) ); ?></span><?php if ( $required ) : ?> <span class="text-alert" aria-hidden="true">*</span><?php endif; ?>
		</label>
		<input type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $id ); ?>" name="kr_<?php echo esc_attr( $key ); ?>"
			value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>" autocomplete="<?php echo esc_attr( $autocomplete ); ?>"
			<?php echo $required ? 'required' : ''; ?>
			<?php echo $error ? 'aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '-err"' : ''; ?>
			class="<?php echo esc_attr( $error ? $broken : $ok ); ?>" />
		<?php if ( $error ) : ?>
			<p id="<?php echo esc_attr( $id ); ?>-err" class="mt-1.5 text-sm text-alert" <?php kr_form_attr( $err_at ); ?>><?php echo esc_html( $error ); ?></p>
		<?php endif; ?>
	</div>
	<?php
};
?>
<?php if ( $state['success'] ) : ?>

	<div role="status">
		<<?php echo $heading; // phpcs:ignore ?> class="text-3xl" <?php nwcs_edit_attr( 'contact', 'form', 'success_title' ); ?>>
			<?php echo esc_html( nwcs_field( 'contact', 'form', 'success_title' ) ); ?>
		</<?php echo $heading; // phpcs:ignore ?>>
		<p class="mt-3 text-lg text-muted" <?php nwcs_edit_attr( 'contact', 'form', 'success_text' ); ?>>
			<?php echo esc_html( nwcs_field( 'contact', 'form', 'success_text' ) ); ?>
		</p>
	</div>

<?php else : ?>

	<<?php echo $heading; // phpcs:ignore ?> class="text-3xl" <?php nwcs_edit_attr( 'contact', 'form', 'title' ); ?>>
		<?php echo esc_html( nwcs_field( 'contact', 'form', 'title' ) ); ?>
	</<?php echo $heading; // phpcs:ignore ?>>
	<p class="mt-2 text-muted" <?php nwcs_edit_attr( 'contact', 'form', 'note' ); ?>>
		<?php echo esc_html( nwcs_field( 'contact', 'form', 'note' ) ); ?>
	</p>

	<?php if ( $errors ) :
		list( $summary, $summary_at ) = kr_quote_error( (string) ( $errors['form'] ?? 'error_summary' ) );
		?>
		<p class="mt-5 border border-alert bg-alert-soft px-4 py-3 text-alert" role="alert" <?php kr_form_attr( $summary_at ); ?>>
			<?php echo esc_html( $summary ); ?>
		</p>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mt-6 grid gap-4 sm:grid-cols-2" novalidate data-kr-form>
		<input type="hidden" name="action" value="kr_quote" />
		<?php wp_nonce_field( 'kr_quote', 'kr_quote_nonce', false ); ?>
		<p class="hidden" aria-hidden="true"><label>Bu alanı boş bırakın <input type="text" name="kr_website" tabindex="-1" autocomplete="off" /></label></p>

		<?php
		$field( 'name', 'label_name', 'text', true, 'name' );
		$field( 'company', 'label_company', 'text', false, 'organization' );
		$field( 'phone', 'label_phone', 'tel', false, 'tel' );
		$field( 'email', 'label_email', 'email', false, 'email' );
		?>

		<div class="sm:col-span-2">
			<label for="<?php echo esc_attr( $prefix ); ?>-product" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'label_product' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'form', 'label_product' ) ); ?></label>
			<select id="<?php echo esc_attr( $prefix ); ?>-product" name="kr_product" data-kr-product class="<?php echo esc_attr( $ok ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'product_empty' ); ?>>
				<option value=""><?php echo esc_html( nwcs_field( 'contact', 'form', 'product_empty' ) ); ?></option>
				<?php foreach ( kr_products() as $product ) : ?>
					<option value="<?php echo esc_attr( $product['name'] ); ?>" <?php selected( $picked, $product['name'] ); ?>><?php echo esc_html( $product['name'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="sm:col-span-2">
			<label for="<?php echo esc_attr( $prefix ); ?>-size" class="<?php echo esc_attr( $label ); ?>"><span <?php nwcs_edit_attr( 'contact', 'form', 'label_size' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'form', 'label_size' ) ); ?></span> <span class="text-alert" aria-hidden="true">*</span></label>
			<textarea id="<?php echo esc_attr( $prefix ); ?>-size" name="kr_size" rows="2" required <?php nwcs_edit_attr( 'contact', 'form', 'size_hint' ); ?>
				placeholder="<?php echo esc_attr( nwcs_field( 'contact', 'form', 'size_hint' ) ); ?>"
				<?php echo isset( $errors['size'] ) ? 'aria-invalid="true" aria-describedby="' . esc_attr( $prefix ) . '-size-err"' : ''; ?>
				class="<?php echo esc_attr( isset( $errors['size'] ) ? $broken : $ok ); ?>"><?php echo esc_textarea( $values['size'] ?? '' ); ?></textarea>
			<?php if ( isset( $errors['size'] ) ) :
				list( $size_error, $size_at ) = kr_quote_error( (string) $errors['size'] );
				?>
				<p id="<?php echo esc_attr( $prefix ); ?>-size-err" class="mt-1.5 text-sm text-alert" <?php kr_form_attr( $size_at ); ?>><?php echo esc_html( $size_error ); ?></p>
			<?php endif; ?>
		</div>

		<div class="sm:col-span-2">
			<label for="<?php echo esc_attr( $prefix ); ?>-message" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'label_message' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'form', 'label_message' ) ); ?></label>
			<textarea id="<?php echo esc_attr( $prefix ); ?>-message" name="kr_message" rows="3" class="<?php echo esc_attr( $ok ); ?>"><?php echo esc_textarea( $values['message'] ?? '' ); ?></textarea>
		</div>

		<div class="flex flex-col gap-3 pt-1 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
			<button type="submit" class="btn btn--lg btn--mark" <?php nwcs_edit_attr( 'contact', 'form', 'submit_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'contact', 'form', 'submit_label' ) ); ?>
			</button>
			<p class="max-w-[20rem] text-sm text-muted" <?php nwcs_edit_attr( 'contact', 'form', 'privacy_note' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'form', 'privacy_note' ) ); ?></p>
		</div>
	</form>

<?php endif; ?>
