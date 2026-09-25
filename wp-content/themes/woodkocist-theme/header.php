<?php
/**
 * Ust menu: marka, sayfalar, WhatsApp. Icerik global.header'dan.
 */

defined( 'ABSPATH' ) || exit;

$menu     = nwcs_rows( 'global', 'header', 'menu' );
$whatsapp = wk_whatsapp();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a href="#icerik" class="wk-skip">İçeriğe geç</a>

<header class="wk-header">
	<div class="wk-wrap wk-header__row">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wk-brand" <?php nwcs_edit_attr( 'global', 'header', 'logo_text' ); ?>>
			<?php echo esc_html( nwcs_field( 'global', 'header', 'logo_text' ) ); ?>
		</a>

		<nav class="wk-nav" aria-label="Ana menü" <?php nwcs_edit_attr( 'global', 'header', 'menu' ); ?>>
			<ul>
				<?php foreach ( $menu as $item ) : ?>
					<li><a href="<?php echo esc_url( wk_link( $item['url'] ?? '' ) ); ?>"><?php echo esc_html( $item['label'] ?? '' ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<?php if ( $whatsapp ) : ?>
			<a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener" class="wk-btn wk-btn--wa wk-header__wa" <?php nwcs_edit_attr( 'global', 'header', 'whatsapp_label' ); ?>>
				<?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				<span class="wk-num"><?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?></span>
			</a>
		<?php endif; ?>

		<button type="button" class="wk-menu-toggle" data-menu-toggle aria-expanded="false" aria-controls="wk-mobile-nav">Menü</button>
	</div>

	<nav id="wk-mobile-nav" class="wk-mobile-nav" aria-label="Mobil menü" hidden>
		<ul class="wk-wrap">
			<?php foreach ( $menu as $item ) : ?>
				<li><a href="<?php echo esc_url( wk_link( $item['url'] ?? '' ) ); ?>"><?php echo esc_html( $item['label'] ?? '' ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
</header>

<main id="icerik">
