<?php
namespace OrionRush\TnyGmaps\Admin;

use OrionRush\TnyGmaps\Support as Support;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}
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
 * @package TNYGMAPS
 */
add_action( 'admin_menu', __NAMESPACE__ . '\\admin_form_create' );


/**
 * Add the admin menu, register settings, and enqueue resources.
 *
 * @since   0.0.4
 * @author orionrush
 * @package TNYGMAPS
 */
function admin_form_create() {
	$plugin_data = Support\getPluginAtts();
	$name         = ( ( ! empty( $plugin_data['Plugin Name'] ) ? $plugin_data['Plugin Name'] : '' ) );

	$page_title = $name;
	$menu_title = $name;
	$capability = 'manage_options';
	$menu_slug  = 'tnygmaps';
	$function   = __NAMESPACE__ . '\\admin_markup';

	$submenu = add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function );

	add_action( 'admin_init', __NAMESPACE__ . '\\register_tnygmaps_settings' );

	// Only load styles and scrips on  on the correct sub-menu page.
	add_action( 'load-' . $submenu, __NAMESPACE__ . '\\load_admin_assets' );
}


/**
 * Register settings
 *
 * @since   0.0.4
 * @author orionrush
 * @package TNYGMAPS
 */
function register_tnygmaps_settings() {

	$args           = array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => null,
	);
	$args_checkbox  = array(
		'type'              => 'int',
		'sanitize_callback' => 'boolval',
		'default'           => true,
	);
	$args_int_width = array(
		'type'              => 'int',
		'sanitize_callback' => __NAMESPACE__ . '\\sanitizeStaticDomWidth',
		'default'           => TNYGMAPS_STATIC_DOM_WIDTH,
	);

	//register our options
	register_setting( 'tnygmaps-settings-group', 'tnygmaps_api_key', $args );
	register_setting( 'tnygmaps-settings-group', 'tnygmaps_custom_icon', $args );
	register_setting( 'tnygmaps-settings-group', 'tnygmaps_mobile', $args_checkbox );
	register_setting( 'tnygmaps-settings-group', 'tnygmaps_mobile_width', $args_int_width );
	register_setting( 'tnygmaps-settings-group', 'tnygmaps_debug', $args_checkbox );

	$icon = '<img id="plugin-icon" src="' . TNYGMAPS_URL . 'assets/img/app_icon_filled.png" >';
	// Add the areas to the options page
	add_settings_section(
		'tnygmaps-settings-group',
		sprintf( __( '%s Tny gMaps Settings', 'orionrush-tnygmaps' ), $icon ),
		'__return_false',
		'tnygmaps-settings-group'
	);

	$keyIconToolTip = sprintf( __( '%s Google requires you have an API key.%s', 'orionrush-tnygmaps' ), '<span data-tooltip="', '">?</span>' );

	add_settings_field(
		'tnygmaps-settings-group-api-key',
		sprintf(__( 'Google API Key: %s', 'orionrush-tnygmaps' ), $keyIconToolTip),
		__NAMESPACE__ . '\\api_key',
		'tnygmaps-settings-group',
		'tnygmaps-settings-group'
	);

	$defaultIconToolTip =  sprintf( __( '%sChoose your site wide default icon. You can choose a different one using when building your map any time you\'d like.%s', 'orionrush-tnygmaps' ), '<span data-tooltip="', '">?</span>' );
	add_settings_field(
		'tnygmaps-settings-group-default-icon',
		sprintf(__( 'Default map icon: %s', 'orionrush-tnygmaps' ), $defaultIconToolTip),
		__NAMESPACE__ . '\\default_icon',
		'tnygmaps-settings-group',
		'tnygmaps-settings-group'
	);


	$staticToolTip =  sprintf( __( '%sThis option produces a %sstatic map%s image rather than a fully interactive map. This wil reduce load times and bandwidth on small %smobile devices%s. %s A width of %s0%spx is equivalent to disabling the feature.%s%s', 'orionrush-tnygmaps' ), '<span data-tooltip="', '<a href=\'https://developers.google.com/maps/documentation/static-maps/\' target=\'_blank\'>', '</a>', '<em><strong>', '</strong></em>', '<br/><br/><em>', '<strong>', '</strong>', '</em>', '">?</span>' );

	add_settings_field(
		'tnygmaps-settings-group-mobile-devices',
		sprintf(__( 'Optimise for mobile: %s', 'orionrush-tnygmaps' ), $staticToolTip),
		__NAMESPACE__ . '\\static_maps',
		'tnygmaps-settings-group',
		'tnygmaps-settings-group'
	);

	$debugToolTip =  sprintf( __( '%sThis option enables front end debugging notices on failed Google API queries, as well as Java Script console messages in your browser\'s developer tools panel. %s Don\'t worry, front end notices only appear to those logged into the front-end with administrator privileges.%s%s', 'orionrush-tnygmaps' ), '<span data-tooltip="', '<br/><br/><em>', '</em>', '">?</span>' );
	add_settings_field(
		'tnygmaps-settings-group-debug',
		sprintf(__( 'Enable Debugging: %s', 'orionrush-tnygmaps' ), $debugToolTip),
		__NAMESPACE__ . '\\debugging',
		'tnygmaps-settings-group',
		'tnygmaps-settings-group'
	);
}

