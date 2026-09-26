<?php
/**
 * Hizmetlerimiz. Dort bagimsiz kutu; her kutuda solda yazi, sagda dikine
 * uzun bir gorsel ve o urune ait "teklif al" dugmesi.
 *
 * Dugme, iletisim formuna urun adini adres uzerinden tasir; form o urunu
 * kendiliginden secili getirir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$items = nwcs_rows( 'services', 'grid', 'items' );
?>
<article>

	<header class="mx-auto max-w-[76rem] px-6 pt-14 text-center md:pt-20">
		<h1 class="font-display text-[2.5rem] font-semibold leading-[1.06] md:text-5xl" <?php nwcs_edit_attr( 'services', 'head', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'services', 'head', 'title' ) ); ?>
		</h1>

		<p class="mx-auto mt-7 max-w-[42rem] text-xl leading-[1.55] md:text-2xl md:leading-[1.5]" <?php nwcs_edit_attr( 'services', 'head', 'lead' ); ?>>
			<?php echo esc_html( nwcs_field( 'services', 'head', 'lead' ) ); ?>
		</p>
	</header>

	<section class="mx-auto max-w-[80rem] px-6 pt-12 md:pt-16">

		<div class="mx-auto grid max-w-[78rem] gap-7 sm:grid-cols-2 sm:gap-7" data-reveal-stagger <?php nwcs_edit_attr( 'services', 'grid', 'items' ); ?>>
			<?php foreach ( $items as $item_index => $item ) :

				// Kapak + ek gorseller tek galeriye toplanir.
				$gallery = array();

				foreach ( array( 'image', 'image_2', 'image_3', 'image_4' ) as $key_index => $key ) {
					$image_id = (int) ( $item[ $key ] ?? 0 );
					$picture  = ahsapambalaj_image( nwcs_image_by_id( $image_id, 'large' ), 'services', $item_index, $key_index );

					if ( ! empty( $picture['url'] ) ) {
						// Buyutmede tam boy (yakinlastirma netligi); yedek tema gorselinde ayni dosya.
						$full = $image_id ? (string) ( nwcs_image_by_id( $image_id, 'full' )['url'] ?? '' ) : '';

						$gallery[] = array(
							'url'  => $picture['url'],
							'full' => '' !== $full ? $full : $picture['url'],
							'alt'  => $picture['alt'] ?: ( $item['title'] ?? '' ),
						);
					}
				}

				// Form hangi urun icin teklif istendigini adresten okur.
				$quote_url = add_query_arg(
					'urun',
					rawurlencode( (string) ( $item['title'] ?? '' ) ),
					ahsapambalaj_link( nwcs_field( 'services', 'grid', 'cta_url' ) )
				) . '#teklif';
				?>
				<div class="card flex flex-col-reverse overflow-hidden sm:flex-row" data-reveal data-gallery="<?php echo esc_attr( wp_json_encode( $gallery ) ); ?>">

					<div class="flex min-w-0 flex-1 flex-col p-6 md:p-7">
						<h2 class="font-display text-2xl font-semibold md:text-[1.75rem]" <?php nwcs_edit_attr( 'services', 'grid', 'items', $item_index, 'title' ); ?>>
							<?php echo esc_html( $item['title'] ?? '' ); ?>
						</h2>

						<p class="mt-3 text-[0.9375rem] leading-[1.6] text-ink/70" <?php nwcs_edit_attr( 'services', 'grid', 'items', $item_index, 'text' ); ?>>
							<?php echo esc_html( $item['text'] ?? '' ); ?>
						</p>

						<?php if ( count( $gallery ) > 1 ) : ?>
							<div class="mt-5 mb-6 flex items-center gap-2">
								<?php foreach ( $gallery as $position => $picture ) : ?>
									<button type="button" data-lightbox-open="<?php echo esc_attr( $position ); ?>"
										class="h-2 w-2 rounded-full bg-timber/45 transition-all duration-200 hover:scale-125 hover:bg-timber"
										aria-label="<?php echo esc_attr( sprintf( '%d. görseli büyüt', $position + 1 ) ); ?>"></button>
								<?php endforeach; ?>

								<span class="ml-1 font-display text-xs tabular-nums text-moss" <?php nwcs_edit_attr( 'services', 'grid', 'gallery_count' ); ?>>
									<?php echo esc_html( ahsapambalaj_text( 'services', 'grid', 'gallery_count', array( 'sayi' => count( $gallery ) ) ) ); ?>
								</span>
							</div>
						<?php endif; ?>

						<a href="<?php echo esc_url( $quote_url ); ?>"
							class="btn btn--md btn--solid mt-auto self-start"
							<?php nwcs_edit_attr( 'services', 'grid', 'cta_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'services', 'grid', 'cta_label' ) ); ?>
						</a>
					</div>

					<?php // Gorsel kartin sag yarisini doldurur; cerceve isini kartin
					      // kendi kenari gorur. Kucuk cerceveli onizleme pul gibi duruyordu. ?>
					<?php if ( $gallery ) : ?>
						<button type="button" data-lightbox-open="0"
							class="group relative h-56 w-full shrink-0 overflow-hidden bg-dust sm:h-auto sm:w-[50%]"
							<?php nwcs_edit_attr( 'services', 'grid', 'items', $item_index, 'image' ); ?>
							aria-label="<?php echo esc_attr( sprintf( '%s görselini büyüt', $item['title'] ?? '' ) ); ?>">

							<img src="<?php echo esc_url( $gallery[0]['url'] ); ?>"
								alt="<?php echo esc_attr( $gallery[0]['alt'] ); ?>"
								class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.05]"
								loading="lazy" decoding="async" />

							<span class="absolute inset-0 flex items-center justify-center bg-night/0 transition-colors duration-200 group-hover:bg-night/30" aria-hidden="true">
								<span class="flex h-11 w-11 scale-90 items-center justify-center rounded-full bg-surface/95 text-ink opacity-0 transition-all duration-200 group-hover:scale-100 group-hover:opacity-100">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<circle cx="11" cy="11" r="7" /><path d="M21 21l-4.3-4.3M11 8v6M8 11h6" />
									</svg>
								</span>
							</span>
						</button>
					<?php endif; ?>

				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="mx-auto max-w-[76rem] px-6 pt-16 md:pt-24">
		<div class="panel p-7 text-center md:p-10">
			<h2 class="font-display text-xl font-semibold md:text-2xl" <?php nwcs_edit_attr( 'services', 'note', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'services', 'note', 'title' ) ); ?>
			</h2>
			<p class="mx-auto mt-4 max-w-[46rem] text-[1.0625rem] leading-[1.7] text-ink/80" <?php nwcs_edit_attr( 'services', 'note', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'services', 'note', 'text' ) ); ?>
			</p>
		</div>
	</section>
</article>

<?php
get_template_part( 'template-parts/lightbox' );

get_footer();
