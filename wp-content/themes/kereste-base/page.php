<?php
/**
 * Sayfa yonlendiricisi: adres manifestte 'template' => 'product' isaretli
 * bir sayfaya denk geliyorsa urun sablonu; degilse sade duzen.
 */

defined( 'ABSPATH' ) || exit;

$product_key = kr_current_product_key();

get_header();

if ( $product_key ) {
	get_template_part( 'template-parts/product-page', null, array( 'key' => $product_key ) );
} else {
	while ( have_posts() ) :
		the_post();
		?>
		<article class="mx-auto max-w-[78rem] px-5 pt-12 md:px-8 md:pt-16">
			<h1 class="text-[2.75rem] md:text-[4rem]" <?php kr_post_attr( (int) get_the_ID() ); ?>><?php the_title(); ?></h1>
			<div class="reading mt-6 text-lg" <?php kr_post_attr( (int) get_the_ID() ); ?>><?php the_content(); ?></div>
		</article>
		<?php
	endwhile;
}

get_footer();
