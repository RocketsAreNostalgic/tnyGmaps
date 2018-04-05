<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

// Are we in a multisite install?
if ( is_multisite() ) {
	global $wpdb;
	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );

	if ( ! empty( $blogs ) ) {
		foreach ( $blogs as $blog ) {
			$blog_id = $blog['blog_id'];
			switch_to_blog( $blog_id );
			tnysig_delete_all_options( $blog_id );
		}
	} else {
		// somehow even though we're in multisite $blogs is empty
		tnygmaps_delete_all_options();
	}

} else {
	// We're in single site install
	tnygmaps_delete_all_options();
}

/**
 * Delete plugin options and any user meta
 *
 * @param null $blog_id
 */
function tnygmaps_delete_all_options( $blog_id = null ) {

	// Plugin options
	delete_option( 'tnygmaps_api_key' );
	delete_option( 'tnygmaps_custom_icon' );
	delete_option( 'orionrush_tnygmaps_options' );  // Legacy

}