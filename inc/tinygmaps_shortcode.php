<?php
/**
 * The shortcode used in the TR GMaps plugin
 * Map details are saved using the transients api
 * [TRMAP z="10" w="100%" h="300px" maptype="ROADMAP" marker="yes" infowindow="" address="" marker=""]
 *
 * @URL http://www.orionrush.com
 * @since 0.0.1
 * @package WordPress
 * @subpackage TR GMaps
 *
 * @copyright (c) 2012-`date "+%Y"` orionrush. All rights reserved.
 * @license [url] GPL
 * 
 */

// Set the debug var as global
global $tinygmaps_debug;

/**
 * [trmap_load_js Register resources]
 */
function trmap_load_js()
{
    wp_register_script('googelmaps_js', 'http://maps.google.com/maps/api/js?libraries=places&sensor=false', null, null, null, 'true');
}
add_action('wp_enqueue_scripts', 'trmap_load_js');

/**
 * Shortcode parameters 
 * @since 0.0.1
 * 
 * @var string $api_key [A check to see if the constant GOOGLEMAPS_API_KEY has been set.]
 * @var string $tinygmaps_map_id [A 13 charecter id key so mutiple maps may be used on the same page.]
 * @var array $attr {
 *      [The Shortcode attributes array.] 
 *      @type string $z Map zoom level 1 - 22
 *      @type string $w Map width in pixels or percent
 *      @type string $h Map height in pixels or percent
 *      @type string $maptype Map type: ROADMAP, SATALITE, HYBRID, TERRAIN
 *
 *      @type string $lat Location coordinates
 *      @type string $lng
 *      @type string $placeref A Google Places API refrence if the Modal Preview provided one.
 *      @type string $address An address to the location as a single string
 *
 * 		// Explicit location values
 *      @type string $name
 *      @type string $street
 *      @type string $city
 *      @type string $region
 *      @type string $postcode
 *      @type string $country
 *      @type string $web
 *      @type string $phone      
 *      @type string $icon
 *      @type string $marker 
 *      
 *      @type string $infowindowdefault ( yes : no ) Show the infowindow on page load, or keep it hidden untill the map icon is clicked. 
 *      
 *      @type string $infowindow Addional contents of the infowindow, but must be text only without any markup. 
 *      @type string $infowindowb64 Addional contents of the infowindow base 64 encoded so complex additional markup won't break the reading of the shortcode by WordPress.
 *      @type string $hidecontrols (true : false) Hides the zoom, street view and other contols
 *      @type boolean $scale (true : false) Is the map scale drawn?
 *      @type boolean $scrollwheel (true : false) Will the map zoom react to mouse scrollwheel? 
 *      @type boolean $refresh (true : false) Will flush any transient data from being cashed for a given location (good for testing results)
 *      @type boolean $debug (true : false) Will render the return values from the Goolge Maps API object for debugging.
 *      }
 */
