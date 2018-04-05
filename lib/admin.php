<?php
namespace OrionRush\TnyGmaps\Admin;
if ( ! defined( 'ABSPATH' ) ) { die(); }
require_once( TNYGMAPS_PATH . 'lib/admin-form.php' );

/**
 * Here we will allow users to:
 * Set their GOOGLE API key
 * https://github.com/AyeCode/google-maps-api-key
 * todo: Choose a default map icon from the list of loaded icons
 * todo: Give documentation
 */

/**
 * Kick it all off.
 *
 * @since 0.0.2
 * @author orionrush
 */
add_action( 'admin_menu', __NAMESPACE__ . '\\admin_form_create' );


/**
 * Add the admin menu, register settings, and enqueue resources.
 *
 * @since   0.0.4
 * @package TNYGMAPS
 */
function admin_form_create() {

	$page_title = TNYGMAPS_NAME;
	$menu_title = TNYGMAPS_NAME;
	$capability = 'manage_options';
	$menu_slug = 'tnygmaps';
	$function =  __NAMESPACE__ . '\\admin_markup';

	$submenu =	add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);

	add_action( 'admin_init', __NAMESPACE__ . '\\register_tnygmaps_settings' );

	// Only load styles and scrips on  on the correct sub-menu page.
	add_action( 'load-' . $submenu, __NAMESPACE__ . '\\load_admin_assets' );
}


/**
 * Register settings
 *
 * @since   0.0.4
 * @package TNYGMAPS
 */
function register_tnygmaps_settings() {
    $args = array(
	    'type' => 'string',
	    'sanitize_callback' => 'sanitize_text_field',
	    'default' => NULL,
    );
	//register our options
	register_setting( 'tnygmaps-settings-group', 'tnygmaps_api_key', $args);
	register_setting( 'tnygmaps-settings-group', 'tnygmaps_custom_icon', $args );
}

/**
 * Load admin assets only on the proper hook
 * https://wordpress.stackexchange.com/a/76420
 *
 * @since   0.0.4
 * @package TNYGMAPS
 */

function load_admin_assets() {
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_assets' );
}

/**
 * Enqueue admin assets
 *
 * @since   0.0.4
 * @package TNYGMAPS
 */
function enqueue_admin_assets() {

	wp_enqueue_style('orionrush-tnygmaps-admin', plugins_url('../assets/css/tnygmaps_admin.css', __FILE__), array('imagepicker-css'));
	wp_enqueue_script('orionrush-tnygmaps-admin-js',  plugins_url('../assets/js/tnygmaps-admin.min.js', __FILE__), array('jquery'),'','');

	wp_enqueue_style('imagepicker-css', plugins_url('../assets/js/vendor/image-picker-master/image-picker/image-picker.css', __FILE__), array());
	wp_enqueue_script('imagepicker-js',  plugins_url('../assets/js/vendor/image-picker-master/image-picker/image-picker.min.js', __FILE__), array('jquery'),'0.3.0','true');
}

