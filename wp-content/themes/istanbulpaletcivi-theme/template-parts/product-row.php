<?php
/**
 * Urun satiri (ana sayfa ve Palet Civileri): buyuk fotograf, ad, nasil
 * cakildigi, kisa aciklama, iki eylem. Satirlar sirayla sola/saga yaslanir.
 *
 * $args: product (pc_products() ogesi), more (baglanti metni), more_edit
 * (more verilince onun isaret tanimi), heading (h2/h3)
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product ) {
	return;
}

$tag = in_array( $args['heading'] ?? '', array( 'h2', 'h3' ), true ) ? $args['heading'] : 'h3';
$more      = isset( $args['more'] ) ? (string) $args['more'] : (string) nwcs_field( 'products', 'list', 'more' );
$more_edit = isset( $args['more'] ) ? ( $args['more_edit'] ?? null ) : array( 'products', 'list', 'more' );
$wa  = pc_whatsapp( sprintf( '%s hakkında fiyat almak istiyorum.', $product['name'] ) );
?>
<article class="pc-prow">
	<a href="<?php echo esc_url( $product['url'] ); ?>" class="pc-prow__media" tabindex="-1" aria-hidden="true"<?php nwcs_edit_attr( $product['key'], 'card', 'image' ); ?>>
		<?php echo pc_img_tag( $product['image'], 'pc-prow__img' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		<?php if ( $product['image']['sample'] ) : ?>
			<span class="pc-sample"><?php echo esc_html( nwcs_field( 'global', 'common', 'sample_note' ) ); ?></span>
		<?php endif; ?>
	</a>
	<div class="pc-prow__body">
		<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapingOutput ?> class="pc-prow__name" <?php nwcs_edit_attr( $product['key'], 'card', 'name' ); ?>>
			<a href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['name'] ); ?></a>
		</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapingOutput ?>>
		<p class="pc-prow__tool" <?php nwcs_edit_attr( $product['key'], 'card', 'tool' ); ?>><?php echo esc_html( $product['tool'] ); ?></p>
		<p class="pc-prow__short" <?php nwcs_edit_attr( $product['key'], 'card', 'short' ); ?>><?php echo esc_html( $product['short'] ); ?></p>
		<div class="pc-prow__actions">
			<a href="<?php echo esc_url( $product['url'] ); ?>" class="pc-btn pc-btn--navy"<?php pc_edit( $more_edit ); ?>><?php echo esc_html( $more ); ?></a>
			<?php if ( $wa ) : ?>
				<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="pc-btn pc-btn--line"<?php nwcs_edit_attr( 'products', 'list', 'ask' ); ?>><?php echo esc_html( nwcs_field( 'products', 'list', 'ask' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</article>
