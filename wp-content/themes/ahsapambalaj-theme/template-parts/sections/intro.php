<?php
/**
 * Ana sayfa giris bolumu: iki sutun. Solda dar baslik ve pas kirmizisi cizgi,
 * sagda one cikan cumle ve metin. Ustte kereste renginde ince ayrac.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mx-auto max-w-[76rem] px-6 pt-20 md:pt-28">

	<div class="grid gap-8 border-t-2 border-timber pt-10 md:grid-cols-[0.9fr_1.1fr] md:gap-16 md:pt-14" data-reveal>

		<div>
			<span class="rule-draw block h-[3px] w-14 bg-signal" aria-hidden="true"></span>
			<h2 class="title-condensed mt-6 max-w-[16ch] font-display text-[2.25rem] leading-[1.02] md:text-5xl" <?php nwcs_edit_attr( 'home', 'intro', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'intro', 'title' ) ); ?>
			</h2>
		</div>

		<div class="md:pt-2">
			<p class="text-lg leading-[1.6] md:text-xl md:leading-[1.55]" <?php nwcs_edit_attr( 'home', 'intro', 'lead' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'intro', 'lead' ) ); ?>
			</p>

			<p class="mt-4 max-w-[40rem] text-[1.0625rem] leading-[1.7] text-ink/70" <?php nwcs_edit_attr( 'home', 'intro', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'intro', 'text' ) ); ?>
			</p>
		</div>
	</div>
</section>
