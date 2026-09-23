<?php
/**
 * Ana sayfa girisi: arka planda slayt gosterisi, uzerinde koyu perde,
 * ortada baslik, olcu cizgisi ve dugmeler. Giris sirali (.rise), arka plan
 * kaydirmaya gore hafifce kayar (data-parallax, assets/motion.js).
 */

defined( 'ABSPATH' ) || exit;

$slides = array();

foreach ( nwcs_rows( 'home', 'hero', 'slides' ) as $slide_index => $row ) {
	$image = ahsapambalaj_image( nwcs_image_by_id( (int) ( $row['image'] ?? 0 ), 'full' ), 'slides', $slide_index );

	if ( ! empty( $image['url'] ) ) {
		$slides[] = $image;
	}
}
?>
<section class="relative isolate flex min-h-[32rem] items-center justify-center overflow-hidden md:min-h-[40rem]">

	<?php if ( $slides ) : ?>
		<div class="parallax absolute inset-0 -z-20" data-slideshow data-parallax <?php nwcs_edit_attr( 'home', 'hero', 'slides' ); ?>>
			<?php foreach ( $slides as $index => $slide ) : ?>
				<img src="<?php echo esc_url( $slide['url'] ); ?>"
					alt="<?php echo esc_attr( $slide['alt'] ?: 'Ahşap ambalaj üretiminden bir kare' ); ?>"
					data-slide
					class="kenburns absolute inset-0 h-full w-full object-cover transition-opacity duration-[1200ms] ease-out <?php echo 0 === $index ? 'opacity-100' : 'opacity-0'; ?>"
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

	<div class="hero-inner mx-auto w-full max-w-[54rem] px-6 py-24 text-center">

		<h1 class="hero-title font-display text-[2.5rem] font-semibold leading-[1.06] text-bone md:text-5xl lg:text-6xl"
			<?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>>
			<?php // Her satir ayri gelir; satirlar arasi 140ms. ?>
			<?php foreach ( preg_split( '/\R/', (string) nwcs_field( 'home', 'hero', 'title' ) ) as $line_index => $line ) : ?>
				<span class="rise block" style="--rise-delay: <?php echo (int) $line_index * 140; ?>ms"><?php echo esc_html( $line ); ?></span>
			<?php endforeach; ?>
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

		<div class="rise mt-11 flex flex-wrap items-center justify-center gap-3" style="--rise-delay: 640ms">
			<a href="<?php echo esc_url( ahsapambalaj_link( nwcs_field( 'home', 'hero', 'primary_url' ) ) ); ?>"
				class="btn btn--lg btn--solid"
				<?php nwcs_edit_attr( 'home', 'hero', 'primary_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'primary_label' ) ); ?>
			</a>

			<a href="<?php echo esc_url( ahsapambalaj_link( nwcs_field( 'home', 'hero', 'secondary_url' ) ) ); ?>"
				class="btn btn--lg btn--light-outline"
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
