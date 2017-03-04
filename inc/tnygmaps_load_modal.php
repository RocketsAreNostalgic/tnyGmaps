<?php
namespace OrionRush\TnyGmaps\LoadModal;
if ( ! defined( 'ABSPATH' ) ) {
	die();
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
 * Retrieve a list of all the icons in the icons directory, as both a list, and as an array.
 *
 * @author orionrush
 * @since 0.0.2
 *
 * @return array
 */

function loaded_tnygmaps_icons() {
	$loaded_icons = plugin_dir_path( __FILE__ ) . "/icons/";
	$loaded_icons = array_diff( scandir( $loaded_icons ), array(
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


	return array( $icons_list, $loaded_icons );
}

/**
 * A filterable function which loads the modal.
 *
 * @author orionrush
 * @since 0.0.3
 *
 */

function load_modal() {
	include_once( TNYGMAPS_PATH . 'inc/tnygmaps_modal.php' );
}

add_action( 'tnygmaps_modal', __NAMESPACE__ . "\\load_modal", 10 );
do_action( 'tnygmaps_modal' );

