<?php
/**
 * Blog yazisi: genis kapak, okuma sutunu, yaninda teklif kutusu, altta diger yazilar.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$current = get_post();
	$image   = sanayi_palet_post_image( $current, 'full' );
	$others  = get_posts(
		array(
			'numberposts'  => 2,
			'post__not_in' => array( $current->ID ),
			'post_status'  => 'publish',
		)
	);
	?>

	<article class="sp-article">
		<header class="sp-article__head">
			<div class="sp-wrap">
				<a class="sp-article__back" href="<?php echo esc_url( sanayi_palet_link( '/blog/' ) ); ?>" <?php nwcs_edit_attr( 'blog', 'post', 'back_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'blog', 'post', 'back_label' ) ); ?>
				</a>
				<h1 class="sp-article__title" <?php sanayi_palet_post_attr( $current->ID, 'Yazı başlığı' ); ?>><?php the_title(); ?></h1>
				<time class="sp-post__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" <?php sanayi_palet_post_attr( $current->ID, 'Yayın tarihi' ); ?>><?php echo esc_html( sanayi_palet_date( $current ) ); ?></time>
			</div>
		</header>

		<?php if ( $image ) : ?>
			<div class="sp-wrap">
				<img class="sp-article__cover" <?php sanayi_palet_post_attr( $current->ID, 'Öne çıkan görsel' ); ?> src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
			</div>
		<?php endif; ?>

		<div class="sp-wrap sp-article__grid">
			<div class="sp-article__content" <?php sanayi_palet_post_attr( $current->ID, 'Yazı metni' ); ?>>
				<?php the_content(); ?>
			</div>

			<aside class="sp-checklist sp-article__aside" aria-labelledby="sp-aside-title">
				<h2 class="sp-checklist__title" id="sp-aside-title" <?php nwcs_edit_attr( 'home', 'ctaband', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'title' ) ); ?></h2>
				<p class="sp-article__aside-text" <?php nwcs_edit_attr( 'home', 'ctaband', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'text' ) ); ?></p>
				<div class="sp-checklist__actions">
					<a class="btn btn--solid" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'home', 'ctaband', 'primary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'ctaband', 'primary_label' ); ?>><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'primary_label' ) ); ?></a>
					<a class="btn btn--outline sp-checklist__whatsapp" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'home', 'ctaband', 'secondary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'ctaband', 'secondary_label' ); ?>>
						<?php sanayi_palet_icon( 'whatsapp', 18 ); ?><?php echo esc_html( nwcs_field( 'home', 'ctaband', 'secondary_label' ) ); ?>
					</a>
				</div>
			</aside>
		</div>
	</article>

	<?php if ( $others ) : ?>
		<section class="sp-section sp-more">
			<div class="sp-wrap">
				<h2 class="sp-title" <?php nwcs_edit_attr( 'blog', 'post', 'more_title' ); ?>><?php echo esc_html( nwcs_field( 'blog', 'post', 'more_title' ) ); ?></h2>
				<div class="sp-bloglist__grid sp-more__grid">
					<?php foreach ( $others as $post_item ) : ?>
						<?php get_template_part( 'template-parts/post-card', null, array( 'post' => $post_item ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
