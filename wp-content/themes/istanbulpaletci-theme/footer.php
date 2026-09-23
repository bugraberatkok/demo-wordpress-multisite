<?php
/**
 * Alt bilgi. Tum sayfalarda aynidir; icerik global.footer bileseninden gelir.
 * Sayfa ve urun listeleri ust menuyle ayni kaynaktan okunur.
 */

defined( 'ABSPATH' ) || exit;

$menu     = nwcs_rows( 'global', 'header', 'menu' );
$logo     = nwcs_image( 'global', 'footer', 'logo_image', 'medium' );
$external = trim( (string) nwcs_field( 'global', 'footer', 'external_label' ) );

$heading = 'text-[0.8125rem] font-semibold text-sheet/55';
$link    = 'text-sheet/85 no-underline transition-colors hover:text-sheet';
?>
</main>

<footer class="on-dark mt-24 bg-night text-sheet/80 md:mt-32">
	<div class="mx-auto max-w-[80rem] px-5 pb-10 pt-16 md:px-8 md:pt-20">

		<div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1.2fr]">

			<div>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-block" <?php nwcs_edit_attr( 'global', 'footer', 'logo_image' ); ?>>
					<?php if ( $logo['url'] ) : ?>
						<img src="<?php echo esc_url( $logo['url'] ); ?>" width="160" height="51" loading="lazy"
							alt="<?php echo esc_attr( nwcs_field( 'global', 'header', 'logo_text' ) ); ?>" class="h-10 w-auto" />
					<?php else : ?>
						<span class="font-display text-2xl font-bold text-sheet"><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_text' ) ); ?></span>
					<?php endif; ?>
				</a>

				<p class="mt-6 max-w-[22rem] text-[0.9375rem] leading-relaxed text-sheet/70" <?php nwcs_edit_attr( 'global', 'footer', 'tagline' ); ?>>
					<?php echo esc_html( nwcs_field( 'global', 'footer', 'tagline' ) ); ?>
				</p>
			</div>

			<div>
				<h2 class="<?php echo esc_attr( $heading ); ?> font-body" <?php nwcs_edit_attr( 'global', 'footer', 'pages_title' ); ?>>
					<?php echo esc_html( nwcs_field( 'global', 'footer', 'pages_title' ) ); ?>
				</h2>
				<ul class="mt-5 space-y-2.5">
					<?php foreach ( $menu as $item ) : ?>
						<li>
							<a href="<?php echo esc_url( ip_link( $item['url'] ?? '' ) ); ?>" class="<?php echo esc_attr( $link ); ?>">
								<?php echo esc_html( $item['label'] ?? '' ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h2 class="<?php echo esc_attr( $heading ); ?> font-body" <?php nwcs_edit_attr( 'global', 'footer', 'products_title' ); ?>>
					<?php echo esc_html( nwcs_field( 'global', 'footer', 'products_title' ) ); ?>
				</h2>
				<ul class="mt-5 space-y-2.5">
					<?php foreach ( ip_products() as $product ) : ?>
						<li>
							<a href="<?php echo esc_url( $product['url'] ); ?>" class="<?php echo esc_attr( $link ); ?>">
								<?php echo esc_html( $product['name'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h2 class="<?php echo esc_attr( $heading ); ?> font-body" <?php nwcs_edit_attr( 'global', 'footer', 'contact_title' ); ?>>
					<?php echo esc_html( nwcs_field( 'global', 'footer', 'contact_title' ) ); ?>
				</h2>
				<ul class="mt-5 space-y-2.5">
					<?php foreach ( array( 'phone', 'mobile', 'email' ) as $key ) : ?>
						<li>
							<a href="<?php echo esc_url( ip_link( nwcs_field( 'global', 'footer', $key . '_url' ) ) ); ?>"
								class="tabular <?php echo esc_attr( $link ); ?>"
								<?php nwcs_edit_attr( 'global', 'footer', $key . '_label' ); ?>>
								<?php echo esc_html( nwcs_field( 'global', 'footer', $key . '_label' ) ); ?>
							</a>
						</li>
					<?php endforeach; ?>
					<li class="pt-2 text-[0.9375rem] leading-relaxed text-sheet/65" <?php nwcs_edit_attr( 'global', 'footer', 'address' ); ?>>
						<?php echo ip_multiline( nwcs_field( 'global', 'footer', 'address' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					</li>
				</ul>
			</div>
		</div>

		<div class="mt-16 flex flex-col gap-3 border-t border-sheet/12 pt-6 text-sm text-sheet/50 md:flex-row md:items-center md:justify-between">
			<p <?php nwcs_edit_attr( 'global', 'footer', 'copyright' ); ?>>
				© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( nwcs_field( 'global', 'footer', 'copyright' ) ); ?>
			</p>

			<?php if ( $external ) : ?>
				<a href="<?php echo esc_url( ip_link( nwcs_field( 'global', 'footer', 'external_url' ) ) ); ?>"
					class="text-indigo-tint no-underline transition-colors hover:text-sheet"
					<?php nwcs_edit_attr( 'global', 'footer', 'external_label' ); ?>>
					<?php echo esc_html( $external ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