add_shortcode('TRMAP', 'trmap_mapme');
function trmap_mapme($attr)
{
    $api_key      = (defined('GOOGLEMAPS_API_KEY')) ? constant('GOOGLEMAPS_API_KEY') : false;
    $tinygmaps_map_id = uniqid('tinygmaps_'); // generate a unique
    global $debug;
    // default atts
    $attr = shortcode_atts(array(
        'z' => '1',
        'w' => '100%',
        'h' => '500px',
        'maptype' => 'ROADMAP',
        'lat' => '',
        'lng' => '',
        'placeref' => '',
        'address' => '',
        'name' => '',
        'street' => '',
        'city' => '',
        'region' => '',
        'postcode' => '',
        'country' => '',
        'web' => '',
        'phone' => '',
        'marker' => '',
        'icon' => '',
        'infowindow' => '',
        'infowindowb64' => '',
        'infowindowdefault' => 'yes',
        'hidecontrols' => 'false',
        'scale' => 'false',
        'scrollwheel' => 'false',
        'refresh' => 'false',
        'debug' => 'false'
    ), $attr);
    
    // clean up array
    array_walk($attr, create_function('&$val', '$val = trim($val);')); //trim white space
    $attr = array_htmlentities($attr); // encode any single quotes that may appear 
    
    // load map params into varriables
    (int) $tinygmaps_z = $attr['z'];
    // make sure h&w have at least px values if nothing is specified
    $tinygmaps_w       = ((substr($attr['w'], -2) != 'px') && (substr($attr['w'], -1) != '%')) ? $attr['w'] . 'px' : $attr['w'];
    $tinygmaps_h       = ((substr($attr['h'], -2) != 'px') && (substr($attr['h'], -1) != '%')) ? $attr['h'] . 'px' : $attr['h'];
    $tinygmaps_maptype = $attr['maptype'];
    
    $tinygmaps_marker        = $attr['marker'];
    $tinygmaps_icon          = $attr['icon'];
    $tinygmaps_infowindow    = $attr['infowindow'];
    $tinygmaps_infowindowb64 = $attr['infowindowb64'];
    
    $tinygmaps_infowindowdefault = ($attr['infowindowdefault'] == 'true' || $attr['infowindowdefault'] == 'yes') ? 'yes' : 'no';
    $tinygmaps_hidecontrols      = ($attr['hidecontrols'] == 'true') ? true : false;
    $tinygmaps_scalecontrol      = ($attr['scale'] == 'true') ? true : false;
    $tinygmaps_scrollwheel       = ($attr['scrollwheel'] == 'true') ? true : false;
    $tinygmaps_refresh           = ($attr['refresh'] == 'true') ? true : false;
    $tinygmaps_debug             = ($attr['debug'] == 'true') ? true : false;
    
    // if these exsists then we've encountered the shortcode, so add the scripts into the footer of the page
    if (!is_admin()) {
        if ($attr['address'] || $attr['placeref'] || $attr['lat'] || $attr['city'])
            wp_print_scripts('googelmaps_js');
        // if ( $attr['address'] || $attr['placeref'] || $attr['lat'] || $attr['city'] ) add_action( 'wp_enqueue_scripts', 'trmap_load_js');	
        
    }
    
    // setup the incoming values
    if ($attr['placeref'] != '' && ($attr['lat'] == '' || $attr['lng'] == '') && $attr['address'] == '') {
        // Here we have a place ref so get/set transient with fetched values
        $attr['address'] = null;
        (array) $attr = tr_map_get_place($api_key, $attr['placeref'], $attr['address'], $tinygmaps_refresh, $tinygmaps_debug);
        
    } elseif ($attr['placeref'] == '' && ($attr['lat'] == '' || $attr['lng'] == '') && $attr['address'] != '') {
        // here we have a address so get/set transient with fetched values		
        (array) $attr = tr_map_get_place($api_key, null, $attr['address'], $tinygmaps_refresh, $tinygmaps_debug);
        
    } elseif ($attr['placeref'] == '' && ($attr['lat'] != '' || $attr['lng'] != '') && $attr['address'] == '') {
        // here we have lat and lng so we will assume the individual params are set too  - do nothing	
        
    } elseif ($attr['placeref'] == '' && ($attr['lat'] == '' || $attr['lng'] == '') && $attr['address'] == '' && $attr['city'] != '') {
        // we have no lat lag but we have address componets so build the address from these attr
        $attr['address'] = $attr['street'] . ', ' . $attr['city'] . ', ' . $attr['region'] . ', ' . $attr['postcode'] . '+' . $attr['country'];
        
        $string          = array(
            ', , ',
            ', , , ',
            ', , , , ',
            ', , , , , '
        );
        $attr['address'] = str_replace($string, '', $attr['address']); // trim any double commas signs that may be in the string
        $attr['address'] = trim($attr['address'], " \t\n\r\0\x0B\,"); // clean any leading whitepasce or commas
        
        if ($attr['address'] == '' || $attr['address'] == ",") {
            $attr['address'] = null; // Set it or forget it
            if ($debug == true && current_user_can('edit_posts'))
                echo __('<b>MAP PLUGIN NOTICE:</b> Insufficiant location information.', 'tinygmaps');
            
        } else {
            (array) $hold = tr_map_get_place($api_key, null, $attr['address'], $tinygmaps_refresh, $tinygmaps_debug);
            // put the missing stuff back in if it's there
            $hold['name']  = $attr['name'];
            $hold['web']   = $attr['web'];
            $hold['phone'] = $attr['phone'];
            $attr          = $hold; // shove it back into the attributes array
        }
        
    } else {
        if ($debug == true && current_user_can('edit_posts'))
            echo __("<b>MAP PLUGIN NOTICE:</b> Conflicting input values.<br> Include either a google <em>placeref</em> <b>OR</b> <em>address</em>, <b>OR</b> explicit <em>lat, lng</em> values <b>WITH</b> explicit location values: <em>name, street, city, state, postcode, country, phone, web.</em></br>", 'tinygmaps');
        return '';
    }
    
    // process the infowindow extras
    $tinygmaps_infowindow_extras = ($tinygmaps_infowindowb64 != '') ? base64_decode($tinygmaps_infowindowb64) : '';
    // add any content from the basic to the end in its own div
    $tinygmaps_infowindow_extras = ($tinygmaps_infowindow != '') ? $tinygmaps_infowindow_extras . '<div>' . $tinygmaps_infowindow . '</div>' : $tinygmaps_infowindow_extras;
    // convert the html special chars
    $tinygmaps_infowindow        = htmlspecialchars_decode($tinygmaps_infowindow_extras, ENT_QUOTES);
    // pass it through KSES to scrub it from unwanted markup
    $tinygmaps_infowindow        = info_window_sanitize($tinygmaps_infowindow);
    // Assemble the infowindow components
    $tinygmaps_infowindow        = get_info_bubble($tinygmaps_icon, $attr['name'], $attr['street'], $attr['city'], $attr['region'], $attr['postcode'], $attr['country'], $attr['phone'], $attr['web'], $tinygmaps_infowindow);
    
    // for external map link
    $linkAddress = $attr['name'] . ' ' . $attr['street'] . ' ' . $attr['city'] . ' ' . $attr['region'] . ' ' . $attr['postcode'] . ' ' . $attr['country'];
    $remove      = array(
        ' ',
        '\t',
        '\n',
        '\r',
        '\0',
        '\x0B'
    ); // wt space
    $linkAddress = str_replace($remove, '+', $linkAddress);
    $linkAddress = urlencode($linkAddress);
    
    // ouput the map
    $returnme = '<style type="text/css" media="screen"> .tr_map_canvas img { max-width: 1000000%; }</style>'; //  Responsive map fix 
    $returnme .= '<div class="tr_maps_wrap_' . $tinygmaps_map_id . ' tr_maps_wrap">';
    $returnme .= '<div class="tr_map_canvas" id="' . $tinygmaps_map_id . '" style="width:' . $tinygmaps_w . '; height:' . $tinygmaps_h . ';"></div>';
    $returnme .= '<div class="tr_maps_link_wrap"><a href="https://maps.google.com/?z=' . $tinygmaps_z . '&q=' . $linkAddress . '&f=d&t=m"  target="_blank">open map in new window</a></div>';
    $returnme .= '</div>';
    $returnme .= '<script type="text/javascript">
		var latlng = new google.maps.LatLng(' . $attr['lat'] . ', ' . $attr['lng'] . ');
		
		var mapOptions = {
			zoom: ' . $tinygmaps_z . ',
			center: latlng,
			scrollwheel: "' . $tinygmaps_scrollwheel . '",
			scaleControl: "' . $tinygmaps_scalecontrol . '",
			disableDefaultUI: "' . $tinygmaps_hidecontrols . '",
			mapTypeId: google.maps.MapTypeId.' . $tinygmaps_maptype . '
		};
		var ' . $tinygmaps_map_id . ' = new google.maps.Map(document.getElementById("' . $tinygmaps_map_id . '"), mapOptions);
		';
    //add custom image
    if ($tinygmaps_marker != '') {
        $returnme .= 'var image = "' . $tinygmaps_marker . '";';
    }
    $returnme .= '
		var marker = new google.maps.Marker({
			map: ' . $tinygmaps_map_id . ',
			';
    if ($tinygmaps_marker != '') {
        $returnme .= 'icon: image,';
    }
    $returnme .= '
			position: ' . $tinygmaps_map_id . '.getCenter()
		});
		';
    
    //infowindow
    if ($tinygmaps_infowindow) {
        $returnme .= '
			var contentString = \'' . $tinygmaps_infowindow . '\'; // Here is my comment
			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});
			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});
			google.maps.event.addListener(marker, \'click\', function() {
			  infowindow.open(' . $tinygmaps_map_id . ',marker);
			});
			';
        //infowindow default
        if ('yes' == $tinygmaps_infowindowdefault) {
            $returnme .= '
					infowindow.open(' . $tinygmaps_map_id . ',marker);
				';
        }
    }
    $returnme .= '</script>';
    return $returnme;
}

