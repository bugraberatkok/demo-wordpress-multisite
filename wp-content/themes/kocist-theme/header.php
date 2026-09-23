<?php
/**
 * Ust bilgi seridi, logo ve ana menu. Degerler 'global' sayfasindan okunur.
 *
 * Duzen referansi tuin.co.uk, menu hareketi revenuecat.com: acilir menuler
 * ayri kutular degil, tek bir kutu ogeler arasinda boyut degistirerek kayar.
 * Davranis assets/js/nav.js, gorunum assets/css/header.css icinde.
 *
 * nwcs_edit_attr() yalnizca panel onizlemesinde isaret basar; ziyaretcide
 * hicbir ek nitelik gorunmez.
 */

defined( 'ABSPATH' ) || exit;

$logo = nwcs_image( 'global', 'header', 'logo_image', 'medium' );
$menu = kocist_menu_items();

// Ortak panoda hangi menu ogesinin hangi panoya dustugu; sablonda iki kez
// dolasmamak icin once indeksler cikariliyor.
$menu_panes = array();

foreach ( $menu as $menu_index => $menu_item ) {
	if ( ! empty( $menu_item['children'] ) ) {
		$menu_panes[ $menu_index ] = $menu_item['children'];
	}
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>

	<?php /* Tasarim token'lari: hem Tailwind renk adi (bg-forest, text-leaf) hem CSS degiskeni. */ ?>
	<style type="text/tailwindcss">
		@theme {
			--color-forest: #17542f;
			--color-forest-2: #1e6b3c;
			--color-forest-3: #0f3d21;
			--color-leaf: #7ab648;
			--color-leaf-dark: #5e9433;
			--color-leaf-soft: #eaf5e1;
			--color-paper: #ffffff;
			--color-paper-2: #f4f6f3;
			--color-ktext: #1f2a24;
			--color-muted: #5f6b63;
			--color-line: #dfe5e0;
		}
	</style>
</head>
<body <?php body_class(); ?>>

<div class="k-topbar" data-nwcs-section="topbar">
	<div class="k-wrap k-topbar__inner">
		<div class="k-topbar__meta">
			<span class="k-topbar__note">
				<span <?php nwcs_edit_attr( 'global', 'topbar', 'note' ); ?>><?php echo esc_html( nwcs_field( 'global', 'topbar', 'note' ) ); ?></span>
				<span class="k-topbar__dot" aria-hidden="true">•</span>
				<span <?php nwcs_edit_attr( 'global', 'topbar', 'hours' ); ?>><?php echo esc_html( nwcs_field( 'global', 'topbar', 'hours' ) ); ?></span>
			</span>

			<?php if ( nwcs_field( 'global', 'topbar', 'bank_label' ) ) : ?>
				<a class="k-topbar__item" href="<?php echo esc_url( kocist_link( nwcs_field( 'global', 'topbar', 'bank_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'topbar', 'bank_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'global', 'topbar', 'bank_label' ) ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="k-topbar__meta">
			<a class="k-topbar__item" href="<?php echo esc_url( kocist_link( nwcs_field( 'global', 'topbar', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'topbar', 'phone_label' ); ?>>
				<?php nwcs_the_icon( nwcs_field( 'global', 'topbar', 'phone_icon' ), 'k-icon', 15 ); ?>
				<?php echo esc_html( nwcs_field( 'global', 'topbar', 'phone_label' ) ); ?>
			</a>
			<a class="k-topbar__item" href="<?php echo esc_url( kocist_link( nwcs_field( 'global', 'topbar', 'email_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'topbar', 'email_label' ); ?>>
				<?php nwcs_the_icon( nwcs_field( 'global', 'topbar', 'email_icon' ), 'k-icon', 15 ); ?>
				<?php echo esc_html( nwcs_field( 'global', 'topbar', 'email_label' ) ); ?>
			</a>
		</div>
	</div>
</div>

<header class="k-header" data-k-header data-nwcs-section="header">
	<div class="k-wrap k-header__inner">

		<a class="k-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( ! empty( $logo['url'] ) ) : ?>
				<img class="k-logo__img" src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'logo_image' ); ?> />
			<?php else : ?>
				<span class="k-logo__mark" aria-hidden="true"></span>
			<?php endif; ?>
			<span class="k-logo__text-wrap">
				<span class="k-logo__text" <?php nwcs_edit_attr( 'global', 'header', 'logo_text' ); ?>><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_text' ) ); ?></span>
				<span class="k-logo__sub" <?php nwcs_edit_attr( 'global', 'header', 'logo_sub' ); ?>><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_sub' ) ); ?></span>
			</span>
		</a>

		<button type="button" class="k-burger" data-k-burger aria-expanded="false" aria-controls="k-nav">
			<span class="screen-reader-text">Menüyü aç veya kapat</span>
			<span class="k-burger__bar" aria-hidden="true"></span>
			<span class="k-burger__bar" aria-hidden="true"></span>
			<span class="k-burger__bar" aria-hidden="true"></span>
		</button>

		<nav class="k-nav" id="k-nav" data-k-nav aria-label="Ana menü">
			<span class="k-nav__indicator" data-k-nav-indicator aria-hidden="true"></span>

			<ul class="k-nav__list">
				<?php foreach ( $menu as $index => $item ) : ?>
					<?php
					$children     = $item['children'];
					$has_children = ! empty( $children );
					$is_current   = kocist_is_current_menu_item( $item['url'] );

					if ( $has_children ) {
						$columns   = kocist_menu_columns( count( $children ) );
						$pane_rows = (int) ceil( count( $children ) / $columns );
					}
					?>
					<li
						class="k-nav__item<?php echo $has_children ? ' k-nav__item--parent' : ''; ?>"
						data-k-nav-item
						<?php echo $has_children ? 'data-k-nav-pane="' . (int) $index . '"' : ''; ?>
					>
						<a
							class="k-nav__link<?php echo $is_current ? ' is-current' : ''; ?>"
							href="<?php echo esc_url( kocist_link( $item['url'] ) ); ?>"
							<?php echo $is_current ? 'aria-current="page"' : ''; ?>
							data-k-nav-link
							<?php nwcs_edit_attr( 'global', 'header', 'menu', $index, 'label' ); ?>
						><?php echo esc_html( $item['label'] ); ?></a>

						<?php if ( $has_children ) : ?>
							<button type="button" class="k-nav__caret" data-k-nav-toggle aria-expanded="false">
								<span class="screen-reader-text">
									<?php echo esc_html( $item['label'] ); ?> alt menüsünü aç
								</span>
								<svg class="k-nav__chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
									<path d="M2.5 4.25 6 7.75l3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</button>

							<?php /* Dar ekran kopyasi; masaustunde gizli, ortak pano devreye girer. */ ?>
							<div class="k-nav__accordion">
								<ul class="k-dropdown__list">
									<?php foreach ( $children as $child ) : ?>
										<li class="k-dropdown__item">
											<a class="k-dropdown__link" href="<?php echo esc_url( kocist_link( $child['url'] ) ); ?>">
												<?php echo esc_html( $child['label'] ); ?>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( $menu_panes ) : ?>
				<?php /* Masaustu: tek kutu, ogeler arasinda boyut degistirerek kayar. */ ?>
				<div class="k-dropdown" data-k-dropdown>
					<div class="k-dropdown__shell" data-k-dropdown-shell>
						<?php foreach ( $menu_panes as $pane_index => $pane_children ) : ?>
							<?php
							$pane_columns = kocist_menu_columns( count( $pane_children ) );
							$pane_rows    = (int) ceil( count( $pane_children ) / $pane_columns );
							?>
							<div class="k-dropdown__pane" data-k-dropdown-pane="<?php echo (int) $pane_index; ?>">
								<ul
									class="k-dropdown__list"
									style="--cols: <?php echo (int) $pane_columns; ?>; --rows: <?php echo (int) $pane_rows; ?>"
								>
									<?php foreach ( $pane_children as $child_index => $child ) : ?>
										<li class="k-dropdown__item" style="--i: <?php echo (int) $child_index; ?>">
											<a class="k-dropdown__link" href="<?php echo esc_url( kocist_link( $child['url'] ) ); ?>">
												<?php echo esc_html( $child['label'] ); ?>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</nav>
	</div>
</header>
