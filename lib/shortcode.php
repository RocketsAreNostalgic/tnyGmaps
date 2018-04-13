<?php
namespace OrionRush\TnyGmaps\Shortcode;
use OrionRush\TnyGmaps\Support as Support;
if ( ! defined( 'ABSPATH' ) ) {	die(); }

/**
 * Adds the shortcode to WP, outputs markup and enqueues the js just in time.
 *
 * @wp_hook add_shortcode
 * @uses remomanuallyrcent()
 * @uses array_htmlentities()
 * @uses map_get_place()
 * @uses map_errors()
 * @uses info_window_sanitize()
 * @uses get_info_bubble()
 * @uses wp_localize_script()
 * @uses wp_enqueue_script()
 *
 * @since:   0.0.1
 * @author:  orionrush
 *
 * @param $attr
 *
 * @return string
 *
 * // Shortcode attributes array
 * @type string $z                  | Map zoom level 1 - 22
 * @type string $w                  | Map width in pixels or percent
 * @type string $h                  | Map height in pixels or percent
 * @type string $maptype            | Map type: ROADMAP, SATELLITE, HYBRID, TERRAIN
 * @type string $lat                | Location latitude
 * @type string $lng                | Location longitude
 * @type string $placeID            | A Google Places API reference if provided one.
 * @type string $address            | An address to the location as a string
 * @type string $name               | Business or location name
 * @type string $street             | Building number and street name
 * @type string $city               | City Name
 * @type string $region             | State or Provence
 * @type string $postcode           | Zip or regional post code
 * @type string $country            | Country code, long or short
 * @type string $web                | URL inclusive of http://
 * @type string $phone              | Phone Number
 * @type string $icon               | An image from the maps api for that location
 * @type string $marker             | A url full or relative to the plugin (see example) to the icon png
 * @type boolean $default_marker    | (true : false) Use the icon chosen in the admin preferences, default 'false'. Overrides $marker. TODO: implement.
 * @type string $infowindowdefault  | (yes : no) Show the infowindow on page load, or keep it hidden until the map icon is clicked.
 * @type string $infowindow         | Additional contents of the infowindow, but must be text only without any markup.
 * @type string $infowindowb64      | Additional contents of the infowindow base 64 encoded so complex additional markup won't break the reading of the shortcode by WordPress.
 * @type string $hidecontrols       | (true : false) Hides the zoom, street view and other controls
 * @type boolean $scale             | (true : false) Is the map scale drawn?
 * @type boolean $attr['scrollwheel']       | (true : false) Will the map zoom react to mouse scrollwheel?
 * @type string $static             | DOM width for when a static map should be drawn instead of a dynamic maps for small screens, empty or '0' will indicate static map is not drawn
 * @type int $static_w              | Width of static map in pixels
 * @type int $static_h              | Height of static map in pixels
 * @type boolean $refresh           | (true : false) Will flush any transient data from being cashed for a given location (good for testing results)
 * @type boolean $debug             | (true : false) Will render the return values from the Google Maps API object for debugging.
 *
 */

