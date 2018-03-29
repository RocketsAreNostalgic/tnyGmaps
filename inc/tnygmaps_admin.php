<?php
namespace OrionRush\Admin;
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/*
Here we will allow users to:
Set their GOOGLE API key
Choose a default map icon from the list of loaded icons
Give documentation
*/

//https://github.com/AyeCode/google-maps-api-key
add_action( 'admin_menu', __NAMESPACE__ . '\\admin_form' );

/**
 * Add the admin menu link.
 *
 * @since   0.0.4
 * @package TNYGMAPS
 */
function admin_form() {
	add_submenu_page( 'options-general.php', TNYGMAPS_NAME, TNYGMAPS_NAME, 'manage_options', 'tnygmaps', __NAMESPACE__ . '\\admin_markup' );
}

/**
 * The html output for the settings page.
 *
 * @since   0.0.4
 * @package TNYGMAPS
 */
function admin_markup() {
	add_thickbox();
	$updated = false;
	if ( isset( $_POST['tnygmaps_api_key'] ) ) {
		$key = esc_attr( $_POST['tnygmaps_api_key'] );
		if ( $key ) {
			$updated = update_option( 'tnygmaps_api_key', trim($key) );
		}
	}
	if ( $updated ) {
		echo '<div class="updated fade"><p><strong>' . __( 'Settings Updates!', 'orionrush_tnygmaps' ) . '</strong></p></div>';
	}
	?>
    <div class="wrap">

    <h2><?php echo TNYGMAPS_NAME ?></h2>
    <p><?php sprintf( __( 'Setting for the %s plugin.', 'orionrush_tnygmaps' ), TNYGMAPS_NAME ); ?></p>
    <p>
        <!--https://developers.google.com/maps/documentation/android-api/signup -->
        <!-- encoded    https://console.developers.google.com/henhouse/?pb=%5B%22hh-0%22,%22maps_backend%22,null,%5B%5D,%22https:%2F%2Fdevelopers.google.com%22,null,%5B%22geocoding_backend%22,%22directions_backend%22,%22distance_matrix_backend%22,%22elevation_backend%22,%22places_backend%22%5D,null,null,null,null,%5B%5D%5D-->
        <a href="#TB_inline?width=600&height=550&inlineId=tnygmaps_api_modal" id="generate-api-button" class="thickbox button-primary" name="<?php _e( 'Generate Google API Key (You must be logged in to a Google account)', 'orionrush_tnygmaps' ); ?>">
            <?php _e( 'Generate Google API Key', 'orionrush_tnygmaps' ); ?>
        </a>
    </p>

    <div id="tnygmaps_api_modal" style="display:none;">
        <style>
            #TB_ajaxContent {padding:0px;}
            #TB_window {width: 600px!important; height: 470px;}                                                                                                                                                                                                                                                                                                                                #TB_ajaxContent {padding:0px;}
        </style>
        <div id="spinner" class="" style="left: 280px; top: 200px; position: absolute;"></div>
            <iframe
                id = "tnygmaps-api-iframe"
                marginheight = "0"
                marginwidth = "0"
                width = "600"
                height = "300"
                src = 'https://console.developers.google.com/henhouse/?pb=%5B%22hh-0%22,%22maps_backend%22,null,%5B%5D,%22https:%2F%2Fdevelopers.google.com%22,null,%5B%22geocoding_backend%22,%22directions_backend%22,%22distance_matrix_backend%22,%22elevation_backend%22,%22places_backend%22%5D,null,null,null,null,%5B%5D%5D'
            >
            </iframe>
                <div class="notice-warning" style="
                            line-height: 19px;
                            padding: 11px 15px;
                            font-size: 14px;
                            text-align: left;
                            margin: 25px 20px 0 15px;
                            background-color: #fff;
                            border-left: 4px solid #ffba00;
                            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,0.1);
                            box-shadow: 0 1px 1px 0 rgba(0,0,0,0.1);"><p><?php echo sprintf(
                        __( 'An error above likely means you haven&#8217t logged into a %sGoogle account%s. %s Read the docs on how to activate an %sapi key%s.', 'orionrush_tnygmaps' ),
			                '<a id="external-api-key" target="_blank" href=\'https://accounts.google.com/signin/v2/identifier?service=cloudconsole&passive=1209600&osid=1&continue=https%3A%2F%2Fconsole.developers.google.com%2Fflows%2Fenableapi%3Fapiid%3Dmaps_backend%2Cmaps_embed_backend%2Cstatic_maps_backend%2Cgeocoding_backend%2Cdirections_backend%2Cdistance_matrix_backend%2Cplaces_backend%2Celevation_backend%26keyType%3DCLIENT_SIDE%26reusekey%3Dtrue&followup=https%3A%2F%2Fconsole.developers.google.com%2Fflows%2Fenableapi%3Fapiid%3Dmaps_backend%2Cmaps_embed_backend%2Cstatic_maps_backend%2Cgeocoding_backend%2Cdirections_backend%2Cdistance_matrix_backend%2Cplaces_backend%2Celevation_backend%26keyType%3DCLIENT_SIDE%26reusekey%3Dtrue&flowName=GlifWebSignIn&flowEntry=ServiceLogin\'>',
			                '</a>',
                            '</br>',
                            '<a id="external-api-key" target="_blank" href=\'https://developers.google.com/maps/documentation/javascript/get-api-key\'>',
                            '</a>' ); ?></p>
                </div>
         <script>
             jQuery(document).ready(function($) {
                 jQuery('#generate-api-button').on("click", function(){
                     jQuery('#spinner').addClass('spinner is-active');
                     console.log('starting spinning');
                 });
                 jQuery('#tnygmaps-api-iframe').on("load", function () {
                     jQuery('#spinner').removeClass('spinner is-active');
                 });
                 jQuery('#external-api-key').on('click', function(){
                     jQuery('#TB_closeWindowButton').click();
                 })
             });
        </script>
        </div>
        <form method="post" action="options-general.php?page=tnygmaps">
            <label for="tnygmaps_api_key"><?php _e( 'Google Maps API key', 'orionrush_tnygmaps' ); ?></label>
            <input title="<?php _e( 'Copy your API key here', 'orionrush_tnygmaps' ); ?>"
                   type="text"
                   name="tnygmaps_api_key"
                   id="tnygmaps_api_key"
                   placeholder="<?php _e( 'Copy your API key here', 'orionrush_tnygmaps' ); ?>"
                   style="padding: 6px; width:50%; display: block;"
                   value="<?php echo esc_attr( get_option( 'tnygmaps_api_key' ) ); ?>"/>
            <p><?php echo sprintf(__('Make sure to restrict where your key can be used via Google\'s %sAPI Console%s.', 'orionrush_tnygmaps'), '<a href="https://console.developers.google.com/apis/credentials/" target="_blank">', '</a>');?></p>
                <?php
                submit_button();
			    ?>
        </form>
    </div>
	<?php
}