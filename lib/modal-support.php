<?php
namespace OrionRush\TnyGmaps\ModalSupport;

use OrionRush\TnyGmaps\Support as Support;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}
/**
 * Outputs modal header scripts
 *
 * @author orionrush
 * @since 0.0.5
 *
 * @param $local_uri
 * @param $api_test
 *
 * @return string
 */
function addHeaderScripts( $api_test ) {
	// Path to the plugin root
	$local_uri = Support\make_root_relative( TNYGMAPS_URL . 'assets/' );
	ob_start(); ?>
    <link type="text/css" rel="stylesheet"
          href="<?php echo esc_url( $local_uri ); ?>../assets/css/tnygmaps_frontend.css"/>
    <link type="text/css" rel="stylesheet"
          href="<?php echo esc_url( $local_uri ); ?>../assets/css/tnygmaps_modal.css"/>
    <link type="text/css" rel="stylesheet"
          href="<?php echo esc_url( $local_uri ); ?>../assets/js/vendor/jquery-qtip-custom/jquery.qtip.css"/>
    <script>
        /* <![CDATA[ */
		<?php if ( $api_test ) { ?>
		var tnygmaps_api = true;
		<?php } else { ?>
		var tnygmaps_api = false;
		<?php }  ?>
        /* ]]> */
    </script>
	<?php
	return ob_get_clean();
}

add_filter( 'tnygmaps_add_header_scripts', __NAMESPACE__ . '\\addHeaderScripts', 30, 1 );

/**
 * Adds the Google api location lookup input group
 *
 * @author orionrush
 * @since 0.0.5
 *
 * @param $api_test
 *
 * @return string
 */
function addGoogleLookupGroup( $api_test ) {
	ob_start(); ?>
    <div class="group <?php echo ( ! $api_test ) ? 'hidden' : '' ?>	" id="mapAddress-group">
        <label class="heading" for="mapAddress"> <?php esc_html_e( 'Address lookup', 'orionrush-tnygmaps' ); ?>:
            <span data-tooltip="<?php echo sprintf( __( 'This section uses the Google Places API to find details about places of interest on found on Google Maps. The details are auto-updated when owners refresh information using Google‘s services.', 'orionrush-tnygmaps' ), '<br/>', '<br/>', '<br/>', '<br/>', '<br/>' ) ?>">?</span>
        </label>
        <div class="autoMapAddress input">
            <input type="text" name="mapAddressAuto" id="mapAddress" class="noEnterSubmit"/>
        </div>
    </div>
	<?php
	return ob_get_clean();
}

add_filter( 'tnygmaps_add_google_lookup_group', __NAMESPACE__ . '\\addGoogleLookupGroup', 30, 1 );

/**
 * Adds the custom address details input group
 *
 * @author orionrush
 * @since 0.0.5
 *
 * @param $api_test
 *
 * @return string
 */