/**
 * Sanitize any incoming infowindow markup
 * @var  string [Infowindow string provided by the user to be scrubbed.]
 * @uses  wp_kses() [This function makes sure that only the allowed HTML element names, attribute names and attribute values plus only sane HTML entities will occur in $string.]
 * @link (wp_kses(), http://codex.wordpress.org/Function_Reference/wp_kses)
 * @return string [Returns the sanatised string]
 */
function info_window_sanitize($string)
{
    str_replace("'", "", $string); // replace single quotes with double
    
    $allowed_html      = array(
        'p' => array(
            'style' => array()
        ),
        'a' => array(
            'style' => array(),
            'href' => array(),
            'title' => array(),
            'target' => array()
        ),
        'span' => array(
            'style' => array()
        ),
        'ul' => array(
            'style' => array()
        ),
        'ol' => array(
            'style' => array()
        ),
        'li' => array(
            'style' => array()
        ),
        'br' => array(),
        'hr' => array(),
        'em' => array(
            'style' => array()
        ),
        'strong' => array(
            'style' => array()
        )
        
    );
    $allowed_protocols = array(
        'http',
        'https'
    );
    return wp_kses($string, $allowed_html, $allowed_protocols);
}



/**
 * [get_info_bubble Assembles the infowindow interior using seed values gathered either from the user via shortcode or the Google API call.]
 * @param  [string] $icon    [Gathered either from the shortcode or Google API call.]
 * @param  [string] $name    [Gathered either from the shortcode or Google API call.]
 * @param  [string] $street  [Gathered either from the shortcode or Google API call.]
 * @param  [string] $city    [Gathered either from the shortcode or Google API call.]
 * @param  [string] $state   [Gathered either from the shortcode or Google API call.]
 * @param  [string] $post    [Gathered either from the shortcode or Google API call.]
 * @param  [string] $country [Gathered either from the shortcode or Google API call.]
 * @param  [string] $phone   [Gathered either from the shortcode or Google API call.]
 * @param  [string] $web     [Gathered either from the shortcode or Google API call.]
 * @param  [string] $info    [Gathered either from the shortcode or Google API call.]
 * @return [string]          [The assembled markup for the interior of the infowindow.]
 */
