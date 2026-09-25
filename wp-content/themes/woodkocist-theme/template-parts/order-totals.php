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
		<dt>Ara toplam<?php echo $count ? esc_html( sprintf( ' (%d ürün)', $count ) ) : ''; ?></dt>
		<dd class="wk-num"><?php echo esc_html( wk_money( $totals['subtotal'] ) ); ?></dd>
	</div>
	<?php if ( ! $totals['vat_included'] ) : ?>
		<div>
			<dt>KDV (%<?php echo esc_html( rtrim( rtrim( number_format( $totals['vat_rate'], 2, ',', '' ), '0' ), ',' ) ); ?>)</dt>
			<dd class="wk-num"><?php echo esc_html( wk_money( $totals['vat'] ) ); ?></dd>
		</div>
	<?php endif; ?>
	<div>
		<dt>Kargo</dt>
		<dd><?php echo esc_html( nwcs_field( 'global', 'shop', 'shipping_label' ) ); ?></dd>
	</div>
	<div class="wk-totals__grand">
		<dt>Toplam<?php echo $totals['vat_included'] ? ' <small>(KDV dahil)</small>' : ''; // phpcs:ignore WordPress.Security.EscapingOutput ?></dt>
		<dd class="wk-num"><?php echo esc_html( wk_money( $totals['total'] ) ); ?></dd>
	</div>
</dl>