function addCutsomDetailsGroup( $api_test ) {
	ob_start(); ?>
    <label class="heading"><?php esc_html_e( 'Custom details', 'orionrush-tnygmaps' ); ?>: <span
                data-tooltip="<?php echo sprintf( __( 'This section uses Google‘s Gocodng API to validate addresses, and so does not automatically update information about the location the future, (like phone numbers or website addresses). Use this tool if %sAddress lookup%s can‘t find or provides incorrect/incomplete results. %s%s If you just want to add a website or phone number, consider adding these to the %sWindow Extras%s section instead.', 'orionrush-tnygmaps' ), '<strong>', '</strong>', '<br/>', '<br/>', '<strong>', '</strong>' ) ?>">?</span>
    </label>
    <div class="group">
        <div id="address_extras" class="accordion" style="display:none;">
            <label for="custom_use_address_check">
				<?php if ( $api_test ) { ?>
					<?php esc_html_e( 'Use custom address details?', 'orionrush-tnygmaps' ); ?>
                    <input type="checkbox" name="custom_use_address_check" value="false">
				<?php } else { ?>
                    <input type="hidden" name="custom_use_address_check" value="checked">
				<?php } ?>
            </label>

            <div class="input ">
                <label for="locName"><?php esc_html_e( 'Place name', 'orionrush-tnygmaps' ); ?>:</label>
                <input type="text"
                       placeholder="<?php esc_html_e( 'Joes Music Emporium', 'orionrush-tnygmaps' ); ?>"
                       name="locName"
                       id="locName" class="noEnterSubmit" disabled/>
            </div>
            <div class="input float large">
                <label for="locStAdr"><?php esc_html_e( 'Street', 'orionrush-tnygmaps' ); ?>:</label>
                <input type="text" name="locStAdr"
                       placeholder="<?php esc_html_e( '32 Somewhere Drive', 'orionrush-tnygmaps' ); ?>"
                       id="locStAdr" class="noEnterSubmit" disabled/>
            </div>
            <div class="input float med">
                <label for="locCity"><?php esc_html_e( 'City', 'orionrush-tnygmaps' ); ?>:</label><br/>
                <input type="text" placeholder="<?php esc_html_e( 'Greenville', 'orionrush-tnygmaps' ); ?>"
                       name="locCity"
                       id="locCity" class="noEnterSubmit" disabled/>
            </div>
            <div class="input float thirds">
                <label for="locRegion"><?php esc_html_e( 'Region/State', 'orionrush-tnygmaps' ); ?>:</label>
                <input type="text" placeholder="<?php esc_html_e( 'Our State', 'orionrush-tnygmaps' ); ?>"
                       name="locRegion"
                       id="locRegion" class="noEnterSubmit" disabled/>
            </div>
            <div class="input float thirds">
                <label for="locPostcode"><?php esc_html_e( 'Postcode', 'orionrush-tnygmaps' ); ?>:</label>
                <input type="text" placeholder="<?php esc_html_e( 'POSTCODE', 'orionrush-tnygmaps' ); ?>"
                       name="locPostcode"
                       id="locPostcode" class="noEnterSubmit" disabled/>
            </div>
            <div class="input float thirds">
                <label for="locCountry"><?php esc_html_e( 'Country', 'orionrush-tnygmaps' ); ?>:</label>
                <input type="text" placeholder="<?php esc_html_e( 'Proud Country', 'orionrush-tnygmaps' ); ?>"
                       name="locCountry"
                       id="locCountry" class="noEnterSubmit" disabled/>
            </div>
            <div class="input float large">
                <label for="locWebsite"><?php esc_html_e( 'Website', 'orionrush-tnygmaps' ); ?>: <span
                            data-tooltip="<?php esc_html_e( 'If you provide a full url (including http://) an outside link will be created. If you provide a page-slug a local link will be rendered ie &quot;about-us&quot;.', 'orionrush-tnygmaps' ); ?>">?</span><br/>
                </label>
                <input type="text" name="locWebsite" placeholder="http://www.someplace.com" id="locWebsite"
                       class="noEnterSubmit" disabled/>
            </div>
            <div class="input float med">
                <label for="locPhone"><?php esc_html_e( 'Primary phone', 'orionrush-tnygmaps' ); ?>
                    :</label><br/>
                <input type="text" placeholder="000-000-0000" name="locPhone" id="locPhone"
                       class="noEnterSubmit"
                       disabled/>
            </div>
            <div class="input float thirds">
                <button name="lookup" type="submit" class="buttn bttn-check" id="lookup-detials"
                        onclick="return false;"><?php esc_html_e( 'Lookup address', 'orionrush-tnygmaps' ); ?></button>
            </div>
            <div class="input float thirds">
                <button name="update" type="submit" class="buttn" id="map-update"
                        onclick="return false;"><?php esc_html_e( 'Update info widow', 'orionrush-tnygmaps' ); ?></button>
            </div>
            <div class="input float thirds">
                <button name="clear-fields" type="submit" class="buttn bttn-warning" id="clear-fields"
                        onclick="return false;"><?php esc_html_e( 'Clear fields', 'orionrush-tnygmaps' ); ?></button>
            </div>
            <div class="input">
                <div onselectstart="return false;" class="hidden floating lookup" id="search-report"></div>
            </div>
            <div class="input hidden">
                <input type="hidden" name="mapLat" id="mapLat" disabled/>
                <input type="hidden" name="mapLng" id="mapLng" disabled/>
                <input type="hidden" name="locIconURL" id="locIconURL" disabled/>
                <input type="hidden" name="locGooglePlaceID" id="locGooglePlaceID" disabled/>
            </div>
        </div>
    </div>
	<?php
	return ob_get_clean();
}

