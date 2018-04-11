<?php
namespace OrionRush\TnyGmaps\Admin;
use OrionRush\TnyGmaps\Support as Support;

if ( ! defined( 'ABSPATH' ) ) { die(); }

/**
 * The html output for the settings page.
 *
 * @since   0.0.4
 * @package TNYGMAPS
 */
function admin_markup() {
	add_thickbox();
	?>
    <div class="wrap">
        <form method="post" action="options.php">
			<?php do_settings_sections( 'tnygmaps-settings-group' ); ?>
	        <?php settings_fields( 'tnygmaps-settings-group' ); ?>
			<?php submit_button(); ?>
        </form>
	<?php
}

function api_key () {
    ?>
    <div class="tnygmaps-api-wrap">
        <div class="tnygmaps-wrap">
            <div class="tnygmaps-stretch">
                <input title="<?php _e( 'Copy your API key here', 'orionrush-tnygmaps' ); ?>"
                       type="text"
                       name="tnygmaps_api_key"
                       id="tnygmaps_api_key"
                       placeholder="<?php _e( 'Copy your API key here', 'orionrush-tnygmaps' ); ?>"
                       value="<?php echo esc_attr( trim(get_option( 'tnygmaps_api_key' ))); ?>"
                />
            </div>
            <div class="tnygmaps-normal">
                <a href="#TB_inline?width=600&height=420&inlineId=tnygmaps_api_modal" id="generate-api-button" class="thickbox tnymaps-api-button" title="<?php _e( 'Generate Google API Key (You must be logged in to a Google account)', 'orionrush-tnygmaps' ); ?>">
					<?php _e( 'Generate Key', 'orionrush-tnygmaps' ); ?>
                </a>
            </div>
        </div>
        <div class="bootstrap-tny">
            <p class=" alert"><?php echo sprintf(__('%sNote:%s  For better security, be sure to restrict where your key can be used via Google\'s %sAPI Console%s.', 'orionrush-tnygmaps'), '<strong>', '</strong>', '<a href="https://console.developers.google.com/apis/credentials/" target="_blank">', '</a>');?></p>
        </div>
    </div>
    <!-- API key modal -->
    <!--https://developers.google.com/maps/documentation/android-api/signup -->
    <!-- encoded    https://console.developers.google.com/henhouse/?pb=%5B%22hh-0%22,%22maps_backend%22,null,%5B%5D,%22https:%2F%2Fdevelopers.google.com%22,null,%5B%22geocoding_backend%22,%22directions_backend%22,%22distance_matrix_backend%22,%22elevation_backend%22,%22places_backend%22%5D,null,null,null,null,%5B%5D%5D-->
    <div id="tnygmaps_api_modal" class="bootstrap-tny" style="display:none;">
        <div id="spinner" class="" style="left: 280px; top: 200px; position: absolute;"></div>
        <iframe
			<?php // use http rather then https to prevent errors Failed to execute postMessage on 'DOMWindow' ?>
                id = "tnygmaps-api-iframe"

                src = 'http://console.developers.google.com/henhouse/?pb=%5B%22hh-0%22,%22maps_backend%22,null,%5B%5D,%22https:%2F%2Fdevelopers.google.com%22,null,%5B%22geocoding_backend%22,%22directions_backend%22,%22distance_matrix_backend%22,%22elevation_backend%22,%22places_backend%22%5D,null,null,null,null,%5B%5D%5D'
        >
        </iframe>
        <div class="bootstrap-tny">
            <div class="alert" style=""><p><?php echo sprintf(
						__( 'An error above likely means you haven&#8217t logged into a %sGoogle account%s. %s For more information, read the docs on how %sapi keys%s work.', 'orionrush-tnygmaps' ),
						'<a id="external-api-key" target="_blank" href=\'http://accounts.google.com/signin/v2/identifier?service=cloudconsole&passive=1209600&osid=1&continue=https%3A%2F%2Fconsole.developers.google.com%2Fflows%2Fenableapi%3Fapiid%3Dmaps_backend%2Cmaps_embed_backend%2Cstatic_maps_backend%2Cgeocoding_backend%2Cdirections_backend%2Cdistance_matrix_backend%2Cplaces_backend%2Celevation_backend%26keyType%3DCLIENT_SIDE%26reusekey%3Dtrue&followup=https%3A%2F%2Fconsole.developers.google.com%2Fflows%2Fenableapi%3Fapiid%3Dmaps_backend%2Cmaps_embed_backend%2Cstatic_maps_backend%2Cgeocoding_backend%2Cdirections_backend%2Cdistance_matrix_backend%2Cplaces_backend%2Celevation_backend%26keyType%3DCLIENT_SIDE%26reusekey%3Dtrue&flowName=GlifWebSignIn&flowEntry=ServiceLogin\'>',
						'</a>',
						'</br>',
						'<a id="external-api-key" target="_blank" href=\'http://developers.google.com/maps/documentation/javascript/get-api-key\'>',
						'</a>' ); ?></p>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                console.log('load');

                jQuery('#generate-api-button').on("click", function(){
                    jQuery('#spinner').addClass('spinner is-active');
                    console.log('starting spinning');
                });
                jQuery('#tnygmaps-api-iframe').on("load", function () {
                    jQuery('#spinner').removeClass('spinner is-active');
                });
            });
        </script>
    </div>
