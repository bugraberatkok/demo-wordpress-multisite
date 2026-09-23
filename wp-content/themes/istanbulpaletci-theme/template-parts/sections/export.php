<?php
/**
 * Ana sayfa: ihracat bandi. Gece zemininde kenardan kenara; fotograf sol
 * yariyi doldurur, metin sag yarida.
 */

defined( 'ABSPATH' ) || exit;

$image = nwcs_image( 'home', 'export', 'image', '1536x1536' );
?>
<section class="on-dark mt-24 bg-night text-sheet md:mt-32">
	<div class="grid lg:grid-cols-2">

		<div class="shot min-h-[16rem] bg-night sm:min-h-[22rem] lg:min-h-[34rem]" <?php nwcs_edit_attr( 'home', 'export', 'image' ); ?>>
			<?php echo ip_image_tag( $image, '', 'Sevkiyata hazır ahşap sandık' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>

		<div class="flex items-center px-5 py-14 md:px-12 lg:px-16 lg:py-20">
			<div class="max-w-[32rem]">
				<h2 class="text-[2.5rem] font-semibold md:text-5xl" <?php nwcs_edit_attr( 'home', 'export', 'title' ); ?>>
					<?php echo esc_html( nwcs_field( 'home', 'export', 'title' ) ); ?>
				</h2>

				<p class="mt-5 text-lg leading-relaxed text-sheet/80" <?php nwcs_edit_attr( 'home', 'export', 'text' ); ?>>
					<?php echo esc_html( nwcs_field( 'home', 'export', 'text' ) ); ?>
				</p>

				<a href="<?php echo esc_url( ip_link( nwcs_field( 'home', 'export', 'button_url' ) ) ); ?>"
					class="btn btn--md btn--light mt-9" <?php nwcs_edit_attr( 'home', 'export', 'button_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'home', 'export', 'button_label' ) ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
