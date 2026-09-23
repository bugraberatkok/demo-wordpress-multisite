<?php
/**
 * Blogdan son uc yazi. Yazilar WordPress'in kendi yazi kayitlarindan gelir;
 * panelde yalnizca baslik ve baglanti metni duzenlenir.
 */

defined( 'ABSPATH' ) || exit;

$posts = get_posts(
	array(
		'numberposts' => 3,
		'post_status' => 'publish',
	)
);

if ( ! $posts ) {
	return;
}
?>
<section class="sp-section sp-blog" data-nwcs-section="blog">
	<div class="sp-wrap">
		<header class="sp-blog__head">
			<h2 class="sp-title" <?php nwcs_edit_attr( 'home', 'blog', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'blog', 'title' ) ); ?></h2>
			<a class="sp-link" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'home', 'blog', 'link_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'blog', 'link_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'blog', 'link_label' ) ); ?>
			</a>
		</header>

		<div class="sp-bloglist__grid">
			<?php foreach ( $posts as $post_item ) : ?>
				<?php get_template_part( 'template-parts/post-card', null, array( 'post' => $post_item ) ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
