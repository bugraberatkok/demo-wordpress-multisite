<?php
/**
 * Blog yazi karti: kapak gorseli, baslik, ozet. Tarih kartta yok (yazilar
 * eski tarihli); yazi sayfasinda kucuk basilir.
 *
 * Beklenen $args: post (WP_Post), featured (bool). Kartin tamami baslik
 * baglantisiyla tiklanir (baglanti ::after ile karti kaplar).
 */

defined( 'ABSPATH' ) || exit;

$post_item = $args['post'] ?? null;

if ( ! $post_item instanceof WP_Post ) {
	return;
}

$featured = ! empty( $args['featured'] );
$image    = ik_post_image( $post_item, $featured ? 'large' : 'medium_large' );
$heading  = $featured ? 'h2' : 'h3';
?>
<article class="ik-post<?php echo $featured ? ' ik-post--featured' : ''; ?>">
	<div class="ik-post__media">
		<?php echo ik_image_tag( $image, 'ik-post__image', '' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
	</div>

	<div class="ik-post__body">
		<<?php echo esc_html( $heading ); ?> class="ik-post__title">
			<a class="ik-post__link" href="<?php echo esc_url( get_permalink( $post_item ) ); ?>"><?php echo esc_html( get_the_title( $post_item ) ); ?></a>
		</<?php echo esc_html( $heading ); ?>>
		<p class="ik-post__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $post_item ), $featured ? 40 : 22 ) ); ?></p>
		<span class="ik-post__more" aria-hidden="true"><?php echo esc_html( nwcs_field( 'blog', 'post', 'read_label' ) ); ?></span>
	</div>
</article>
