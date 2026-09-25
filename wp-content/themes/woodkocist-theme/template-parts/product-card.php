<?php
/**
 * Urun karti. Gorsel yoksa urun kodu buyuk bir plaka olarak gorunur
 * (ahsaba yakilmis damga gibi); havuza gorsel eklenince fotograf gelir.
 * Fiyati olan urunde "Sepete ekle", olmayanda "Fiyat sor" (urun sayfasi).
 *
 * $args['product']: wk_products() ogesi. $args['heading']: baslik duzeyi (h2/h3).
 * $args['edit']: panel onizlemesinde karta tiklayinca acilacak alan
 * (sayfa, bilesen, alan); orn. kategori sayfasinda o kategorinin listesi.
 * Ad ve fiyat siteye ozel degistirilebildigi icin karta (listeye) baglidir;
 * kod, gorsel ve ozellikler havuzdaki urunu acar (wk_product_src).
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product ) {
	return;
}

$heading = in_array( $args['heading'] ?? 'h3', array( 'h2', 'h3' ), true ) ? $args['heading'] : 'h3';
$image   = wk_image( $product );
$specs   = wk_card_specs( $product );
$buy     = wk_can_buy( $product );
?>
<article class="wk-card" <?php ! empty( $args['edit'] ) && nwcs_edit_attr( ...$args['edit'] ); ?>>
	<a href="<?php echo esc_url( $product['url'] ); ?>" class="wk-card__media" tabindex="-1" aria-hidden="true" <?php wk_product_src( $product, 'Görsel' ); ?>>
		<?php if ( $image['url'] ) : ?>
			<img src="<?php echo esc_url( $image['url'] ); ?>" alt="" loading="lazy" decoding="async" />
		<?php else : ?>
			<span class="wk-plate"><?php echo esc_html( $product['code'] ); ?></span>
		<?php endif; ?>
	</a>
	<div class="wk-card__body">
		<p class="wk-card__code" <?php wk_product_src( $product, 'Ürün kodu' ); ?>><?php echo esc_html( $product['code'] ); ?></p>
		<<?php echo $heading; // phpcs:ignore WordPress.Security.EscapingOutput ?> class="wk-card__title">
			<a href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['title'] ); ?></a>
		</<?php echo $heading; // phpcs:ignore WordPress.Security.EscapingOutput ?>>
		<?php if ( $specs ) : ?>
			<ul class="wk-card__specs" aria-label="Özellikler" <?php wk_product_src( $product, 'Özellikler' ); ?>>
				<?php foreach ( $specs as $pair ) : ?>
					<li><?php echo esc_html( $pair[1] ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="wk-card__foot">
			<?php if ( $buy ) : ?>
				<p class="wk-card__price"><span class="wk-num"><?php echo esc_html( $product['price'] ); ?></span> <small <?php wk_price_note_attr(); ?>><?php echo esc_html( wk_price_note() ); ?></small></p>
				<?php wk_add_to_cart_form( $product ); ?>
			<?php else : ?>
				<p class="wk-card__price wk-card__price--ask" <?php nwcs_edit_attr( 'global', 'card', 'price_ask' ); ?>><?php echo esc_html( nwcs_field( 'global', 'card', 'price_ask' ) ); ?></p>
				<a class="wk-btn wk-btn--line" href="<?php echo esc_url( $product['url'] ); ?>" <?php nwcs_edit_attr( 'global', 'card', 'ask_button' ); ?>><?php echo esc_html( nwcs_field( 'global', 'card', 'ask_button' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</article>
