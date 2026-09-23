<?php
/**
 * Ana sayfa: son uc blog yazisi. Yayinlanmis yazi yoksa bolum basilmaz.
 */

defined( 'ABSPATH' ) || exit;

$latest = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $latest->have_posts() ) {
	return;
}
?>
<section class="k-section k-home-blog" id="blog" data-nwcs-section="blog">
	<div class="k-wrap">
		<div class="k-home-blog__head">
			<div class="k-section-head">
				<h2 class="k-section-title" <?php nwcs_edit_attr( 'home', 'blog', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'blog', 'title' ) ); ?></h2>
				<p class="k-section-sub" <?php nwcs_edit_attr( 'home', 'blog', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'home', 'blog', 'subtitle' ) ); ?></p>
			</div>
			<a class="k-home-blog__all" href="<?php echo esc_url( kocist_blog_url() ); ?>" <?php nwcs_edit_attr( 'home', 'blog', 'link_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'blog', 'link_label' ) ); ?>
				<?php nwcs_the_icon( 'arrow', 'k-icon', 16 ); ?>
			</a>
		</div>

		<div class="k-post-grid">
			<?php
			while ( $latest->have_posts() ) :
				$latest->the_post();
				kocist_post_card( 'h3' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
