<?php
if ( ! defined( 'ABSPATH' ) ) die();
/*

Plugin Name: TR Google Maps Plugin
Description: A google maps plugin sponsored by Tom Rush.com
Version: 0.0.1
License: GPL
Author: Ben Rush
Author URI: http://www.orionrush.com

Inspired by Google Maps v3 Shortcode by yohda, and Pippin's Simple Google Maps Short Code
Works via a tinyMCE modal window which allows the user to input preferences and preview the result.
Map details are saved using the transients api, which are refreshed via Google when the transient expires. If an API key is provided the Places API is available for address lookup, alternatively a manual address lookup can be done using Google's Gecoding API.  
[TRMAP id="map" z="10" w="100%" h="300" placeref="" maptype="ROADMAP" marker="path/to/custom/marker.png" infowindow="Some Content"]
 
*/

/**********************************************************************
 * Acquire a Google Places API key for your website domain
 * See the README.md file for more information
 * https://developers.google.com/places/documentation/
/*********************************************************************/

define('GOOGLEMAPS_API_KEY', 'AIzaSyBJk9dfvS4WYVVzrPNUOshAUZSgqufNSls'); 

/***********************************************************************
 * Definitions
/*********************************************************************/

define('TRMAPS_MAPSPLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('TRMAPS_MAPSPLUGIN_URL', plugin_dir_url( __FILE__ ));

/***********************************************************************
 * Includes
/*********************************************************************/

// Translation files
load_plugin_textdomain( 'trmaps', false, TRMAPS_MAPSPLUGIN_PATH . '/inc/languages/' );
// Admin screen
require_once ( TRMAPS_MAPSPLUGIN_PATH . 'inc/tr_maps_admin.php');
// Shortcode 
require_once ( TRMAPS_MAPSPLUGIN_PATH . 'inc/tr_maps_shortcode.php');
// Modal window and tinyMCE button
require_once ( TRMAPS_MAPSPLUGIN_PATH . 'inc/tr_maps_tinyMCE.php');