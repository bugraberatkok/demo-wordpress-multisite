<?php
/**
 * Yazi karti (ana sayfa ve blog listesi). Dongu icinde cagrilir.
 *
 * Panel isareti yalnizca baslikta: onizlemede baslik yazinin panel sayfasini
 * acar, kapak ve metin de orada. Gorsel yaziya giden baglantidir.
 */

defined( 'ABSPATH' ) || exit;

$image   = pc_post_image();
$post_id = (int) get_the_ID();
?>
<article class="pc-post">
	<a href="<?php the_permalink(); ?>" class="pc-post__media" tabindex="-1" aria-hidden="true">
		<?php echo pc_img_tag( $image, 'pc-post__img' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		<?php if ( $image['sample'] ) : ?>
			<span class="pc-sample"><?php echo esc_html( nwcs_field( 'global', 'common', 'sample_note' ) ); ?></span>
		<?php endif; ?>
	</a>
	<time class="pc-post__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
	<h3 class="pc-post__title"<?php pc_post_edit_attr( $post_id, 'title' ); ?>><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
</article>
