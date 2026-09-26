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
$row   = array( 'products', 'catalog', 'items', (int) $product['index'] );

// Alt metin: urunun gizli panel sayfasinda yazildiysa o, yoksa kisa aciklama.
$lead      = ik_product_page_field( $product, 'head', 'lead' );
$lead_edit = '' !== $lead ? array( ik_product_page_key( $product ), 'head', 'lead' ) : array( ...$row, 'short' );

get_template_part(
	'template-parts/page-head',
	null,
	array(
		'title'      => $product['title'],
		'text'       => '' !== $lead ? $lead : $product['short'],
		'title_edit' => array( ...$row, 'title' ),
		'text_edit'  => $lead_edit,
		'crumbs'     => array(
			array( nwcs_field( 'products', 'detail', 'back_label' ), ik_link( '/urunlerimiz/' ), array( 'products', 'detail', 'back_label' ) ),
		),
	)
);
?>
<section class="ik-section ik-product">
	<div class="ik-wrap ik-product__grid">
		<article class="ik-product__main">
			<figure class="ik-product__media" <?php nwcs_edit_attr( 'products', 'catalog', 'items', $product['index'], 'image' ); ?>>
				<?php if ( ! empty( $image['url'] ) ) : ?>
					<?php // Buyutme: tam boy gorsel pencerede, icinde ikinci kademe yakinlastirma (assets/js/product-zoom.js). ?>
					<button type="button" class="ik-zoom-open" data-ik-zoom-open
						data-full="<?php echo esc_url( ik_product_image( $product, 'full' )['url'] ?? $image['url'] ); ?>"
						aria-label="<?php echo esc_attr( $product['title'] . ' görselini büyüt' ); ?>">
						<?php echo ik_image_tag( $image, 'ik-product__image', null, 'eager' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
						<span class="ik-zoom-open__badge" aria-hidden="true">Büyüt</span>
					</button>
				<?php else : ?>
					<?php echo ik_image_tag( $image, 'ik-product__image', null, 'eager' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				<?php endif; ?>
			</figure>

			<?php if ( ! empty( $image['url'] ) ) : ?>
				<dialog class="ik-zoom" data-ik-zoom aria-label="<?php echo esc_attr( $product['title'] ); ?>">
					<div class="ik-zoom__frame">
						<div class="ik-zoom__stage" data-ik-zoom-stage>
							<img class="ik-zoom__image" data-ik-zoom-image src="" alt="<?php echo esc_attr( $image['alt'] ?: $product['title'] ); ?>" />
						</div>

						<form method="dialog" class="ik-zoom__close">
							<button type="submit" class="ik-zoom__btn" aria-label="Kapat">&times;</button>
						</form>

						<div class="ik-zoom__bar" role="group" aria-label="Yakınlaştırma">
							<button type="button" class="ik-zoom__btn" data-ik-zoom-out aria-label="Uzaklaştır">&minus;</button>
							<span class="ik-zoom__level" data-ik-zoom-level aria-live="polite">%100</span>
							<button type="button" class="ik-zoom__btn" data-ik-zoom-in aria-label="Yakınlaştır">+</button>
							<button type="button" class="ik-zoom__btn ik-zoom__btn--text" data-ik-zoom-reset>Sığdır</button>
						</div>
					</div>
					<p class="ik-zoom__hint">Yakınlaştırmak için görsele tıklayın ya da tekerleği kullanın; yakınken sürükleyerek gezinin.</p>
				</dialog>
			<?php endif; ?>

			<div class="ik-prose ik-product__body" <?php nwcs_edit_attr( 'products', 'catalog', 'items', $product['index'], 'body' ); ?>>
				<?php ik_paragraphs( $product['body'] ); ?>
			</div>

			<?php if ( $specs ) : ?>
				<h2 class="ik-product__subtitle" <?php nwcs_edit_attr( 'products', 'detail', 'specs_title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'detail', 'specs_title' ) ); ?></h2>
				<dl class="ik-specs" <?php nwcs_edit_attr( 'products', 'catalog', 'items', $product['index'], 'specs' ); ?>>
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
							<a class="ik-sidelist__link" href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $item['slug'] === $product['slug'] ? ' aria-current="page"' : ''; ?> <?php nwcs_edit_attr( 'products', 'catalog', 'items', (int) $item['index'], 'title' ); ?>>
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
