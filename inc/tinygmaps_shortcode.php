<?php
/**
 * Shortcode outputs markup and enqueues the js just in time.
 *
 * Shortcode parameters
 * @since 0.0.1
 * 
 * @var string $api_key [A check to see if the constant GOOGLE_API_KEY has been set.]
 * @var string $tinygmaps_map_id [A 13 character id key so multiple maps may be used on the same page.]
 * @var array $attr {
 *      // Shortcode attributes array
 *      @type string $z             | Map zoom level 1 - 22
 *      @type string $w             | Map width in pixels or percent
 *      @type string $h             | Map height in pixels or percent
 *      @type string $maptype       | Map type: ROADMAP, SATELLITE, HYBRID, TERRAIN
 *      @type string $lat           | Location latitude
 *      @type string $lng           | Location longitude
 *      @type string $placeID       | A Google Places API reference if provided one.
 *      @type string $address       | An address to the location as a string
 *      @type string $name          | Business or location name
 *      @type string $street        | Building number and street name
 *      @type string $city          | City Name
 *      @type string $region        | State or Provence
 *      @type string $postcode      | Zip or regional post code
 *      @type string $country       | Country code, long or short
 *      @type string $web           | URL inclusive of http://
 *      @type string $phone         | Phone Number
 *      @type string $icon          | An image from the maps api for that location
 *      @type string $marker        | A url full or relative to the plugin (see example) to the icon png
 *      @type string $infowindowdefault  | (yes : no) Show the infowindow on page load, or keep it hidden until the map icon is clicked.
 *      @type string $infowindow    | Additional contents of the infowindow, but must be text only without any markup.
 *      @type string $infowindowb64 | Additional contents of the infowindow base 64 encoded so complex additional markup won't break the reading of the shortcode by WordPress.
 *      @type string $hidecontrols  | (true : false) Hides the zoom, street view and other controls
 *      @type boolean $scale        | (true : false) Is the map scale drawn?
 *      @type boolean $scrollwheel  |  (true : false) Will the map zoom react to mouse scrollwheel?
 *      @type string $static        | Dom width for when a static map should be drawn instead of a dynamic maps for small screens, empty or '0' will indicate not map is drawn
 *      @type int $static_w         | Width of static map in pixels
 *      @type int $static_h         | Height of of static map in pixels
 *      @type boolean $refresh      | (true : false) Will flush any transient data from being cashed for a given location (good for testing results)
 *      @type boolean $debug        | (true : false) Will render the return values from the Google Maps API object for debugging.
 *      }
 */

/* Register scripts */
add_action('init', 'tinygmaps_register_scripts');
function tinygmaps_register_scripts(){
    wp_register_script('googelmaps_js', 'http://maps.google.com/maps/api/js?libraries=places&signed_in=true', null, null, 'true');
    wp_register_script('tinygmaps_init', TINYGMAP_URL . '/inc/js/tinygmaps.min.js', array('googelmaps_js', 'jquery'), '0.0.1', 'true');
}

