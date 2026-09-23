<?php
/**
 * Ana sayfa: blogdan son uc yazi. Yazi yoksa bolum hic basilmaz.
 */

defined( 'ABSPATH' ) || exit;

$posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $posts->have_posts() ) {
	return;
}

$blog_page = (int) get_option( 'page_for_posts' );
?>
<section class="mx-auto max-w-[80rem] px-5 pt-24 md:px-8 md:pt-32">

	<?php
	get_template_part(
		'template-parts/section-head',
		null,
		array(
			'page'      => 'home',
			'component' => 'blog',
			'url'       => $blog_page ? get_permalink( $blog_page ) : '',
		)
	);
	?>

	<ul class="mt-12 grid gap-5 md:grid-cols-3 lg:gap-6">
		<?php
		while ( $posts->have_posts() ) :
			$posts->the_post();
			?>
			<li><?php get_template_part( 'template-parts/post-card' ); ?></li>
		<?php endwhile; ?>
	</ul>
</section>
<?php
wp_reset_postdata();
