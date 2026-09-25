<?php
/**
 * Bulunamayan sayfa ya da bu sitede gosterilmeyen urun.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="wk-pagehead">
	<div class="wk-wrap">
		<h1 class="wk-hero__title">Bu sayfa bulunamadı</h1>
		<p class="wk-hero__lead">Ürün kaldırılmış ya da adresi değişmiş olabilir. Ürünlere göz atın ya da WhatsApp’tan sorun.</p>
		<p><a class="wk-btn wk-btn--primary wk-btn--lg" href="<?php echo esc_url( home_url( '/#urunler' ) ); ?>">Ürünlere dön</a></p>
	</div>
</section>

<?php
get_footer();
