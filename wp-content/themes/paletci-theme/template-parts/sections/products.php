<?php
/**
 * Urun kartlari bolumu.
 *
 * Urunler artik bu sayfada degil, merkezi havuzda tutulur. Site hangi urunleri
 * gosterecegini ve varsa kendi istisnalarini panelden belirler.
 * Fiyat bos ise kartta "Teklif al" gorunur.
 */

defined( 'ABSPATH' ) || exit;

$items     = function_exists( 'nwcs_site_products' ) ? nwcs_site_products() : array();
$cta_label = nwcs_field( 'home', 'products', 'cta_label' );
?>
<section class="p-section" id="urunler" data-nwcs-section="products">
	<div class="p-wrap">
		<div class="p-head">
			<h2 class="p-title" <?php nwcs_edit_attr( 'home', 'products', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'products', 'title' ) ); ?></h2>
			<p class="p-sub" <?php nwcs_edit_attr( 'home', 'products', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'home', 'products', 'subtitle' ) ); ?></p>
		</div>

		<div class="p-products" <?php nwcs_edit_attr( 'home', 'products', 'pool' ); ?>>
			<?php foreach ( $items as $item ) : ?>
				<article class="p-product">
					<div class="p-product__media">
						<?php echo paletci_image_tag( $item['image'], '', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					</div>
					<div class="p-product__body">
						<h3 class="p-product__title"><?php echo esc_html( $item['title'] ); ?></h3>

						<span class="p-product__price<?php echo $item['has_price'] ? '' : ' is-quote'; ?>">
							<?php echo esc_html( $item['price_label'] ); ?>
						</span>

						<?php if ( ! empty( $item['spec'] ) ) : ?>
							<span class="p-product__meta"><?php echo esc_html( $item['spec'] ); ?></span>
						<?php endif; ?>

						<p class="p-product__text"><?php echo esc_html( $item['short'] ); ?></p>

						<a class="p-product__link" href="<?php echo esc_url( $item['body'] ? $item['url'] : '#teklif' ); ?>">
							<?php echo esc_html( $item['body'] ? 'Ürün detayı' : $cta_label ); ?>
							<?php nwcs_the_icon( 'arrow', 'p-icon', 16 ); ?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>

			<?php if ( ! $items ) : ?>
				<p class="p-sub">Bu site için henüz ürün seçilmedi.</p>
			<?php endif; ?>
		</div>
	</div>
</section>
