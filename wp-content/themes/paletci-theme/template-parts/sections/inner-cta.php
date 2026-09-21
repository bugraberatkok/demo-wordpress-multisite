<?php
/**
 * Alt sayfa sonu cagrisi.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="p-section" data-nwcs-section="cta">
	<div class="p-wrap">
		<div class="p-cta-card">
			<div>
				<h2 <?php nwcs_edit_attr( 'inner', 'cta', 'title' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'cta', 'title' ) ); ?></h2>
				<p <?php nwcs_edit_attr( 'inner', 'cta', 'text' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'cta', 'text' ) ); ?></p>
			</div>
			<a class="p-btn p-btn--light" href="<?php echo esc_url( paletci_link( nwcs_field( 'inner', 'cta', 'button_url' ) ) ); ?>" <?php nwcs_edit_attr( 'inner', 'cta', 'button_label' ); ?>>
				<?php nwcs_the_icon( 'whatsapp', 'p-icon', 18 ); ?>
				<?php echo esc_html( nwcs_field( 'inner', 'cta', 'button_label' ) ); ?>
			</a>
		</div>
	</div>
</section>
