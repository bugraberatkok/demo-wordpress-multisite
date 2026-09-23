<?php
/**
 * Urun istifi: kesim yuzu fotograflari araliksiz dizilir, her birinin
 * ustunde depo etiketi gibi zimbalanmis bir urun plakasi durur.
 *
 * Beklenen $args: products (ik_products() satirlari), detailed (bool) —
 * /urunlerimiz/ sayfasinda kartin altinda kisa aciklama da basilir.
 * Kartin tamami baslik baglantisiyla tiklanir (::after karti kaplar).
 */

defined( 'ABSPATH' ) || exit;

$products = $args['products'] ?? array();
$detailed = ! empty( $args['detailed'] );

if ( ! $products ) {
	return;
}
?>
<ul class="ik-stack<?php echo $detailed ? ' ik-stack--detailed' : ''; ?>">
	<?php foreach ( $products as $product ) : ?>
		<li class="ik-log">
			<div class="ik-log__face">
				<?php echo ik_image_tag( ik_product_image( $product ), 'ik-log__image', '' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</div>
			<h3 class="ik-log__tag">
				<a class="ik-log__link" href="<?php echo esc_url( $product['url'] ); ?>" <?php nwcs_edit_attr( 'products', 'catalog', 'items', $product['index'], 'title' ); ?>><?php echo esc_html( $product['title'] ); ?></a>
			</h3>
			<?php if ( $detailed && '' !== trim( $product['short'] ) ) : ?>
				<p class="ik-log__short" <?php nwcs_edit_attr( 'products', 'catalog', 'items', $product['index'], 'short' ); ?>><?php echo esc_html( $product['short'] ); ?></p>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