add_shortcode('TINYGMAPS', 'trmap_mapme');
function trmap_mapme($attr) {
    // Lets enqueue the scripts only if the shortcode has been added
    wp_enqueue_script('googelmaps_js');
    wp_enqueue_script('tinygmaps_init');

    $api_key = (defined('GOOGLE_API_KEY')) ? constant('GOOGLE_API_KEY') : false;
    $tinygmaps_map_id = uniqid('tnygmps_'); // generate a unique map instance for each map displayed
    // default atts
    $attr = shortcode_atts(array(
        'z' => '1',
        'w' => '100%',
        'h' => '500px',
        'maptype' => 'ROADMAP',
        'lat' => '',
        'lng' => '',
        'placeid' => '',
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
        'static' => '767',
        'static_w' => '500',
        'static_h' => '500',
        'refresh' => 'false', // executes if present and not equal to false
        'debug' => 'false'    // executes if present and not equal to false
    ), $attr);
    // clean up array
    array_walk($attr, create_function('&$val', '$val = trim($val);')); //trim white space
    $attr = array_htmlentities($attr); // encode any single quotes that may appear in text

    // load map params into variables
    (int)$tinygmaps_z = $attr['z'];

    // make sure h&w have at least px values if nothing is specified
    $tinygmaps_w = ((substr($attr['w'], -2) != 'px') && (substr($attr['w'], -1) != '%')) ? $attr['w'] . 'px' : $attr['w'];
    $tinygmaps_h = ((substr($attr['h'], -2) != 'px') && (substr($attr['h'], -1) != '%')) ? $attr['h'] . 'px' : $attr['h'];
    $tinygmaps_maptype = $attr['maptype'];

    $tinygmaps_marker = (filter_var($attr['marker'], FILTER_VALIDATE_URL) != FALSE) ? $attr['marker'] : null;
    $tinygmaps_icon = $attr['icon'];
    $tinygmaps_infowindow = $attr['infowindow'];
    $tinygmaps_infowindowb64 = $attr['infowindowb64'];

    $tinygmaps_infowindowdefault = ($attr['infowindowdefault'] == 'true' || $attr['infowindowdefault'] == 'yes') ? 'yes' : 'no';
    $tinygmaps_hidecontrols = ($attr['hidecontrols'] == 'true') ? true : false;
    $tinygmaps_scalecontrol = ($attr['scale'] == 'true') ? true : false;
    $tinygmaps_scrollwheel = ($attr['scrollwheel'] == 'true') ? true : false;

    $tinygmaps_static_width = remove_px_percent($attr['static']);
    $tinygmaps_static_w = remove_px_percent($attr['static_w']);
    $tinygmaps_static_h = remove_px_percent($attr['static_h']);

    $tinygmaps_refresh = (( $attr['refresh'] != 'false' ) ? true : false );
    $tinygmaps_debug = (( $attr['debug'] != 'false' ) ? true : false );

    // setup the incoming values
    if (!empty($attr['placeid'])) {
        // Here we have a place ref so get/set transient with fetched values
        // strip address out as we will want to refer to google's cashed values instead
        $attr['address'] = null;
        // here we have a place_ID so get/set transient with fetched values
        $attr = tr_map_get_place($api_key, $attr['placeid'], null, $tinygmaps_refresh, $tinygmaps_debug);

    } elseif (empty($attr['placeid']) && (empty($attr['lat']) || empty($attr['lng'])) && !empty($attr['address'])) {

        // here we have a address so get/set transient with fetched values
        $attr = tr_map_get_place($api_key, null, $attr['address'], $tinygmaps_refresh, $tinygmaps_debug);

    } elseif (empty($attr['placeid']) && (!empty($attr['lat']) && !empty($attr['lng'])) && empty($attr['address'])) {
        // here we have lat and lng so we will gather any other individual params that are set.
        // no call to tr_map_get_place as we will not cache these values in a transient, as the user will have provided as much material as possible.

    } elseif (empty($attr['placeid']) && (empty($attr['lat']) || empty($attr['lng'])) && empty($attr['address']) && !empty($attr['street']) && !empty($attr['city']) && !empty($attr['region'])) {
        // here we have missing lat lags, and no unified address strings, may have enough address components so build the place query from these attr
        $attr['address'] = $attr['street'] . ', ' . $attr['city'] . ', ' . $attr['region'] . ', ' . $attr['postcode'] . '+' . $attr['country'];
        $string = array(
            ', , ',
            ', , , ',
            ', , , , ',
            ', , , , , '
        );
        $attr['address'] = str_replace($string, ' ', $attr['address']); // trim any double commas signs that may be in the string
        $attr['address'] = trim($attr['address'], " \t\n\r\0\x0B\,"); // clean any leading whitepasce or commas

        if ($attr['address'] == '' || $attr['address'] == ",") {
            $attr['address'] = null; // Set it or forget it

            echo  tr_map_errors($tinygmaps_debug, 'insufficient_address');

        } else {
            // here we don't have an place_ID or address, but we've constructed an address string from the other
            $hold = tr_map_get_place($api_key, null, $attr['address'], $tinygmaps_refresh, $tinygmaps_debug);

            // put the missing stuff back in if it's there
            $hold['name'] = $attr['name'];
            $hold['web'] = $attr['web'];
            $hold['phone'] = $attr['phone'];
            $attr = $hold; // shove it back into the attributes array
        }
    } else {

        if (empty($attr['lat']) || empty($attr['lng'])) {
            echo tr_map_errors($tinygmaps_debug, 'malformed_params');
            return false;
        }
    }


    // Don't continue with if we are in admin, sometimes there is a slow response from tr_map_get_place and it always returning in time....
    if(!is_admin() && !empty($attr)){
        // process the infowindow extras
        $tinygmaps_infowindow_extras = ($tinygmaps_infowindowb64 != '') ? base64_decode($tinygmaps_infowindowb64) : '';

        // add any content from the basic to the end in its own div
        $tinygmaps_infowindow_extras = ($tinygmaps_infowindow != '') ? $tinygmaps_infowindow_extras . '<div>' . $tinygmaps_infowindow . '</div>' : '';

        // convert the html special chars
        $tinygmaps_infowindow = htmlspecialchars_decode($tinygmaps_infowindow_extras, ENT_QUOTES);

        // pass it through KSES to scrub it from unwanted markup
        $tinygmaps_infowindow = info_window_sanitize($tinygmaps_infowindow);

        // Assemble the infowindow components
        $tinygmaps_infowindow = get_info_bubble($tinygmaps_icon, $attr['name'], $attr['street'], $attr['city'], $attr['region'], $attr['postcode'], $attr['country'], $attr['phone'], $attr['web'], $tinygmaps_infowindow);

        // for external map link
        $linkAddress = $attr['name'] . ' ' . $attr['street'] . ' ' . $attr['city'] . ' ' . $attr['region'] . ' ' . $attr['postcode'] . ' ' . $attr['country'];
        $linkAddress_url = urlencode($linkAddress);

        // Clean up whitespace and commas
        $remove = array(
            '  ',
            ' ',
            ', ',
            ',',
            '%2C', // commas
            '\t',
            '\t\t',
            '\n',
            '\n\n',
            '\r',
            '\r\r',
            '\0',
            '\0\0',
            '\x0B',
            '\x0B\x0B'
        );
        $linkAddress_url = str_replace($remove, '+', $linkAddress_url);
        $linkAddress_url = str_replace('++', '', $linkAddress_url); // remove double plus from an empty attribute string

        /**
         * We enqueue the js properly and now can pass the vars as globals through wp_localize_script, sweet.
         * We are able to have multiple maps too - nice!
         *
         * Also we have set up retina 2x and 3x resolutions
         * 3x requires google api key
         *
         *
         * http://ottopress.com/2010/passing-parameters-from-php-to-javascripts-in-plugins/
         * https://pippinsplugins.com/use-wp_localize_script-it-is-awesome/
         * http://wordpress.stackexchange.com/questions/91546/pass-custom-fields-values-to-google-maps
         * http://wordpress.stackexchange.com/questions/135821/creating-multiple-wp-localize-script-for-shortcode
         * http://wordpress.stackexchange.com/questions/114807/localize-variable-for-multiple-shortcodes
         *
         * Example js http://codepen.io/anon/pen/zGxxaQ
         */

        // Load all the  variables into array for js global var
        $tinygmaps_init_array = array(
            'z' => (int)$tinygmaps_z,
            'h' => $tinygmaps_h,
            'maptype' => $tinygmaps_maptype,
            'lat' => $attr['lat'],
            'lng' => $attr['lng'],
            'marker' => $tinygmaps_marker,
            'icon' => $tinygmaps_icon,
            'infowindow' => $tinygmaps_infowindow,
            'infowindowdefault' => (boolean)$tinygmaps_infowindowdefault,
            'hidecontrols' => (boolean)$tinygmaps_hidecontrols,
            'scale' => (boolean)$tinygmaps_scalecontrol,
            'scrollwheel' => (boolean)$tinygmaps_scrollwheel,
            'static' => (string)$tinygmaps_static_width,
            'static_h' => (int)$tinygmaps_static_w,
            'static_w' => (int)$tinygmaps_static_h
        );
        // Add them to the page via wp_localize_script
        wp_localize_script('tinygmaps_init', $tinygmaps_map_id . '_loc', $tinygmaps_init_array);
        wp_enqueue_script('tinygmaps_init'); // will appear in footer

        // Build the 'view map on its own' link
        $static_src = "https://maps.google.com/maps/api/staticmap?size=" . $tinygmaps_static_w . "x" . $tinygmaps_static_h . "&zoom=" . $tinygmaps_z;
        $static_src .= "&center=" . $linkAddress_url;
        $static_src .= "&markers=label:m" . "%257C" . "icon:" . $tinygmaps_marker . "%7C" . $linkAddress_url . "&maptype=" . $tinygmaps_maptype;
        $static_src .= "format=jpg";
        $static_src_2x = $static_src . "&scale=2 2x,";
        // output the map wrappers and links
        $markup = '<div class="tnygmps_wrap" id="' . $tinygmaps_map_id . '_wrap">';
        $markup .= '    <div class="tnygmps_canvas" id="' . $tinygmaps_map_id . '" style="width:' . $tinygmaps_w . '; height:auto;">'; //height will be set by js for googlemaps api
        $markup .= '        <img class="tnygmps_staticimg" src="' . $static_src . '" srcset="' . $static_src_2x . '" style="width:' . $tinygmaps_static_w . '; height:' . $tinygmaps_static_h . ';" alt="Google Map for ' . $attr['name'] . '">';
        if ($tinygmaps_infowindow) { // if we have an infowindow
            $markup .= '        <div class="tnygmps_static_bubble well well-small" >' . $tinygmaps_infowindow . '</div>';
        }
        $markup .= '    </div>';
        $markup .= '    <div class="tnygmps_link_wrap"><a href="https://maps.google.com/?q=' . $linkAddress_url . '&t=m"  class="tnygmps_ext_lnk" target="_blank">open map in new window</a></div>';
        //    $markup .= '<pre>' .$linkAddress_url . '</pre>';// troubleshoot url params
        $markup .= '</div>';

        return $markup;
    }
}

