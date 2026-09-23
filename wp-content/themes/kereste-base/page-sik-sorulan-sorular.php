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
			Sorunuz burada yok mu?
			<a href="<?php echo esc_url( kr_quote_fallback_url() ); ?>" data-kr-quote class="font-semibold">Bize yazın</a>
			ya da
			<a href="<?php echo esc_url( kr_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>" class="tabular font-semibold"><?php echo esc_html( nwcs_field( 'global', 'header', 'phone_label' ) ); ?></a>
			numarasını arayın.
		</p>
	</div>
</article>
<?php
get_footer();
