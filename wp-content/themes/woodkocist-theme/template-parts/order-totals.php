<?php
/**
 * Tutar dokumu (sepet, odeme, onay). $args['totals']: wk_cart_totals().
 */

defined( 'ABSPATH' ) || exit;

$totals = $args['totals'];
$count  = (int) ( $args['count'] ?? 0 );
?>
<dl class="wk-totals">
	<div>
		<dt <?php nwcs_edit_attr( 'global', 'cart', 'subtotal' ); ?>><?php echo esc_html( nwcs_field( 'global', 'cart', 'subtotal' ) ); ?><?php if ( $count ) : ?> <span <?php nwcs_edit_attr( 'global', 'cart', 'subtotal_count' ); ?>><?php echo esc_html( wk_text( 'global', 'cart', 'subtotal_count', array( 'sayi' => $count ) ) ); ?></span><?php endif; ?></dt>
		<dd class="wk-num"><?php echo esc_html( wk_money( $totals['subtotal'] ) ); ?></dd>
	</div>
	<?php if ( ! $totals['vat_included'] ) : ?>
		<div>
			<dt <?php nwcs_edit_attr( 'global', 'cart', 'vat' ); ?>><?php echo esc_html( nwcs_field( 'global', 'cart', 'vat' ) ); ?> (%<?php echo esc_html( rtrim( rtrim( number_format( $totals['vat_rate'], 2, ',', '' ), '0' ), ',' ) ); ?>)</dt>
			<dd class="wk-num"><?php echo esc_html( wk_money( $totals['vat'] ) ); ?></dd>
		</div>
	<?php endif; ?>
	<div>
		<dt <?php nwcs_edit_attr( 'global', 'cart', 'shipping' ); ?>><?php echo esc_html( nwcs_field( 'global', 'cart', 'shipping' ) ); ?></dt>
		<dd <?php nwcs_edit_attr( 'global', 'shop', 'shipping_label' ); ?>><?php echo esc_html( nwcs_field( 'global', 'shop', 'shipping_label' ) ); ?></dd>
	</div>
	<div class="wk-totals__grand">
		<dt <?php nwcs_edit_attr( 'global', 'cart', 'total' ); ?>><?php echo esc_html( nwcs_field( 'global', 'cart', 'total' ) ); ?><?php if ( $totals['vat_included'] ) : ?> <small <?php nwcs_edit_attr( 'global', 'cart', 'total_vat' ); ?>><?php echo esc_html( nwcs_field( 'global', 'cart', 'total_vat' ) ); ?></small><?php endif; ?></dt>
		<dd class="wk-num"><?php echo esc_html( wk_money( $totals['total'] ) ); ?></dd>
	</div>
</dl>
