<?php
/************************************************************************
 * Markup for modal window for _tr_gmaps tinyMCE editor
 * This modal is a shortcode builder with preview functionality for the TR Maps plugin.
 * @link http://www.orionrush.com
 * @since 0.0.1
 *
 * @package WordPress
 * @subpackage TR Gmaps
 * 
 * @copyright (c) 2012-`date "+%Y"` orionrush. All rights reserved.
 * @license GPL
 * **********************************************************************
 */

// Load up Wordpress
define('WP_USE_THEMES', false);
require('../../../../../../wp-blog-header.php');

echo constant(WP_DEFAULT_THEME);

$plugin_tinymce_dir_url = make_root_relative( TRMAPS_MAPSPLUGIN_URL . 'inc/tinymce/' );
// do we have the api?
(defined('GOOGLEMAPS_API_KEY') && (constant('GOOGLEMAPS_API_KEY') != null || constant('GOOGLEMAPS_API_KEY') != '')) ?  $api_test = true : $api_Test = false;

// Retrieve a list of all the icons in the icons directory 
$loaded_icons = plugin_dir_path( __FILE__ ) . "../icons/";
$loaded_icons = array_diff(scandir($loaded_icons), array('..', '.', '.DS_Store'));
$loaded_icons = array_values($loaded_icons );
$icons_list = array_map(
		function ($el){
			return "<a href=\"#\" title=\"{$el}\" class=\"map-icon\">{$el}</a>";
		}, $loaded_icons
	);
$icons_list = implode(", ", $icons_list);

/**
 * Returns local urls as root realitive strings.
 * Takes a full URL, removes the server name and returns the result as a root relative URL.
 * @since 0.0.1
 * @param  string $url The full url to be made root realitive.
 * @return string The root realitive url.
 */
function make_root_relative($url) {
    return preg_replace('!http(s)?://' . $_SERVER['SERVER_NAME'] . '/!', '/', (string)$url);
}
?>

