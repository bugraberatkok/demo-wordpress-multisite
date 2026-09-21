<?php
/**
 * Anasayfa. Hero sabittir; diger bolumler kayitli siraya gore basilir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

paletci_section( 'hero' );

foreach ( nwcs_section_order( 'home' ) as $section ) {
	paletci_section( $section );
}

get_footer();
