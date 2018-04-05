<?php
namespace OrionRush\TnyGmaps;
if ( ! defined( 'ABSPATH' ) ) die();

/**
 * Plugin Name: Tny gMaps
 * Description: A Google maps plugin powered Google's places, static maps and geocoding APIs.
 * Version: 0.0.4
 * Author: Ben Rush
 * Author URI: http://www.orionrush.com
 * Plugin URI: http://www.rocketsarenostalgic.net
 * License: GPL
 * License URI: https://wordpress.org/about/gpl/
 * Text Domain: orionrush_tnygmaps
 */

/**
 * Map details are saved using the wp transients api, which are refreshed via Google when the transient expires.
 * If an API key is provided the Places API is available for address lookup, alternatively a manual address lookup can be done using Google's Gecoding API.
 * Basic usage
 * [TNYGMAP id="map" z="10" w="100%" h="300" placeref="" maptype="ROADMAP" default_marker="false" marker="path/to/custom/marker.png" infowindow="Some Content"]
 */

/***********************************************************************
 * Definitions
 * /*********************************************************************/
define( 'TNYGMAPS_VERSION', '0.0.3' );
define( 'TNYGMAPS_NAME', __('Tny gMaps', 'orionrush_tnygmaps' ) );
define( 'TNYGMAPS_PLUGIN', __FILE__ );                      // Plugin location
define( 'TNYGMAPS_PATH', plugin_dir_path( __FILE__ ) );     // File path to the plugin directory
define( 'TNYGMAPS_URL', plugin_dir_url( __FILE__ ) );       // URL to the plugin


// Icons Directory
define( 'TNYGMAPS_ICONS_DIR_NAME', 'tny_gMaps_icons' );     // Name of the icons directory

$upload_dir = wp_upload_dir();                              // WP uploads directory array
$upload_dir_base = trailingslashit( $upload_dir['basedir'] );    //as path with trailing slash
$upload_dir_base = $upload_dir_base . TNYGMAPS_ICONS_DIR_NAME . '/';     // Assembled path uploads icon directory
define( 'TNYGMAPS_ICONS_DIR', $upload_dir_base );             // The file path to map icons directory in your uploads directory

$upload_dir_URL = trailingslashit( $upload_dir['baseurl'] );
$upload_dir_URL = $upload_dir_URL . TNYGMAPS_ICONS_DIR_NAME . '/';
define( 'TNYGMAPS_ICONS_DIR_URL', $upload_dir_URL );        // The URL to map icons directory in your uploads directory


define( 'TNYGMAPS_ICONS_DIR_CREATED', file_exists( TNYGMAPS_ICONS_DIR ) ); // Bool: Check of the directory was successfully created.

// GOOGLE Maps default pin icon
define( 'TNYGMAPS_GOOGLE_ICON_URL', TNYGMAPS_URL . 'assets/ic_map_marker.png' );       // URL to default icon

/**********************************************************************
 * Acquire a Google Places API key for your website domain
 * See the README.md file for details
 * https://developers.google.com/places/documentation/
 * /*********************************************************************/

$api_key = false;
if (get_option('tnygmaps_api_key')) {
	$api_key = trim(get_option( 'tnygmaps_api_key' ));
}

if ( ! defined( 'GOOGLE_API_KEY' ) &&  $api_key != false ) {
	define( 'GOOGLE_API_KEY', $api_key );
}

//Set the debug var as a global, $tnygmaps_debug = true;
global $tnygmaps_debug;

/***********************************************************************
 * Includes
 * *********************************************************************/

// Activation
require_once (TNYGMAPS_PATH . 'lib/activation.php');

// Helper functions
require_once( TNYGMAPS_PATH . 'lib/support.php' );

// Admin screen
require_once ( TNYGMAPS_PATH . 'lib/admin.php');

// Ajax functions (must be included on the main plugin file)
require_once( TNYGMAPS_PATH . 'lib/ajax.php' );

// Shortcode
require_once( TNYGMAPS_PATH . 'lib/shortcode.php' );

// GUI via modal and tinyMCE button
require_once( TNYGMAPS_PATH . 'lib/tinyMCE.php' );

// Plugin setting link
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), __NAMESPACE__ . '\\Support\\plugin_add_settings_link');

// Activation
register_activation_hook( TNYGMAPS_PLUGIN, __NAMESPACE__ . '\\Activation\\activate' );

// Languages
function load_textdomain() {
	load_plugin_textdomain( 'orionrush_tnygmaps', false, TNYGMAPS_PATH . '/lang/' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_textdomain' );