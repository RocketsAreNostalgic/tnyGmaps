<?php
if ( ! defined( 'ABSPATH' ) ) die();
/**
* ========================================
* Here we add any buttons to the TINYMCE menu to make it easy for editors
* http://wp.smashingmagazine.com/2012/05/01/wordpress-shortcodes-complete-guide/
* ======================================== */

// Register all buttons
function register_button( $buttons ) {
   array_push( $buttons, "|", "trsig", "|", "trdates", "|", "trgmaps" );
   return $buttons;
}

// Add the plugin js for each button
function add_plugin( $plugin_array ) {
   //$plugin_array['trsig'] = plugin_dir_path( __FILE__ ) . 'js/TinyMCE_additions/_tr_sig.js';
   $plugin_array['trsig'] = plugins_url('js/TinyMCE_additions/tr_sig.js', __FILE__);
   $plugin_array['trdates'] = plugins_url('js/TinyMCE_additions/tr_dates.js', __FILE__);
   $plugin_array['trgmaps'] = plugins_url('js/TinyMCE_additions/tr_gmaps.js', __FILE__);
   return $plugin_array;
}

/**
* ========================================
* Google Maps
* Inserts populated Googal Map short coded
* resorurces - _tr_gmaps-shortcode.php
* ======================================== */

/**
* ========================================
* Tr Signature
* inserts a span which includes the Signature name, and image via css replacement method
* css via main style sheet
* ======================================== */

/**
* ========================================
* Tour Dates button
* adds shortcode to list a range of the Tour Dates Post type
* Resources jquery ui - datepicker
* ======================================== */

// jquery UI datepicker css has been 'scoped' with the prefix '_tr'
function tr_datepicker_init() {
   $pluginfolder = get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
   wp_enqueue_script('jquery-ui-core', $pluginfolder . '/inc/js/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js', 'jquery' );
   wp_enqueue_script('jquery-ui-datepicker', $pluginfolder . '/inc/js/jquery-ui-1.9.2.custom/js/jquery.ui.datepicker.min.js', array('jquery', 'jquery-ui-core') );
   wp_enqueue_style('jquery.ui.theme', $pluginfolder . '/js/jquery-ui-1.9.2.custom/css/TR/jquery-ui-1.9.2.custom.min.css');
}

function tr_datepicker_footer() {
   ?>
   <script type="text/javascript">
   jQuery(document).ready(function(){
      jQuery('.mydatepicker').datepicker({
         dateFormat : 'yy-mm-dd'
      });
   });
   </script>
   <?php
}

/* Add the plugin buttons */
function tr_tinyMCE_buttons() {
   if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
      return;
   }
   // display only if the rich editor is enabled.
   if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_external_plugins', 'add_plugin' );
      add_filter( 'mce_buttons', 'register_button' );
      add_action('admin_init', 'tr_datepicker_init');
      add_action('admin_footer', 'tr_datepicker_footer');
   }
}
add_action('init', 'tr_tinyMCE_buttons');

