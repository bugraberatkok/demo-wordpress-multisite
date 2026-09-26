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

$main = $images[0];

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

get_header();
?>
<div class="k-wrap k-product__crumbs">
	<?php kocist_the_trail( kocist_catalog_trail( $product['group'] ?? '', $product['sub'] ?? '', $product['title'], (int) $product['id'] ) ); ?>
</div>

<section class="k-product k-product--pool">
	<div class="k-wrap k-product__grid">

		<div class="k-product__gallery" data-k-gallery>
			<div class="k-product__stage" <?php kocist_product_attr( $product, 'Görseller' ); ?>>
				<?php echo kocist_image_tag( $main, 'k-product__photo', 'Örnek görsel — ürün' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

				<?php kocist_zoom_button( $main ); ?>

				<?php if ( count( $images ) > 1 ) : ?>
					<button type="button" class="k-product__arrow k-product__arrow--prev" data-k-gallery-prev>
						<span class="screen-reader-text">Önceki görsel</span>
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
							<path d="M11 3.5 5.5 9l5.5 5.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</button>

					<button type="button" class="k-product__arrow k-product__arrow--next" data-k-gallery-next>
						<span class="screen-reader-text">Sonraki görsel</span>
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
							<path d="M7 3.5 12.5 9 7 14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</button>

					<span class="k-product__counter" data-k-gallery-counter aria-hidden="true">1 / <?php echo (int) count( $images ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( count( $images ) > 1 ) : ?>
				<div class="k-product__thumbs">
					<?php foreach ( $images as $thumb_index => $thumb ) : ?>
						<button
							type="button"
							class="k-product__thumb<?php echo 0 === $thumb_index ? ' is-active' : ''; ?>"
							data-k-thumb
							data-full="<?php echo esc_url( $thumb['url'] ); ?>"
							data-alt="<?php echo esc_attr( $thumb['alt'] ?? '' ); ?>"
							aria-label="<?php echo esc_attr( sprintf( '%d. görsel', $thumb_index + 1 ) ); ?>"
						>
							<img src="<?php echo esc_url( $thumb['url'] ); ?>" alt="" loading="lazy" decoding="async" />
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
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
				<?php if ( $product['spec'] ) : ?>
					<span class="k-product__spec" <?php kocist_product_attr( $product, 'Özellikler' ); ?>><?php echo esc_html( $product['spec'] ); ?></span>
				<?php endif; ?>
			</div>

			<div class="k-product__actions">
				<a class="k-product__btn k-product__btn--primary" href="<?php echo esc_url( kocist_link( nwcs_field( 'product', 'main', 'cta_url' ) ?: '#teklif' ) ); ?>" <?php nwcs_edit_attr( 'product', 'main', 'cta_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'product', 'main', 'cta_label' ) ?: 'Teklif Alın' ); ?>
				</a>
				<?php if ( nwcs_field( 'product', 'main', 'secondary_label' ) ) : ?>
					<a class="k-product__btn k-product__btn--ghost" href="<?php echo esc_url( kocist_link( nwcs_field( 'product', 'main', 'secondary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'product', 'main', 'secondary_label' ); ?>>
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
 * Detay metni ve urun tablolari: ust bolumun altinda, tam genislikte. Uzun
 * metin bilgi sutununu uzatip butonlari asagi itmesin diye buraya alindi.
 */
$body_html = trim( (string) $product['body'] ) !== '' ? wp_kses_post( wpautop( $product['body'] ) ) : '';
$tables    = kocist_product_tables( $product );
$preview   = function_exists( 'nwcs_is_preview' ) && nwcs_is_preview();

if ( '' !== $body_html || $tables || $preview ) :
	?>
	<section class="k-pdetail<?php echo ( '' !== $body_html && ( $tables || $preview ) ) ? ' k-pdetail--split' : ''; ?>" aria-labelledby="k-pdetail-title">
		<div class="k-wrap">
			<h2 class="k-pdetail__title" id="k-pdetail-title" <?php nwcs_edit_attr( 'product', 'detail', 'body_title' ); ?>><?php echo esc_html( nwcs_field( 'product', 'detail', 'body_title' ) ?: 'Ürün Detayı' ); ?></h2>

			<div class="k-pdetail__grid">
				<?php if ( '' !== $body_html ) : ?>
					<div class="k-pdetail__text k-product__desc--rich" <?php kocist_product_attr( $product, 'Detay metni' ); ?>><?php echo $body_html; // phpcs:ignore WordPress.Security.EscapingOutput -- wp_kses_post ile temizlendi. ?></div>
				<?php endif; ?>

				<?php if ( $tables || $preview ) : ?>
					<div class="k-pdetail__tables">
						<?php foreach ( $tables as $table ) : ?>
							<?php kocist_render_product_table( $table ); ?>
						<?php endforeach; ?>

						<?php if ( ! $tables ) : ?>
							<?php // Yalnizca panel onizlemesinde: tiklaninca tablo alanlari acilir. ?>
							<div class="k-ptable k-ptable--empty" <?php nwcs_edit_attr( 'product', 'tables', 'items' ); ?>>
								<p class="k-ptable__empty-title">Bu ürüne tablo ekleyin</p>
								<p class="k-ptable__empty-text">Tıklayın, “Satır ekle” ile yeni tablo açın ve Ürün alanına <strong><?php echo esc_html( $product['title'] ); ?></strong> yazın. Bu kutu yalnızca panelde görünür.</p>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
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
						<a class="k-pcard__link" href="<?php echo esc_url( $has_page ? $item['url'] : kocist_link( '#teklif' ) ); ?>">
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
