<?php
/**
 * Talep formu (Cozum Merkezi ve Ozel Uretim). Isleyici: inc/requests.php.
 * $args['kind']: 'iletisim' | 'ozel'. Urun sayfasindan ?urun=<kod ad> ile
 * gelinirse urun secili gelir.
 */

defined( 'ABSPATH' ) || exit;

$kind  = ( $args['kind'] ?? 'iletisim' ) === 'ozel' ? 'ozel' : 'iletisim';
$def   = wk_request_kinds()[ $kind ];
$page  = $def['page']; // Panelde bu formun sayfasi (contact / custom).
$field = static fn( string $name ): string => (string) nwcs_field( 'global', 'forms', $name );
$attr  = static function ( string $name ): void {
	nwcs_edit_attr( 'global', 'forms', $name );
};
$state = wk_request_state();

$errors   = $state['errors'];
$values   = $state['values'];
$selected = $values['product'] ?? ( isset( $_GET['urun'] ) ? sanitize_text_field( wp_unslash( $_GET['urun'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$describe = static function ( string $key ) use ( $errors ): string {
	return isset( $errors[ $key ] ) ? 'aria-invalid="true" aria-describedby="wk-' . esc_attr( $key ) . '-err"' : '';
};

$error = static function ( string $key ) use ( $errors ): void {
	if ( isset( $errors[ $key ] ) ) {
		printf( '<p class="wk-field__error" id="wk-%s-err"%s>%s</p>', esc_attr( $key ), wk_error_attr( 'forms', (string) $errors[ $key ] ), esc_html( $errors[ $key ] ) ); // phpcs:ignore WordPress.Security.EscapingOutput -- nitelik eklentide kacirilir.
	}
};

$input = static function ( string $key, string $label_field, string $type = 'text', string $auto = '', bool $required = true, string $placeholder_field = '' ) use ( $values, $describe, $error, $field, $attr ): void {
	$placeholder = '' !== $placeholder_field ? $field( $placeholder_field ) : '';
	?>
	<div class="wk-field">
		<label for="wk-<?php echo esc_attr( $key ); ?>" <?php $attr( $label_field ); ?>><?php echo esc_html( $field( $label_field ) ); ?><?php if ( ! $required ) : ?> <span class="wk-optional" <?php $attr( 'optional' ); ?>><?php echo esc_html( $field( 'optional' ) ); ?></span><?php endif; ?></label>
		<input id="wk-<?php echo esc_attr( $key ); ?>" name="wk_<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $type ); ?>"
			value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>"
			<?php echo $auto ? 'autocomplete="' . esc_attr( $auto ) . '"' : ''; ?>
			<?php echo $placeholder ? 'placeholder="' . esc_attr( $placeholder ) . '"' : ''; ?>
			<?php echo $required ? 'required' : ''; ?>
			<?php '' !== $placeholder_field && $attr( $placeholder_field ); ?>
			<?php echo $describe( $key ); // phpcs:ignore WordPress.Security.EscapingOutput ?> />
		<?php $error( $key ); ?>
	</div>
	<?php
};
?>
<div id="talep" class="wk-form-wrap">
	<?php if ( $state['success'] && $kind === $state['kind'] ) : ?>
		<div class="wk-notice wk-notice--ok" role="status" tabindex="-1" data-focus>
			<p <?php nwcs_edit_attr( $page, 'request', 'success' ); ?>><strong <?php $attr( 'thanks' ); ?>><?php echo esc_html( $field( 'thanks' ) ); ?></strong> <?php echo esc_html( $def['success'] ); ?></p>
		</div>
	<?php else : ?>
		<?php if ( ! empty( $errors['form'] ) ) : ?>
			<div class="wk-notice wk-notice--err" role="alert" tabindex="-1" data-focus><p <?php echo wk_error_attr( 'forms', (string) $errors['form'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>><?php echo esc_html( $errors['form'] ); ?></p></div>
		<?php elseif ( $errors ) : ?>
			<div class="wk-notice wk-notice--err" role="alert" tabindex="-1" data-focus><p <?php $attr( 'errors_count' ); ?>><?php echo esc_html( wk_text( 'global', 'forms', 'errors_count', array( 'sayi' => count( $errors ) ) ) ); ?></p></div>
		<?php endif; ?>

		<form class="wk-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" <?php echo $def['file'] ? 'enctype="multipart/form-data"' : ''; ?> novalidate>
			<input type="hidden" name="action" value="wk_request" />
			<input type="hidden" name="wk_kind" value="<?php echo esc_attr( $kind ); ?>" />
			<?php wp_nonce_field( 'wk_request', 'wk_request_nonce' ); ?>
			<div class="wk-hp" aria-hidden="true">
				<label for="wk-website">Bu alanı boş bırakın</label>
				<input id="wk-website" name="wk_website" type="text" tabindex="-1" autocomplete="off" />
			</div>

			<?php
			$input( 'name', 'name_label', 'text', 'name' );
			if ( $def['company'] ) {
				$input( 'company', 'company_label', 'text', 'organization', false );
			}
			$input( 'phone', 'phone_label', 'tel', 'tel', true, 'phone_hint' );
			$input( 'email', 'email_label', 'email', 'email' );
			?>

			<div class="wk-field wk-field--wide">
				<label for="wk-department" <?php nwcs_edit_attr( $page, 'request', 'select_label' ); ?>><?php echo esc_html( $def['select'][1] ); ?></label>
				<select id="wk-department" name="wk_department" required <?php echo $describe( 'department' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> <?php nwcs_edit_attr( $page, 'request', 'options' ); ?>>
					<option value="" <?php $attr( 'choose' ); ?>><?php echo esc_html( $field( 'choose' ) ); ?></option>
					<?php foreach ( $def['select'][2] as $option ) : ?>
						<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $values['department'] ?? ( $selected && 'iletisim' === $kind ? ( $def['select'][2][0] ?? '' ) : '' ), $option ); ?> <?php nwcs_edit_attr( $page, 'request', 'options' ); ?>><?php echo esc_html( $option ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php $error( 'department' ); ?>
			</div>

			<?php if ( 'iletisim' === $kind ) : ?>
				<div class="wk-field wk-field--wide">
					<label for="wk-product" <?php $attr( 'product_label' ); ?>><?php echo esc_html( $field( 'product_label' ) ); ?> <span class="wk-optional" <?php $attr( 'optional' ); ?>><?php echo esc_html( $field( 'optional' ) ); ?></span></label>
					<select id="wk-product" name="wk_product" <?php nwcs_edit_attr( 'shop', 'pool', 'pool' ); ?>>
						<option value="" <?php $attr( 'product_none' ); ?>><?php echo esc_html( $field( 'product_none' ) ); ?></option>
						<?php foreach ( wk_products() as $product ) :
							$value = trim( $product['code'] . ' ' . $product['title'] );
							?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected, $value ); ?> <?php wk_product_src( $product, 'Ürün adı' ); ?>><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<div class="wk-field wk-field--wide">
				<label for="wk-message" <?php nwcs_edit_attr( $page, 'request', 'message_label' ); ?>><?php echo esc_html( $def['message'][0] ); ?></label>
				<textarea id="wk-message" name="wk_message" rows="4" placeholder="<?php echo esc_attr( $def['message'][2] ); ?>" <?php nwcs_edit_attr( $page, 'request', 'message_hint' ); ?> <?php echo $def['message'][1] ? 'required' : ''; ?> <?php echo $describe( 'message' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>><?php echo esc_textarea( $values['message'] ?? '' ); ?></textarea>
				<?php $error( 'message' ); ?>
			</div>

			<?php if ( $def['file'] ) : ?>
				<div class="wk-field wk-field--wide">
					<label for="wk-file" <?php $attr( 'file_label' ); ?>><?php echo esc_html( $field( 'file_label' ) ); ?> <span class="wk-optional" <?php $attr( 'optional' ); ?>><?php echo esc_html( $field( 'optional' ) ); ?></span></label>
					<input id="wk-file" name="wk_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.dwg" aria-describedby="wk-file-hint<?php echo isset( $errors['file'] ) ? ' wk-file-err' : ''; ?>" />
					<p class="wk-field__hint" id="wk-file-hint" <?php $attr( 'file_help' ); ?>><?php echo esc_html( $field( 'file_help' ) ); ?></p>
					<?php $error( 'file' ); ?>
				</div>
			<?php endif; ?>

			<div class="wk-field wk-field--wide wk-check">
				<input id="wk-consent" name="wk_consent" type="checkbox" value="1" required <?php checked( ! empty( $values['consent'] ) ); ?> <?php echo $describe( 'consent' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> />
				<label for="wk-consent" <?php $attr( 'consent' ); ?>><?php echo wk_text_html( 'global', 'forms', 'consent', array( 'kvkk' => '<a href="' . esc_url( wk_page_url( 'kvkk' ) ) . '" target="_blank"' . wk_attr_string( 'global', 'forms', 'consent_link' ) . '>' . esc_html( $field( 'consent_link' ) ) . '</a>' ) ); // phpcs:ignore WordPress.Security.EscapingOutput -- metin kacirildi. ?></label>
				<?php $error( 'consent' ); ?>
			</div>

			<div class="wk-field--wide"><button type="submit" class="wk-btn wk-btn--primary wk-btn--lg" <?php nwcs_edit_attr( $page, 'request', 'button' ); ?>><?php echo esc_html( $def['button'] ); ?></button></div>
		</form>
	<?php endif; ?>
</div>