/**
 * Sanitize any incoming infowindow markup
 * @var  string [Infowindow string provided by the user to be scrubbed.]
 * @uses  wp_kses() [This function makes sure that only the allowed HTML element names, attribute names and attribute values plus only sane HTML entities will occur in $string.]
 * @link (wp_kses(), http://codex.wordpress.org/Function_Reference/wp_kses)
 * @return string [Returns the sanitised string]
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
            'target' => array(),
            'class' => array()
        ),
        'span' => array(
            'style' => array(),
            'class' => array()
        ),
        'ul' => array(
            'style' => array(),
            'class' => array()
        ),
        'ol' => array(
            'style' => array(),
            'class' => array()
        ),
        'li' => array(
            'style' => array(),
            'class' => array()
        ),
        'br' => array(),
        'hr' => array(),
        'em' => array(
            'style' => array(),
            'class' => array()
        ),
        'strong' => array(
            'style' => array(),
            'class' => array()
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
    $iconStyle                 = (($icon != '') ? 'max-width: 150px; ' : 'max-width: 200px; ');
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
 * This functions takes care of the fetching of location information through Google's places or gecoding apis.
 * Stores the results in a transient for later to increase responsiveness and reduce api requests.
 * Sends all error conditions to tr_map_errors()
 *
 * @param $api_key          | The Client API Key provided by the user.
 * @param $placeID          | The optional Google Places Reference if it has been provided
 * @param string $address   | An optional address sting
 * @param $force_refresh    | Set by the "refresh" short code parm, this flushes any transient values associated with this address hash
 * @param $tinygmaps_debug  | Prints debugging information on to the client end, if the user has admin rights.
 * @return array|bool       | Returns an array of values, or false if errors were encounterd.
 */


