<?php
/**
 * Urun sayfasi (/civiler/<urun>/). pc_product_template() bu dosyayi secer.
 *
 * Solda anlatim ve "kullanildigi isler"; sagda yapiskan siparis kutusu
 * (olcu ve fiyat icin arayin: gercek olcu listesi gelene kadar tablo yok).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$key      = pc_current_product_key();
$products = pc_products();
$product  = $products[ $key ] ?? null;

if ( ! $product ) {
	get_footer();
	return;
}

$hub      = pc_manifest()['pages']['products'] ?? array();
$phone    = pc_phone();
$wa       = pc_whatsapp( sprintf( '%s hakkında fiyat almak istiyorum.', $product['name'] ) );
$uses     = nwcs_rows( $key, 'detail', 'uses' );
$contact  = pc_link( pc_manifest()['pages']['contact']['path'] ?? '/iletisim/' );
$quote    = add_query_arg( 'urun', rawurlencode( $product['name'] ), $contact ) . '#siparis';
$others   = array_diff_key( $products, array( $key => true ) );

pc_part(
	'page-head',
	array(
		'title'  => $product['name'],
		'lead'   => (string) nwcs_field( $key, 'detail', 'lead' ),
		'crumbs' => array( array( (string) nwcs_field( 'products', 'head', 'title' ), pc_link( $hub['path'] ?? '/' ), array( 'products', 'head', 'title' ) ) ),
		'title_edit' => array( $key, 'card', 'name' ),
		'lead_edit'  => array( $key, 'detail', 'lead' ),
	)
);
?>

<section class="pc-section">
	<div class="pc-wrap pc-product">
		<div class="pc-product__main">
			<figure class="pc-product__figure" <?php nwcs_edit_attr( $key, 'card', 'image' ); ?>>
				<?php echo pc_img_tag( $product['image'], 'pc-product__img', true ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				<?php if ( $product['image']['sample'] ) : ?>
					<span class="pc-sample"><?php echo esc_html( nwcs_field( 'global', 'common', 'sample_note' ) ); ?></span>
				<?php endif; ?>
			</figure>

			<p class="pc-product__tool" <?php nwcs_edit_attr( $key, 'card', 'tool' ); ?>><?php echo esc_html( $product['tool'] ); ?></p>

			<div class="pc-prose" <?php nwcs_edit_attr( $key, 'detail', 'body' ); ?>>
				<?php echo pc_paragraphs( (string) nwcs_field( $key, 'detail', 'body' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</div>

			<?php if ( $uses ) : ?>
				<h2 class="pc-product__subtitle"<?php nwcs_edit_attr( 'products', 'shared', 'uses_title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'shared', 'uses_title' ) ); ?></h2>
				<ul class="pc-ticks" <?php nwcs_edit_attr( $key, 'detail', 'uses' ); ?>>
					<?php foreach ( $uses as $index => $use ) : ?>
						<li<?php nwcs_edit_attr( $key, 'detail', 'uses', (int) $index, 'text' ); ?>><?php echo esc_html( $use['text'] ?? '' ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<aside class="pc-order-card" aria-label="Sipariş">
			<h2 class="pc-order-card__title"<?php nwcs_edit_attr( 'products', 'shared', 'order_title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'shared', 'order_title' ) ); ?></h2>
			<p <?php nwcs_edit_attr( 'products', 'shared', 'price_note' ); ?>><?php echo esc_html( nwcs_field( 'products', 'shared', 'price_note' ) ); ?></p>
			<a href="<?php echo esc_url( $phone['url'] ); ?>" class="pc-btn pc-btn--navy pc-btn--block"<?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>>
				<?php echo pc_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				<span class="pc-num"><?php echo esc_html( $phone['label'] ); ?></span>
			</a>
			<?php if ( $wa ) : ?>
				<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="pc-btn pc-btn--wa pc-btn--block"<?php nwcs_edit_attr( 'products', 'shared', 'wa_label' ); ?>>
					<?php echo pc_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					<?php echo esc_html( nwcs_field( 'products', 'shared', 'wa_label' ) ); ?>
				</a>
			<?php endif; ?>
			<a href="<?php echo esc_url( $quote ); ?>" class="pc-link"<?php nwcs_edit_attr( 'products', 'shared', 'form_label' ); ?>><?php echo esc_html( nwcs_field( 'products', 'shared', 'form_label' ) ); ?></a>
		</aside>
	</div>
</section>

<?php if ( $others ) : ?>
	<section class="pc-section pc-section--steel" aria-labelledby="pc-others">
		<div class="pc-wrap">
			<header class="pc-head">
				<h2 id="pc-others" <?php nwcs_edit_attr( 'products', 'shared', 'others' ); ?>><?php echo esc_html( nwcs_field( 'products', 'shared', 'others' ) ); ?></h2>
			</header>
			<div class="pc-prows">
				<?php foreach ( $others as $other ) : ?>
					<?php pc_part( 'product-row', array( 'product' => $other ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
