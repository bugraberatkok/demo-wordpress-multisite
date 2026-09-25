<?php
/**
 * Talep formu (Cozum Merkezi ve Ozel Uretim). Isleyici: inc/requests.php.
 * $args['kind']: 'iletisim' | 'ozel'. Urun sayfasindan ?urun=<kod ad> ile
 * gelinirse urun secili gelir.
 */

defined( 'ABSPATH' ) || exit;

$kind  = ( $args['kind'] ?? 'iletisim' ) === 'ozel' ? 'ozel' : 'iletisim';
$def   = wk_request_kinds()[ $kind ];
$state = wk_request_state();

$errors   = $state['errors'];
$values   = $state['values'];
$selected = $values['product'] ?? ( isset( $_GET['urun'] ) ? sanitize_text_field( wp_unslash( $_GET['urun'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$describe = static function ( string $key ) use ( $errors ): string {
	return isset( $errors[ $key ] ) ? 'aria-invalid="true" aria-describedby="wk-' . esc_attr( $key ) . '-err"' : '';
};

$error = static function ( string $key ) use ( $errors ): void {
	if ( isset( $errors[ $key ] ) ) {
		printf( '<p class="wk-field__error" id="wk-%s-err">%s</p>', esc_attr( $key ), esc_html( $errors[ $key ] ) );
	}
};

$input = static function ( string $key, string $label, string $type = 'text', string $auto = '', bool $required = true, string $placeholder = '' ) use ( $values, $describe, $error ): void {
	?>
	<div class="wk-field">
		<label for="wk-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?><?php echo $required ? '' : ' <span class="wk-optional">(isteğe bağlı)</span>'; ?></label>
		<input id="wk-<?php echo esc_attr( $key ); ?>" name="wk_<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $type ); ?>"
			value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>"
			<?php echo $auto ? 'autocomplete="' . esc_attr( $auto ) . '"' : ''; ?>
			<?php echo $placeholder ? 'placeholder="' . esc_attr( $placeholder ) . '"' : ''; ?>
			<?php echo $required ? 'required' : ''; ?>
			<?php echo $describe( $key ); // phpcs:ignore WordPress.Security.EscapingOutput ?> />
		<?php $error( $key ); ?>
	</div>
	<?php
};
?>
<div id="talep" class="wk-form-wrap">
	<?php if ( $state['success'] && $kind === $state['kind'] ) : ?>
		<div class="wk-notice wk-notice--ok" role="status" tabindex="-1" data-focus>
			<p><strong>Teşekkürler.</strong> <?php echo esc_html( $def['success'] ); ?></p>
		</div>
	<?php else : ?>
		<?php if ( ! empty( $errors['form'] ) ) : ?>
			<div class="wk-notice wk-notice--err" role="alert" tabindex="-1" data-focus><p><?php echo esc_html( $errors['form'] ); ?></p></div>
		<?php elseif ( $errors ) : ?>
			<div class="wk-notice wk-notice--err" role="alert" tabindex="-1" data-focus><p>Formda düzeltilmesi gereken <?php echo count( $errors ); ?> alan var; aşağıda kırmızıyla işaretli.</p></div>
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
			$input( 'name', 'Ad soyad', 'text', 'name' );
			if ( $def['company'] ) {
				$input( 'company', 'Firma unvanı', 'text', 'organization', false );
			}
			$input( 'phone', 'Telefon', 'tel', 'tel', true, '05XX XXX XX XX' );
			$input( 'email', 'E-posta', 'email', 'email' );
			?>

			<div class="wk-field wk-field--wide">
				<label for="wk-department"><?php echo esc_html( $def['select'][1] ); ?></label>
				<select id="wk-department" name="wk_department" required <?php echo $describe( 'department' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>>
					<option value="">Seçin</option>
					<?php foreach ( $def['select'][2] as $option ) : ?>
						<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $values['department'] ?? ( $selected && 'iletisim' === $kind ? 'Genel Bilgi ve Destek' : '' ), $option ); ?>><?php echo esc_html( $option ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php $error( 'department' ); ?>
			</div>

			<?php if ( 'iletisim' === $kind ) : ?>
				<div class="wk-field wk-field--wide">
					<label for="wk-product">Ürün <span class="wk-optional">(isteğe bağlı)</span></label>
					<select id="wk-product" name="wk_product">
						<option value="">Belirli bir ürün değil</option>
						<?php foreach ( wk_products() as $product ) :
							$value = trim( $product['code'] . ' ' . $product['title'] );
							?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected, $value ); ?>><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<div class="wk-field wk-field--wide">
				<label for="wk-message"><?php echo esc_html( $def['message'][0] ); ?></label>
				<textarea id="wk-message" name="wk_message" rows="4" placeholder="<?php echo esc_attr( $def['message'][2] ); ?>" <?php echo $def['message'][1] ? 'required' : ''; ?> <?php echo $describe( 'message' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>><?php echo esc_textarea( $values['message'] ?? '' ); ?></textarea>
				<?php $error( 'message' ); ?>
			</div>

			<?php if ( $def['file'] ) : ?>
				<div class="wk-field wk-field--wide">
					<label for="wk-file">Teknik çizim ya da referans görsel <span class="wk-optional">(isteğe bağlı)</span></label>
					<input id="wk-file" name="wk_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.dwg" aria-describedby="wk-file-hint<?php echo isset( $errors['file'] ) ? ' wk-file-err' : ''; ?>" />
					<p class="wk-field__hint" id="wk-file-hint">En fazla 5 MB. PDF, JPG, PNG ya da DWG.</p>
					<?php $error( 'file' ); ?>
				</div>
			<?php endif; ?>

			<div class="wk-field wk-field--wide wk-check">
				<input id="wk-consent" name="wk_consent" type="checkbox" value="1" required <?php checked( ! empty( $values['consent'] ) ); ?> <?php echo $describe( 'consent' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> />
				<label for="wk-consent"><a href="<?php echo esc_url( wk_page_url( 'kvkk' ) ); ?>" target="_blank">KVKK Aydınlatma Metni</a>’ni okudum; kişisel verilerimin bu talep için işlenmesini kabul ediyorum.</label>
				<?php $error( 'consent' ); ?>
			</div>

			<div class="wk-field--wide"><button type="submit" class="wk-btn wk-btn--primary wk-btn--lg"><?php echo esc_html( $def['button'] ); ?></button></div>
		</form>
	<?php endif; ?>
</div>
