<?php
/**
 * Sik sorulan sorular.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<article>
	<?php get_template_part( 'template-parts/page-head', null, array( 'page' => 'faq' ) ); ?>

	<div class="mx-auto max-w-[56rem] px-5 pt-12 md:px-8 md:pt-16">
		<?php get_template_part( 'template-parts/faq-list' ); ?>

		<p class="mt-10 text-lg text-muted">
			<span <?php nwcs_edit_attr( 'faq', 'more', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'more', 'lead' ) ); ?></span>
			<a href="<?php echo esc_url( kr_quote_fallback_url() ); ?>" data-kr-quote class="font-semibold" <?php nwcs_edit_attr( 'faq', 'more', 'link' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'more', 'link' ) ); ?></a>
			<span <?php nwcs_edit_attr( 'faq', 'more', 'or' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'more', 'or' ) ); ?></span>
			<a href="<?php echo esc_url( kr_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>" class="tabular font-semibold" <?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>><?php echo esc_html( nwcs_field( 'global', 'header', 'phone_label' ) ); ?></a>
			<span <?php nwcs_edit_attr( 'faq', 'more', 'call' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'more', 'call' ) ); ?></span>
		</p>
	</div>
</article>
<?php
get_footer();
