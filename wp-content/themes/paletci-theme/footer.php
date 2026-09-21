<?php
/**
 * Paletci footer.
 */

defined( 'ABSPATH' ) || exit;

$links = nwcs_rows( 'global', 'footer', 'links' );
?>
<footer class="p-footer">
	<div class="p-wrap">
		<div class="p-footer__grid">
			<div>
				<h3><?php echo esc_html( nwcs_field( 'global', 'footer', 'about_title' ) ); ?></h3>
				<p><?php echo esc_html( nwcs_field( 'global', 'footer', 'about_text' ) ); ?></p>
			</div>

			<div>
				<h3><?php echo esc_html( nwcs_field( 'global', 'footer', 'links_title' ) ); ?></h3>
				<ul class="p-footer__list">
					<?php foreach ( $links as $link ) : ?>
						<li><a href="<?php echo esc_url( paletci_link( $link['url'] ?? '' ) ); ?>"><?php echo esc_html( $link['label'] ?? '' ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h3><?php echo esc_html( nwcs_field( 'global', 'footer', 'contact_title' ) ); ?></h3>
				<div class="p-footer__contact">
					<span><?php echo esc_html( nwcs_field( 'global', 'footer', 'address' ) ); ?></span>
					<a href="<?php echo esc_url( paletci_link( nwcs_field( 'global', 'footer', 'phone_url' ) ) ); ?>">
						<?php nwcs_the_icon( 'phone', 'p-icon', 18 ); ?>
						<span><?php echo esc_html( nwcs_field( 'global', 'footer', 'phone_label' ) ); ?></span>
					</a>
					<a href="<?php echo esc_url( paletci_link( nwcs_field( 'global', 'footer', 'mobile_url' ) ) ); ?>">
						<?php nwcs_the_icon( 'whatsapp', 'p-icon', 18 ); ?>
						<span><?php echo esc_html( nwcs_field( 'global', 'footer', 'mobile_label' ) ); ?></span>
					</a>
					<a href="<?php echo esc_url( paletci_link( nwcs_field( 'global', 'footer', 'email_url' ) ) ); ?>">
						<?php nwcs_the_icon( 'mail', 'p-icon', 18 ); ?>
						<span><?php echo esc_html( nwcs_field( 'global', 'footer', 'email_label' ) ); ?></span>
					</a>
				</div>
			</div>
		</div>

		<div class="p-footer__bottom">
			<span><?php echo esc_html( nwcs_field( 'global', 'footer', 'copyright' ) ); ?></span>
			<span><?php echo esc_html( nwcs_field( 'global', 'footer', 'demo_note' ) ); ?></span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
