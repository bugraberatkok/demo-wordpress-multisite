<?php
/**
 * Ana sayfa: teklif bolumu. Solda kisa metin ve hizli iletisim yollari,
 * sagda Iletisim sayfasindakiyle ayni form.
 */

defined( 'ABSPATH' ) || exit;
?>
<section id="teklif" class="mx-auto max-w-[80rem] scroll-mt-24 px-5 pt-24 md:px-8 md:pt-32">
	<div class="grid items-start gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-16">

		<div class="lg:sticky lg:top-28">
			<h2 class="text-[2.5rem] font-semibold md:text-5xl" <?php nwcs_edit_attr( 'home', 'quote', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'quote', 'title' ) ); ?>
			</h2>

			<p class="mt-4 max-w-[30rem] text-lg leading-relaxed text-steel" <?php nwcs_edit_attr( 'home', 'quote', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'quote', 'text' ) ); ?>
			</p>

			<?php get_template_part( 'template-parts/contact-lines' ); ?>
		</div>

		<div class="sheet p-6 md:p-9">
			<?php get_template_part( 'template-parts/quote-form' ); ?>
		</div>
	</div>
</section>
