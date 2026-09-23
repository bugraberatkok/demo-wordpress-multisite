<?php
/**
 * Ic sayfa basligi: koyu zemin, buyuk baslik, kisa aciklama.
 *
 * Beklenen $args: page (manifest sayfa anahtari). Istege bagli: title ve
 * text (alan yerine dogrudan deger), crumbs ([ [etiket, adres], ... ]).
 */

defined( 'ABSPATH' ) || exit;

$page      = (string) ( $args['page'] ?? '' );
$own_title = isset( $args['title'] );
$own_text  = isset( $args['text'] );
$title     = $own_title ? (string) $args['title'] : nwcs_field( $page, 'head', 'title' );
$text      = $own_text ? (string) $args['text'] : nwcs_field( $page, 'head', 'text' );
$crumbs    = $args['crumbs'] ?? array();
?>
<header class="ik-pagehead">
	<div class="ik-wrap">
		<?php if ( $crumbs ) : ?>
			<nav class="ik-crumbs" aria-label="Konum">
				<ol>
					<?php foreach ( $crumbs as $crumb ) : ?>
						<li><a href="<?php echo esc_url( $crumb[1] ); ?>"><?php echo esc_html( $crumb[0] ); ?></a></li>
					<?php endforeach; ?>
				</ol>
			</nav>
		<?php endif; ?>

		<h1 class="ik-pagehead__title" <?php $own_title || nwcs_edit_attr( $page, 'head', 'title' ); ?>><?php echo esc_html( $title ); ?></h1>

		<?php if ( '' !== trim( $text ) ) : ?>
			<p class="ik-pagehead__text" <?php $own_text || nwcs_edit_attr( $page, 'head', 'text' ); ?>><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
	</div>
</header>
