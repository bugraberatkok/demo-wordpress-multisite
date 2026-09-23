<?php
/**
 * Teklif seridi: parti boyasi renginde bant, teklif penceresini acar.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mx-auto max-w-[78rem] px-5 pt-20 md:px-8 md:pt-28">
	<div class="flex flex-col gap-6 bg-mark px-7 py-10 text-paper md:flex-row md:items-center md:justify-between md:px-12">
		<div class="max-w-[40rem]">
			<h2 class="text-[2rem] md:text-[2.6rem]" <?php nwcs_edit_attr( 'home', 'quote', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'quote', 'title' ) ); ?>
			</h2>
			<p class="mt-3 text-lg text-paper/85" <?php nwcs_edit_attr( 'home', 'quote', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'quote', 'text' ) ); ?>
			</p>
		</div>
		<a href="<?php echo esc_url( kr_quote_fallback_url() ); ?>" data-kr-quote class="btn btn--lg btn--light shrink-0 self-start md:self-auto"
			<?php nwcs_edit_attr( 'home', 'quote', 'button_label' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'quote', 'button_label' ) ); ?>
		</a>
	</div>
</section>
