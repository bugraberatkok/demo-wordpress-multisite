<?php
/**
 * Teklif seridi. Yesil = eylem kurali geregi sayfadaki tek dolu yesil alan.
 * Icerik ortalanir; dugme genis ve ortada durur.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mt-20 bg-forest text-bone md:mt-28">
	<div class="mx-auto max-w-[52rem] px-6 py-16 text-center md:py-20">

		<h2 class="font-display text-3xl font-semibold leading-[1.12] md:text-4xl" <?php nwcs_edit_attr( 'home', 'ctaband', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'title' ) ); ?>
		</h2>

		<p class="mx-auto mt-5 max-w-[36rem] text-base text-bone/80" <?php nwcs_edit_attr( 'home', 'ctaband', 'text' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'text' ) ); ?>
		</p>

		<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'home', 'ctaband', 'button_url' ) ) ); ?>"
			class="mx-auto mt-10 block w-full max-w-[24rem] rounded-pill bg-bone px-10 py-5 text-center font-display text-lg font-semibold text-forest-deep transition-colors duration-200 hover:bg-timber-soft"
			<?php nwcs_edit_attr( 'home', 'ctaband', 'button_label' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'button_label' ) ); ?>
		</a>
	</div>
</section>
