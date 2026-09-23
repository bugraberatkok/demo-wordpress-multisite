<?php
/**
 * Kurumsal: Amacimiz. Sayfanin tek vurgulu ani: koyu yesil kartta buyuk
 * dizilmis tek cumle.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="k-section k-section--tight" data-nwcs-section="aim">
	<div class="k-wrap">
		<div class="k-aim">
			<h2 class="k-aim__label" <?php nwcs_edit_attr( 'inner', 'aim', 'title' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'aim', 'title' ) ); ?></h2>
			<p class="k-aim__statement" <?php nwcs_edit_attr( 'inner', 'aim', 'statement' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'aim', 'statement' ) ); ?></p>
			<p class="k-aim__text" <?php nwcs_edit_attr( 'inner', 'aim', 'body' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'aim', 'body' ) ); ?></p>
		</div>
	</div>
</section>
