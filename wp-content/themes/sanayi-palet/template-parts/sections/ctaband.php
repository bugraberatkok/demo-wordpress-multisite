<?php
/**
 * Teklif seridi: cam zemin, iki eylem.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="sp-cta" data-nwcs-section="ctaband">
	<div class="sp-wrap sp-cta__inner">
		<div>
			<h2 class="sp-cta__title" <?php nwcs_edit_attr( 'home', 'ctaband', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'title' ) ); ?></h2>
			<p class="sp-cta__text" <?php nwcs_edit_attr( 'home', 'ctaband', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'text' ) ); ?></p>
		</div>
		<div class="sp-cta__actions">
			<a class="btn btn--solid" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'home', 'ctaband', 'primary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'ctaband', 'primary_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'primary_label' ) ); ?>
			</a>
			<a class="btn btn--outline" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'home', 'ctaband', 'secondary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'ctaband', 'secondary_label' ); ?>>
				<?php sanayi_palet_icon( 'whatsapp', 18 ); ?>
				<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'secondary_label' ) ); ?>
			</a>
		</div>
	</div>
</section>
