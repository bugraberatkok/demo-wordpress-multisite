<?php
/**
 * Ust bilgi seridi ve ana menu. Degerler 'global' sayfasindan okunur.
 */

defined( 'ABSPATH' ) || exit;

$logo = nwcs_image( 'global', 'header', 'logo_image', 'thumbnail' );
$menu = nwcs_rows( 'global', 'header', 'menu' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div class="k-topbar">
	<div class="k-wrap k-topbar__inner">
		<span><?php echo esc_html( nwcs_field( 'global', 'topbar', 'note' ) ); ?></span>
		<div class="k-topbar__meta">
			<span class="k-topbar__item">
				<?php nwcs_the_icon( nwcs_field( 'global', 'topbar', 'hours_icon' ), 'k-icon', 16 ); ?>
				<?php echo esc_html( nwcs_field( 'global', 'topbar', 'hours' ) ); ?>
			</span>
			<a class="k-topbar__item" href="<?php echo esc_url( kocist_link( nwcs_field( 'global', 'topbar', 'phone_url' ) ) ); ?>">
				<?php nwcs_the_icon( nwcs_field( 'global', 'topbar', 'phone_icon' ), 'k-icon', 16 ); ?>
				<?php echo esc_html( nwcs_field( 'global', 'topbar', 'phone_label' ) ); ?>
			</a>
			<a class="k-topbar__item" href="<?php echo esc_url( kocist_link( nwcs_field( 'global', 'topbar', 'email_url' ) ) ); ?>">
				<?php echo esc_html( nwcs_field( 'global', 'topbar', 'email_label' ) ); ?>
			</a>
		</div>
	</div>
</div>

<header class="k-header">
	<div class="k-wrap k-header__inner">
		<a class="k-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( ! empty( $logo['url'] ) ) : ?>
				<img class="k-logo__img" src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" />
			<?php endif; ?>
			<span>
				<span class="k-logo__text"><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_text' ) ); ?></span><br />
				<span class="k-logo__sub"><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_sub' ) ); ?></span>
			</span>
		</a>

		<nav class="k-nav" aria-label="Ana menü">
			<?php foreach ( $menu as $item ) : ?>
				<a href="<?php echo esc_url( kocist_link( $item['url'] ?? '' ) ); ?>"><?php echo esc_html( $item['label'] ?? '' ); ?></a>
			<?php endforeach; ?>
		</nav>

		<a class="k-btn k-btn--primary" href="<?php echo esc_url( kocist_link( nwcs_field( 'global', 'header', 'cta_url' ) ) ); ?>">
			<?php echo esc_html( nwcs_field( 'global', 'header', 'cta_label' ) ); ?>
		</a>
	</div>
</header>
