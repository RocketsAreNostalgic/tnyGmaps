<?php
namespace OrionRush\TnyGmaps\TinyMCE;
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Add additional plugin button to tinyMCE button array
 *
 * @wp_hook: init via tinyMCE_buttons()
 * @since:   0.0.1
 * @author:  orionrush
 *
 * @param  array $buttons [tinyMCE button array]
 *
 * @return array $buttons [tinyMCE button array with button added]
 */
function register_button( $buttons ) {
	array_push( $buttons, "TNYGMAPS" );

	return $buttons;
}

/**
 * Add the plugin js for each button
 *
 * @wp_hook: init via tinyMCE_buttons()
 * @since:   0.0.1
 * @author:  orionrush
 *
 * @param  array $plugin_array [tinyMCE plugin array]
 *
 * @return array    [tinyMCE plugin array with TNYGMAPS js added]
 */
function add_plugin( $plugin_array ) {
	$plugin_array['TNYGMAPS'] = plugins_url( 'js/tnygmaps_tinyMCE_load_gmaps.min.js', __FILE__ );

	return $plugin_array;
}

/**
 * Add the plugin buttons to tinymce visual editor, via add_filter hooks
 *
 * @wp_hook: init
 * @since:   0.0.1
 * @author:  orionrush
 *
 * @return NULL
 */
function tinyMCE_buttons() {
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	// display only if the rich editor is enabled.
	if ( get_user_option( 'rich_editing' ) == 'true' ) {
		add_filter( 'mce_external_plugins', __NAMESPACE__ . '\\add_plugin' );
		add_filter( 'mce_buttons', __NAMESPACE__ . '\\register_button' );
	}
}

add_action( 'init', __NAMESPACE__ . '\\tinyMCE_buttons' );
