<?php
/**
 * Sikca sorulan sorular: akordiyon.
 *
 * Her soru bir <button aria-expanded>, cevap ona aria-controls ile bagli.
 * Betik yuklenmezse cevaplar acik kalir (CSS'te kapali hal .is-ready
 * sinifina bagli), boylece icerik hicbir kosulda kaybolmaz.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'product', 'faq', 'items' );

if ( ! $items ) {
	return;
}
?>
<section class="k-faq" data-nwcs-section="faq">
	<div class="k-wrap">
		<h2 class="k-faq__title"><?php echo esc_html( nwcs_field( 'product', 'faq', 'title' ) ); ?></h2>

		<div class="k-faq__list" data-k-faq>
			<?php foreach ( $items as $faq_index => $item ) : ?>
				<?php $faq_id = 'k-faq-' . ( (int) $faq_index + 1 ); ?>
				<div class="k-faq__item" data-k-faq-item>
					<h3 class="k-faq__heading">
						<button
							type="button"
							class="k-faq__question"
							data-k-faq-toggle
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $faq_id ); ?>"
						>
							<span><?php echo esc_html( $item['question'] ?? '' ); ?></span>
							<svg class="k-faq__chevron" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
								<path d="M4 6.5 8 10.5l4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
					</h3>

					<div class="k-faq__panel" id="<?php echo esc_attr( $faq_id ); ?>">
						<div class="k-faq__answer">
							<p><?php echo esc_html( $item['answer'] ?? '' ); ?></p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
