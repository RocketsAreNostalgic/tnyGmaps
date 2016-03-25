<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
/*

Plugin Name: Tiny Google Maps Plugin
Description: A google maps plugin sponsored by Tom Rush.com
Version: 0.0.2
License: GPL
Author: Ben Rush
Author URI: http://www.orionrush.com

Inspired by Google Maps v3 Shortcode by yohda, and Pippin's Simple Google Maps Short Code
Works via a tinyMCE modal window which allows the user to input preferences and preview the result.
Map details are saved using the transients api, which are refreshed via Google when the transient expires. If an API key is provided the Places API is available for address lookup, alternatively a manual address lookup can be done using Google's Gecoding API.  
[TINYGMAP id="map" z="10" w="100%" h="300" placeref="" maptype="ROADMAP" marker="path/to/custom/marker.png" infowindow="Some Content"]
 
*/

/**********************************************************************
 * Acquire a Google Places API key for your website domain
 * See the README.md file for more information
 * https://developers.google.com/places/documentation/
 * /*********************************************************************/
if ( ! defined( 'GOOGLE_API_KEY' ) ) {
	define( 'GOOGLE_API_KEY', 'AIzaSyBJk9dfvS4WYVVzrPNUOshAUZSgqufNSls' );
}
/***********************************************************************
 * Definitions
 * /*********************************************************************/

define( 'TINYGMAP_PATH', plugin_dir_path( __FILE__ ) );
define( 'TINYGMAP_URL', plugin_dir_url( __FILE__ ) );


// //Set the debug var as global
global $tinygmaps_debug;
//$tinygmaps_debug = true;
/***********************************************************************
 * Includes
 * /*********************************************************************/

// Translation files
load_plugin_textdomain( 'tinygmaps', false, TINYGMAP_PATH . '/inc/languages/' );
// Admin screen
//require_once ( TINYGMAP_PATH . 'inc/tr_maps_admin.php');


// Ajax functions, must be in the main plugin file
require_once( TINYGMAP_PATH . 'inc/tinygmaps_ajax.php' );
// Shortcode
require_once( TINYGMAP_PATH . 'inc/tinygmaps_shortcode.php' );
// GUI via modal and tinyMCE button
require_once( TINYGMAP_PATH . 'inc/tinygmaps_tinyMCE.php' );
