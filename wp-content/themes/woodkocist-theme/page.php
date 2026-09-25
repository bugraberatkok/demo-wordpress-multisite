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
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Anasayfa</a></li>
					<li><a href="<?php echo esc_url( wk_page_url( 'politikalar' ) ); ?>">Kurumsal</a></li>
					<li aria-current="page"><?php the_title(); ?></li>
				</ol>
			</nav>
			<h1 class="wk-hero__title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="wk-hero__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<div class="wk-wrap wk-section wk-doc">
		<article class="wk-doc__main wk-prose">
			<?php the_content(); ?>
			<p class="wk-doc__updated">Son güncelleme: <?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?></p>
		</article>
		<?php wk_part( 'doc-nav' ); ?>
	</div>
	<?php
endwhile;

get_footer();
