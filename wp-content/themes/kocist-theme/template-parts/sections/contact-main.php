<?php
/**
 * Iletisim sayfasi.
 *
 * Duzen: koyu yesil bir baslik bandi, bandin altina binen beyaz iletisim
 * seridi, ardindan solda form sagda harita. Simetrik "kart listesi + form
 * karti" duzeninden kacinildi; agirlik banda ve haritaya verildi.
 *
 * Form bilincli olarak DEMO'dur: hicbir yere gonderilmez, kaydedilmez ve
 * "gonderildi" basarisi gosterilmez (paletci temasindaki teklif formuyla
 * ayni yaklasim). Gercek iletisim icin telefon, WhatsApp ve e-posta
 * baglantilari serit'te duruyor.
 *
 * Harita gomme adresi panelden gelmiyor; panelde yalnizca konum METNI ya da
 * koordinat tutuluyor, adres burada kuruluyor. Boylece iframe kaynagi her
 * zaman google.com kalir.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Serit ogesi. Degeri bos olan oge hic cizilmez.
 */
$kocist_strip_item = static function ( string $icon, string $title, string $text, string $url = '' ): void {
	if ( '' === trim( $text ) ) {
		return;
	}

	$tag = '' === trim( $url ) ? 'div' : 'a';
	?>
	<<?php echo esc_html( $tag ); ?>
		class="k-strip__item"
		<?php if ( 'a' === $tag ) : ?>
			href="<?php echo esc_url( kocist_link( $url ) ); ?>"
		<?php endif; ?>
	>
		<span class="k-strip__label">
			<?php nwcs_the_icon( $icon, 'k-strip__icon', 15 ); ?>
			<?php echo esc_html( $title ); ?>
		</span>
		<span class="k-strip__value"><?php echo esc_html( $text ); ?></span>
	</<?php echo esc_html( $tag ); ?>>
	<?php
};

// Harita hedefi: koordinat girilmisse o, yoksa adres metni.
$query  = trim( (string) nwcs_field( 'contact', 'map', 'query' ) );
$coords = trim( (string) nwcs_field( 'contact', 'map', 'coords' ) );

/*
 * Bicim dogrulaniyor; "41.14,28.46" disinda bir sey yazilirsa yok sayilir
 * ve adrese geri donulur.
 */
