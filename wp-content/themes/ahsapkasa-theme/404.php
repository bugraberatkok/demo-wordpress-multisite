<?php
/**
 * Bulunamayan sayfa.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="mx-auto max-w-[76rem] px-6 pt-24 pb-10">
	<p class="font-display text-sm tracking-[0.14em] text-timber">404</p>
	<h1 class="mt-4 max-w-[18ch] font-display text-4xl font-semibold md:text-5xl">Aradığınız sayfa burada değil.</h1>
	<p class="reading mt-6 text-base text-ink/75">
		Bağlantı eski olabilir. Ürünlerimize hizmetlerimiz sayfasından, teklif için iletişim sayfasından ulaşabilirsiniz.
	</p>
	<div class="mt-9 flex flex-wrap gap-3">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rounded-pill bg-forest px-7 py-3.5 font-display text-base font-semibold text-bone transition-colors hover:bg-forest-deep">Ana sayfaya dön</a>
		<a href="<?php echo esc_url( home_url( '/iletisim/' ) ); ?>" class="rounded-pill border border-ink/20 px-7 py-3.5 font-display text-base font-medium transition-colors hover:border-ink/45 hover:bg-bone-deep">Teklif alın</a>
	</div>
</section>
<?php
get_footer();
