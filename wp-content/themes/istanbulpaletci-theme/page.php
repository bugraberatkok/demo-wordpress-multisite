<?php
/**
 * Sayfa yonlendiricisi.
 *
 * Adresi manifestte 'template' => 'product' isaretli bir sayfaya denk
 * geliyorsa urun sablonu basilir; bes urun sayfasi icin bes ayri dosya
 * tutulmaz. Diger sayfalar sade duzende gosterilir.
 */

defined( 'ABSPATH' ) || exit;

$product_key = ip_current_product_key();

get_header();

if ( $product_key ) {
	get_template_part( 'template-parts/product-page', null, array( 'key' => $product_key ) );
} else {
	?>
	<article class="mx-auto max-w-[80rem] px-5 pt-14 md:px-8 md:pt-20">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<h1 class="text-[3rem] font-bold leading-[0.95] md:text-[4.5rem]" <?php ip_post_attr( (int) get_the_ID(), 'Sayfa metni' ); ?>><?php the_title(); ?></h1>
			<div class="prose-ip mt-8" <?php ip_post_attr( (int) get_the_ID(), 'Sayfa metni' ); ?>><?php the_content(); ?></div>
		<?php endwhile; ?>
	</article>
	<?php
}

get_footer();
