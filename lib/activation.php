<?php
namespace OrionRush\TnyGmaps\Activation;
use WP_Error;
if ( ! defined( 'ABSPATH' ) ) { die(); }

/**
 * Check for minimum operating requirements
 * We've not tested this below WP 4.7
 *
 * Adds default options to database.
 *
 * @param string $wpv - Minimum WP Version
 * @param string $phpv - Minimum PHP Version
 *
 * @since 0.0.3
 * @author orionrush
 */
function activate( $blah = null, $phpv = "5.6", $wpv = "4.7" ) {

	$flag           = null;
	$current        = null;
	$target_version = null;
	$wp_version     = get_bloginfo( 'version' );

	if ( version_compare( PHP_VERSION, $phpv, '<' ) ) {
		$flag            = 'PHP';
		$current_version = PHP_VERSION;
		$target_version  = $phpv;
	}
	if ( version_compare( $wp_version, $wpv, '<' ) ) {
		$flag            = 'WordPress';
		$current_version = $wp_version;
		$target_version  = $wpv;

	}

	if ( $flag !== null ) {

		$name   = TNYGMAPS_NAME;
		$format = __( 'Sorry, <strong>%s</strong> requires %s version %s or greater. <br/> You are currently running version: %s' );

		wp_die( sprintf( $format, $name, $flag, $target_version, $current_version ), 'Plugin Activation Error', array(
			'response'  => 500,
			'back_link' => true
		) );
		deactivate_plugins( plugin_basename( TNYGMAPS_PLUGIN ) );

		return;
	}
	// Copy the icons to the uploads folder.
	create_map_icon_directory();
}
/**
 * Create directory for
 * Adds default options to database.
 *
 * @param string $wpv - Minimum WP Version
 * @param string $phpv - Minimum PHP Version
 *
 * @since 0.0.3
 * @author orionrush
 *
 */

function create_map_icon_directory () {

	$errorMessage = new WP_Error();
	$targetIconDir = TNYGMAPS_ICONS_DIR;

	try {
		// test to see if it exists already, test to see if it is empty
		$doesTargetDirExists  = file_exists( $targetIconDir );
		$isTargetDirWriteable = is_writable( $targetIconDir );
		$isTargetDirEmpty     = ! ( new \FilesystemIterator( $targetIconDir ) )->valid();
		} catch ( \Exception $e ) {
		$errorMessage->add( 'Error:', $e->getMessage() );
	}
		try {
			// Make the directory
			if ( wp_mkdir_p( $targetIconDir ) ) {
					try {
						// Copy contents of plugin maps directory to new uploads directory
						$plugin_img_dir = TNYGMAPS_PATH . '/assets/' . TNYGMAPS_ICONS_DIR_NAME . '/';

						$images = opendir( $plugin_img_dir );
						while ( $read_image = readdir( $images ) ) {
							if ( $read_image != '.' && $read_image != '..' ) {
								if ( ! file_exists( $read_image ) ) {
									copy( $plugin_img_dir . $read_image, $targetIconDir . '/' . $read_image );
								}
							}
						}
					} catch ( \Exception $e ) {
						$errorMessage;
					}
			}
		} catch ( \Exception $e ) {
			$errorMessage->add( 'Error:', $e->getMessage() );
		}
	write_log( $errorMessage );
}