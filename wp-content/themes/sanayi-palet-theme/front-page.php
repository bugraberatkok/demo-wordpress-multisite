<?php
/**
 * Ana sayfa. Hero sabit; alttaki bolumlerin sirasi Icerik Studyosu'ndan gelir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

sanayi_palet_section( 'hero' );

foreach ( nwcs_section_order( 'home' ) as $section ) {
	sanayi_palet_section( $section );
}

get_footer();
