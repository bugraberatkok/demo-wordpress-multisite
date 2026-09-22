<?php
/**
 * Gorsel buyutme penceresi. Sayfada bir kez basilir; hangi galerinin
 * gosterilecegini tiklanan dugme belirler (assets/media.js).
 *
 * Buyutulmus gorselde ayrica yakinlastirma var: tekerlek, +/- dugmeleri ve
 * tiklama ile yakinlasir, yakinken surukleyerek gezilir.
 */

defined( 'ABSPATH' ) || exit;
?>
<dialog data-lightbox
	class="m-auto w-[min(92vw,68rem)] rounded-sm bg-transparent p-0 backdrop:bg-night/85 backdrop:backdrop-blur-sm">

	<div class="relative">

		<div data-lightbox-stage
			class="relative mx-auto max-h-[78vh] overflow-hidden rounded-sm bg-night"
			style="cursor: zoom-in; touch-action: none;">
			<img data-lightbox-image src="" alt=""
				class="mx-auto block max-h-[78vh] w-auto max-w-full select-none object-contain"
				style="transform-origin: center center;" draggable="false" />
		</div>

		<form method="dialog" class="absolute right-3 top-3">
			<button type="submit" aria-label="Kapat"
				class="flex h-11 w-11 items-center justify-center rounded-full bg-night/70 text-2xl leading-none text-bone transition-colors hover:bg-night">
				&times;
			</button>
		</form>

		<div data-lightbox-nav class="pointer-events-none absolute inset-y-0 left-0 right-0 flex items-center justify-between px-3">
			<button type="button" data-lightbox-prev aria-label="Önceki görsel"
				class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-night/70 text-bone transition-colors hover:bg-night">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M15 18l-6-6 6-6" />
				</svg>
			</button>

			<button type="button" data-lightbox-next aria-label="Sonraki görsel"
				class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-night/70 text-bone transition-colors hover:bg-night">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M9 18l6-6-6-6" />
				</svg>
			</button>
		</div>

		<?php // Yakinlastirma denetimleri ?>
		<div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 items-center gap-1 rounded-pill bg-night/75 px-2 py-1.5 backdrop-blur">
			<button type="button" data-zoom-out aria-label="Uzaklaştır"
				class="flex h-8 w-8 items-center justify-center rounded-full text-lg leading-none text-bone transition-colors hover:bg-bone/15">−</button>

			<span data-zoom-level class="min-w-[3.5rem] text-center font-display text-xs tabular-nums text-bone/85">%100</span>

			<button type="button" data-zoom-in aria-label="Yakınlaştır"
				class="flex h-8 w-8 items-center justify-center rounded-full text-lg leading-none text-bone transition-colors hover:bg-bone/15">+</button>

			<button type="button" data-zoom-reset
				class="ml-1 rounded-pill px-3 py-1 font-display text-xs text-bone/85 transition-colors hover:bg-bone/15">Sığdır</button>
		</div>
	</div>

	<div class="mt-4 flex items-center justify-between gap-6 px-1">
		<p data-lightbox-caption class="text-sm text-bone/85"></p>
		<p data-lightbox-counter class="shrink-0 font-display text-sm tabular-nums text-bone/70"></p>
	</div>
</dialog>
