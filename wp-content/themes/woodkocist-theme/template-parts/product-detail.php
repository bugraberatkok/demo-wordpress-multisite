<?php
/**
 * Urun sayfasi (/urun/<slug>/). Eklenti (nwcs_product_template) bu dosyayi
 * $product ile cagirir: site istisnalari uygulanmis havuz urunu.
 *
 * Solda gorsel (yoksa kod plakasi) ve aciklama; sagda fiyat, kod ve siparis
 * dugmeleri, altinda teknik ozellikler tablosu; en altta ayni seriden urunler.
 */

defined( 'ABSPATH' ) || exit;

/** @var array $product */

foreach ( wk_products() as $candidate ) {
	if ( $candidate['id'] === $product['id'] ) {
		$product = $candidate;
		break;
	}
}

$image   = wk_image( $product );
$specs   = wk_specs( (string) $product['spec'] );
$wa      = wk_whatsapp( wk_order_text( $product ) );
$contact = add_query_arg( 'urun', rawurlencode( trim( $product['code'] . ' ' . $product['title'] ) ), home_url( '/iletisim/' ) ) . '#siparis';
$lines   = wp_list_pluck( wk_lines(), 'label', 'slug' );
$line    = '';

foreach ( array_keys( $product['categories'] ) as $slug ) {
	if ( isset( $lines[ $slug ] ) ) {
		$line = $slug;
		break;
	}
}

$related = array_slice(
	array_values(
		array_filter(
			wk_products(),
			static fn( array $p ): bool => $p['id'] !== $product['id'] && ( '' === $line || isset( $p['categories'][ $line ] ) )
		)
	),
	0,
	4
);

get_header();
?>

<nav class="wk-wrap wk-crumbs" aria-label="Konum">
	<ol>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Anasayfa</a></li>
		<?php if ( $line ) : ?>
			<li><a href="<?php echo esc_url( add_query_arg( 'seri', $line, home_url( '/' ) ) . '#urunler' ); ?>"><?php echo esc_html( $lines[ $line ] ); ?></a></li>
		<?php endif; ?>
		<li aria-current="page"><?php echo esc_html( $product['title'] ); ?></li>
	</ol>
</nav>

<article class="wk-wrap wk-product">
	<div class="wk-product__media">
		<?php if ( $image['url'] ) : ?>
			<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?: $product['title'] ); ?>" fetchpriority="high" />
		<?php else : ?>
			<span class="wk-plate wk-plate--lg" aria-hidden="true"><?php echo esc_html( $product['code'] ); ?></span>
		<?php endif; ?>
	</div>

	<div class="wk-product__info">
		<p class="wk-product__code">Ürün kodu <span class="wk-num"><?php echo esc_html( $product['code'] ); ?></span></p>
		<h1 class="wk-product__title"><?php echo esc_html( $product['title'] ); ?></h1>
		<?php if ( '' !== trim( (string) $product['short'] ) ) : ?>
			<p class="wk-product__short"><?php echo esc_html( $product['short'] ); ?></p>
		<?php endif; ?>

		<p class="wk-price wk-price--lg<?php echo $product['has_price'] ? '' : ' wk-price--ask'; ?>">
			<?php echo esc_html( $product['has_price'] ? $product['price'] : 'Fiyat için sorun' ); ?>
		</p>

		<div class="wk-product__actions">
			<?php if ( $wa ) : ?>
				<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="wk-btn wk-btn--wa wk-btn--lg">
					<?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					WhatsApp’tan sipariş verin
				</a>
			<?php endif; ?>
			<a href="<?php echo esc_url( $contact ); ?>" class="wk-btn wk-btn--line wk-btn--lg">Formla sorun</a>
		</div>

		<?php if ( $specs ) : ?>
			<h2 class="wk-product__subtitle">Teknik özellikler</h2>
			<dl class="wk-specs">
				<?php foreach ( $specs as $pair ) : ?>
					<div>
						<dt><?php echo esc_html( $pair[0] ); ?></dt>
						<dd><?php echo esc_html( $pair[1] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
	</div>

	<div class="wk-product__body wk-prose">
		<?php echo wp_kses_post( wpautop( (string) $product['body'] ) ); ?>
	</div>
</article>

<?php if ( $related ) : ?>
	<section class="wk-related" aria-labelledby="wk-related-title">
		<div class="wk-wrap">
			<h2 id="wk-related-title" class="wk-h2"><?php echo esc_html( $line ? sprintf( '%s serisinden', $lines[ $line ] ) : 'Diğer ürünler' ); ?></h2>
			<div class="wk-grid wk-grid--4">
				<?php foreach ( $related as $item ) : ?>
					<?php wk_part( 'product-card', array( 'product' => $item ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
