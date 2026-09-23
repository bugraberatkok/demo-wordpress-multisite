<?php
/**
 * Insan Kaynaklari: basvuru cagrisi. E-posta adresi buton disinda da
 * yazili; e-posta istemcisi acilmayan ziyaretci adresi kopyalayabilsin.
 */

defined( 'ABSPATH' ) || exit;

$email = trim( (string) nwcs_field( 'hr', 'apply', 'email' ) );
?>
<section class="k-section k-section--tight" data-nwcs-section="apply">
	<div class="k-wrap">
		<div class="k-apply">
			<div class="k-apply__text">
				<h2 class="k-apply__title" <?php nwcs_edit_attr( 'hr', 'apply', 'title' ); ?>><?php echo esc_html( nwcs_field( 'hr', 'apply', 'title' ) ); ?></h2>
				<p class="k-apply__body" <?php nwcs_edit_attr( 'hr', 'apply', 'text' ); ?>><?php echo esc_html( nwcs_field( 'hr', 'apply', 'text' ) ); ?></p>
			</div>

			<div class="k-apply__actions">
				<a class="k-btn k-btn--primary" href="<?php echo esc_url( kocist_link( nwcs_field( 'hr', 'apply', 'button_url' ) ) ); ?>" <?php nwcs_edit_attr( 'hr', 'apply', 'button_label' ); ?>>
					<?php nwcs_the_icon( 'mail', 'k-icon', 18 ); ?>
					<?php echo esc_html( nwcs_field( 'hr', 'apply', 'button_label' ) ); ?>
				</a>
				<?php if ( '' !== $email ) : ?>
					<a class="k-apply__email" href="<?php echo esc_url( kocist_link( 'mailto:' . $email ) ); ?>" <?php nwcs_edit_attr( 'hr', 'apply', 'email' ); ?>><?php echo esc_html( $email ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
