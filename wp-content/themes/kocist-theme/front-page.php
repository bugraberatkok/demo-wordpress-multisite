<?php
/**
 * Ana sayfa. Hero sabit konumdadir; diger bolumler kayitli siraya gore basilir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

kocist_section( 'hero' );

foreach ( nwcs_section_order( 'home' ) as $section ) {
	kocist_section( $section );
}

get_footer();
