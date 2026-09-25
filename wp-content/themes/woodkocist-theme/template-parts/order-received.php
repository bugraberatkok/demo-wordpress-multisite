<?php
/**
 * Siparis onayi (/odeme/?siparis=…&anahtar=…): numara, durum, odeme bilgisi,
 * urunler ve teslimat. Banka hesaplari Magaza Ayarlari'ndan; bossa uydurulmaz.
 * $args['order']: WP_Post.
 */

defined( 'ABSPATH' ) || exit;

$order    = $args['order'];
$id       = (int) $order->ID;
$number   = wk_order_number( $id );
$status   = (string) get_post_meta( $id, '_wko_status', true );
$items    = wk_order_items( $id );
$total    = (float) get_post_meta( $id, '_wko_total', true );
$vat      = (float) get_post_meta( $id, '_wko_vat', true );
$address  = json_decode( (string) get_post_meta( $id, '_wko_address', true ), true ) ?: array();
$fabrika  = 'fabrika' === get_post_meta( $id, '_wko_delivery', true );
$bank     = wk_bank_lines();
$is_new   = 'odeme-bekleniyor' === $status;

// Gorunen yazilar: Tum Sayfalar -> Siparis Onayi (global.order).
$text = static fn( string $name ): string => (string) nwcs_field( 'global', 'order', $name );
$attr = static function ( string $name ): void {
	nwcs_edit_attr( 'global', 'order', $name );
};

get_header();
?>

<section class="wk-pagehead wk-pagehead--compact">
	<div class="wk-wrap">
		<?php wk_part( 'checkout-steps', array( 'step' => 3 ) ); ?>
		<h1 class="wk-hero__title" <?php $attr( $is_new ? 'done_title' : 'status_title' ); ?>><?php echo esc_html( $text( $is_new ? 'done_title' : 'status_title' ) ); ?></h1>
	</div>
</section>

