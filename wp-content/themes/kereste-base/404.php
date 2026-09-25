<?php
/**
 * Bulunamayan sayfa.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="mx-auto max-w-[78rem] px-5 pt-16 md:px-8 md:pt-24">
	<span class="stencil block text-[3rem]">404</span>
	<h1 class="mt-4 max-w-[16ch] text-[2.75rem] md:text-[4rem]" <?php nwcs_edit_attr( 'notfound', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'title' ) ); ?></h1>
	<p class="mt-5 max-w-[36rem] text-lg text-muted" <?php nwcs_edit_attr( 'notfound', 'head', 'text' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'text' ) ); ?></p>
	<div class="mt-8 flex flex-wrap gap-3">
		<a href="<?php echo esc_url( kr_link( kr_page_path( 'products', '/urunler/' ) ) ); ?>" class="btn btn--lg btn--ink" <?php nwcs_edit_attr( 'notfound', 'head', 'products_button' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'products_button' ) ); ?></a>
		<a href="<?php echo esc_url( kr_quote_fallback_url() ); ?>" data-kr-quote class="btn btn--lg btn--mark" <?php nwcs_edit_attr( 'notfound', 'head', 'quote_button' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'quote_button' ) ); ?></a>
	</div>
</section>
<?php
get_footer();
