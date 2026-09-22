<?php
/**
 * Ana sayfa girisi: arka planda slayt gosterisi, uzerinde koyu perde,
 * ortada baslik, olcu cizgisi ve dugmeler.
 */

defined( 'ABSPATH' ) || exit;

$slides = array();

foreach ( nwcs_rows( 'home', 'hero', 'slides' ) as $row ) {
	$image = nwcs_image_by_id( $row['image'] ?? 0, 'full' );

	if ( ! empty( $image['url'] ) ) {
		$slides[] = $image;
	}
}
?>
<section class="relative isolate flex min-h-[32rem] items-center justify-center overflow-hidden md:min-h-[40rem]">

	<?php if ( $slides ) : ?>
		<div class="absolute inset-0 -z-20" data-slideshow <?php nwcs_edit_attr( 'home', 'hero', 'slides' ); ?>>
			<?php foreach ( $slides as $index => $slide ) : ?>
				<img src="<?php echo esc_url( $slide['url'] ); ?>"
					alt="<?php echo esc_attr( $slide['alt'] ?: 'Koçist üretiminden bir kare' ); ?>"
					data-slide
					class="absolute inset-0 h-full w-full object-cover transition-opacity duration-[1200ms] ease-out <?php echo 0 === $index ? 'opacity-100' : 'opacity-0'; ?>"
					<?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy"'; ?>
					decoding="async" />
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<span class="absolute inset-0 -z-20 bg-ink" aria-hidden="true"></span>
	<?php endif; ?>

	<?php // Perde notr koyu tonda; yesil yalnizca eylem rengi olarak kalsin. ?>
	<span class="absolute inset-0 -z-10 bg-night/78" aria-hidden="true"></span>
	<span class="absolute inset-0 -z-10 bg-gradient-to-b from-night/55 via-night/20 to-night/75" aria-hidden="true"></span>

	<div class="mx-auto w-full max-w-[54rem] px-6 py-24 text-center">

		<h1 class="font-display text-[2.5rem] font-semibold leading-[1.06] text-bone md:text-5xl lg:text-6xl"
			<?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>>
			<?php echo ahsapkasa_multiline( nwcs_field( 'home', 'hero', 'title' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</h1>

		<div class="measure measure--light measure--draw mx-auto mt-9 max-w-[34rem]">
			<span class="measure__tick" aria-hidden="true"></span>
			<span class="measure__line" aria-hidden="true"></span>
			<span class="measure__label" <?php nwcs_edit_attr( 'home', 'hero', 'measure_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'measure_label' ) ); ?>
			</span>
			<span class="measure__line" aria-hidden="true"></span>
			<span class="measure__tick" aria-hidden="true"></span>
		</div>

		<div class="mt-11 flex flex-wrap items-center justify-center gap-3">
			<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'home', 'hero', 'primary_url' ) ) ); ?>"
				class="rounded-pill bg-forest px-8 py-4 font-display text-base font-semibold text-bone transition-colors duration-200 hover:bg-forest-deep"
				<?php nwcs_edit_attr( 'home', 'hero', 'primary_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'primary_label' ) ); ?>
			</a>

			<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'home', 'hero', 'secondary_url' ) ) ); ?>"
				class="rounded-pill border border-bone/45 px-8 py-4 font-display text-base font-medium text-bone transition-colors duration-200 hover:border-bone hover:bg-bone/12"
				<?php nwcs_edit_attr( 'home', 'hero', 'secondary_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'secondary_label' ) ); ?>
			</a>
		</div>
	</div>

	<?php if ( count( $slides ) > 1 ) : ?>
		<div class="absolute bottom-8 left-1/2 z-10 flex -translate-x-1/2 gap-2.5" data-slideshow-dots role="tablist" aria-label="Fotoğraflar">
			<?php foreach ( $slides as $index => $slide ) : ?>
				<button type="button" role="tab" data-slide-dot
					aria-label="<?php echo esc_attr( sprintf( '%d. fotoğraf', $index + 1 ) ); ?>"
					aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
					class="h-2.5 w-2.5 rounded-full bg-bone/45 transition-all duration-300 hover:bg-bone/80 aria-selected:w-7 aria-selected:bg-bone"></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