$has_coords = (bool) preg_match( '/^-?\d{1,2}(\.\d+)?\s*,\s*-?\d{1,3}(\.\d+)?$/', $coords );
$place      = $has_coords ? preg_replace( '/\s+/', '', $coords ) : $query;
?>
<section class="k-contact" data-nwcs-section="head">

	<div class="k-contact__band">
		<div class="k-wrap">
			<p class="k-contact__eyebrow"><?php echo esc_html( nwcs_field( 'contact', 'head', 'eyebrow' ) ); ?></p>
			<h1 class="k-contact__title"><?php echo esc_html( nwcs_field( 'contact', 'head', 'title' ) ); ?></h1>
			<p class="k-contact__subtitle"><?php echo esc_html( nwcs_field( 'contact', 'head', 'subtitle' ) ); ?></p>
		</div>
	</div>

	<div class="k-wrap">

		<?php /* Bandin altina binen serit: en cok kullanilan dort bilgi. */ ?>
		<div class="k-strip" data-nwcs-section="info">
			<?php
			$kocist_strip_item(
				(string) nwcs_field( 'contact', 'info', 'phone_icon' ),
				(string) nwcs_field( 'contact', 'info', 'phone_title' ),
				(string) nwcs_field( 'contact', 'info', 'phone_label' ),
				(string) nwcs_field( 'contact', 'info', 'phone_url' )
			);

			$kocist_strip_item(
				(string) nwcs_field( 'contact', 'info', 'whatsapp_icon' ),
				(string) nwcs_field( 'contact', 'info', 'whatsapp_title' ),
				(string) nwcs_field( 'contact', 'info', 'whatsapp_label' ),
				(string) nwcs_field( 'contact', 'info', 'whatsapp_url' )
			);

			$kocist_strip_item(
				(string) nwcs_field( 'contact', 'info', 'email_icon' ),
				(string) nwcs_field( 'contact', 'info', 'email_title' ),
				(string) nwcs_field( 'contact', 'info', 'email_label' ),
				(string) nwcs_field( 'contact', 'info', 'email_url' )
			);

			$kocist_strip_item(
				(string) nwcs_field( 'contact', 'info', 'hours_icon' ),
				(string) nwcs_field( 'contact', 'info', 'hours_title' ),
				(string) nwcs_field( 'contact', 'info', 'hours' )
			);
			?>
		</div>

		<div class="k-contact__grid">

			<div class="k-contact__form-col" data-nwcs-section="form">
				<h2 class="k-contact__h2"><?php echo esc_html( nwcs_field( 'contact', 'form', 'title' ) ); ?></h2>
				<p class="k-contact__lead"><?php echo esc_html( nwcs_field( 'contact', 'form', 'text' ) ); ?></p>

				<form class="k-contact__form" novalidate>
					<div class="k-field">
						<label for="kc-name"><?php echo esc_html( nwcs_field( 'contact', 'form', 'name_label' ) ); ?></label>
						<input type="text" id="kc-name" name="kc-name" autocomplete="name" placeholder="<?php echo esc_attr( nwcs_field( 'contact', 'form', 'name_ph' ) ); ?>" />
					</div>

					<div class="k-field">
						<label for="kc-phone"><?php echo esc_html( nwcs_field( 'contact', 'form', 'phone_label' ) ); ?></label>
						<input type="tel" id="kc-phone" name="kc-phone" autocomplete="tel" placeholder="<?php echo esc_attr( nwcs_field( 'contact', 'form', 'phone_ph' ) ); ?>" />
					</div>

					<div class="k-field">
						<label for="kc-email"><?php echo esc_html( nwcs_field( 'contact', 'form', 'email_label' ) ); ?></label>
						<input type="email" id="kc-email" name="kc-email" autocomplete="email" placeholder="<?php echo esc_attr( nwcs_field( 'contact', 'form', 'email_ph' ) ); ?>" />
					</div>

					<div class="k-field">
						<label for="kc-subject"><?php echo esc_html( nwcs_field( 'contact', 'form', 'subject_label' ) ); ?></label>
						<input type="text" id="kc-subject" name="kc-subject" autocomplete="off" placeholder="<?php echo esc_attr( nwcs_field( 'contact', 'form', 'subject_ph' ) ); ?>" />
					</div>

					<div class="k-field k-field--wide">
						<label for="kc-detail"><?php echo esc_html( nwcs_field( 'contact', 'form', 'detail_label' ) ); ?></label>
						<textarea id="kc-detail" name="kc-detail" rows="5" placeholder="<?php echo esc_attr( nwcs_field( 'contact', 'form', 'detail_ph' ) ); ?>"></textarea>
					</div>

					<div class="k-field k-field--wide k-contact__submit-row">
						<button type="button" class="k-contact__submit" disabled>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'submit_label' ) ); ?>
						</button>

						<span class="k-contact__demo">
							<?php nwcs_the_icon( 'shield', 'k-strip__icon', 15 ); ?>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'demo_notice' ) ); ?>
						</span>
					</div>
				</form>
			</div>

			<?php if ( '' !== $place ) : ?>
				<div class="k-contact__map-col" data-nwcs-section="map">
					<div class="k-map">
						<iframe
							class="k-map__frame"
							src="<?php echo esc_url( 'https://www.google.com/maps?q=' . rawurlencode( $place ) . '&z=16&output=embed' ); ?>"
							title="<?php echo esc_attr( '' !== $query ? $query : $place ); ?>"
							loading="lazy"
							referrerpolicy="no-referrer-when-downgrade"
							allowfullscreen
						></iframe>

						<?php /* Adres ve yol tarifi haritanin uzerine biniyor. */ ?>
						<div class="k-map__card">
							<p class="k-map__title"><?php echo esc_html( nwcs_field( 'contact', 'map', 'title' ) ); ?></p>
							<p class="k-map__address"><?php echo esc_html( nwcs_field( 'contact', 'info', 'address' ) ); ?></p>

							<a
								class="k-map__link"
								href="<?php echo esc_url( 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $place ) ); ?>"
								target="_blank"
								rel="noopener noreferrer"
							>
								<?php echo esc_html( nwcs_field( 'contact', 'map', 'link_label' ) ); ?>
								<span aria-hidden="true">→</span>
							</a>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