function map_me( $attr ) {
	$map_errors = '';
	wp_enqueue_style('tnygmaps_styles');
	// Lets enqueue the scripts only if the shortcode has been added
	wp_enqueue_script( 'googelmaps_js' );
	wp_enqueue_script( 'tnygmaps_init' );

	//A check to see if the constant GOOGLE_API_KEY has been set.
	$api_key = ( defined( 'GOOGLE_API_KEY' ) ) ? constant( 'GOOGLE_API_KEY' ) : false;

	// generate a  13 character unique id  so multiple maps may be used on the same page
	$map_id = uniqid( 'tnygmps_' );

	// default atts
	$attr = shortcode_atts( array(
		'z'                 => '1',
		'w'                 => '100%',
		'h'                 => '500px',
		'maptype'           => 'ROADMAP',
		'lat'               => '',
		'lng'               => '',
		'placeid'           => '',
		'address'           => '',
		'name'              => '',
		'street'            => '',
		'city'              => '',
		'region'            => '',
		'postcode'          => '',
		'country'           => '',
		'web'               => '',
		'phone'             => '',
		'marker'            => '',
		'default_marker'    => '',
		'icon'              => '',
		'infowindow'        => '',
		'infowindowb64'     => '',
		'infowindowdefault' => 'yes',
		'hidecontrols'      => 'false',
		'scale'             => 'false',
		'scrollwheel'       => 'false',
		'static_DOM_width'  => '',
		'static_w'          => '500',
		'static_h'          => '500',
		'refresh'           => 'false', // executes if present and not equal to false
		'debug'             => 'false'  // executes if present and not equal to false
	), $attr );

	// Sanitise the incoming values
	$attr =  sanitise_atributes_array ($attr);

	$attr_place = "";


	// setup the incoming values with either cached API response or assemble query and get response
	if ( ! empty( $attr['placeid'] ) ) {
		// Here we have a place ref so get/set transient with fetched values
		// strip address out as we will want to refer to google's cashed values instead
		$attr['address'] = null;
		// here we have a place_ID so get/set transient with fetched values
		$attr_place = map_get_place( $api_key, $attr['placeid'], null, $attr['refresh'], $attr['debug'] );
		if (array_key_exists('errors', $attr_place)){
			$map_errors .= $attr_place['errors'];

			return $map_errors;
		}

	} elseif ( empty( $attr['placeid'] ) && ( empty( $attr['lat'] ) || empty( $attr['lng'] ) ) && ! empty( $attr['address'] ) ) {

		// here we have a address so get/set transient with fetched values
		$attr_place = map_get_place( $api_key, null, $attr['address'], $attr['refresh'], $attr['debug'] );

		if (array_key_exists('errors', $attr_place)){
			$map_errors .= $attr_place['errors'];
			return $map_errors;
		}

	} elseif ( empty( $attr['placeid'] ) && ( ! empty( $attr['lat'] ) && ! empty( $attr['lng'] ) ) && empty( $attr['address'] ) ) {
		// here we have lat and lng so we will gather any other individual params that are set.
		// no call to map_get_place as we will not cache these values in a transient, as the user will have provided as much material as possible.

	} elseif ( empty( $attr['placeid'] ) && ( empty( $attr['lat'] ) || empty( $attr['lng'] ) ) && empty( $attr['address'] ) ) {

		// here we have missing lat lags, and no unified address strings, may have enough address components so build the place query from these attr
		$attr['address'] = $attr['street'] . ', ' . $attr['city'] . ', ' . $attr['region'] . ', ' . $attr['postcode'] . '+' . $attr['country'];
		$attr['address'] = cleanCommasAddressString ($attr['address']);

		if ( $attr['address'] === null ) {
			$map_errors .= map_errors( $attr['debug'], 'insufficient_address' );
		} else {
			// here we don't have an place_ID or address, but we've constructed an address string from the other
			$attr_place = map_get_place( $api_key, null, $attr['address'], $attr['refresh'], $attr['debug'] );

			if (array_key_exists('errors', $attr_place) ){
				$map_errors .= $attr_place['errors'];
				$map_errors .=  map_errors( $attr['debug'], 'insufficient_address' );

				return $map_errors;

			}
		}
	} else {

		if ( empty( $attr['lat'] ) || empty( $attr['lng'] ) ) {
			$map_errors .= map_errors( $attr['debug'], 'malformed_params' );

			return $map_errors;
		}
	}
	if (is_array($attr_place)){
		// Combine the two arrays into one
		$attr = array_replace($attr ,$attr_place);
	}

	// Don't continue if we are not in the admin. Sometimes there is a slow response from map_get_place and it doesn't always returning in time....
	if ( ! is_admin() && ! empty( $attr ) ) {

		// process the infowindow extras
		$infowindow_extras = '';
		if ( $attr['infowindowb64'] != 'bnVsbA=='){ // base64 for null
			$infowindow_extras = ( ! empty( $attr['infowindowb64'] ) ) ? base64_decode( $attr['infowindowb64'] ) : '';

			// add any content from the basic infowindow attr to the end in its own div
			$infowindow_extras = ( ! empty( $attr['infowindow']) ) ? $infowindow_extras . '<div>' . $attr['infowindow']. '</div>' : $infowindow_extras;
		}
		// convert the html special chars
		$attr['infowindow']= htmlspecialchars_decode( $infowindow_extras, ENT_QUOTES );
		// pass it through KSES to scrub it from unwanted markup
		$attr['infowindow'] = info_window_sanitize( $attr['infowindow'] );

		// Assemble the infowindow components
		$attr['infowindow'] = get_info_bubble( $attr['icon'], $attr['name'], $attr['street'], $attr['city'], $attr['region'], $attr['postcode'], $attr['country'], $attr['phone'], $attr['web'], $attr['infowindow'] );

		// for external map link
		$linkAddress     = $attr['name'] . ' ' . $attr['street'] . ' ' . $attr['city'] . ' ' . $attr['region'] . ' ' . $attr['postcode'] . ' ' . $attr['country'];
		$linkAddress_url = cleanLinkAddress_url($linkAddress);

		enqueueJsGlobals ($map_id, $attr);

		// Build the 'view map on its own' link
		$static_src = "https://maps.google.com/maps/api/staticmap?key=" . GOOGLE_API_KEY . "&size=" . $attr['static_w'] . "x" . $attr['static_h'] . "&zoom=" . $attr['z'];
		$static_src .= "&center=" . $linkAddress_url;
		$static_src .= "&markers=label:m" . "%257C" . "icon:" . $attr['marker'] . "%7C" . $linkAddress_url . "&maptype=" . $attr['maptype'];
		$static_src .= "format=jpg";
		$static_src_2x = $static_src . "&scale=2 2x,";
		// output the map wrappers and links
		$markup = '<div class="tnygmps_wrap" id="' . $map_id . '_wrap">';
		$markup .= '    <div class="tnygmps_canvas" id="' . $map_id . '" style="width:' . $attr['static_w'] . '; height:' . $attr['static_h'] . ';">'; //height will be reset by js for googlemaps api

		// Only show this if the plugin option is enabled
		if ($attr['static_DOM_width'] !== '0' ){
			$alt_text = __("Google Map for", "orionrush-tyngmaps") . ' ' . $attr['name'];
			$markup .= '        <img class="tnygmps_staticimg" src="' . $static_src . '" srcset="' . $static_src_2x . '" style="width:' . $attr['static_w'] . '; height:' . $attr['static_h'] . ';" alt="' . __("Google Map for", 'orionrush-tyngmaps') . ' ' . $attr['name'] . '">';

			if ( $attr['infowindow'] ) { // if we have an infowindow
				$markup .= '        <div class="tnygmps_static_bubble well well-small" >' . wp_specialchars_decode($attr['infowindow']) . '</div>';
			}
		} else {
			// Base 64 Encoded 1x1 px transparent gif as placeholder.
			$markup .= '        <img class="tnygmps_staticimg" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" style="width:' . $attr['static_w'] . '; height:' . $attr['static_h'] . ';">';
		}
		$markup .= '    </div>';
		$markup .= '    <div class="tnygmps_link_wrap"><a href="https://maps.google.com/?q=' . $linkAddress_url . '&t=m"  class="tnygmps_ext_lnk" target="_blank">' . Support\openMapInNewWin() . '</a></div>';
		$markup .= '</div>';

		if ($map_errors){

			return $map_errors;
		}
		return $markup;
	}
}
add_shortcode( 'tnygmaps',  __NAMESPACE__ . '\\map_me' );
add_shortcode( 'tinygmaps', __NAMESPACE__ . '\\map_me' );   // Legacy
add_shortcode( 'TINYGMAPS', __NAMESPACE__ . '\\map_me' );   // Legacy
add_shortcode( 'TNYGMAPS',  __NAMESPACE__ . '\\map_me' );   // Legacy

