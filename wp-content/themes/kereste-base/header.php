<?php
/**
 * Ust kisim: ince bilgi seridi (adres, e-posta, calisma saati) ve koyu menu
 * bandi. Istanbul Keresteci ile ayni duzen: cam agacli iki satir logo, beyaz
 * menu, sagda "arayin" telefon kutusu. Uc keresteci sitesi ust kisimda
 * akraba gorunur; renkler her sitenin kendi parti boyasi.
 *
 * Genislik butcesi: telefon kutusu xl'de, WhatsApp simgesi md'de, teklif
 * dugmesi sm'de gorunur; dar ekranda hepsi mobil menude.
 */

defined( 'ABSPATH' ) || exit;

$menu     = nwcs_rows( 'global', 'header', 'menu' );
$phone    = array(
	'label' => (string) nwcs_field( 'global', 'header', 'phone_label' ),
	'note'  => (string) nwcs_field( 'global', 'header', 'phone_note' ),
	'url'   => kr_link( nwcs_field( 'global', 'header', 'phone_url' ) ),
);
$cta      = (string) nwcs_field( 'global', 'header', 'cta_label' );
$whatsapp = trim( (string) nwcs_field( 'global', 'header', 'whatsapp_url' ) );
$wa_label = (string) nwcs_field( 'global', 'header', 'whatsapp_label' );
$address  = trim( (string) nwcs_field( 'global', 'footer', 'address' ) );
$email    = trim( (string) nwcs_field( 'global', 'footer', 'email' ) );
$hours    = trim( (string) nwcs_field( 'global', 'footer', 'hours' ) );
$map      = trim( (string) nwcs_field( 'contact', 'details', 'map_url' ) );
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

<div class="hidden bg-ink-deep text-[0.875rem] text-paper/75 md:block">
	<div class="mx-auto flex h-10 max-w-[78rem] items-center gap-7 px-5 md:px-8">
		<?php if ( '' !== $address ) : ?>
			<a href="<?php echo esc_url( $map ?: '#' ); ?>" <?php echo $map ? 'target="_blank" rel="noopener"' : ''; ?>
				class="flex min-w-0 items-center gap-2 text-paper/75 no-underline hover:text-paper" <?php nwcs_edit_attr( 'global', 'footer', 'address' ); ?>>
				<?php nwcs_the_icon( 'pin', 'shrink-0 text-mark-bright', 16 ); ?>
				<span class="truncate"><?php echo esc_html( (string) preg_replace( '/\s*\R\s*/u', ', ', $address ) ); ?></span>
			</a>
		<?php endif; ?>
		<?php if ( '' !== $email ) : ?>
			<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="hidden items-center gap-2 text-paper/75 no-underline hover:text-paper lg:flex"
				<?php nwcs_edit_attr( 'global', 'footer', 'email' ); ?>>
				<?php nwcs_the_icon( 'mail', 'shrink-0 text-mark-bright', 16 ); ?>
				<?php echo esc_html( $email ); ?>
			</a>
		<?php endif; ?>
		<?php if ( '' !== $hours ) : ?>
			<span class="tabular ml-auto flex shrink-0 items-center gap-2" <?php nwcs_edit_attr( 'global', 'footer', 'hours' ); ?>>
				<?php nwcs_the_icon( 'clock', 'shrink-0 text-mark-bright', 16 ); ?>
				<?php echo esc_html( $hours ); ?>
			</span>
		<?php endif; ?>
	</div>
</div>

