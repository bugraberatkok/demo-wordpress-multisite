<?php
/**
 * Iletisim satirlari: telefon, cep/WhatsApp, e-posta, adres. Ana sayfa ve
 * Iletisim sayfasi.
 */

defined( 'ABSPATH' ) || exit;

$phone    = pc_phone();
$whatsapp = pc_whatsapp();
$email    = trim( (string) nwcs_field( 'global', 'header', 'email' ) );
$hours    = trim( (string) nwcs_field( 'contact', 'details', 'hours' ) );
?>
<dl class="pc-lines">
	<div>
		<dt>Telefon</dt>
		<dd><a class="pc-num" href="<?php echo esc_url( $phone['url'] ); ?>"><?php echo esc_html( $phone['label'] ); ?></a></dd>
	</div>
	<div>
		<dt>Cep ve WhatsApp</dt>
		<dd>
			<a class="pc-num" href="<?php echo esc_url( pc_link( nwcs_field( 'global', 'footer', 'mobile_url' ) ) ); ?>"><?php echo esc_html( nwcs_field( 'global', 'footer', 'mobile_label' ) ); ?></a>
			<?php if ( $whatsapp ) : ?>
				<a class="pc-lines__wa" href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener">WhatsApp’tan yazın</a>
			<?php endif; ?>
		</dd>
	</div>
	<?php if ( $email ) : ?>
		<div>
			<dt>E-posta</dt>
			<dd><a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a></dd>
		</div>
	<?php endif; ?>
	<div>
		<dt>Adres</dt>
		<dd <?php nwcs_edit_attr( 'contact', 'details', 'address' ); ?>><?php echo pc_multiline( (string) nwcs_field( 'contact', 'details', 'address' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?></dd>
	</div>
	<?php if ( $hours ) : ?>
		<div>
			<dt>Çalışma saatleri</dt>
			<dd><?php echo esc_html( $hours ); ?></dd>
		</div>
	<?php endif; ?>
</dl>
