<?php
/*
* Based on : Google Maps v3 Shortcode by yohda
* Added some cleanup, and a button for the tinymce editor found under _tr_support_tinymce.php
* Works with a modal window from the editor that allows the user to input data and generate the shortcode as needed.
* Map details are saved using the transients api
* [map id="map" z="10" w="100%" h="300" maptype="ROADMAP" marker="yes" infowindow="" address="" kml=""  markerimage=""]
*/
function tr_gmaps_scripts() {
	wp_register_script( 'googelmaps', 'http://maps.google.com/maps/api/js?libraries=places&sensor=false' , array(), NULL, true );
	wp_register_style( 'googelmaps', TR_MAPSPLUGIN_URL . 'css/maps.css', '', NULL, $media = 'all' );
}
add_action( 'wp_enqueue_scripts', 'tr_gmaps_scripts' );

// test if the_content has the shortcode in it
function tr_maps_has_shortcode() {
    global $post;
    $post_id = $post->ID;
    $post_to_check = get_post($post_id);
    $shortcode = 'Nothing';
    // false because we have to search through the post content first
    $found = false;
    // check the post content for the short code
    if ( stripos($post_to_check->post_content, '[' . $shortcode) == !false ) {
        // we have found the short code so add the styles
        wp_enqueue_style('googelmaps');
		wp_enqueue_script('googelmaps');
    }
}
add_filter( 'the_content', 'tr_maps_has_shortcode' );

