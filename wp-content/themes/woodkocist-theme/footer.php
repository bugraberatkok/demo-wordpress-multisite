<?php
/**
 * Alt bilgi: marka seridi, marka + iletisim, koleksiyon, kurumsal ve yasal
 * sayfalar; mobilde sabit WhatsApp cubugu.
 */

defined( 'ABSPATH' ) || exit;

$whatsapp = wk_whatsapp();
// Urun sayfasinda mobil cubuk urun kodlu hazir mesajla acilir.
$bar_link = ! empty( $GLOBALS['wk_current_product'] ) ? wk_whatsapp( wk_order_text( $GLOBALS['wk_current_product'] ) ) : $whatsapp;
$phone    = trim( (string) nwcs_field( 'global', 'header', 'phone_label' ) );
$email    = trim( (string) nwcs_field( 'global', 'header', 'email' ) );
$band     = trim( (string) nwcs_field( 'global', 'footer', 'band' ) );
$hide_bar = is_page( array( 'sepet', 'odeme' ) );
?>
</main>

<footer class="wk-footer">
	<?php if ( '' !== $band ) : ?>
		<p class="wk-footer__band" <?php nwcs_edit_attr( 'global', 'footer', 'band' ); ?>><?php echo esc_html( $band ); ?></p>
	<?php endif; ?>

	<div class="wk-wrap wk-footer__grid">
		<div class="wk-footer__about">
			<p class="wk-footer__brand wk-mark"><?php echo wk_brand_mark(); // phpcs:ignore WordPress.Security.EscapingOutput -- ogeler kacirildi. ?></p>
			<p class="wk-footer__tagline" <?php nwcs_edit_attr( 'global', 'footer', 'tagline' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'tagline' ) ); ?></p>
			<ul class="wk-footer__contact">
				<?php if ( $whatsapp ) : ?>
					<li><?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener">WhatsApp: <span class="wk-num"><?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?></span></a></li>
				<?php endif; ?>
				<?php if ( $phone ) : ?>
					<li><?php echo wk_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><a class="wk-num" href="<?php echo esc_url( wk_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<li><span class="wk-footer__at" aria-hidden="true">@</span><a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
				<?php endif; ?>
				<li class="wk-footer__address" <?php nwcs_edit_attr( 'global', 'footer', 'address' ); ?>><?php echo wk_multiline( (string) nwcs_field( 'global', 'footer', 'address' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?></li>
			</ul>
		</div>

		<nav aria-labelledby="wk-f-collection">
			<h2 id="wk-f-collection" class="wk-footer__title">Koleksiyon</h2>
			<ul class="wk-footer__list">
				<?php foreach ( wk_category_tree() as $line ) : ?>
					<li><a href="<?php echo esc_url( $line['url'] ); ?>"><?php echo esc_html( $line['label'] ); ?></a></li>
				<?php endforeach; ?>
				<li><a href="<?php echo esc_url( wk_shop_url() ); ?>">Tüm ürünler</a></li>
			</ul>
		</nav>

		<?php if ( wk_corporate_pages() ) : ?>
			<nav aria-labelledby="wk-f-corporate">
				<h2 id="wk-f-corporate" class="wk-footer__title">Kurumsal</h2>
				<ul class="wk-footer__list">
					<?php foreach ( wk_corporate_pages() as $page ) : ?>
						<li><a href="<?php echo esc_url( $page['url'] ); ?>"><?php echo esc_html( $page['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<?php if ( wk_legal_pages() ) : ?>
			<nav aria-labelledby="wk-f-legal">
				<h2 id="wk-f-legal" class="wk-footer__title">Çözüm Merkezi</h2>
				<ul class="wk-footer__list">
					<?php foreach ( wk_legal_pages() as $page ) : ?>
						<li><a href="<?php echo esc_url( $page['url'] ); ?>"><?php echo esc_html( $page['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	</div>

	<div class="wk-wrap wk-footer__bottom">
		<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( nwcs_field( 'global', 'footer', 'copyright' ) ); ?></p>
		<a href="https://www.kocist.com.tr">kocist.com.tr</a>
	</div>
</footer>

<?php if ( $whatsapp && ! $hide_bar ) : ?>
	<a class="wk-callbar" href="<?php echo esc_url( $bar_link ); ?>" target="_blank" rel="noopener">
		<?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		WhatsApp’tan sorun
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
