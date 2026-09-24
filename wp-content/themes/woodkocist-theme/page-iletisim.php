<?php
/**
 * Iletisim: WhatsApp, (girildiyse) telefon ve e-posta, adres, siparis formu.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$wa    = wk_whatsapp();
$phone = trim( (string) nwcs_field( 'global', 'header', 'phone_label' ) );
$email = trim( (string) nwcs_field( 'global', 'header', 'email' ) );
?>

<section class="wk-pagehead">
	<div class="wk-wrap">
		<h1 class="wk-hero__title" <?php nwcs_edit_attr( 'contact', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'head', 'title' ) ); ?></h1>
		<p class="wk-hero__lead" <?php nwcs_edit_attr( 'contact', 'head', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'head', 'lead' ) ); ?></p>
	</div>
</section>

<section class="wk-order">
	<div class="wk-wrap wk-order__grid">
		<dl class="wk-lines-info">
			<?php if ( $wa ) : ?>
				<div><dt>WhatsApp</dt><dd><a class="wk-num" href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener"><?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?></a></dd></div>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<div><dt>Telefon</dt><dd><a class="wk-num" href="<?php echo esc_url( wk_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>"><?php echo esc_html( $phone ); ?></a></dd></div>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<div><dt>E-posta</dt><dd><a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a></dd></div>
			<?php endif; ?>
			<div><dt>Adres</dt><dd <?php nwcs_edit_attr( 'contact', 'details', 'address' ); ?>><?php echo wk_multiline( (string) nwcs_field( 'contact', 'details', 'address' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?></dd></div>
		</dl>
		<?php wk_part( 'order-form' ); ?>
	</div>
</section>

<?php
get_footer();
