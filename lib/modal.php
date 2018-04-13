<?php
namespace OrionRush\TnyGmaps\Modal;
use OrionRush\TnyGmaps\Support as Support;
if ( ! defined( 'ABSPATH' ) ) { die(); }

/************************************************************************
 * Markup for modal window for tny gMaps tinyMCE editor
 * This modal is a shortcode builder with preview functionality for the TnyGmaps plugin.
 * @link http://www.orionrush.com
 * @since 0.0.1
 *
 * @package WordPress
 * @subpackage TnygMaps
 *
 * @copyright (c) 2012-`date "+%Y"` orionrush. All rights reserved.
 * @license GPL
 * **********************************************************************
 */

// Path to the plugin root
$local_uri = Support\make_root_relative( TNYGMAPS_URL . 'assets/' );
// do we have the google places api key?
$api_test = Support\test_google_key();
// Icon list
$icons_array  = Support\loaded_tnygmaps_icons();
$icons_list   = $icons_array[0];
$loaded_icons = $icons_array[1];


function loaded_icons_url() {
	if (file_exists( TNYGMAPS_ICONS_DIR )) {
	    $icon_dir_url = TNYGMAPS_ICONS_DIR_URL;
    } else {
		$icon_dir_url = TNYGMAPS_URL . 'assets/' . TNYGMAPS_ICONS_DIR_NAME . '/';
    }
    echo $icon_dir_url;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo TNYGMAPS_NAME ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link type="text/css" rel="stylesheet" href="<?php echo $local_uri ?>../assets/css/tnygmaps_frontend.css"/>
        <link type="text/css" rel="stylesheet" href="<?php echo $local_uri ?>../assets/css/tnygmaps_modal.css"/>
        <link type="text/css" rel="stylesheet" href="<?php echo $local_uri ?>../assets/js/vendor/jquery-qtip-custom/jquery.qtip.css"/>
        <script>
            /* <![CDATA[ */
            <?php if ( $api_test ) { ?>
            var tnygmaps_api = true;
            <?php } else { ?>
            var tnygmaps_api = false;
            <?php }  ?>
            /* ]]> */
        </script>
    </head>
    <body>
    <div id="overlay" class=""></div>
        <?php if ( ! $api_test ) { ?>
            <div class="alert warning floating"> <?php echo sprintf( __( 'GOOGLE API KEY NOT SET: Visit the %splugin settings page%s to get one.', 'orionrush-tnygmaps' ), '<a href="' . admin_url() . 'options-general.php?page=tnygmaps" target="_parent">', '</a>' ); ?> </div>
        <?php } ?>
        <div id="button-dialog" class="wrap">
            <form action="/" method="get" accept-charset="utf-8">

                <div class="group <?php echo ( ! $api_test ) ? 'hidden' : '' ?>	" id="mapAddress-group">
                    <label class="heading" for="mapAddress"> <?php _e( 'Address lookup', 'orionrush-tnygmaps' ) ?>: <span data-tooltip="<?php echo sprintf( __( 'This section uses the Google Places API to find details about places of interest on found on Google Maps. The details are auto-updated when owners refresh information using Google‘s services.', 'orionrush-tnygmaps' ), '<br/>', '<br/>', '<br/>', '<br/>', '<br/>' ) ?>">?</span>
                    </label>
                    <div class="autoMapAddress input">
                        <input type="text" name="mapAddressAuto" id="mapAddress" class="noEnterSubmit"/>
                    </div>
                </div>
                <label class="heading"><?php _e( 'Custom details', 'orionrush-tnygmaps' ); ?>: <span data-tooltip="<?php echo sprintf( __( 'This section uses Google‘s Gocodng API to validate addresses, and so does not automatically update information about the location the future, (like phone numbers or website addresses). Use this tool if %sAddress lookup%s can‘t find or provides incorrect/incomplete results. %s%s If you just want to add a website or phone number, consider adding these to the %sWindow Extras%s section instead.', 'orionrush-tnygmaps' ), '<strong>', '</strong>', '<br/>', '<br/>', '<strong>', '</strong>' ) ?>">?</span>
                </label>

                <div class="group">
                    <div id="address_extras" class="accordion" style="display:none;">
                        <label for="custom_use_address_check">
                            <?php if ( $api_test ) { ?>
                                <?php _e( 'Use custom address details?', 'orionrush-tnygmaps' ); ?>
                                <input type="checkbox" name="custom_use_address_check" value="false">
                            <?php } else { ?>
                                <input type="hidden" name="custom_use_address_check" value="checked">
                            <?php } ?>
                        </label>

                        <div class="input ">
                            <label for="locName"><?php _e( 'Place name', 'orionrush-tnygmaps' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'Joes Music Emporium', 'orionrush-tnygmaps' ); ?>"
                                   name="locName"
                                   id="locName" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float large">
                            <label for="locStAdr"><?php _e( 'Street', 'orionrush-tnygmaps' ); ?>:</label>
                            <input type="text" name="locStAdr"
                                   placeholder="<?php _e( '32 Somewhere Drive', 'orionrush-tnygmaps' ); ?>"
                                   id="locStAdr" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float med">
                            <label for="locCity"><?php _e( 'City', 'orionrush-tnygmaps' ); ?>:</label><br/>
                            <input type="text" placeholder="<?php _e( 'Greenville', 'orionrush-tnygmaps' ); ?>"
                                   name="locCity"
                                   id="locCity" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float thirds">
                            <label for="locRegion"><?php _e( 'Region/State', 'orionrush-tnygmaps' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'Our State', 'orionrush-tnygmaps' ); ?>"
                                   name="locRegion"
                                   id="locRegion" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float thirds">
                            <label for="locPostcode"><?php _e( 'Postcode', 'orionrush-tnygmaps' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'POSTCODE', 'orionrush-tnygmaps' ); ?>"
                                   name="locPostcode"
                                   id="locPostcode" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float thirds">
                            <label for="locCountry"><?php _e( 'Country', 'orionrush-tnygmaps' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'Proud Country', 'orionrush-tnygmaps' ); ?>"
                                   name="locCountry"
                                   id="locCountry" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float large">
                            <label for="locWebsite"><?php _e( 'Website', 'orionrush-tnygmaps' ); ?>: <span data-tooltip="<?php _e( 'If you provide a full url (including http://) an outside link will be created. If you provide a page-slug a local link will be rendered ie &quot;about-us&quot;.', 'orionrush-tnygmaps' ); ?>">?</span><br/>
                            </label>
                            <input type="text" name="locWebsite" placeholder="http://www.someplace.com" id="locWebsite"
                                   class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float med">
                            <label for="locPhone"><?php _e( 'Primary phone', 'orionrush-tnygmaps' ); ?>:</label><br/>
                            <input type="text" placeholder="000-000-0000" name="locPhone" id="locPhone"
                                   class="noEnterSubmit"
                                   disabled/>
                        </div>
                        <div class="input float thirds">
                            <button name="lookup" type="submit" class="buttn bttn-check" id="lookup-detials"
                                    onclick="return false;"><?php _e( 'Lookup address', 'orionrush-tnygmaps' ); ?></button>
                        </div>
                        <div class="input float thirds">
                            <button name="update" type="submit" class="buttn" id="map-update"
                                    onclick="return false;"><?php _e( 'Update info widow', 'orionrush-tnygmaps' ); ?></button>
                        </div>
                        <div class="input float thirds">
                            <button name="clear-fields" type="submit" class="buttn bttn-warning" id="clear-fields"
                                    onclick="return false;"><?php _e( 'Clear fields', 'orionrush-tnygmaps' ); ?></button>
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
                <label class="heading"><?php _e( 'Window extras', 'orionrush-tnygmaps' ); ?>: <span data-tooltip="<?php _e( 'Add custom information to the marker info window. Allowed HTML is auto corrected to prevent malformed markup from breaking your page. Longer blocks of markup should be copy-pasted in.', 'orionrush-tnygmaps' ); ?>">?</span>
                </label>
                <div class="group">
                    <div class="input">
                        <div id="mapInfoWindowContainer" class="accordion" style="display:none;">
                        <textarea rows="5" cols="60" name="mapInfoWindow" id="mapInfoWindow" class="active"
                                  placeholder="<?php esc_attr_e( 'Some HTML allowed:<p>, <a>, <span>, <ul>, <ol>, <li>, <em>, <strong>. Attribute styles and classes are allowed i.e. ( <p style="background-color: blue;" class="foo bar">Foo</p>) Use <br/> for line breaks. Single quotes are NOT allowed.', 'orionrush-tnygmaps' ); ?>"></textarea>
                        </div>
                    </div>
                </div>
                <label class="heading"><?php _e( 'Map attributes', 'orionrush-tnygmaps' ); ?>: <span data-tooltip="<?php _e( 'Adjust the appearence of your map.', 'orionrush-tnygmaps' ); ?>">?</span></label>
                <div class="group">

                    <div id="map_extras" class="accordion" style="display:none;">
                        <div class="input float half padRight">
                            <label for="mapHeight"><?php _e( 'Height', 'orionrush-tnygmaps' ); ?>:</label>
                            <input class="inline" type="text" name="mapHeight" value="500px" id="mapHeight"/>
                        </div>
                        <div class="input float half">
                            <label for="mapWidth"><?php _e( 'Width', 'orionrush-tnygmaps' ); ?>:</label>
                            <input class="inline" type="text" name="mapWidth" value="100%" id="mapWidth"/>
                        </div>
                        <div class="detial-notice">
                            <small>
                                <em><b><?php _e( 'Specify size in &#039;px&#039; or &#039;%&#039;.', 'orionrush-tnygmaps' ); ?></b></em>
                                <br><em><?php _e( 'Height &amp; width settings are not reflected in map preview.', 'orionrush-tnygmaps' ); ?></em>
                            </small>
                        </div>
                        <div class="input float">
                            <label for="mapType"><?php _e( 'Map:', 'orionrush-tnygmaps' ); ?></label>
                            <select name="mapType" id="mapType">
                                <option selected="selected"
                                        value="ROADMAP"><?php _e( 'ROADMAP', 'orionrush-tnygmaps' ); ?></option>
                                <option value="SATELLITE"><?php _e( 'SATELLITE', 'orionrush-tnygmaps' ); ?></option>
                                <option value="HYBRID"><?php _e( 'HYBRID', 'orionrush-tnygmaps' ); ?></option>
                                <option value="TERRAIN"><?php _e( 'TERRAIN', 'orionrush-tnygmaps' ); ?></option>
                            </select>
                        </div>
                        <div class="input float ">
                            <label for="mapMarker"><?php _e( 'Marker', 'orionrush-tnygmaps' ); ?>:</label>
                            <select name="mapMarker" id="mapMarker">

                            <?php $custom_icon = get_option('tnygmaps_custom_icon'); ?>
                            <?php if ($custom_icon && $custom_icon != "gMaps default pin") : ?>
                                <option value="default"><?php _e( 'Default', 'orionrush-tnygmaps' ); ?></option>
                            <?php endif; ?>
                                <option value="google"><?php _e( 'Google', 'orionrush-tnygmaps' ); ?></option>
                                <option value="custom"><?php _e( 'Custom', 'orionrush-tnygmaps' ); ?></option>
                            </select>
                        </div>
                        <div class="input float">
                            <label><?php _e( 'Zoom', 'orionrush-tnygmaps' ); ?>:</label>
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
                                <option selected="selected" value="11">11</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="18">19</option>
                                <option value="18">20</option>
                                <option value="18">21</option>
                                <option value="18">22</option>
                            </select>
                        </div>
                        <div class="input float">
                            <label for="mapControls"><?php _e( 'Controls', 'orionrush-tnygmaps' ); ?>:</label>
                            <select name="mapControls" id="mapControls">
                                <option selected="selected"
                                        value="false"><?php _e( 'on', 'orionrush-tnygmaps' ); ?></option>
                                <option value="true"><?php _e( 'off', 'orionrush-tnygmaps' ); ?></option>
                            </select>
                        </div>
                        <div class="mapMarkerImage_wrap">
                            <div class="input">
                                <label class="heading" for="mapMarkerImage"><?php _e( 'Custom icon URL', 'orionrush-tnygmaps' ); ?>: <span data-tooltip="<?php _e( 'List the full (or root realitive) path to your custom icon, or select from the list below. If your custom icon won&#039;t load, then there is probably a typo in your path.', 'orionrush-tnygmaps' ); ?>">?</span>
                                </label>
                                <input type="text"
                                       name="mapMarkerImage"
                                       placeholder="<?php echo loaded_icons_url(); ?>/"
                                       value="<?php loaded_icons_url(); ?><?php echo( $loaded_icons[0] ); ?>"
                                       id="mapMarkerImage"
                                />
                                <span class="icon-list alert">
                                <em><?php _e( 'Loaded icons', 'orionrush-tnygmaps' ); ?>:</em>
                                <br/><?php print( $icons_list ) ?>
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
            </form>
        </div>
        <div class="map-preview">
            <div class="map-wrap tnygmps_wrap">
                <div id="map_canvas" class="tnygmps_canvas"></div>
                <div class="tnygmps_link_wrap"><a href="#" class="tnygmps_ext_lnk" onclick="return false;" target=""><?php echo Support\openMapInNewWin(); ?></a></div>
            </div>
            <input name="save" type="submit" class="submit-button" id="insert" value="Insert Map Shortcode"
                   onclick="javascript:tnyGmapsAssembleShortcode.insert(tnyGmapsAssembleShortcode.local_ed)"/>
        </div>
    </body>
    <script src="<?php echo $local_uri ?>js/vendor/spin-js/spin.min.js"></script>
    <script type="text/javascript">
        var opts = {
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
        var target = document.getElementById('overlay');
        var spinner = new Spinner(opts).spin(target);
    </script>
    <script src="<?php echo includes_url() ?>js/jquery/jquery.js"></script> <!--should be cached -->
    <script src="<?php echo includes_url() ?>js/tinymce/tiny_mce_popup.js"></script>
    <script src="//maps.google.com/maps/api/js?key=<?php echo GOOGLE_API_KEY ?>&libraries=places"></script>
    <script src="<?php echo $local_uri; ?>js/tnygmaps_modal_logic.min.js"></script>
    <script src="<?php echo $local_uri; ?>js/vendor/jquery-base64/jquery.base64.min.js"></script>
    <script src="<?php echo $local_uri; ?>js/vendor/jquery-htmlclean/jquery.htmlClean.min.js"></script>
    <script src="<?php echo $local_uri; ?>js/vendor/jquery-qtip-custom/jquery.qtip.min.js"></script>
</html>