<?php
if ( ! defined( 'ABSPATH' ) ) die();
/***********************************************************************
 * Ajax
/*********************************************************************/

/*
 * Returns the modal html content
 *
 * @wp_hook: wp_ajax
 * @since:   0.0.1
 * @author:  orionrush
 *
 */
function tinygmaps_modal() {
    ob_start();
    header('Content-Type: text/html; charset=utf-8');
    include ( TINYGMAP_PATH . 'inc/tinymce/modal/tinygmaps_modal.php');
    $string = ob_get_clean();
    exit($string);
}
add_action('wp_ajax_tinygmaps_modal', 'tinygmaps_modal');

/*
 * Returns the js that triggers google maps in the modal
 *
 * @wp_hook: wp_ajax
 * @since:   0.0.1
 * @author:  orionrush
 *
 */
function tinygmaps_loadgmaps() {

        ob_start();
        header('Content-Type: text/javascript; charset=utf-8');
        include ( TINYGMAP_PATH . 'inc/tinymce/modal/tinygmaps_modal_loadmaps.min.js');
        $string = ob_get_clean();
        exit($string);
}
add_action('wp_ajax_tinygmaps_loadgmaps', 'tinygmaps_loadgmaps');

 /*
  * Add needed global vars to js to wp head
  * We couldn't use wp_localize_script without making a call to a script, so instead we'll just output it
  * This allows us to cache js and this way we dont have to dynamically load the js through php
  *
  * @wp_hook:   admin_print_scripts
  * @global:    $pagenow
  * @echo:      script with global js values
  *
  * @since:     0.0.1
  * @author:    orionrush
  *
  */
function tinygmaps_load_js_globals (){
    global $pagenow;
    if (! empty($pagenow) && ('post-new.php' === $pagenow || 'post.php' === $pagenow )){
        if ( defined('GOOGLEMAPS_API_KEY') && (constant('GOOGLEMAPS_API_KEY') != null || constant('GOOGLEMAPS_API_KEY') != '') ) {
            $have_key = true;
        } else {
            $have_key = false;
        }
        $js_globals[ 'ajaxurl' ] =  admin_url( 'admin-ajax.php' );
        $js_globals[ 'haveGPlaces_key' ] = $have_key;
        $js_globals[ 'pluginURI' ] = TINYGMAP_URL;

        echo '<script>var tinygmaps =' . json_encode($js_globals) . '</script>';
    }

}
add_action( 'admin_print_scripts', 'tinygmaps_load_js_globals' );
