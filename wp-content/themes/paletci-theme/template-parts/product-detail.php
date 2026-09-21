<?php
/**
 * Urun detay sayfasi (/urun/<slug>/).
 *
 * $product: merkezi havuzdan gelen, site istisnalari uygulanmis urun.
 * Eklenti bu sablonu locate_template ile bulur ve $product ile birlikte yukler.
 */

defined( 'ABSPATH' ) || exit;

/** @var array $product */
get_header();
?>
<div class="p-pagehead">
	<div class="p-wrap">
		<span class="p-badge"><?php echo esc_html( nwcs_field( 'home', 'products', 'title' ) ); ?></span>
		<h1 class="p-pagehead__title"><?php echo esc_html( $product['title'] ); ?></h1>
		<?php if ( $product['short'] ) : ?>
			<p class="p-pagehead__sub"><?php echo esc_html( $product['short'] ); ?></p>
		<?php endif; ?>
	</div>
</div>

<section class="p-section">
	<div class="p-wrap p-detail">
		<div class="p-detail__media">
			<?php echo paletci_image_tag( $product['image'], '', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>

		<div class="p-detail__body">
			<div class="p-detail__price">
				<span class="<?php echo $product['has_price'] ? 'p-detail__amount' : 'p-detail__quote'; ?>">
					<?php echo esc_html( $product['price_label'] ); ?>
				</span>
				<?php if ( $product['spec'] ) : ?>
					<span class="p-product__meta"><?php echo esc_html( $product['spec'] ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( $product['body'] ) : ?>
				<div class="p-detail__text"><?php echo wp_kses_post( wpautop( $product['body'] ) ); ?></div>
			<?php endif; ?>

			<?php if ( $product['categories'] ) : ?>
				<p class="p-detail__cats">
					<?php foreach ( $product['categories'] as $name ) : ?>
						<span class="p-chip"><?php echo esc_html( $name ); ?></span>
					<?php endforeach; ?>
				</p>
			<?php endif; ?>

			<div class="p-detail__actions">
				<a class="p-btn p-btn--clay" href="<?php echo esc_url( home_url( '/#teklif' ) ); ?>">
					<?php echo esc_html( $product['has_price'] ? 'Sipariş için yazın' : 'Fiyat teklifi isteyin' ); ?>
					<?php nwcs_the_icon( 'arrow', 'p-icon', 18 ); ?>
				</a>
				<a class="p-btn p-btn--outline" href="<?php echo esc_url( paletci_link( nwcs_field( 'global', 'header', 'whatsapp_url' ) ) ); ?>">
					<?php nwcs_the_icon( 'whatsapp', 'p-icon', 18 ); ?>
					<?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?>
				</a>
			</div>

			<p class="nwcs-detail-note">
				Bu ürün merkezî havuzdan gelir; içeriği Ağ Yönetimi → Ürün Havuzu'ndan düzenlenir.
			</p>
		</div>
	</div>
</section>
<?php
get_footer();
