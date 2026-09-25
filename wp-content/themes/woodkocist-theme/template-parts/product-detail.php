<?php
/**
 * Urun sayfasi (/urun/<slug>/). Eklenti (nwcs_product_template) bu dosyayi
 * $product ile cagirir: site istisnalari uygulanmis havuz urunu.
 *
 * Solda gorsel (yoksa kod plakasi) ve aciklama; sagda fiyat, adet ve "Sepete
 * ekle" (fiyatsiz urunde WhatsApp / form ile fiyat sorma),
 * altinda teknik ozellikler tablosu; en altta ayni kategoriden urunler.
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
$contact = add_query_arg( 'urun', rawurlencode( trim( $product['code'] . ' ' . $product['title'] ) ), wk_page_url( 'iletisim' ) ) . '#talep';
$place   = wk_product_place( $product );
$group   = $place['child'] ?? $place['line'];
$buy     = wk_can_buy( $product );

// Mobil WhatsApp cubugu (footer.php) bu urunun mesajini kullansin.
$GLOBALS['wk_current_product'] = $product;

$related = array_slice(
	array_values(
		array_filter(
			wk_products(),
			static fn( array $p ): bool => $p['id'] !== $product['id'] && ( ! $group || isset( $p['categories'][ $group['slug'] ] ) )
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
		<li><a href="<?php echo esc_url( wk_shop_url() ); ?>">Mağaza</a></li>
		<?php if ( $place['line'] ) : ?>
			<li><a href="<?php echo esc_url( $place['line']['url'] ); ?>"><?php echo esc_html( $place['line']['label'] ); ?></a></li>
		<?php endif; ?>
		<?php if ( $place['child'] ) : ?>
			<li><a href="<?php echo esc_url( $place['child']['url'] ); ?>"><?php echo esc_html( $place['child']['label'] ); ?></a></li>
		<?php endif; ?>
		<li aria-current="page"><?php echo esc_html( $product['code'] ); ?></li>
	</ol>
</nav>

<article class="wk-wrap wk-product">
	<div class="wk-product__media" <?php wk_product_src( $product, 'Görsel' ); ?>>
		<?php if ( $image['url'] ) : ?>
			<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?: $product['title'] ); ?>" fetchpriority="high" />
		<?php else : ?>
			<span class="wk-plate wk-plate--lg" aria-hidden="true"><?php echo esc_html( $product['code'] ); ?></span>
		<?php endif; ?>
	</div>

	<div class="wk-product__info">
		<p class="wk-product__code" <?php wk_product_src( $product, 'Ürün kodu' ); ?>>Ürün kodu <span class="wk-num"><?php echo esc_html( $product['code'] ); ?></span></p>
		<h1 class="wk-product__title" <?php wk_product_src( $product, 'Ürün adı' ); ?>><?php echo esc_html( $product['title'] ); ?></h1>
		<?php if ( '' !== trim( (string) $product['short'] ) ) : ?>
			<p class="wk-product__short" <?php wk_product_src( $product, 'Kısa açıklama' ); ?>><?php echo esc_html( $product['short'] ); ?></p>
		<?php endif; ?>

		<div class="wk-buy">
			<?php if ( $buy ) : ?>
				<p class="wk-buy__price"><span class="wk-num"><?php echo esc_html( $product['price'] ); ?></span> <small><?php echo esc_html( wk_price_note() ); ?></small></p>
				<?php wk_add_to_cart_form( $product, true, 'wk-add--lg' ); ?>
				<?php if ( $wa ) : ?>
					<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="wk-buy__alt">
						<?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
						Sorunuz mu var? WhatsApp’tan yazın
					</a>
				<?php endif; ?>
			<?php else : ?>
				<p class="wk-buy__price wk-buy__price--ask">Fiyat için sorun</p>
				<p class="wk-buy__why">Bu ürünün fiyatını size ayrıca bildiriyoruz. WhatsApp mesajına ürün kodu kendiliğinden eklenir.</p>
				<div class="wk-product__actions">
					<?php if ( $wa ) : ?>
						<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="wk-btn wk-btn--primary wk-btn--lg">
							<?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
							WhatsApp’tan fiyat sor
						</a>
					<?php endif; ?>
					<a href="<?php echo esc_url( $contact ); ?>" class="wk-btn wk-btn--line wk-btn--lg">Formla sor</a>
				</div>
			<?php endif; ?>
			<ul class="wk-assure">
				<li><?php echo wk_icon( 'truck' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> <?php echo esc_html( nwcs_field( 'global', 'shop', 'shipping_note' ) ); ?></li>
				<li><?php echo wk_icon( 'ruler' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> Farklı ölçü mü lazım? <a href="<?php echo esc_url( wk_page_url( 'ozel-uretim-talep-formu' ) ); ?>">Özel üretim isteyin</a></li>
			</ul>
		</div>

		<?php if ( $specs ) : ?>
			<h2 class="wk-product__subtitle">Teknik özellikler</h2>
			<dl class="wk-specs" <?php wk_product_src( $product, 'Teknik özellikler' ); ?>>
				<?php foreach ( $specs as $pair ) : ?>
					<div>
						<dt><?php echo esc_html( $pair[0] ); ?></dt>
						<dd><?php echo esc_html( $pair[1] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
	</div>

	<div class="wk-product__body wk-prose" <?php wk_product_src( $product, 'Detay metni' ); ?>>
		<?php echo wp_kses_post( wpautop( (string) $product['body'] ) ); ?>
	</div>
</article>

<?php if ( $related ) : ?>
	<section class="wk-related" aria-labelledby="wk-related-title">
		<div class="wk-wrap">
			<h2 id="wk-related-title" class="wk-h2"><?php echo esc_html( $group ? sprintf( 'Diğer %s', mb_strtolower( $group['label'] ) ) : 'Diğer ürünler' ); ?></h2>
			<div class="wk-grid wk-grid--4">
				<?php foreach ( $related as $item ) : ?>
					<?php wk_part( 'product-card', array( 'product' => $item, 'heading' => 'h3' ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
