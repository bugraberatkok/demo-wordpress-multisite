<?php
/**
 * 404 sayfasi.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="k-pagehead">
	<div class="k-wrap">
		<p class="k-pagehead__crumb">404</p>
		<h1 class="k-pagehead__title">Sayfa bulunamadı</h1>
		<p class="k-pagehead__sub">Aradığınız sayfa taşınmış ya da hiç var olmamış olabilir.</p>
	</div>
</div>

<div class="k-section">
	<div class="k-wrap">
		<a class="k-btn k-btn--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>">Ana sayfaya dön</a>
	</div>
</div>
<?php
get_footer();
