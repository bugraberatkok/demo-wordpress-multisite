<?php
/**
 * Alt sayfa giris metni.
 */

defined( 'ABSPATH' ) || exit;

$image = nwcs_image( 'inner', 'intro', 'image' );
?>
<section class="p-section" data-nwcs-section="intro">
	<div class="p-wrap p-intro">
		<div>
			<div class="p-head p-head--left">
				<h2 class="p-title"><?php echo esc_html( nwcs_field( 'inner', 'intro', 'title' ) ); ?></h2>
			</div>
			<div class="p-intro__body"><?php echo esc_html( nwcs_field( 'inner', 'intro', 'body' ) ); ?></div>
		</div>
		<div class="p-intro__media">
			<?php echo paletci_image_tag( $image, '', 'Örnek görsel — ürünler' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>
	</div>
</section>
