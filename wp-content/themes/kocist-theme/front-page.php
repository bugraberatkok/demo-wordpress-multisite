<?php
/**
 * Ana sayfa. Hero sabit konumdadir; diger bolumler kayitli siraya gore basilir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

kocist_section( 'hero' );

foreach ( kocist_home_sections() as $section ) {
	kocist_section( $section );
}

get_footer();
