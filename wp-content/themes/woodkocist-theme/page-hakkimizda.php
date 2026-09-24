<?php
/**
 * Hakkimizda.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="wk-pagehead">
	<div class="wk-wrap">
		<h1 class="wk-hero__title" <?php nwcs_edit_attr( 'about', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'head', 'title' ) ); ?></h1>
		<p class="wk-hero__lead" <?php nwcs_edit_attr( 'about', 'head', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'about', 'head', 'lead' ) ); ?></p>
	</div>
</section>

<section class="wk-section">
	<div class="wk-wrap wk-prose" <?php nwcs_edit_attr( 'about', 'story', 'text' ); ?>>
		<?php echo wk_paragraphs( (string) nwcs_field( 'about', 'story', 'text' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
	</div>
</section>

<?php
get_footer();
