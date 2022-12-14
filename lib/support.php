<?php
namespace OrionRush\TnyGmaps\Support;
if ( ! defined( 'ABSPATH' ) ) {
	die();
}


/**
 * Returns an array of plugin details
 * @since 0.0.5
 * @author orionrush
 *
 * @return array
 */
function getPluginAtts() {
	$plugin_data = get_file_data( TNYGMAPS_PLUGIN, array(
		'Plugin Name' => 'Plugin Name',
		'Version'    => 'Version',
		'Author'     => 'Author'
	) );

	return $plugin_data;
}

/**
 * Returns local urls as root relative strings.
 * Takes a full URL, removes the server name and returns the result as a root relative URL.
 *
 * @since 0.0.1
 * @author orionrush
 *
 * @param  string $url The full url to be made root relative.
 *
 * @return string The root relative url.
 */
function make_root_relative( $url ) {
	return preg_replace( '!http(s)?://' . $_SERVER['SERVER_NAME'] . '/!', '/', (string) $url );
}

/**
 * Test if the google api key has been set.
 *
 * @author orionrush
 * @since 0.0.2
 *
 * @return bool
 */
function test_google_key() {
	if ( defined( 'GOOGLE_API_KEY' ) && GOOGLE_API_KEY ) {
		return true;
	}

	return false;
}


/**
 * Set transient time for how often google places api map data is stored before being checked again for freshness.
 *
 * @author orionrush
 * @since 0.0.4
 *
 * @return num
 */

function getMapTransientExpiry() {
	//cache address or place details for 3 months (in seconds)
	$time = 3600 * 24 * 30 * 3;

	return apply_filters( 'tnygmaps_expiry', $time );
}


/**
 * Filterable function to return 'Open map in new window' link
 *
 * @author orionrush
 * @since 0.0.4
 *
 * @return string
 */
function openMapInNewWin() {
	$string = __( 'open map in new window', 'orionrush-tnygmaps' );

	return apply_filters( 'tnygmaps_mapInNewWindow', $string );
}


/**
 * Retrieve the name of the google icon
 * we need this to be translateable so a constant wont do
 *  Used in forms and js
 * @since 0.0.4
 * @return string
 *
 */

function gMapsDefultIconName() {
	$name = __( 'gMaps default pin', 'orionrush-tnygmaps' );

	return $name;
}

/**
 * Checks if the map icons have been moved to the uploads folder and returns the correct url.
 * @author orionrush
 * @since 0.0.4
 * @return string
 */
function loaded_tnygmaps_icons_url() {
	if ( file_exists( TNYGMAPS_ICONS_DIR ) ) {
		$icon_dir_url = TNYGMAPS_ICONS_DIR_URL;
	} else {
		$icon_dir_url = TNYGMAPS_URL . 'assets/' . TNYGMAPS_ICONS_DIR_NAME . '/';
	}

	return $icon_dir_url;
}


/**
 * Retrieve a list of all the icons in the icons directory, as both a list of formatted links, and as an array.
 *
 * @author orionrush
 * @since 0.0.2
 *
 * @return array
 */

function loaded_tnygmaps_icons() {

	if ( ! file_exists( TNYGMAPS_ICONS_DIR ) ) {
		$icons_path = plugin_dir_path( __FILE__ ) . "../assets/" . TNYGMAPS_ICONS_DIR_NAME . "/";
	} else {
		$icons_path = TNYGMAPS_ICONS_DIR;
	}

	$loaded_icons = array_diff( scandir( $icons_path ), array(
		'..',
		'.',
		'.DS_Store',
		'.psd',
		'.tiff',
		'.tif',
		'.pdf'
	) );

	$loaded_icons = array_values( $loaded_icons );

	$icons_list = array_map(
		function ( $el ) {
			return "<a href=\"#\" title=\"{$el}\" class=\"map-icon\">{$el}</a>";
		}, $loaded_icons
	);
	$icons_list = implode( ", ", $icons_list );

	$icons_images_list = array_map(
		function ( $el ) {
			$icons_path = TNYGMAPS_ICONS_DIR;

			return "<li><div class=\"thumbnail\"><img class=\"image_picker_image\" src=\"{$icons_path}{$el}\" alt=\"{$el}\" /></div></li>";
		}, $loaded_icons
	);
	$icons_images_list = implode( "", $icons_images_list );

	return array( $icons_list, $loaded_icons, $icons_images_list );
}

/**
 * A filterable function which loads the modal.
 *
 * @author orionrush
 * @since 0.0.3
 *
 */

function load_modal() {
	include( TNYGMAPS_PATH . 'lib/modal.php' );
}

add_action( 'tnygmaps_modal', __NAMESPACE__ . "\\load_modal", 10 );


/**
 * Adds a settings link to the plugin instance in the plugins management list.
 *
 * @param $links
 *
 * @return mixed
 */
function plugin_add_settings_link( $links = array() ) {
	$links[] = '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=tnygmaps' ) ) . '">Settings</a>';

	return $links;
}

/**
 * A simple logging function good for troubleshooting ajax etc.
 *
 * @param $log // the message or array to be printed to the log
 * @param bool $force // Force a log even if WP_DEBUG_LOG is not enabled
 *
 * @since 0.0.3
 *
 */
function write_log( $log, $force = false ) {
	if ( true === WP_DEBUG_LOG || $force ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}