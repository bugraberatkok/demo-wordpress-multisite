<?php
/**
 * Yedek sablon: sayfa basligi ve editor icerigi.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/page-head', null, array( 'title' => get_the_title(), 'text' => '' ) );
	?>
	<section class="ik-section">
		<div class="ik-wrap">
			<div class="ik-prose ik-prose--page">
				<?php the_content(); ?>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
