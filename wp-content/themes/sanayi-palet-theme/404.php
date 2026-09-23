<?php
/**
 * Bulunamayan sayfa.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="sp-wrap sp-section">
	<h1 class="sp-page__title">Aradığınız sayfa burada değil.</h1>
	<p>Bağlantı eski olabilir. Ana sayfadan devam edebilirsiniz.</p>
	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Ana sayfaya dön</a></p>
</section>
<?php
get_footer();
