<?php
/**
 * Ana sayfa girisi: ferah tipografi bloku, altinda olcu cizgisi ve
 * kenardan kenara fotograf seridi.
 */

defined( 'ABSPATH' ) || exit;

$band = nwcs_image( 'home', 'hero', 'image', 'full' );
?>
<section class="pt-14 md:pt-20">

	<div class="mx-auto max-w-[76rem] px-6">
		<h1 class="max-w-[18ch] font-display text-[2.5rem] font-semibold leading-[1.05] md:text-5xl lg:text-6xl"
			<?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>>
			<?php echo ahsapkasa_multiline( nwcs_field( 'home', 'hero', 'title' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</h1>

		<div class="measure measure--draw mt-9 max-w-[42rem]">
			<span class="measure__tick" aria-hidden="true"></span>
			<span class="measure__line" aria-hidden="true"></span>
			<span class="measure__label" <?php nwcs_edit_attr( 'home', 'hero', 'measure_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'measure_label' ) ); ?>
			</span>
			<span class="measure__line" aria-hidden="true"></span>
			<span class="measure__tick" aria-hidden="true"></span>
		</div>

		<div class="mt-10 flex flex-wrap items-center gap-3">
			<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'home', 'hero', 'primary_url' ) ) ); ?>"
				class="rounded-pill bg-forest px-7 py-3.5 font-display text-base font-semibold text-bone transition-colors duration-200 hover:bg-forest-deep"
				<?php nwcs_edit_attr( 'home', 'hero', 'primary_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'primary_label' ) ); ?>
			</a>

			<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'home', 'hero', 'secondary_url' ) ) ); ?>"
				class="rounded-pill border border-ink/20 px-7 py-3.5 font-display text-base font-medium text-ink transition-colors duration-200 hover:border-ink/45 hover:bg-bone-deep"
				<?php nwcs_edit_attr( 'home', 'hero', 'secondary_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'secondary_label' ) ); ?>
			</a>
		</div>
	</div>

	<figure class="mt-14 md:mt-20">
		<?php echo ahsapkasa_image_tag( $band, 'h-[44vh] max-h-[620px] w-full object-cover md:h-[58vh]', 'Koçist atölyesi' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

		<figcaption class="mx-auto mt-3 max-w-[76rem] px-6 font-display text-xs tracking-[0.06em] text-moss"
			<?php nwcs_edit_attr( 'home', 'hero', 'image_caption' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'hero', 'image_caption' ) ); ?>
		</figcaption>
	</figure>
</section>
