<?php
namespace OrionRush\TnyGmaps;
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Plugin Name: Tny gMaps
 * Description: A Google maps plugin powered Google's places, static maps and geocoding APIs.
 * Version: 0.0.2
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
 * [TNYGMAP id="map" z="10" w="100%" h="300" placeref="" maptype="ROADMAP" marker="path/to/custom/marker.png" infowindow="Some Content"]
 */

/***********************************************************************
 * Definitions
 * /*********************************************************************/
define( 'TNYGMAPS_VERSION', '0.0.4' );
define( 'TNYGMAPS_NAME', __('Tny gMaps', 'orionrush_tnygmaps' ) );
define( 'TNYGMAPS_PATH', plugin_dir_path( __FILE__ ) );
define( 'TNYGMAPS_URL', plugin_dir_url( __FILE__ ) );


/**********************************************************************
 * Acquire a Google Places API key for your website domain
 * See the README.md file for details
 * https://developers.google.com/places/documentation/
 * /*********************************************************************/
if ( ! defined( 'GOOGLE_API_KEY' ) ) {
	define( 'GOOGLE_API_KEY', 'AIzaSyB3gFO_LJ0GbDFlj2vmZyKM5G0Zge3JBzk' );
}

//Set the debug var as a global, $tnygmaps_debug = true;
global $tnygmaps_debug;

/***********************************************************************
 * Includes
 * *********************************************************************/

// Translation files
load_plugin_textdomain( 'orionrush_tnygmaps', false, TNYGMAPS_PATH . '/lang/' );

// Helper functions
require_once( TNYGMAPS_PATH . 'inc/tnygmaps_support.php' );

// Admin screen
require_once ( TNYGMAPS_PATH . 'inc/tnygmaps_admin.php');

// Ajax functions, must be in the main plugin file
require_once( TNYGMAPS_PATH . 'inc/tnygmaps_ajax.php' );

// Shortcode
require_once( TNYGMAPS_PATH . 'inc/tnygmaps_shortcode.php' );

// GUI via modal and tinyMCE button
require_once( TNYGMAPS_PATH . 'inc/tnygmaps_tinyMCE.php' );

