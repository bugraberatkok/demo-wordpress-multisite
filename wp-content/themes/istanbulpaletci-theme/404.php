<?php
/**
 * Bulunamayan sayfa: kullaniciyi urunlere ve iletisime yonlendirir.
 * Metinler Icerik Studyosu'ndan (404 Sayfasi) gelir.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="mx-auto max-w-[80rem] px-5 pt-20 md:px-8 md:pt-28">
	<h1 class="max-w-[16ch] text-[3rem] font-bold leading-[0.95] md:text-[4.5rem]" <?php nwcs_edit_attr( 'notfound', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'title' ) ); ?></h1>
	<p class="mt-6 max-w-[36rem] text-xl text-steel" <?php nwcs_edit_attr( 'notfound', 'head', 'text' ); ?>>
		<?php echo esc_html( nwcs_field( 'notfound', 'head', 'text' ) ); ?>
	</p>
	<div class="mt-10 flex flex-wrap gap-3">
		<a href="<?php echo esc_url( ip_link( nwcs_field( 'notfound', 'head', 'primary_url' ) ) ); ?>" class="btn btn--lg btn--solid" <?php nwcs_edit_attr( 'notfound', 'head', 'primary_label' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'primary_label' ) ); ?></a>
		<a href="<?php echo esc_url( ip_link( nwcs_field( 'notfound', 'head', 'secondary_url' ) ) ); ?>" class="btn btn--lg btn--outline" <?php nwcs_edit_attr( 'notfound', 'head', 'secondary_label' ); ?>><?php echo esc_html( nwcs_field( 'notfound', 'head', 'secondary_label' ) ); ?></a>
	</div>
</section>
<?php
get_footer();
