<?php
/**
 * Bulunamayan sayfa. Metinler Icerik Studyosu'ndan (404 Sayfasi) gelir.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="sp-wrap sp-section">
	<h1 class="sp-page__title" <?php nwcs_edit_attr( 'notfound', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'title' ) ); ?></h1>
	<p <?php nwcs_edit_attr( 'notfound', 'head', 'text' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'text' ) ); ?></p>
	<p><a href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'notfound', 'head', 'link_url' ) ) ); ?>" <?php nwcs_edit_attr( 'notfound', 'head', 'link_label' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'link_label' ) ); ?></a></p>
</section>
<?php
get_footer();
