<?php
/**
 * Gorsel buyutme penceresi. Sayfada bir kez basilir; hangi galerinin
 * gosterilecegini tiklanan dugme belirler (assets/media.js).
 *
 * Buyutulmus gorselde yakinlastirma var: tekerlek, +/- dugmeleri, tiklama
 * ve klavye (+, -, 0); yakinken surukleyerek gezilir.
 */

defined( 'ABSPATH' ) || exit;

$control = 'flex items-center justify-center rounded-[3px] bg-night/80 text-sheet transition-colors hover:bg-indigo';
?>
<dialog data-lightbox
	class="on-dark m-auto w-[min(94vw,72rem)] bg-transparent p-0 text-sheet backdrop:bg-night/90 backdrop:backdrop-blur-sm">

	<div class="relative">
		<div data-lightbox-stage class="relative mx-auto max-h-[80vh] overflow-hidden bg-sheet" style="cursor: zoom-in; touch-action: none;">
			<img data-lightbox-image src="" alt=""
				class="mx-auto block max-h-[80vh] w-auto max-w-full select-none object-contain"
				style="transform-origin: center center;" draggable="false" />
		</div>

		<form method="dialog" class="absolute right-3 top-3">
			<button type="submit" aria-label="Kapat" class="<?php echo esc_attr( $control ); ?> h-11 w-11 text-2xl leading-none">&times;</button>
		</form>

		<div data-lightbox-nav class="pointer-events-none absolute inset-y-0 left-0 right-0 flex items-center justify-between px-3">
			<button type="button" data-lightbox-prev aria-label="Önceki görsel" class="<?php echo esc_attr( $control ); ?> pointer-events-auto h-12 w-12">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6" /></svg>
			</button>
			<button type="button" data-lightbox-next aria-label="Sonraki görsel" class="<?php echo esc_attr( $control ); ?> pointer-events-auto h-12 w-12">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6" /></svg>
			</button>
		</div>

		<div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 items-center gap-1 rounded-[3px] bg-night/85 px-2 py-1.5">
			<button type="button" data-zoom-out aria-label="Uzaklaştır" class="flex h-8 w-8 items-center justify-center rounded-[3px] text-lg leading-none transition-colors hover:bg-sheet/15">−</button>
			<span data-zoom-level class="tabular min-w-[3.5rem] text-center text-sm text-sheet/85">%100</span>
			<button type="button" data-zoom-in aria-label="Yakınlaştır" class="flex h-8 w-8 items-center justify-center rounded-[3px] text-lg leading-none transition-colors hover:bg-sheet/15">+</button>
			<button type="button" data-zoom-reset class="ml-1 rounded-[3px] px-3 py-1 text-sm text-sheet/85 transition-colors hover:bg-sheet/15">Sığdır</button>
		</div>
	</div>

	<div class="mt-4 flex items-center justify-between gap-6 px-1">
		<p data-lightbox-caption class="text-sm text-sheet/85"></p>
		<p data-lightbox-counter class="tabular shrink-0 text-sm text-sheet/70"></p>
	</div>
</dialog>
