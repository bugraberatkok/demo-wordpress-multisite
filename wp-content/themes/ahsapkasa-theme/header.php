<?php
/**
 * Ust menu. Tum sayfalarda aynidir; icerik global.header bileseninden gelir.
 */

defined( 'ABSPATH' ) || exit;

$logo    = nwcs_image( 'global', 'header', 'logo_image', 'medium' );
$menu    = nwcs_rows( 'global', 'header', 'menu' );
$delays  = array( 'delay-[40ms]', 'delay-[90ms]', 'delay-[140ms]', 'delay-[190ms]', 'delay-[240ms]', 'delay-[290ms]' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-bone text-ink antialiased' ); ?>>
<?php wp_body_open(); ?>

<a href="#icerik" class="btn btn--sm btn--solid sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[60]">
	İçeriğe geç
</a>

<header
	data-header
	data-stuck="false"
	class="group fixed inset-x-0 top-0 z-50 bg-bone transition-shadow duration-300 ease-out data-[stuck=true]:shadow-[0_1px_0_0_var(--color-dust)]">

	<div class="mx-auto flex h-[88px] max-w-[76rem] items-center justify-between gap-8 px-6 transition-[height] duration-300 ease-out group-data-[stuck=true]:h-[64px]">

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3" <?php nwcs_edit_attr( 'global', 'header', 'logo_text' ); ?>>
			<?php if ( ! empty( $logo['url'] ) ) : ?>
				<?php // Logo gorseli zaten firma adini tasiyor; yazi tekrarlanmaz. ?>
				<img src="<?php echo esc_url( $logo['url'] ); ?>"
					alt="<?php echo esc_attr( trim( nwcs_field( 'global', 'header', 'logo_text' ) . ' ' . nwcs_field( 'global', 'header', 'logo_sub' ) ) ); ?>"
					class="h-11 w-auto transition-all duration-300 ease-out group-data-[stuck=true]:h-8" />
			<?php else : ?>
				<span aria-hidden="true" class="flex h-9 w-9 items-center justify-center rounded-sm bg-forest transition-all duration-300 ease-out group-data-[stuck=true]:h-7 group-data-[stuck=true]:w-7">
					<span class="h-3 w-3 rounded-full bg-timber-soft"></span>
				</span>

				<span class="leading-none">
					<span class="block font-display text-lg font-semibold tracking-tight transition-all duration-300 ease-out group-data-[stuck=true]:text-base">
						<?php echo esc_html( nwcs_field( 'global', 'header', 'logo_text' ) ); ?>
					</span>
					<span class="mt-1 block overflow-hidden font-display text-xs tracking-[0.16em] text-moss opacity-100 transition-all duration-300 ease-out group-data-[stuck=true]:mt-0 group-data-[stuck=true]:max-h-0 group-data-[stuck=true]:opacity-0"
						<?php nwcs_edit_attr( 'global', 'header', 'logo_sub' ); ?>>
						<?php echo esc_html( nwcs_field( 'global', 'header', 'logo_sub' ) ); ?>
					</span>
				</span>
			<?php endif; ?>
		</a>

		<nav class="hidden items-center gap-9 md:flex" aria-label="Ana menü" <?php nwcs_edit_attr( 'global', 'header', 'menu' ); ?>>
			<?php foreach ( $menu as $index => $item ) :
				$url = ahsapkasa_link( $item['url'] ?? '' );
				?>
				<a href="<?php echo esc_url( $url ); ?>"
					class="navtick relative font-display text-sm font-medium text-ink/85 transition-colors duration-200 hover:text-forest"
					<?php echo ahsapkasa_is_current( $item['url'] ?? '' ) ? 'aria-current="page"' : ''; ?>>
					<?php echo esc_html( $item['label'] ?? '' ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<div class="flex items-center gap-2">
			<?php // Baglanti girilmeden dugme tiklanamaz durur; kimseyi bos bir
			      // adrese goturmesin diye. ?>
			<?php $whatsapp = trim( (string) nwcs_field( 'global', 'header', 'whatsapp_url' ) ); ?>
			<a href="<?php echo $whatsapp ? esc_url( ahsapkasa_link( $whatsapp ) ) : '#'; ?>"
				<?php echo $whatsapp ? 'target="_blank" rel="noopener"' : 'aria-disabled="true" tabindex="-1" title="WhatsApp bağlantısı henüz girilmedi"'; ?>
				class="btn btn--sm btn--outline hidden md:inline-flex<?php echo $whatsapp ? '' : ' pointer-events-none opacity-55'; ?>"
				<?php nwcs_edit_attr( 'global', 'header', 'whatsapp_label' ); ?>>
				<span class="text-forest" aria-hidden="true"><?php nwcs_the_icon( 'whatsapp', '', 18 ); ?></span>
				<?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?>
			</a>

			<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'global', 'header', 'cta_url' ) ) ); ?>"
				class="btn btn--sm btn--solid hidden md:inline-flex"
				<?php nwcs_edit_attr( 'global', 'header', 'cta_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'global', 'header', 'cta_label' ) ); ?>
			</a>

			<button type="button" data-nav-toggle aria-expanded="false" aria-controls="menu-mobil"
				class="group/btn -mr-2 flex h-11 w-11 flex-col items-center justify-center gap-[5px] md:hidden"
				aria-label="Menüyü aç">
				<span class="block h-[2px] w-6 bg-ink transition-transform duration-300 ease-out group-aria-expanded/btn:translate-y-[7px] group-aria-expanded/btn:rotate-45"></span>
				<span class="block h-[2px] w-6 bg-ink transition-opacity duration-200 group-aria-expanded/btn:opacity-0"></span>
				<span class="block h-[2px] w-6 bg-ink transition-transform duration-300 ease-out group-aria-expanded/btn:-translate-y-[7px] group-aria-expanded/btn:-rotate-45"></span>
			</button>
		</div>
	</div>

	<div id="menu-mobil" data-nav-panel data-open="false"
		class="group/panel overflow-hidden border-t border-dust bg-bone/97 backdrop-blur transition-[max-height,opacity] duration-300 ease-out max-h-0 opacity-0 data-[open=true]:max-h-[30rem] data-[open=true]:opacity-100 md:hidden">
		<nav class="mx-auto flex max-w-[76rem] flex-col gap-1 px-6 py-5" aria-label="Mobil menü">
			<?php foreach ( $menu as $index => $item ) : ?>
				<a href="<?php echo esc_url( ahsapkasa_link( $item['url'] ?? '' ) ); ?>"
					class="translate-y-2 border-b border-dust/70 py-3 font-display text-base font-medium text-ink opacity-0 transition duration-300 ease-out last:border-0 aria-[current=page]:text-forest group-data-[open=true]/panel:translate-y-0 group-data-[open=true]/panel:opacity-100 <?php echo esc_attr( $delays[ $index ] ?? 'delay-[290ms]' ); ?>"
					<?php echo ahsapkasa_is_current( $item['url'] ?? '' ) ? 'aria-current="page"' : ''; ?>>
					<?php echo esc_html( $item['label'] ?? '' ); ?>
				</a>
			<?php endforeach; ?>

			<a href="<?php echo $whatsapp ? esc_url( ahsapkasa_link( $whatsapp ) ) : '#'; ?>"
				<?php echo $whatsapp ? 'target="_blank" rel="noopener"' : 'aria-disabled="true" tabindex="-1"'; ?>
				class="btn btn--sm btn--outline mt-4 w-full translate-y-2 opacity-0 transition duration-300 ease-out group-data-[open=true]/panel:translate-y-0 group-data-[open=true]/panel:opacity-100 delay-[240ms]<?php echo $whatsapp ? '' : ' pointer-events-none opacity-55'; ?>">
				<span class="text-forest" aria-hidden="true"><?php nwcs_the_icon( 'whatsapp', '', 18 ); ?></span>
				<?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?>
			</a>

			<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'global', 'header', 'cta_url' ) ) ); ?>"
				class="btn btn--sm btn--solid mt-3 w-full translate-y-2 opacity-0 transition duration-300 ease-out group-data-[open=true]/panel:translate-y-0 group-data-[open=true]/panel:opacity-100 delay-[290ms]">
				<?php echo esc_html( nwcs_field( 'global', 'header', 'cta_label' ) ); ?>
			</a>
		</nav>
	</div>
</header>

<main id="icerik" class="pt-[88px]">
