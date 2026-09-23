<?php
/**
 * Alt sayfa basligi (Kurumsal, Insan Kaynaklari, Blog).
 *
 * Hangi sayfanin 'page_head' bileseninin okunacagi $args['page'] ile gelir;
 * verilmezse Kurumsal ('inner').
 */

defined( 'ABSPATH' ) || exit;

$head_page = (string) ( $args['page'] ?? 'inner' );
$subtitle  = nwcs_field( $head_page, 'page_head', 'subtitle' );
?>
<div class="k-pagehead" data-nwcs-section="page_head">
	<div class="k-wrap">
		<p class="k-pagehead__crumb" <?php nwcs_edit_attr( $head_page, 'page_head', 'breadcrumb' ); ?>><?php echo esc_html( nwcs_field( $head_page, 'page_head', 'breadcrumb' ) ); ?></p>
		<h1 class="k-pagehead__title" <?php nwcs_edit_attr( $head_page, 'page_head', 'title' ); ?>><?php echo esc_html( nwcs_field( $head_page, 'page_head', 'title' ) ); ?></h1>
		<?php if ( '' !== trim( (string) $subtitle ) ) : ?>
			<p class="k-pagehead__sub" <?php nwcs_edit_attr( $head_page, 'page_head', 'subtitle' ); ?>><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
	</div>
</div>
