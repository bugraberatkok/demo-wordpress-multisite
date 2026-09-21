<?php
/**
 * Urun kartlari bolumu.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'home', 'products', 'items' );
?>
<section class="p-section" id="urunler" data-nwcs-section="products">
	<div class="p-wrap">
		<div class="p-head">
			<h2 class="p-title" <?php nwcs_edit_attr( 'home', 'products', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'products', 'title' ) ); ?></h2>
			<p class="p-sub" <?php nwcs_edit_attr( 'home', 'products', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'home', 'products', 'subtitle' ) ); ?></p>
		</div>

		<div class="p-products">
			<?php
			foreach ( $items as $index => $item ) :
				$image = nwcs_image_by_id( (int) ( $item['image'] ?? 0 ), 'medium_large' );
				?>
				<article class="p-product">
					<div class="p-product__media" <?php nwcs_edit_attr( 'home', 'products', 'items', $index, 'image' ); ?>>
						<?php echo paletci_image_tag( $image, '', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					</div>
					<div class="p-product__body">
						<h3 class="p-product__title" <?php nwcs_edit_attr( 'home', 'products', 'items', $index, 'title' ); ?>><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
						<?php if ( ! empty( $item['meta'] ) ) : ?>
							<span class="p-product__meta" <?php nwcs_edit_attr( 'home', 'products', 'items', $index, 'meta' ); ?>><?php echo esc_html( $item['meta'] ); ?></span>
						<?php endif; ?>
						<p class="p-product__text" <?php nwcs_edit_attr( 'home', 'products', 'items', $index, 'text' ); ?>><?php echo esc_html( $item['text'] ?? '' ); ?></p>
						<a class="p-product__link" href="<?php echo esc_url( paletci_link( $item['link_url'] ?? '' ) ); ?>" <?php nwcs_edit_attr( 'home', 'products', 'items', $index, 'link_label' ); ?>>
							<?php echo esc_html( $item['link_label'] ?? '' ); ?>
							<?php nwcs_the_icon( 'arrow', 'p-icon', 16 ); ?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
