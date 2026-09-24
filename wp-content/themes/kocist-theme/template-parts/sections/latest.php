<?php
/**
 * Son eklenen urunler: tek satirlik, saga dogru kayan serit.
 *
 * Baslik konteynerle hizali; kartlar ayni sol cizgiden baslayip ekranin sag
 * kenarindan tasiyor, yarim gorunen son kart devami oldugunu belli ediyor.
 * Oklar, ilerleme cizgisi ve fareyle surukleme assets/js/latest.js'te.
 *
 * Kartlar panelde elle girilir; varsayilanlar test icerigidir.
 */

defined( 'ABSPATH' ) || exit;

$items      = nwcs_rows( 'home', 'latest', 'items' );
$link_label = nwcs_field( 'home', 'latest', 'link_label' );

if ( ! $items ) {
	return;
}

// Kart gorseli panelde secilmemisse temadaki ornek fotograflar (sirayla).
$item_defaults = array(
	'urun-1.jpg',
	'ahsap-kamelya-3x3-zeminli.webp',
	'ahsap-salincak-4-kisilik-oval-golgelikli.webp',
	'playwood.webp',
	's2-kereste.jpg',
	's2-ambalaj.jpg',
	'urun-2.jpg',
	's2-hirdavat.jpg',
);
?>
<section class="k-section k-section--alt k-latest" id="yeni-urunler" data-nwcs-section="latest" aria-labelledby="k-latest-title">
	<div class="k-wrap k-latest__head">
		<div>
			<h2 class="k-section-title" id="k-latest-title" <?php nwcs_edit_attr( 'home', 'latest', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'latest', 'title' ) ); ?></h2>
			<p class="k-section-sub" <?php nwcs_edit_attr( 'home', 'latest', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'home', 'latest', 'subtitle' ) ); ?></p>
		</div>

		<div class="k-latest__nav" data-k-latest-nav hidden>
			<button type="button" class="k-latest__arrow" data-k-latest-prev aria-label="Önceki ürünler">
				<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<button type="button" class="k-latest__arrow" data-k-latest-next aria-label="Sonraki ürünler">
				<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
		</div>
	</div>

	<ul class="k-latest__track" data-k-latest-track tabindex="0" aria-label="Son eklenen ürünler listesi" <?php nwcs_edit_attr( 'home', 'latest', 'items' ); ?>>
		<?php
		foreach ( $items as $index => $item ) :
			$title = (string) ( $item['title'] ?? '' );
			$image = kocist_image_or_default( nwcs_image_by_id( (int) ( $item['image'] ?? 0 ), 'large' ), $item_defaults[ $index % count( $item_defaults ) ], $title );
			?>
			<li class="k-latest__item">
				<a class="k-latest__card" href="<?php echo esc_url( kocist_link( $item['link_url'] ?? '', '/iletisim/' ) ); ?>" draggable="false" <?php nwcs_edit_attr( 'home', 'latest', 'items', $index, 'title' ); ?>>
					<span class="k-latest__media">
						<?php echo kocist_image_tag( $image, 'k-latest__img', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					</span>

					<span class="k-latest__body">
						<?php if ( ! empty( $item['category'] ) ) : ?>
							<span class="k-latest__cat"><?php echo esc_html( $item['category'] ); ?></span>
						<?php endif; ?>

						<span class="k-latest__title"><?php echo esc_html( $title ); ?></span>

						<?php if ( ! empty( $item['spec'] ) ) : ?>
							<span class="k-latest__spec"><?php echo esc_html( $item['spec'] ); ?></span>
						<?php endif; ?>

						<?php if ( $link_label ) : ?>
							<span class="k-latest__cta"><?php echo esc_html( $link_label ); ?></span>
						<?php endif; ?>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="k-wrap">
		<div class="k-latest__progress" aria-hidden="true"><span data-k-latest-bar></span></div>
	</div>
</section>
