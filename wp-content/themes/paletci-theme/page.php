<?php
/**
 * Sayfa sablonu. Ornek alt sayfa ('urunlerimiz') manifestteki 'inner'
 * bilesenlerinden cizilir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_page( 'urunlerimiz' ) ) {
	paletci_section( 'page-head' );
	paletci_section( 'intro' );
	paletci_section( 'list' );
	paletci_section( 'inner-cta' );
} else {
	?>
	<div class="p-pagehead">
		<div class="p-wrap">
			<h1 class="p-pagehead__title"><?php the_title(); ?></h1>
		</div>
	</div>
	<div class="p-section">
		<div class="p-wrap">
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
