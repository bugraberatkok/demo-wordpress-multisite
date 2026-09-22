<?php
/**
 * Genel yedek sablon.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="mx-auto max-w-[76rem] px-6 pt-20 pb-10">
	<h1 class="font-display text-4xl font-semibold"><?php echo esc_html( get_the_title() ?: get_bloginfo( 'name' ) ); ?></h1>

	<div class="reading mt-8">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
</section>
<?php
get_footer();