<div class="wk-wrap wk-section wk-received" data-order-done>
	<div class="wk-received__main">
		<div class="wk-received__head">
			<span class="wk-received__ok" aria-hidden="true"><?php echo wk_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapingOutput ?></span>
			<div>
				<p class="wk-received__label" <?php $attr( 'number_label' ); ?>><?php echo esc_html( $text( 'number_label' ) ); ?></p>
				<p class="wk-received__number wk-num"><?php echo esc_html( $number ); ?></p>
				<p class="wk-received__status" <?php $attr( 'status_label' ); ?>><?php echo esc_html( $text( 'status_label' ) ); ?> <strong><?php echo esc_html( WK_ORDER_STATUSES[ $status ] ?? '—' ); ?></strong></p>
			</div>
		</div>

		<?php if ( $is_new ) : ?>
			<section class="wk-panel" aria-labelledby="wk-pay-title">
				<h2 id="wk-pay-title" class="wk-panel__title" <?php $attr( 'pay_title' ); ?>><?php echo esc_html( $text( 'pay_title' ) ); ?></h2>
				<?php $account = $bank ? 'account_below' : 'account_later'; ?>
				<ol class="wk-paysteps">
					<li <?php $attr( 'pay_step1' ); ?>><?php echo wk_text_html( 'global', 'order', 'pay_step1', array( 'tutar' => '<strong class="wk-num">' . esc_html( wk_money( $total ) ) . '</strong>', 'hesap' => '<span' . wk_attr_string( 'global', 'order', $account ) . '>' . esc_html( $text( $account ) ) . '</span>' ) ); // phpcs:ignore WordPress.Security.EscapingOutput -- metin kacirildi. ?></li>
					<li <?php $attr( 'pay_step2' ); ?>><?php echo wk_text_html( 'global', 'order', 'pay_step2', array( 'numara' => '<strong class="wk-num">' . esc_html( $number ) . '</strong>' ) ); // phpcs:ignore WordPress.Security.EscapingOutput -- metin kacirildi. ?></li>
					<li <?php $attr( 'pay_step3' ); ?>><?php echo esc_html( $text( 'pay_step3' ) ); ?></li>
				</ol>
				<?php if ( $bank ) : ?>
					<div class="wk-bank" <?php nwcs_edit_attr( 'global', 'shop', 'bank_accounts' ); ?>>
						<?php foreach ( $bank as $line ) : ?>
							<p class="wk-num"><?php echo esc_html( $line ); ?></p>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="wk-notice wk-notice--info" <?php $attr( 'bank_missing' ); ?>><?php echo esc_html( $text( 'bank_missing' ) ); ?></p>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<section class="wk-panel" aria-labelledby="wk-items-title">
			<h2 id="wk-items-title" class="wk-panel__title" <?php $attr( 'items_title' ); ?>><?php echo esc_html( $text( 'items_title' ) ); ?></h2>
			<ul class="wk-mini">
				<?php foreach ( $items as $item ) : ?>
					<li>
						<span class="wk-mini__qty wk-num"><?php echo (int) $item['qty']; ?>×</span>
						<span class="wk-mini__name"><span class="wk-cart__code"><?php echo esc_html( $item['code'] ); ?></span><?php echo esc_html( $item['title'] ); ?></span>
						<span class="wk-num"><?php echo esc_html( wk_money( (float) $item['total'] ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<dl class="wk-totals">
				<?php if ( $vat > 0 ) : ?>
					<div><dt <?php nwcs_edit_attr( 'global', 'cart', 'vat' ); ?>><?php echo esc_html( nwcs_field( 'global', 'cart', 'vat' ) ); ?></dt><dd class="wk-num"><?php echo esc_html( wk_money( $vat ) ); ?></dd></div>
				<?php endif; ?>
				<div><dt <?php nwcs_edit_attr( 'global', 'cart', 'shipping' ); ?>><?php echo esc_html( nwcs_field( 'global', 'cart', 'shipping' ) ); ?></dt><dd <?php $fabrika ? $attr( 'pickup_row' ) : nwcs_edit_attr( 'global', 'shop', 'shipping_label' ); ?>><?php echo esc_html( $fabrika ? $text( 'pickup_row' ) : nwcs_field( 'global', 'shop', 'shipping_label' ) ); ?></dd></div>
				<div class="wk-totals__grand"><dt <?php nwcs_edit_attr( 'global', 'cart', 'total' ); ?>><?php echo esc_html( nwcs_field( 'global', 'cart', 'total' ) ); ?></dt><dd class="wk-num"><?php echo esc_html( wk_money( $total ) ); ?></dd></div>
			</dl>
		</section>
	</div>

	<aside class="wk-received__side">
		<section class="wk-panel" aria-labelledby="wk-ship-title">
			<h2 id="wk-ship-title" class="wk-panel__title" <?php $attr( 'ship_title' ); ?>><?php echo esc_html( $text( 'ship_title' ) ); ?></h2>
			<?php if ( $fabrika ) : ?>
				<p <?php nwcs_edit_attr( 'global', 'shop', 'pickup_address' ); ?>><span <?php $attr( 'pickup_prefix' ); ?>><?php echo esc_html( $text( 'pickup_prefix' ) ); ?></span> <?php echo esc_html( (string) nwcs_field( 'global', 'shop', 'pickup_address' ) ); ?></p>
			<?php else : ?>
				<p><?php echo esc_html( (string) ( $address['address'] ?? '' ) ); ?><br /><?php echo esc_html( trim( ( $address['district'] ?? '' ) . ' / ' . ( $address['city'] ?? '' ), ' /' ) ); ?></p>
			<?php endif; ?>
			<p class="wk-form__note" <?php nwcs_edit_attr( 'global', 'shop', 'lead_time' ); ?>><?php echo esc_html( nwcs_field( 'global', 'shop', 'lead_time' ) ); ?></p>
		</section>
		<section class="wk-panel" aria-labelledby="wk-help-title">
			<h2 id="wk-help-title" class="wk-panel__title" <?php $attr( 'help_title' ); ?>><?php echo esc_html( $text( 'help_title' ) ); ?></h2>
			<p <?php $attr( 'help_desc' ); ?>><?php echo esc_html( $text( 'help_desc' ) ); ?></p>
			<?php if ( wk_whatsapp() ) : ?>
				<a class="wk-btn wk-btn--line" href="<?php echo esc_url( wk_whatsapp( sprintf( 'Merhaba, %s numaralı siparişim hakkında yazıyorum.', $number ) ) ); ?>" target="_blank" rel="noopener" <?php $attr( 'help_button' ); ?>><?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> <?php echo esc_html( $text( 'help_button' ) ); ?></a>
			<?php endif; ?>
		</section>
		<p><a href="<?php echo esc_url( wk_shop_url() ); ?>" <?php nwcs_edit_attr( 'global', 'cart', 'continue' ); ?>><?php echo esc_html( nwcs_field( 'global', 'cart', 'continue' ) ); ?></a></p>
	</aside>
</div>

<?php
get_footer();
