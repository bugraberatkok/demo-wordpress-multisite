<?php
/**
 * Blog ve Haberler listesi (/haberler-blog/, yazilar sayfasi).
 */

defined( 'ABSPATH' ) || exit;

get_header();

pc_part(
	'page-head',
	array(
		'title' => (string) nwcs_field( 'blog', 'head', 'title' ),
		'lead'  => (string) nwcs_field( 'blog', 'head', 'lead' ),
		'page'  => 'blog',
	)
);
?>

<section class="pc-section">
	<div class="pc-wrap">
		<?php if ( have_posts() ) : ?>
			<div class="pc-posts">
				<?php
				while ( have_posts() ) :
					the_post();
					pc_part( 'post-card' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => 'Önceki', 'next_text' => 'Sonraki' ) ); ?>
		<?php else : ?>
			<p>Henüz yazı yok.</p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
