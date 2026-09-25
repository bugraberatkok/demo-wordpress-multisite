<?php
/**
 * Bulunamayan sayfa.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="mx-auto max-w-[76rem] px-6 pt-24 pb-10">
	<p class="font-display text-sm tracking-[0.14em] text-timber" <?php nwcs_edit_attr( 'notfound', 'head', 'code' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'code' ) ); ?></p>
	<h1 class="mt-4 max-w-[18ch] font-display text-4xl font-semibold md:text-5xl" <?php nwcs_edit_attr( 'notfound', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'title' ) ); ?></h1>
	<p class="reading mt-6 text-base text-ink/75" <?php nwcs_edit_attr( 'notfound', 'head', 'text' ); ?>>
		<?php echo esc_html( nwcs_field( 'notfound', 'head', 'text' ) ); ?>
	</p>
	<div class="mt-9 flex flex-wrap gap-3">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--md btn--solid" <?php nwcs_edit_attr( 'notfound', 'head', 'home_label' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'home_label' ) ); ?></a>
		<a href="<?php echo esc_url( home_url( '/iletisim/' ) ); ?>" class="btn btn--md btn--outline" <?php nwcs_edit_attr( 'notfound', 'head', 'cta_label' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'cta_label' ) ); ?></a>
	</div>
</section>
<?php
get_footer();