/**
 * Pass the vars as globals through wp_localize_script, each map recieves it's own ID string
 *
 * @since:   0.0.4
 *
 * @author: orionrush
 * @param $map_id
 * @param $attr
 *
 * TODO: Set up retina 2x and 3x resolutions
 */
function enqueueJsGlobals ($map_id, $attr) {

//	 http://ottopress.com/2010/passing-parameters-from-php-to-javascripts-in-plugins/
//	 https://pippinsplugins.com/use-wp_localize_script-it-is-awesome/
//	 http://wordpress.stackexchange.com/questions/91546/pass-custom-fields-values-to-google-maps
//	 http://wordpress.stackexchange.com/questions/135821/creating-multiple-wp-localize-script-for-shortcode
//	 http://wordpress.stackexchange.com/questions/114807/localize-variable-for-multiple-shortcodes
//	 Example: http://codepen.io/anon/pen/zGxxaQ


	// Load all the  variables into array for js global var
	$init_array = array(
		'z'                 =>	$attr['z'],
		'w'                 =>	$attr['w'],
		'h'                 =>	$attr['h'],
		'maptype'           =>	$attr['maptype'],
		'lat'               =>	$attr['lat'],
		'lng'               =>	$attr['lng'],
		'marker'            =>	$attr['marker'],
		'default_marker'    =>	$attr['default_marker'],
		'infowindow'        =>	$attr['infowindow'],
		'infowindowb64'     =>	$attr['infowindowb64'],
		'infowindowdefault' =>	$attr['infowindowdefault'],
		'hidecontrols'      =>	$attr['hidecontrols'],
		'scale'             =>	$attr['scale'],
		'scrollwheel'       =>	$attr['scrollwheel'],
		'static_DOM_width'  =>	$attr['static_DOM_width'],
		'static_w'          =>	$attr['static_w'],
		'static_h'          =>	$attr['static_h'],
		'debug'             =>	$attr['debug'],
	);

	// Add the params to the page as js global vars via wp_localize_script
	wp_localize_script( 'tnygmaps_init', $map_id . '_loc',  $init_array );
	wp_enqueue_script( 'tnygmaps_init' ); // will appear in footer
}


/**
 * Cleans up the address string by removing double commas and whitespace.
 *
 * @since:   0.0.4
 * @author: orionrush
 *
 * @param $address
 *
 * @return mixed|string
 */
function cleanCommasAddressString ($address){
	$string = array(
		', , ',
		', , , ',
		', , , , ',
		', , , , , '
	);
	$address = str_replace($string, '  ',  $address); // trim any double commas signs that may be in the string
	$address = trim( $address, " \t\n\r\0\x0B\," ); // clean any leading whitespace or commas
	$address = (( $address == '' || $address == "," ) ? null : $address ); // Set it or forget it
	return $address;

}
/**
 * Cleans up the Google map address link by stripping disallowed characters.
 *
 * @since:   0.0.4
 * @author: orionrush
 *
 * @param $url
 *
 * @return mixed
 */
