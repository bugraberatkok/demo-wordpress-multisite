<?php
/**
 * Kuzen siteye kopru: "Hafif ve ekonomik mi? -> kavak", "Dayanim mi? ->
 * ithal". Iki site birbirini ziyaretciye dogru urunu bulduran yerde anar.
 */

defined( 'ABSPATH' ) || exit;

$url = trim( (string) nwcs_field( 'home', 'cousin', 'button_url' ) );

if ( '' === $url ) {
	return;
}
?>
<section class="mx-auto max-w-[78rem] px-5 pt-20 md:px-8 md:pt-28">
	<div class="grid gap-6 border-[1.5px] border-dashed border-ink/30 p-7 md:grid-cols-[1fr_auto] md:items-center md:gap-10 md:p-10">
		<div>
			<h2 class="text-[1.9rem] md:text-[2.4rem]" <?php nwcs_edit_attr( 'home', 'cousin', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'cousin', 'title' ) ); ?>
			</h2>
			<p class="mt-3 max-w-[40rem] text-lg text-muted" <?php nwcs_edit_attr( 'home', 'cousin', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'cousin', 'text' ) ); ?>
			</p>
		</div>
		<a href="<?php echo esc_url( kr_link( $url ) ); ?>" class="btn btn--lg btn--ink self-start md:self-auto" rel="noopener"
			<?php nwcs_edit_attr( 'home', 'cousin', 'button_label' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'cousin', 'button_label' ) ); ?>
		</a>
	</div>
</section>
