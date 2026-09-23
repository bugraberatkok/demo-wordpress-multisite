<?php
/**
 * Ic sayfa basligi: buyuk baslik ve kisa aciklama.
 *
 * Beklenen $args: page (manifest sayfa anahtari). Alanlar <page>.head.title/text.
 */

defined( 'ABSPATH' ) || exit;

$page = (string) ( $args['page'] ?? '' );
?>
<header class="sp-pagehead">
	<div class="sp-wrap">
		<h1 class="sp-pagehead__title" <?php nwcs_edit_attr( $page, 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( $page, 'head', 'title' ) ); ?></h1>
		<p class="sp-pagehead__text" <?php nwcs_edit_attr( $page, 'head', 'text' ); ?>><?php echo esc_html( nwcs_field( $page, 'head', 'text' ) ); ?></p>
	</div>
</header>
