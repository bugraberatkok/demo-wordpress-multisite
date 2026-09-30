<?php
/**
 * Sik sorulan sorular (/sss/): manifestteki 'faq' sayfasindan.
 *
 * Yalnizca cevabi dolu sorular (nwcs_faq_rows); cevabi bos soru firmadan
 * cevap bekliyor, sitede gorunmez. Gruplar panel sirasiyla, her soru
 * <details> ile acilir (betik yok). Eklenti sayfayi FAQPage olarak isaretler.
 * Gorunum assets/site.css (.pc-sss).
 */

defined( 'ABSPATH' ) || exit;

get_header();

pc_part(
	'page-head',
	array(
		'title' => (string) nwcs_field( 'faq', 'head', 'title' ),
		'lead'  => (string) nwcs_field( 'faq', 'head', 'lead' ),
		'page'  => 'faq',
	)
);

$faq_groups = function_exists( 'nwcs_faq_groups' ) ? nwcs_faq_groups( 'faq', (string) nwcs_field( 'faq', 'more', 'group_other' ) ) : array();
$faq_phone  = (string) nwcs_field( 'global', 'header', 'phone_label' );
// Panel onizlemesinde cevaplar acik: tiklaninca cevabin satiri da acilabilsin.
$faq_open = function_exists( 'nwcs_is_preview' ) && nwcs_is_preview();
?>
<section class="pc-section pc-sss">
	<div class="pc-wrap">
		<?php foreach ( $faq_groups as $faq_group => $faq_rows ) : ?>
			<?php $faq_anchor = 'sss-' . sanitize_title( $faq_group ); ?>
			<div class="pc-sss__group" id="<?php echo esc_attr( $faq_anchor ); ?>">
				<h2 class="pc-sss__title" <?php '' !== $faq_rows[0]['group'] ? nwcs_edit_attr( 'faq', 'items', 'rows', $faq_rows[0]['_row'], 'group' ) : nwcs_edit_attr( 'faq', 'more', 'group_other' ); ?>><?php echo esc_html( $faq_group ); ?></h2>
				<div class="pc-sss__list">
					<?php foreach ( $faq_rows as $faq_row ) : ?>
						<details class="pc-sss__item"<?php echo $faq_open ? ' open' : ''; ?>>
							<summary class="pc-sss__q">
								<span <?php nwcs_edit_attr( 'faq', 'items', 'rows', $faq_row['_row'], 'question' ); ?>><?php echo esc_html( $faq_row['question'] ); ?></span>
								<span class="pc-sss__icon" aria-hidden="true"></span>
							</summary>
							<div class="pc-sss__a" <?php nwcs_edit_attr( 'faq', 'items', 'rows', $faq_row['_row'], 'answer' ); ?>>
								<?php echo pc_paragraphs( $faq_row['answer'] ); // phpcs:ignore WordPress.Security.EscapingOutput -- pc_paragraphs kacisli. ?>
							</div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<p class="pc-sss__more">
			<span <?php nwcs_edit_attr( 'faq', 'more', 'text' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'more', 'text' ) ); ?></span>
			<?php if ( '' !== trim( $faq_phone ) ) : ?>
				<a class="pc-num" href="<?php echo esc_url( pc_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>><?php echo esc_html( $faq_phone ); ?></a>
			<?php endif; ?>
		</p>
	</div>
</section>
<?php
get_footer();
