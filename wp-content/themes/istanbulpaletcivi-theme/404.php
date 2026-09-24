<?php
/**
 * Bulunamayan sayfa: ne oldugunu soyle, nereye gidilecegini goster.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$phone = pc_phone();

pc_part( 'page-head', array( 'title' => 'Bu sayfa bulunamadı' ) );
?>

<section class="pc-section">
	<div class="pc-wrap pc-narrow">
		<p class="pc-lead">Aradığınız adres taşınmış ya da kaldırılmış olabilir. Çivi tiplerine göz atın ya da bizi arayın.</p>
		<div class="pc-hero__actions">
			<a href="<?php echo esc_url( pc_link( pc_manifest()['pages']['products']['path'] ?? '/' ) ); ?>" class="pc-btn pc-btn--navy">Palet çivileri</a>
			<a href="<?php echo esc_url( $phone['url'] ); ?>" class="pc-btn pc-btn--line"><span class="pc-num"><?php echo esc_html( $phone['label'] ); ?></span></a>
		</div>
	</div>
</section>

<?php
get_footer();
