<?php
/**
 * Iletisim: bilgiler, mesaj formu, harita.
 *
 * Form admin-post.php'ye gider ve mesaji gercekten kaydeder
 * (sanayi_palet_handle_message). Harita anahtarsiz Google Maps gomme adresiyle
 * acilir; yalnizca sayfa bu bolume geldiginde yuklenir (loading="lazy").
 */

defined( 'ABSPATH' ) || exit;

get_header();

$state  = sanayi_palet_message_state();
$errors = $state['errors'];
$values = $state['values'];
$query  = nwcs_field( 'contact', 'map', 'query' );

// Urun kartlarindaki "teklif isteyin" baglantisi konuyu tasir (?konu=Euro palet).
// Hatali gonderimden donuldugunde kullanicinin kendi yazdigi one gecer.
if ( ! isset( $values['subject'] ) && isset( $_GET['konu'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- yalnizca on doldurma.
	$values['subject'] = sprintf( '%s için teklif', sanitize_text_field( wp_unslash( $_GET['konu'] ) ) );
}

$info = array(
	array( 'icon' => 'pin', 'label' => 'address_label', 'value' => 'address', 'url' => '' ),
	array( 'icon' => 'phone', 'label' => 'phone_label', 'value' => 'phone', 'url' => 'phone_url' ),
	array( 'icon' => 'whatsapp', 'label' => 'whatsapp_label', 'value' => 'whatsapp', 'url' => 'whatsapp_url' ),
	array( 'icon' => 'mail', 'label' => 'email_label', 'value' => 'email', 'url' => 'email_url' ),
);

/**
 * Tek form alani: etiket, girdi, varsa hata.
 */
$field = static function ( string $key, string $label, string $type, bool $required, string $autocomplete ) use ( $errors, $values ): void {
	$id    = 'sp-' . $key;
	$error = $errors[ $key ] ?? '';
	?>
	<div class="sp-field<?php echo 'message' === $key ? ' sp-field--wide' : ''; ?>">
		<label class="sp-field__label" for="<?php echo esc_attr( $id ); ?>">
			<?php echo esc_html( $label ); ?>
			<?php if ( ! $required ) : ?>
				<span class="sp-field__optional">(isteğe bağlı)</span>
			<?php endif; ?>
		</label>

		<?php if ( 'textarea' === $type ) : ?>
			<textarea class="sp-field__input" id="<?php echo esc_attr( $id ); ?>" name="sp_<?php echo esc_attr( $key ); ?>" rows="6"<?php echo $required ? ' required' : ''; ?><?php echo $error ? ' aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '-error"' : ''; ?>><?php echo esc_textarea( $values[ $key ] ?? '' ); ?></textarea>
		<?php else : ?>
			<input class="sp-field__input" id="<?php echo esc_attr( $id ); ?>" name="sp_<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $type ); ?>" autocomplete="<?php echo esc_attr( $autocomplete ); ?>" value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>"<?php echo $required ? ' required' : ''; ?><?php echo $error ? ' aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '-error"' : ''; ?> />
		<?php endif; ?>

		<?php if ( $error ) : ?>
			<p class="sp-field__error" id="<?php echo esc_attr( $id ); ?>-error"><?php echo esc_html( $error ); ?></p>
		<?php endif; ?>
	</div>
	<?php
};

get_template_part( 'template-parts/page-head', null, array( 'page' => 'contact' ) );
?>

<section class="sp-section sp-contact">
	<div class="sp-wrap sp-contact__grid">

		<ul class="sp-contact__info">
			<?php foreach ( $info as $row ) : ?>
				<?php
				$value = nwcs_field( 'contact', 'info', $row['value'] );
				$url   = $row['url'] ? nwcs_field( 'contact', 'info', $row['url'] ) : '';

				if ( '' === trim( $value ) ) {
					continue;
				}
				?>
				<li class="sp-contact__item">
					<span class="sp-contact__icon"><?php sanayi_palet_icon( $row['icon'], 24 ); ?></span>
					<div>
						<h2 class="sp-contact__label" <?php nwcs_edit_attr( 'contact', 'info', $row['label'] ); ?>><?php echo esc_html( nwcs_field( 'contact', 'info', $row['label'] ) ); ?></h2>
						<?php if ( $url ) : ?>
							<a class="sp-contact__value" href="<?php echo esc_url( sanayi_palet_link( $url ) ); ?>" <?php nwcs_edit_attr( 'contact', 'info', $row['value'] ); ?>><?php echo esc_html( $value ); ?></a>
						<?php else : ?>
							<p class="sp-contact__value" <?php nwcs_edit_attr( 'contact', 'info', $row['value'] ); ?>><?php echo nl2br( esc_html( $value ) ); ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="sp-form" id="form">
			<h2 class="sp-form__title" <?php nwcs_edit_attr( 'contact', 'form', 'title' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'form', 'title' ) ); ?></h2>

			<?php if ( $state['success'] ) : ?>
				<div class="sp-form__notice sp-form__notice--ok" role="status">
					<?php sanayi_palet_icon( 'check', 24 ); ?>
					<p <?php nwcs_edit_attr( 'contact', 'form', 'success' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'form', 'success' ) ); ?></p>
				</div>
			<?php else : ?>
				<p class="sp-form__text" <?php nwcs_edit_attr( 'contact', 'form', 'text' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'form', 'text' ) ); ?></p>

				<?php if ( $errors ) : ?>
					<div class="sp-form__notice sp-form__notice--error" role="alert">
						<p><?php echo esc_html( $errors['form'] ?? 'Mesaj gönderilmedi. İşaretli alanları düzeltip tekrar gönderin.' ); ?></p>
					</div>
				<?php endif; ?>

				<form class="sp-form__grid" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
					<input type="hidden" name="action" value="sanayi_palet_message" />
					<?php wp_nonce_field( 'sanayi_palet_message', 'sp_nonce' ); ?>

					<?php
					$field( 'name', 'Adınız soyadınız', 'text', true, 'name' );
					$field( 'email', 'E-posta', 'email', true, 'email' );
					$field( 'phone', 'Telefonunuz', 'tel', false, 'tel' );
					$field( 'subject', 'Konu', 'text', false, 'off' );
					$field( 'message', 'Mesajınız', 'textarea', true, 'off' );
					?>

					<?php // Bot tuzagi: ekran okuyuculardan ve klavyeden gizli. ?>
					<div class="screen-reader-text" aria-hidden="true">
						<label for="sp-website">Web siteniz</label>
						<input type="text" id="sp-website" name="sp_website" tabindex="-1" autocomplete="off" />
					</div>

					<div class="sp-form__actions">
						<button type="submit" class="btn btn--solid" <?php nwcs_edit_attr( 'contact', 'form', 'button_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'button_label' ) ); ?>
						</button>
					</div>
				</form>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( '' !== trim( $query ) ) : ?>
	<section class="sp-map" aria-label="Harita">
		<iframe
			class="sp-map__frame"
			title="<?php echo esc_attr( 'Harita: ' . $query ); ?>"
			src="<?php echo esc_url( 'https://www.google.com/maps?q=' . rawurlencode( $query ) . '&output=embed' ); ?>"
			loading="lazy"
			referrerpolicy="no-referrer-when-downgrade"></iframe>

		<div class="sp-wrap sp-map__bar">
			<a class="btn btn--solid" href="<?php echo esc_url( 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $query ) ); ?>" target="_blank" rel="noopener" <?php nwcs_edit_attr( 'contact', 'map', 'directions_label' ); ?>>
				<?php sanayi_palet_icon( 'pin', 18 ); ?>
				<?php echo esc_html( nwcs_field( 'contact', 'map', 'directions_label' ) ); ?>
				<span class="screen-reader-text">(yeni sekmede açılır)</span>
			</a>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
