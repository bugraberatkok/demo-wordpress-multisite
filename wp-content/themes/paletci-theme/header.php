<?php
/**
 * Paletci ust menu.
 */

defined( 'ABSPATH' ) || exit;

$logo = nwcs_image( 'global', 'header', 'logo_image', 'thumbnail' );
$menu = nwcs_rows( 'global', 'header', 'menu' );
$text = (string) nwcs_field( 'global', 'header', 'logo_text' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="p-header" data-nwcs-section="header">
	<div class="p-wrap p-header__inner">
		<a class="p-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( ! empty( $logo['url'] ) ) : ?>
				<img class="p-logo__img" src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'logo_image' ); ?> />
			<?php else : ?>
				<span class="p-logo__mark" aria-hidden="true" <?php nwcs_edit_attr( 'global', 'header', 'logo_image' ); ?>><?php echo esc_html( mb_substr( $text, 0, 1 ) ); ?></span>
			<?php endif; ?>
			<span>
				<span class="p-logo__text" <?php nwcs_edit_attr( 'global', 'header', 'logo_text' ); ?>><?php echo esc_html( $text ); ?></span><br />
				<span class="p-logo__sub" <?php nwcs_edit_attr( 'global', 'header', 'logo_sub' ); ?>><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_sub' ) ); ?></span>
			</span>
		</a>

		<nav class="p-nav" aria-label="Ana menü">
			<?php foreach ( $menu as $index => $item ) : ?>
				<a href="<?php echo esc_url( paletci_link( $item['url'] ?? '' ) ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'menu', $index, 'label' ); ?>><?php echo esc_html( $item['label'] ?? '' ); ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="p-header__actions">
			<a class="p-header__phone" href="<?php echo esc_url( paletci_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>>
				<?php nwcs_the_icon( nwcs_field( 'global', 'header', 'phone_icon' ), 'p-icon', 18 ); ?>
				<?php echo esc_html( nwcs_field( 'global', 'header', 'phone_label' ) ); ?>
			</a>
			<a class="p-btn p-btn--forest" href="<?php echo esc_url( paletci_link( nwcs_field( 'global', 'header', 'whatsapp_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'whatsapp_label' ); ?>>
				<?php nwcs_the_icon( nwcs_field( 'global', 'header', 'whatsapp_icon' ), 'p-icon', 18 ); ?>
				<?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?>
			</a>
		</div>
	</div>
</header>
