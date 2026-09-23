<?php
/**
 * Genel yedek sablon: arsivler (kategori, etiket, tarih) blog listesiyle
 * ayni duzeni kullanir.
 */

defined( 'ABSPATH' ) || exit;

if ( is_archive() || is_search() || is_home() ) {
	require get_theme_file_path( 'home.php' );
	return;
}

require get_theme_file_path( 'page.php' );
