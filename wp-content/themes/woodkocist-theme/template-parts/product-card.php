<?php
/**
 * Urun karti. Gorsel yoksa urun kodu buyuk bir plaka olarak gorunur
 * (ahsaba yakilmis damga gibi); havuza gorsel eklenince fotograf gelir.
 * data-* nitelikleri seri/kategori suzgeci icin (assets/site.js).
 *
 * $args['product']: wk_products() ogesi.
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product ) {
	return;
}

$image = wk_image( $product );
$specs = wk_card_specs( $product );
?>
<article class="wk-card"
	data-cats="<?php echo esc_attr( implode( ' ', array_keys( $product['categories'] ) ) ); ?>">
	<a href="<?php echo esc_url( $product['url'] ); ?>" class="wk-card__link">
		<span class="wk-card__media">
			<?php if ( $image['url'] ) : ?>
				<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?: $product['title'] ); ?>" loading="lazy" decoding="async" />
			<?php else : ?>
				<span class="wk-plate" aria-hidden="true"><?php echo esc_html( $product['code'] ); ?></span>
			<?php endif; ?>
		</span>
		<span class="wk-card__body">
			<span class="wk-card__code"><?php echo esc_html( $product['code'] ); ?></span>
			<span class="wk-card__title"><?php echo esc_html( $product['title'] ); ?></span>
			<?php if ( $specs ) : ?>
				<span class="wk-card__specs">
					<?php foreach ( $specs as $pair ) : ?>
						<span><?php echo esc_html( $pair[1] ); ?></span>
					<?php endforeach; ?>
				</span>
			<?php endif; ?>
			<span class="wk-price<?php echo $product['has_price'] ? '' : ' wk-price--ask'; ?>">
				<?php echo esc_html( $product['has_price'] ? $product['price'] : 'Fiyat için sorun' ); ?>
			</span>
		</span>
	</a>
</article>
