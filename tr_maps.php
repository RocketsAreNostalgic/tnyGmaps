<?php
if ( ! defined( 'ABSPATH' ) ) die();
/*
Plugin Name: tr_maps
Description: A google maps plugin for Tom Rush.com
Version: 0.1
License: GPL
Author: Ben Rush
Author URI: http://www.orionrush.com
*/

/***********************************************************************
 * See the Read Me.txt file for further information.
/*********************************************************************/


/***********************************************************************
 * Definitions
/*********************************************************************/
define('TR_MAPSPLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('TR_MAPSPLUGIN_URL', plugin_dir_url( __FILE__ ));

/***********************************************************************
 * Includes
/*********************************************************************/
//include( TR_MAPSPLUGIN_PATH . 'inc/tr_maps_shortcode.php');
//include( TR_MAPSPLUGIN_PATH . 'inc/tr_maps_tinyMCE.php');

/***********************************************************************
 * Resource Loading
/*********************************************************************/

// Register Scripts & Styles
// check the current post for the existence of a short code



// function tr_gmaps_scripts() {
//     wp_register_script( 'googelmaps', 'http://maps.google.com/maps/api/js?libraries=places&sensor=false' , '', '', true );
//     wp_register_style( 'googelmaps', TR_MAPSPLUGIN_URL . 'css/maps.css', '', NULL, $media = 'all' );
// }

// if(!has_shortcode('contactform')) {
//     add_action( 'wp_enqueue_scripts', 'tr_gmaps_scripts' );
// }

// // lets start by detecting if the shortcode exsits so we only load up css and js when needed.
// global $post;
// $post_to_check = get_post(get_the_ID());
// echo $post_to_check;
// exit;
// function tr_maps_futures($post) {
//   if ( empty($post) ) // return true (no posts) in admin as well.
//     return $post;
//   $found = false;  // false because we have to search through the posts first

//   // search through each post
//   foreach ($post as $posts) {
//     // check the post content for the short code
//     if ( stripos($post->post_content, '[tr_gmaps') )
//         // we have found a post with the short code
//         $found = true;
//         break;
//     }
//   if (!$found){
//     add_action( 'wp_enqueue_scripts', 'tr_gmaps_scripts' );
//     echo "found it!";
//     exit;
//   }
//   return $post;
// }