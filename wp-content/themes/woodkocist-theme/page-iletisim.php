<?php
/**
 * Cozum Merkezi (/iletisim/): destek hatlari, e-posta, adresler, harita ve
 * talep formu (inc/requests.php).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$wa    = wk_whatsapp();
$phone = trim( (string) nwcs_field( 'global', 'header', 'phone_label' ) );
$email = trim( (string) nwcs_field( 'global', 'header', 'email' ) );
$lines = nwcs_rows( 'contact', 'details', 'lines' );
$map   = trim( (string) nwcs_field( 'contact', 'details', 'map' ) );
?>

<section class="wk-pagehead wk-pagehead--compact">
	<div class="wk-wrap">
		<h1 class="wk-hero__title" <?php nwcs_edit_attr( 'contact', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'head', 'title' ) ); ?></h1>
		<p class="wk-hero__lead" <?php nwcs_edit_attr( 'contact', 'head', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'head', 'lead' ) ); ?></p>
	</div>
</section>

<div class="wk-wrap wk-section wk-contact">
	<div class="wk-contact__info">
		<ul class="wk-lines-cards">
			<?php if ( $wa ) : ?>
				<li>
					<span class="wk-lines-cards__label" <?php nwcs_edit_attr( 'contact', 'labels', 'wa_line' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'labels', 'wa_line' ) ); ?></span>
					<a class="wk-num" href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" <?php nwcs_edit_attr( 'global', 'header', 'whatsapp_label' ); ?>><?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?></a>
				</li>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<li>
					<span class="wk-lines-cards__label" <?php nwcs_edit_attr( 'contact', 'labels', 'phone_line' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'labels', 'phone_line' ) ); ?></span>
					<a class="wk-num" href="<?php echo esc_url( wk_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>><?php echo wk_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><?php echo esc_html( $phone ); ?></a>
				</li>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<li>
					<span class="wk-lines-cards__label" <?php nwcs_edit_attr( 'contact', 'labels', 'email_line' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'labels', 'email_line' ) ); ?></span>
					<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'email' ); ?>><?php echo esc_html( $email ); ?></a>
				</li>
			<?php endif; ?>
		</ul>

		<?php if ( $lines ) : ?>
			<dl class="wk-lines-info" <?php nwcs_edit_attr( 'contact', 'details', 'lines' ); ?>>
				<?php foreach ( $lines as $index => $line ) : ?>
					<div><dt <?php nwcs_edit_attr( 'contact', 'details', 'lines', (int) $index, 'label' ); ?>><?php echo esc_html( $line['label'] ?? '' ); ?></dt><dd <?php nwcs_edit_attr( 'contact', 'details', 'lines', (int) $index, 'value' ); ?>><?php echo esc_html( $line['value'] ?? '' ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>

		<ul class="wk-quicklinks">
			<li><a href="<?php echo esc_url( wk_page_url( 'sss' ) ); ?>" <?php nwcs_edit_attr( 'contact', 'labels', 'quick_faq' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'labels', 'quick_faq' ) ); ?></a></li>
			<li><a href="<?php echo esc_url( wk_page_url( 'iptal-iade-kosullari' ) ); ?>" <?php nwcs_edit_attr( 'contact', 'labels', 'quick_returns' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'labels', 'quick_returns' ) ); ?></a></li>
			<li><a href="<?php echo esc_url( wk_page_url( 'odeme-teslimat' ) ); ?>" <?php nwcs_edit_attr( 'contact', 'labels', 'quick_payment' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'labels', 'quick_payment' ) ); ?></a></li>
			<li><a href="<?php echo esc_url( wk_page_url( 'ozel-uretim-talep-formu' ) ); ?>" <?php nwcs_edit_attr( 'contact', 'labels', 'quick_custom' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'labels', 'quick_custom' ) ); ?></a></li>
		</ul>
	</div>

	<div class="wk-contact__form">
		<h2 class="wk-h3" <?php nwcs_edit_attr( 'contact', 'labels', 'form_title' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'labels', 'form_title' ) ); ?></h2>
		<p class="wk-lead" <?php nwcs_edit_attr( 'contact', 'labels', 'form_desc' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'labels', 'form_desc' ) ); ?></p>
		<?php wk_part( 'request-form', array( 'kind' => 'iletisim' ) ); ?>
	</div>
</div>

<?php if ( '' !== $map ) : ?>
	<div class="wk-map">
		<iframe title="<?php echo esc_attr( $map ); ?> haritada" src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( $map ) . '&t=m&z=15&output=embed' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
	</div>
<?php endif; ?>

<?php
get_footer();
