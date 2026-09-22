<?php
/**
 * Hizmetlerimiz. Dort bagimsiz kutu; her kutuda once gorsel, altinda metin.
 * Gorseller tiklaninca buyur ve urune ait diger gorseller arasinda gezilir.
 *
 * Satirlar arasindaki bosluk, ortadaki teklif dugmesini tamamen icine alacak
 * kadar genis; boylece dugme hicbir kutunun uzerine binmez ve dort kutu da
 * ayni yapida kalir (gorseller ayni hizada).
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

		<div class="relative mx-auto max-w-[64rem]">

			<div class="grid gap-7 sm:grid-cols-2 sm:gap-x-8 sm:gap-y-40" <?php nwcs_edit_attr( 'services', 'grid', 'items' ); ?>>
				<?php foreach ( $items as $item ) :

					// Kapak + ek gorseller tek galeriye toplanir.
					$gallery = array();

					foreach ( array( 'image', 'image_2', 'image_3', 'image_4' ) as $key ) {
						$picture = nwcs_image_by_id( $item[ $key ] ?? 0, 'large' );

						if ( ! empty( $picture['url'] ) ) {
							$gallery[] = array(
								'url' => $picture['url'],
								'alt' => $picture['alt'] ?: ( $item['title'] ?? '' ),
							);
						}
					}
					?>
					<div class="card flex flex-col overflow-hidden" data-gallery="<?php echo esc_attr( wp_json_encode( $gallery ) ); ?>">

						<?php if ( $gallery ) : ?>
							<button type="button" data-lightbox-open="0"
								class="group relative block h-48 w-full overflow-hidden md:h-52"
								aria-label="<?php echo esc_attr( sprintf( '%s görselini büyüt', $item['title'] ?? '' ) ); ?>">

								<img src="<?php echo esc_url( $gallery[0]['url'] ); ?>"
									alt="<?php echo esc_attr( $gallery[0]['alt'] ); ?>"
									class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.04]"
									loading="lazy" decoding="async" />

								<span class="absolute inset-0 flex items-center justify-center bg-night/0 transition-colors duration-200 group-hover:bg-night/30" aria-hidden="true">
									<span class="flex h-12 w-12 scale-90 items-center justify-center rounded-full bg-bone/95 text-ink opacity-0 transition-all duration-200 group-hover:scale-100 group-hover:opacity-100">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<circle cx="11" cy="11" r="7" /><path d="M21 21l-4.3-4.3M11 8v6M8 11h6" />
										</svg>
									</span>
								</span>
							</button>
						<?php endif; ?>

						<?php // Kucuk gorseller yerine nokta: cok daha az yer kaplar,
						      // tiklaninca ayni sekilde o gorseli acar. ?>
						<?php if ( count( $gallery ) > 1 ) : ?>
							<div class="flex items-center gap-2 px-5 pt-4 md:px-6">
								<?php foreach ( $gallery as $position => $picture ) : ?>
									<button type="button" data-lightbox-open="<?php echo esc_attr( $position ); ?>"
										class="h-2 w-2 rounded-full bg-timber/45 transition-all duration-200 hover:scale-125 hover:bg-timber"
										aria-label="<?php echo esc_attr( sprintf( '%d. görseli büyüt', $position + 1 ) ); ?>"></button>
								<?php endforeach; ?>

								<span class="ml-1 font-display text-xs tabular-nums text-moss">
									<?php echo esc_html( sprintf( '%d görsel', count( $gallery ) ) ); ?>
								</span>
							</div>
						<?php endif; ?>

						<div class="px-5 pb-6 pt-4 md:px-6">
							<h2 class="font-display text-xl font-semibold md:text-2xl">
								<?php echo esc_html( $item['title'] ?? '' ); ?>
							</h2>

							<p class="mt-2 text-base leading-[1.65] text-ink/75">
								<?php echo esc_html( $item['text'] ?? '' ); ?>
							</p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'services', 'grid', 'center_url' ) ) ); ?>"
				class="quadbtn absolute left-1/2 top-1/2 z-10 hidden h-[8.5rem] w-[8.5rem] -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center rounded-full bg-forest text-center font-display text-base font-semibold leading-tight text-bone hover:bg-forest-deep sm:flex"
				<?php nwcs_edit_attr( 'services', 'grid', 'center_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'services', 'grid', 'center_label' ) ); ?>
				<span class="mt-1 block font-display text-xs font-normal text-bone/75" <?php nwcs_edit_attr( 'services', 'grid', 'center_note' ); ?>>
					<?php echo esc_html( nwcs_field( 'services', 'grid', 'center_note' ) ); ?>
				</span>
			</a>
		</div>

		<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'services', 'grid', 'center_url' ) ) ); ?>"
			class="mt-8 block rounded-pill bg-forest px-7 py-4 text-center font-display text-base font-semibold text-bone transition-colors hover:bg-forest-deep sm:hidden">
			<?php echo esc_html( nwcs_field( 'services', 'grid', 'center_label' ) ); ?>
		</a>
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
