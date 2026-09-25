<?php
/**
 * Sepet (/sepet/). Satirlar sunucuda Urun Havuzu fiyatiyla hesaplanir
 * (inc/cart.php). Adet degisince site.js formu kendisi gonderir; JavaScript
 * yoksa "Sepeti guncelle" dugmesi.
 */

defined( 'ABSPATH' ) || exit;

$cart   = wk_cart();
$totals = wk_cart_totals( $cart );
$text   = static fn( string $name ): string => (string) nwcs_field( 'global', 'cart', $name );
$attr   = static function ( string $name ): void {
	nwcs_edit_attr( 'global', 'cart', $name );
};

get_header();
?>

<section class="wk-pagehead wk-pagehead--compact">
	<div class="wk-wrap">
		<?php wk_part( 'checkout-steps', array( 'step' => 1 ) ); ?>
		<h1 class="wk-hero__title" <?php $attr( 'heading' ); ?>><?php echo esc_html( $text( 'heading' ) ); ?></h1>
	</div>
</section>

<div class="wk-wrap wk-section">
	<?php if ( $cart['removed'] ) : ?>
		<div class="wk-notice wk-notice--err" role="status">
			<p <?php $attr( 'removed' ); ?>><?php echo esc_html( wk_text( 'global', 'cart', 'removed', array( 'urunler' => implode( ', ', $cart['removed'] ) ) ) ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( ! $cart['lines'] ) : ?>
		<div class="wk-emptycart">
			<?php echo wk_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			<h2 <?php $attr( 'empty_title' ); ?>><?php echo esc_html( $text( 'empty_title' ) ); ?></h2>
			<p <?php $attr( 'empty_desc' ); ?>><?php echo esc_html( $text( 'empty_desc' ) ); ?></p>
			<a class="wk-btn wk-btn--primary wk-btn--lg" href="<?php echo esc_url( wk_shop_url() ); ?>" <?php $attr( 'empty_button' ); ?>><?php echo esc_html( $text( 'empty_button' ) ); ?></a>
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
							<th scope="col" <?php $attr( 'col_product' ); ?>><?php echo esc_html( $text( 'col_product' ) ); ?></th>
							<th scope="col" <?php $attr( 'col_unit' ); ?>><?php echo esc_html( $text( 'col_unit' ) ); ?></th>
							<th scope="col" <?php $attr( 'col_qty' ); ?>><?php echo esc_html( $text( 'col_qty' ) ); ?></th>
							<th scope="col" <?php $attr( 'col_total' ); ?>><?php echo esc_html( $text( 'col_total' ) ); ?></th>
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
										<span class="wk-cart__code" <?php wk_product_src( $product, 'Ürün kodu' ); ?>><?php echo esc_html( $product['code'] ); ?></span>
										<a class="wk-cart__name" href="<?php echo esc_url( $product['url'] ); ?>" <?php wk_product_src( $product, 'Ürün adı' ); ?>><?php echo esc_html( $product['title'] ); ?></a>
									</span>
								</td>
								<td data-label="<?php echo esc_attr( $text( 'col_unit' ) ); ?>" class="wk-num"><?php echo esc_html( wk_money( $line['unit'] ) ); ?></td>
								<td data-label="<?php echo esc_attr( $text( 'col_qty' ) ); ?>">
									<div class="wk-qty" data-qty>
										<button type="button" class="wk-qty__btn" data-step="-1" aria-label="<?php echo esc_attr( $product['code'] ); ?> adedini azalt">−</button>
										<label class="wk-sr" for="wk-line-<?php echo (int) $id; ?>"><?php echo esc_html( $product['code'] ); ?> adedi</label>
										<input id="wk-line-<?php echo (int) $id; ?>" class="wk-qty__input" type="number" name="wk_qty[<?php echo (int) $id; ?>]" value="<?php echo (int) $line['qty']; ?>" min="0" max="<?php echo (int) WK_QTY_MAX; ?>" inputmode="numeric" />
										<button type="button" class="wk-qty__btn" data-step="1" aria-label="<?php echo esc_attr( $product['code'] ); ?> adedini artır">+</button>
									</div>
								</td>
								<td data-label="<?php echo esc_attr( $text( 'col_total' ) ); ?>" class="wk-num wk-cart__total"><?php echo esc_html( wk_money( $line['total'] ) ); ?></td>
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
					<a href="<?php echo esc_url( wk_shop_url() ); ?>" <?php $attr( 'continue' ); ?>><?php echo esc_html( $text( 'continue' ) ); ?></a>
					<button type="submit" class="wk-btn wk-btn--line" data-cart-update <?php $attr( 'update' ); ?>><?php echo esc_html( $text( 'update' ) ); ?></button>
				</div>
			</form>

			<aside class="wk-summary" aria-labelledby="wk-summary-title">
				<h2 id="wk-summary-title" class="wk-summary__title" <?php $attr( 'summary_title' ); ?>><?php echo esc_html( $text( 'summary_title' ) ); ?></h2>
				<?php wk_part( 'order-totals', array( 'totals' => $totals, 'count' => $cart['count'] ) ); ?>
				<a class="wk-btn wk-btn--primary wk-btn--lg wk-btn--block" href="<?php echo esc_url( home_url( '/odeme/' ) ); ?>" <?php $attr( 'checkout_button' ); ?>><?php echo esc_html( $text( 'checkout_button' ) ); ?></a>
				<ul class="wk-assure">
					<li <?php $attr( 'assure' ); ?>><?php echo wk_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> <?php echo esc_html( $text( 'assure' ) ); ?></li>
					<li <?php nwcs_edit_attr( 'global', 'shop', 'shipping_note' ); ?>><?php echo wk_icon( 'truck' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> <?php echo esc_html( nwcs_field( 'global', 'shop', 'shipping_note' ) ); ?></li>
				</ul>
			</aside>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
