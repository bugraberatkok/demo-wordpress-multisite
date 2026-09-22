<?php
/**
 * Ana sayfa girisi: kenardan kenara fotograf, uzerinde koyu perde,
 * ortada baslik, olcu cizgisi ve dugmeler.
 */

defined( 'ABSPATH' ) || exit;

$image = nwcs_image( 'home', 'hero', 'image', 'full' );
?>
<section class="relative isolate flex min-h-[32rem] items-center justify-center overflow-hidden md:min-h-[40rem]">

	<?php if ( ! empty( $image['url'] ) ) : ?>
		<img src="<?php echo esc_url( $image['url'] ); ?>"
			alt="<?php echo esc_attr( $image['alt'] ?: 'Koçist atölyesi' ); ?>"
			class="absolute inset-0 -z-20 h-full w-full object-cover"
			fetchpriority="high" decoding="async" />
	<?php else : ?>
		<span class="absolute inset-0 -z-20 bg-ink" aria-hidden="true"></span>
	<?php endif; ?>

	<?php // Perde notr koyu tonda; yesil yalnizca eylem rengi olarak kalsin. ?>
	<span class="absolute inset-0 -z-10 bg-night/78" aria-hidden="true"></span>
	<span class="absolute inset-0 -z-10 bg-gradient-to-b from-night/55 via-night/20 to-night/75" aria-hidden="true"></span>

	<div class="mx-auto w-full max-w-[54rem] px-6 py-24 text-center" <?php nwcs_edit_attr( 'home', 'hero', 'image' ); ?>>

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
</section>
