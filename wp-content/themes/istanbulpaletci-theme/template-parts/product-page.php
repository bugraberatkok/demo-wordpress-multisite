<?php
/**
 * Urun sayfasi. Bes urun ayni sablonu kullanir; hangi urunun gosterilecegini
 * adres belirler (page.php -> ip_current_product_key()).
 *
 * Masaustu: solda galeri ve aciklama, sagda baslik, sartname ve teklif.
 * Mobil (DOM sirasi): baslik, galeri, sartname + teklif, aciklama.
 */

defined( 'ABSPATH' ) || exit;

$key      = $args['key'];
$product  = ip_products()[ $key ];
$gallery  = ip_product_gallery( $key );
$specs    = nwcs_rows( $key, 'specs', 'rows' );
$body     = (string) nwcs_field( $key, 'detail', 'body' );
$whatsapp = trim( (string) nwcs_field( 'contact', 'details', 'whatsapp_url' ) );
$phone    = array(
	'label' => (string) nwcs_field( 'contact', 'details', 'phone_label' ),
	'url'   => ip_link( nwcs_field( 'contact', 'details', 'phone_url' ) ),
);
$others   = array_diff_key( ip_products(), array( $key => true ) );
$hub      = ip_manifest()['pages']['products'] ?? array( 'label' => 'Ürünlerimiz', 'path' => '/urunlerimiz/' );
?>
<article>

	<nav class="mx-auto max-w-[80rem] px-5 pt-8 md:px-8" aria-label="Konum">
		<ol class="flex flex-wrap items-center gap-2 text-sm text-steel">
			<li>
				<a href="<?php echo esc_url( ip_link( $hub['path'] ) ); ?>" class="text-steel no-underline hover:text-indigo" <?php nwcs_edit_attr( 'products', 'head', 'title' ); ?>>
					<?php echo esc_html( nwcs_field( 'products', 'head', 'title' ) ); ?>
				</a>
			</li>
			<li aria-hidden="true">/</li>
			<li aria-current="page" class="font-medium text-ink" <?php nwcs_edit_attr( $key, 'card', 'name' ); ?>><?php echo esc_html( $product['name'] ); ?></li>
		</ol>
	</nav>

	<div class="mx-auto grid max-w-[80rem] gap-10 px-5 pt-6 md:px-8 lg:grid-cols-[1.1fr_0.9fr] lg:grid-rows-[auto_1fr_auto] lg:gap-x-16 lg:gap-y-10">

		<header class="lg:col-start-2 lg:row-start-1">
			<h1 class="text-[3rem] font-bold leading-[0.95] md:text-[4rem]" <?php nwcs_edit_attr( $key, 'card', 'name' ); ?>>
				<?php echo esc_html( $product['name'] ); ?>
			</h1>
			<p class="mt-5 text-xl leading-snug text-steel" <?php nwcs_edit_attr( $key, 'detail', 'lead' ); ?>>
				<?php echo esc_html( nwcs_field( $key, 'detail', 'lead' ) ); ?>
			</p>
		</header>

		<?php // Galeri: buyuk gorsel buyutme penceresini acar; kucukler buyugu degistirir. ?>
		<div class="lg:col-start-1 lg:row-span-2 lg:row-start-1" data-gallery="<?php echo esc_attr( wp_json_encode( $gallery ) ); ?>" data-product-gallery>
			<?php if ( $gallery ) : ?>
				<button type="button" data-lightbox-open="0" data-gallery-main
					class="group sheet relative block w-full cursor-zoom-in p-2"
					aria-label="<?php echo esc_attr( sprintf( '%s görselini büyüt', $product['name'] ) ); ?>"
					<?php nwcs_edit_attr( $key, 'detail', 'gallery' ); ?>>
					<span class="shot block aspect-[4/3]">
						<img src="<?php echo esc_url( $gallery[0]['url'] ); ?>" alt="<?php echo esc_attr( $gallery[0]['alt'] ); ?>"
							fetchpriority="high" decoding="async" data-gallery-image />
					</span>

					<span class="absolute bottom-5 right-5 flex h-11 w-11 items-center justify-center rounded-[3px] bg-sheet/95 text-ink ring-1 ring-line transition-colors group-hover:text-indigo" aria-hidden="true">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="7" /><path d="M21 21l-4.3-4.3M11 8v6M8 11h6" />
						</svg>
					</span>
				</button>

				<?php if ( count( $gallery ) > 1 ) : ?>
					<ul class="mt-3 grid grid-cols-4 gap-3 sm:grid-cols-6">
						<?php foreach ( $gallery as $index => $picture ) : ?>
							<li>
								<button type="button" data-gallery-thumb="<?php echo esc_attr( (string) $index ); ?>"
									aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>"
									class="shot block aspect-square w-full border border-line opacity-70 transition hover:opacity-100 aria-pressed:border-indigo aria-pressed:opacity-100 aria-pressed:ring-1 aria-pressed:ring-indigo"
									aria-label="<?php echo esc_attr( sprintf( '%d. görseli göster', $index + 1 ) ); ?>">
									<img src="<?php echo esc_url( $picture['thumb'] ); ?>" alt="" loading="lazy" decoding="async" />
								</button>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			<?php else : ?>
				<div class="sheet p-2">
					<?php echo ip_image_tag( array(), 'aspect-[4/3] w-full', 'Görsel eklenmedi' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="lg:col-start-2 lg:row-start-2">
			<?php if ( $specs ) : ?>
				<section class="sheet px-6 pb-2 pt-5 md:px-7" aria-labelledby="sartname" <?php nwcs_edit_attr( $key, 'specs', 'rows' ); ?>>
					<h2 id="sartname" class="font-body text-lg font-semibold" <?php nwcs_edit_attr( 'products', 'shared', 'specs_title' ); ?>>
						<?php echo esc_html( nwcs_field( 'products', 'shared', 'specs_title' ) ); ?>
					</h2>

					<dl class="spec mt-4">
						<?php foreach ( $specs as $row ) : ?>
							<div class="spec__row">
								<dt class="spec__key"><?php echo esc_html( $row['label'] ?? '' ); ?></dt>
								<dd class="spec__value"><?php echo esc_html( $row['value'] ?? '' ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				</section>
			<?php endif; ?>

			<div class="mt-6 flex flex-wrap gap-3">
				<a href="<?php echo esc_url( ip_quote_url( $product['name'] ) ); ?>" class="btn btn--lg btn--solid max-sm:w-full"
					<?php nwcs_edit_attr( 'products', 'shared', 'quote_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'products', 'shared', 'quote_label' ) ); ?>
				</a>

				<?php if ( $whatsapp ) : ?>
					<a href="<?php echo esc_url( ip_link( $whatsapp ) ); ?>" target="_blank" rel="noopener" class="btn btn--lg btn--whatsapp max-sm:w-full"
						<?php nwcs_edit_attr( 'global', 'header', 'whatsapp_label' ); ?>>
						<?php nwcs_the_icon( 'whatsapp', 'shrink-0', 20 ); ?>
						<?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?>
					</a>
				<?php endif; ?>
			</div>

			<p class="mt-5 text-steel">
				<span <?php nwcs_edit_attr( 'products', 'shared', 'call_note' ); ?>><?php echo esc_html( nwcs_field( 'products', 'shared', 'call_note' ) ); ?></span>
				<a href="<?php echo esc_url( $phone['url'] ); ?>" class="tabular font-semibold" <?php nwcs_edit_attr( 'contact', 'details', 'phone_label' ); ?>><?php echo esc_html( $phone['label'] ); ?></a>
			</p>
		</div>

		<?php if ( '' !== trim( $body ) ) : ?>
			<section class="lg:col-start-1 lg:row-start-3 lg:pt-6" aria-labelledby="urun-hakkinda">
				<h2 id="urun-hakkinda" class="text-[2rem] font-semibold" <?php nwcs_edit_attr( 'products', 'shared', 'body_title' ); ?>>
					<?php echo esc_html( nwcs_field( 'products', 'shared', 'body_title' ) ); ?>
				</h2>
				<div class="reading mt-4 text-lg leading-relaxed text-ink/85" <?php nwcs_edit_attr( $key, 'detail', 'body' ); ?>>
					<?php echo ip_paragraphs( $body ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				</div>
			</section>
		<?php endif; ?>
	</div>

	<?php if ( $others ) : ?>
		<section class="mx-auto max-w-[80rem] px-5 pt-24 md:px-8 md:pt-32" aria-labelledby="diger-urunler">
			<h2 id="diger-urunler" class="text-[2.5rem] font-semibold md:text-5xl" <?php nwcs_edit_attr( 'products', 'shared', 'related_title' ); ?>>
				<?php echo esc_html( nwcs_field( 'products', 'shared', 'related_title' ) ); ?>
			</h2>

			<ul class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
				<?php foreach ( $others as $other ) : ?>
					<li><?php get_template_part( 'template-parts/product-card', null, array( 'product' => $other ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>
</article>

<?php
get_template_part( 'template-parts/lightbox' );
