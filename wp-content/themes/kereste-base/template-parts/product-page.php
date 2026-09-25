<?php
/**
 * Urun sayfasi. Solda galeri (buyutulur), sagda sablon harfli etiket,
 * baslik, teknik bilgiler ve teklif; altta aciklama ve diger urunler.
 */

defined( 'ABSPATH' ) || exit;

$key      = $args['key'];
$product  = kr_products()[ $key ];
$gallery  = kr_product_gallery( $key );
$specs    = nwcs_rows( $key, 'specs', 'rows' );
$body     = (string) nwcs_field( $key, 'detail', 'body' );
$others   = array_diff_key( kr_products(), array( $key => true ) );
$whatsapp = trim( (string) nwcs_field( 'global', 'header', 'whatsapp_url' ) );
?>
<article>
	<div class="bg-stone">
		<div class="mx-auto max-w-[78rem] px-5 pb-14 pt-8 md:px-8 md:pb-16">

			<nav aria-label="Konum">
				<ol class="flex flex-wrap items-center gap-2 text-sm text-muted">
					<li><a href="<?php echo esc_url( kr_link( kr_page_path( 'products', '/urunler/' ) ) ); ?>" class="text-muted no-underline hover:text-mark" <?php nwcs_edit_attr( 'products', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'head', 'title' ) ); ?></a></li>
					<li aria-hidden="true">/</li>
					<li aria-current="page" class="font-semibold text-ink" <?php nwcs_edit_attr( $key, 'card', 'name' ); ?>><?php echo esc_html( $product['name'] ); ?></li>
				</ol>
			</nav>

			<div class="mt-6 grid gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:gap-14">

				<div data-kr-gallery="<?php echo esc_attr( wp_json_encode( $gallery ) ); ?>" <?php nwcs_edit_attr( $key, 'detail', 'gallery' ); ?>>
					<?php if ( $gallery ) : ?>
						<button type="button" data-kr-open="0" data-kr-main class="group shot relative block aspect-[16/9] w-full cursor-zoom-in"
							aria-label="<?php echo esc_attr( sprintf( '%s görselini büyüt', $product['name'] ) ); ?>">
							<img src="<?php echo esc_url( $gallery[0]['url'] ); ?>" alt="<?php echo esc_attr( $gallery[0]['alt'] ); ?>" fetchpriority="high" decoding="async" data-kr-main-image />
							<span class="absolute bottom-3 right-3 bg-ink/80 px-2.5 py-1.5 text-sm font-semibold text-paper" aria-hidden="true" <?php nwcs_edit_attr( 'products', 'shared', 'zoom_label' ); ?>><?php echo esc_html( nwcs_field( 'products', 'shared', 'zoom_label' ) ); ?></span>
						</button>

						<?php if ( count( $gallery ) > 1 ) : ?>
							<ul class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-6">
								<?php foreach ( $gallery as $index => $picture ) : ?>
									<li>
										<button type="button" data-kr-thumb="<?php echo esc_attr( (string) $index ); ?>" aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>"
											class="shot block aspect-[4/3] w-full opacity-65 ring-mark transition hover:opacity-100 aria-pressed:opacity-100 aria-pressed:ring-2"
											aria-label="<?php echo esc_attr( sprintf( '%d. görseli göster', $index + 1 ) ); ?>">
											<img src="<?php echo esc_url( $picture['thumb'] ); ?>" alt="" loading="lazy" decoding="async" />
										</button>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					<?php else : ?>
						<?php echo kr_image_tag( array(), 'aspect-[16/9] w-full', 'Görsel eklenmedi' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					<?php endif; ?>
				</div>

				<div>
					<span class="stencil block text-[1.9rem]" <?php nwcs_edit_attr( $key, 'card', 'mark' ); ?>><?php echo esc_html( $product['mark'] ); ?></span>
					<h1 class="mt-3 text-[2.5rem] md:text-[3.25rem]" <?php nwcs_edit_attr( $key, 'card', 'name' ); ?>><?php echo esc_html( $product['name'] ); ?></h1>
					<p class="mt-4 text-lg leading-relaxed text-muted" <?php nwcs_edit_attr( $key, 'detail', 'lead' ); ?>><?php echo esc_html( nwcs_field( $key, 'detail', 'lead' ) ); ?></p>

					<div class="mt-7 flex flex-wrap gap-3">
						<a href="<?php echo esc_url( kr_quote_fallback_url( $product['name'] ) ); ?>" data-kr-quote data-kr-product-name="<?php echo esc_attr( $product['name'] ); ?>"
							class="btn btn--lg btn--mark max-sm:w-full" <?php nwcs_edit_attr( 'products', 'shared', 'quote_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'products', 'shared', 'quote_label' ) ); ?>
						</a>
						<?php if ( $whatsapp ) : ?>
							<a href="<?php echo esc_url( kr_link( $whatsapp ) ); ?>" target="_blank" rel="noopener" class="btn btn--lg btn--whatsapp max-sm:w-full"
								<?php nwcs_edit_attr( 'global', 'header', 'whatsapp_label' ); ?>>
								<?php nwcs_the_icon( 'whatsapp', 'shrink-0', 20 ); ?>
								<?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="mx-auto grid max-w-[78rem] gap-12 px-5 pt-14 md:px-8 md:pt-20 lg:grid-cols-[1.15fr_0.85fr] lg:gap-14">
		<?php if ( '' !== trim( $body ) ) : ?>
			<div class="reading text-lg leading-relaxed" <?php nwcs_edit_attr( $key, 'detail', 'body' ); ?>>
				<?php echo kr_paragraphs( $body ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</div>
		<?php endif; ?>

		<?php if ( $specs ) : ?>
			<section aria-labelledby="teknik" <?php nwcs_edit_attr( $key, 'specs', 'rows' ); ?>>
				<h2 id="teknik" class="text-[1.6rem]" <?php nwcs_edit_attr( 'products', 'shared', 'specs_title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'shared', 'specs_title' ) ); ?></h2>
				<dl class="mt-4 border-t-2 border-ink">
					<?php foreach ( $specs as $index => $row ) : ?>
						<div class="spec__row">
							<dt class="spec__key" <?php nwcs_edit_attr( $key, 'specs', 'rows', (int) $index, 'label' ); ?>><?php echo esc_html( $row['label'] ?? '' ); ?></dt>
							<dd class="spec__value" <?php nwcs_edit_attr( $key, 'specs', 'rows', (int) $index, 'value' ); ?>><?php echo esc_html( $row['value'] ?? '' ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</section>
		<?php endif; ?>
	</div>

	<?php if ( $others ) : ?>
		<section class="mx-auto max-w-[78rem] px-5 pt-20 md:px-8 md:pt-28" aria-labelledby="diger">
			<h2 id="diger" class="text-[2.25rem] md:text-[2.75rem]" <?php nwcs_edit_attr( 'products', 'shared', 'related_title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'shared', 'related_title' ) ); ?></h2>
			<ul class="mt-14 grid gap-x-5 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
				<?php foreach ( $others as $other ) : ?>
					<li><?php get_template_part( 'template-parts/product-tag', null, array( 'product' => $other, 'hole' => 'var(--color-paper)' ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>
</article>
