<?php
/**
 * Sik sorulan sorular (/sss/): manifestteki 'faq' sayfasindan.
 *
 * Yalnizca cevabi dolu sorular (nwcs_faq_rows); cevabi bos soru firmadan
 * cevap bekliyor, sitede gorunmez. Gruplar panel sirasiyla, her soru
 * <details> ile acilir (betik yok). Eklenti sayfayi FAQPage olarak isaretler.
 * Soru satirlarinin gorunumu build/istanbulpaletci.css (.faq-*).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$faq_groups = function_exists( 'nwcs_faq_groups' ) ? nwcs_faq_groups( 'faq', (string) nwcs_field( 'faq', 'more', 'group_other' ) ) : array();
$faq_phone  = (string) nwcs_field( 'global', 'header', 'phone_label' );
// Panel onizlemesinde cevaplar acik: tiklaninca cevabin satiri da acilabilsin.
$faq_open = function_exists( 'nwcs_is_preview' ) && nwcs_is_preview();
?>
<article>
	<?php get_template_part( 'template-parts/page-head', null, array( 'page' => 'faq' ) ); ?>

	<div class="mx-auto max-w-[80rem] px-5 pb-24 pt-12 md:px-8 md:pb-32 md:pt-16">
		<?php foreach ( $faq_groups as $faq_group => $faq_rows ) : ?>
			<?php $faq_anchor = 'sss-' . sanitize_title( $faq_group ); ?>
			<section id="<?php echo esc_attr( $faq_anchor ); ?>" class="grid scroll-mt-28 gap-3 border-t-2 border-ink pb-10 pt-6 lg:grid-cols-[1fr_2.4fr] lg:gap-12" aria-labelledby="<?php echo esc_attr( $faq_anchor . '-baslik' ); ?>">
				<h2 id="<?php echo esc_attr( $faq_anchor . '-baslik' ); ?>" class="font-display text-[2rem] font-semibold leading-none md:text-[2.5rem] lg:sticky lg:top-28 lg:self-start lg:pt-4" <?php '' !== $faq_rows[0]['group'] ? nwcs_edit_attr( 'faq', 'items', 'rows', $faq_rows[0]['_row'], 'group' ) : nwcs_edit_attr( 'faq', 'more', 'group_other' ); ?>><?php echo esc_html( $faq_group ); ?></h2>
				<div class="min-w-0">
					<?php foreach ( $faq_rows as $faq_row ) : ?>
						<details class="faq-item"<?php echo $faq_open ? ' open' : ''; ?>>
							<summary class="faq-q">
								<span <?php nwcs_edit_attr( 'faq', 'items', 'rows', $faq_row['_row'], 'question' ); ?>><?php echo esc_html( $faq_row['question'] ); ?></span>
								<span class="faq-icon" aria-hidden="true"></span>
							</summary>
							<div class="faq-a" <?php nwcs_edit_attr( 'faq', 'items', 'rows', $faq_row['_row'], 'answer' ); ?>>
								<?php echo ip_paragraphs( $faq_row['answer'] ); // phpcs:ignore WordPress.Security.EscapingOutput -- ip_paragraphs kacisli. ?>
							</div>
						</details>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>

		<p class="border-t-2 border-ink pt-7 text-lg lg:pl-[calc((100%-3rem)/3.4+3rem)]">
			<span <?php nwcs_edit_attr( 'faq', 'more', 'text' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'more', 'text' ) ); ?></span>
			<?php if ( '' !== trim( $faq_phone ) ) : ?>
				<a class="tabular ml-1 whitespace-nowrap font-semibold underline decoration-2 underline-offset-4" href="<?php echo esc_url( ip_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>><?php echo esc_html( $faq_phone ); ?></a>
			<?php endif; ?>
		</p>
	</div>
</article>
<?php
get_footer();
