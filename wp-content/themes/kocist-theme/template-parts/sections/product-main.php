<?php
/**
 * Urun sayfasi ust bolumu: solda galeri, sagda urun bilgileri.
 *
 * Galeri kucuk gorsellerine basildiginda buyuk gorsel degisir
 * (assets/js/product.js). Betik calismazsa kucuk gorseller yine de
 * kendi dosyalarina giden baglanti olarak kalir; icerik erisilebilir olur.
 */

defined( 'ABSPATH' ) || exit;

$main_image = nwcs_image( 'product', 'main', 'image', 'large' );
$gallery    = nwcs_rows( 'product', 'main', 'gallery' );
$features   = nwcs_rows( 'product', 'main', 'features' );
?>
<section class="k-product" data-nwcs-section="main">
	<div class="k-wrap k-product__grid">

		<div class="k-product__gallery" data-k-gallery>
			<div class="k-product__stage">
				<?php echo kocist_image_tag( $main_image, 'k-product__photo', 'Örnek görsel — ürün' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

				<?php if ( count( $gallery ) > 1 ) : ?>
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

					<span class="k-product__counter" data-k-gallery-counter aria-hidden="true">1 / <?php echo (int) count( $gallery ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( count( $gallery ) > 1 ) : ?>
				<div class="k-product__thumbs">
					<?php foreach ( $gallery as $thumb_index => $thumb ) : ?>
						<?php
						$thumb_image = nwcs_image_by_id( (int) ( $thumb['image'] ?? 0 ), 'medium' );
						$full_image  = nwcs_image_by_id( (int) ( $thumb['image'] ?? 0 ), 'large' );

						if ( empty( $thumb_image['url'] ) ) {
							continue;
						}

						$thumb_alt = trim( (string) ( $thumb['alt'] ?? '' ) );
						?>
						<button
							type="button"
							class="k-product__thumb<?php echo 0 === $thumb_index ? ' is-active' : ''; ?>"
							data-k-thumb
							data-full="<?php echo esc_url( $full_image['url'] ); ?>"
							data-alt="<?php echo esc_attr( $thumb_alt ); ?>"
							aria-label="<?php echo esc_attr( $thumb_alt ); ?>"
						>
							<img src="<?php echo esc_url( $thumb_image['url'] ); ?>" alt="" loading="lazy" decoding="async" />
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="k-product__info">
			<p class="k-product__category"><?php echo esc_html( nwcs_field( 'product', 'main', 'category' ) ); ?></p>
			<h1 class="k-product__title"><?php echo esc_html( nwcs_field( 'product', 'main', 'title' ) ); ?></h1>
			<p class="k-product__subtitle"><?php echo esc_html( nwcs_field( 'product', 'main', 'subtitle' ) ); ?></p>

			<p class="k-product__desc"><?php echo esc_html( nwcs_field( 'product', 'main', 'description' ) ); ?></p>

			<?php if ( $features ) : ?>
				<ul class="k-product__features">
					<?php foreach ( $features as $feature ) : ?>
						<li class="k-product__feature">
							<?php nwcs_the_icon( (string) ( $feature['icon'] ?? '' ), 'k-product__feature-icon', 19 ); ?>
							<span><?php echo esc_html( $feature['text'] ?? '' ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="k-product__actions">
				<a class="k-product__btn k-product__btn--primary" href="<?php echo esc_url( kocist_link( nwcs_field( 'product', 'main', 'cta_url' ) ) ); ?>">
					<?php echo esc_html( nwcs_field( 'product', 'main', 'cta_label' ) ); ?>
				</a>
				<a class="k-product__btn k-product__btn--ghost" href="<?php echo esc_url( kocist_link( nwcs_field( 'product', 'main', 'secondary_url' ) ) ); ?>">
					<?php echo esc_html( nwcs_field( 'product', 'main', 'secondary_label' ) ); ?>
				</a>
			</div>

			<p class="k-product__note"><?php echo esc_html( nwcs_field( 'product', 'main', 'note' ) ); ?></p>
		</div>
	</div>
</section>
