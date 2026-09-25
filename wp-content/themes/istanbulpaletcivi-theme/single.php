<?php
/**
 * Blog yazisi: okunakli tek sutun, altta siparis cagrisi ve diger yazilar.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$blog  = pc_manifest()['pages']['blog'] ?? array();
	$image = pc_post_image();
	$phone = pc_phone();
	$wa    = pc_whatsapp();
	$id    = (int) get_the_ID();

	pc_part(
		'page-head',
		array(
			'title'      => get_the_title(),
			'crumbs'     => array( array( (string) nwcs_field( 'blog', 'head', 'title' ), pc_link( $blog['path'] ?? '/' ), array( 'blog', 'head', 'title' ) ) ),
			'title_edit' => array( 'post', $id, 'Yazı başlığı' ),
		)
	);
	?>

	<article class="pc-section">
		<div class="pc-wrap pc-narrow">
			<time class="pc-post__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"<?php pc_post_attr( $id, 'Yayın tarihi' ); ?>><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
			<figure class="pc-article__figure"<?php pc_post_attr( $id, 'Öne çıkan görsel' ); ?>>
				<?php echo pc_img_tag( $image, 'pc-article__img', true ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				<?php if ( $image['sample'] ) : ?>
					<span class="pc-sample"><?php echo esc_html( nwcs_field( 'global', 'common', 'sample_note' ) ); ?></span>
				<?php endif; ?>
			</figure>
			<div class="pc-prose pc-prose--article"<?php pc_post_attr( $id, 'Yazı metni' ); ?>>
				<?php the_content(); ?>
			</div>

			<aside class="pc-article__cta">
				<p<?php nwcs_edit_attr( 'blog', 'post', 'cta' ); ?>><?php echo esc_html( nwcs_field( 'blog', 'post', 'cta' ) ); ?></p>
				<div class="pc-hero__actions">
					<a href="<?php echo esc_url( $phone['url'] ); ?>" class="pc-btn pc-btn--navy"<?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>><?php echo pc_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><span class="pc-num"><?php echo esc_html( $phone['label'] ); ?></span></a>
					<?php if ( $wa ) : ?>
						<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="pc-btn pc-btn--wa"<?php nwcs_edit_attr( 'blog', 'post', 'wa_label' ); ?>><?php echo pc_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><?php echo esc_html( nwcs_field( 'blog', 'post', 'wa_label' ) ); ?></a>
					<?php endif; ?>
				</div>
			</aside>
		</div>
	</article>

	<?php
	$others = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => true,
		)
	);

	if ( $others->have_posts() ) :
		?>
		<section class="pc-section pc-section--steel" aria-labelledby="pc-more">
			<div class="pc-wrap">
				<header class="pc-head"><h2 id="pc-more"<?php nwcs_edit_attr( 'blog', 'post', 'others' ); ?>><?php echo esc_html( nwcs_field( 'blog', 'post', 'others' ) ); ?></h2></header>
				<div class="pc-posts pc-posts--3">
					<?php
					while ( $others->have_posts() ) :
						$others->the_post();
						pc_part( 'post-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
