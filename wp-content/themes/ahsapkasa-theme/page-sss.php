<?php
/**
 * Sik sorulan sorular (/sss/): manifestteki 'faq' sayfasindan.
 *
 * Yalnizca cevabi dolu sorular (nwcs_faq_rows); cevabi bos soru firmadan
 * cevap bekliyor, sitede gorunmez. Hic cevapli soru yoksa sayfa yalnizca
 * baslik ve telefon satiriyla acilir; menudeki baglanti da gizlenir
 * (ahsapkasa_menu). Gruplar panel sirasiyla, her soru <details> ile acilir
 * (betik yok). Eklenti sayfayi FAQPage olarak isaretler (cevapli soru varsa).
 * Soru satirlarinin gorunumu build/ahsapkasa.css (.faq-*).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$faq_groups = function_exists( 'nwcs_faq_groups' ) ? nwcs_faq_groups( 'faq', (string) nwcs_field( 'faq', 'more', 'group_other' ) ) : array();
$faq_phone  = (string) nwcs_field( 'global', 'footer', 'phone_label' );
// Panel onizlemesinde cevaplar acik: tiklaninca cevabin satiri da acilabilsin.
$faq_open = ahsapkasa_is_preview();
?>
<article>

	<header class="mx-auto max-w-[52rem] px-6 pt-14 md:pt-20">
		<h1 class="font-display text-[2.5rem] font-semibold leading-[1.06] md:text-5xl" <?php nwcs_edit_attr( 'faq', 'head', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'faq', 'head', 'title' ) ); ?>
		</h1>
		<p class="mt-5 max-w-[40rem] text-lg leading-[1.6] text-ink/75 md:text-xl" <?php nwcs_edit_attr( 'faq', 'head', 'lead' ); ?>>
			<?php echo esc_html( nwcs_field( 'faq', 'head', 'lead' ) ); ?>
		</p>
	</header>

	<div class="mx-auto max-w-[52rem] px-6 pb-24 pt-12 md:pb-32 md:pt-16">
		<?php foreach ( $faq_groups as $faq_group => $faq_rows ) : ?>
			<?php $faq_anchor = 'sss-' . sanitize_title( $faq_group ); ?>
			<section id="<?php echo esc_attr( $faq_anchor ); ?>" class="faq-group scroll-mt-28" aria-labelledby="<?php echo esc_attr( $faq_anchor . '-baslik' ); ?>">
				<h2 id="<?php echo esc_attr( $faq_anchor . '-baslik' ); ?>" class="faq-title font-display text-2xl font-semibold md:text-[1.75rem]" <?php '' !== $faq_rows[0]['group'] ? nwcs_edit_attr( 'faq', 'items', 'rows', $faq_rows[0]['_row'], 'group' ) : nwcs_edit_attr( 'faq', 'more', 'group_other' ); ?>><?php echo esc_html( $faq_group ); ?></h2>
				<?php foreach ( $faq_rows as $faq_row ) : ?>
					<details class="faq-item"<?php echo $faq_open ? ' open' : ''; ?>>
						<summary class="faq-q">
							<span <?php nwcs_edit_attr( 'faq', 'items', 'rows', $faq_row['_row'], 'question' ); ?>><?php echo esc_html( $faq_row['question'] ); ?></span>
							<span class="faq-icon" aria-hidden="true"></span>
						</summary>
						<div class="faq-a" <?php nwcs_edit_attr( 'faq', 'items', 'rows', $faq_row['_row'], 'answer' ); ?>>
							<?php echo ahsapkasa_paragraphs( $faq_row['answer'] ); // phpcs:ignore WordPress.Security.EscapingOutput -- kacisli. ?>
						</div>
					</details>
				<?php endforeach; ?>
			</section>
		<?php endforeach; ?>

		<p class="faq-more text-lg">
			<span <?php nwcs_edit_attr( 'faq', 'more', 'text' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'more', 'text' ) ); ?></span>
			<?php if ( '' !== trim( $faq_phone ) ) : ?>
				<a class="ml-1 whitespace-nowrap font-semibold" href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'global', 'footer', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'phone_label' ); ?>><?php echo esc_html( $faq_phone ); ?></a>
			<?php endif; ?>
		</p>
	</div>
</article>
<?php
get_footer();
