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
				$image = nwcs_image_by_id( (int) ( $item['image'] ?? 0 ), 'large' );
				?>
				<a
					class="k-group"
					href="<?php echo esc_url( kocist_link( $item['link_url'] ?? '' ) ); ?>"
					data-k-group
					style="--i: <?php echo (int) $index; ?>"
					<?php nwcs_edit_attr( 'home', 'catalog', 'items', $index, 'title' ); ?>
				>
					<?php echo kocist_image_tag( $image, 'k-group__img', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

					<span class="k-group__body">
						<span class="k-group__title"><?php echo esc_html( $item['title'] ?? '' ); ?></span>
						<span class="k-group__rule" aria-hidden="true"></span>

						<?php if ( ! empty( $item['link_label'] ) ) : ?>
							<span class="k-group__btn"><?php echo esc_html( $item['link_label'] ); ?></span>
						<?php endif; ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
