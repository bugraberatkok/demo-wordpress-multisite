<?php
/**
 * Alt bilgi. Tum sayfalarda aynidir; icerik global.footer bileseninden gelir.
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="sp-footer">
	<div class="sp-wrap">
		<div class="sp-footer__grid">
			<div class="sp-footer__about">
				<p class="sp-footer__name" <?php nwcs_edit_attr( 'global', 'header', 'logo_text' ); ?>><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_text' ) ); ?></p>
				<p <?php nwcs_edit_attr( 'global', 'footer', 'about_text' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'about_text' ) ); ?></p>
			</div>

			<nav class="sp-footer__nav" aria-label="Alt menü" <?php nwcs_edit_attr( 'global', 'footer', 'links' ); ?>>
				<ul>
					<?php foreach ( nwcs_rows( 'global', 'footer', 'links' ) as $index => $link ) : ?>
						<li><a href="<?php echo esc_url( sanayi_palet_link( $link['url'] ?? '' ) ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'links', (int) $index, 'label' ); ?>><?php echo esc_html( $link['label'] ?? '' ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<address class="sp-footer__contact">
				<p class="sp-footer__address" <?php nwcs_edit_attr( 'global', 'footer', 'address' ); ?>>
					<?php sanayi_palet_icon( 'pin', 16 ); ?>
					<span><?php echo nl2br( esc_html( nwcs_field( 'global', 'footer', 'address' ) ) ); ?></span>
				</p>
				<p>
					<a href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'global', 'footer', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'phone_label' ); ?>>
						<?php sanayi_palet_icon( 'phone', 16 ); ?><?php echo esc_html( nwcs_field( 'global', 'footer', 'phone_label' ) ); ?>
					</a>
				</p>
				<p>
					<a href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'global', 'footer', 'whatsapp_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'whatsapp_label' ); ?>>
						<?php sanayi_palet_icon( 'whatsapp', 16 ); ?><?php echo esc_html( nwcs_field( 'global', 'footer', 'whatsapp_label' ) ); ?>
					</a>
				</p>
				<p>
					<a href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'global', 'footer', 'email_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'email_label' ); ?>>
						<?php sanayi_palet_icon( 'mail', 16 ); ?><?php echo esc_html( nwcs_field( 'global', 'footer', 'email_label' ) ); ?>
					</a>
				</p>
			</address>
		</div>

		<p class="sp-footer__legal" <?php nwcs_edit_attr( 'global', 'footer', 'copyright' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'copyright' ) ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
