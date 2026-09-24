<?php
/**
 * Yedek sablon (diger sayfalar, arsivler).
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="wk-pagehead">
	<div class="wk-wrap">
		<h1 class="wk-hero__title"><?php echo esc_html( is_singular() ? get_the_title() : wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
	</div>
</section>

<section class="wk-section">
	<div class="wk-wrap wk-prose">
		<?php
		while ( have_posts() ) :
			the_post();
			is_singular() ? the_content() : printf( '<p><a href="%s">%s</a></p>', esc_url( get_permalink() ), esc_html( get_the_title() ) );
		endwhile;
		?>
	</div>
</section>

<?php
get_footer();