function tr_map_get_place($api_key, $placeID, $address = '', $force_refresh, $debug){
    if (empty($api_key)) {
        // notice to admin users that we don't have an api key
        echo tr_map_errors($debug, 'no_api_key');
    }

    // Transient hashes need to be less then 45 char long, in case placeID ever gets that big
    $location = ($placeID) ? substr($placeID, 0, 44) : substr(md5($address), 0, 44);
    // Have we been here before?
    $location = get_transient($location);


    if ($force_refresh || false === $location) {
        // We don't have a transient saved or we want to update it
        if (!empty($placeID) && $api_key) {
            $args = array(
                'placeid' => $placeID,
                'key' => $api_key,
                'sensor' => 'false'
            );
            $url = add_query_arg($args, 'https://maps.googleapis.com/maps/api/place/details/json');

        } elseif (!empty($address)) {
            // we must be using the geocode api

            if ($api_key) {
                $args = array(
                    'address' => urlencode($address),
                    'sensor' => 'false',
                    'key' => $api_key // Google asks for it in the docs, though doesn't expressly require it ... yet
                );
            } else { // No api key
                $args = array(
                    'address' => urlencode($address),
                    'sensor' => 'false'
                );
            }
            $url = add_query_arg($args, 'https://maps.googleapis.com/maps/api/geocode/json');
        } elseif (empty($address))  {
            // we don't have enough information to do the job!
            echo  tr_map_errors($debug, 'malformed_params');
            return false;
        }

        // Get the data from Google's servers
        $response = wp_remote_get($url);

        // Catch any errors from wp_remote_get
        if (is_wp_error($response)) {
            echo tr_map_errors($debug, 'wp_error_get', $response);
            return false;
        }

        // retrieve the data from the response
        $data = wp_remote_retrieve_body($response);

        if (is_wp_error($data)) {
            //Throw any failed results to front end for debugging
            echo tr_map_errors($debug, 'wp_error_data', $data);
            return false;
        }

        if ($response['response']['code'] == 200) {
            // We have received a reply from Google

            // Interpret the response
            $data = json_decode($data);

            //Throw any failed results to front end for debugging
            echo tr_map_errors($debug, $data->status, $data);

            if ($data->status !== 'OK') {
                // We've received a non "OK" Response so lets catch that as well.
                $responseCode = $response['response']['code'];
                echo tr_map_errors($debug, $responseCode, $data);
                return false;

            } elseif ($data->status === 'OK') {
                /* We've received a positive result, populate the variables */

                // Using place_ID vs Geocoding APIs
                $result = ($placeID != '' && $placeID != null) ? $data->result : $data->results[0];
                $coordinates = $result->geometry->location;
                $cache_value['lat'] = (string)$coordinates->lat;
                $cache_value['lng'] = (string)$coordinates->lng;
                // top level items
                $cache_value['name'] = (string)(property_exists($result, 'name')) ? htmlentities((string)$result->name, ENT_QUOTES) : '';
                $cache_value['icon'] = (string)(property_exists($result, 'icon')) ? ((string)$result->icon) : '';
                $cache_value['phone'] = (string)(property_exists($result, 'formatted_phone_number')) ? ($result->formatted_phone_number) : '';
                $cache_value['web'] = (string)(property_exists($result, 'website')) ? ($result->website) : '';
                // Address components
                $premise = (processObject('premise', $result)) ? processObject('premise', $result) . ', ' : '';
                $street_number = processObject('street_number', $result);
                $route = processObject('route', $result);
                $street_number = processObject('street_number', $result);
                $streetAddress = $premise . ' ' . $street_number . ' ' . $route;
                $cache_value['street'] = htmlentities($streetAddress, ENT_QUOTES);
                // City
                $city = (processObject('administrative_area3', $result)) ? processObject('administrative_area3', $result) : '';
                $city = (processObject('locality', $result)) ? processObject('locality', $result) : $city;
                $city = (processObject('sublocality', $result)) ? processObject('sublocality', $result) : $city;
                $city = (processObject('postal_town', $result)) ? processObject('postal_town', $result) : $city;
                $cache_value['city'] = htmlentities($city, ENT_QUOTES);

                // State
                $region = (processObject('locality', $result)) ? processObject('locality', $result) : '';
                $region = (processObject('administrative_area_level_1', $result)) ? processObject('administrative_area_level_1', $result) : $region;
                $regionB = (processObject('administrative_area_level_2', $result)) ? processObject('administrative_area_level_2', $result) : '';
                if (($region == '') && ($regionB != $city))
                    $region = $regionB;
                $cache_value['region'] = htmlentities($region, ENT_QUOTES);
                // Postcode
                $cache_value['postcode'] = htmlentities(processObject('postal_code', $result), ENT_QUOTES);
                // Country
                $cache_value['country'] = htmlentities(processObject('country', $result), ENT_QUOTES);

                //cache address details for 3 months
                set_transient(substr($placeID, 0, 44), $cache_value, 3600 * 24 * 30 * 3);
                $data = $cache_value;
            }
        }
    } else {
        $data = $location;
    }
    return ($data) ? (array)$data : false;
}

