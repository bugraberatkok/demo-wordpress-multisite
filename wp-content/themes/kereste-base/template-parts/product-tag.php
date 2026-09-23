<?php
/**
 * Paket etiketi: urun karti. Kereste paketine asilan etiket gibi: ustte
 * delik ve ip, sablon harfle olcu ya da tur, sonra gorsel ve ad.
 *
 * $args['product']: kr_products() satiri.
 * $args['hole']: deligin gosterecegi zemin rengi (etiketin durdugu yuzey).
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product ) {
	return;
}

$hole = $args['hole'] ?? 'var(--color-stone)';
?>
<a href="<?php echo esc_url( $product['url'] ); ?>" class="tag tag--link flex h-full flex-col px-5 pb-5 pt-11 text-ink no-underline"
	style="--tag-hole: <?php echo esc_attr( $hole ); ?>" <?php nwcs_edit_attr( $product['key'], 'card' ); ?>>

	<span class="stencil block text-[1.55rem]"><?php echo esc_html( $product['mark'] ); ?></span>

	<span class="shot mt-4 block aspect-[16/10]">
		<?php echo kr_image_tag( $product['image'], '', $product['name'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
	</span>

	<span class="mt-4 block font-slab text-[1.35rem] font-bold leading-tight"><?php echo esc_html( $product['name'] ); ?></span>
	<span class="mt-1.5 block text-[0.9375rem] leading-snug text-muted"><?php echo esc_html( $product['short'] ); ?></span>
</a>
