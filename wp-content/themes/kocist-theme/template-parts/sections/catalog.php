<?php
/**
 * Ana urun gamlari: dort kart yan yana, gorselin uzerinde ortalanmis baslik.
 *
 * Durur haldeyken yalnizca baslik ve alt baslik gorunur; imlec gelince
 * altlarindaki cizgi acilir ve Kesfet butonu asagidan yukselir. Kartlar
 * ekrana girdikce sirayla belirir (assets/js/catalog.js).
 *
 * Kartin tamami tiklanabilir; buton gorsel bir isarettir, bu yuzden ayri
 * bir baglanti degil <span> olarak basiliyor (ic ice baglanti gecersiz
 * isaretleme olurdu).
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'home', 'catalog', 'items' );

// Kart gorseli panelde secilmemisse temadaki kategori fotograflari.
$item_defaults = array( 's2-kereste.jpg', 's2-ambalaj.jpg', 's2-dekorasyon.jpg', 's2-hirdavat.jpg' );
?>
<section class="k-section" id="katalog" data-nwcs-section="catalog">
	<div class="k-wrap">
		<?php /* Buyuk baslik yerine ince bir etiket; kartlarin onune gecmiyor. */ ?>
		<h2 class="k-groups__label" data-k-reveal>
			<span class="k-groups__dash" aria-hidden="true"></span>
			<span <?php nwcs_edit_attr( 'home', 'catalog', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'catalog', 'title' ) ); ?></span>
		</h2>

		<div class="k-groups" data-k-groups>
			<?php
			foreach ( $items as $index => $item ) :
				$image = kocist_image_or_default( nwcs_image_by_id( (int) ( $item['image'] ?? 0 ), 'large' ), $item_defaults[ $index ] ?? '', (string) ( $item['title'] ?? '' ) );

				// Kart hala varsayilan capaya gidiyorsa kendi grubunun sayfasina.
				$item_url = (string) ( $item['link_url'] ?? '' );

				if ( kocist_is_catalog_placeholder( $item_url ) ) {
					$item_url = kocist_catalog_url_for_text( (string) ( $item['title'] ?? '' ) ) ?: $item_url;
				}
				?>
				<a
					class="k-group"
					href="<?php echo esc_url( kocist_link( $item_url, '/#katalog' ) ); ?>"
					data-k-group
					style="--i: <?php echo (int) $index; ?>"
					<?php nwcs_edit_attr( 'home', 'catalog', 'items', $index, 'title' ); ?>
				>
					<?php echo kocist_image_tag( $image, 'k-group__img', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

					<span class="k-group__body">
						<span class="k-group__title"><?php echo esc_html( $item['title'] ?? '' ); ?></span>
						<span class="k-group__rule" aria-hidden="true"></span>

						<?php if ( ! empty( $item['link_label'] ) ) : ?>
							<span class="k-group__btn" <?php nwcs_edit_attr( 'home', 'catalog', 'items', $index, 'link_label' ); ?>><?php echo esc_html( $item['link_label'] ); ?></span>
						<?php endif; ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
