<?php
/**
 * Blog listesi (/blog/): uclu kart izgarasi.
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/page-head', null, array( 'page' => 'blog' ) );
?>

<section class="ik-section ik-bloglist">
	<div class="ik-wrap">
		<?php if ( ! have_posts() ) : ?>
			<p class="ik-lead" <?php nwcs_edit_attr( 'blog', 'head', 'empty' ); ?>><?php echo esc_html( nwcs_field( 'blog', 'head', 'empty' ) ); ?></p>
		<?php else : ?>
			<div class="ik-posts">
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<?php get_template_part( 'template-parts/post-card', null, array( 'post' => get_post() ) ); ?>
				<?php endwhile; ?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'prev_text' => 'Önceki',
					'next_text' => 'Sonraki',
					'class'     => 'ik-pagination',
				)
			);
			?>
		<?php endif; ?>
	</div>
</section>

<?php
ik_section( 'ctaband' );

get_footer();
