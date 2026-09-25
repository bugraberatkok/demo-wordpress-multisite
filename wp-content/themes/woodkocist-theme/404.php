<?php
/**
 * Bulunamayan sayfa ya da bu sitede gosterilmeyen urun.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="wk-pagehead">
	<div class="wk-wrap">
		<h1 class="wk-hero__title" <?php nwcs_edit_attr( 'notfound', 'head', 'heading' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'heading' ) ); ?></h1>
		<p class="wk-hero__lead" <?php nwcs_edit_attr( 'notfound', 'head', 'message' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'message' ) ); ?></p>
		<p><a class="wk-btn wk-btn--primary wk-btn--lg" href="<?php echo esc_url( home_url( '/#urunler' ) ); ?>" <?php nwcs_edit_attr( 'notfound', 'head', 'button' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'button' ) ); ?></a></p>
	</div>
</section>

<?php
get_footer();
