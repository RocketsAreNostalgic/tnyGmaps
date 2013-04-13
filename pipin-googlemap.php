<?php
/*
Plugin Name: Simple Google Map Short Codes
Plugin URL: http://pippinsplugins.com/simple-google-map-short-codes
Description: Plain and simple Google Maps via a Short Code
Version: 1.0
Author: Pippin Williamson
Author URI: http://pippinsplugins.com
Contributors: mordauk
*/

/**
 * Displays the event map
 *
 * @access	  private
 * @since	   1.0
 * @return	  void
*/

function pw_map_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'address' 	=> false,
			'width' 	=> '100%',
			'height' 	=> '400px'
		),
		$atts
	);
	$address = $atts['address'];

	if( $address ) :
		$coordinates = pw_map_get_coordinates( $address );
		if( !is_array( $coordinates ) )
			return;

		$map_id = uniqid( 'pw_map_' ); // generate a unique ID for this map
		ob_start(); ?>

		<div class="pw_map_canvas" id="<?php echo $map_id; ?>" style="height: <?php echo $atts['height']; ?>; width: <?php echo $atts['width']; ?>"></div>
		<script type="text/javascript">
			var map_<?php echo $map_id; ?>;
			function pw_run_map_<?php echo $map_id ; ?>(){
				var location = new google.maps.LatLng("<?php echo $coordinates['lat']; ?>", "<?php echo $coordinates['lng']; ?>");
				var map_options = {
					zoom: 15,
					center: location,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				map_<?php echo $map_id ; ?> = new google.maps.Map(document.getElementById("<?php echo $map_id ; ?>"), map_options);
				var marker = new google.maps.Marker({
				position: location,
				map: map_<?php echo $map_id ; ?>
				});
			}
			pw_run_map_<?php echo $map_id ; ?>();
		</script>
		<?php
	endif;
	return ob_get_clean();
}
add_shortcode( 'pw_map', 'pw_map_shortcode' );


/**
 * Loads Google Map API
 *
 * @access	  private
 * @since	   1.0
 * @return	  void
*/


// This will load it on every page.
function pw_map_load_scripts() {
	wp_enqueue_script( 'google-maps-api', 'http://maps.google.com/maps/api/js?sensor=false' );
}
add_action( 'wp_enqueue_scripts', 'pw_map_load_scripts' );


/**
 * Retrieve coordinates for an address
 *
 * Coordinates are cached using transients and a hash of the address
 *
 * @access	  private
 * @since	   1.0
 * @return	  void
*/

function pw_map_get_coordinates( $address, $force_refresh = false ) {
	$address_hash = md5($address); //create hash of address for database storage & comparison
	$coordinates = get_transient( $address_hash ); // store the address hash as a transient 

	if( $force_refresh || $coordinates === false ) {
		// api 2
		$url = 'http://maps.google.com/maps/geo?q=' . urlencode($address) . '&output=xml';

		// we'd need to get the detials from this api 3
		//$url = 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false'
		//http://stackoverflow.com/a/4343691/362445 working with JSON in PHP

		//$url = 'http://maps.google.com/maps/api/geocode/xml?address=' . urlencode($address) . '&sensor=false'
	 	$response = wp_remote_get( $url );
	 	if( is_wp_error( $response ) )
	 		return;
	 	$xml = wp_remote_retrieve_body( $response );
	 	//$json = wp_remote_retrieve_body( $response );
	 	if( is_wp_error( $xml ) )
	 		return;
		if ($response['response']['code'] == 200) {
			$data = new SimpleXMLElement($xml);
			//$data = new json_decode($json, true); // turn it into an associative array

			if ($data->Response->Status->code == 200) {
			  	$coordinates = $data->Response->Placemark->Point->coordinates;
			  	//Placemark->Point->coordinates;
			  	$coordinates = explode(',', $coordinates[0]);
			  	$cache_value['lat'] = $coordinates[1];
			  	$cache_value['lng'] = $coordinates[0];
			  	$cache_value['address'] = (string) $data->Response->Placemark->address[0];

			  	//We need to get every value that might be picked up via the plugin popup
			  			  //icon
			  			  //place name
			  			  //address
			  			  //phone
			  			  //web
			  			  //user added map pin buble text handeled in enclosing shortcode
			  	// cache coordinates for 3 months
			  	set_transient($address_hash, $cache_value, 3600*24*30*3);
			  	$data = $cache_value;
			} elseif ($data->Response->Status->code == 602) { // API3 "ZERO_RESULTS"
			  	return 'Unable to parse entered address or latlag. API response code: ' . @$data->Response->Status->code; 
			} else {
			   	return 'XML parsing error. Please try again later. API response code: ' . @$data->Response->Status->code; // API 3 INVALID_REQUEST, ZERO_RESULTS
			}

		} else {
		 	return 'Unable to contact Google API service.'; 
		}
	} else {
		$data = $coordinates;
	}
	return $data;
}