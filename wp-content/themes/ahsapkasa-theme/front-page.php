<?php
/**
 * Ana sayfa. Hero sabit; alttaki bolumlerin sirasi Icerik Studyosu'ndan gelir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

ahsapkasa_section( 'hero' );

foreach ( nwcs_section_order( 'home' ) as $section ) {
	ahsapkasa_section( $section );
}

get_footer();