/**
 * *********************************************************************
 * Process the returned Geocodeing, or Places API object for address values
 * 
 * @var string $needle [The google address element being searched for]
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
 * @var    array $elem [An array of strings], using & to pass the array by reference so we are not changing the original
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

/**
 * @param $dim
 * @return mixed returns the string without any instances of 'px' or '%'
 */
function remove_px_percent ($dim){
    $dim = str_ireplace('px', '', $dim);
    $dim = str_replace('%', '', $dim);
    return $dim;
}

/**
 * Holds all possible debug reporting and echo's it to the page for logged in admins to use for debugging
 *
 * @param $tinygmaps_debug
 * @param $error
 * @param string $response
 * @return bool
 * @todo when debugging give the results anyway
 */

function tr_map_errors($debug, $error, $response = ''){
    // Only show these notices on the front end if debugging is on, and to those who can edit posts
    if ($debug && current_user_can('edit_posts') && !is_admin()) {
        $message ='';

        switch ($error):
            case 'insufficient_address';
            case 'malformed_params';
                $message .= __("<b>MAP PLUGIN NOTICE: </b> Whoops! You possibly have conflicting input values.<br>Please check that the shortcode is formed properly. Include either a google <em>placeID</em> <b>OR</b> <em>address</em> line, <b>OR</b> explicit <em>lat, lng</em> values <b>WITH</b> explicit location parameters: <em>name, street, city, state, postcode, country, phone and web.</em></br>", 'tinygmaps');
                break;

            case 'wp_error_get';
                $message .= __("<b>MAP PLUGIN NOTICE:</b> We received an error in the URL response: </br>", 'tinygmaps');
                $message .= "<pre>";
                $message .= print_r($response, true);
                $message .= "</pre>";
                break;

            case 'wp_error_data';
                $message .= __("<b>MAP PLUGIN NOTICE:</b> There were problems retrieving the body of the response. ", 'tinygmaps');
                $message .= "<pre>";
                $message .= print_r($response, true);
                $message .= "</pre>";
                break;

            case 'no_api_key';
                $message .= __("<p><b>MAP PLUGIN NOTICE:</b> Looks like you've used a Google place_ID, but right now the Google API key has not been set. Because of this we cannot process the <b>placeid</b> shortcode parameter. Either add more address details or see the documentation on how to get your own api key.</p>", 'tinygmaps');
                break;

            case 'NO_CONNECT';
                $message .= __('<b>MAP PLUGIN NOTICE:</b> Unable to contact Google API, service response: ' . $response, 'tinygmaps');
                break;

            // GOOGLE API RESPONSES
            case 'OK';
            case 'UNKNOWN_ERROR';
            case 'ZERO_RESULTS';
            case 'OVER_QUERY_LIMIT';
            case 'REQUEST_DENIED';
            case 'INVALID_REQUEST';
                $message .= __("<b>MAP PLUGIN NOTICE:</b> Google Response: " . $error, 'tinygmaps');
                $message .= "<pre>";
                $message .= print_r($response, true);
                $message .= "</pre>";
                break;

            case 'NOT_FOUND';
                echo __('<b>MAP PLUGIN NOTICE:</b> Place not found. Usually this is due to an incomplete or corrupted reference string.', 'tinygmaps');
                echo "<pre>";
                $message .= print_r($response, true);
                echo "</pre>";
                break;

            case '';
                $message .= __('<b>MAP PLUGIN NOTICE:</b> Here is the data returned from the query, our error reporter is confused.', 'tinygmaps');
                $message .= "<pre>";
                $message .= print_r($response, true);
                $message .= "</pre>";
                break;

            default:
                $message .= __('<b>MAP PLUGIN NOTICE:</b> Something went wrong while retrieving your map, please ensure you have entered the short code correctly formed.', 'tinygmaps');
                $message .= "<pre>";
                $message .= "The response message code was: " . $error . "</br></br>";
                $message .= print_r($response, true);
                $message .= "</pre>";
        endswitch;

        return $message;
    }
}