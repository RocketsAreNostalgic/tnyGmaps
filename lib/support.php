<?php
namespace OrionRush\TnyGmaps\Support;
if ( ! defined( 'ABSPATH' ) ) { die(); }

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
 * Retrieve a list of all the icons in the icons directory, as both a list of formatted links, and as an array.
 *
 * @author orionrush
 * @since 0.0.2
 *
 * @return array
 */

//		$loaded_icons = plugin_dir_path( __FILE__ ) . "../assets/" . TNYGMAPS_ICONS_DIR_NAME . "/";

function loaded_tnygmaps_icons() {

	if ( !file_exists( TNYGMAPS_ICONS_DIR ) ) {
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

	$icons_list   = array_map(
		function ( $el ) {
			return "<a href=\"#\" title=\"{$el}\" class=\"map-icon\">{$el}</a>";
		}, $loaded_icons
	);
	$icons_list   = implode( ", ", $icons_list );

	$icons_images_list = array_map(
		function ( $el ) {
			$icons_path = TNYGMAPS_ICONS_DIR;
			return "<li><div class=\"thumbnail\"><img class=\"image_picker_image\" src=\"{$icons_path}{$el}\" alt=\"{$el}\" /></div></li>";
		}, $loaded_icons
	);
	$icons_images_list   = implode( "", $icons_images_list );

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
 * Adds a settings link to the plugin instance in the plugins managment list.
 *
 * @param $links
 *
 * @return mixed
 */
function plugin_add_settings_link( $links = array() ) {
	$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=tnygmaps') ) .'">Settings</a>';
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
function write_log( mixed $log, $force = false ) {
	if ( true === WP_DEBUG_LOG || $force ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}
