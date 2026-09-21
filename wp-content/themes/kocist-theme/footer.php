<?php
/**
 * Footer. Degerler 'global' sayfasindan okunur.
 */

defined( 'ABSPATH' ) || exit;

$links = nwcs_rows( 'global', 'footer', 'links' );
?>
<footer class="k-footer" data-nwcs-section="footer">
	<div class="k-wrap">
		<div class="k-footer__grid">
			<div>
				<h3 <?php nwcs_edit_attr( 'global', 'footer', 'about_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'about_title' ) ); ?></h3>
				<p class="k-footer__about" <?php nwcs_edit_attr( 'global', 'footer', 'about_text' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'about_text' ) ); ?></p>
			</div>

			<div>
				<h3 <?php nwcs_edit_attr( 'global', 'footer', 'links_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'links_title' ) ); ?></h3>
				<ul class="k-footer__list">
					<?php foreach ( $links as $index => $link ) : ?>
						<li><a href="<?php echo esc_url( kocist_link( $link['url'] ?? '' ) ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'links', $index, 'label' ); ?>><?php echo esc_html( $link['label'] ?? '' ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h3 <?php nwcs_edit_attr( 'global', 'footer', 'contact_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'contact_title' ) ); ?></h3>
				<div class="k-footer__contact">
					<div <?php nwcs_edit_attr( 'global', 'footer', 'address' ); ?>>
						<?php nwcs_the_icon( nwcs_field( 'global', 'footer', 'address_icon' ), 'k-icon', 18 ); ?>
						<span><?php echo esc_html( nwcs_field( 'global', 'footer', 'address' ) ); ?></span>
					</div>
					<a href="<?php echo esc_url( kocist_link( nwcs_field( 'global', 'footer', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'phone_label' ); ?>>
						<?php nwcs_the_icon( 'phone', 'k-icon', 18 ); ?>
						<span><?php echo esc_html( nwcs_field( 'global', 'footer', 'phone_label' ) ); ?></span>
					</a>
					<a href="<?php echo esc_url( kocist_link( nwcs_field( 'global', 'footer', 'email_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'email_label' ); ?>>
						<?php nwcs_the_icon( 'mail', 'k-icon', 18 ); ?>
						<span><?php echo esc_html( nwcs_field( 'global', 'footer', 'email_label' ) ); ?></span>
					</a>
				</div>
			</div>
		</div>

		<div class="k-footer__bottom">
			<span <?php nwcs_edit_attr( 'global', 'footer', 'copyright' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'copyright' ) ); ?></span>
			<span <?php nwcs_edit_attr( 'global', 'footer', 'demo_note' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'demo_note' ) ); ?></span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
