<?php
/**
 * Ust menu. Logo yazisi ve rozet alanlardan gelir; logo gorseli yuklenirse
 * yazi logosunun yerini alir. Genis ekranda yanina grup (Koçist) logosu gelir.
 *
 * Genislik butcesi: lg'de menu satiri doldugu icin WhatsApp dugmesi lg'de
 * gizlenir, xl'de yalniz simge olarak doner; telefon yazisi 2xl'de gorunur.
 */

defined( 'ABSPATH' ) || exit;

$logo  = nwcs_image( 'global', 'header', 'logo_image', 'medium' );
$word  = (string) nwcs_field( 'global', 'header', 'logo_word' );
$rest  = (string) nwcs_field( 'global', 'header', 'logo_rest' );
$badge = (string) nwcs_field( 'global', 'header', 'logo_mark' );
$menu  = nwcs_rows( 'global', 'header', 'menu' );
$phone = array(
	'label' => (string) nwcs_field( 'global', 'header', 'phone_label' ),
	'url'   => kr_link( nwcs_field( 'global', 'header', 'phone_url' ) ),
);
$cta      = (string) nwcs_field( 'global', 'header', 'cta_label' );
$whatsapp = trim( (string) nwcs_field( 'global', 'header', 'whatsapp_url' ) );
$wa_label = (string) nwcs_field( 'global', 'header', 'whatsapp_label' );
$parent   = nwcs_image( 'global', 'header', 'parent_logo', 'medium' );
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

<a href="#icerik" class="btn btn--sm btn--mark sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60]">İçeriğe geç</a>

