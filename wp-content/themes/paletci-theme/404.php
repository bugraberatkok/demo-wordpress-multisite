<?php
/**
 * 404 sayfasi.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="p-pagehead">
	<div class="p-wrap">
		<span class="p-badge">404</span>
		<h1 class="p-pagehead__title">Sayfa bulunamadı</h1>
		<p class="p-pagehead__sub">Aradığınız sayfa taşınmış ya da hiç var olmamış olabilir.</p>
	</div>
</div>

<section class="p-section">
	<div class="p-wrap" style="text-align:center">
		<a class="p-btn p-btn--clay" href="<?php echo esc_url( home_url( '/' ) ); ?>">Anasayfaya dön</a>
	</div>
</section>
<?php
get_footer();
