<?php
/**
 * scripts/data/redirects.php listelerini sitelerin yonlendirme ayarina yazar.
 *
 *   wp eval-file /scripts/load-redirects.php
 *
 * Ag genelinde calisir (--url gerekmez); site adres adina (slug) gore eslesir.
 * Panelde elle eklenmis kurallari korumak icin var olan listenin sonuna
 * eklemez, listeyi DEGISTIRIR; bu yuzden once mevcut liste gosterilir.
 * --dry-run ile yalnizca ne yazilacagini gosterir.
 */

defined( 'ABSPATH' ) || die( 'Yalnizca WP-CLI ile calistirilir.' );

$lists   = require __DIR__ . '/data/redirects.php';
$dry_run = in_array( '--dry-run', (array) ( $GLOBALS['argv'] ?? array() ), true ) || getenv( 'DRY_RUN' );

foreach ( get_sites( array( 'number' => 100 ) ) as $site ) {
	$slug = trim( $site->path, '/' );

	if ( ! isset( $lists[ $slug ] ) ) {
		continue;
	}

	switch_to_blog( (int) $site->blog_id );

	$text   = trim( $lists[ $slug ] ) . "\n";
	$parsed = nwcs_redirects_parse( $text );
	$before = (string) get_option( NWCS_REDIRECTS_OPTION, '' );

	foreach ( $parsed['errors'] as $error ) {
		WP_CLI::warning( "/$slug/: $error" );
	}

	if ( $dry_run ) {
		WP_CLI::log( sprintf( '/%s/: %d kural yazilacak (su an %d).', $slug, count( $parsed['rules'] ), count( nwcs_redirects_parse( $before )['rules'] ) ) );
	} else {
		update_option( NWCS_REDIRECTS_OPTION, $text, false );
		WP_CLI::log( sprintf( '/%s/: %d kural yazildi.', $slug, count( $parsed['rules'] ) ) );
	}

	restore_current_blog();
}

WP_CLI::success( $dry_run ? 'Deneme bitti; hicbir sey yazilmadi.' : 'Yonlendirme listeleri yuklendi.' );
