<?php
/**
 * Alt bilgi ve mobilde sabit WhatsApp cubugu.
 */

defined( 'ABSPATH' ) || exit;

$whatsapp = wk_whatsapp();
$phone    = trim( (string) nwcs_field( 'global', 'header', 'phone_label' ) );
$email    = trim( (string) nwcs_field( 'global', 'header', 'email' ) );
?>
</main>

<footer class="wk-footer">
	<div class="wk-wrap wk-footer__grid">
		<div>
			<p class="wk-footer__brand"><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_text' ) ); ?></p>
			<p class="wk-footer__tagline" <?php nwcs_edit_attr( 'global', 'footer', 'tagline' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'tagline' ) ); ?></p>
		</div>
		<div>
			<h2 class="wk-footer__title">Seriler</h2>
			<ul class="wk-footer__list">
				<?php foreach ( wk_lines() as $line ) : ?>
					<li><a href="<?php echo esc_url( home_url( '/?seri=' . $line['slug'] . '#urunler' ) ); ?>"><?php echo esc_html( $line['label'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div>
			<h2 class="wk-footer__title">İletişim</h2>
			<ul class="wk-footer__list">
				<?php if ( $whatsapp ) : ?>
					<li><a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener">WhatsApp: <span class="wk-num"><?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?></span></a></li>
				<?php endif; ?>
				<?php if ( $phone ) : ?>
					<li><a class="wk-num" href="<?php echo esc_url( wk_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<li><a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
				<?php endif; ?>
				<li class="wk-footer__address" <?php nwcs_edit_attr( 'global', 'footer', 'address' ); ?>><?php echo wk_multiline( (string) nwcs_field( 'global', 'footer', 'address' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?></li>
			</ul>
		</div>
	</div>
	<div class="wk-wrap wk-footer__bottom">
		<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( nwcs_field( 'global', 'footer', 'copyright' ) ); ?></p>
		<a href="https://www.kocist.com.tr">kocist.com.tr</a>
	</div>
</footer>

<?php if ( $whatsapp ) : ?>
	<a class="wk-callbar" href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener">
		<?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		WhatsApp’tan sipariş verin
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