add_filter( 'tnygmaps_add_custom_details_group', __NAMESPACE__ . '\\addCutsomDetailsGroup', 30, 1 );


/**
 * Adds the info window extras input group
 *
 * @author orionrush
 * @since 0.0.5
 *
 * @param $html
 *
 * @return string
 */
function addWindowExtrasGroup( $html ) {
	ob_start(); ?>
    <label class="heading"><?php esc_html_e( 'Window extras', 'orionrush-tnygmaps' ); ?>: <span
                data-tooltip="<?php esc_html_e( 'Add custom information to the marker info window. Allowed HTML is auto corrected to prevent malformed markup from breaking your page. Longer blocks of markup should be copy-pasted in.', 'orionrush-tnygmaps' ); ?>">?</span>
    </label>
    <div class="group">
        <div class="input">
            <div id="mapInfoWindowContainer" class="accordion" style="display:none;">
                        <textarea rows="5" cols="60" name="mapInfoWindow" id="mapInfoWindow" class="active"
                                  placeholder="<?php echo esc_html( sprintf( __( 'Some HTML allowed: , %s, %s, %s, %s, %s, %s, %s, %s. Attribute styles and classes are allowed i.e. ( %s style="background-color: blue;" class="foo bar">Foo%s) Use %s for line breaks. Single quotes are NOT allowed.', 'orionrush-tnygmaps' ), '<p>', '<a>', '<span>', '<ul>', '<ol>', '<li>', '<em>', '<strong>', '<p', '</p>', '<br/>' ) ); ?>"></textarea>
            </div>
        </div>
    </div>
	<?php
	return ob_get_clean();
}

add_filter( 'tnygmaps_add_window_extras_group', __NAMESPACE__ . '\\addWindowExtrasGroup', 30, 1 );

/**
 * Adds the map attributes group
 *
 * @author orionrush
 * @since 0.0.5
 *
 * @param $html
 *
 * @return string
 */