/**
 * Sanitize the settings array
 *
 * @param $input
 *
 * @return array
 *
 * @since 0.0.2
 * @author orionrush
 * @package TNYGMAPS
 */
function settings_sanitize( $input ) {
	$output = array(
		'mobile' => array()
	);
	if ( isset( $input['post_types'] ) ) {
		$post_types = get_post_types();
		foreach ( (array) $input['post_types'] as $post_type ) {
			if ( array_key_exists( $post_type, $post_types ) ) {
				$output['post_types'][] = $post_type;
			}
		}
	}

	return $output;
}

/**
 * Load admin assets only on the proper hook
 * https://wordpress.stackexchange.com/a/76420
 *
 * @since   0.0.4
 * @author orionrush
 * @package TNYGMAPS
 */

function load_admin_assets() {
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_assets' );
}

/**
 * Enqueue admin assets
 *
 * @since   0.0.4
 * @author orionrush
 * @package TNYGMAPS
 */
function enqueue_admin_assets() {

	$plugin_data = Support\getPluginAtts();
	$ver         = ( ( ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : '' ) );

	wp_enqueue_style( 'orionrush-tnygmaps-admin', plugins_url( '../assets/css/tnygmaps_admin.css', __FILE__ ), array( 'imagepicker-css' ), $ver );
	wp_enqueue_script( 'orionrush-tnygmaps-admin-js', plugins_url( '../assets/js/tnygmaps-admin.min.js', __FILE__ ), array( 'jquery' ), $ver, 'false' );

	wp_enqueue_style( 'imagepicker-css', plugins_url( '../assets/js/vendor/image-picker-master/image-picker/image-picker.css', __FILE__ ), array(), $ver );
	wp_enqueue_script( 'imagepicker-js', plugins_url( '../assets/js/vendor/image-picker-master/image-picker/image-picker.min.js', __FILE__ ), array( 'jquery' ), '0.3.0', 'true' );

	wp_enqueue_style( 'jquery-qtip-custom', plugins_url( '../assets/js/vendor/jquery-qtip-custom/jquery.qtip.css', __FILE__ ), array(), '2.1.1' );
	wp_enqueue_script( 'jquery-qtip-custom', plugins_url( '../assets/js/vendor/jquery-qtip-custom/jquery.qtip.min.js', __FILE__ ), array( 'jquery' ), '2.1.1', 'true' );

}

/**
 * Returns either a positive int, or the preset value for DOM width.
 *
 * @param $int
 *
 * @return int
 *
 * @since   0.0.4
 * @author orionrush
 * @package TNYGMAPS
 */
function sanitizeStaticDomWidth( $int ) {
	if ( ! is_numeric( $int ) ) {
		$int = absint( $int );
	}
	if ( $int == null || $int == "" ) {
		return TNYGMAPS_STATIC_DOM_WIDTH;
	} else {
		return $int;
	}
}