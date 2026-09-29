<?php
/**
 * Urun detay sayfasi (/urun/<slug>/) — Kocist temasi.
 *
 * Ornek urun sayfasinin (/urun/, product-main.php) duzeni havuz urunuyle
 * doldurulur: solda galeri, sagda bilgi ve butonlar. Ustte konum yolu
 * (Ana Sayfa / Kategoriler / grup / kategori / urun), altta ayni
 * kategorideki diger urunler. Butonlar ve buton alti not urun sayfasinin
 * panel alanlarindan gelir; her urunde ayri girilmez.
 *
 * Galeri davranisi assets/js/product.js, gorunum assets/css/product.css ve
 * assets/css/category.css.
 */

defined( 'ABSPATH' ) || exit;

/** @var array $product Eklentiden: site istisnalari uygulanmis havuz urunu. */

// Grup ve kategori bilgisi kategori agacindaki kaydindan.
foreach ( kocist_catalog_products() as $candidate ) {
	if ( $candidate['id'] === $product['id'] ) {
		$product = $candidate;
		break;
	}
}

$groups   = kocist_catalog_groups();
$group    = $groups[ $product['group'] ?? '' ] ?? null;
$sub      = $group['subs'][ $product['sub'] ?? '' ] ?? null;
$category = $sub ?? $group;

// Galeri: havuzdaki gorseller; hic yoksa urun adina uyan tema fotografi.
$images = array_values( array_filter( (array) ( $product['images'] ?? array() ), static fn( $image ): bool => ! empty( $image['url'] ) && ! kocist_is_placeholder_image( $image ) ) );

if ( ! $images ) {
	$images = array( kocist_product_image( $product ) );
}

// Buyuk sahne icin buyuk kopya (havuz 768 px veriyor), buyutmede tam boy.
$images = array_map( static fn( array $image ): array => kocist_pool_image_large( $image, '1536x1536' ), $images );
$main   = $images[0];

// Teklif ve WhatsApp: urune ozel (inc/quote.php).
$quote_href = kocist_quote_url( $product );
$wa_panel   = (string) nwcs_field( 'product', 'main', 'secondary_url' );
$wa_href    = kocist_is_whatsapp_url( $wa_panel ) ? kocist_product_wa_url( $product, $wa_panel ) : kocist_link( $wa_panel );

// Ayni kategorideki (yoksa ayni gruptaki) diger urunler, en fazla dort.
$related       = array();
$related_head  = '';
$related_scope = $category;

if ( $group ) {
	$pool = $sub ? kocist_catalog_products_in( $group['slug'], $sub['slug'] ) : array();

	if ( count( $pool ) > 1 ) {
		$related_key  = 'related_sub';
		$related_head = kocist_text( 'product', 'detail', 'related_sub', array( 'kategori' => $sub['name'] ) );
	} else {
		$pool          = kocist_catalog_products_in( $group['slug'] );
		$related_key   = 'related_group';
		$related_head  = kocist_text( 'product', 'detail', 'related_group', array( 'grup' => $group['name'] ) );
		$related_scope = $group;
	}

	$related = array_slice(
		array_values( array_filter( $pool, static fn( array $item ): bool => $item['id'] !== $product['id'] ) ),
		0,
		4
	);
}

// Etiketli ozellikler ("Etiket: deger; ..."): uc ve fazlasi foy, bir-iki tanesi satir icinde.
$spec_pairs = kocist_spec_pairs( (string) $product['spec'], 1 );

get_header();
?>
<div class="k-wrap k-product__crumbs">
	<?php kocist_the_trail( kocist_catalog_trail( $product['group'] ?? '', $product['sub'] ?? '', $product['title'], (int) $product['id'] ) ); ?>
</div>

