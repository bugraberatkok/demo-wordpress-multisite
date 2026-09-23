<?php
/**
 * Bulunamayan sayfa: kullaniciyi urunlere ve iletisime yonlendirir.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="mx-auto max-w-[80rem] px-5 pt-20 md:px-8 md:pt-28">
	<h1 class="max-w-[16ch] text-[3rem] font-bold leading-[0.95] md:text-[4.5rem]">Aradığınız sayfa burada değil.</h1>
	<p class="mt-6 max-w-[36rem] text-xl text-steel">
		Bağlantı eskimiş olabilir. Ürünlere ürünlerimiz sayfasından, teklif için iletişim sayfasından ulaşabilirsiniz.
	</p>
	<div class="mt-10 flex flex-wrap gap-3">
		<a href="<?php echo esc_url( home_url( '/urunlerimiz/' ) ); ?>" class="btn btn--lg btn--solid">Ürünlerimiz</a>
		<a href="<?php echo esc_url( home_url( '/iletisim/' ) ); ?>" class="btn btn--lg btn--outline">İletişim</a>
	</div>
</section>
<?php
get_footer();
