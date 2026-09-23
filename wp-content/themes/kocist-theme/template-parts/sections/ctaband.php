<?php
/**
 * Teklif seridi.
 *
 * Demo kurulumda form gonderimi yoktur; kullanici gercek telefon/e-posta
 * baglantisina yonlendirilir.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="k-cta" id="teklif" data-nwcs-section="ctaband">
	<div class="k-wrap k-cta__inner">
		<div>
			<h2 class="k-cta__title" <?php nwcs_edit_attr( 'home', 'ctaband', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'title' ) ); ?></h2>
			<p class="k-cta__text" <?php nwcs_edit_attr( 'home', 'ctaband', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'text' ) ); ?></p>
		</div>
		<a class="k-btn k-btn--primary" href="<?php echo esc_url( kocist_link( nwcs_field( 'home', 'ctaband', 'button_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'ctaband', 'button_label' ); ?>>
			<?php nwcs_the_icon( 'phone', 'k-icon', 18 ); ?>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'button_label' ) ); ?>
		</a>
		<p class="k-cta__note" <?php nwcs_edit_attr( 'home', 'ctaband', 'note' ); ?>><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'note' ) ); ?></p>
	</div>
</section>
