<?php
/**
 * Ust menu. Tum sayfalarda aynidir; icerik global.header bileseninden gelir.
 *
 * "Urunlerimiz" baglantisinin yaninda bir acilir panel vardir: bes urun
 * gorselleriyle listelenir. Panel, adresi Urunlerimiz sayfasinin yolu olan
 * menu ogesine baglanir; menu metni degisse de panel yerinde kalir.
 */

defined( 'ABSPATH' ) || exit;

$logo      = nwcs_image( 'global', 'header', 'logo_image', 'medium' );
$logo_text = (string) nwcs_field( 'global', 'header', 'logo_text' );
$menu      = nwcs_rows( 'global', 'header', 'menu' );
$products  = ip_products();
$hub_path  = ip_link_path( (string) ( ip_manifest()['pages']['products']['path'] ?? '/urunlerimiz/' ) );
$phone     = array(
	'label' => (string) nwcs_field( 'global', 'header', 'phone_label' ),
	'url'   => ip_link( nwcs_field( 'global', 'header', 'phone_url' ) ),
);
$whatsapp  = trim( (string) nwcs_field( 'global', 'header', 'whatsapp_url' ) );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-paper text-ink antialiased' ); ?>>
<?php wp_body_open(); ?>

<a href="#icerik" class="btn btn--sm btn--solid sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[60]">
	İçeriğe geç
</a>

