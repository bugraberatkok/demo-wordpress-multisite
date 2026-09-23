<?php
/**
 * WhatsApp yardim kutusu: fotograf zemin, koyu perde, yesil WhatsApp dairesi,
 * baslik, aciklama ve numara. Kutunun tamami WhatsApp baglantisidir.
 */

defined( 'ABSPATH' ) || exit;

$image = ik_image_or_default( nwcs_image( 'products', 'help', 'image', 'medium_large' ), 'urun-kereste.jpg' );
?>
<a class="ik-help" href="<?php echo esc_url( ik_link( nwcs_field( 'products', 'help', 'number_url' ) ) ); ?>" target="_blank" rel="noopener">
	<?php if ( ! empty( $image['url'] ) ) : ?>
		<img class="ik-help__image" src="<?php echo esc_url( $image['url'] ); ?>" alt="" loading="lazy" decoding="async" />
	<?php endif; ?>
	<span class="ik-help__icon"><?php ik_icon( 'whatsapp', 34 ); ?></span>
	<span class="ik-help__title" <?php nwcs_edit_attr( 'products', 'help', 'title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'help', 'title' ) ); ?></span>
	<span class="ik-help__text" <?php nwcs_edit_attr( 'products', 'help', 'text' ); ?>><?php echo esc_html( nwcs_field( 'products', 'help', 'text' ) ); ?></span>
	<span class="ik-help__number" <?php nwcs_edit_attr( 'products', 'help', 'number_label' ); ?>><?php echo esc_html( nwcs_field( 'products', 'help', 'number_label' ) ); ?></span>
	<span class="screen-reader-text">(WhatsApp yeni sekmede açılır)</span>
</a>
