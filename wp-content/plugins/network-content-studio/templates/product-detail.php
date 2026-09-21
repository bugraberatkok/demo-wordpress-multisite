<?php
/**
 * Urun detay sayfasi — tema kendi sablonunu vermediginde kullanilan sade yedek.
 *
 * $product: merkezi havuzdan gelen, site istisnalari uygulanmis urun.
 */

defined( 'ABSPATH' ) || exit;

/** @var array $product */
get_header();
?>
<main style="max-width:860px;margin:0 auto;padding:48px 20px">
	<h1><?php echo esc_html( $product['title'] ); ?></h1>

	<p style="font-size:18px;font-weight:700">
		<?php echo esc_html( $product['price_label'] ); ?>
		<?php if ( $product['spec'] ) : ?>
			<small style="font-weight:400;opacity:.7"> · <?php echo esc_html( $product['spec'] ); ?></small>
		<?php endif; ?>
	</p>

	<?php if ( ! empty( $product['image']['url'] ) ) : ?>
		<img src="<?php echo esc_url( $product['image']['url'] ); ?>"
			alt="<?php echo esc_attr( $product['image']['alt'] ); ?>"
			style="max-width:100%;height:auto;margin:20px 0" />
	<?php endif; ?>

	<?php if ( $product['short'] ) : ?>
		<p><?php echo esc_html( $product['short'] ); ?></p>
	<?php endif; ?>

	<?php if ( $product['body'] ) : ?>
		<?php echo wp_kses_post( wpautop( $product['body'] ) ); ?>
	<?php endif; ?>
</main>
<?php
get_footer();
