<?php
/**
 * Ana sayfa: sik sorulanlardan ilk birkaci ve tumune baglanti.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mx-auto grid max-w-[78rem] gap-8 px-5 pt-20 md:px-8 md:pt-28 lg:grid-cols-[0.8fr_1.2fr] lg:gap-14">
	<div>
		<h2 class="text-[2.25rem] md:text-[3rem]" <?php nwcs_edit_attr( 'home', 'faq', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'faq', 'title' ) ); ?>
		</h2>
		<a href="<?php echo esc_url( kr_link( kr_page_path( 'faq', '/sik-sorulan-sorular/' ) ) ); ?>" class="btn btn--md btn--outline mt-6"
			<?php nwcs_edit_attr( 'home', 'faq', 'link_label' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'faq', 'link_label' ) ); ?>
		</a>
	</div>

	<?php get_template_part( 'template-parts/faq-list', null, array( 'limit' => max( 1, (int) nwcs_field( 'home', 'faq', 'count' ) ) ) ); ?>
</section>
