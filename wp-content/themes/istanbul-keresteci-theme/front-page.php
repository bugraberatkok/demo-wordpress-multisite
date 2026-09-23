<?php
/**
 * Ana sayfa: hero sabit ilk bolumdur, digerleri panelde kayitli siraya gore.
 */

defined( 'ABSPATH' ) || exit;

get_header();

ik_section( 'hero' );

$order = nwcs_section_order( 'home' ) ?: array( 'about', 'products', 'process', 'blog', 'ctaband' );

foreach ( $order as $section ) {
	ik_section( $section );
}

get_footer();
