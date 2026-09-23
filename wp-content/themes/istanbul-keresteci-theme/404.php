<?php
/**
 * Bulunamayan sayfa: urunlere ve ana sayfaya yol gosterir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/page-head',
	null,
	array(
		'title' => 'Bu adreste bir sayfa yok',
		'text'  => 'Bağlantı eski ya da hatalı olabilir. Aradığınız ürün aşağıdaki listede olabilir.',
	)
);
?>
<section class="ik-section">
	<div class="ik-wrap">
		<?php get_template_part( 'template-parts/product-grid', null, array( 'products' => ik_products() ) ); ?>
	</div>
</section>
<?php
get_footer();