<header data-header class="sticky top-0 z-50 border-b border-line bg-paper">
	<div class="mx-auto flex h-[4.5rem] max-w-[78rem] items-center gap-6 px-5 md:px-8">

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-3 text-ink no-underline" <?php nwcs_edit_attr( 'global', 'header', 'logo_word' ); ?>>
			<?php if ( $logo['url'] ) : ?>
				<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( trim( $word . ' ' . $rest ) ); ?>" class="h-10 w-auto" />
			<?php else : ?>
				<?php // Yazi logosu: sablon harfli rozet + slab isim. ?>
				<span class="flex h-10 w-10 items-center justify-center bg-mark font-stencil text-[1.05rem] leading-none text-paper" aria-hidden="true"><?php echo esc_html( $badge ); ?></span>
				<span class="font-slab text-[1.4rem] font-bold leading-none tracking-tight">
					<?php echo esc_html( $word ); ?><span class="font-semibold text-muted"><?php echo esc_html( $rest ); ?></span>
				</span>
			<?php endif; ?>
		</a>

		<?php if ( $parent['url'] ) : ?>
			<a href="<?php echo esc_url( kr_link( nwcs_field( 'global', 'header', 'parent_url' ) ) ); ?>" target="_blank" rel="noopener"
				class="hidden shrink-0 border-l border-line pl-5 xl:block" <?php nwcs_edit_attr( 'global', 'header', 'parent_logo' ); ?>>
				<img src="<?php echo esc_url( $parent['url'] ); ?>" alt="<?php echo esc_attr( nwcs_field( 'global', 'header', 'parent_label' ) ); ?>" class="h-8 w-auto" />
			</a>
		<?php endif; ?>

		<nav class="ml-6 hidden items-center gap-7 lg:flex" aria-label="Ana menü" <?php nwcs_edit_attr( 'global', 'header', 'menu' ); ?>>
			<?php foreach ( $menu as $item ) :
				$url = (string) ( $item['url'] ?? '' );
				?>
				<a href="<?php echo esc_url( kr_link( $url ) ); ?>"
					class="navline text-[0.9688rem] font-semibold text-ink no-underline transition-colors hover:text-mark"
					<?php echo kr_is_current( $url ) ? 'aria-current="page"' : ( kr_is_within( $url ) ? 'data-section-current' : '' ); ?>>
					<?php echo esc_html( $item['label'] ?? '' ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<div class="ml-auto flex items-center gap-4">
			<a href="<?php echo esc_url( $phone['url'] ); ?>" class="tabular hidden font-semibold text-ink no-underline hover:text-mark 2xl:inline"
				<?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>>
				<?php echo esc_html( $phone['label'] ); ?>
			</a>

			<?php if ( $whatsapp ) : ?>
				<a href="<?php echo esc_url( kr_link( $whatsapp ) ); ?>" target="_blank" rel="noopener"
					class="btn btn--sm btn--whatsapp hidden md:inline-flex lg:hidden xl:inline-flex xl:aspect-square xl:px-0 2xl:aspect-auto 2xl:px-4"
					aria-label="<?php echo esc_attr( $wa_label ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'whatsapp_label' ); ?>>
					<?php nwcs_the_icon( 'whatsapp', 'shrink-0', 18 ); ?>
					<span class="xl:sr-only 2xl:not-sr-only"><?php echo esc_html( $wa_label ); ?></span>
				</a>
			<?php endif; ?>

			<a href="<?php echo esc_url( kr_quote_fallback_url() ); ?>" data-kr-quote class="btn btn--sm btn--mark hidden sm:inline-flex"
				<?php nwcs_edit_attr( 'global', 'header', 'cta_label' ); ?>>
				<?php echo esc_html( $cta ); ?>
			</a>

			<button type="button" data-nav-toggle aria-expanded="false" aria-controls="menu-mobil" aria-label="Menüyü aç"
				class="group/btn -mr-2 flex h-11 w-11 flex-col items-center justify-center gap-[5px] lg:hidden">
				<span class="block h-[2px] w-6 bg-ink transition-transform duration-300 group-aria-expanded/btn:translate-y-[7px] group-aria-expanded/btn:rotate-45"></span>
				<span class="block h-[2px] w-6 bg-ink transition-opacity duration-200 group-aria-expanded/btn:opacity-0"></span>
				<span class="block h-[2px] w-6 bg-ink transition-transform duration-300 group-aria-expanded/btn:-translate-y-[7px] group-aria-expanded/btn:-rotate-45"></span>
			</button>
		</div>
	</div>

	<div id="menu-mobil" data-nav-panel hidden class="border-t border-line bg-paper lg:hidden">
		<nav class="mx-auto max-w-[78rem] px-5 pb-6 pt-2 md:px-8" aria-label="Mobil menü">
			<ul>
				<?php foreach ( $menu as $item ) : ?>
					<li class="border-b border-line">
						<a href="<?php echo esc_url( kr_link( $item['url'] ?? '' ) ); ?>"
							class="block py-3.5 text-lg font-semibold text-ink no-underline aria-[current=page]:text-mark"
							<?php echo kr_is_current( (string) ( $item['url'] ?? '' ) ) ? 'aria-current="page"' : ''; ?>>
							<?php echo esc_html( $item['label'] ?? '' ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="mt-5 grid gap-3 sm:grid-cols-2">
				<a href="<?php echo esc_url( $phone['url'] ); ?>" class="btn btn--md btn--outline tabular"><?php echo esc_html( $phone['label'] ); ?></a>
				<?php if ( $whatsapp ) : ?>
					<a href="<?php echo esc_url( kr_link( $whatsapp ) ); ?>" target="_blank" rel="noopener" class="btn btn--md btn--whatsapp">
						<?php nwcs_the_icon( 'whatsapp', 'shrink-0', 18 ); ?>
						<?php echo esc_html( $wa_label ); ?>
					</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( kr_quote_fallback_url() ); ?>" data-kr-quote class="btn btn--md btn--mark <?php echo $whatsapp ? 'sm:col-span-2' : ''; ?>"><?php echo esc_html( $cta ); ?></a>
			</div>
			<?php if ( $parent['url'] ) : ?>
				<p class="mt-6 flex items-center gap-3 text-sm text-muted">
					<img src="<?php echo esc_url( $parent['url'] ); ?>" alt="" class="h-7 w-auto" />
					<?php echo esc_html( nwcs_field( 'global', 'header', 'parent_label' ) ); ?>
				</p>
			<?php endif; ?>
		</nav>
	</div>
</header>

<main id="icerik">
