<?php
/**
 * Havuz urunleri bolumu (gorselli kartlar).
 *
 * Kategori kartlari (catalog.php) elle girilen dort grubu gosterir; bu bolum
 * ise panelden bu site icin secilen tekil urunleri.
 *
 * Kartlar merkezi urun havuzundan gelir; bu sitenin secimi ve istisnalari
 * panelden yonetilir. Fiyat bos ise "Teklif al" gorunur.
 */

defined( 'ABSPATH' ) || exit;

$items     = function_exists( 'nwcs_site_products' ) ? nwcs_site_products() : array();
$cta_label = nwcs_field( 'home', 'products', 'cta_label' );

// Ziyaretci bos bir bolum gormesin; bos uyarisi yalnizca panel onizlemesinde.
if ( ! $items && ! ( function_exists( 'nwcs_is_preview' ) && nwcs_is_preview() ) ) {
	return;
}
?>
<section class="k-section k-section--alt" id="urunler" data-nwcs-section="products">
	<div class="k-wrap">
		<div class="k-section-head">
			<h2 class="k-section-title" <?php nwcs_edit_attr( 'home', 'products', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'products', 'title' ) ); ?></h2>
			<p class="k-section-sub" <?php nwcs_edit_attr( 'home', 'products', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'home', 'products', 'subtitle' ) ); ?></p>
		</div>

		<div class="k-catalog" <?php nwcs_edit_attr( 'home', 'products', 'pool' ); ?>>
			<?php foreach ( $items as $item ) : ?>
				<article class="k-cat">
					<div class="k-cat__media">
						<?php echo kocist_image_tag( $item['image'], '', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					</div>
					<div class="k-cat__body">
						<h3 class="k-cat__title"><?php echo esc_html( $item['title'] ); ?></h3>

						<span class="k-cat__price<?php echo $item['has_price'] ? '' : ' is-quote'; ?>">
							<?php echo esc_html( $item['price_label'] ); ?>
						</span>

						<p class="k-cat__text"><?php echo esc_html( $item['short'] ); ?></p>

						<a class="k-cat__link" href="<?php echo esc_url( $item['body'] ? $item['url'] : '#teklif' ); ?>">
							<?php echo esc_html( $item['body'] ? 'Ürün detayı' : $cta_label ); ?>
							<?php nwcs_the_icon( 'arrow', 'k-icon', 16 ); ?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>

			<?php if ( ! $items ) : ?>
				<p class="k-section-sub">Bu site için henüz ürün seçilmedi.</p>
			<?php endif; ?>
		</div>
	</div>
</section>
