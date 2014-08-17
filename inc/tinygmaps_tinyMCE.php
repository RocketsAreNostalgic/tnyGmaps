<?php
if ( ! defined( 'ABSPATH' ) ) die();
/**
* ========================================
* Add the Map Modal button to tinyMCE editor
*
* ======================================== */
// Register all buttons
/**
 * [tinygmaps_register_button Add additional plugin button to tinyMCE button array]
 * @param  array $buttons [tinyMCE button array]
 * @return array $buttons [tinyMCE button array with button added]
 */
function tinygmaps_register_button( $buttons ) {
   array_push( $buttons, "|", "TINYGMAPS" );
   return $buttons;
}

/**
 * [tinygmaps_add_plugin Add the plugin js for each button]
 * @param  array    $plugin_array [tinyMCE plugin array]
 * @return array    [tinyMCE plugin array with TINYGMAPS js added]
 */
function tinygmaps_add_plugin( $plugin_array ) {  
   $plugin_array['TINYGMAPS'] = plugins_url('/tinymce/tinygmaps_tinyMCE_load_gmaps.js', __FILE__);
   return $plugin_array;
}

/**
 * [tinygmaps_tinyMCE_buttons Add the plugin buttons via the add_filter hook]
 * @return NULL
 */
add_action('init', 'tinygmaps_tinyMCE_buttons');
function tinygmaps_tinyMCE_buttons() {
   if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
      return;
   }
   // display only if the rich editor is enabled.
   if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_external_plugins', 'tinygmaps_add_plugin' );
      add_filter( 'mce_buttons', 'tinygmaps_register_button' );
   }
}