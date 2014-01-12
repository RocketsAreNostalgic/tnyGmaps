<?php
if ( ! defined( 'ABSPATH' ) ) die();
/**
* ========================================
* Adds the Google Maps plugin to tinyMCE editor
*
* ======================================== */
// Register all buttons
function tr_maps_register_button( $buttons ) {
   array_push( $buttons, "|", "trgmaps" );
   return $buttons;
}

// Add the plugin js for each button
function tr_maps_add_plugin( $plugin_array ) {
   $plugin_array['trgmaps'] = plugins_url('tinyMCE_plugin/tr_maps_tinyMCE_load_gmaps.js', __FILE__);
   return $plugin_array;
}

// Add the plugin buttons
add_action('init', 'tr_maps_tinyMCE_buttons');
function tr_maps_tinyMCE_buttons() {
   if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
      return;
   }
   // display only if the rich editor is enabled.
   if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_external_plugins', 'tr_maps_add_plugin' );
      add_filter( 'mce_buttons', 'tr_maps_register_button' );
   }
}