<header data-header class="sticky top-0 z-50 border-b border-paper/10 bg-ink text-paper">
	<div class="mx-auto flex h-[5rem] max-w-[78rem] items-center gap-6 px-5 md:px-8">

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="shrink-0 no-underline" <?php nwcs_edit_attr( 'global', 'header', 'logo_word' ); ?>>
			<?php kr_logo( 'dark' ); ?>
		</a>

		<nav class="ml-6 hidden items-center gap-7 lg:flex" aria-label="Ana menü" <?php nwcs_edit_attr( 'global', 'header', 'menu' ); ?>>
			<?php foreach ( $menu as $item ) :
				$url = (string) ( $item['url'] ?? '' );
				?>
				<a href="<?php echo esc_url( kr_link( $url ) ); ?>"
					class="navline text-[0.9688rem] font-semibold text-paper/90 no-underline transition-colors hover:text-paper"
					<?php echo kr_is_current( $url ) ? 'aria-current="page"' : ( kr_is_within( $url ) ? 'data-section-current' : '' ); ?>>
					<?php echo esc_html( $item['label'] ?? '' ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<div class="ml-auto flex items-center gap-4">
			<a href="<?php echo esc_url( $phone['url'] ); ?>" class="hidden items-center gap-3 border-l border-paper/15 pl-6 text-paper no-underline xl:flex"
				<?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>>
				<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-mark text-paper"><?php nwcs_the_icon( 'phone', '', 20 ); ?></span>
				<span class="grid leading-tight">
					<span class="text-[0.8125rem] text-paper/65" <?php nwcs_edit_attr( 'global', 'header', 'phone_note' ); ?>><?php echo esc_html( $phone['note'] ); ?></span>
					<span class="tabular kr-condensed text-[1.2rem] font-extrabold"><?php echo esc_html( $phone['label'] ); ?></span>
				</span>
			</a>

			<?php if ( $whatsapp ) : ?>
				<a href="<?php echo esc_url( kr_link( $whatsapp ) ); ?>" target="_blank" rel="noopener"
					class="btn btn--sm btn--whatsapp hidden aspect-square px-0 md:inline-flex"
					aria-label="<?php echo esc_attr( $wa_label ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'whatsapp_label' ); ?>>
					<?php nwcs_the_icon( 'whatsapp', 'shrink-0', 18 ); ?>
				</a>
			<?php endif; ?>

			<a href="<?php echo esc_url( kr_quote_fallback_url() ); ?>" data-kr-quote class="btn btn--sm btn--mark hidden sm:inline-flex"
				<?php nwcs_edit_attr( 'global', 'header', 'cta_label' ); ?>>
				<?php echo esc_html( $cta ); ?>
			</a>

			<button type="button" data-nav-toggle aria-expanded="false" aria-controls="menu-mobil" aria-label="Menüyü aç"
				class="group/btn -mr-2 flex h-11 w-11 flex-col items-center justify-center gap-[5px] lg:hidden">
				<span class="block h-[2px] w-6 bg-paper transition-transform duration-300 group-aria-expanded/btn:translate-y-[7px] group-aria-expanded/btn:rotate-45"></span>
				<span class="block h-[2px] w-6 bg-paper transition-opacity duration-200 group-aria-expanded/btn:opacity-0"></span>
				<span class="block h-[2px] w-6 bg-paper transition-transform duration-300 group-aria-expanded/btn:-translate-y-[7px] group-aria-expanded/btn:-rotate-45"></span>
			</button>
		</div>
	</div>

	<div id="menu-mobil" data-nav-panel hidden class="border-t border-paper/10 bg-ink lg:hidden">
		<nav class="mx-auto max-w-[78rem] px-5 pb-6 pt-2 md:px-8" aria-label="Mobil menü">
			<ul>
				<?php foreach ( $menu as $item ) : ?>
					<li class="border-b border-paper/10">
						<a href="<?php echo esc_url( kr_link( $item['url'] ?? '' ) ); ?>"
							class="block py-3.5 text-lg font-semibold text-paper no-underline aria-[current=page]:text-mark-bright"
							<?php echo kr_is_current( (string) ( $item['url'] ?? '' ) ) ? 'aria-current="page"' : ''; ?>>
							<?php echo esc_html( $item['label'] ?? '' ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="mt-5 grid gap-3 sm:grid-cols-2">
				<a href="<?php echo esc_url( $phone['url'] ); ?>" class="btn btn--md btn--ghost tabular">
					<?php nwcs_the_icon( 'phone', 'shrink-0', 18 ); ?>
					<?php echo esc_html( $phone['label'] ); ?>
				</a>
				<?php if ( $whatsapp ) : ?>
					<a href="<?php echo esc_url( kr_link( $whatsapp ) ); ?>" target="_blank" rel="noopener" class="btn btn--md btn--whatsapp">
						<?php nwcs_the_icon( 'whatsapp', 'shrink-0', 18 ); ?>
						<?php echo esc_html( $wa_label ); ?>
					</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( kr_quote_fallback_url() ); ?>" data-kr-quote class="btn btn--md btn--mark <?php echo $whatsapp ? 'sm:col-span-2' : ''; ?>"><?php echo esc_html( $cta ); ?></a>
			</div>
		</nav>
	</div>
</header>

<main id="icerik">
