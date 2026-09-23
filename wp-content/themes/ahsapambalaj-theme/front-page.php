<?php
/**
 * Ana sayfa. Hero sabit; alttaki bolumlerin sirasi Icerik Studyosu'ndan gelir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

ahsapambalaj_section( 'hero' );

foreach ( nwcs_section_order( 'home' ) as $section ) {
	ahsapambalaj_section( $section );
}

get_footer();