<header data-header data-stuck="false"
	class="group fixed inset-x-0 top-0 z-50 border-b border-transparent bg-sheet transition-[border-color] duration-300 data-[stuck=true]:border-line">

	<div class="mx-auto flex h-20 max-w-[80rem] items-center gap-6 px-5 transition-[height] duration-300 ease-out group-data-[stuck=true]:h-16 md:px-8">

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="shrink-0" <?php nwcs_edit_attr( 'global', 'header', 'logo_image' ); ?>>
			<?php if ( $logo['url'] ) : ?>
				<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo_text ); ?>"
					width="160" height="51"
					class="h-10 w-auto transition-[height] duration-300 ease-out group-data-[stuck=true]:h-8" />
			<?php else : ?>
				<span class="font-display text-2xl font-bold text-ink"><?php echo esc_html( $logo_text ); ?></span>
			<?php endif; ?>
		</a>

		<nav class="ml-4 hidden items-center gap-8 lg:flex" aria-label="Ana menü" <?php nwcs_edit_attr( 'global', 'header', 'menu' ); ?>>
			<?php foreach ( $menu as $item ) :
				$url     = (string) ( $item['url'] ?? '' );
				$current = ip_is_current( $url );
				$within  = ! $current && ip_is_within( $url );
				$is_hub  = $products && ip_link_path( $url ) === $hub_path;
				$link    = sprintf(
					'<a href="%1$s" class="navline text-[0.9375rem] font-medium text-ink no-underline transition-colors hover:text-indigo" %2$s>%3$s</a>',
					esc_url( ip_link( $url ) ),
					$current ? 'aria-current="page"' : ( $within ? 'data-section-current' : '' ),
					esc_html( $item['label'] ?? '' )
				);

				if ( ! $is_hub ) {
					echo $link; // phpcs:ignore WordPress.Security.EscapingOutput -- yukarida kacirildi.
					continue;
				}
				?>
				<div class="relative flex items-center gap-1" data-dropdown>
					<?php echo $link; // phpcs:ignore WordPress.Security.EscapingOutput -- yukarida kacirildi. ?>

					<button type="button" data-dropdown-toggle aria-expanded="false" aria-controls="menu-urunler"
						class="group/chev -mr-2 flex h-8 w-8 items-center justify-center rounded-[3px] text-steel transition-colors hover:text-indigo aria-expanded:text-indigo"
						aria-label="<?php echo esc_attr( sprintf( '%s alt menüsü', $item['label'] ?? '' ) ); ?>">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
							class="transition-transform duration-200 group-aria-expanded/chev:rotate-180">
							<path d="M6 9l6 6 6-6" />
						</svg>
					</button>

					<div id="menu-urunler" data-dropdown-panel data-open="false"
						class="invisible absolute left-1/2 top-full w-[40rem] pt-6 -translate-x-1/2 translate-y-1 opacity-0 transition duration-200 ease-out data-[open=true]:visible data-[open=true]:translate-y-0 data-[open=true]:opacity-100">
						<?php // Tek golge burada: sayfanin ustunde suzulen katman oldugunu gostermek icin. ?>
						<div class="sheet shadow-[0_18px_40px_-16px_rgb(16_17_46/0.35)]">
							<ul class="grid grid-cols-2 gap-px bg-line">
								<?php foreach ( $products as $product ) : ?>
									<li class="bg-sheet">
										<a href="<?php echo esc_url( $product['url'] ); ?>"
											class="flex items-center gap-4 p-4 text-ink no-underline transition-colors hover:bg-indigo-wash aria-[current=page]:bg-indigo-wash"
											<?php echo ip_is_current( $product['path'] ) ? 'aria-current="page"' : ''; ?>>
											<span class="shot aspect-[4/3] w-20 shrink-0 border border-line">
												<?php echo ip_image_tag( $product['image'], '', $product['name'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
											</span>
											<span class="min-w-0">
												<span class="block font-semibold"><?php echo esc_html( $product['name'] ); ?></span>
												<span class="dimtag mt-1 text-[0.8125rem] font-medium text-steel"><?php echo esc_html( $product['size'] ); ?></span>
											</span>
										</a>
									</li>
								<?php endforeach; ?>

								<?php // Tek sayida urunde son hucre bos kalmasin: tum urunler baglantisi. ?>
								<li class="bg-sheet <?php echo count( $products ) % 2 ? '' : 'col-span-2'; ?>">
									<a href="<?php echo esc_url( ip_link( $url ) ); ?>"
										class="flex h-full items-center p-4 font-semibold text-indigo no-underline transition-colors hover:bg-indigo-wash"
										<?php nwcs_edit_attr( 'global', 'header', 'all_products' ); ?>>
										<?php echo esc_html( nwcs_field( 'global', 'header', 'all_products' ) ); ?>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</nav>

		<div class="ml-auto flex items-center gap-2">
			<a href="<?php echo esc_url( $phone['url'] ); ?>"
				class="btn btn--sm btn--outline hidden md:inline-flex"
				aria-label="<?php echo esc_attr( sprintf( 'Bizi arayın: %s', $phone['label'] ) ); ?>"
				<?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>>
				<?php nwcs_the_icon( 'phone', 'shrink-0', 18 ); ?>
				<span class="tabular hidden xl:inline"><?php echo esc_html( $phone['label'] ); ?></span>
			</a>

			<?php if ( $whatsapp ) : ?>
				<a href="<?php echo esc_url( ip_link( $whatsapp ) ); ?>" target="_blank" rel="noopener"
					class="btn btn--sm btn--whatsapp hidden md:inline-flex"
					<?php nwcs_edit_attr( 'global', 'header', 'whatsapp_label' ); ?>>
					<?php nwcs_the_icon( 'whatsapp', 'shrink-0', 18 ); ?>
					<?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?>
				</a>
			<?php endif; ?>

			<button type="button" data-nav-toggle aria-expanded="false" aria-controls="menu-mobil"
				class="group/btn -mr-2 flex h-11 w-11 flex-col items-center justify-center gap-[5px] lg:hidden"
				aria-label="Menüyü aç">
				<span class="block h-[2px] w-6 bg-ink transition-transform duration-300 ease-out group-aria-expanded/btn:translate-y-[7px] group-aria-expanded/btn:rotate-45"></span>
				<span class="block h-[2px] w-6 bg-ink transition-opacity duration-200 group-aria-expanded/btn:opacity-0"></span>
				<span class="block h-[2px] w-6 bg-ink transition-transform duration-300 ease-out group-aria-expanded/btn:-translate-y-[7px] group-aria-expanded/btn:-rotate-45"></span>
			</button>
		</div>
	</div>

	<?php // Mobil panel: urunler akordeon olarak acilir. ?>
	<div id="menu-mobil" data-nav-panel data-open="false"
		class="invisible max-h-0 overflow-y-auto border-t border-line bg-sheet opacity-0 transition-[max-height,opacity,visibility] duration-300 ease-out data-[open=true]:visible data-[open=true]:max-h-[calc(100dvh-4rem)] data-[open=true]:opacity-100 lg:hidden">
		<nav class="mx-auto max-w-[80rem] px-5 pb-6 pt-2 md:px-8" aria-label="Mobil menü">
			<ul>
				<?php foreach ( $menu as $index => $item ) :
					$url    = (string) ( $item['url'] ?? '' );
					$is_hub = $products && ip_link_path( $url ) === $hub_path;
					?>
					<li class="border-b border-line">
						<div class="flex items-center justify-between">
							<a href="<?php echo esc_url( ip_link( $url ) ); ?>"
								class="block flex-1 py-3.5 text-lg font-medium text-ink no-underline aria-[current=page]:text-indigo"
								<?php echo ip_is_current( $url ) ? 'aria-current="page"' : ''; ?>>
								<?php echo esc_html( $item['label'] ?? '' ); ?>
							</a>

							<?php if ( $is_hub ) : ?>
								<button type="button" data-accordion-toggle aria-expanded="<?php echo ip_is_within( $url ) ? 'true' : 'false'; ?>" aria-controls="mobil-urunler"
									class="group/chev flex h-11 w-11 items-center justify-center text-steel aria-expanded:text-indigo"
									aria-label="<?php echo esc_attr( sprintf( '%s alt menüsü', $item['label'] ?? '' ) ); ?>">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
										class="transition-transform duration-200 group-aria-expanded/chev:rotate-180">
										<path d="M6 9l6 6 6-6" />
									</svg>
								</button>
							<?php endif; ?>
						</div>

						<?php if ( $is_hub ) : ?>
							<ul id="mobil-urunler" class="pb-3" <?php echo ip_is_within( $url ) ? '' : 'hidden'; ?>>
								<?php foreach ( $products as $product ) : ?>
									<li>
										<a href="<?php echo esc_url( $product['url'] ); ?>"
											class="flex items-center gap-3 py-2 text-ink no-underline aria-[current=page]:text-indigo"
											<?php echo ip_is_current( $product['path'] ) ? 'aria-current="page"' : ''; ?>>
											<span class="shot aspect-[4/3] w-14 shrink-0 border border-line">
												<?php echo ip_image_tag( $product['image'], '', $product['name'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
											</span>
											<span class="font-medium"><?php echo esc_html( $product['name'] ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="mt-6 grid gap-3 sm:grid-cols-2">
				<a href="<?php echo esc_url( $phone['url'] ); ?>" class="btn btn--md btn--outline">
					<?php nwcs_the_icon( 'phone', 'shrink-0', 18 ); ?>
					<span class="tabular"><?php echo esc_html( $phone['label'] ); ?></span>
				</a>

				<?php if ( $whatsapp ) : ?>
					<a href="<?php echo esc_url( ip_link( $whatsapp ) ); ?>" target="_blank" rel="noopener" class="btn btn--md btn--whatsapp">
						<?php nwcs_the_icon( 'whatsapp', 'shrink-0', 18 ); ?>
						<?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		</nav>
	</div>
</header>

<main id="icerik" class="pt-20">
