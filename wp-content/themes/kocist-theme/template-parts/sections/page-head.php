<?php
/**
 * Ornek alt sayfa basligi.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="k-pagehead" data-nwcs-section="page_head">
	<div class="k-wrap">
		<p class="k-pagehead__crumb"><?php echo esc_html( nwcs_field( 'inner', 'page_head', 'breadcrumb' ) ); ?></p>
		<h1 class="k-pagehead__title"><?php echo esc_html( nwcs_field( 'inner', 'page_head', 'title' ) ); ?></h1>
		<p class="k-pagehead__sub"><?php echo esc_html( nwcs_field( 'inner', 'page_head', 'subtitle' ) ); ?></p>
	</div>
</div>
