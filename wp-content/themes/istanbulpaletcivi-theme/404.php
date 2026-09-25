<?php
/**
 * Bulunamayan sayfa: ne oldugunu soyle, nereye gidilecegini goster.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$phone = pc_phone();

pc_part(
	'page-head',
	array(
		'title'      => (string) nwcs_field( 'notfound', 'head', 'title' ),
		'title_edit' => array( 'notfound', 'head', 'title' ),
	)
);
?>

<section class="pc-section">
	<div class="pc-wrap pc-narrow">
		<p class="pc-lead"<?php nwcs_edit_attr( 'notfound', 'head', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'lead' ) ); ?></p>
		<div class="pc-hero__actions">
			<a href="<?php echo esc_url( pc_link( pc_manifest()['pages']['products']['path'] ?? '/' ) ); ?>" class="pc-btn pc-btn--navy"<?php nwcs_edit_attr( 'notfound', 'head', 'button' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'button' ) ); ?></a>
			<a href="<?php echo esc_url( $phone['url'] ); ?>" class="pc-btn pc-btn--line"<?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>><span class="pc-num"><?php echo esc_html( $phone['label'] ); ?></span></a>
		</div>
	</div>
</section>

<?php
get_footer();
