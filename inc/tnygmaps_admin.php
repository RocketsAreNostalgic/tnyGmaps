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
	if ( isset( $_POST['tnygmaps'] ) ) {
		$key = esc_attr( $_POST['tnygmaps'] );
		if ( $key ) {
			$updated = update_option( 'tnygmaps', $key );
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
            <!-- encoded    https://console.developers.google.com/henhouse/?pb=[%22hh-1%22,%22maps_backend%22,null,[],%22https://developers.google.com%22,null,[%22maps_backend%22,%22geocoding_backend%22,%22directions_backend%22,%22distance_matrix_backend%22,%22elevation_backend%22,%22places_backend%22],null]&TB_iframe=true&width=600&height=400-->
            <a href='https://console.developers.google.com/henhouse/?pb=["hh-1","maps_backend",null,[],"https://developers.google.com",null,["maps_backend","geocoding_backend","directions_backend","distance_matrix_backend","elevation_backend","places_backend"],null]&TB_iframe=true&width=600&height=400'
               class="thickbox button-primary"
               name="<?php _e( 'Generate API Key - ( MUST be logged in to your Google account )', 'orionrush_tnygmaps' ); ?>">
				<?php _e( 'Generate Google API Key', 'orionrush_tnygmaps' ); ?>
            </a>
			<?php echo sprintf( __( 'or %sclick here%s.', 'orionrush_tnygmaps' ), '<a target="_blank" href=\'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend&keyType=CLIENT_SIDE&reusekey=true\'>', '</a>' ) ?>
        </p>

        <form method="post" action="options-general.php?page=tny-gmaps">
            <label for="rgmk_google_map_api_key"><?php _e( 'Google Maps API KEY', 'orionrush_tnygmaps' ); ?></label>
            <input title="<?php _e( 'Copy your API key here', 'orionrush_tnygmaps' ); ?>" type="text"
                   name="tnygmaps_api_key" id="tnygmaps_api_key"
                   placeholder="<?php _e( 'Copy your API key here', 'orionrush_tnygmaps' ); ?>"
                   style="padding: 6px; width:50%; display: block;"
                   value="<?php echo esc_attr( get_option( 'tnygmaps' ) ); ?>"/>

			<?php
			submit_button();
			?>
        </form>
    </div>
	<?php
}