<?php
/**
 * Ana sayfa. Hero sabit; alttaki modullerin hangileri ve hangi sirayla
 * gorunecegi cocuk temanin manifestinden (sortable_sections) ve Icerik
 * Studyosu'ndaki siralamadan gelir. Iki kuzen site boylece ayri modullerle
 * ayni iskeleti kullanir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

kr_section( 'hero' );

foreach ( nwcs_section_order( 'home' ) as $section ) {
	kr_section( $section );
}

get_footer();
