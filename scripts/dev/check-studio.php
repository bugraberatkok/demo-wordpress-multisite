<?php
/**
 * Gelistirme kontrolu: Icerik Studyosu bir sitenin manifestini goruyor mu?
 * Kullanim: wp eval-file /scripts/dev/check-studio.php <blog_id>
 */
$blog_id = (int) ( $args[0] ?? 0 );
$sites   = nwcs_editable_sites();

if ( ! isset( $sites[ $blog_id ] ) ) {
	WP_CLI::error( "Site {$blog_id} panelde yok." );
}

$manifest = nwcs_manifest_for_blog( $blog_id );
$fields   = 0;

foreach ( $manifest['pages'] as $key => $page ) {
	$count = 0;
	foreach ( $page['components'] as $component ) {
		$count += count( $component['fields'] );
	}
	$fields += $count;
	WP_CLI::log( sprintf( '%-22s %-34s %2d alan  %s', $key, $page['label'], $count, $page['path'] ) );
}

WP_CLI::success( sprintf( '%s: %d sayfa, %d alan. Panel etiketi: %s', $manifest['site_key'], count( $manifest['pages'] ), $fields, $sites[ $blog_id ]['label'] ) );
