<?php
/**
 * Ana sayfa giris bolumu: ortalanmis, cerceveli kutu icinde.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mx-auto max-w-[76rem] px-6 pt-20 md:pt-28">

	<div class="card mx-auto max-w-[66rem] px-7 py-9 text-center md:px-16 md:py-11">

		<span class="mx-auto block h-[2px] w-12 bg-timber" aria-hidden="true"></span>

		<h2 class="mx-auto mt-6 max-w-[34ch] font-display text-2xl font-semibold md:text-3xl" <?php nwcs_edit_attr( 'home', 'intro', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'intro', 'title' ) ); ?>
		</h2>

		<p class="mx-auto mt-5 max-w-[52rem] text-lg leading-[1.6] md:text-xl md:leading-[1.55]" <?php nwcs_edit_attr( 'home', 'intro', 'lead' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'intro', 'lead' ) ); ?>
		</p>

		<p class="mx-auto mt-4 max-w-[46rem] text-[1.0625rem] leading-[1.7] text-ink/70" <?php nwcs_edit_attr( 'home', 'intro', 'text' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'intro', 'text' ) ); ?>
		</p>
	</div>
</section>