<section class="k-product k-product--pool">
	<div class="k-wrap k-product__grid">

		<div class="k-product__gallery" data-k-gallery>
			<?php if ( count( $images ) > 1 ) : ?>
				<div class="k-product__thumbs">
					<?php foreach ( $images as $thumb_index => $thumb ) : ?>
						<button
							type="button"
							class="k-product__thumb<?php echo 0 === $thumb_index ? ' is-active' : ''; ?>"
							data-k-thumb
							data-full="<?php echo esc_url( $thumb['url'] ); ?>"
							data-zoom="<?php echo esc_url( $thumb['full'] ?: $thumb['url'] ); ?>"
							data-alt="<?php echo esc_attr( $thumb['alt'] ?? '' ); ?>"
							aria-label="<?php echo esc_attr( sprintf( '%d. görsel', $thumb_index + 1 ) ); ?>"
						>
							<img src="<?php echo esc_url( $thumb['thumb'] ?: $thumb['url'] ); ?>" alt="" width="64" height="64" decoding="async" />
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="k-product__stage" <?php kocist_product_attr( $product, 'Görseller' ); ?>>
				<?php echo kocist_image_tag( $main, 'k-product__photo', 'Örnek görsel — ürün' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

				<?php kocist_zoom_button( $main ); ?>

			</div>

		</div>

		<div class="k-product__info">
			<?php if ( $category ) : ?>
				<p class="k-product__category">
					<a href="<?php echo esc_url( $category['url'] ); ?>" <?php $sub ? nwcs_edit_attr( 'global', $sub['edit'][0], 'items', $sub['edit'][1], 'label' ) : nwcs_edit_attr( 'global', 'header', 'menu', $group['menu_row'], 'label' ); ?>><?php echo esc_html( $category['name'] ); ?></a>
				</p>
			<?php endif; ?>

			<h1 class="k-product__title" <?php kocist_product_attr( $product, 'Ürün adı' ); ?>><?php echo esc_html( $product['title'] ); ?></h1>

			<?php if ( $product['short'] ) : ?>
				<p class="k-product__subtitle" <?php kocist_product_attr( $product, 'Kısa açıklama' ); ?>><?php echo esc_html( $product['short'] ); ?></p>
			<?php endif; ?>

			<div class="k-product__facts">
				<span class="k-product__price<?php echo $product['has_price'] ? '' : ' is-quote'; ?>" <?php kocist_product_attr( $product, 'Fiyat' ); ?>><?php echo esc_html( $product['price_label'] ); ?></span>
				<?php // Uzun "Etiket: deger; ..." metni altta tablo olur; kisa metin burada kalir. ?>
				<?php if ( $product['spec'] && ! $spec_pairs ) : ?>
					<span class="k-product__spec" <?php kocist_product_attr( $product, 'Özellikler' ); ?>><?php echo esc_html( $product['spec'] ); ?></span>
				<?php endif; ?>
			</div>

			<div class="k-product__actions">
				<a class="k-product__btn k-product__btn--primary" href="<?php echo esc_url( $quote_href ); ?>" <?php nwcs_edit_attr( 'product', 'main', 'cta_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'product', 'main', 'cta_label' ) ?: 'Teklif Alın' ); ?>
				</a>
				<?php if ( nwcs_field( 'product', 'main', 'secondary_label' ) ) : ?>
					<a class="k-product__btn k-product__btn--ghost" href="<?php echo esc_url( $wa_href ); ?>"<?php echo kocist_is_whatsapp_url( $wa_href ) ? ' target="_blank" rel="noopener"' : ''; ?> <?php nwcs_edit_attr( 'product', 'main', 'secondary_label' ); ?>>
						<?php echo esc_html( nwcs_field( 'product', 'main', 'secondary_label' ) ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( nwcs_field( 'product', 'main', 'note' ) ) : ?>
				<p class="k-product__note" <?php nwcs_edit_attr( 'product', 'main', 'note' ); ?>><?php echo esc_html( nwcs_field( 'product', 'main', 'note' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
/*
 * Urunun hikayesi: ust bolumun altinda. Yalnizca urunun kendi icerigi
 * (detay metni, ozellikler, galeri, tablolar); hicbir sey uydurulmaz.
 * Bolum urunun ayrinti seviyesine gore kurulur (nwcs_product_detail_level):
 *  - rich:   bolumler ek fotograflarla yan yana, sirayla sag / sol.
 *  - medium: buyuk giris + iki sutun metin + teknik ozellik foyu.
 *  - table:  kisa not + olcu / model tablosu one cikar (hirdavat, civi...).
 *  - brief:  kisa metin tek sutunda, sade.
 * Foy yalnizca en az uc ozellikle; daha azi metnin altinda satir icinde.
 * One cikanlar serit yalnizca en az uc kisa ozellikle.
 */
$body_html  = trim( (string) $product['body'] ) !== '' ? wp_kses_post( wpautop( $product['body'] ) ) : '';
$split      = function_exists( 'nwcs_product_split_tables' ) ? nwcs_product_split_tables( $body_html ) : array( 'text' => $body_html, 'tables' => array() );
$tables     = kocist_product_tables( $product );
$preview    = function_exists( 'nwcs_is_preview' ) && nwcs_is_preview();
$sections   = function_exists( 'nwcs_product_body_sections' ) ? nwcs_product_body_sections( $split['text'] ) : array( 'lead' => $split['text'], 'chapters' => array() );
if ( function_exists( 'nwcs_product_demote_duplicate_lead' ) ) {
	$sections = nwcs_product_demote_duplicate_lead( $sections, (string) $product['short'] );
}
$all_pairs  = $spec_pairs ?: ( $sections['pairs'] ?? array() );
$facts      = function_exists( 'nwcs_product_key_facts' ) && count( $all_pairs ) >= 3 ? nwcs_product_key_facts( $all_pairs ) : array();
$story      = array_values( array_filter( array_slice( $images, 1 ), static fn( array $image ): bool => ! empty( $image['url'] ) && ! kocist_is_placeholder_image( $image ) ) );
$text_len   = mb_strlen( trim( wp_strip_all_tags( $split['text'] ) ) );
$level      = function_exists( 'nwcs_product_detail_level' ) ? nwcs_product_detail_level( $text_len, count( $sections['chapters'] ), count( $story ), count( $all_pairs ), (bool) ( $split['tables'] || $tables ) ) : 'medium';
$pictured   = 'rich' === $level;
$has_text   = '' !== $sections['lead'] || $sections['chapters'];
$sheet      = count( $all_pairs ) >= 3;
$top_text   = '' !== $sections['lead'] || ( ! $pictured && $sections['chapters'] );
$all_tables = $split['tables'] || $tables || $preview;

// Tablolari metnin icindeki gibi degil, urun tablosu gorunumunde basar.
$inline_table = static function ( string $html ): string {
	return (string) preg_replace( '#<table\b(?![^>]*\bclass=)#i', '<table class="k-ptable__table"', $html, 1 );
};

if ( $has_text || $all_pairs || $all_tables ) :
	?>
	<section class="k-pstory k-pstory--<?php echo esc_attr( $level ); ?><?php echo $sheet ? ' k-pstory--specs' : ''; ?>" aria-labelledby="k-pstory-title">
		<div class="k-wrap">
			<h2 class="k-pstory__heading" id="k-pstory-title" <?php nwcs_edit_attr( 'product', 'detail', 'body_title' ); ?>><?php echo esc_html( nwcs_field( 'product', 'detail', 'body_title' ) ?: 'Ürün Detayı' ); ?></h2>

			<?php if ( count( $facts ) >= 3 ) : ?>
				<dl class="k-facts" <?php kocist_product_attr( $product, 'Özellikler' ); ?>>
					<?php foreach ( $facts as $fact ) : ?>
						<div class="k-facts__item">
							<dt><?php echo esc_html( $fact[0] ); ?></dt>
							<dd><?php echo esc_html( $fact[1] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<?php if ( $top_text || $sheet ) : ?>
				<div class="k-pstory__top<?php echo $top_text ? '' : ' k-pstory__top--solo'; ?>">
					<?php if ( $top_text ) : ?>
						<div class="k-pstory__text" <?php kocist_product_attr( $product, 'Detay metni' ); ?>>
							<?php if ( 'brief' === $level || 'table' === $level ) : ?>
								<?php // Kisa metin: tek sutun, okunur boyda; buyuk giris ve iki sutun yok. ?>
								<div class="k-pstory__note">
									<?php echo wp_kses_post( $sections['lead'] ); ?>
									<?php foreach ( $sections['chapters'] as $chapter ) : ?>
										<?php if ( '' !== $chapter['title'] ) : ?>
											<h3 class="k-pstory__title"><?php echo esc_html( $chapter['title'] ); ?></h3>
										<?php endif; ?>
										<?php echo wp_kses_post( $chapter['html'] ); ?>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<?php if ( '' !== $sections['lead'] ) : ?>
									<div class="k-pstory__lead"><?php echo wp_kses_post( $sections['lead'] ); ?></div>
								<?php endif; ?>

								<?php if ( ! $pictured && $sections['chapters'] ) : ?>
									<div class="k-pstory__cols">
										<?php foreach ( $sections['chapters'] as $chapter ) : ?>
											<div class="k-pstory__chapter">
												<?php if ( '' !== $chapter['title'] ) : ?>
													<h3 class="k-pstory__title"><?php echo esc_html( $chapter['title'] ); ?></h3>
												<?php endif; ?>
												<?php echo wp_kses_post( $chapter['html'] ); ?>
											</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ( $all_pairs && ! $sheet ) : ?>
								<?php // Bir iki ozellik icin foy acilmaz: metnin altinda satir icinde. ?>
								<dl class="k-pstory__inline" <?php kocist_product_attr( $product, 'Özellikler' ); ?>>
									<?php foreach ( $all_pairs as $pair ) : ?>
										<div><dt><?php echo esc_html( $pair[0] ); ?></dt><dd><?php echo esc_html( $pair[1] ); ?></dd></div>
									<?php endforeach; ?>
								</dl>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $sheet ) : ?>
						<aside class="k-pstory__specs" <?php kocist_product_attr( $product, 'Özellikler' ); ?>>
							<h3 class="k-pstory__specs-title">Teknik özellikler</h3>
							<dl class="k-specs-list">
								<?php foreach ( $all_pairs as $pair ) : ?>
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

			<?php if ( $all_tables ) : ?>
				<div class="k-pdetail__tables k-pstory__tables">
					<?php foreach ( $split['tables'] as $table_html ) : ?>
						<div class="k-ptable" <?php kocist_product_attr( $product, 'Detay metni' ); ?>>
							<div class="k-ptable__scroll" role="region" aria-label="<?php echo esc_attr( $product['title'] ); ?> tablosu" tabindex="0">
								<?php echo wp_kses_post( $inline_table( $table_html ) ); ?>
							</div>
						</div>
					<?php endforeach; ?>

					<?php foreach ( $tables as $table ) : ?>
						<?php kocist_render_product_table( $table, $product ); ?>
					<?php endforeach; ?>

					<?php if ( ! $tables && $preview ) : ?>
						<?php // Yalnizca panel onizlemesinde: tiklaninca urun Urun Havuzu'nda acilir. ?>
						<div class="k-ptable k-ptable--empty" <?php kocist_product_attr( $product, 'Ürün tablosu' ); ?>>
							<p class="k-ptable__empty-title">Bu ürüne tablo ekleyin</p>
							<p class="k-ptable__empty-text">Tıklayın; ürün Ürün Havuzu'nda açılır. “Ürün Tabloları” bölümünden tabloyu hücre hücre doldurun. Bu kutu yalnızca panelde görünür.</p>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $pictured ) : ?>
				<div class="k-pstory__rows" <?php kocist_product_attr( $product, 'Detay metni' ); ?>>
					<?php foreach ( $sections['chapters'] as $index => $chapter ) : ?>
						<?php $picture = $story[ $index ] ?? null; ?>
						<div class="k-pstory__row<?php echo $picture ? '' : ' k-pstory__row--text'; ?>">
							<?php if ( $picture ) : ?>
								<button type="button" class="k-pstory__photo" data-k-pstory-open="<?php echo (int) $index + 1; ?>"
									aria-label="<?php echo esc_attr( $chapter['title'] ?: $product['title'] ); ?> görselini büyüt">
									<img src="<?php echo esc_url( $picture['url'] ); ?>"
										<?php if ( ! empty( $picture['srcset'] ) ) : ?>srcset="<?php echo esc_attr( $picture['srcset'] ); ?>" sizes="(min-width: 900px) 50vw, 100vw"<?php endif; ?>
										alt="<?php echo esc_attr( $picture['alt'] ?? '' ); ?>" loading="lazy" decoding="async" />
								</button>
							<?php endif; ?>
							<div class="k-pstory__chapter">
								<?php if ( '' !== $chapter['title'] ) : ?>
									<h3 class="k-pstory__title"><?php echo esc_html( $chapter['title'] ); ?></h3>
								<?php endif; ?>
								<?php echo wp_kses_post( $chapter['html'] ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php elseif ( count( $story ) >= 2 ) : ?>
				<?php // Metin kisa ama fotograf cok: fotograflar serit halinde; tiklayinca buyutme penceresi. ?>
				<ul class="k-pstory__strip" aria-label="<?php echo esc_attr( $product['title'] ); ?> fotoğrafları">
					<?php foreach ( array_slice( $story, 0, 6 ) as $index => $picture ) : ?>
						<li>
							<button type="button" class="k-pstory__photo" data-k-pstory-open="<?php echo (int) $index + 1; ?>" aria-label="<?php echo esc_attr( sprintf( '%d. fotoğrafı büyüt', $index + 2 ) ); ?>">
								<img src="<?php echo esc_url( $picture['url'] ); ?>" alt="<?php echo esc_attr( $picture['alt'] ?? '' ); ?>" loading="lazy" decoding="async" />
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( $related ) : ?>
	<section class="k-section k-related">
		<div class="k-wrap">
			<div class="k-related__head">
				<h2 class="k-related__title" <?php nwcs_edit_attr( 'product', 'detail', $related_key ); ?>><?php echo esc_html( $related_head ); ?></h2>
				<a class="k-shelf__all" href="<?php echo esc_url( $related_scope['url'] ); ?>" <?php nwcs_edit_attr( 'product', 'detail', 'see_all' ); ?>>
					<?php echo esc_html( nwcs_field( 'product', 'detail', 'see_all' ) ); ?>
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
						<path d="M3 8h9.5M8.5 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</a>
			</div>

			<ul class="k-plist">
				<?php foreach ( $related as $item ) : ?>
					<?php $has_page = '' !== trim( (string) $item['body'] ); ?>
					<li class="k-pcard">
						<a class="k-pcard__link" href="<?php echo esc_url( $has_page ? $item['url'] : kocist_quote_url( $item ) ); ?>">
							<span class="k-pcard__media" <?php kocist_product_attr( $item, 'Görsel' ); ?>>
								<?php echo kocist_image_tag( kocist_product_image( $item ), 'k-pcard__img', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
							</span>
							<span class="k-pcard__body">
								<span class="k-pcard__title" <?php kocist_product_attr( $item, 'Ürün adı' ); ?>><?php echo esc_html( $item['title'] ); ?></span>
								<span class="k-pcard__foot">
									<?php if ( $item['has_price'] ) : ?>
										<span class="k-pcard__price" <?php kocist_product_attr( $item, 'Fiyat' ); ?>><?php echo esc_html( $item['price_label'] ); ?></span>
									<?php else : ?>
										<span class="k-pcard__price is-quote" <?php nwcs_edit_attr( 'kategoriler', 'texts', 'price_quote' ); ?>><?php echo esc_html( nwcs_field( 'kategoriler', 'texts', 'price_quote' ) ); ?></span>
									<?php endif; ?>
									<span class="k-pcard__go" <?php nwcs_edit_attr( 'kategoriler', 'texts', $has_page ? 'go_detail' : 'go_quote' ); ?>><?php echo esc_html( nwcs_field( 'kategoriler', 'texts', $has_page ? 'go_detail' : 'go_quote' ) ); ?></span>
								</span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php
// Sik sorulan sorular urun sayfasiyla ortak; panelden tek yerde duzenlenir.
kocist_section( 'product-faq' );

get_footer();
