<?php
/**
 * Urun sayfasi (/urun/<slug>/). Eklenti (nwcs_product_template) bu dosyayi
 * $product ile cagirir: site istisnalari uygulanmis havuz urunu.
 *
 * Solda ad, fiyat, adet ve "Sepete ekle" (fiyatsiz urunde WhatsApp / form ile
 * fiyat sorma); sagda buyuk galeri (yoksa kod plakasi).
 * Gorsel kendi oraninda, kirpilmadan gosterilir: dikey fotograf ekrandan uzun
 * olabilir (firmanin istegi, eski woodkocist.com.tr urun sayfasi gibi).
 * Fareyle uzerine gelince imlecin oldugu yer buyur; tiklayinca tam ekran,
 * tam ekranda tiklayinca daha da yakinlasir. Altta detay metni ve teknik
 * ozellikler kendi bolumunde, en altta ayni kategoriden urunler.
 */

defined( 'ABSPATH' ) || exit;

/** @var array $product */

foreach ( wk_products() as $candidate ) {
	if ( $candidate['id'] === $product['id'] ) {
		$product = $candidate;
		break;
	}
}

$gallery = wk_gallery( $product );
$specs   = wk_specs( (string) $product['spec'] );
$wa      = wk_whatsapp( wk_order_text( $product ) );
// Formlar ?urun=<havuz no> ile acilir; urun sunucuda cozulur ve form dolu gelir.
$contact = add_query_arg( 'urun', (int) $product['id'], wk_page_url( 'iletisim' ) ) . '#talep';
$custom  = add_query_arg( 'urun', (int) $product['id'], wk_page_url( 'ozel-uretim-talep-formu' ) ) . '#talep';
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
	<div class="wk-product__media" data-wk-gallery <?php wk_product_src( $product, 'Görsel' ); ?>>
		<?php if ( $gallery ) : ?>
			<?php if ( count( $gallery ) > 1 ) : ?>
				<ul class="wk-product__thumbs" aria-label="<?php echo esc_attr( $product['title'] ); ?> görselleri">
					<?php foreach ( $gallery as $index => $item ) : ?>
						<li>
							<button type="button" class="wk-product__thumb<?php echo 0 === $index ? ' is-active' : ''; ?>"
								data-wk-thumb
								data-src="<?php echo esc_url( $item['src'] ); ?>"
								data-srcset="<?php echo esc_attr( $item['srcset'] ); ?>"
								data-width="<?php echo (int) $item['width']; ?>" data-height="<?php echo (int) $item['height']; ?>"
								aria-label="<?php echo esc_attr( sprintf( 'Görsel %d', $index + 1 ) ); ?>"
								aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>">
								<img src="<?php echo esc_url( $item['thumb'] ); ?>" alt="" width="64" height="64" loading="lazy" decoding="async" />
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php $first = $gallery[0]; ?>
			<?php // Fareyle uzerine gelince imlecin oldugu yer buyur; tiklayinca tam ekran (assets/site.js). ?>
			<button type="button" class="wk-product__stage" data-wk-stage data-full="<?php echo esc_url( $first['src'] ); ?>"
				aria-label="<?php echo esc_attr( $product['title'] ); ?> görselini tam ekranda aç">
				<img class="wk-product__photo" data-wk-photo src="<?php echo esc_url( $first['src'] ); ?>"
					<?php if ( $first['srcset'] ) : ?>srcset="<?php echo esc_attr( $first['srcset'] ); ?>" sizes="(min-width: 1280px) 760px, (min-width: 960px) 58vw, 100vw"<?php endif; ?>
					<?php if ( $first['width'] && $first['height'] ) : ?>width="<?php echo (int) $first['width']; ?>" height="<?php echo (int) $first['height']; ?>"<?php endif; ?>
					alt="<?php echo esc_attr( $first['alt'] ?: $product['title'] ); ?>" fetchpriority="high" decoding="async" />
			</button>

			<dialog class="wk-lb" data-wk-lb aria-label="<?php echo esc_attr( $product['title'] ); ?>">
				<div class="wk-lb__stage" data-wk-lb-stage>
					<img class="wk-lb__img" data-wk-lb-img src="" alt="<?php echo esc_attr( $product['title'] ); ?>" />
				</div>
				<p class="wk-lb__hint" data-wk-lb-hint>Yakınlaştırmak için görsele tıklayın; yakınken fareyle gezinin.</p>
				<?php if ( count( $gallery ) > 1 ) : ?>
					<button type="button" class="wk-lb__nav wk-lb__nav--prev" data-wk-lb-prev aria-label="Önceki görsel"><span aria-hidden="true">‹</span></button>
					<button type="button" class="wk-lb__nav wk-lb__nav--next" data-wk-lb-next aria-label="Sonraki görsel"><span aria-hidden="true">›</span></button>
					<p class="wk-lb__count" data-wk-lb-count aria-live="polite"></p>
				<?php endif; ?>
				<button type="button" class="wk-lb__close" data-wk-lb-close aria-label="Kapat"><span aria-hidden="true">×</span></button>
			</dialog>
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
				<li <?php nwcs_edit_attr( 'product', 'labels', 'custom_question' ); ?>><?php echo wk_icon( 'ruler' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> <?php echo esc_html( nwcs_field( 'product', 'labels', 'custom_question' ) ); ?> <a href="<?php echo esc_url( $custom ); ?>" <?php nwcs_edit_attr( 'product', 'labels', 'custom_link' ); ?>><?php echo esc_html( nwcs_field( 'product', 'labels', 'custom_link' ) ); ?></a></li>
			</ul>
		</div>

	</div>

</article>

<?php
$has_body = '' !== trim( wp_strip_all_tags( (string) $product['body'] ) );
?>
<?php if ( $has_body || $specs ) : ?>
	<?php // Detay metni ve teknik ozellikler gorselin altinda, kendi bolumunde: sol sutun uzayip sag bos kalmasin. ?>
	<section class="wk-wrap wk-pdetail<?php echo $has_body && $specs ? ' wk-pdetail--split' : ''; ?>" aria-label="Ürün detayı">
		<?php if ( $has_body ) : ?>
			<div class="wk-pdetail__text wk-prose" <?php wk_product_src( $product, 'Detay metni' ); ?>>
				<?php echo wp_kses_post( wpautop( (string) $product['body'] ) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $specs ) : ?>
			<aside class="wk-pdetail__specs">
				<h2 class="wk-product__subtitle" <?php nwcs_edit_attr( 'product', 'labels', 'specs_title' ); ?>><?php echo esc_html( nwcs_field( 'product', 'labels', 'specs_title' ) ); ?></h2>
				<dl class="wk-specs" <?php wk_product_src( $product, 'Teknik özellikler' ); ?>>
					<?php foreach ( $specs as $pair ) : ?>
						<div>
							<dt><?php echo esc_html( $pair[0] ); ?></dt>
							<dd><?php echo esc_html( $pair[1] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</aside>
		<?php endif; ?>
	</section>
<?php endif; ?>

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
