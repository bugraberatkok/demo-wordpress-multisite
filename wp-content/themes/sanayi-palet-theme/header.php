<?php
/**
 * Ust menu. Tum sayfalarda aynidir; icerik global.header bileseninden gelir.
 */

defined( 'ABSPATH' ) || exit;

$logo = nwcs_image( 'global', 'header', 'logo_image', 'medium' );
$name = nwcs_field( 'global', 'header', 'logo_text' );
$sub  = nwcs_field( 'global', 'header', 'logo_sub' );
?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="sp-skip btn btn--sm btn--solid" href="#icerik">İçeriğe geç</a>

<header class="sp-header" data-sp-header data-open="false">
	<div class="sp-wrap sp-header__bar">

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sp-logo" <?php nwcs_edit_attr( 'global', 'header', 'logo_text' ); ?>>
			<?php if ( ! empty( $logo['url'] ) ) : ?>
				<img class="sp-logo__image" src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( trim( $name . ' ' . $sub ) ); ?>" />
			<?php else : ?>
				<span class="sp-logo__name"><?php echo esc_html( $name ); ?></span>
				<span class="sp-logo__sub" <?php nwcs_edit_attr( 'global', 'header', 'logo_sub' ); ?>><?php echo esc_html( $sub ); ?></span>
			<?php endif; ?>
		</a>

		<button type="button" class="sp-header__toggle" aria-expanded="false" aria-controls="sp-menu" data-sp-toggle>
			<span class="sp-header__toggle-bars" aria-hidden="true"></span>
			<span class="sp-header__toggle-label" <?php nwcs_edit_attr( 'global', 'header', 'menu_toggle' ); ?>><?php echo esc_html( nwcs_field( 'global', 'header', 'menu_toggle' ) ); ?></span>
		</button>

		<div class="sp-header__panel" id="sp-menu">
			<nav class="sp-nav" aria-label="Ana menü" <?php nwcs_edit_attr( 'global', 'header', 'menu' ); ?>>
				<ul>
					<?php foreach ( nwcs_rows( 'global', 'header', 'menu' ) as $index => $item ) : ?>
						<?php
						$url     = (string) ( $item['url'] ?? '' );
						$current = sanayi_palet_is_current( $url );
						?>
						<li>
							<a href="<?php echo esc_url( sanayi_palet_link( $url ) ); ?>"<?php echo $current ? ' aria-current="page"' : ''; ?> <?php nwcs_edit_attr( 'global', 'header', 'menu', (int) $index, 'label' ); ?>>
								<?php echo esc_html( $item['label'] ?? '' ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="sp-header__actions">
				<a class="sp-header__phone" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>>
					<?php sanayi_palet_icon( 'phone', 18 ); ?>
					<?php echo esc_html( nwcs_field( 'global', 'header', 'phone_label' ) ); ?>
				</a>
				<a class="btn btn--sm btn--solid" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'global', 'header', 'cta_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'cta_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'global', 'header', 'cta_label' ) ); ?>
				</a>
			</div>
		</div>
	</div>
</header>

<main id="icerik">
