<?php
/**
 * Sayfa sablonu.
 *
 * Demo kapsamindaki ornek alt sayfa ('kurumsal') manifestteki 'inner'
 * bilesenlerinden cizilir; diger sayfalar normal icerikle gosterilir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$is_demo_inner = is_page( 'kurumsal' );

if ( is_page( 'iletisim' ) ) {
	// Iletisim sayfasi: manifestteki 'contact' bilesenlerinden cizilir.
	kocist_section( 'contact-main' );
} elseif ( is_page( 'urun' ) ) {
	// Ornek urun sayfasi: manifestteki 'product' bilesenlerinden cizilir.
	kocist_section( 'product-main' );
	kocist_section( 'product-specs' );
	kocist_section( 'product-faq' );
} elseif ( $is_demo_inner ) {
	kocist_section( 'page-head' );
	kocist_section( 'story' );
	kocist_section( 'values' );
} else {
	?>
	<div class="k-pagehead">
		<div class="k-wrap">
			<h1 class="k-pagehead__title"><?php the_title(); ?></h1>
		</div>
	</div>
	<div class="k-section">
		<div class="k-wrap">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
	<?php
}

get_footer();
