<?php
/**
 * Diger sayfalar (manifest disinda panelden eklenen sayfa).
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	pc_part( 'page-head', array( 'title' => get_the_title() ) );
	?>
	<section class="pc-section">
		<div class="pc-wrap pc-narrow pc-prose pc-prose--article">
			<?php the_content(); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
