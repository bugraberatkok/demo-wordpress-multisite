<?php
/**
 * Ornek alt sayfa basligi.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="p-pagehead" data-nwcs-section="page_head">
	<div class="p-wrap">
		<span class="p-badge" <?php nwcs_edit_attr( 'inner', 'page_head', 'badge' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'page_head', 'badge' ) ); ?></span>
		<h1 class="p-pagehead__title" <?php nwcs_edit_attr( 'inner', 'page_head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'page_head', 'title' ) ); ?></h1>
		<p class="p-pagehead__sub" <?php nwcs_edit_attr( 'inner', 'page_head', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'page_head', 'subtitle' ) ); ?></p>
	</div>
</div>
