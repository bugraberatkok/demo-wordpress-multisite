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
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" <?php nwcs_edit_attr( 'global', 'legal', 'crumb_home' ); ?>><?php echo esc_html( nwcs_field( 'global', 'legal', 'crumb_home' ) ); ?></a></li>
		<li><a href="<?php echo esc_url( wk_shop_url() ); ?>" <?php nwcs_edit_attr( 'global', 'legal', 'crumb_shop' ); ?>><?php echo esc_html( nwcs_field( 'global', 'legal', 'crumb_shop' ) ); ?></a></li>
		<?php if ( $place['line'] ) : ?>
			<li><a href="<?php echo esc_url( $place['line']['url'] ); ?>" <?php nwcs_edit_attr( 'home', 'catalog', 'lines', (int) $place['line']['row'], 'label' ); ?>><?php echo esc_html( $place['line']['label'] ); ?></a></li>
		<?php endif; ?>
		<?php if ( $place['child'] ) : ?>
			<?php $child_page = wk_category_page_key( $place['child']['slug'] ); ?>
			<li><a href="<?php echo esc_url( $place['child']['url'] ); ?>" <?php $child_page && nwcs_edit_attr( $child_page, 'head', 'name' ); ?>><?php echo esc_html( $place['child']['label'] ); ?></a></li>
		<?php endif; ?>
		<li aria-current="page" <?php wk_product_src( $product, 'Ürün kodu' ); ?>><?php echo esc_html( $product['code'] ); ?></li>
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
		<p class="wk-product__code" <?php wk_product_src( $product, 'Ürün kodu' ); ?>><span <?php nwcs_edit_attr( 'product', 'labels', 'code_label' ); ?>><?php echo esc_html( nwcs_field( 'product', 'labels', 'code_label' ) ); ?></span> <span class="wk-num"><?php echo esc_html( $product['code'] ); ?></span></p>
		<h1 class="wk-product__title" <?php wk_product_src( $product, 'Ürün adı' ); ?>><?php echo esc_html( $product['title'] ); ?></h1>
		<?php if ( '' !== trim( (string) $product['short'] ) ) : ?>
			<p class="wk-product__short" <?php wk_product_src( $product, 'Kısa açıklama' ); ?>><?php echo esc_html( $product['short'] ); ?></p>
		<?php endif; ?>

		<div class="wk-buy">
			<?php if ( $buy ) : ?>
				<p class="wk-buy__price"><span class="wk-num" <?php wk_product_src( $product, 'Fiyat' ); ?>><?php echo esc_html( $product['price'] ); ?></span> <small <?php wk_price_note_attr(); ?>><?php echo esc_html( wk_price_note() ); ?></small></p>
				<?php wk_add_to_cart_form( $product, true, 'wk-add--lg' ); ?>
				<?php if ( $wa ) : ?>
					<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="wk-buy__alt" <?php nwcs_edit_attr( 'product', 'labels', 'wa_question' ); ?>>
						<?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
						<?php echo esc_html( nwcs_field( 'product', 'labels', 'wa_question' ) ); ?>
					</a>
				<?php endif; ?>
			<?php else : ?>
				<p class="wk-buy__price wk-buy__price--ask" <?php nwcs_edit_attr( 'global', 'card', 'price_ask' ); ?>><?php echo esc_html( nwcs_field( 'global', 'card', 'price_ask' ) ); ?></p>
				<p class="wk-buy__why" <?php nwcs_edit_attr( 'product', 'labels', 'ask_why' ); ?>><?php echo esc_html( nwcs_field( 'product', 'labels', 'ask_why' ) ); ?></p>
				<div class="wk-product__actions">
					<?php if ( $wa ) : ?>
						<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="wk-btn wk-btn--primary wk-btn--lg" <?php nwcs_edit_attr( 'product', 'labels', 'wa_price' ); ?>>
							<?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
							<?php echo esc_html( nwcs_field( 'product', 'labels', 'wa_price' ) ); ?>
						</a>
					<?php endif; ?>
					<a href="<?php echo esc_url( $contact ); ?>" class="wk-btn wk-btn--line wk-btn--lg" <?php nwcs_edit_attr( 'product', 'labels', 'form_ask' ); ?>><?php echo esc_html( nwcs_field( 'product', 'labels', 'form_ask' ) ); ?></a>
				</div>
			<?php endif; ?>
			<ul class="wk-assure">
				<li <?php nwcs_edit_attr( 'global', 'shop', 'shipping_note' ); ?>><?php echo wk_icon( 'truck' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> <?php echo esc_html( nwcs_field( 'global', 'shop', 'shipping_note' ) ); ?></li>
				<li <?php nwcs_edit_attr( 'product', 'labels', 'custom_question' ); ?>><?php echo wk_icon( 'ruler' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> <?php echo esc_html( nwcs_field( 'product', 'labels', 'custom_question' ) ); ?> <a href="<?php echo esc_url( wk_page_url( 'ozel-uretim-talep-formu' ) ); ?>" <?php nwcs_edit_attr( 'product', 'labels', 'custom_link' ); ?>><?php echo esc_html( nwcs_field( 'product', 'labels', 'custom_link' ) ); ?></a></li>
			</ul>
		</div>

		<?php if ( $specs ) : ?>
			<h2 class="wk-product__subtitle" <?php nwcs_edit_attr( 'product', 'labels', 'specs_title' ); ?>><?php echo esc_html( nwcs_field( 'product', 'labels', 'specs_title' ) ); ?></h2>
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
			<h2 id="wk-related-title" class="wk-h2" <?php nwcs_edit_attr( 'product', 'labels', $group ? 'related_title' : 'related_all' ); ?>><?php echo esc_html( $group ? wk_text( 'product', 'labels', 'related_title', array( 'kategori' => mb_strtolower( $group['label'] ) ) ) : nwcs_field( 'product', 'labels', 'related_all' ) ); ?></h2>
			<?php
			// Karta tiklayinca bu kategorinin (yoksa Magaza'nin) urun listesi acilir; ad ve fiyat orada siteye ozel.
			$related_page = $group ? wk_category_page_key( $group['slug'] ) : '';
			$related_edit = $related_page ? array( $related_page, 'products', 'pool' ) : array( 'shop', 'pool', 'pool' );
			?>
			<div class="wk-grid wk-grid--4">
				<?php foreach ( $related as $item ) : ?>
					<?php wk_part( 'product-card', array( 'product' => $item, 'heading' => 'h3', 'edit' => $related_edit ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
