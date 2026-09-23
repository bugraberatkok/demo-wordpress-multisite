<?php
/**
 * Teklif penceresi: her sayfada bir kez. data-kr-quote tasiyan her dugme
 * acar; dugme urun adini tasiyabilir (site.js).
 *
 * Gonderimden sonra sayfa ?teklif= ile doner; pencere kendiliginden acilip
 * hatalari ya da basari mesajini gosterir. Iletisim sayfasinda form sayfanin
 * icinde oldugu icin pencere basilmaz.
 */

defined( 'ABSPATH' ) || exit;

if ( kr_link_path( kr_page_path( 'contact', '/iletisim/' ) ) === kr_request_path() ) {
	return;
}

$state = kr_quote_state();
?>
<dialog data-kr-dialog class="kr-dialog m-auto w-[min(94vw,40rem)] max-h-[92vh] bg-paper p-0 text-ink"
	<?php echo $state['active'] ? 'data-open-on-load' : ''; ?> aria-labelledby="kr-dialog-title">
	<div class="relative border-t-4 border-mark p-6 sm:p-8">
		<form method="dialog" class="absolute right-3 top-3">
			<button type="submit" aria-label="Kapat" class="flex h-10 w-10 items-center justify-center text-2xl leading-none text-muted hover:text-ink">&times;</button>
		</form>
		<div id="kr-dialog-title">
			<?php get_template_part( 'template-parts/quote-form', null, array( 'prefix' => 'pencere', 'heading' => 'h2' ) ); ?>
		</div>
	</div>
</dialog>
