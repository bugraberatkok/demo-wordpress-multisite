<?php
/**
 * Urun detay sayfasi (/urun/<slug>/) — Kocist temasi.
 *
 * $product: merkezi havuzdan gelen, site istisnalari uygulanmis urun.
 */

defined( 'ABSPATH' ) || exit;

/** @var array $product */
get_header();
?>
<div class="k-pagehead">
	<div class="k-wrap">
		<p class="k-pagehead__crumb">Ana Sayfa / <?php echo esc_html( nwcs_field( 'home', 'products', 'title' ) ); ?></p>
		<h1 class="k-pagehead__title"><?php echo esc_html( $product['title'] ); ?></h1>
		<?php if ( $product['short'] ) : ?>
			<p class="k-pagehead__sub"><?php echo esc_html( $product['short'] ); ?></p>
		<?php endif; ?>
	</div>
</div>

<section class="k-section">
	<div class="k-wrap k-story">
		<div>
			<div class="k-detail__price">
				<span class="k-cat__price<?php echo $product['has_price'] ? '' : ' is-quote'; ?>">
					<?php echo esc_html( $product['price_label'] ); ?>
				</span>
				<?php if ( $product['spec'] ) : ?>
					<span class="k-detail__spec"><?php echo esc_html( $product['spec'] ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( $product['body'] ) : ?>
				<div class="k-story__body"><?php echo wp_kses_post( wpautop( $product['body'] ) ); ?></div>
			<?php endif; ?>

			<div class="k-hero__actions" style="margin-top:22px">
				<a class="k-btn k-btn--dark" href="<?php echo esc_url( home_url( '/#teklif' ) ); ?>">
					<?php echo esc_html( $product['has_price'] ? 'Sipariş için arayın' : 'Teklif isteyin' ); ?>
				</a>
			</div>
		</div>

		<div class="k-story__media">
			<?php echo kocist_image_tag( kocist_product_image( $product ), '', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>
	</div>
</section>
<?php
get_footer();
