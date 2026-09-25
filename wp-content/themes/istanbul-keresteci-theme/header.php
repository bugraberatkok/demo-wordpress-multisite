<?php
/**
 * Ust bilgi seridi, logo, ana menu ve telefon. Degerler 'global' sayfasindan.
 *
 * Urunlerimiz acilir listesi urun satirlarindan uretilir (ik_menu). Masaustunde
 * imlec ya da klavye odagiyla, dar ekranda ok dugmesiyle acilir; davranis
 * assets/js/nav.js icinde.
 */

defined( 'ABSPATH' ) || exit;

$menu = ik_menu();
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

<a class="ik-skip btn btn--sm btn--solid" href="#icerik">İçeriğe geç</a>

<div class="ik-topbar">
	<div class="ik-wrap ik-topbar__inner">
		<a class="ik-topbar__item ik-topbar__address" href="<?php echo esc_url( ik_link( nwcs_field( 'global', 'topbar', 'address_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'topbar', 'address' ); ?>>
			<?php ik_icon( 'pin', 16 ); ?>
			<?php echo esc_html( nwcs_field( 'global', 'topbar', 'address' ) ); ?>
		</a>
		<a class="ik-topbar__item" href="<?php echo esc_url( ik_link( nwcs_field( 'global', 'topbar', 'email_url' ) ) ); ?>" <?php nwcs_edit_attr( 'global', 'topbar', 'email_label' ); ?>>
			<?php ik_icon( 'mail', 16 ); ?>
			<span class="ik-topbar__label"><?php echo esc_html( nwcs_field( 'global', 'topbar', 'email_label' ) ); ?></span>
		</a>

		<div class="ik-topbar__social">
			<?php foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook' ) as $network => $network_label ) : ?>
				<?php $network_url = nwcs_field( 'global', 'topbar', $network . '_url' ); ?>
				<?php if ( '' !== trim( $network_url ) ) : ?>
					<a class="ik-topbar__social-link" href="<?php echo esc_url( $network_url ); ?>" target="_blank" rel="noopener" <?php nwcs_edit_attr( 'global', 'topbar', $network . '_url' ); ?>>
						<?php ik_icon( $network, 18 ); ?>
						<span class="screen-reader-text"><?php echo esc_html( $network_label ); ?> (yeni sekmede açılır)</span>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<header class="ik-header" data-ik-header data-open="false">
	<div class="ik-wrap ik-header__bar">

		<?php ik_logo(); ?>

		<button type="button" class="ik-header__toggle" aria-expanded="false" aria-controls="ik-menu" data-ik-toggle>
			<span class="ik-header__toggle-bars" aria-hidden="true"></span>
			<span class="ik-header__toggle-label">Menü</span>
		</button>

		<div class="ik-header__panel" id="ik-menu">
			<nav class="ik-nav" aria-label="Ana menü" <?php nwcs_edit_attr( 'global', 'header', 'menu' ); ?>>
				<ul class="ik-nav__list">
					<?php foreach ( $menu as $item ) : ?>
						<?php
						$has_children = ! empty( $item['children'] );
						$current      = ik_is_current( $item['url'] );
						$drop_id      = 'ik-drop-' . (int) $item['index'];
						?>
						<li class="ik-nav__item<?php echo $has_children ? ' ik-nav__item--parent' : ''; ?>"<?php echo $has_children ? ' data-ik-parent data-open="false"' : ''; ?>>
							<a class="ik-nav__link" href="<?php echo esc_url( ik_link( $item['url'] ) ); ?>"<?php echo $current ? ' aria-current="page"' : ''; ?> <?php nwcs_edit_attr( 'global', 'header', 'menu', $item['index'], 'label' ); ?>>
								<?php echo esc_html( $item['label'] ); ?>
							</a>

							<?php if ( $has_children ) : ?>
								<button type="button" class="ik-nav__caret" aria-expanded="false" aria-controls="<?php echo esc_attr( $drop_id ); ?>" data-ik-caret>
									<?php ik_icon( 'caret', 14 ); ?>
									<span class="screen-reader-text"><?php echo esc_html( $item['label'] ); ?> listesini aç</span>
								</button>

								<div class="ik-drop" id="<?php echo esc_attr( $drop_id ); ?>">
									<ul class="ik-drop__list">
										<?php foreach ( $item['children'] as $child ) : ?>
											<?php $thumb = ik_product_image( $child['product'], 'thumbnail' ); ?>
											<li>
												<a class="ik-drop__link" href="<?php echo esc_url( $child['url'] ); ?>"<?php echo ik_is_current( $child['url'] ) ? ' aria-current="page"' : ''; ?> <?php nwcs_edit_attr( 'products', 'catalog', 'items', (int) $child['product']['index'], 'title' ); ?>>
													<?php echo ik_image_tag( $thumb, 'ik-drop__thumb', '' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
													<span><?php echo esc_html( $child['label'] ); ?></span>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
									<a class="ik-drop__all ik-textlink" href="<?php echo esc_url( ik_link( $item['url'] ) ); ?>" <?php nwcs_edit_attr( 'global', 'header', 'all_label' ); ?>>
										<?php echo esc_html( nwcs_field( 'global', 'header', 'all_label' ) ); ?>
										<?php ik_icon( 'arrow', 16 ); ?>
									</a>
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<a class="ik-call" href="<?php echo esc_url( ik_link( nwcs_field( 'global', 'header', 'phone_url' ) ) ); ?>">
				<span class="ik-call__icon"><?php ik_icon( 'phone', 22 ); ?></span>
				<span class="ik-call__text">
					<span class="ik-call__note" <?php nwcs_edit_attr( 'global', 'header', 'phone_note' ); ?>><?php echo esc_html( nwcs_field( 'global', 'header', 'phone_note' ) ); ?></span>
					<span class="ik-call__number" <?php nwcs_edit_attr( 'global', 'header', 'phone_label' ); ?>><?php echo esc_html( nwcs_field( 'global', 'header', 'phone_label' ) ); ?></span>
				</span>
			</a>
		</div>
	</div>
</header>

<main id="icerik">