<?php
}

function default_icon () {
	// Loaded icons
	$icons_array  = Support\loaded_tnygmaps_icons();
	$gMapsIconName = Support\gMapsDefultIconName();

	if ( !file_exists( TNYGMAPS_ICONS_DIR) ) {
		$path = TNYGMAPS_URL . "assets/" . TNYGMAPS_ICONS_DIR_NAME . "/";
	} else {
		$path = TNYGMAPS_ICONS_DIR_URL;
	}

	$current_icon = esc_attr( get_option( 'tnygmaps_custom_icon' ) );

    ?>
    <!-- Selection grid of icons -->
    <!-- https://rvera.github.io/image-picker/-->
    <div class="bootstrap-tny">
        <div class="well">
            <select class="tnygmaps_custom_icon image-picker show-html"
                    data-limit="1"
                    name="tnygmaps_custom_icon"
                    id="tnygmaps_custom_icon" >
                <option name="tnygmaps_custom_icon_option" value="<?php echo $gMapsIconName ?>" data-img-src="<?php echo TNYGMAPS_GOOGLE_ICON_URL ?>" data-img-alt="<?php echo $gMapsIconName ?>"><?php _e('Google Pin', 'orionrush-tnygmaps');?></option>
				<?php foreach ($icons_array[1] as $key=>$value) : ?>
					<?php $icon_url = $path . $value; ?>
					<?php   $selected = ($current_icon ==  $icon_url) ? true : false ?>
                    <option name="tnygmaps_custom_icon_option"
						<?php if ($selected) { echo 'selected'; }?>
                            value="<?php echo $icon_url; ?>"
                            data-img-src="<?php echo $icon_url ?>"
                            data-img-alt="<?php echo $value ?>" >
                    </option>
				<?php endforeach; ?>
            </select>
        </div>
    </div>
    <strong><?php _e( 'Your icon URL:', 'orionrush-tnygmaps' ); ?></strong>
    <div id="tnygmaps_custom_icon_url" class="bootstrap-tny">
		<?php echo '<pre class="bootstrap-tny">' . $current_icon . '</pre>'; ?>
    </div>
	<?php if ( !file_exists( TNYGMAPS_ICONS_DIR ) ) : ?>
        <div class="bootstrap-tny">
            <p class=" alert"><?php echo sprintf(__('%sNote:%s  It looks like we haven\'t been able to move our icons to your uploads folder. This isn\'t a big deal unless you want to use custom icons. If so copy the "%s" directory into your uploads folder. This way your icons wont be overwritten with a plugin update.', 'orionrush-tnygmaps'), '<strong>', '</strong>', TNYGMAPS_ICONS_DIR_NAME);?></p>
        </div>
	<?php endif; ?>
<?php
}

function static_maps (){?>
        <br/>
        <fieldset>
           <input type="checkbox" name="tnygmaps_mobile" value="1" <?php checked(1, get_option('tnygmaps_mobile'), true);?> /> <?php _e('Use Static Maps on mobile devices', 'orionrush-tnygmaps'); ?>
        </fieldset>
    <div class="bootstrap-tny">
    <br/>
    </div>
    <div class="bootstrap-tny">
        <p class=" alert"><?php echo sprintf(__('%sNote:%s  This option produces a %s"static map"%s or image rather then a fully interactive map for %smobile devices%s in order to decrease load times and bandwidth.', 'orionrush-tnygmaps'), '<strong>', '</strong>', '<a href="https://developers.google.com/maps/documentation/static-maps/" target="_blank">', '</a>', '<em><strong>', '</strong></em>');?></p>
    </div>
            <br/>
           <fieldset>
                <input title="<?php _e( 'Copy your API key here', 'orionrush-tnygmaps' ); ?>"
                       type="text"
                       name="tnygmaps_mobile_width"
                       id="tnygmaps_mobile_width"
                       placeholder="<?php echo  TNYGMAPS_STATIC_DOM_WIDTH; ?>"
                       value="<?php echo esc_attr( trim(get_option( 'tnygmaps_mobile_width' ))); ?>"
                /> <?php __('Max viewport width (in px) for small screens.', 'orionrush-tnygmaps'); ?>
           </fieldset>
    </div>
    <div class="bootstrap-tny">
        <br/>
        <p class=" alert"><?php echo sprintf(__('%sNote:%s The break-point or screen width below which a static map will be served. %s A with of %s0%s is the equivalent of disabling static maps.', 'orionrush-tnygmaps'), '<strong>', '</strong>', '<br />','<strong>', '</strong>');?></p>
    </div>
<?php
}

function debugging () {?>
    <br/>
    <fieldset>
        <input type="checkbox" name="tnygmaps_debug" value="1" <?php checked(1, get_option('tnygmaps_debug'), true);?> /> Enable Debugging
    </fieldset>
    <div class="bootstrap-tny">
        <br/>
    </div>
    <div class="bootstrap-tny">
        <p class=" alert"><?php echo sprintf(__('%sNote:%s  This option enables both JS Console debugging, and front end Google Maps query debugging notices. Front end notices will only appear to logged in admin users.', 'orionrush-tnygmaps'), '<strong>', '</strong>', '<a href="https://developers.google.com/maps/documentation/static-maps/" target="_blank">', '</a>');?></p>
    </div>
	<?php
}