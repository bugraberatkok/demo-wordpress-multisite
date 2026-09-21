<?php
/**
 * Plugin Name:       Network Content Studio
 * Description:       Ag genelinde tek icerik veri katmani ve alan manifesti. MVP 1'de veri katmani, MVP 2'de Icerik Studyosu paneli.
 * Version:           0.2.0
 * Network:           true
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Text Domain:       nwcs
 */

defined( 'ABSPATH' ) || exit;

define( 'NWCS_VERSION', '0.2.0' );
define( 'NWCS_DIR', plugin_dir_path( __FILE__ ) );
define( 'NWCS_URL', plugin_dir_url( __FILE__ ) );

require_once NWCS_DIR . 'includes/manifest.php';
require_once NWCS_DIR . 'includes/store.php';
require_once NWCS_DIR . 'includes/icons.php';

if ( is_admin() ) {
	require_once NWCS_DIR . 'includes/admin/panel.php';
	require_once NWCS_DIR . 'includes/admin/fields.php';
	require_once NWCS_DIR . 'includes/admin/save.php';
}
