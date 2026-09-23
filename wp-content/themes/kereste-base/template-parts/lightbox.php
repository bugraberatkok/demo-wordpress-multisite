<?php
/**
 * Gorsel buyutme penceresi (urun galerileri). Sayfada bir kez; hangi
 * galerinin gosterilecegini tiklanan dugme belirler (site.js).
 *
 * Pencere icinde ikinci kademe yakinlastirma (assets/zoom.js): gorsele
 * tiklayinca yaklasir, tekerlek / iki parmakla 5 kata kadar, surukleyerek
 * gezilir; altta - % + ve "Sigdir" dugmeleri.
 */

defined( 'ABSPATH' ) || exit;

$control = 'flex items-center justify-center bg-ink/80 text-paper transition-colors hover:bg-mark disabled:opacity-40 disabled:hover:bg-ink/80';
?>
<dialog data-kr-lightbox class="kr-dialog m-auto w-fit max-w-[94vw] bg-transparent p-0">
	<div class="relative mx-auto w-fit max-w-full bg-paper">
		<div data-kr-zoom-stage class="relative mx-auto flex max-h-[82vh] w-fit max-w-full items-center justify-center overflow-hidden">
			<img data-kr-lightbox-image src="" alt="" class="block max-h-[82vh] w-auto max-w-full select-none object-contain" />
		</div>

		<form method="dialog" class="absolute right-3 top-3">
			<button type="submit" aria-label="Kapat" class="<?php echo esc_attr( $control ); ?> h-11 w-11 text-2xl leading-none">&times;</button>
		</form>

		<div data-kr-lightbox-nav class="pointer-events-none absolute inset-y-0 left-0 right-0 flex items-center justify-between px-3">
			<button type="button" data-kr-lightbox-prev aria-label="Önceki görsel" class="<?php echo esc_attr( $control ); ?> pointer-events-auto h-12 w-12">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6" /></svg>
			</button>
			<button type="button" data-kr-lightbox-next aria-label="Sonraki görsel" class="<?php echo esc_attr( $control ); ?> pointer-events-auto h-12 w-12">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 18l6-6-6-6" /></svg>
			</button>
		</div>

		<div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 items-center gap-1" role="group" aria-label="Yakınlaştırma">
			<button type="button" data-kr-zoom-out aria-label="Uzaklaştır" class="<?php echo esc_attr( $control ); ?> h-10 w-10 text-xl leading-none">&minus;</button>
			<span data-kr-zoom-level class="tabular flex h-10 min-w-[3.75rem] items-center justify-center bg-ink/80 px-2 text-sm font-semibold text-paper" aria-live="polite">%100</span>
			<button type="button" data-kr-zoom-in aria-label="Yakınlaştır" class="<?php echo esc_attr( $control ); ?> h-10 w-10 text-xl leading-none">+</button>
			<button type="button" data-kr-zoom-reset class="<?php echo esc_attr( $control ); ?> h-10 px-3 text-sm font-semibold">Sığdır</button>
		</div>
	</div>
	<p data-kr-lightbox-caption class="mt-3 text-center text-sm text-paper/85"></p>
	<p class="mt-1 text-center text-xs text-paper/60">Yakınlaştırmak için görsele tıklayın ya da tekerleği kullanın; yakınken sürükleyerek gezinin.</p>
</dialog>
