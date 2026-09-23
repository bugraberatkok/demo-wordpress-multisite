<?php
/**
 * Gorsel buyutme penceresi (urun galerileri). Sayfada bir kez; hangi
 * galerinin gosterilecegini tiklanan dugme belirler (site.js).
 */

defined( 'ABSPATH' ) || exit;

$control = 'flex items-center justify-center bg-ink/80 text-paper transition-colors hover:bg-mark';
?>
<dialog data-kr-lightbox class="kr-dialog m-auto w-[min(94vw,68rem)] bg-transparent p-0">
	<div class="relative bg-paper">
		<img data-kr-lightbox-image src="" alt="" class="mx-auto block max-h-[82vh] w-auto max-w-full object-contain" />

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
	</div>
	<p data-kr-lightbox-caption class="mt-3 text-center text-sm text-paper/85"></p>
</dialog>
