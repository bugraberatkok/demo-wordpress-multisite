<?php
/**
 * Bulunamayan sayfa.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="mx-auto max-w-[78rem] px-5 pt-16 md:px-8 md:pt-24">
	<span class="stencil block text-[3rem]">404</span>
	<h1 class="mt-4 max-w-[16ch] text-[2.75rem] md:text-[4rem]">Aradığınız sayfa burada değil.</h1>
	<p class="mt-5 max-w-[36rem] text-lg text-muted">Bağlantı eskimiş olabilir. Kereste çeşitlerine ürünler sayfasından, fiyat için teklif formundan ulaşabilirsiniz.</p>
	<div class="mt-8 flex flex-wrap gap-3">
		<a href="<?php echo esc_url( kr_link( kr_page_path( 'products', '/urunler/' ) ) ); ?>" class="btn btn--lg btn--ink">Ürünler</a>
		<a href="<?php echo esc_url( kr_quote_fallback_url() ); ?>" data-kr-quote class="btn btn--lg btn--mark">Teklif al</a>
	</div>
</section>
<?php
get_footer();
