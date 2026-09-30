<?php
/**
 * Sik sorulan sorular (/sss/): manifestteki 'faq' sayfasindan.
 *
 * Yalnizca cevabi dolu sorular (nwcs_faq_rows): cevabi bos soru firmadan
 * cevap bekliyor, sitede gorunmez. Gruplar panel sirasiyla; her grup kendi
 * capasiyla (#sss-kereste gibi), urun sayfasindaki baglanti oraya gelir.
 * Acilip kapanma <details> ile; betik yok.
 */

defined( 'ABSPATH' ) || exit;

$faq_groups = function_exists( 'nwcs_faq_groups' ) ? nwcs_faq_groups( 'faq', (string) nwcs_field( 'faq', 'more', 'group_other' ) ) : array();
$faq_phone  = (string) nwcs_field( 'global', 'topbar', 'phone_label' );
// Panel onizlemesinde cevaplar acik: tiklaninca cevabin satiri da acilabilsin.
$faq_open   = function_exists( 'nwcs_is_preview' ) && nwcs_is_preview();
?>
<div class="k-sss" data-nwcs-section="items">
	<div class="k-wrap">
		<?php foreach ( $faq_groups as $faq_group => $faq_rows ) : ?>
			<?php $faq_anchor = 'sss-' . sanitize_title( $faq_group ); ?>
			<section class="k-sss__group" id="<?php echo esc_attr( $faq_anchor ); ?>" aria-labelledby="<?php echo esc_attr( $faq_anchor . '-baslik' ); ?>">
				<h2 class="k-sss__title" id="<?php echo esc_attr( $faq_anchor . '-baslik' ); ?>" <?php '' !== $faq_rows[0]['group'] ? nwcs_edit_attr( 'faq', 'items', 'rows', $faq_rows[0]['_row'], 'group' ) : nwcs_edit_attr( 'faq', 'more', 'group_other' ); ?>><?php echo esc_html( $faq_group ); ?></h2>
				<div class="k-sss__list">
					<?php foreach ( $faq_rows as $faq_row ) : ?>
						<details class="k-sss__item"<?php echo $faq_open ? ' open' : ''; ?>>
							<summary class="k-sss__q">
								<span <?php nwcs_edit_attr( 'faq', 'items', 'rows', $faq_row['_row'], 'question' ); ?>><?php echo esc_html( $faq_row['question'] ); ?></span>
								<span class="k-sss__icon" aria-hidden="true"></span>
							</summary>
							<div class="k-sss__a" <?php nwcs_edit_attr( 'faq', 'items', 'rows', $faq_row['_row'], 'answer' ); ?>>
								<?php echo kocist_paragraphs( $faq_row['answer'] ); // phpcs:ignore WordPress.Security.EscapingOutput -- kocist_paragraphs kacisli. ?>
							</div>
						</details>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>

		<p class="k-sss__more">
			<span <?php nwcs_edit_attr( 'faq', 'more', 'text' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'more', 'text' ) ); ?></span>
			<?php if ( '' !== trim( $faq_phone ) ) : ?>
				<a href="<?php echo esc_url( kocist_link( nwcs_field( 'global', 'topbar', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'topbar', 'phone_label' ); ?>><?php echo esc_html( $faq_phone ); ?></a>
			<?php endif; ?>
		</p>
	</div>
</div>
