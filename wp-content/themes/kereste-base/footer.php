<?php
/**
 * Alt bilgi: firma, sayfalar, urunler, iletisim ve kardes siteler.
 * Kardes siteler agin birbirine baglandigi yer; kuzen site en basta.
 */

defined( 'ABSPATH' ) || exit;

$menu   = nwcs_rows( 'global', 'header', 'menu' );
$family = nwcs_rows( 'global', 'footer', 'family' );
$phone  = (string) nwcs_field( 'global', 'header', 'phone_label' );
$email  = (string) nwcs_field( 'global', 'footer', 'email' );

$heading = 'font-sans text-[0.8125rem] font-bold text-paper/55';
$link    = 'text-paper/85 no-underline transition-colors hover:text-paper';
?>
</main>

<footer class="mt-24 bg-ink text-paper/80 md:mt-32">
	<div class="mx-auto max-w-[78rem] px-5 pb-10 pt-16 md:px-8">

		<div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
			<div>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-block no-underline" <?php nwcs_edit_attr( 'global', 'header', 'logo_word' ); ?>>
					<?php kr_logo( 'dark' ); ?>
				</a>
				<p class="mt-5 max-w-[22rem] text-[0.9375rem] leading-relaxed text-paper/70" <?php nwcs_edit_attr( 'global', 'footer', 'tagline' ); ?>>
					<?php echo esc_html( nwcs_field( 'global', 'footer', 'tagline' ) ); ?>
				</p>
			</div>

			<div>
				<h2 class="<?php echo esc_attr( $heading ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'pages_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'pages_title' ) ); ?></h2>
				<ul class="mt-4 space-y-2">
					<?php foreach ( $menu as $index => $item ) : ?>
						<li><a href="<?php echo esc_url( kr_link( $item['url'] ?? '' ) ); ?>" class="<?php echo esc_attr( $link ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'menu', (int) $index, 'label' ); ?>><?php echo esc_html( $item['label'] ?? '' ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h2 class="<?php echo esc_attr( $heading ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'products_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'products_title' ) ); ?></h2>
				<ul class="mt-4 space-y-2">
					<?php foreach ( kr_products() as $product ) : ?>
						<li><a href="<?php echo esc_url( $product['url'] ); ?>" class="<?php echo esc_attr( $link ); ?>" <?php nwcs_edit_attr( $product['key'], 'card', 'name' ); ?>><?php echo esc_html( $product['name'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h2 class="<?php echo esc_attr( $heading ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'contact_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'contact_title' ) ); ?></h2>
				<ul class="mt-4 space-y-2 text-[0.9375rem]">
					<li><a href="<?php echo esc_url( kr_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>" class="tabular <?php echo esc_attr( $link ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>><?php echo esc_html( $phone ); ?></a></li>
					<?php $mobile = trim( (string) nwcs_field( 'global', 'header', 'mobile_label' ) ); ?>
					<?php if ( '' !== $mobile ) : ?>
						<li><a href="<?php echo esc_url( kr_link( nwcs_field( 'global', 'header', 'mobile_url' ) ) ); ?>" class="tabular <?php echo esc_attr( $link ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'mobile_label' ); ?>><?php echo esc_html( $mobile ); ?></a></li>
					<?php endif; ?>
					<?php if ( '' !== trim( $email ) ) : ?>
						<li><a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="<?php echo esc_attr( $link ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'email' ); ?>><?php echo esc_html( $email ); ?></a></li>
					<?php endif; ?>
					<li class="leading-relaxed text-paper/65" <?php nwcs_edit_attr( 'global', 'footer', 'address' ); ?>><?php echo kr_multiline( nwcs_field( 'global', 'footer', 'address' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?></li>
					<li class="tabular text-paper/65" <?php nwcs_edit_attr( 'global', 'footer', 'hours' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'hours' ) ); ?></li>
				</ul>
			</div>
		</div>

		<?php if ( $family ) : ?>
			<div class="mt-14 border-t border-paper/12 pt-8" <?php nwcs_edit_attr( 'global', 'footer', 'family' ); ?>>
				<h2 class="<?php echo esc_attr( $heading ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'family_title' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'family_title' ) ); ?></h2>
				<ul class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2 lg:grid-cols-4">
					<?php foreach ( $family as $index => $site ) : ?>
						<li>
							<a href="<?php echo esc_url( kr_link( $site['url'] ?? '' ) ); ?>" class="font-semibold <?php echo esc_attr( $link ); ?>" <?php nwcs_edit_attr( 'global', 'footer', 'family', (int) $index, 'label' ); ?>><?php echo esc_html( $site['label'] ?? '' ); ?></a>
							<span class="block text-sm text-paper/55" <?php nwcs_edit_attr( 'global', 'footer', 'family', (int) $index, 'note' ); ?>><?php echo esc_html( $site['note'] ?? '' ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<p class="mt-12 border-t border-paper/12 pt-6 text-sm text-paper/50" <?php nwcs_edit_attr( 'global', 'footer', 'copyright' ); ?>>
			© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( nwcs_field( 'global', 'footer', 'copyright' ) ); ?>
		</p>
	</div>
</footer>

<?php get_template_part( 'template-parts/quote-dialog' ); ?>
<?php
// Buyutme penceresi yalnizca urun sayfalarinda (galeri orada).
if ( kr_current_product_key() ) {
	get_template_part( 'template-parts/lightbox' );
}
?>

<?php wp_footer(); ?>
</body>
</html>