function cleanLinkAddress_url($url){
	// Clean up whitespace, commas, new lines etc.
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

	// replace disallowed characters with '+'
	$url = str_replace( $remove, '+', $url );
	// Remove any inadvertent double '++'
	$url = str_replace( '++', '', $url );
	// Encode it just in case
	$url = urlencode( $url );
	return $url;
}

/**
 * Sanitises the attributes array
 *
 * @since:   0.0.4
 * @author: orionrush
 *
 * @param $attr
 *
 * @return array
 */
function sanitise_atributes_array ($attr){

	// Override with global debugging values set in options page.
	if(get_option('tnygmaps_debug')){
		$attr['debug'] = true;
	}

	array_walk( $attr, create_function( '&$val', '$val = trim($val);' ) ); //trim white space
	// Sanitize array elements whole stock with htmlentities encoding entities and double and single quotes
	$attr                       = array_htmlentities( $attr );
	// load map params into variables
	$attr['z']                  = filter_var( $attr['z'], FILTER_SANITIZE_NUMBER_INT, array('default' => '11') );
	// make sure h&w have at least px values if nothing is specified
	$attr['w']                  = ( ( substr( $attr['w'], - 2 ) != 'px' ) && ( substr( $attr['w'], - 1 ) != '%' ) ) ? $attr['w'] . 'px' : $attr['w'];
	$attr['h']                  = ( ( substr( $attr['h'], - 2 ) != 'px' ) && ( substr( $attr['h'], - 1 ) != '%' ) ) ? $attr['h'] . 'px' : $attr['h'];
	// Sanitize map type, default to ROADMAP
	$maptypes = array ('ROADMAP', 'SATELLITE',  'HYBRID', 'TERRAIN');
	if (!in_array($attr['maptype'], $maptypes)){
		$attr['maptype'] = 'ROADMAP';
	}
	$attr['lat']                = (is_numeric($attr['lat']) ? $attr['lat'] : "");
	$attr['lng']                = (is_numeric($attr['lng']) ? $attr['lng'] : "");
	$attr['placeid']            = filter_var($attr['placeid'], FILTER_SANITIZE_STRIPPED);
	$attr['address']            = filter_var($attr['address'], FILTER_SANITIZE_STRIPPED);
	$attr['name']               = filter_var($attr['name'], FILTER_SANITIZE_STRIPPED);
	$attr['street']             = filter_var($attr['street'], FILTER_SANITIZE_STRIPPED);
	$attr['city']               = filter_var($attr['city'], FILTER_SANITIZE_STRIPPED);
	$attr['region']             = filter_var($attr['region'], FILTER_SANITIZE_STRIPPED);
	$attr['postcode']           = filter_var($attr['postcode'], FILTER_SANITIZE_STRIPPED);
	$attr['country']            = filter_var($attr['country'], FILTER_SANITIZE_STRIPPED);
	$attr['web']                = filter_var($attr['web'], FILTER_SANITIZE_STRIPPED);
	$attr['phone']              = filter_var($attr['phone'], FILTER_SANITIZE_STRIPPED);
	$attr['marker']             = ( filter_var( $attr['marker'], FILTER_VALIDATE_URL ) != false ) ? $attr['marker'] : null;
	$attr['default_marker']     = ( $attr['default_marker'] == 'true' ) ? true : false;
	// Populate the default marker image
	$default_marker_option  = ( filter_var( get_option( 'tnygmaps_custom_icon' ), FILTER_VALIDATE_URL ) );
	// Override $attr['marker'] with the default option if available.
	if ($attr['default_marker'] && $default_marker_option ) {
		$name = Support\gMapsDefultIconName();
		if ($default_marker_option != $name) {
			$attr['marker']     = $default_marker_option;
		} else {
			$attr['marker']     = '';
		}
	}
	$attr['icon']               = ( filter_var( $attr['icon'], FILTER_VALIDATE_URL ) != false ) ? $attr['icon'] : null;
	//  $attr['infowindow'];    // sanitized in $infowindow_extras
	//  $attr['infowindowb64']; // sanitized in $infowindow_extras
	$attr['infowindowdefault']  = ( $attr['infowindowdefault'] == 'true' || $attr['infowindowdefault'] == 'yes' ) ? 'yes' : 'no';
	$attr['hidecontrols']       = ( $attr['hidecontrols'] == 'true' ) ? true : false;
	$attr['scale']              = ( $attr['scale'] == 'true' ) ? true : false;
	$attr['scrollwheel']        = ( $attr['scrollwheel'] == 'true' ) ? true : false;
	// See if static maps have been enabled. If not, setting 'static_DOM_width' to 0 will disable the feature
	$attr['static_DOM_width']   = remove_px_percent( $attr['static_DOM_width'] );
	if (!get_option('tnygmaps_mobile')) {
		$attr['static_DOM_width'] = '0';
	}
	// If it hasn't been specified, test it against the global setting.
	if (!$attr['static_DOM_width']) {
		$attr['static_DOM_width'] = remove_px_percent(get_option('tnygmaps_mobile_width'));
		// If no option set for some reason, use baked in constant
		if (!$attr['static_DOM_width']){
			$attr['static_DOM_width'] = TNYGMAPS_STATIC_DOM_WIDTH;
		}
	}
	$attr['static_w']           = remove_px_percent( $attr['static_w'] );
	$attr['static_h']           = remove_px_percent( $attr['static_h'] );
	$attr['refresh']            = ( ( $attr['refresh'] != 'false' ) ? true : false );
	$attr['debug']              = ( ( $attr['debug'] != 'false' ) ? true : false );

	return (array) $attr;
}

 * Register scripts
 *
 * @wp_hook: init
 * @since:   0.0.2
 * @author: orionrush
 *
 */
