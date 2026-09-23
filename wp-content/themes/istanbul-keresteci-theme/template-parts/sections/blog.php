<?php
/**
 * Son uc blog yazisi.
 */

defined( 'ABSPATH' ) || exit;

$recent = get_posts( array( 'numberposts' => 3, 'post_status' => 'publish' ) );

if ( ! $recent ) {
	return;
}
?>
<section class="ik-section ik-blog" aria-labelledby="ik-blog-title">
	<div class="ik-wrap">
		<div class="ik-section__head">
			<h2 class="ik-title" id="ik-blog-title" <?php nwcs_edit_attr( 'home', 'blog', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'blog', 'title' ) ); ?></h2>
			<a class="ik-textlink" href="<?php echo esc_url( ik_link( nwcs_field( 'home', 'blog', 'all_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'blog', 'all_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'blog', 'all_label' ) ); ?>
				<?php ik_icon( 'arrow', 18 ); ?>
			</a>
		</div>

		<div class="ik-posts">
			<?php foreach ( $recent as $post_item ) : ?>
				<?php get_template_part( 'template-parts/post-card', null, array( 'post' => $post_item ) ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
