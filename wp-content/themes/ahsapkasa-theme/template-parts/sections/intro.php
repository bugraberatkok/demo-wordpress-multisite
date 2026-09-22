<?php
/**
 * Ana sayfa giris paragrafi: dar okuma kolonu, Literata.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mx-auto max-w-[76rem] px-6 pt-20 md:pt-28">
	<div class="reading">
		<p class="text-xl leading-[1.55] md:text-2xl md:leading-[1.5]" <?php nwcs_edit_attr( 'home', 'intro', 'lead' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'intro', 'lead' ) ); ?>
		</p>

		<p class="mt-7 text-base text-ink/75" <?php nwcs_edit_attr( 'home', 'intro', 'text' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'intro', 'text' ) ); ?>
		</p>
	</div>
</section>
