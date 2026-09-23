<?php
/**
 * Urun detayi: /urunlerimiz/<slug>/.
 *
 * Solda fotograf ve metin, sagda butun urunlerin listesi (bulunulan urun
 * isaretli), altinda WhatsApp yardim kutusu. Beklenen $args: product.
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? array();

if ( ! $product ) {
	return;
}

$image = ik_product_image( $product, 'large' );
$specs = ik_specs( $product['specs'] );

get_template_part(
	'template-parts/page-head',
	null,
	array(
		'title'  => $product['title'],
		'text'   => $product['short'],
		'crumbs' => array(
			array( nwcs_field( 'products', 'detail', 'back_label' ), home_url( '/urunlerimiz/' ) ),
		),
	)
);
?>
<section class="ik-section ik-product">
	<div class="ik-wrap ik-product__grid">
		<article class="ik-product__main">
			<figure class="ik-product__media">
				<?php echo ik_image_tag( $image, 'ik-product__image', null, 'eager' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</figure>

			<div class="ik-prose ik-product__body" <?php nwcs_edit_attr( 'products', 'catalog', 'items', $product['index'], 'body' ); ?>>
				<?php ik_paragraphs( $product['body'] ); ?>
			</div>

			<?php if ( $specs ) : ?>
				<h2 class="ik-product__subtitle" <?php nwcs_edit_attr( 'products', 'detail', 'specs_title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'detail', 'specs_title' ) ); ?></h2>
				<dl class="ik-specs">
					<?php foreach ( $specs as $spec ) : ?>
						<div class="ik-specs__row">
							<dt><?php echo esc_html( $spec[0] ); ?></dt>
							<dd><?php echo esc_html( $spec[1] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<div class="ik-product__actions">
				<a class="btn btn--solid" href="<?php echo esc_url( add_query_arg( 'urun', rawurlencode( $product['title'] ), ik_link( nwcs_field( 'products', 'detail', 'quote_url' ) ) ) ); ?>" <?php nwcs_edit_attr( 'products', 'detail', 'quote_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'products', 'detail', 'quote_label' ) ); ?>
				</a>
				<a class="btn btn--ghost" href="<?php echo esc_url( add_query_arg( 'text', rawurlencode( $product['title'] . ' hakkında bilgi almak istiyorum.' ), ik_link( nwcs_field( 'products', 'help', 'number_url' ) ) ) ); ?>" target="_blank" rel="noopener" <?php nwcs_edit_attr( 'products', 'detail', 'whatsapp_label' ); ?>>
					<?php ik_icon( 'whatsapp', 20 ); ?>
					<?php echo esc_html( nwcs_field( 'products', 'detail', 'whatsapp_label' ) ); ?>
				</a>
			</div>
		</article>

		<aside class="ik-product__aside">
			<nav class="ik-sidelist" aria-labelledby="ik-sidelist-title">
				<h2 class="ik-sidelist__title" id="ik-sidelist-title" <?php nwcs_edit_attr( 'products', 'detail', 'others_title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'detail', 'others_title' ) ); ?></h2>
				<ul>
					<?php foreach ( ik_products() as $item ) : ?>
						<li>
							<a class="ik-sidelist__link" href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $item['slug'] === $product['slug'] ? ' aria-current="page"' : ''; ?>>
								<?php echo esc_html( $item['title'] ); ?>
								<?php ik_icon( 'arrow', 16 ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<?php get_template_part( 'template-parts/help-box' ); ?>
		</aside>
	</div>
</section>
