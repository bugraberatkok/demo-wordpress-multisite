<?php
/**
 * Blog listesi (/blog/). Ilk yazi buyuk ve yatay, digerleri ucerli izgara.
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/page-head', null, array( 'page' => 'blog' ) );

global $wp_query;
$posts = $wp_query->posts;
?>

<section class="sp-section sp-bloglist">
	<div class="sp-wrap">
		<?php if ( ! $posts ) : ?>
			<p class="sp-lead" <?php nwcs_edit_attr( 'blog', 'post', 'empty_text' ); ?>><?php echo esc_html( nwcs_field( 'blog', 'post', 'empty_text' ) ); ?></p>
		<?php else : ?>
			<?php
			$first = array_shift( $posts );

			if ( ! is_paged() ) {
				get_template_part( 'template-parts/post-card', null, array( 'post' => $first, 'featured' => true ) );
			} else {
				array_unshift( $posts, $first );
			}
			?>

			<?php if ( $posts ) : ?>
				<div class="sp-bloglist__grid">
					<?php foreach ( $posts as $post_item ) : ?>
						<?php get_template_part( 'template-parts/post-card', null, array( 'post' => $post_item ) ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php
			the_posts_pagination(
				array(
					'prev_text' => '<span' . sanayi_palet_edit_attr( 'blog', 'post', 'prev_label' ) . '>' . esc_html( nwcs_field( 'blog', 'post', 'prev_label' ) ) . '</span>',
					'next_text' => '<span' . sanayi_palet_edit_attr( 'blog', 'post', 'next_label' ) . '>' . esc_html( nwcs_field( 'blog', 'post', 'next_label' ) ) . '</span>',
					'class'     => 'sp-pagination',
				)
			);
			?>
		<?php endif; ?>
	</div>
</section>

<?php
sanayi_palet_section( 'ctaband' );

get_footer();