function addMapAttributesGroup( $html ) {
	ob_start();
	// Icon list
	$icons_array  = Support\loaded_tnygmaps_icons();
	$icons_list   = $icons_array[0];
	$loaded_icons = $icons_array[1];
	?>
    <label class="heading"><?php esc_html_e( 'Map attributes', 'orionrush-tnygmaps' ); ?>: <span
                data-tooltip="<?php esc_html_e( 'Adjust the appearence of your map.', 'orionrush-tnygmaps' ); ?>">?</span></label>
    <div class="group">

        <div id="map_extras" class="accordion" style="display:none;">
            <div class="input float half padRight">
                <label for="mapHeight"><?php esc_html_e( 'Height', 'orionrush-tnygmaps' ); ?>:</label>
                <input class="inline" type="text" name="mapHeight" value="500px" id="mapHeight"/>
            </div>
            <div class="input float half">
                <label for="mapWidth"><?php esc_html_e( 'Width', 'orionrush-tnygmaps' ); ?>:</label>
                <input class="inline" type="text" name="mapWidth" value="100%" id="mapWidth"/>
            </div>
            <div class="detial-notice">
                <small>
                    <em><b><?php esc_html_e( 'Specify size in &#039;px&#039; or &#039;%&#039;.', 'orionrush-tnygmaps' ); ?></b></em>
                    <br><em><?php esc_html_e( 'Height &amp; width settings are not reflected in map preview.', 'orionrush-tnygmaps' ); ?></em>
                </small>
            </div>
            <div class="input float">
                <label for="mapType"><?php esc_html_e( 'Map:', 'orionrush-tnygmaps' ); ?></label>
                <select name="mapType" id="mapType">
                    <option selected="selected"
                            value="ROADMAP"><?php esc_html_e( 'ROADMAP', 'orionrush-tnygmaps' ); ?></option>
                    <option value="SATELLITE"><?php esc_html_e( 'SATELLITE', 'orionrush-tnygmaps' ); ?></option>
                    <option value="HYBRID"><?php esc_html_e( 'HYBRID', 'orionrush-tnygmaps' ); ?></option>
                    <option value="TERRAIN"><?php esc_html_e( 'TERRAIN', 'orionrush-tnygmaps' ); ?></option>
                </select>
            </div>
            <div class="input float ">
                <label for="mapMarker"><?php esc_html_e( 'Marker', 'orionrush-tnygmaps' ); ?>:</label>
                <select name="mapMarker" id="mapMarker">

					<?php $custom_icon = get_option( 'tnygmaps_custom_icon' ); ?>
					<?php if ( $custom_icon && $custom_icon != "gMaps default pin" ) : ?>
                        <option value="default"><?php esc_html_e( 'Default', 'orionrush-tnygmaps' ); ?></option>
					<?php endif; ?>
                    <option value="google"><?php esc_html_e( 'Google', 'orionrush-tnygmaps' ); ?></option>
                    <option value="custom"><?php esc_html_e( 'Custom', 'orionrush-tnygmaps' ); ?></option>
                </select>
            </div>
            <div class="input float">
                <label><?php esc_html_e( 'Zoom', 'orionrush-tnygmaps' ); ?>:</label>
                <select id="mapZoom" name="mapZoom">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11" selected="selected">11</option>
                    <option value="13">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option value="21">21</option>
                    <option value="22">22</option>
                    <option value="23">23</option>
                </select>
            </div>
            <div class="input float">
                <label for="mapControls"><?php esc_html_e( 'Controls', 'orionrush-tnygmaps' ); ?>:</label>
                <select name="mapControls" id="mapControls">
                    <option selected="selected"
                            value="false"><?php esc_html_e( 'on', 'orionrush-tnygmaps' ); ?></option>
                    <option value="true"><?php esc_html_e( 'off', 'orionrush-tnygmaps' ); ?></option>
                </select>
            </div>
            <div class="mapMarkerImage_wrap">
                <div class="input">
                    <label class="heading"
                           for="mapMarkerImage"><?php esc_html_e( 'Custom icon URL', 'orionrush-tnygmaps' ); ?>:
                        <span data-tooltip="<?php esc_html_e( 'List the full (or root realitive) path to your custom icon, or select from the list below. If your custom icon won&#039;t load, then there is probably a typo in your path.', 'orionrush-tnygmaps' ); ?>">?</span>
                    </label>
                    <input type="text"
                           name="mapMarkerImage"
                           placeholder="<?php echo esc_attr( __NAMESPACE__ . Support\loaded_tnygmaps_icons_url() ); ?>/"
                           value="<?php echo esc_url( __NAMESPACE__ . Support\loaded_tnygmaps_icons_url() ); ?><?php echo esc_attr( $loaded_icons[0] ); ?>"
                           id="mapMarkerImage"
                    />
                    <span class="icon-list alert">
                        <em><?php esc_html_e( 'Loaded icons', 'orionrush-tnygmaps' ); ?>:</em>
                        <br/><?php esc_html( print( $icons_list ) ); ?>
                        <p>
                            <em>
                                <?php echo sprintf(
	                                __( 'Icons courtesy of the %sMap Icons Collection%s', 'orionrush-tnygmaps' ),
	                                '<a href="http://mapicons.nicolasmollet.com/" target="_blank">',
	                                '</a>' );
                                ?>
                            </em>
                        </p>
                    </span>
                </div>
            </div>

        </div>
    </div>
	<?php
	return ob_get_clean();
}

