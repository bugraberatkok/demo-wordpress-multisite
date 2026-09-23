<?php
/**
 * Teklif seridi: koyu bant, solda baslik ve metin, sagda dugme. Dugme
 * sayfanin eylem rengi (pas kirmizisi).
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="relative mt-20 overflow-hidden bg-night text-bone md:mt-28">
	<?php // Ust kenarda yavasca akan cetvel: olcu dilinin seritteki karsiligi. ?>
	<span class="ruler" aria-hidden="true"></span>
	<div class="mx-auto grid max-w-[76rem] items-center gap-8 px-6 py-16 md:grid-cols-[1fr_auto] md:gap-14 md:py-20" data-reveal>
		<div>

		<h2 class="title-condensed max-w-[20ch] font-display text-[2.25rem] leading-[1.04] md:text-5xl" <?php nwcs_edit_attr( 'home', 'ctaband', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'title' ) ); ?>
		</h2>

		<p class="mt-5 max-w-[36rem] text-base text-bone/75" <?php nwcs_edit_attr( 'home', 'ctaband', 'text' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'text' ) ); ?>
		</p>
		</div>

		<a href="<?php echo esc_url( ahsapambalaj_link( nwcs_field( 'home', 'ctaband', 'button_url' ) ) ); ?>"
			class="btn btn--lg btn--solid w-full md:w-auto md:min-w-[16rem]"
			<?php nwcs_edit_attr( 'home', 'ctaband', 'button_label' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'ctaband', 'button_label' ) ); ?>
		</a>
	</div>
</section>
