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

	pc_part(
		'page-head',
		array(
			'title'  => get_the_title(),
			'crumbs' => array( array( (string) nwcs_field( 'blog', 'head', 'title' ), pc_link( $blog['path'] ?? '/' ) ) ),
		)
	);
	?>

	<article class="pc-section">
		<div class="pc-wrap pc-narrow">
			<time class="pc-post__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
			<figure class="pc-article__figure">
				<?php echo pc_img_tag( $image, 'pc-article__img', true ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				<?php if ( $image['sample'] ) : ?>
					<span class="pc-sample">Örnek görsel</span>
				<?php endif; ?>
			</figure>
			<div class="pc-prose pc-prose--article">
				<?php the_content(); ?>
			</div>

			<aside class="pc-article__cta">
				<p>Palet çivisi siparişi ve fiyat için arayın ya da WhatsApp’tan yazın.</p>
				<div class="pc-hero__actions">
					<a href="<?php echo esc_url( $phone['url'] ); ?>" class="pc-btn pc-btn--navy"><?php echo pc_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><span class="pc-num"><?php echo esc_html( $phone['label'] ); ?></span></a>
					<?php if ( $wa ) : ?>
						<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="pc-btn pc-btn--wa"><?php echo pc_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>WhatsApp</a>
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
				<header class="pc-head"><h2 id="pc-more">Diğer yazılar</h2></header>
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
