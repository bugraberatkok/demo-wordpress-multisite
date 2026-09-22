<?php
/**
 * Ana sayfa giris bolumu: basligi olan, ortalanmis kisa metin.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mx-auto max-w-[76rem] px-6 pt-20 text-center md:pt-28">

	<h2 class="mx-auto max-w-[28ch] font-display text-3xl font-semibold md:text-4xl" <?php nwcs_edit_attr( 'home', 'intro', 'title' ); ?>>
		<?php echo esc_html( nwcs_field( 'home', 'intro', 'title' ) ); ?>
	</h2>

	<p class="mx-auto mt-7 max-w-[44rem] text-xl leading-[1.55] md:text-2xl md:leading-[1.5]" <?php nwcs_edit_attr( 'home', 'intro', 'lead' ); ?>>
		<?php echo esc_html( nwcs_field( 'home', 'intro', 'lead' ) ); ?>
	</p>

	<p class="mx-auto mt-6 max-w-[36rem] text-base text-ink/75" <?php nwcs_edit_attr( 'home', 'intro', 'text' ); ?>>
		<?php echo esc_html( nwcs_field( 'home', 'intro', 'text' ) ); ?>
	</p>
</section>