<!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Google Map</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link type="text/css" rel="stylesheet" href="tr_maps_modal.css?<?php echo uniqid(); ?>" />
	<link type="text/css" rel="stylesheet" href="jquery.qtip.custom/jquery.qtip.min.css" />
	<script src="<?php echo get_bloginfo('url') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	</head>
	<body>
		<?php if (!$api_test) { ?> 
			<div class="alert warning fadeout floating"> <?php _e('API KEY NOT DEFINED, see documentation for full functionality.', 'trmaps'); ?> </div>
		<?php } ?>
		<div id="button-dialog" class="wrap">
			<form action="/" method="get" accept-charset="utf-8">
				<?php ?>

							
				<div class="group <?php echo(!$api_test)? 'hidden': ''?>	" id="mapAddress-group">
				<label class="heading" for="mapAddress" > <?php _e('Address lookup', 'trmaps')?>:</label>
					<div class="autoMapAddress input">
						<input type="text" name="mapAddressAuto"  id="mapAddress" class="noEnterSubmit" />
					</div>
				</div>
		
			<?php if ($api_test){?>						
				<label class="heading" ><?php _e('Location details', 'trmaps'); ?>:<span data-tooltip="<?php _e('Only use this section if &quot;Address lookup&quot; can&#039;t find, gives incorrect or incomplete results. </br>&quot;Address lookup&quot; gives us a refrence we can use to automatically upadte location details from Google&#039;s servers.</br></br>Results from this section may differ from that of &quot;Address lookup&quot; and may require you provide greater detial to achive correct results.</br> </br>If you just want to add a website or phone number, consider adding these to the &quot;Infowindow extras&quot; section instead.', 'trmaps')?>">?</span></label>
			<? } else { ?>
				<label class="heading" ><?php _e('Location details', 'trmaps'); ?>:<span data-tooltip="<?php _e('Input your location address.', 'trmaps'); ?>" >?</span></label>
				<? } ?>
				<div class="group">
						<div id="address_extras" class="accordian" style="display:none;">
							<label for="custom_use_address_check">
							<?php if ($api_test){?>
							<?php _e('Use custom address details?', 'trmaps'); ?>
							<input type="checkbox" name="custom_use_address_check" value="false">
							<? } else { ?>
							<input type="hidden" name="custom_use_address_check" value="checked">
							<? } ?>
							</label>
							
							<div class="input ">
								<label for="locName"><?php _e('Place name', 'trmaps'); ?>:</label>
								<input type="text" placeholder="<?php _e('Joes Music Emporium', 'trmaps'); ?>" name="locName"  id="locName" class="noEnterSubmit" disabled/>
							</div>
							<div class="input float large">
								<label for="locStAdr"><?php _e('Street', 'trmaps'); ?>:</label>
								<input type="text" name="locStAdr"  placeholder="<?php _e('32 Somewhere Drive', 'trmaps'); ?>" id="locStAdr"  class="noEnterSubmit" disabled/>
							</div>
							<div class="input float med">
								<label for="locCity"><?php _e('City', 'trmaps'); ?>:</label><br/>
								<input type="text" placeholder="<?php _e('Greenville', 'trmaps'); ?>" name="locCity"  id="locCity" class="noEnterSubmit" disabled />
							</div>
							<div class="input float thirds">
								<label for="locRegion"><?php _e('Region/State', 'trmaps'); ?>:</label>
								<input type="text" placeholder="<?php _e('Our State', 'trmaps'); ?>"  name="locRegion"  id="locRegion" class="noEnterSubmit" disabled/>
							</div>
							<div class="input float thirds">
								<label for="locPostcode"><?php _e('Postcode', 'trmaps'); ?>:</label>
								<input type="text" placeholder="<?php _e('POSTCODE', 'trmaps'); ?>"  name="locPostcode"  id="locPostcode" class="noEnterSubmit" disabled/>
							</div>
							<div class="input float thirds">
								<label for="locCountry"><?php _e('Country', 'trmaps'); ?>:</label>
								<input type="text" placeholder="<?php _e('Proud Country', 'trmaps'); ?>"  name="locCountry"  id="locCountry" class="noEnterSubmit" disabled/>
							</div>
								<div class="input float large">
								<label for="locWebsite"><?php _e('Website', 'trmaps'); ?>:</label><br/>
								<input type="text" name="locWebsite"  placeholder="http://www.someplace.com" id="locWebsite" class="noEnterSubmit" disabled/>
							</div>	
							<div class="input float med">
								<label for="locPhone"><?php _e('Primary phone', 'trmaps'); ?>:</label><br/>
								<input type="text" placeholder="000-000-0000"  name="locPhone"  id="locPhone" class="noEnterSubmit" disabled/>
							</div>	
							<div class="input float thirds">
								<button name="lookup" type="submit" class="buttn bttn-submit" id="lookup-detials" onclick="return false;" ><?php _e('Lookup address', 'trmaps'); ?></button>
							</div>
							<div class="input float thirds">
								<button name="update" type="submit" class="buttn" id="map-update" onclick="return false;" ><?php _e('Update info widow', 'trmaps'); ?></button>
							</div>
							<div class="input float thirds">
								<button name="clear-fields" type="submit" class="buttn bttn-warning" id="clear-fields" onclick="return false;" ><?php _e('Clear feilds', 'trmaps'); ?></button>
							</div>
							<div class="input">
								<div onselectstart="return false;" class="hidden floating lookup" id="search-report"></div>
							</div>
							<div class="input hidden">
								<input type="hidden" name="mapLat"  id="mapLat" disabled />
								<input type="hidden" name="mapLng"  id="mapLng" disabled/>
								<input type="hidden" name="locIconURL"  id="locIconURL"  disabled />
								<input type="hidden" name="locGoogleRef"  id="locGoogleRef" disabled />
							</div>
						</div>
				</div>
				<label class="heading" ><?php _e('Infowindow extras', 'trmaps');?>:<span data-tooltip="<?php _e('Use this text area to add custom information to the marker info window. Some HTML is allowed here though the field auto corrects malformed or disalowed markup -– so best to copy-paste anything complex.', 'trmaps'); ?>">?</span></label>
				<div class="group">
					<div class="input">
						<div id="mapInfoWindowContainer" class="accordian" style="display:none;">
							<textarea rows="5" cols="60" name="mapInfoWindow" id="mapInfoWindow" class="active" placeholder="<?php _e('Some HTML allowed: &lt;p&gt;, &lt;a&gt;, &lt;span&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;em&gt;, &lt;strong&gt;. Atribute styles allowed, use &lt;br/&gt; for line breaks. Single quotes not allowed.', 'trmaps'); ?>"></textarea>
						</div>
					</div>
				</div>
				<label class="heading"><?php _e('Map attributes', 'trmaps'); ?>:<span data-tooltip="<?php _e('Use these settings to adjust the appearence of your map.', 'trmaps'); ?>" >?</span></label>
				<div class="group" >

						<div id="map_extras" class="accordian" style="display:none;">
							<div class="input float half padRight">
								<label  for="mapHeight"><?php _e('Hight', 'trmaps'); ?>:</label>
								<input class="inline" type="text" name="mapHeight" value="500px" id="mapHeight" />

							</div>
							<div class="input float half">
								<label for="mapWidth"><?php _e('Width', 'trmaps'); ?>:</label>
								<input class="inline" type="text" name="mapWidth" value="100%" id="mapWidth" />
							</div>
							<div class="detial-notice">
								<small><em><b><?php _e('Specify size in &#039;px&#039; or &#039;%&#039;', 'trmaps'); ?></b></em></small>
							</div>
							<div class="input float">
								<label for="mapType">Map:</label>
								<select name="mapType" id="mapType" >
									<option selected="selected" value="ROADMAP" ><?php _e('ROADMAP', 'trmaps'); ?></option>
									<option value="SATELLITE" ><?php _e('SATELLITE', 'trmaps'); ?></option>
									<option value="HYBRID" ><?php _e('HYBRID', 'trmaps'); ?></option>
									<option value="TERRAIN" ><?php _e('TERRAIN', 'trmaps'); ?></option>
								</select>
							</div>
							<div class="input float ">
								<label for="mapMarker"><?php _e('Marker', 'trmaps'); ?>:</label>
								<select name="mapMarker" id="mapMarker">
									<option selected="selected" value="concert"><?php _e('Concert', 'trmaps'); ?></option>
									<option value="default"><?php _e('Google', 'trmaps'); ?></option>
									<option value="custom"><?php _e('Custom', 'trmaps'); ?></option>
								</select>					
							</div>
							<div class="input float">
								<label><?php _e('Zoom', 'trmaps'); ?>:</label>
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
								<label for="mapControls"><?php _e('Controls', 'trmaps'); ?>:</label>
								<select name="mapControls" id="mapControls" >
									<option selected="selected" value="false"><?php _e('on', 'trmaps'); ?></option>
									<option value="true"><?php _e('off', 'trmaps'); ?></option>
								</select>					
							</div>
							<div class="mapMarkerImage_wrap">
								<div class="input">
									<label class="heading" for="mapMarkerImage"><?php _e('Custom icon URL', 'trmaps'); ?>: <span data-tooltip="<?php _e('List the full (or root realitive) path to your custom icon, or select from the list below. If your custom icon won&#039;t load, then there is probably a typo in your path.', 'trmaps'); ?>">?</span></label>
									<input type="text" name="mapMarkerImage" placeholder="<?php echo $plugin_tinymce_dir_url; ?>/icons/" value="<?php echo $plugin_tinymce_dir_url; ?>/icons/<?php echo($loaded_icons[0]); ?>" id="mapMarkerImage" />
									<span class="icon-list alert" ><em>Loaded icons:</em> <br /><?php print($icons_list) ?>
									<p><em><?php _e('Icons courtesy of the', 'trmaps'); ?> <a href="http://mapicons.nicolasmollet.com/" target="_blank"><?php _e('Map Icons Collection', 'trmaps'); ?></a></em></p>
									</span> 
								</div>
							</div>
							<div class="detial-notice">
								<small><em><br><?php _e('Hight &amp; width settings are not refelected in map preview.', 'trmaps'); ?></em></small>
							</div>
						</div>	
				</div>
			</form>
		</div>
		<div class="map-preview">
			<div class="map-wrap">
				<div id="map_canvas"></div>
			</div>
			<input name="save" type="submit" class="submit-button" id="insert" value="Insert Map Shortcode" onclick="javascript:trGmaps.insert(trGmaps.local_ed)">
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
		<script src="//maps.google.com/maps/api/js?libraries=places&amp;sensor=false"></script>
		<script src="jquery-base64/jquery.base64.min.js"></script>
		<script src="jquery-htmlClean/jquery.htmlClean.js"></script>
		<script src="jquery.qtip.custom/jquery.qtip.min.js"></script>
		<script src="tr_maps_modal_loadmaps.php"></script>
	</body>
</html>