function tr_mapme($attr) {
	// Load the scripts - will default to footer at this point.
	wp_enqueue_script('googelmaps');

	// default atts
	$attr = shortcode_atts(array(
									'lat'   => '0',
									'lon'	=> '0',
									'id' => 'map',
									'z' => '1',
									'w' => '400',
									'h' => '300',
									'maptype' => 'ROADMAP',
									'address' => '',
									'kml' => '',
									'kmlautofit' => 'yes',
									'marker' => '',
									'markerimage' => '',
									'traffic' => 'no',
									'bike' => 'no',
									'fusion' => '',
									'start' => '',
									'end' => '',
									'infowindow' => '',
									'infowindowdefault' => 'yes',
									'directions' => '',
									'hidecontrols' => 'false',
									'scale' => 'false',
									'scrollwheel' => 'true'
									), $attr);
	$returnme = '<div id="' .$attr['id'] . '" style="width:' . $attr['w'] . 'px;height:' . $attr['h'] . 'px;"></div>';

	//directions panel
	if($attr['start'] != '' && $attr['end'] != '')
	{
		$panelwidth = $attr['w']-20;
		$returnme .= '<div id="directionsPanel" style="width:' . $panelwidth . 'px;height:' . $attr['h'] . 'px;"></div><br>';
	}
	$returnme .= '<script type="text/javascript">
		var latlng = new google.maps.LatLng(' . $attr['lat'] . ', ' . $attr['lon'] . ');
		var myOptions = {
			zoom: ' . $attr['z'] . ',
			center: latlng,
			scrollwheel: ' . $attr['scrollwheel'] .',
			scaleControl: ' . $attr['scale'] .',
			disableDefaultUI: ' . $attr['hidecontrols'] .',
			mapTypeId: google.maps.MapTypeId.' . $attr['maptype'] . '
		};
		var ' . $attr['id'] . ' = new google.maps.Map(document.getElementById("' . $attr['id'] . '"),
		myOptions);';
		//kml
		if($attr['kml'] != '')
		{
			if($attr['kmlautofit'] == 'no'){
				$returnme .= 'var kmlLayerOptions = {preserveViewport:true};';
			}
			else {
				$returnme .= 'var kmlLayerOptions = {preserveViewport:false};';
			}
			$returnme .= 'var kmllayer = new google.maps.KmlLayer(\'' . html_entity_decode($attr['kml']) . '\',kmlLayerOptions);
			kmllayer.setMap(' . $attr['id'] . ');';
		}

		//directions
		if($attr['start'] != '' && $attr['end'] != '')
		{
			$returnme .= '
			var directionDisplay;
			var directionsService = new google.maps.DirectionsService();
			directionsDisplay = new google.maps.DirectionsRenderer();
			directionsDisplay.setMap(' . $attr['id'] . ');
			directionsDisplay.setPanel(document.getElementById("directionsPanel"));
				var start = \'' . $attr['start'] . '\';
				var end = \'' . $attr['end'] . '\';
				var request = {
					origin:start,
					destination:end,
					travelMode: google.maps.DirectionsTravelMode.DRIVING
				};
				directionsService.route(request, function(response, status) {
					if (status == google.maps.DirectionsStatus.OK) {
						directionsDisplay.setDirections(response);
					}
				});
			';
		}
		//traffic
		if($attr['traffic'] == 'yes')
		{
			$returnme .= 'var trafficLayer = new google.maps.TrafficLayer();
			trafficLayer.setMap(' . $attr['id'] . ');';
		}

		//bike
		if($attr['bike'] == 'yes')
		{
			$returnme .= '
			var bikeLayer = new google.maps.BicyclingLayer();
			bikeLayer.setMap(' . $attr['id'] . ');
			';
		}

		//fusion tables
		if($attr['fusion'] != '')
		{
			$returnme .= '
			var fusionLayer = new google.maps.FusionTablesLayer(' . $attr['fusion'] . ');
			fusionLayer.setMap(' . $attr['id'] . ');
			';
		}

		//address
		if($attr['address'] != '')
		{
			$returnme .= '
			var geocoder_' . $attr['id'] . ' = new google.maps.Geocoder();
			var address = \'' . $attr['address'] . '\';
			geocoder_' . $attr['id'] . '.geocode( { \'address\': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					' . $attr['id'] . '.setCenter(results[0].geometry.location);
					';

					if ($attr['marker'] !='')
					{
						//add custom image
						if ($attr['markerimage'] !='')
						{
							$returnme .= 'var image = "'. $attr['markerimage'] .'";';
						}
						$returnme .= '
						var marker = new google.maps.Marker({
							map: ' . $attr['id'] . ',
							';
							if ($attr['markerimage'] !='')
							{
								$returnme .= 'icon: image,';
							}
						$returnme .= '
							position: ' . $attr['id'] . '.getCenter()
						});
						';

						//infowindow
						if($attr['infowindow'] != '')
						{
							//first convert and decode html chars
							$thiscontent = htmlspecialchars_decode($attr['infowindow']);
							$thiscontent.= '</br><span  style="float: right; margin-top:10px;"><a target="_blank" href="https://maps.google.com/maps?saddr=' . $attr['address'] . '">open map in new window</a></span>';
							$returnme .= '
							var contentString = \'' . $thiscontent . '\';
							var infowindow = new google.maps.InfoWindow({
								content: contentString
							});

							var infowindow = new google.maps.InfoWindow({
								content: contentString
							});

							google.maps.event.addListener(marker, \'click\', function() {
							  infowindow.open(' . $attr['id'] . ',marker);
							});
							';

							//infowindow default
							if ($attr['infowindowdefault'] == 'yes')
							{
								$returnme .= '
									infowindow.open(' . $attr['id'] . ',marker);
								';
							}
						}
					}
			$returnme .= '
				} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
			});
			';
		}
		//marker: show if address is not specified
		if ($attr['marker'] != '' && $attr['address'] == '')
		{
			//add custom image
			if ($attr['markerimage'] !=''){
				$returnme .= 'var image = "'. $attr['markerimage'] .'";';
			}
			$returnme .= '
				var marker = new google.maps.Marker({
				map: ' . $attr['id'] . ',
				';
				if ($attr['markerimage'] !='')
				{
					$returnme .= 'icon: image,';
				}
			$returnme .= '
				position: ' . $attr['id'] . '.getCenter()
			});
			';
			//infowindow
			if($attr['infowindow'] != '')
			{
				$returnme .= '
				var contentString = \'' . $attr['infowindow'] . $thiscontent . '\'; // Here is my comment
				var infowindow = new google.maps.InfoWindow({
					content: contentString
				});

				google.maps.event.addListener(marker, \'click\', function() {
				  infowindow.open(' . $attr['id'] . ',marker);
				});
				';
				//infowindow default
				if ($attr['infowindowdefault'] == 'yes')
				{
					$returnme .= '
						infowindow.open(' . $attr['id'] . ',marker);
					';
				}
			}
		}
		$returnme .= '</script>';
		return $returnme;
}
 add_shortcode('tr_gmaps', 'tr_mapme');
