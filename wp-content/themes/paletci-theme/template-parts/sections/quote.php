<?php
/**
 * Teklif bolumu.
 *
 * Form bilincli olarak DEMO'dur: hicbir yere gonderilmez, kaydedilmez ve
 * "gonderildi" basarisi gosterilmez. Gercek iletisim icin telefon/WhatsApp
 * baglantilari verilir.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="p-section" id="teklif" data-nwcs-section="quote">
	<div class="p-wrap p-quote">
		<div>
			<div class="p-head p-head--left">
				<h2 class="p-title" <?php nwcs_edit_attr( 'home', 'quote', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'quote', 'title' ) ); ?></h2>
				<p class="p-sub" <?php nwcs_edit_attr( 'home', 'quote', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'quote', 'text' ) ); ?></p>
			</div>

			<div class="p-quote__actions">
				<a class="p-btn p-btn--forest" href="<?php echo esc_url( paletci_link( nwcs_field( 'home', 'quote', 'call_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'quote', 'call_label' ); ?>>
					<?php nwcs_the_icon( 'phone', 'p-icon', 18 ); ?>
					<?php echo esc_html( nwcs_field( 'home', 'quote', 'call_label' ) ); ?>
				</a>
			</div>
		</div>

		<div class="p-quote__card">
			<p class="p-demo-note" <?php nwcs_edit_attr( 'home', 'quote', 'demo_notice' ); ?>>
				<?php nwcs_the_icon( 'shield', 'p-icon', 18 ); ?>
				<span><?php echo esc_html( nwcs_field( 'home', 'quote', 'demo_notice' ) ); ?></span>
			</p>

			<form class="p-quote__form" novalidate>
				<div class="p-field">
					<label for="pq-name"><?php echo esc_html( nwcs_field( 'home', 'quote', 'name_label' ) ); ?></label>
					<input type="text" id="pq-name" name="pq-name" autocomplete="off" />
				</div>
				<div class="p-field">
					<label for="pq-phone"><?php echo esc_html( nwcs_field( 'home', 'quote', 'phone_label' ) ); ?></label>
					<input type="tel" id="pq-phone" name="pq-phone" autocomplete="off" />
				</div>
				<div class="p-field">
					<label for="pq-detail"><?php echo esc_html( nwcs_field( 'home', 'quote', 'detail_label' ) ); ?></label>
					<textarea id="pq-detail" name="pq-detail"></textarea>
				</div>

				<button type="submit" class="p-btn p-btn--clay">
					<?php echo esc_html( nwcs_field( 'home', 'quote', 'button_label' ) ); ?>
				</button>

				<p class="p-demo-note" data-demo-result hidden style="margin-top:16px">
					<span>Bu demo formu mesaj iletmez. Bilgileriniz hiçbir yere gönderilmedi; talebinizi telefon veya WhatsApp ile iletin.</span>
				</p>
			</form>
		</div>
	</div>
</section>

<script>
( function () {
	var form = document.querySelector( '.p-quote__form' );
	if ( ! form ) { return; }
	form.addEventListener( 'submit', function ( event ) {
		event.preventDefault();
		var note = form.querySelector( '[data-demo-result]' );
		if ( note ) { note.hidden = false; }
	} );
}() );
</script>
