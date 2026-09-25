<?php
/**
 * Sik sorulan sorular listesi (acilir kapanir). SSS sayfasi ve ana sayfa
 * ayni parcayi kullanir.
 *
 * $args['limit']: kac soru (0 = hepsi).
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'faq', 'items', 'rows' );
$limit = (int) ( $args['limit'] ?? 0 );

if ( $limit > 0 ) {
	$items = array_slice( $items, 0, $limit );
}
?>
<div class="border-t-2 border-ink" <?php nwcs_edit_attr( 'faq', 'items', 'rows' ); ?>>
	<?php foreach ( $items as $index => $item ) : ?>
		<details class="faq border-b border-line">
			<summary class="flex items-center justify-between gap-6 py-5">
				<span class="font-slab text-[1.3rem] font-bold leading-snug" <?php nwcs_edit_attr( 'faq', 'items', 'rows', (int) $index, 'question' ); ?>><?php echo esc_html( $item['question'] ?? '' ); ?></span>
				<span class="faq__sign stencil shrink-0 text-2xl" aria-hidden="true">+</span>
			</summary>
			<div class="reading pb-6 text-[1.0625rem] leading-relaxed text-muted" <?php nwcs_edit_attr( 'faq', 'items', 'rows', (int) $index, 'answer' ); ?>>
				<?php echo kr_paragraphs( (string) ( $item['answer'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</div>
		</details>
	<?php endforeach; ?>
</div>
