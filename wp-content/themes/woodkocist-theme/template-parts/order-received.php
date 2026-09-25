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

get_header();
?>

<section class="wk-pagehead wk-pagehead--compact">
	<div class="wk-wrap">
		<?php wk_part( 'checkout-steps', array( 'step' => 3 ) ); ?>
		<h1 class="wk-hero__title"><?php echo $is_new ? 'Siparişiniz alındı' : 'Sipariş durumu'; ?></h1>
	</div>
</section>

<div class="wk-wrap wk-section wk-received" data-order-done>
	<div class="wk-received__main">
		<div class="wk-received__head">
			<span class="wk-received__ok" aria-hidden="true"><?php echo wk_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapingOutput ?></span>
			<div>
				<p class="wk-received__label">Sipariş numaranız</p>
				<p class="wk-received__number wk-num"><?php echo esc_html( $number ); ?></p>
				<p class="wk-received__status">Durum: <strong><?php echo esc_html( WK_ORDER_STATUSES[ $status ] ?? '—' ); ?></strong></p>
			</div>
		</div>

		<?php if ( $is_new ) : ?>
			<section class="wk-panel" aria-labelledby="wk-pay-title">
				<h2 id="wk-pay-title" class="wk-panel__title">Ödeme: Banka Havalesi / EFT</h2>
				<ol class="wk-paysteps">
					<li>Toplam <strong class="wk-num"><?php echo esc_html( wk_money( $total ) ); ?></strong> tutarını <?php echo $bank ? 'aşağıdaki hesaba' : 'size ileteceğimiz banka hesabına'; ?> gönderin.</li>
					<li>Açıklama kısmına sipariş numaranızı yazın: <strong class="wk-num"><?php echo esc_html( $number ); ?></strong></li>
					<li>Ödemeniz onaylanınca siparişiniz üretim programına alınır; teslimat günü için sizi ararız.</li>
				</ol>
				<?php if ( $bank ) : ?>
					<div class="wk-bank">
						<?php foreach ( $bank as $line ) : ?>
							<p class="wk-num"><?php echo esc_html( $line ); ?></p>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="wk-notice wk-notice--info">Banka hesap bilgilerini sipariş numaranızla birlikte telefon ya da e-postayla size ileteceğiz. Beklemek istemezseniz WhatsApp’tan sipariş numaranızı yazın.</p>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<section class="wk-panel" aria-labelledby="wk-items-title">
			<h2 id="wk-items-title" class="wk-panel__title">Ürünler</h2>
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
					<div><dt>KDV</dt><dd class="wk-num"><?php echo esc_html( wk_money( $vat ) ); ?></dd></div>
				<?php endif; ?>
				<div><dt>Kargo</dt><dd><?php echo esc_html( $fabrika ? 'Fabrikadan teslim' : nwcs_field( 'global', 'shop', 'shipping_label' ) ); ?></dd></div>
				<div class="wk-totals__grand"><dt>Toplam</dt><dd class="wk-num"><?php echo esc_html( wk_money( $total ) ); ?></dd></div>
			</dl>
		</section>
	</div>

	<aside class="wk-received__side">
		<section class="wk-panel" aria-labelledby="wk-ship-title">
			<h2 id="wk-ship-title" class="wk-panel__title">Teslimat</h2>
			<?php if ( $fabrika ) : ?>
				<p>Fabrikadan teslim: <?php echo esc_html( (string) nwcs_field( 'global', 'shop', 'pickup_address' ) ); ?></p>
			<?php else : ?>
				<p><?php echo esc_html( (string) ( $address['address'] ?? '' ) ); ?><br /><?php echo esc_html( trim( ( $address['district'] ?? '' ) . ' / ' . ( $address['city'] ?? '' ), ' /' ) ); ?></p>
			<?php endif; ?>
			<p class="wk-form__note"><?php echo esc_html( nwcs_field( 'global', 'shop', 'lead_time' ) ); ?></p>
		</section>
		<section class="wk-panel" aria-labelledby="wk-help-title">
			<h2 id="wk-help-title" class="wk-panel__title">Bir sorun mu var?</h2>
			<p>Adres ya da fatura bilgisini değiştirmek için sipariş numaranızla bize yazın.</p>
			<?php if ( wk_whatsapp() ) : ?>
				<a class="wk-btn wk-btn--line" href="<?php echo esc_url( wk_whatsapp( sprintf( 'Merhaba, %s numaralı siparişim hakkında yazıyorum.', $number ) ) ); ?>" target="_blank" rel="noopener"><?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> WhatsApp’tan yazın</a>
			<?php endif; ?>
		</section>
		<p><a href="<?php echo esc_url( wk_shop_url() ); ?>">Alışverişe devam et</a></p>
	</aside>
</div>

<?php
get_footer();
