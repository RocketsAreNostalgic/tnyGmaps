<?php
namespace OrionRush\TnyGmaps\Modal;
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
/************************************************************************
 * Markup for modal window for _tr_gmaps tinyMCE editor
 * This modal is a shortcode builder with preview functionality for the TnyGmaps plugin.
 * @link http://www.orionrush.com
 * @since 0.0.1
 *
 * @package WordPress
 * @subpackage TinyGmaps
 *
 * @copyright (c) 2012-`date "+%Y"` orionrush. All rights reserved.
 * @license GPL
 * **********************************************************************
 */

// Path to the plugin root
$local_uri = \OrionRush\TnyGmaps\Support\make_root_relative( TNYGMAPS_URL . 'inc/' );
// do we have the google places api key?
$api_test = \OrionRush\TnyGmaps\Support\test_google_key();
// Icon list
$icons_array  = \OrionRush\TnyGmaps\Support\loaded_tnygmaps_icons();
$icons_list   = $icons_array[0];
$loaded_icons = $icons_array[1];

?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo TNYGMAPS_NAME ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link type="text/css" rel="stylesheet" href="<?php echo $local_uri ?>css/tnygmaps_modal.css"/>
        <link type="text/css" rel="stylesheet" href="<?php echo $local_uri ?>js/vendor/jquery-qtip-custom/jquery.qtip.css"/>
        <script>
            /* <![CDATA[ */
            <?php if ( $api_test ) { ?>
            var tnygmps_api = true;
            <?php } else { ?>
            var tnygmps_api = false;
            <?php }  ?>
            /* ]]> */
        </script>
    </head>
    <body>
        <?php if ( ! $api_test ) { ?>
            <div class="alert warning floating"> <?php echo sprintf( __( 'GOOGLE API KEY NOT SET: Visit the %splugin settings page%s to get one.', 'orionrush_tnygmaps' ), '<a href="' . admin_url() . 'options-general.php?page=tnygmaps" target="_parent">', '</a>' ); ?> </div>
        <?php } ?>
        <div id="button-dialog" class="wrap">
            <form action="/" method="get" accept-charset="utf-8">

                <div class="group <?php echo ( ! $api_test ) ? 'hidden' : '' ?>	" id="mapAddress-group">
                    <label class="heading" for="mapAddress"> <?php _e( 'Address lookup:', 'orionrush_tnygmaps' ) ?>
                        <span data-tooltip="<?php echo sprintf( __( 'This section uses the Google Places API to find details about places of interest on found on Google Maps. The details are auto-updated when owners refresh information using Google‘s services.', 'orionrush_tnygmaps' ), '<br/>', '<br/>', '<br/>', '<br/>', '<br/>' ) ?>">?</span></label>
                    </label>
                    <div class="autoMapAddress input">
                        <input type="text" name="mapAddressAuto" id="mapAddress" class="noEnterSubmit"/>
                    </div>
                </div>
                <label class="heading"><?php _e( 'Custom details:', 'orionrush_tnygmaps' ); ?>
                    <span data-tooltip="<?php echo sprintf( __( 'This section uses Google‘s Gocodng API to validate addresses, and so does not automatically update information about the location the future, (like phone numbers or website addresses). Use this tool if %sAddress lookup%s can‘t find or provides incorrect/incomplete results. %s%s If you just want to add a website or phone number, consider adding these to the %sWindow Extras%s section instead.', 'orionrush_tnygmaps' ), '<strong>', '</strong>', '<br/>', '<br/>', '<strong>', '</strong>' ) ?>">?</span>
                </label>
                <div class="group">
                    <div id="address_extras" class="accordion" style="display:none;">
                        <label for="custom_use_address_check">
                            <?php if ( $api_test ) { ?>
                                <?php _e( 'Use custom address details?', 'orionrush_tnygmaps' ); ?>
                                <input type="checkbox" name="custom_use_address_check" value="false">
                            <?php } else { ?>
                                <input type="hidden" name="custom_use_address_check" value="checked">
                            <?php } ?>
                        </label>

                        <div class="input ">
                            <label for="locName"><?php _e( 'Place name:', 'orionrush_tnygmaps' ); ?></label>
                            <input type="text" placeholder="<?php _e( 'Joes Music Emporium', 'orionrush_tnygmaps' ); ?>"
                                   name="locName"
                                   id="locName" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float large">
                            <label for="locStAdr"><?php _e( 'Street', 'orionrush_tnygmaps' ); ?>:</label>
                            <input type="text" name="locStAdr"
                                   placeholder="<?php _e( '32 Somewhere Drive', 'orionrush_tnygmaps' ); ?>"
                                   id="locStAdr" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float med">
                            <label for="locCity"><?php _e( 'City', 'orionrush_tnygmaps' ); ?>:</label><br/>
                            <input type="text" placeholder="<?php _e( 'Greenville', 'orionrush_tnygmaps' ); ?>"
                                   name="locCity"
                                   id="locCity" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float thirds">
                            <label for="locRegion"><?php _e( 'Region/State', 'orionrush_tnygmaps' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'Our State', 'orionrush_tnygmaps' ); ?>"
                                   name="locRegion"
                                   id="locRegion" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float thirds">
                            <label for="locPostcode"><?php _e( 'Postcode', 'orionrush_tnygmaps' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'POSTCODE', 'orionrush_tnygmaps' ); ?>"
                                   name="locPostcode"
                                   id="locPostcode" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float thirds">
                            <label for="locCountry"><?php _e( 'Country', 'orionrush_tnygmaps' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'Proud Country', 'orionrush_tnygmaps' ); ?>"
                                   name="locCountry"
                                   id="locCountry" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float large">
                            <label for="locWebsite"><?php _e( 'Website', 'orionrush_tnygmaps' ); ?>:</label>
                            <span data-tooltip="<?php _e( 'If you provide a full url (including http://) an outside link will be created. If you provide a page-slug a local link will be rendered ie &quot;about-us&quot;.', 'orionrush_tnygmaps' ); ?>">?</span><br/>
                            <input type="text" name="locWebsite" placeholder="http://www.someplace.com" id="locWebsite"
                                   class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float med">
                            <label for="locPhone"><?php _e( 'Primary phone', 'orionrush_tnygmaps' ); ?>:</label><br/>
                            <input type="text" placeholder="000-000-0000" name="locPhone" id="locPhone"
                                   class="noEnterSubmit"
                                   disabled/>
                        </div>
                        <div class="input float thirds">
                            <button name="lookup" type="submit" class="buttn bttn-submit" id="lookup-detials"
                                    onclick="return false;"><?php _e( 'Lookup address', 'orionrush_tnygmaps' ); ?></button>
                        </div>
                        <div class="input float thirds">
                            <button name="update" type="submit" class="buttn" id="map-update"
                                    onclick="return false;"><?php _e( 'Update info widow', 'orionrush_tnygmaps' ); ?></button>
                        </div>
                        <div class="input float thirds">
                            <button name="clear-fields" type="submit" class="buttn bttn-warning" id="clear-fields"
                                    onclick="return false;"><?php _e( 'Clear fields', 'orionrush_tnygmaps' ); ?></button>
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
                <label class="heading"><?php _e( 'Window extras', 'orionrush_tnygmaps' ); ?>:<span
                            data-tooltip="<?php _e( 'Add custom information to the marker info window. Allowed HTML is auto corrected to prevent malformed markup from breaking your page. Longer blocks of markup should be copy-pasted in.', 'orionrush_tnygmaps' ); ?>">?</span></label>
                <div class="group">
                    <div class="input">
                        <div id="mapInfoWindowContainer" class="accordion" style="display:none;">
                        <textarea rows="5" cols="60" name="mapInfoWindow" id="mapInfoWindow" class="active"
                                  placeholder="<?php esc_attr_e( 'Some HTML allowed:<p>, <a>, <span>, <ul>, <ol>, <li>, <em>, <strong>. Attribute styles and classes are allowed i.e. ( <p style="background-color: blue;" class="foo bar">Foo</p>) Use <br/> for line breaks. Single quotes are NOT allowed.', 'orionrush_tnygmaps' ); ?>"></textarea>
                        </div>
                    </div>
                </div>
                <label class="heading"><?php _e( 'Map attributes', 'orionrush_tnygmaps' ); ?>:<span
                            data-tooltip="<?php _e( 'Adjust the appearence of your map.', 'orionrush_tnygmaps' ); ?>">?</span></label>
                <div class="group">

                    <div id="map_extras" class="accordion" style="display:none;">
                        <div class="input float half padRight">
                            <label for="mapHeight"><?php _e( 'Height', 'orionrush_tnygmaps' ); ?>:</label>
                            <input class="inline" type="text" name="mapHeight" value="500px" id="mapHeight"/>

                        </div>
                        <div class="input float half">
                            <label for="mapWidth"><?php _e( 'Width', 'orionrush_tnygmaps' ); ?>:</label>
                            <input class="inline" type="text" name="mapWidth" value="100%" id="mapWidth"/>
                        </div>
                        <div class="detial-notice">
                            <small>
                                <em><b><?php _e( 'Specify size in &#039;px&#039; or &#039;%&#039;.', 'orionrush_tnygmaps' ); ?></b></em>
                                <br><em><?php _e( 'Height &amp; width settings are not reflected in map preview.', 'orionrush_tnygmaps' ); ?></em>
                            </small>
                        </div>
                        <div class="input float">
                            <label for="mapType"><?php _e( 'Map:', 'orionrush_tnygmaps' ); ?></label>
                            <select name="mapType" id="mapType">
                                <option selected="selected"
                                        value="ROADMAP"><?php _e( 'ROADMAP', 'orionrush_tnygmaps' ); ?></option>
                                <option value="SATELLITE"><?php _e( 'SATELLITE', 'orionrush_tnygmaps' ); ?></option>
                                <option value="HYBRID"><?php _e( 'HYBRID', 'orionrush_tnygmaps' ); ?></option>
                                <option value="TERRAIN"><?php _e( 'TERRAIN', 'orionrush_tnygmaps' ); ?></option>
                            </select>
                        </div>
                        <div class="input float ">
                            <label for="mapMarker"><?php _e( 'Marker', 'orionrush_tnygmaps' ); ?>:</label>
                            <select name="mapMarker" id="mapMarker">
                                <option selected="selected"
                                        value="concert"><?php _e( 'Concert', 'orionrush_tnygmaps' ); ?></option>
                                <option value="default"><?php _e( 'Google', 'orionrush_tnygmaps' ); ?></option>
                                <option value="custom"><?php _e( 'Custom', 'orionrush_tnygmaps' ); ?></option>
                            </select>
                        </div>
                        <div class="input float">
                            <label><?php _e( 'Zoom', 'orionrush_tnygmaps' ); ?>:</label>
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
                            <label for="mapControls"><?php _e( 'Controls', 'orionrush_tnygmaps' ); ?>:</label>
                            <select name="mapControls" id="mapControls">
                                <option selected="selected"
                                        value="false"><?php _e( 'on', 'orionrush_tnygmaps' ); ?></option>
                                <option value="true"><?php _e( 'off', 'orionrush_tnygmaps' ); ?></option>
                            </select>
                        </div>
                        <div class="mapMarkerImage_wrap">
                            <div class="input">
                                <label class="heading"
                                       for="mapMarkerImage"><?php _e( 'Custom icon URL', 'orionrush_tnygmaps' ); ?>:
                                    <span
                                            data-tooltip="<?php _e( 'List the full (or root realitive) path to your custom icon, or select from the list below. If your custom icon won&#039;t load, then there is probably a typo in your path.', 'orionrush_tnygmaps' ); ?>">?</span></label>
                                <input type="text" name="mapMarkerImage" placeholder="<?php echo $local_uri; ?>/icons/"
                                       value="<?php echo $local_uri; ?>/icons/<?php echo( $loaded_icons[0] ); ?>"
                                       id="mapMarkerImage"/>
                                <span class="icon-list alert">
                                <em><?php _e( 'Loaded icons', 'orionrush_tnygmaps' ); ?>:</em>
                                <br/><?php print( $icons_list ) ?>
                                    <p>
                                    <em>
                                        <?php echo sprintf(
                                            __( 'Icons courtesy of the %sMap Icons Collection%s', 'orionrush_tnygmaps' ),
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
            <div class="map-wrap">
                <div id="map_canvas"></div>
            </div>
            <input name="save" type="submit" class="submit-button" id="insert" value="Insert Map Shortcode"
                   onclick="javascript:tnyGmaps.insert(tnyGmaps.local_ed)"/>
        </div>
    </body>
    <script src="<?php echo includes_url() ?>js/jquery/jquery.js"></script> <!--should be cached -->
    <script src="<?php echo includes_url() ?>js/tinymce/tiny_mce_popup.js"></script>
    <script src="//maps.google.com/maps/api/js?key=<?php echo GOOGLE_API_KEY ?>&libraries=places"></script>
    <script src="<?php echo $local_uri; ?>js/tnygmaps_modal_loadmaps.min.js"></script>
    <script src="<?php echo $local_uri; ?>js/vendor/jquery-base64/jquery.base64.min.js"></script>
    <script src="<?php echo $local_uri; ?>js/vendor/jquery-htmlclean/jquery.htmlClean.min.js"></script>
    <script src="<?php echo $local_uri; ?>js/vendor/jquery.qtip.custom/jquery.qtip.min.js"></script>
</html>