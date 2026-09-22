<?php
/**
 * Teklif seridi. Yesil = eylem kurali geregi sayfadaki tek dolu yesil alan.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mt-20 bg-forest text-bone md:mt-28">
	<div class="mx-auto flex max-w-[76rem] flex-col gap-8 px-6 py-16 md:flex-row md:items-end md:justify-between md:py-20">

		<div class="max-w-[34rem]">
			<h2 class="font-display text-3xl font-semibold leading-[1.12] md:text-4xl" <?php nwcs_edit_attr( 'home', 'ctaband', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'title' ) ); ?>
			</h2>
			<p class="mt-5 text-base text-bone/80" <?php nwcs_edit_attr( 'home', 'ctaband', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'text' ) ); ?>
			</p>
		</div>

		<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'home', 'ctaband', 'button_url' ) ) ); ?>"
			class="shrink-0 self-start rounded-pill bg-bone px-8 py-4 font-display text-base font-semibold text-forest-deep transition-colors duration-200 hover:bg-timber-soft md:self-auto"
			<?php nwcs_edit_attr( 'home', 'ctaband', 'button_label' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'button_label' ) ); ?>
		</a>
	</div>
</section>
