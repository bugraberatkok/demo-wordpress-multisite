<?php
/**
 * Bulunamayan sayfa: urunlere ve ana sayfaya yol gosterir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Baslik ve aciklama panelde: 404 Sayfasi > Sayfa Basligi.
get_template_part( 'template-parts/page-head', null, array( 'page' => 'notfound' ) );
?>
<section class="ik-section">
	<div class="ik-wrap">
		<?php get_template_part( 'template-parts/product-grid', null, array( 'products' => ik_products() ) ); ?>
	</div>
</section>
<?php
get_footer();
