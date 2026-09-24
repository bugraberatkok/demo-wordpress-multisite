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
		$related_head = sprintf( '%s kategorisinde diğer ürünler', $sub['name'] );
	} else {
		$pool          = kocist_catalog_products_in( $group['slug'] );
		$related_head  = sprintf( '%s grubunda diğer ürünler', $group['name'] );
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
	<?php kocist_the_trail( kocist_catalog_trail( $product['group'] ?? '', $product['sub'] ?? '', $product['title'] ) ); ?>
</div>

<section class="k-product k-product--pool">
	<div class="k-wrap k-product__grid">

		<div class="k-product__gallery" data-k-gallery>
			<div class="k-product__stage">
				<?php echo kocist_image_tag( $main, 'k-product__photo', 'Örnek görsel — ürün' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

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
					<a href="<?php echo esc_url( $category['url'] ); ?>"><?php echo esc_html( $category['name'] ); ?></a>
				</p>
			<?php endif; ?>

			<h1 class="k-product__title"><?php echo esc_html( $product['title'] ); ?></h1>

			<?php if ( $product['short'] ) : ?>
				<p class="k-product__subtitle"><?php echo esc_html( $product['short'] ); ?></p>
			<?php endif; ?>

			<div class="k-product__facts">
				<span class="k-product__price<?php echo $product['has_price'] ? '' : ' is-quote'; ?>"><?php echo esc_html( $product['price_label'] ); ?></span>
				<?php if ( $product['spec'] ) : ?>
					<span class="k-product__spec"><?php echo esc_html( $product['spec'] ); ?></span>
				<?php endif; ?>
			</div>

			<div class="k-product__desc k-product__desc--rich"><?php echo wp_kses_post( wpautop( $product['body'] ) ); ?></div>

			<div class="k-product__actions">
				<a class="k-product__btn k-product__btn--primary" href="<?php echo esc_url( kocist_link( nwcs_field( 'product', 'main', 'cta_url' ) ?: '#teklif' ) ); ?>">
					<?php echo esc_html( nwcs_field( 'product', 'main', 'cta_label' ) ?: 'Teklif Alın' ); ?>
				</a>
				<?php if ( nwcs_field( 'product', 'main', 'secondary_label' ) ) : ?>
					<a class="k-product__btn k-product__btn--ghost" href="<?php echo esc_url( kocist_link( nwcs_field( 'product', 'main', 'secondary_url' ) ) ); ?>">
						<?php echo esc_html( nwcs_field( 'product', 'main', 'secondary_label' ) ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( nwcs_field( 'product', 'main', 'note' ) ) : ?>
				<p class="k-product__note"><?php echo esc_html( nwcs_field( 'product', 'main', 'note' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( $related ) : ?>
	<section class="k-section k-related">
		<div class="k-wrap">
			<div class="k-related__head">
				<h2 class="k-related__title"><?php echo esc_html( $related_head ); ?></h2>
				<a class="k-shelf__all" href="<?php echo esc_url( $related_scope['url'] ); ?>">
					Tümünü gör
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
							<span class="k-pcard__media">
								<?php echo kocist_image_tag( kocist_product_image( $item ), 'k-pcard__img', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
							</span>
							<span class="k-pcard__body">
								<span class="k-pcard__title"><?php echo esc_html( $item['title'] ); ?></span>
								<span class="k-pcard__foot">
									<span class="k-pcard__price<?php echo $item['has_price'] ? '' : ' is-quote'; ?>"><?php echo esc_html( $item['price_label'] ); ?></span>
									<span class="k-pcard__go"><?php echo esc_html( $has_page ? 'İncele' : 'Teklif iste' ); ?></span>
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
