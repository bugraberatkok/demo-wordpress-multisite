<?php
/**
 * Sik sorulan sorular (/sss/): manifestteki 'faq' sayfasindan.
 *
 * Yalnizca cevabi dolu sorular (nwcs_faq_rows); cevabi bos soru firmadan
 * cevap bekliyor, sitede gorunmez. Gruplar panel sirasiyla, her soru
 * <details> ile acilir (betik yok). Eklenti sayfayi FAQPage olarak isaretler.
 * Gorunum assets/css/faq.css.
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/page-head', null, array( 'page' => 'faq' ) );

$faq_groups = function_exists( 'nwcs_faq_groups' ) ? nwcs_faq_groups( 'faq', (string) nwcs_field( 'faq', 'more', 'group_other' ) ) : array();
$faq_phone  = (string) nwcs_field( 'global', 'header', 'phone_label' );
// Panel onizlemesinde cevaplar acik: tiklaninca cevabin satiri da acilabilsin.
$faq_open = function_exists( 'nwcs_is_preview' ) && nwcs_is_preview();
?>
<section class="ik-section ik-sss" aria-label="<?php echo esc_attr( nwcs_field( 'faq', 'head', 'title' ) ); ?>">
	<div class="ik-wrap">
		<?php foreach ( $faq_groups as $faq_group => $faq_rows ) : ?>
			<?php $faq_anchor = 'sss-' . sanitize_title( $faq_group ); ?>
			<div class="ik-sss__group" id="<?php echo esc_attr( $faq_anchor ); ?>">
				<h2 class="ik-sss__title" <?php '' !== $faq_rows[0]['group'] ? nwcs_edit_attr( 'faq', 'items', 'rows', $faq_rows[0]['_row'], 'group' ) : nwcs_edit_attr( 'faq', 'more', 'group_other' ); ?>><?php echo esc_html( $faq_group ); ?></h2>
				<div class="ik-sss__list">
					<?php foreach ( $faq_rows as $faq_row ) : ?>
						<details class="ik-sss__item"<?php echo $faq_open ? ' open' : ''; ?>>
							<summary class="ik-sss__q">
								<span <?php nwcs_edit_attr( 'faq', 'items', 'rows', $faq_row['_row'], 'question' ); ?>><?php echo esc_html( $faq_row['question'] ); ?></span>
								<span class="ik-sss__icon" aria-hidden="true"></span>
							</summary>
							<div class="ik-sss__a" <?php nwcs_edit_attr( 'faq', 'items', 'rows', $faq_row['_row'], 'answer' ); ?>>
								<?php ik_paragraphs( $faq_row['answer'] ); ?>
							</div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<p class="ik-sss__more">
			<span <?php nwcs_edit_attr( 'faq', 'more', 'text' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'more', 'text' ) ); ?></span>
			<?php if ( '' !== trim( $faq_phone ) ) : ?>
				<a href="<?php echo esc_url( ik_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>><?php echo esc_html( $faq_phone ); ?></a>
			<?php endif; ?>
		</p>
	</div>
</section>
<?php
get_footer();
