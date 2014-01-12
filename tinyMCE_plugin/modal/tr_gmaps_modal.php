
<?php
/************************************************************************
 * Modal window for _tr_gmaps tinyMCE editor
 * This is a shortcode builder with preview functionality
 * Lets the user set the parameters for the generated map and see what the output will look like.
 *
 * Copyright (c) 2012-`date "+%Y"` orionrush. All rights reserved.
 *
 * [map id="map" z="10" w="100%" h="300" maptype="ROADMAP" marker="yes" infowindow="" address="" kml=""  markerimage=""]
 * **********************************************************************
 */
// Load up Wordpress so we can use get_bloginfo('url') etc
define('WP_USE_THEMES', false);
require('../../../../../../../wp-blog-header.php');
$pluginfolder = get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));

?>
<!DOCTYPE html>
<html>
<head>
<title>Google Map</title>
<link rel="stylesheet" href="tr_maps_modal.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
<script src="http://maps.google.com/maps/api/js?libraries=places&sensor=false"></script>
<script src="<?php echo get_bloginfo('url') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
<script src="tr_maps_modal_loadmaps.js"/>
</head>
<body>
	<div id="button-dialog">
		<form action="/" method="get" accept-charset="utf-8">
			<div class="input">
				<label for="mapAddress">Address:</label></br>
				<input style="width: 90%" class="wide" type="text" name="mapAddress" value="" id="mapAddress" />
			</div>
			<div class="input">
				<label for="mapId">Map Id:
				<input class="" type="text" name="mapId" value="map" id="mapId" />
				</label></br>
				<label><em>Each map needs a unique id: map1, map2 etc.</em></label></br>
			</div>
			<hr>
			<div class="input Left">
				<label for="mapHight">Map Hight:
				<input type="text" name="mapHeight" value="300" id="mapHeight" />
				</label></br>
			</div>
			<div class="input float">
				<label for="mapWidth">Map Width:
				<input type="text" name="mapWidth" value="100%" id="mapWidth" />
				</label></br>
			</div>
			<hr>
			<div class="input float">
				<label for="mapType">Map Type:
				<select list="mapType" name="mapType" id="mapType" />
					<option selected="selected" value="ROADMAP" >ROADMAP</option>
					<option value="SATELLITE" >SATELLITE</option>
					<option value="HYBRID" >HYBRID</option>
					<option value="TERRAIN" >TERRAIN</option>
				</select>
				</label></br>
			</div>
			<div class="input float ">
				<label for="mapMarker">Marker Type:
				<select list="mapMarker" name="mapMarker" value="Concert" id="mapMarker" />
					<option value="none">None</option>
					<option selected="selected" value="concert">Concert</option>
					<option value="default">Default</option>
					<option value="custom">Custom</option>
				</select>
				</label></br>
			</div>
					<div class="input float">
				<label>Zoom Level:
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
					<option value="11">11</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option selected="selected" value="17">17</option>
					<option value="18">18</option>
					<option value="18">19</option>
					<option value="18">20</option>
					<option value="18">21</option>
					<option value="18">22</option>
				</select>
				</label></br>
			</div>
			<hr>
			<div class="input">
				<label classfor="mapKLM">KML URL:</label></br>
				<input class="wide" type="text" name="mapKML" placeholder="URL/to/kml/address" value="" id="mapKML" />
			</div>
			<div class="input">
				<label for="mapMarkerImage">URL of custom map icon</label></br>
				<input class="wide" type="text" name="mapMarkerImage" placeholder="URL/to/custom/map/icon.png" value="" id="mapMarkerImage" />
			</div>
			<div class="input">
				<label for="mapInfoWindow">Info Bubble Text:</br></label>
				<textarea rows="10" cols="60" class="wide" type="text" name="mapInfoWindow" placeholder="For HTML use &lt;span&gt; instead of &lt;div&gt; and avoid single/double quotes" value="" id="mapInfoWindow"></textarea>
			</div>
			<input name="save" type="submit" class="button button-primary button-large wp-core-ui" id="insert" value="Insert" onclick="javascript:tr_gmaps.insert(tr_gmaps.local_ed)">
		</form>
	</div>
	<label><em>Map preview does not reflect all available options.</em></label></br>
	<div id="map_canvas"></div>
</body>
<footer>
</footer>
</html>