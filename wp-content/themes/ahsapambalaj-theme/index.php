<?php
/**
 * Genel yedek sablon.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="mx-auto max-w-[76rem] px-6 pt-20 pb-10">
	<h1 class="font-display text-4xl font-semibold" <?php if ( function_exists( 'nwcs_post_attr' ) && is_singular() ) { nwcs_post_attr( (int) get_queried_object_id(), 'Sayfa metni' ); } ?>><?php echo esc_html( get_the_title() ?: get_bloginfo( 'name' ) ); ?></h1>

	<div class="reading mt-8" <?php if ( function_exists( 'nwcs_post_attr' ) && is_singular() ) { nwcs_post_attr( (int) get_queried_object_id(), 'Sayfa metni' ); } ?>>
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