function register_scripts() {

	wp_register_style('tnygmaps_styles', TNYGMAPS_URL . 'assets/css/tnygmaps_frontend.css');

	wp_register_script( 'googelmaps_js', 'http://maps.google.com/maps/api/js?libraries=places&key=' . GOOGLE_API_KEY, null, null, 'true' );
	wp_register_script( 'tnygmaps_init', TNYGMAPS_URL . 'assets/js/tnygmaps.min.js', array(
		'googelmaps_js',
		'jquery'
	), '0.0.1', 'true' );
}

add_action( 'init', __NAMESPACE__ . '\\register_scripts' );


/**
 * Sanitize any incoming infowindow markup
 *
 * @uses  wp_kses() http://codex.wordpress.org/Function_Reference/wp_kses
 *
 * @since:   0.0.2
 * @author:  orionrush
 *
 * @param  string | Infowindow string provided by the user to be scrubbed.
 * @return string | Returns the sanitised string
 *
 */
function info_window_sanitize( $string ) {

	str_replace( "'", "", $string ); // replace single quotes with double

	$allowed_html      = array(
		'p'      => array(
			'style' => array()
		),
		'a'      => array(
			'style'  => array(),
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
			'class'  => array()
		),
		'span'   => array(
			'style' => array(),
			'class' => array()
		),
		'ul'     => array(
			'style' => array(),
			'class' => array()
		),
		'ol'     => array(
			'style' => array(),
			'class' => array()
		),
		'li'     => array(
			'style' => array(),
			'class' => array()
		),
		'br'     => array(),
		'hr'     => array(),
		'em'     => array(
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

	return wp_kses( $string, $allowed_html, $allowed_protocols );
}


/**
 * Assemble the infowindow interior using seed values gathered either from  the user
 * via shortcode or return vals from the Google API call.
 *
 * @since:   0.0.2
 * @author:  orionrush
 *
 * @param [string] $icon
 * @param [string] $name
 * @param [string] $street
 * @param [string] $city
 * @param [string] $state
 * @param [string] $post
 * @param [string] $country
 * @param [string] $phone
 * @param [string] $web
 * @param [string] $info
 *
 * @return [string] // The assembled markup for the interior of the infowindow.
 */
function get_info_bubble( $icon, $name, $street, $city, $state, $post, $country, $phone, $web, $info ) {
	// Data sanitise
	$icon    = esc_url( $icon );
	$name    = esc_textarea( $name );
	$street  = esc_textarea( $street );
	$city    = esc_textarea( $city );
	$state   = esc_textarea( $state );
	$post    = esc_textarea( $post );
	$country = esc_textarea( $country );
	$phone   = esc_textarea( $phone );
	$web     = esc_url( $web );
	$info    = esc_textarea( $info );

	// Build the output
	$infowindowPlace = '<div class="marker inside"  >';
	$infowindowPlace .= '<b>' . $name . '</b>';
	$infowindowPlace .= '<table>';
	$infowindowPlace .= '<tbody>';
	$infowindowPlace .= '<tr>';
	$infowindowPlace .= '<td>';
	$iconStyle = ( ( $icon != '' ) ? 'max-width: 150px; ' : 'max-width: 200px; ' );
	$infowindowPlace .= '<div class="" style="' . $iconStyle . '" >';
	$infowindowPlace .= ( $street != null && $street != '' && $street != '' ) ? '<div>' . $street . '</div>' : '';
	$infowindowPlace .= ( $city != null && $city != '' && $city !== '' ) ? '<div>' . $city . ', ' : '<div>';
	$infowindowPlace .= ( $state != null && $state != '' && $state != '' ) ? $state . '</div>' : '</div>';
	$infowindowPlace .= ( $post != null && $post != '' && $post != '' ) ? '<div>' . $post . '</div>' : '</div>';
	$infowindowPlace .= ( $country != null && $country != '' && $country != '' ) ? '<div>' . $country . '</div>' : '</div>';
	$infowindowPlace .= ( $phone != null && $phone != '' && $phone != '' ) ? '<div>' . $phone . '</div>' : '';
	$infowindowPlace .= ( $web != null && $web != '' && $web != '' ) ? '<div style="max-width: 100%; white-space: nowrap; width: 100%; overflow: hidden;  -o-text-overflow: ellipsis;  text-overflow: ellipsis;"><a href="' . $web . '" class="gmap_link" target="_blank" style="">' . $web . '</a></div>' : '';
	$infowindowPlace .= '</div>';
	$infowindowPlace .= '</td>';
	$infowindowPlace .= '<td>';
	$infowindowPlace .= ( $icon != null && $icon != '' ) ? '<img src="' . $icon . '" class="marker icon" style="margin: 0 5px 15px 5px; width: 60px; height: auto; " />' : '';
	$infowindowPlace .= '</td>';
	$infowindowPlace .= '</tr>';
	$infowindowPlace .= '</tbody>';
	$infowindowPlace .= '</table>';
	$infowindowPlace .= ( $info != null && $info != '' ) ? '<div class="marker extras" style="border-top: 1px dotted #949494; margin-top: 5px; max-width: 265px; min-height: 40px; overflow: hidden; white-space: pre-wrap;" >' . $info . '</div>' : '';
	$infowindowPlace .= '</div>';

	return $infowindowPlace;
}


/**
 * This functions takes care of the fetching of location information through Google's places or gecoding apis.
 * Stores the results in a transient for later to increase responsiveness and reduce api requests.
 * Sends all error conditions to map_errors()
 *
 * @uses map_errors
 * @uses processAddressObject
 *
 * @since:   0.0.2
 * @author:  orionrush
 *
 * @param $api_key          | The Client API Key provided by the user.
 * @param $placeID          | The optional Google Places Reference if it has been provided
 * @param string $address   | An optional address sting
 * @param $force_refresh    | Set by the "refresh" short code parm, this flushes any transient values associated with this address hash
 * @param $debug            | Prints debugging information on to the client end, if the user has admin rights.
 *
 * @return array|bool       | Returns an array of values, or false if errors were encountered.
 */

function map_get_place( $api_key, $placeID, $address = '', $force_refresh, $debug ) {
	$map_errors = '';
	if ( empty( $api_key ) ) {
		// notice to admin users that we don't have an api key
		$map_errors .=  map_errors( $debug, 'no_api_key' );

		return false;
	}

	// We are using our placeID for as our transient hash,
	// however transient hashes need to be less then 45 char long, so we trim them,
	// just in case Google's placeID ever get longer then this
	$location = ( $placeID ) ? substr( $placeID, 0, 44 ) : substr( md5( $address ), 0, 44 );
	// Have we been here before?
	$location = get_transient( $location );

	if ( $force_refresh || false === $location ) {
		// We don't have a transient saved or we want to update it
		if ( ! empty( $placeID ) && $api_key ) {
			$args = array(
				'placeid' => $placeID,
				'key'     => $api_key,
				'sensor'  => 'false'
			);
			$url  = add_query_arg( $args, 'https://maps.googleapis.com/maps/api/place/details/json' );

		} elseif ( ! empty( $address ) ) {
			// we must be using the geocode api

			if ( $api_key ) {
				$args = array(
					'address' => urlencode( $address ),
					'sensor'  => 'false',
					'key'     => $api_key
				);
			} else { // No api key
				$args = array(
					'address' => urlencode( $address ),
					'sensor'  => 'false'
				);
			}
			$url = add_query_arg( $args, 'https://maps.googleapis.com/maps/api/geocode/json' );
		} elseif ( empty( $address ) ) {
			Support\write_log('we don\'t have enough information to finish the job!');
			$map_errors .=  map_errors( $debug, 'malformed_params' );
		}

		// Get the data from Google's servers
		$response = wp_remote_get( $url );

		// Catch any errors from wp_remote_get
		if ( is_wp_error( $response ) ) {
			Support\write_log('Error in wp_remote_get');
			$map_errors .=  map_errors( $debug, 'wp_error_get', $response );
		}

		// retrieve the data from the response
		$data = wp_remote_retrieve_body( $response );

		if ( is_wp_error( $data ) ) {
			Support\write_log('Failed results from wp_remote_retrieve_body');
			$map_errors .= map_errors( $debug, 'wp_error_data', $data );
		}

		if ( $response['response']['code'] == 200 ) {
			// We have received a reply from Google

			// Interpret the response
			$data = json_decode( $data );
			Support\write_log('Google Replied: 200');

			if ( $data->status !== 'OK' ) {
				Support\write_log('We\'ve received a non "OK" response from Google');
				$responseCode = $response['response']['code'];
				$map_errors .= map_errors( $debug, $responseCode, $data );

			} elseif ( $data->status === 'OK' ) {
				/* We've received a positive result, populate the variables */

				// Using place_ID vs Geocoding APIs
				$result             = ( $placeID != '' && $placeID != null ) ? $data->result : $data->results[0];
				$coordinates        = $result->geometry->location;
				$cache_value['lat'] = (string) $coordinates->lat;
				$cache_value['lng'] = (string) $coordinates->lng;
				// top level items
				$cache_value['name']  = (string) ( property_exists( $result, 'name' ) ) ? htmlentities( (string) $result->name, ENT_QUOTES ) : '';
				$cache_value['icon']  = (string) ( property_exists( $result, 'icon' ) ) ? ( (string) $result->icon ) : '';
				$cache_value['phone'] = (string) ( property_exists( $result, 'formatted_phone_number' ) ) ? ( $result->formatted_phone_number ) : '';
				$cache_value['web']   = (string) ( property_exists( $result, 'website' ) ) ? ( esc_url( $result->website ) ) : '';
				// Address components
				$premise               = ( processAddressObject( 'premise', $result ) ) ? processAddressObject( 'premise', $result ) . ', ' : '';
				$street_number         = processAddressObject( 'street_number', $result );
				$route                 = processAddressObject( 'route', $result );
				$street_number         = processAddressObject( 'street_number', $result );
				$streetAddress         = $premise . ' ' . $street_number . ' ' . $route;
				$cache_value['street'] = htmlentities( $streetAddress, ENT_QUOTES );
				// City
				$city                = ( processAddressObject( 'administrative_area3', $result ) ) ? processAddressObject( 'administrative_area3', $result ) : '';
				$city                = ( processAddressObject( 'locality', $result ) ) ? processAddressObject( 'locality', $result ) : $city;
				$city                = ( processAddressObject( 'sublocality', $result ) ) ? processAddressObject( 'sublocality', $result ) : $city;
				$city                = ( processAddressObject( 'postal_town', $result ) ) ? processAddressObject( 'postal_town', $result ) : $city;
				$cache_value['city'] = htmlentities( $city, ENT_QUOTES );

				// State
				$region  = ( processAddressObject( 'locality', $result ) ) ? processAddressObject( 'locality', $result ) : '';
				$region  = ( processAddressObject( 'administrative_area_level_1', $result ) ) ? processAddressObject( 'administrative_area_level_1', $result ) : $region;
				$regionB = ( processAddressObject( 'administrative_area_level_2', $result ) ) ? processAddressObject( 'administrative_area_level_2', $result ) : '';
				if ( ( $region == '' ) && ( $regionB != $city ) ) {
					$region = $regionB;
				}
				$cache_value['region'] = htmlentities( $region, ENT_QUOTES );
				// Postcode
				$cache_value['postcode'] = htmlentities( processAddressObject( 'postal_code', $result ), ENT_QUOTES );
				// Country
				$cache_value['country'] = htmlentities( processAddressObject( 'country', $result ), ENT_QUOTES );

				//cache address or place details for default time

				$time = Support\getMapTransientExpiry();
				set_transient( substr( $placeID, 0, 44 ), $cache_value, $time );
				$data = $cache_value;
			}
		}
	} else {
		$data = $location;
	}

	if ($map_errors){

		$errors = [
			"errors" => $map_errors,
		];
		return $errors;
	}
	return ( $data ) ? (array) $data : false;
}

/**
 * Process the returned Geocodeing or Places API object for address values
 *
 * @since:   0.0.2
 * @author:  orionrush
 *
 * @param string $needle  | The google address element being searched for
 * @param array $haystack | The Google place results as an array
 * @return string         | The information held in the array at the "needle" position sought after, such as "city" or "postcode"
 *
 */
function processAddressObject( $needle, $haystack ) {
	foreach ( $haystack->address_components as $address ) {
		// Repeat the following for each desired type
		if ( in_array( $needle, $address->types ) ) {
			return trim( $address->long_name );
		}
	}

	return null;
}

/**
 * Iterates through and performs htmlentites ENT_QUOTES on all elements of an array
 *
 * @uses array_htmlentities (itself)
 * @since:   0.0.2
 * @author:  orionrush
 *
 * @param  array $elem  | An array of strings. We use & to pass the array by reference, so we don't want to change the original values
 * @return array        | An array of strings with all quotes encoded
 *
 */
function array_htmlentities( &$elem ) {
	if ( ! is_array( $elem ) ) {
		$elem = htmlentities( $elem, ENT_QUOTES );
	} else {
		foreach ( $elem as $key => $value ) {
			$elem[ $key ] = array_htmlentities( $value );
		}
	}

	return $elem;
}

/**
 * Takes a string and returns a number without instances of 'px' or '%'
 *
 * @since:   0.0.2
 * @author:  orionrush
 *
 * @param $dim
 * @return numeric
 */
function remove_px_percent( $dim ) {

	$dim = str_ireplace( 'px', '', $dim );
	$dim = str_replace( '%', '', $dim );

	if ( ! is_numeric( $dim ) ) {
		$dim = null;
	}

	return $dim;

}

/**
 * All possible debug reports, Echos results to front end map pages for logged-in post editors.
 *
 * @since:   0.0.2
 * @author:  orionrush
 *
 * @param $debug
 * @param $error
 * @param string $response
 *
 * @return bool
 */

function map_errors( $debug, $error, $response = '' ) {
	// Only show these notices on the front end if debugging is on, and only to those who can edit posts
	if ( $debug && current_user_can( 'administrator' ) && ! is_admin() ) {

		$headline = __('MAP PLUGIN NOTICE: ', 'orionrush-tnygmaps' );
		$message = '';

		switch ( $error ):
			case 'insufficient_address';
			case 'malformed_params';
			$message .= sprintf('<p><b>%s</b><br/> %s</p><p>%s<b>%s</b><em>%s</em><b>%s</b><em>%s</em> %s <b>%s %s</b> <em>%s</em> %s <b>%s %s</b> <em>%s</em></p>',
				$headline,
				__('Whoops! You have conflicting or insufficient address values.', 'orionrush-tnygmaps' ),
				__('Please check that the shortcode is formed properly. Include either a ', 'orionrush-tnygmaps' ),
				__('Google ', 'orionrush-tnygmaps' ),
				__('placeID ', 'orionrush-tnygmaps' ),
				__('OR ', 'orionrush-tnygmaps' ),
				__('address ', 'orionrush-tnygmaps' ),
				__('line, ', 'orionrush-tnygmaps' ),
				__('OR ', 'orionrush-tnygmaps' ),
				__('explicit, ', 'orionrush-tnygmaps' ),
				__('lat, lng, ', 'orionrush-tnygmaps' ),
				__('values ', 'orionrush-tnygmaps' ),
				__('OR ', 'orionrush-tnygmaps' ),
				__('explicit location parameters: ', 'orionrush-tnygmaps' ),
				__('name, street, city, state, postcode, country.', 'orionrush-tnygmaps' )
			);
			$message .= "
				";
			break;
			case 'wp_error_get';
				$message .= sprintf('<p><b>%s</b><br/> %s</p>',
					$headline,
					__('We received an error in the URL response: ', 'orionrush-tnygmaps' )
				);
				$message .= "<pre>";
				$message .= print_r( $response, true );
				$message .= "</pre>
				";
				break;

			case 'wp_error_data';
				$message .= sprintf('<p><b>%s</b><br/>%s</p>',
					$headline,
					__('There were problems retrieving the body of the response.', 'orionrush-tnygmaps' )
				);
				$message .= "<pre>";
				$message .= print_r( $response, true );
				$message .= "</pre>
				";
				break;

			case 'no_api_key';
				$message .= sprintf('<p><b>%s</b><br/>%s <em>%s</em> %s</p>',
					$headline,
					__('Looks like you\'ve used a Google place_ID, but you don\'t have Google API key . Because of this we cannot process the, ', 'orionrush-tnygmaps' ),
					__('placeid', 'orionrush-tnygmaps' ),
					__('shortcode parameter. Either add address details manually, or see the documentation on how to get your own api key.', 'orionrush-tnygmaps' )
				);
				break;

			case 'NO_CONNECT';
				$message .= sprintf(
					'<p><b>%s</b>%s</p>',
					$headline,
					__('Unable to contact Google API, service response: ', 'orionrush-tnygmaps' )
				);
				$message .= "<pre>";
				$message .= print_r( $response, true );
				$message .= "</pre>
				";
				break;

			// GOOGLE API RESPONSES
			case 'OK';
			case 'UNKNOWN_ERROR';
			case 'ZERO_RESULTS';
			case 'OVER_QUERY_LIMIT';
			case 'REQUEST_DENIED';
			case 'INVALID_REQUEST';
				$message .= sprintf(
					'<p><b>%s</b><br/>%s</p>',
					$headline,
					__('Google Response: ', 'orionrush-tnygmaps' ) . $error
				);
				$message .= "<pre>";
				$message .= print_r( $response, true );
				$message .= "</pre>
				";
				break;

			case 'NOT_FOUND';
				$message .= sprintf(
					'<p><b>%s</b>%s</p>',
					$headline,
					__('Place not found. Usually this is due to an incomplete or corrupted reference string.', 'orionrush-tnygmaps' )
				);
				$message .= "<pre>";
				$message .= print_r( $response, true );
				$message .= "</pre>
				";
				break;

			case '';
				$message .= sprintf(
					'<p><b>%s</b><br/>%s</p>',
					$headline,
					__('Here is the data returned from the query, our error reporter is confused.', 'orionrush-tnygmaps' )
				);
				$message .= "<pre>";
				$message .= print_r( $response, true );
				$message .= "</pre>
				";
				break;

			default:
				$message .= sprintf(
					'<p><b>%s</b><br/>%s <br/><b>%s %s</b></p>',
					$headline,
					__('Something went wrong while retrieving your map, please ensure you have entered the short code parameters correctly.', 'orionrush-tnygmaps' ),
					__('Google\'s response message code is: ', 'orionrush-tnygmaps'),
					$error
				);
				$message .= "<pre>";
				$message .= print_r( $response, true );
				$message .= "</pre>
				";
		endswitch;
		Support\write_log('Mapping call errors:');
		Support\write_log($message);
		return $message;
	}
}
