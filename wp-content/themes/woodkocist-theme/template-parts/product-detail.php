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
/*
 * Urunun hikayesi: gorselin altindaki bolum. Yalnizca urunun kendi icerigi
 * (detay metni, ozellikler, galeri); hicbir sey uydurulmaz. Bolum urunun
 * ayrinti seviyesine gore kurulur (nwcs_product_detail_level):
 *  - rich:   bolumler ek fotograflarla yan yana, sirayla sag / sol.
 *  - medium: buyuk giris + iki sutun metin + teknik ozellik foyu.
 *  - table:  kisa not + metindeki olcu / model tablosu one cikar.
 *  - brief:  kisa metin iki dengeli sutunda, sade.
 * Foy yalnizca en az uc ozellikle; daha azi metnin altinda satir icinde.
 */
$split    = function_exists( 'nwcs_product_split_tables' ) ? nwcs_product_split_tables( wp_kses_post( wpautop( (string) $product['body'] ) ) ) : array( 'text' => wp_kses_post( wpautop( (string) $product['body'] ) ), 'tables' => array() );
$sections = function_exists( 'nwcs_product_body_sections' ) ? nwcs_product_body_sections( $split['text'] ) : array( 'lead' => $split['text'], 'chapters' => array() );
if ( function_exists( 'nwcs_product_demote_duplicate_lead' ) ) {
	$sections = nwcs_product_demote_duplicate_lead( $sections, (string) $product['short'] );
}
// Urunun kendi ozellikleri yoksa detay metnindeki "Etiket: deger" satirlari.
if ( ! $specs && ! empty( $sections['pairs'] ) ) {
	$specs = $sections['pairs'];
}
$facts    = function_exists( 'nwcs_product_key_facts' ) && count( $specs ) >= 3 ? nwcs_product_key_facts( $specs ) : array();
$story    = array_slice( $gallery, 1 );
$text_len = mb_strlen( trim( wp_strip_all_tags( $split['text'] ) ) );
$level    = function_exists( 'nwcs_product_detail_level' ) ? nwcs_product_detail_level( $text_len, count( $sections['chapters'] ), count( $story ), count( $specs ), (bool) $split['tables'] ) : 'medium';
$pictured = 'rich' === $level;
$has_text = '' !== $sections['lead'] || $sections['chapters'];
$sheet    = count( $specs ) >= 3;
// Ust satirda metin var mi: giris ya da (fotografsiz duzende) bolumler. Yoksa foy tek basina, iki sutun.
$top_text = '' !== $sections['lead'] || ( ! $pictured && $sections['chapters'] );
?>
<?php if ( $has_text || $specs || $split['tables'] ) : ?>
	<section class="wk-wrap wk-story wk-story--<?php echo esc_attr( $level ); ?><?php echo $sheet ? ' wk-story--specs' : ''; ?>" aria-label="Ürün detayı">
		<?php if ( count( $facts ) >= 3 ) : ?>
			<dl class="wk-facts" <?php wk_product_src( $product, 'Teknik özellikler' ); ?>>
				<?php foreach ( $facts as $fact ) : ?>
					<div class="wk-facts__item">
						<dt><?php echo esc_html( $fact[0] ); ?></dt>
						<dd><?php echo esc_html( $fact[1] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>

		<?php if ( $top_text || $sheet ) : ?>
			<div class="wk-story__top<?php echo $top_text ? '' : ' wk-story__top--solo'; ?>">
				<?php if ( $top_text ) : ?>
					<div class="wk-story__text" <?php wk_product_src( $product, 'Detay metni' ); ?>>
						<?php if ( 'brief' === $level || 'table' === $level ) : ?>
							<div class="wk-story__note">
								<?php echo wp_kses_post( $sections['lead'] ); ?>
								<?php foreach ( $sections['chapters'] as $chapter ) : ?>
									<?php if ( '' !== $chapter['title'] ) : ?>
										<h3 class="wk-story__title"><?php echo esc_html( $chapter['title'] ); ?></h3>
									<?php endif; ?>
									<?php echo wp_kses_post( $chapter['html'] ); ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<?php if ( '' !== $sections['lead'] ) : ?>
								<div class="wk-story__lead"><?php echo wp_kses_post( $sections['lead'] ); ?></div>
							<?php endif; ?>

							<?php if ( ! $pictured && $sections['chapters'] ) : ?>
								<div class="wk-story__cols">
									<?php foreach ( $sections['chapters'] as $chapter ) : ?>
										<div class="wk-story__chapter">
											<?php if ( '' !== $chapter['title'] ) : ?>
												<h3 class="wk-story__title"><?php echo esc_html( $chapter['title'] ); ?></h3>
											<?php endif; ?>
											<?php echo wp_kses_post( $chapter['html'] ); ?>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( $specs && ! $sheet ) : ?>
							<?php // Bir iki ozellik icin foy acilmaz: metnin altinda satir icinde. ?>
							<dl class="wk-story__inline" <?php wk_product_src( $product, 'Teknik özellikler' ); ?>>
								<?php foreach ( $specs as $pair ) : ?>
									<div><dt><?php echo esc_html( $pair[0] ); ?></dt><dd><?php echo esc_html( $pair[1] ); ?></dd></div>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $sheet ) : ?>
					<aside class="wk-story__specs">
						<h2 class="wk-story__specs-title" <?php nwcs_edit_attr( 'product', 'labels', 'specs_title' ); ?>><?php echo esc_html( nwcs_field( 'product', 'labels', 'specs_title' ) ); ?></h2>
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
			</div>
		<?php endif; ?>

		<?php if ( $split['tables'] ) : ?>
			<div class="wk-story__tables" <?php wk_product_src( $product, 'Detay metni' ); ?>>
				<?php foreach ( $split['tables'] as $table_html ) : ?>
					<div class="wk-story__table" role="region" aria-label="<?php echo esc_attr( $product['title'] ); ?> tablosu" tabindex="0"><?php echo wp_kses_post( $table_html ); ?></div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $pictured ) : ?>
			<div class="wk-story__rows" <?php wk_product_src( $product, 'Detay metni' ); ?>>
				<?php foreach ( $sections['chapters'] as $index => $chapter ) : ?>
					<?php $picture = $story[ $index ] ?? null; ?>
					<div class="wk-story__row<?php echo $picture ? '' : ' wk-story__row--text'; ?>">
						<?php if ( $picture ) : ?>
							<button type="button" class="wk-story__photo" data-wk-open="<?php echo (int) $index + 1; ?>"
								aria-label="<?php echo esc_attr( $chapter['title'] ?: $product['title'] ); ?> görselini tam ekranda aç">
								<img src="<?php echo esc_url( $picture['src'] ); ?>"
									<?php if ( $picture['srcset'] ) : ?>srcset="<?php echo esc_attr( $picture['srcset'] ); ?>" sizes="(min-width: 960px) 50vw, 100vw"<?php endif; ?>
									<?php if ( $picture['width'] && $picture['height'] ) : ?>width="<?php echo (int) $picture['width']; ?>" height="<?php echo (int) $picture['height']; ?>"<?php endif; ?>
									alt="<?php echo esc_attr( $picture['alt'] ); ?>" loading="lazy" decoding="async" />
							</button>
						<?php endif; ?>
						<div class="wk-story__chapter">
							<?php if ( '' !== $chapter['title'] ) : ?>
								<h3 class="wk-story__title"><?php echo esc_html( $chapter['title'] ); ?></h3>
							<?php endif; ?>
							<?php echo wp_kses_post( $chapter['html'] ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php elseif ( count( $story ) >= 2 ) : ?>
			<?php // Metin kisa ama fotograf cok: fotograf seridi; tiklayinca tam ekran. ?>
			<ul class="wk-story__strip" aria-label="<?php echo esc_attr( $product['title'] ); ?> fotoğrafları">
				<?php foreach ( array_slice( $story, 0, 6 ) as $index => $picture ) : ?>
					<li>
						<button type="button" class="wk-story__photo" data-wk-open="<?php echo (int) $index + 1; ?>" aria-label="<?php echo esc_attr( sprintf( '%d. fotoğrafı tam ekranda aç', $index + 2 ) ); ?>">
							<img src="<?php echo esc_url( $picture['src'] ); ?>" <?php if ( $picture['srcset'] ) : ?>srcset="<?php echo esc_attr( $picture['srcset'] ); ?>" sizes="(min-width: 960px) 16vw, 45vw"<?php endif; ?> alt="<?php echo esc_attr( $picture['alt'] ); ?>" loading="lazy" decoding="async" />
						</button>
					</li>
				<?php endforeach; ?>
			</ul>
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
