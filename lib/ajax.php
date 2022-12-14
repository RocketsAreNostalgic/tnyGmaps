<?php
namespace OrionRush\TnyGmaps\Ajax;
use OrionRush\TnyGmaps\Support as Support;

if ( ! defined( 'ABSPATH' ) ) {	die(); }

/***********************************************************************
 * Ajax
 * /*********************************************************************/

/**
 * A filterable function which loads the modal.
 *
 * @author orionrush
 * @since 0.0.3
 *
 */

function load_modal() {
	require_once( TNYGMAPS_PATH . 'lib/modal.php' );
}

add_action( 'tnygmaps_modal', __NAMESPACE__ . "\\load_modal", 10 );


/**
 * Returns the modal html content
 *
 * @wp_hook: wp_ajax
 * @since:   0.0.1
 * @author:  orionrush
 *
 */
function modal() {
	ob_start();
	header( 'Content-Type: text/html; charset=utf-8' );
	do_action( 'tnygmaps_modal' );
	$string = ob_get_clean();
	exit( $string );
}

add_action( 'wp_ajax_tnygmaps_modal', __NAMESPACE__ . '\\modal' );


/**
 * Returns the js that triggers google maps in the modal
 *
 * @wp_hook: wp_ajax
 * @since:   0.0.1
 * @author:  orionrush
 */
function loadgmaps() {
	wp_enqueue_script( 'tnygmaps_modal', TNYGMAPS_PATH . 'assets/js/tnygmaps_modal_logic.min.js', false );
}

function enqueue_loadgmaps() {
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\loadgmaps' );
}

add_action( 'wp_ajax_tnygmaps_loadgmaps', __NAMESPACE__ . '\\enqueue_loadgmaps' );


/**
 * Add needed global vars for js to wp head
 * We couldn't use wp_localize_script without making a call to a script, so instead we'll just output it
 * This allows us to cache js and this way we don't have to dynamically load the js through php
 *
 * @wp_hook:   admin_print_scripts
 * @global:    $pagenow
 * @echo:      script with global js values
 *
 * @since:     0.0.1
 * @author:    orionrush
 *
 */
function load_js_globals() {
	global $pagenow;
	if ( ! empty( $pagenow ) && ( 'post-new.php' === $pagenow || 'post.php' === $pagenow ) ) {
		if ( defined( 'GOOGLE_API_KEY' ) && ( constant( 'GOOGLE_API_KEY' ) != null || constant( 'GOOGLE_API_KEY' ) != '' ) ) {
			$have_key = true;
		} else {
			$have_key = false;
		}
		$js_globals['ajaxurl']         = admin_url( 'admin-ajax.php' );
		$js_globals['haveGPlaces_key'] = $have_key;
		$js_globals['pluginURI']       = TNYGMAPS_URL;
		$js_globals['pluginDIR']       = plugin_dir_url( __DIR__ );

		if (file_exists( TNYGMAPS_ICONS_DIR )){
			$js_globals['plugin_icons_URL']       = TNYGMAPS_ICONS_DIR_URL;
		} else {
			$js_globals['plugin_icons_URL']       = TNYGMAPS_URL . 'assets/' . TNYGMAPS_ICONS_DIR_NAME . '/';
		}

		$js_globals['google_icon']    = TNYGMAPS_GOOGLE_ICON_URL;
		$js_globals['google_icon_name']    = Support\gMapsDefultIconName();

		$custom_icon = get_option('tnygmaps_custom_icon');
		if ($custom_icon && $custom_icon != 'gMaps default pin') {
			$js_globals['custom_icon']    = $custom_icon;
		} else {
			$js_globals['custom_icon']    = false;
		}
		echo '<script>var tnygmaps =' . json_encode( $js_globals ) . '</script>';
	}
}

add_action( 'admin_print_scripts', __NAMESPACE__ . '\\load_js_globals' );