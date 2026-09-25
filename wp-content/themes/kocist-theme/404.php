<?php
/**
 * 404 sayfasi.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="k-pagehead">
	<div class="k-wrap">
		<p class="k-pagehead__crumb" <?php nwcs_edit_attr( 'notfound', 'head', 'crumb' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'crumb' ) ); ?></p>
		<h1 class="k-pagehead__title" <?php nwcs_edit_attr( 'notfound', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'title' ) ); ?></h1>
		<p class="k-pagehead__sub" <?php nwcs_edit_attr( 'notfound', 'head', 'text' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'text' ) ); ?></p>
	</div>
</div>

<div class="k-section">
	<div class="k-wrap">
		<a class="k-btn k-btn--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>" <?php nwcs_edit_attr( 'notfound', 'head', 'button' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'button' ) ); ?></a>
	</div>
</div>
<?php
get_footer();
