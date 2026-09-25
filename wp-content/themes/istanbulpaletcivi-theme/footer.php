<?php
/**
 * Alt bilgi ve mobilde ekranin altinda sabit Ara / WhatsApp cubugu.
 */

defined( 'ABSPATH' ) || exit;

$menu     = nwcs_rows( 'global', 'header', 'menu' );
$logo     = pc_logo();
$phone    = pc_phone();
$whatsapp = pc_whatsapp();
$email    = trim( (string) nwcs_field( 'global', 'header', 'email' ) );
$external = trim( (string) nwcs_field( 'global', 'footer', 'external_label' ) );
?>
</main>

<footer class="pc-footer">
	<div class="pc-wrap">
		<div class="pc-footer__grid">
			<div>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="pc-footer__logo"<?php nwcs_edit_attr( 'global', 'header', 'logo_image' ); ?>>
					<?php if ( $logo['url'] ) : ?>
						<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( nwcs_field( 'global', 'header', 'logo_text' ) ); ?>" width="205" height="40" loading="lazy" />
					<?php else : ?>
						<?php echo esc_html( nwcs_field( 'global', 'header', 'logo_text' ) ); ?>
					<?php endif; ?>
				</a>
				<p class="pc-footer__tagline" <?php nwcs_edit_attr( 'global', 'footer', 'tagline' ); ?>>
					<?php echo esc_html( nwcs_field( 'global', 'footer', 'tagline' ) ); ?>
				</p>
			</div>

			<div>
				<h2 class="pc-footer__title"<?php nwcs_edit_attr( 'global', 'footer', 'pages_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'pages_title' ) ); ?></h2>
				<ul class="pc-footer__list">
					<?php foreach ( $menu as $index => $item ) : ?>
						<li><a href="<?php echo esc_url( pc_link( $item['url'] ?? '' ) ); ?>"<?php nwcs_edit_attr( 'global', 'header', 'menu', (int) $index, 'label' ); ?>><?php echo esc_html( $item['label'] ?? '' ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h2 class="pc-footer__title"<?php nwcs_edit_attr( 'global', 'footer', 'products_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'products_title' ) ); ?></h2>
				<ul class="pc-footer__list">
					<?php foreach ( pc_products() as $product ) : ?>
						<li><a href="<?php echo esc_url( $product['url'] ); ?>"<?php nwcs_edit_attr( $product['key'], 'card', 'name' ); ?>><?php echo esc_html( $product['name'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h2 class="pc-footer__title"<?php nwcs_edit_attr( 'global', 'footer', 'contact_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'contact_title' ) ); ?></h2>
				<ul class="pc-footer__list">
					<li><a class="pc-num" href="<?php echo esc_url( $phone['url'] ); ?>"<?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>><?php echo esc_html( $phone['label'] ); ?></a></li>
					<li>
						<a class="pc-num" href="<?php echo esc_url( pc_link( nwcs_field( 'global', 'footer', 'mobile_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'mobile_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'global', 'footer', 'mobile_label' ) ); ?>
						</a>
					</li>
					<?php if ( $email ) : ?>
						<li><a href="<?php echo esc_url( 'mailto:' . $email ); ?>"<?php nwcs_edit_attr( 'global', 'header', 'email' ); ?>><?php echo esc_html( $email ); ?></a></li>
					<?php endif; ?>
					<li class="pc-footer__address" <?php nwcs_edit_attr( 'global', 'footer', 'address' ); ?>>
						<?php echo pc_multiline( (string) nwcs_field( 'global', 'footer', 'address' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					</li>
				</ul>
			</div>
		</div>

		<div class="pc-footer__bottom">
			<p <?php nwcs_edit_attr( 'global', 'footer', 'copyright' ); ?>>
				© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( nwcs_field( 'global', 'footer', 'copyright' ) ); ?>
			</p>
			<?php if ( $external ) : ?>
				<a href="<?php echo esc_url( pc_link( nwcs_field( 'global', 'footer', 'external_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'external_label' ); ?>>
					<?php echo esc_html( $external ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</footer>

<div class="pc-callbar">
	<a href="<?php echo esc_url( $phone['url'] ); ?>" class="pc-callbar__call"<?php nwcs_edit_attr( 'global', 'footer', 'call_label' ); ?>>
		<?php echo pc_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		<?php echo esc_html( nwcs_field( 'global', 'footer', 'call_label' ) ); ?>
	</a>
	<?php if ( $whatsapp ) : ?>
		<a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener" class="pc-callbar__wa"<?php nwcs_edit_attr( 'global', 'footer', 'wa_label' ); ?>>
			<?php echo pc_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			<?php echo esc_html( nwcs_field( 'global', 'footer', 'wa_label' ) ); ?>
		</a>
	<?php endif; ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
