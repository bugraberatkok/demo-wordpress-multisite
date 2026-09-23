<?php
/**
 * Fiyat seridi: telefon numarasi buyuk basilir, yaninda WhatsApp.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="ik-cta" aria-labelledby="ik-cta-title">
	<div class="ik-wrap ik-cta__inner">
		<div class="ik-cta__text">
			<h2 class="ik-cta__title" id="ik-cta-title" <?php nwcs_edit_attr( 'home', 'ctaband', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'title' ) ); ?></h2>
			<p <?php nwcs_edit_attr( 'home', 'ctaband', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'text' ) ); ?></p>
		</div>
		<div class="ik-cta__actions">
			<a class="ik-cta__phone" href="<?php echo esc_url( ik_link( nwcs_field( 'home', 'ctaband', 'primary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'ctaband', 'primary_label' ); ?>>
				<?php ik_icon( 'phone', 28 ); ?>
				<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'primary_label' ) ); ?>
			</a>
			<a class="btn btn--ghost" href="<?php echo esc_url( ik_link( nwcs_field( 'home', 'ctaband', 'secondary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'ctaband', 'secondary_label' ); ?>>
				<?php ik_icon( 'whatsapp', 20 ); ?>
				<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'secondary_label' ) ); ?>
			</a>
		</div>
	</div>
</section>