function get_info_bubble($icon, $name, $street, $city, $state, $post, $country, $phone, $web, $info)
{
    $iconStyle             = (($icon != '') ? 'max-width: 150px; ' : 'max-width: 200px; ');
    $tinygmaps_infowindowPlace = '<div class="marker inside"  >';
    $tinygmaps_infowindowPlace .= '<b>' . $name . '</b>';
    $tinygmaps_infowindowPlace .= '<table>';
    $tinygmaps_infowindowPlace .= '<tbody>';
    $tinygmaps_infowindowPlace .= '<tr>';
    $tinygmaps_infowindowPlace .= '<td>';
    $tinygmaps_infowindowPlace .= '<div class="" style="' . $iconStyle . '" >';
    $tinygmaps_infowindowPlace .= ($street != null && $street != '' && $street != '') ? '<div>' . $street . '</div>' : '';
    $tinygmaps_infowindowPlace .= ($city != null && $city != '' && $city !== '') ? '<div>' . $city . ', ' : '<div>';
    $tinygmaps_infowindowPlace .= ($state != null && $state != '' && $state != '') ? $state . '</div>' : '</div>';
    $tinygmaps_infowindowPlace .= ($post != null && $post != '' && $post != '') ? '<div>' . $post . '</div>' : '</div>';
    $tinygmaps_infowindowPlace .= ($country != null && $country != '' && $country != '') ? '<div>' . $country . '</div>' : '</div>';
    $tinygmaps_infowindowPlace .= ($phone != null && $phone != '' && $phone != '') ? '<div>' . $phone . '</div>' : '';
    $tinygmaps_infowindowPlace .= ($web != null && $web != '' && $web != '') ? '<div style="max-width: 100%; white-space: nowrap; width: 100%; overflow: hidden;  -o-text-overflow: ellipsis;  text-overflow: ellipsis;"><a href="' . $web . '" class="gmap_link" target="_blank" style="">' . $web . '</a></div>' : '';
    $tinygmaps_infowindowPlace .= '</div>';
    $tinygmaps_infowindowPlace .= '</td>';
    $tinygmaps_infowindowPlace .= '<td>';
    $tinygmaps_infowindowPlace .= ($icon != null && $icon != '') ? '<img src="' . $icon . '" class="marker icon" style="margin: 0 5px 15px 5px; width: 60px; height: auto; " />' : '';
    $tinygmaps_infowindowPlace .= '</td>';
    $tinygmaps_infowindowPlace .= '</tr>';
    $tinygmaps_infowindowPlace .= '</tbody>';
    $tinygmaps_infowindowPlace .= '</table>';
    $tinygmaps_infowindowPlace .= ($info != null && $info != '') ? '<div class="marker extras" style="border-top: 1px dotted #949494; margin-top: 5px; max-width: 265px; min-height: 40px; overflow: hidden; white-space: pre-wrap;" >' . $info . '</div>' : '';
    $tinygmaps_infowindowPlace .= '</div>';
    return $tinygmaps_infowindowPlace;
}