add_filter( 'tnygmaps_add_map_attr_group', __NAMESPACE__ . '\\addMapAttributesGroup', 30, 1 );

/**
 * Adds the map preview and shortcode button group
 *
 * @author orionrush
 * @since 0.0.5
 *
 * @param $html
 *
 * @return string
 */
function addMapPreview( $html ) {
	ob_start(); ?>
    <div class="map-preview">
        <div class="map-wrap tnygmps_wrap">
            <div id="map_canvas" class="tnygmps_canvas"></div>
            <div class="tnygmps_link_wrap"><a href="#" class="tnygmps_ext_lnk" onclick="return false;"
                                              target=""><?php echo esc_html( Support\openMapInNewWin() ); ?></a></div>
        </div>
        <input name="save" type="submit" class="submit-button" id="insert" value="Insert Map Shortcode"
               onclick="javascript:tnyGmapsAssembleShortcode.insert(tnyGmapsAssembleShortcode.local_ed)"/>
    </div>
	<?php
	return ob_get_clean();
}

add_filter( 'tnygmaps_add_map_preview', __NAMESPACE__ . '\\addMapPreview', 30, 1 );

/**
 * Adds footer scripts
 *
 * @author orionrush
 * @since 0.0.5
 *
 * @param $local_uri
 *
 * @return string
 */
function addFooterScripts( $api_test ) {
	// Path to the plugin root
	$local_uri = Support\make_root_relative( TNYGMAPS_URL . 'assets/' );
	ob_start(); ?>
    <script src="<?php echo esc_url( $local_uri ); ?>js/vendor/spin-js/spin.min.js"></script>
    <script type="text/javascript">
		var opts, target, spinner;
		opts = {
			lines: 11,
			length: 40,
			width: 10,
			radius: 28,
			scale: 0.75,
			corners: 1,
			color: '#000',
			opacity: 0.25,
			rotate: 0,
			direction: 1,
			speed: 0.7,
			trail: 68,
			fps: 20,
			zIndex: 2e9,
			className: 'spinner',
			top: '50%',
			left: '50%',
			shadow: false,
			hwaccel: false,
			position: 'absolute'
		};
		target = document.getElementById( 'overlay' );
		spinner = new Spinner( opts ).spin( target );
    </script>
    <script src="<?php echo esc_url( includes_url() ); ?>js/jquery/jquery.js"></script> <!--should be cached -->
    <script src="<?php echo esc_url( includes_url() ); ?>js/tinymce/tiny_mce_popup.js"></script>
    <script src="//maps.google.com/maps/api/js?key=<?php echo esc_attr( GOOGLE_API_KEY ); ?>&libraries=places"></script>
    <script src="<?php echo esc_url( $local_uri ); ?>js/tnygmaps_modal_logic.min.js"></script>
    <script src="<?php echo esc_url( $local_uri ); ?>js/vendor/jquery-base64/jquery.base64.min.js"></script>
    <script src="<?php echo esc_url( $local_uri ); ?>js/vendor/jquery-htmlclean/jquery.htmlClean.min.js"></script>
    <script src="<?php echo esc_url( $local_uri ); ?>js/vendor/jquery-qtip-custom/jquery.qtip.min.js"></script>
	<?php
	return ob_get_clean();
}

add_filter( 'tnygmaps_add_footer_scripts', __NAMESPACE__ . '\\addFooterScripts', 30, 1 );
