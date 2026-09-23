<?php
/**
 * Teklif seridi. Yesil = eylem kurali geregi sayfadaki tek dolu yesil alan.
 * Icerik ortalanir; dugme genis ve ortada durur.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="relative mt-20 overflow-hidden bg-forest text-bone md:mt-28">
	<?php // Ust kenarda yavasca akan cetvel: olcu dilinin seritteki karsiligi. ?>
	<span class="ruler" aria-hidden="true"></span>
	<div class="mx-auto max-w-[52rem] px-6 py-16 text-center md:py-20" data-reveal>

		<h2 class="font-display text-3xl font-semibold leading-[1.12] md:text-4xl" <?php nwcs_edit_attr( 'home', 'ctaband', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'title' ) ); ?>
		</h2>

		<p class="mx-auto mt-5 max-w-[36rem] text-base text-bone/80" <?php nwcs_edit_attr( 'home', 'ctaband', 'text' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'text' ) ); ?>
		</p>

		<a href="<?php echo esc_url( ahsapambalaj_link( nwcs_field( 'home', 'ctaband', 'button_url' ) ) ); ?>"
			class="btn btn--lg btn--light mx-auto mt-10 w-full max-w-[24rem]"
			<?php nwcs_edit_attr( 'home', 'ctaband', 'button_label' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'button_label' ) ); ?>
		</a>
	</div>
</section>
