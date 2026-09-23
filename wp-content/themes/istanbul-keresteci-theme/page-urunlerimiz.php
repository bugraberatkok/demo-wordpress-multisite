<?php
/**
 * Urunlerimiz: butun urunler, kisa aciklamalariyla.
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/page-head', null, array( 'page' => 'products' ) );
?>
<section class="ik-section ik-catalog">
	<div class="ik-wrap">
		<?php get_template_part( 'template-parts/product-grid', null, array( 'products' => ik_products(), 'detailed' => true ) ); ?>
	</div>
</section>
<?php
ik_section( 'ctaband' );

get_footer();