/**
 * [tr_map_get_place gets the place information from the approporate Google API and sets it to a transient]
 * @param  [type] $api_key       [The Client API Key provided by the user.]
 * @param  [type] $placeRef      [The optional Google Places Refrence if it has been provided]
 * @param  string $address       [An optional address sting]
 * @param  [type] $force_refresh [Set by the "refresh" short code parm, this flushes any transient values associated with this addres hash]
 * @param  [type] $plugin_debug  [Prints debuggin information on to the client end, if the user has admin rights.]
 * @return [type]                [description]
 */
function tr_map_get_place($api_key, $placeRef, $address = '', $force_refresh, $plugin_debug)
{
    global $debug;
    // Transient hashes need to be less then 45 char long
    $location = ($placeRef) ? substr($placeRef, 0, 44) : substr(md5($address), 0, 44);
    $location = get_transient($location);
    
    if ($force_refresh || false === $location) {
        if ($placeRef != '' && $placeRef != null) {
            // return early now if we dont have an api key
            if (!$api_key) {
                exit(__("<b>MAP PLUGIN NOTICE:</b> Google API Key has not been set and we cannot process a Places API refrence without it. See documentation on how to get and set your own key.", 'tinygmaps'));
                
            }
            $args = array(
                'reference' => $placeRef,
                'key' => $api_key,
                'sensor' => 'false'
            );
            $url  = add_query_arg($args, 'https://maps.googleapis.com/maps/api/place/details/json');
        } elseif ($address != '') { // we're geocoding
            $args = array(
                'address' => urlencode($address),
                'sensor' => 'false'
            );
            //// $args       = array( 'address' => urlencode( $address ), 'lat' => $lat, 'lng' => $lng, 'sensor' => 'false' );
            $url  = add_query_arg($args, 'http://maps.googleapis.com/maps/api/geocode/json');
        } else {
            if ($debug == true && current_user_can('edit_posts')) {
                echo __("<b>MAP PLUGIN NOTICE:</b> There doesn't seem to be a location or address, check that the shortcode is formed propperly.", 'tinygmaps');
            }
            return '';
        }
        
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            if ($debug == true && current_user_can('edit_posts')) {
                echo __("<b>MAP PLUGIN NOTICE:</b> We recieved an error in the responce: </br>", 'tinygmaps');
                echo "<pre>";
                print_r($response);
                echo "</pre>";
            }
            return '';
        }
        $data = wp_remote_retrieve_body($response);
        
        if (is_wp_error($data)) {
            if ($debug == true && current_user_can('edit_posts')) {
                echo __("<b>MAP PLUGIN NOTICE:</b> There were problems retrieving the body of the responce. ", 'tinygmaps');
            }
            return '';
        }
        if ($response['response']['code'] == 200) {
            $data = json_decode($data);
            
            if ($data->status === 'OK') {
                if ($debug == true && current_user_can('edit_posts')) {
                    echo __("<b>MAP PLUGIN NOTICE:</b> Status OK", 'tinygmaps');
                    echo "<pre>";
                    print_r($data);
                    echo "</pre>";
                }
                if ($plugin_debug == true && current_user_can('edit_posts')) {
                    echo __("<b>Google Responce:</b>", 'tinygmaps');
                    echo "<pre>";
                    echo $debug;
                    print_r($data);
                    echo "</pre>";
                }
                // Places vs Geocoding APIs
                $result = ($placeRef != '' && $placeRef != null) ? $data->result : $data->results[0];
                
                $coordinates        = $result->geometry->location;
                $cache_value['lat'] = (string) $coordinates->lat;
                $cache_value['lng'] = (string) $coordinates->lng;
                
                // top level items
                $cache_value['name']  = (string) (property_exists($result, 'name')) ? htmlentities((string) $result->name, ENT_QUOTES) : '';
                $cache_value['icon']  = (string) (property_exists($result, 'icon')) ? ((string) $result->icon) : '';
                $cache_value['phone'] = (string) (property_exists($result, 'formatted_phone_number')) ? ($result->formatted_phone_number) : '';
                $cache_value['web']   = (string) (property_exists($result, 'website')) ? ($result->website) : '';
                
                // Address components		
                $premise               = (processObject('premise', $result)) ? processObject('premise', $result) . ', ' : '';
                $street_number         = processObject('street_number', $result);
                $route                 = processObject('route', $result);
                $street_number         = processObject('street_number', $result);
                $streetAddress         = $premise . ' ' . $street_number . ' ' . $route;
                $cache_value['street'] = htmlentities($streetAddress, ENT_QUOTES);
                
                // City
                $city                = (processObject('administrative_area3', $result)) ? processObject('administrative_area3', $result) : '';
                $city                = (processObject('locality', $result)) ? processObject('locality', $result) : $city;
                $city                = (processObject('sublocality', $result)) ? processObject('sublocality', $result) : $city;
                $city                = (processObject('postal_town', $result)) ? processObject('postal_town', $result) : $city;
                $cache_value['city'] = htmlentities($city, ENT_QUOTES);
                
                // State
                $region  = (processObject('locality', $result)) ? processObject('locality', $result) : '';
                $region  = (processObject('administrative_area_level_1', $result)) ? processObject('administrative_area_level_1', $result) : $region;
                $regionB = (processObject('administrative_area_level_2', $result)) ? processObject('administrative_area_level_2', $result) : '';
                if (($region == '') && ($regionB != $city))
                    $region = $regionB;
                $cache_value['region']   = htmlentities($region, ENT_QUOTES);
                // Postcode
                $cache_value['postcode'] = htmlentities(processObject('postal_code', $result), ENT_QUOTES);
                // Country
                $cache_value['country']  = htmlentities(processObject('country', $result), ENT_QUOTES);
                
                //cache address details for 3 months
                set_transient(substr($placeRef, 0, 44), $cache_value, 3600 * 24 * 30 * 3);
                $data = $cache_value;
                
            } elseif ($data->status === 'UNKNOWN_ERROR') {
                if ($debug == true && current_user_can('edit_posts'))
                    echo __('<b>MAP PLUGIN NOTICE:</b> Server-side error, please try again in a short while.', 'tinygmaps');
                return '';
            } elseif ($data->status === 'ZERO_RESULTS') {
                if ($debug == true && current_user_can('edit_posts'))
                    echo __('<b>MAP PLUGIN NOTICE:</b> No location found for the entered place reference. This may indicate the location may have changed names, or is no longer opperating.', 'tinygmaps');
                return '';
            } elseif ($data->status === 'OVER_QUERY_LIMIT') {
                if ($debug == true && current_user_can('edit_posts'))
                    echo __('<b>MAP PLUGIN NOTICE:</b> This API Key is over its quota.', 'tinygmaps');
                return '';
            } elseif ($data->status === 'REQUEST_DENIED') {
                if ($debug == true && current_user_can('edit_posts'))
                    echo __('<b>MAP PLUGIN NOTICE:</b> Request Denied. Usually this is due to the sensor parameter being missing in the search string..', 'tinygmaps');
                return '';
            } elseif ($data->status === 'INVALID_REQUEST') {
                if ($debug == true && current_user_can('edit_posts'))
                    echo __('<b>MAP PLUGIN NOTICE:</b> Invalid request. Did you enter a location reference?', 'tinygmaps');
                return '';
            } elseif ($data->status === 'NOT_FOUND') {
                if ($debug == true && current_user_can('edit_posts'))
                    echo __('<b>MAP PLUGIN NOTICE:</b> Place not found. Usually this is due to an incomplete or corrupted reference string.', 'tinygmaps');
                return '';
            } else {
                if ($debug == true && current_user_can('edit_posts'))
                    echo __('<b>MAP PLUGIN NOTICE:</b> Something went wrong while retrieving your map, please ensure you have entered the short code correctly.', 'tinygmaps');
                return '';
            }
        } else {
            $responseCode = $response['response']['code'];
            if ($debug == true && current_user_can('edit_posts'))
                echo __('Unable to contact Google API, service response: ' . $responseCode, 'tinygmaps');
            return '';
        }
    } else {
        // return cached results
        $data = $location;
    }
    if ($plugin_debug == true && current_user_can('edit_posts')) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
    return ($data) ? $data : '';
}

/**
 * *********************************************************************
 * Process the returned Geocodeing, or Places API object for address values
 * 
 * @var string $needle [The google address element beeing searched for]
 * @var array $haystack [The Google place results as an array]
 * @return string [The information held in the array at the "needle" position saught after, such as "city" or "postcode" ] 
 * 
 */
function processObject($needle, $haystack)
{
    foreach ($haystack->address_components as $address) {
        // Repeat the following for each desired type
        if (in_array($needle, $address->types)) {
            return trim($address->long_name);
        }
    }
    return null;
}
/**
 * Iterates through and performs htmlentites ENT_QUOTES on all elements of an array 
 * @var    array $elem [An array of strings]
 * @return array [an array of strings with all quotes encoded]
 * 
 */
function array_htmlentities(&$elem)
{
    if (!is_array($elem)) {
        $elem = htmlentities($elem, ENT_QUOTES);
    } else {
        foreach ($elem as $key => $value)
            $elem[$key] = array_htmlentities($value);
    }
    return $elem;
}