<?php
/**
 * Urun karti: ana sayfa izgarasi ve urun sayfasindaki "diger urunler".
 *
 * $args['product'] ip_products() satiridir. Kartin tamami tek baglantidir;
 * geri bildirim yalnizca kenar rengidir.
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product ) {
	return;
}

$level = $args['heading'] ?? 'h3';
?>
<a href="<?php echo esc_url( $product['url'] ); ?>"
	class="card card--link flex h-full flex-col overflow-hidden text-ink no-underline"
	<?php nwcs_edit_attr( $product['key'], 'card' ); ?>>

	<div class="shot aspect-[4/3] border-b border-line">
		<?php echo ip_image_tag( $product['image'], '', $product['name'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
	</div>

	<div class="flex flex-1 flex-col p-5 md:p-6">
		<<?php echo tag_escape( $level ); ?> class="text-xl font-semibold"><?php echo esc_html( $product['name'] ); ?></<?php echo tag_escape( $level ); ?>>

		<p class="mt-2 text-[0.9375rem] leading-relaxed text-steel"><?php echo esc_html( $product['short'] ); ?></p>

		<span class="dimtag mt-auto pt-5"><?php echo esc_html( $product['size'] ); ?></span>
	</div>
</a>
