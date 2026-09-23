<?php
/**
 * Blog yazi karti: kapak gorseli, tarih, baslik, ozet.
 *
 * Beklenen $args: post (WP_Post), featured (bool) — blog listesinin ilk
 * yazisi yatay ve buyuk basilir. Kartin tamami baslik baglantisiyla
 * tiklanir (baglanti ::after ile karti kaplar); icinde baska baglanti yok.
 */

defined( 'ABSPATH' ) || exit;

$post_item = $args['post'] ?? null;

if ( ! $post_item instanceof WP_Post ) {
	return;
}

$featured = ! empty( $args['featured'] );
$image    = sanayi_palet_post_image( $post_item, $featured ? 'large' : 'medium_large' );
$heading  = $featured ? 'h2' : 'h3';
?>
<article class="sp-post<?php echo $featured ? ' sp-post--featured' : ''; ?>">
	<div class="sp-post__media">
		<?php if ( $image ) : ?>
			<img class="sp-post__image" src="<?php echo esc_url( $image['url'] ); ?>" alt="" loading="lazy" decoding="async" />
		<?php else : ?>
			<span class="sp-range__noimage sp-post__noimage"><?php sanayi_palet_icon( 'box', 48 ); ?></span>
		<?php endif; ?>
	</div>

	<div class="sp-post__body">
		<time class="sp-post__date" datetime="<?php echo esc_attr( get_the_date( 'c', $post_item ) ); ?>"><?php echo esc_html( sanayi_palet_date( $post_item ) ); ?></time>
		<<?php echo esc_html( $heading ); ?> class="sp-post__title">
			<a class="sp-post__link" href="<?php echo esc_url( get_permalink( $post_item ) ); ?>"><?php echo esc_html( get_the_title( $post_item ) ); ?></a>
		</<?php echo esc_html( $heading ); ?>>
		<p class="sp-post__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $post_item ), $featured ? 40 : 22 ) ); ?></p>
		<span class="sp-post__more" aria-hidden="true"><?php echo esc_html( nwcs_field( 'blog', 'post', 'read_label' ) ); ?></span>
	</div>
</article>
