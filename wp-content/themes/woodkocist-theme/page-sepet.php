<?php
/**
 * Sepet (/sepet/). Satirlar sunucuda Urun Havuzu fiyatiyla hesaplanir
 * (inc/cart.php). Adet degisince site.js formu kendisi gonderir; JavaScript
 * yoksa "Sepeti guncelle" dugmesi.
 */

defined( 'ABSPATH' ) || exit;

$cart   = wk_cart();
$totals = wk_cart_totals( $cart );

get_header();
?>

<section class="wk-pagehead wk-pagehead--compact">
	<div class="wk-wrap">
		<?php wk_part( 'checkout-steps', array( 'step' => 1 ) ); ?>
		<h1 class="wk-hero__title">Sepetim</h1>
	</div>
</section>

<div class="wk-wrap wk-section">
	<?php if ( $cart['removed'] ) : ?>
		<div class="wk-notice wk-notice--err" role="status">
			<p>Şu ürünler artık satışta olmadığı ya da fiyatı değiştiği için sepetinizden çıkarıldı: <?php echo esc_html( implode( ', ', $cart['removed'] ) ); ?>.</p>
		</div>
	<?php endif; ?>

	<?php if ( ! $cart['lines'] ) : ?>
		<div class="wk-emptycart">
			<?php echo wk_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			<h2>Sepetiniz boş</h2>
			<p>Beğendiğiniz ürünün sayfasında “Sepete ekle”ye basın; ürün burada görünür.</p>
			<a class="wk-btn wk-btn--primary wk-btn--lg" href="<?php echo esc_url( wk_shop_url() ); ?>">Ürünlere göz atın</a>
		</div>
	<?php else : ?>
		<div class="wk-cart">
			<form class="wk-cart__lines" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-cart-form>
				<input type="hidden" name="action" value="wk_cart" />
				<input type="hidden" name="wk_do" value="update" />
				<table class="wk-cart__table">
					<caption class="wk-sr">Sepetteki ürünler</caption>
					<thead>
						<tr>
							<th scope="col">Ürün</th>
							<th scope="col">Birim fiyat</th>
							<th scope="col">Adet</th>
							<th scope="col">Tutar</th>
							<th scope="col"><span class="wk-sr">Kaldır</span></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $cart['lines'] as $line ) : ?>
							<?php
							$product = $line['product'];
							$id      = (int) $product['id'];
							$image   = wk_image( $product );
							?>
							<tr>
								<td class="wk-cart__product">
									<a class="wk-cart__thumb" href="<?php echo esc_url( $product['url'] ); ?>" tabindex="-1" aria-hidden="true">
										<?php if ( $image['url'] ) : ?>
											<img src="<?php echo esc_url( $image['url'] ); ?>" alt="" loading="lazy" />
										<?php else : ?>
											<span class="wk-plate wk-plate--sm"><?php echo esc_html( $product['code'] ); ?></span>
										<?php endif; ?>
									</a>
									<span>
										<span class="wk-cart__code"><?php echo esc_html( $product['code'] ); ?></span>
										<a class="wk-cart__name" href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['title'] ); ?></a>
									</span>
								</td>
								<td data-label="Birim fiyat" class="wk-num"><?php echo esc_html( wk_money( $line['unit'] ) ); ?></td>
								<td data-label="Adet">
									<div class="wk-qty" data-qty>
										<button type="button" class="wk-qty__btn" data-step="-1" aria-label="<?php echo esc_attr( $product['code'] ); ?> adedini azalt">−</button>
										<label class="wk-sr" for="wk-line-<?php echo (int) $id; ?>"><?php echo esc_html( $product['code'] ); ?> adedi</label>
										<input id="wk-line-<?php echo (int) $id; ?>" class="wk-qty__input" type="number" name="wk_qty[<?php echo (int) $id; ?>]" value="<?php echo (int) $line['qty']; ?>" min="0" max="<?php echo (int) WK_QTY_MAX; ?>" inputmode="numeric" />
										<button type="button" class="wk-qty__btn" data-step="1" aria-label="<?php echo esc_attr( $product['code'] ); ?> adedini artır">+</button>
									</div>
								</td>
								<td data-label="Tutar" class="wk-num wk-cart__total"><?php echo esc_html( wk_money( $line['total'] ) ); ?></td>
								<td class="wk-cart__remove">
									<button type="submit" name="wk_remove" value="<?php echo (int) $id; ?>" class="wk-iconbtn wk-iconbtn--light">
										<?php echo wk_icon( 'trash' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
										<span class="wk-sr"><?php echo esc_html( $product['code'] ); ?> ürününü sepetten çıkar</span>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<div class="wk-cart__actions">
					<a href="<?php echo esc_url( wk_shop_url() ); ?>">Alışverişe devam et</a>
					<button type="submit" class="wk-btn wk-btn--line" data-cart-update>Sepeti güncelle</button>
				</div>
			</form>

			<aside class="wk-summary" aria-labelledby="wk-summary-title">
				<h2 id="wk-summary-title" class="wk-summary__title">Sipariş özeti</h2>
				<?php wk_part( 'order-totals', array( 'totals' => $totals, 'count' => $cart['count'] ) ); ?>
				<a class="wk-btn wk-btn--primary wk-btn--lg wk-btn--block" href="<?php echo esc_url( home_url( '/odeme/' ) ); ?>">Siparişi tamamla</a>
				<ul class="wk-assure">
					<li><?php echo wk_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> Üyelik gerekmez; ad, telefon ve adres yeterli.</li>
					<li><?php echo wk_icon( 'truck' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> <?php echo esc_html( nwcs_field( 'global', 'shop', 'shipping_note' ) ); ?></li>
				</ul>
			</aside>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
