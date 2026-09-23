<?php
/**
 * Sayfa. /urunlerimiz/ altindaki alt sayfalar urun detayi olarak cizilir;
 * digerleri basit metin sayfasidir.
 */

defined( 'ABSPATH' ) || exit;

$product = ik_current_product();

if ( $product ) {
	get_header();
	get_template_part( 'template-parts/product-detail', null, array( 'product' => $product ) );
	get_footer();
	return;
}

get_template_part( 'index' );
