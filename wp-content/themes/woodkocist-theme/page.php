<?php
/**
 * Kurumsal ve yasal sayfalar (KVKK, mesafeli satis, surdurulebilirlik...).
 * Metin WordPress duzenleyicisinden; ozet (Excerpt) sayfa basinda ve arama
 * aciklamasinda. Yaninda diger kurumsal sayfalar.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="wk-pagehead wk-pagehead--compact">
		<div class="wk-wrap">
			<nav class="wk-crumbs wk-crumbs--light" aria-label="Konum">
				<ol>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" <?php nwcs_edit_attr( 'global', 'legal', 'crumb_home' ); ?>><?php echo esc_html( nwcs_field( 'global', 'legal', 'crumb_home' ) ); ?></a></li>
					<li><a href="<?php echo esc_url( wk_page_url( 'politikalar' ) ); ?>" <?php nwcs_edit_attr( 'global', 'legal', 'crumb_corporate' ); ?>><?php echo esc_html( nwcs_field( 'global', 'legal', 'crumb_corporate' ) ); ?></a></li>
					<li aria-current="page" <?php wk_post_src( get_the_ID() ); ?>><?php the_title(); ?></li>
				</ol>
			</nav>
			<h1 class="wk-hero__title" <?php wk_post_src( get_the_ID() ); ?>><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="wk-hero__lead" <?php wk_post_src( get_the_ID() ); ?>><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<div class="wk-wrap wk-section wk-doc">
		<article class="wk-doc__main wk-prose" <?php wk_post_src( get_the_ID() ); ?>>
			<?php the_content(); ?>
			<p class="wk-doc__updated" <?php nwcs_edit_attr( 'global', 'legal', 'updated' ); ?>><?php echo esc_html( wk_text( 'global', 'legal', 'updated', array( 'tarih' => get_the_modified_date( 'j F Y' ) ) ) ); ?></p>
		</article>
		<?php wk_part( 'doc-nav' ); ?>
	</div>
	<?php
endwhile;

get_footer();
