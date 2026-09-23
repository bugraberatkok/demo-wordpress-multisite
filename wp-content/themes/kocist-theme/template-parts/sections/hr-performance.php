<?php
/**
 * Insan Kaynaklari: performans degerlendirme. Solda baslik, sagda metin.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="k-section" data-nwcs-section="performance">
	<div class="k-wrap k-split">
		<div class="k-split__head">
			<h2 class="k-section-title" <?php nwcs_edit_attr( 'hr', 'performance', 'title' ); ?>><?php echo esc_html( nwcs_field( 'hr', 'performance', 'title' ) ); ?></h2>
		</div>

		<div class="k-prose" <?php nwcs_edit_attr( 'hr', 'performance', 'body' ); ?>>
			<?php echo kocist_paragraphs( nwcs_field( 'hr', 'performance', 'body' ) ); // phpcs:ignore WordPress.Security.EscapingOutput -- kocist_paragraphs kacisli basar. ?>
		</div>
	</div>
</section>
