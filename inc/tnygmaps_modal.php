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
$local_uri = \OrionRush\TnyGmaps\LoadModal\make_root_relative( TNYGMAPS_URL . 'inc/' );

// do we have the google places api key?
$api_test = \OrionRush\TnyGmaps\LoadModal\test_google_key();

// Icon list
$icons_array  = \OrionRush\TnyGmaps\LoadModal\loaded_tnygmaps_icons();
$icons_list   = $icons_array[0];
$loaded_icons = $icons_array[1];

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Google Map</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link type="text/css" rel="stylesheet" href="<?php echo $local_uri ?>css/tnygmaps_modal.css?; ?>"/>
        <link type="text/css" rel="stylesheet" href="<?php echo $local_uri ?>js/vendor/jquery.qtip.custom/jquery.qtip.css"/>
    </head>
    <body>
        <?php if ( ! $api_test ) { ?>
            <div class="alert warning fadeout floating"> <?php _e( 'API KEY NOT DEFINED, see documentation for full functionality.', 'tny_gmap' ); ?> </div>
        <?php } ?>
        <div id="button-dialog" class="wrap">
            <form action="/" method="get" accept-charset="utf-8">

                <div class="group <?php echo ( ! $api_test ) ? 'hidden' : '' ?>	" id="mapAddress-group">
                    <label class="heading" for="mapAddress"> <?php _e( 'Address lookup', 'tny_gmap' ) ?>:</label>
                    <div class="autoMapAddress input">
                        <input type="text" name="mapAddressAuto" id="mapAddress" class="noEnterSubmit"/>
                    </div>
                </div>

                <?php if ( $api_test ) { ?>
                    <label class="heading"><?php _e( 'Location details', 'tny_gmap' ); ?>:<span
                                data-tooltip="<?php _e( 'Only use this section if &quot;Address lookup&quot; can&#039;t find, gives incorrect or incomplete results. </br>&quot;Address lookup&quot; gives us a reference we can use to automatically update location details from Google&#039;s servers.</br></br>Results from this section may differ from that of &quot;Address lookup&quot; and may require you provide greater detail to achieve correct results.</br> </br>If you just want to add a website or phone number, consider adding these to the &quot;Infowindow extras&quot; section instead.', 'tny_gmap' ) ?>">?</span></label>
                <?php } else { ?>
                    <label class="heading"><?php _e( 'Location details', 'tny_gmap' ); ?>:<span
                                data-tooltip="<?php _e( ' This section doesn&#039;t use Google&#039;s servers to validate addresses, or automatically update this information if it changes in future. For this you need an API Key from them. However, we can use the information you provide in the fields below to create a fixed address lookup.</br></br>For best results provide as much Street, City, Postcode information as you can.</br> </br>If you want to add other details, consider adding these to the &quot;Infowindow extras&quot; section instead.', 'tny_gmap' ); ?>">?</span></label>
                <?php } ?>
                <div class="group">
                    <div id="address_extras" class="accordion" style="display:none;">
                        <label for="custom_use_address_check">
                            <?php if ( $api_test ) { ?>
                                <?php _e( 'Use custom address details?', 'tny_gmap' ); ?>
                                <input type="checkbox" name="custom_use_address_check" value="false">
                            <?php } else { ?>
                                <input type="hidden" name="custom_use_address_check" value="checked">
                            <?php } ?>
                        </label>

                        <div class="input ">
                            <label for="locName"><?php _e( 'Place name', 'tny_gmap' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'Joes Music Emporium', 'tny_gmap' ); ?>" name="locName"
                                   id="locName" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float large">
                            <label for="locStAdr"><?php _e( 'Street', 'tny_gmap' ); ?>:</label>
                            <input type="text" name="locStAdr" placeholder="<?php _e( '32 Somewhere Drive', 'tny_gmap' ); ?>"
                                   id="locStAdr" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float med">
                            <label for="locCity"><?php _e( 'City', 'tny_gmap' ); ?>:</label><br/>
                            <input type="text" placeholder="<?php _e( 'Greenville', 'tny_gmap' ); ?>" name="locCity"
                                   id="locCity" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float thirds">
                            <label for="locRegion"><?php _e( 'Region/State', 'tny_gmap' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'Our State', 'tny_gmap' ); ?>" name="locRegion"
                                   id="locRegion" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float thirds">
                            <label for="locPostcode"><?php _e( 'Postcode', 'tny_gmap' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'POSTCODE', 'tny_gmap' ); ?>" name="locPostcode"
                                   id="locPostcode" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float thirds">
                            <label for="locCountry"><?php _e( 'Country', 'tny_gmap' ); ?>:</label>
                            <input type="text" placeholder="<?php _e( 'Proud Country', 'tny_gmap' ); ?>" name="locCountry"
                                   id="locCountry" class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float large">
                            <label for="locWebsite"><?php _e( 'Website', 'tny_gmap' ); ?>:</label><br/>
                            <input type="text" name="locWebsite" placeholder="http://www.someplace.com" id="locWebsite"
                                   class="noEnterSubmit" disabled/>
                        </div>
                        <div class="input float med">
                            <label for="locPhone"><?php _e( 'Primary phone', 'tny_gmap' ); ?>:</label><br/>
                            <input type="text" placeholder="000-000-0000" name="locPhone" id="locPhone" class="noEnterSubmit"
                                   disabled/>
                        </div>
                        <div class="input float thirds">
                            <button name="lookup" type="submit" class="buttn bttn-submit" id="lookup-detials"
                                    onclick="return false;"><?php _e( 'Lookup address', 'tny_gmap' ); ?></button>
                        </div>
                        <div class="input float thirds">
                            <button name="update" type="submit" class="buttn" id="map-update"
                                    onclick="return false;"><?php _e( 'Update info widow', 'tny_gmap' ); ?></button>
                        </div>
                        <div class="input float thirds">
                            <button name="clear-fields" type="submit" class="buttn bttn-warning" id="clear-fields"
                                    onclick="return false;"><?php _e( 'Clear feilds', 'tny_gmap' ); ?></button>
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
                <label class="heading"><?php _e( 'Infowindow extras', 'tny_gmap' ); ?>:<span
                            data-tooltip="<?php _e( 'Use this text area to add custom information to the marker info window. Some HTML is allowed here, and you will find that field auto corrects any malformed or disalowed markup. This prevents a broken tag inserted here from messing up the rest of your site, but it can be frustrating for anything complex. If you want to try longer bit of additional markup, it is easiest to copy-paste it into the field.', 'tny_gmap' ); ?>">?</span></label>
                <div class="group">
                    <div class="input">
                        <div id="mapInfoWindowContainer" class="accordion" style="display:none;">
                                    <textarea rows="5" cols="60" name="mapInfoWindow" id="mapInfoWindow" class="active"
                                              placeholder="<?php _e( 'Some HTML allowed: &lt;p&gt;, &lt;a&gt;, &lt;span&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;em&gt;, &lt;strong&gt;. Attribute styles and classes are allowed i.e. ( &lt;p style=&quot;background-color: blue;&quot; class=&quot;foo bar&quot;&gt;&lt;/p&gt;) Use &lt;br/&gt; for line breaks. Single quotes are NOT allowed.', 'tny_gmap' ); ?>"></textarea>
                        </div>
                    </div>
                </div>
                <label class="heading"><?php _e( 'Map attributes', 'tny_gmap' ); ?>:<span
                            data-tooltip="<?php _e( 'Use these settings to adjust the appearence of your map.', 'tny_gmap' ); ?>">?</span></label>
                <div class="group">

                    <div id="map_extras" class="accordion" style="display:none;">
                        <div class="input float half padRight">
                            <label for="mapHeight"><?php _e( 'Height', 'tny_gmap' ); ?>:</label>
                            <input class="inline" type="text" name="mapHeight" value="500px" id="mapHeight"/>

                        </div>
                        <div class="input float half">
                            <label for="mapWidth"><?php _e( 'Width', 'tny_gmap' ); ?>:</label>
                            <input class="inline" type="text" name="mapWidth" value="100%" id="mapWidth"/>
                        </div>
                        <div class="detial-notice">
                            <small>
                                <em><b><?php _e( 'Specify size in &#039;px&#039; or &#039;%&#039;.', 'tny_gmap' ); ?></b></em>
                                <br><em><?php _e( 'Height &amp; width settings are not reflected in map preview.', 'tny_gmap' ); ?></em>
                            </small>
                        </div>
                        <div class="input float">
                            <label for="mapType">Map:</label>
                            <select name="mapType" id="mapType">
                                <option selected="selected" value="ROADMAP"><?php _e( 'ROADMAP', 'tny_gmap' ); ?></option>
                                <option value="SATELLITE"><?php _e( 'SATELLITE', 'tny_gmap' ); ?></option>
                                <option value="HYBRID"><?php _e( 'HYBRID', 'tny_gmap' ); ?></option>
                                <option value="TERRAIN"><?php _e( 'TERRAIN', 'tny_gmap' ); ?></option>
                            </select>
                        </div>
                        <div class="input float ">
                            <label for="mapMarker"><?php _e( 'Marker', 'tny_gmap' ); ?>:</label>
                            <select name="mapMarker" id="mapMarker">
                                <option selected="selected" value="concert"><?php _e( 'Concert', 'tny_gmap' ); ?></option>
                                <option value="default"><?php _e( 'Google', 'tny_gmap' ); ?></option>
                                <option value="custom"><?php _e( 'Custom', 'tny_gmap' ); ?></option>
                            </select>
                        </div>
                        <div class="input float">
                            <label><?php _e( 'Zoom', 'tny_gmap' ); ?>:</label>
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
                            <label for="mapControls"><?php _e( 'Controls', 'tny_gmap' ); ?>:</label>
                            <select name="mapControls" id="mapControls">
                                <option selected="selected" value="false"><?php _e( 'on', 'tny_gmap' ); ?></option>
                                <option value="true"><?php _e( 'off', 'tny_gmap' ); ?></option>
                            </select>
                        </div>
                        <div class="mapMarkerImage_wrap">
                            <div class="input">
                                <label class="heading" for="mapMarkerImage"><?php _e( 'Custom icon URL', 'tny_gmap' ); ?>:
                                    <span
                                            data-tooltip="<?php _e( 'List the full (or root realitive) path to your custom icon, or select from the list below. If your custom icon won&#039;t load, then there is probably a typo in your path.', 'tny_gmap' ); ?>">?</span></label>
                                <input type="text" name="mapMarkerImage" placeholder="<?php echo $local_uri; ?>/icons/"
                                       value="<?php echo $local_uri; ?>/icons/<?php echo( $loaded_icons[0] ); ?>"
                                       id="mapMarkerImage"/>
                                <span class="icon-list alert"><em>Loaded icons:</em> <br/><?php print( $icons_list ) ?>
                                    <p><em><?php _e( 'Icons courtesy of the', 'tny_gmap' ); ?> <a
                                                    href="http://mapicons.nicolasmollet.com/"
                                                    target="_blank"><?php _e( 'Map Icons Collection', 'tny_gmap' ); ?></a></em>
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