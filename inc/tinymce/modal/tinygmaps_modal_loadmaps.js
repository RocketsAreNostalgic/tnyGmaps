// enable trim in older browsers
if (!String.prototype.trim) {
  String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g,'');
    };
}
//var parent.tinygmaps.haveGPlaces_key; // provided global
//var parent.tinygmaps.pluginURI; // provided global
var lat = null;
var lng = null;
var mapZoomReturn = null;
var mapWidthReturn = null;
var mapHeightReturn = null;
var mapTypeReturn = null;
var mapControlsReturn = null;
var mapAddressReturn = null;
var mapAddressElement = null;
var locGooglePlaceID = null;
var mapMarkerReturn = null;
var mapMarkerImageReturn = null;
var infowindow = null;
var infowindowPlace = null;
var mapInfoWindowReturn = null;
var combinedInfoWindow = null;
var tinyGmaps = null;
var map = null;
var marker = null;
var mapOptions = null;
var mapCurrCenter = null;
var markerOutput = null;
var locPlace = null;

var markerCustom = parent.tinygmaps.pluginURI + 'inc/tinymce/icons/music_folk_map.png';

// populate variables with field values 
function seed_vars (){
	lat = jQuery('input#mapLat').val();
	lng = jQuery('input#mapLng').val();
	//console.log('lat-lng: ' + lat + ' ' + lng);
	mapZoomReturn = parseInt(jQuery('select[id=mapZoom]').val(), 10);
	//console.log('mapZoomReturn: ' + mapZoomReturn);
	mapWidthReturn = jQuery('input[id=mapWidth]').val();
	//console.log('mapWidthReturn: ' + mapWidthReturn);
	mapHeightReturn = jQuery('input[id=mapHeight]').val();
	//console.log('mapHeightReturn: ' + mapHeightReturn);
	mapTypeReturn = jQuery('select[id=mapType]').val();
	//console.log('mapTypeReturn: ' + mapTypeReturn);
	locGooglePlaceID = jQuery('input#locGooglePlaceID').val();
	//console.log('locGooglePlaceID: ' + locGooglePlaceID);
	mapAddressReturn = jQuery('input[id=mapAddress]').val();
	//console.log('mapAddressReturn: ' + mapAddressReturn);
	locName = jQuery('input#locName').val();
	//console.log('locName: ' + locName);
	locPhone = jQuery('input#locPhone').val();
	//console.log('locPhone: ' + locPhone);
	locStreet = jQuery('input#locStAdr').val();
	//console.log('locStreet: ' + locStreet);
	locCity = jQuery('input#locCity').val();
	//console.log('locCity: ' + locCity);
	locRegion = jQuery('input#locRegion').val();
	//console.log('locRegion: ' + locRegion);
	locPostcode = jQuery('input#locPostcode').val();
	//console.log('locPostcode: ' + locPostcode);
	locCountry = jQuery('input#locCountry').val();
	//console.log('locCountry: ' + locCountry);
	locWeb = jQuery('input#locWebsite').val();
	//console.log('locWeb: ' + locWeb);
	locIcon = jQuery('input#locIconURL').val();
	//console.log('locIcon: ' + locIcon);
	mapMarkerImageReturn = get_marker_image();
	//console.log('mapMarkerImageReturn: ' + mapMarkerImageReturn);
	mapInfoWindowReturn = clean_html(jQuery('textarea[id=mapInfoWindow]').val());
	//console.log('mapInfoWindowReturn: ' + mapInfoWindowReturn);
	mapControlsReturn = jQuery('select[id=mapControls]').val() == 'true';
	//console.log('mapControlsReturn: ' + mapControlsReturn);
}

// Retrieve the the marker image
function get_marker_image(){
	mapMarkerReturn = jQuery('select[id=mapMarker]').val();
	switch(mapMarkerReturn)
	{
		case 'concert':
			mapMarkerImageReturn = markerCustom;
		break;
		case 'default':
			mapMarkerImageReturn = ''; // will fallback to Google marker icon 
		break;
		case 'custom':
			mapMarkerImageReturn = jQuery('#mapMarkerImage').val();
		break;
	}
	//console.log('Marker image: ' + mapMarkerImageReturn);
	return mapMarkerImageReturn;
} 
// User isn't useing auto-complete so clean the inputs fields so we can reverse lookup the address and cache the cordinates
function custom_location(){
	jQuery('#mapAddress').val(''); 	// empty autocomplete feild
	jQuery('#locGooglePlaceID').val(''); 	// empty location ref
	jQuery('#locIconURL').val(''); 	// empty icon
	jQuery('#mapLat').val(''); 		// empty cordinates
	jQuery('#mapLng').val('');
}

/*
 * Initilize the map
*/
google.maps.event.addDomListener(window, 'load', initialize);
function initialize(infowindow) {
	/* 
	 * Form UX
	 * We put this here so that we have access to map handelers 
	 */ 
	jQuery(document).ready(function($) {
		// Prevent the return key from submitting the form too soon
		jQuery('.noEnterSubmit').keypress(function(e){
	    	if ( e.which == 13 ) return false;
		});
		// Set focus
		setFocus(jQuery(this));
		// Accordion
		jQuery('.accordion').hide();
		jQuery('.heading').click(function () {
			// close it if its open
			if (jQuery(this).next().find('.accordion').hasClass('open')){
				jQuery(this).next().find('.open').removeClass('open active').slideUp(function(){
					jQuery("#button-dialog form").find('input:text').first().trigger('focus');
				});
				
				return;
			}
			// close all others
		    jQuery('.heading').not(this).next('.group').find('.accordion').slideUp(function(){
		    	jQuery(this).removeClass('open active');
		    	setFocus(jQuery(this));
		    });
		    // open the clicked one if its closed
		    jQuery(this).next().find('.accordion').slideDown(400, function(){
		    	setFocus(jQuery(this));
		    	jQuery(this).addClass('open active');
			});
		});
		// Custom Location details & regulate access to auto lookup field
	    jQuery('input[name="custom_use_address_check"]').click(function() {
		    if( jQuery(this).is(':checked')) {
	        	jQuery('#mapAddress-group').slideUp(400, function(){
		        	jQuery('#address_extras').find('input:text, button').addClass('active highlight').prop('disabled', false).delay(700).show( 1 , function(){
		        		jQuery(this).removeClass('highlight');
		        	}); 
			    	jQuery("#locName").trigger('focus');
	        	});
		    } 
		    if ( jQuery(this).is(':checked') == false ) {
		    	jQuery('#mapAddress-group').slideDown();
		    	jQuery('#address_extras').find('input:text, button').prop('disabled', true).val('').removeClass('active');; // disable and clear
		    	jQuery(".autoMapAddress").parent().slideDown(400, function(){
					setFocus(jQuery(this));
					generateMap();
				});	
		     }
	    });
		if (parent.tinygmaps.haveGPlaces_key == false) {
                jQuery('#tinygmaps.haveGPlaces_key').remove();
				jQuery('#address_extras').show();
				jQuery('#address_extras').find('input:text, button').prop('disabled', false);
	    } else {
				jQuery('#address_extras').find('input:text, button').prop('disabled', true);
		}
		// Conditional display of custom map icon url
	    jQuery('.mapMarkerImage_wrap').hide();
		jQuery('#mapMarker').change(function() {
	         var selected = $("#mapMarker option:selected").text();
	         if (selected === 'Custom'){
	         	jQuery('.mapMarkerImage_wrap').slideDown();
	         	setFocus(jQuery(this));
	         } else {
	         	jQuery('.mapMarkerImage_wrap').slideUp();
	         }
	    });
	    // Clear fields
	    jQuery('button[id="clear-fields"]').click(function() {
	    	if (jQuery('#search-report').is(':animated')){
	    		return false;
	    	} else {
	    	jQuery('#address_extras :input:not(input:checkbox)', 'div').val([]);
			//console.log('fields cleared');
			updateAlert('Cleared!','confirm');
			}
	    });
	   	// Icon list interaction
	    jQuery('.map-icon').click(function(e){
	    	e.preventDefault();
			var icon = jQuery(this).attr('title');
	    	jQuery('input#mapMarkerImage').val( parent.tinygmaps.pluginURI + 'inc/tinymce/icons/' + icon );
	    	update_marker();
		});
	    // Notice
	    jQuery('.fadeout').delay(2000).fadeOut(800);
		//Tool Tip
		jQuery('[data-tooltip !=""]').qtip({
	       	position: {
	       		my: 'top-left'
	       	},
	       	content: {
	           	attr: 'data-tooltip'
	       	}
    	});
	}); 
	// set focus on first input or text area
	function setFocus(obj){
		jQuery(obj).find('input, textarea').first()
		;
	}
	function updateAlert(string, alertClass){
		alertClass = alertClass + " alert";
		jQuery('#search-report').each(function(){
			jQuery(this).addClass(alertClass);
			jQuery(this).width((jQuery(this).parent().width()-15));
			jQuery(this).append(string);
			jQuery('#search-report').fadeIn(800).delay(300).fadeOut(800, function(){
				jQuery(this).removeClass(alertClass).empty();
			});
		})	
	};

	/*
	 * MAP Init
	 */  
	var input = document.getElementById('mapAddress');
	var autocomplete = new google.maps.places.Autocomplete(input);
	var infowindow = new google.maps.InfoWindow();
	// Draw the map 
	generateMap();

	function generateMap(){
		seed_vars ();
		(lat !== '' && lng !== '' ) ? initCenter = new google.maps.LatLng(lat,lng) : initCenter = new google.maps.LatLng(43.703793, -72.326187);
		mapOptions = {
			center: initCenter,
			zoom: mapZoomReturn,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			scrollwheel : false,
			streetViewControl: false,
			panControl: false,
			disableDefaultUI : mapControlsReturn
		};
		map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
		updateMapType(); // needed for when we cycle controls on/off
		marker = new google.maps.Marker({
			position: initCenter,
			map: map, 
			icon: mapMarkerImageReturn  
		});
		infowindowPlace = get_info_bubble(locIcon, locName, locStreet, locCity, locRegion, locPostcode, locCountry, locPhone, locWeb, mapInfoWindowReturn);
		infowindow.setContent(infowindowPlace);
		if (locName !== '')	infowindow.open(map, marker);
		google.maps.event.addListener(marker, 'click', function() {
   			if (locName !== '')	infowindow.open(map, marker);
  		});
		addListeners();
		//console.log('map created');
	}

	// Set up event listeners 
	function addListeners(){
		// Custom Location, here we strip out any refrences to Auto lookup if any of these feilds are modified
		google.maps.event.addDomListener(document.getElementById('locName'), 'change', custom_location );
		google.maps.event.addDomListener(document.getElementById('locPhone'), 'change', custom_location );
		google.maps.event.addDomListener(document.getElementById('locStAdr'), 'change', custom_location );
		google.maps.event.addDomListener(document.getElementById('locCity'), 'change', custom_location );
		google.maps.event.addDomListener(document.getElementById('locRegion'), 'change', custom_location );
		google.maps.event.addDomListener(document.getElementById('locPostcode'), 'change', custom_location );
		google.maps.event.addDomListener(document.getElementById('locCountry'), 'change', custom_location );
		google.maps.event.addDomListener(document.getElementById('locWebsite'), 'change', custom_location );
		// Custom location button
		google.maps.event.addDomListener(document.getElementById('lookup-detials'), 'click', updateCustomlocation );
		// Custom location update infowindwo
		google.maps.event.addDomListener(document.getElementById('map-update'), 'click', updateMapInfoWindow );

		// Controls DOM->MAP
		google.maps.event.addDomListener(document.getElementById('mapControls'), 'change', generateMap );
		// Map Type DOM ->MAP
		google.maps.event.addDomListener(document.getElementById('mapType'), 'change', updateMapType );
		// Map Type MAP ->DOM
		google.maps.event.addListener(map, 'maptypeid_changed', updateDomMapType );
		// Info window DOM->MAP
		google.maps.event.addDomListener(document.getElementById('mapInfoWindow'), 'change', updateMapInfoWindow );
		google.maps.event.addDomListener(document.getElementById('mapInfoWindow'), 'click', openInfoWindow );
		setInterval(function() { updateMapCustomDeets(jQuery('#mapInfoWindow').val()); }, 100);	
		// Marker selection DOM->MAP
		google.maps.event.addDomListener(document.getElementById('mapMarker'), 'change', update_marker);
	    // Custom marker image DOM->MAP
		google.maps.event.addDomListener(document.getElementById('mapMarkerImage'), 'change', update_marker);		
		// Zoom DOM->MAP
		google.maps.event.addDomListener(document.getElementById('mapZoom'), 'change', upadateMapZoomChange );		
		// Place Loop DOM -> MAP
		google.maps.event.addListener(autocomplete, 'place_changed', updateMapAutocomplete );
		// Zoom MAP->DOM
		 google.maps.event.addListener(map, 'zoom_changed', updateMapZoomChange );
		// Click handeler for marker & infowindow
		addMarkerClickHandler();
	}

	/* Listener functions */
	
	function addMarkerClickHandler(){
		// open the info window on marker click if there is one
		google.maps.event.addListener(marker, 'click', function() {
			if (mapInfoWindowReturn !== '')	infowindow.open(map, marker); // only if it has something in it
	  	});
	}
    function upadateMapZoomChange () {
        mapZoomReturn = parseInt(jQuery('select[id=mapZoom]').val(), 10);
        mapCurrCenter = map.getCenter(); // center on present location
		map.setZoom(mapZoomReturn);
		map.setCenter(mapCurrCenter);
    }
	function updateMapZoomChange() {
		var zoomLevel = map.getZoom();
		mapCurrCenter = map.getCenter(); // center on present location
		map.setCenter(mapCurrCenter);
		jQuery('select[id=mapZoom] option').filter(function() {
		        return (jQuery(this).text() == zoomLevel);
		    }).prop('selected', true);
	}
    function updateMapType() {
        mapTypeReturn = jQuery('select[id=mapType]').val();
        //map.setMapTypeId(mapTypeReturn);
        if (mapTypeReturn == "ROADMAP"){ map.setMapTypeId(google.maps.MapTypeId.ROADMAP); };
        if (mapTypeReturn == "SATELLITE"){ map.setMapTypeId(google.maps.MapTypeId.SATELLITE); };
        if (mapTypeReturn == "HYBRID"){ map.setMapTypeId(google.maps.MapTypeId.HYBRID); };
        if (mapTypeReturn == "TERRAIN"){ map.setMapTypeId(google.maps.MapTypeId.TERRAIN); };
    }
	function updateDomMapType() {
		var mapType = map.getMapTypeId();
		mapType = mapType.toUpperCase();
		jQuery('select[id=mapType] option').filter(function() {
		    return ( jQuery(this).text() == mapType);
		}).prop('selected', true);
	}
	function updateMapInfoWindow () {
		seed_vars();
		mapInfoWindowReturn = clean_html(jQuery('textarea#mapInfoWindow').val());
        infowindowPlace = get_info_bubble(locIcon, locName, locStreet, locCity, locRegion, locPostcode, locCountry, locPhone, locWeb, mapInfoWindowReturn);
		infowindow.setContent(infowindowPlace);
		jQuery('textarea#mapInfoWindow').prop('disabled', true).addClass('highlight alert').delay(700).show( 1 , function(){
			  jQuery(this).val(mapInfoWindowReturn).removeClass('highlight alert').prop('disabled', false);
			  jQuery(this)[0].selectionStart =  jQuery(this)[0].selectionEnd =  jQuery(this).val().length; // reset the cursor
		});
    }
    function openInfoWindow(){
    	if (jQuery('#mapInfoWindow').is(":visible")){
	    	// Add marker extras
	    	if (!jQuery('.marker-extras').length > 0) {
	    	 	seed_vars();
	    	 	infowindowPlace = get_info_bubble(locIcon, locName, locStreet, locCity, locRegion, locPostcode, locCountry, locPhone, locWeb, '...');
	    		infowindow.setContent(infowindowPlace);
	    	}
	    	// add the keystroke timer
			var timer = null;
			jQuery('#mapInfoWindow').live('keydown', function(e){
				if (timer) {
					clearTimeout(timer);
				}
					timer = setTimeout(updateMapInfoWindow, 2500);
			});
		}
    	if (!infowindow.open(map, marker)) infowindow.open(map, marker);    	
    }
    function updateMapCustomDeets(value){
    	var content = jQuery('.marker-extras').html();
    	if (content !== value)  content = clean_html(value);
    	jQuery('.marker-extras').html(content);
    }

	function update_marker(){
		markerImage = get_marker_image(); // returns URL as string
		marker.setIcon(markerImage);
		(locPlace !== null ) ? marker.setPosition(locPlace.geometry.location) : '';
		marker.setMap(map);		
	}
	function updateMapAutocomplete() {
		infowindow.close();
		input.className = '';
		locPlace = autocomplete.getPlace();
		 console.log(locPlace);
		if (!locPlace.geometry) {
			// Inform the user that the place was not found and return.
			input.className = 'notfound';
			return;
		}
		// //build the map
		if (locPlace.geometry.viewport) {
		
			map.fitBounds(locPlace.geometry.viewport);
			mapCurrCenter = map.getCenter();
		} else {
			map.setCenter(locPlace.geometry.location);
			map.setZoom(parseFloat(mapZoomReturn));
			mapCurrCenter = map.getCenter();
		}
		// Process the results
		if (locPlace.address_components) {
			// clear any previous values
			locName = '';
			locStreetNum = '';
			locStreet = '';
			locCity = '';
			locRegion = '';
			locPostcode = '';
			locCountry = '';
			locIcon = '';
			locPhone = '';
			locWeb = '';
			lat = '';
			lng = '';
			locGooglePlaceID = '';

			// these live at the top level of the object so we can access them easily
			// get the information and set the text field if it exsists, else clear the varriable and the associated field


			// Googles Places Refrence ID
			(locPlace.place_id) ?  locGooglePlaceID = locPlace.place_id : locGooglePlaceID = '';
			jQuery('#locGooglePlaceID').val(locGooglePlaceID.trim());
			//Lng & Lat
			lat = locPlace.geometry.location.lat();
			jQuery('#mapLat').val(lat);	
			lng = locPlace.geometry.location.lng();
			jQuery('#mapLng').val(lng);
			//Name 
			locName = locPlace.name;
			jQuery('#locName').val(locName);
			// Icon
			locIcon = locPlace.icon;
			jQuery('#locIconURL').val(locIcon.trim());
			//Phone
			locPhone = locPlace.formatted_phone_number;
			jQuery('#locPhone').val(locPhone);	
			//Website
			locWeb = locPlace.website;
			jQuery('#locWebsite').val(locWeb);
			
			// Do the same for nested address_components -- iterate through the array, then set the field value

			// Premise or building name
			var locPremise = processObject( 'premise', locPlace.address_components );
			// street number
			locStreetNum = processObject ( 'street_number', locPlace.address_components );
			// Street name
			locStreet = processObject ( 'route', locPlace.address_components  );

			// Set Input
			(locPremise) ? locPremise = locPremise + ', ' : '';
			var streetCombined = (locPremise + locStreetNum + ' ' + locStreet).trim();
			jQuery('input#locStAdr').val(streetCombined);

			// City-Town
			if ( processObject ( 'administrative_area3', locPlace.address_components  )) locCity = processObject( 'administrative_area3', locPlace.address_components );
			if ( processObject ( 'locality', locPlace.address_components  )) locCity = processObject( 'locality', locPlace.address_components );
			if ( processObject ( 'sublocality', locPlace.address_components  )) locCity = processObject( 'sublocality', locPlace.address_components );
			if ( processObject ( 'postal_town', locPlace.address_components  )) locCity = processObject( 'postal_town', locPlace.address_components );
			// Set Input
			jQuery('input#locCity').val(locCity.trim());

			// state
			locRegion = processObject ( 'administrative_area_level_1', locPlace.address_components );
			if (!locRegion) locRegion = processObject ( 'administrative_area_level_2', locPlace.address_components );
		
			// if we dont have a state but do have a region, use that instead. 
			if ((locRegion !=='' || locRegion !== null)  && (locRegion == '' || locRegion == null)) locRegion = locRegion;
			jQuery('#locCity').val(locCity.trim());	
			jQuery('#locRegion').val(locRegion.trim());	

			// Postal Code
			locPostcode = processObject ( 'postal_code', locPlace.address_components  );
			jQuery('#locPostcode').val(locPostcode);	

			locCountry = processObject ( 'country', locPlace.address_components  );
			jQuery('#locCountry').val(locCountry);	
			locAddress = locStreet + ', ' + locCity + ', ' + locRegion + ', ' + locPostcode + ', ' + locCountry;

		}
		generateMap();
	}
	// Here we are not using the places api, but instead geocoding api
	function updateCustomlocation(){
		if (jQuery('#search-report').is(':animated')){
	    		return false;
	    	} else {
			custom_location(); // clear the custom bits
			seed_vars (); // update the vars with the changes

			// use address fields to create a new address lookup using geocoding api
			locAddress = locName + '+' + locStreet + '+' + locCity + '+' + locRegion + '+' + locPostcode + '+' + locCountry;
			//console.log(locAddress);
			// locStreetNum = '';
			// locRegion = '';
			geocoder = new google.maps.Geocoder();
			callGeocode(function(){
				// update the map and marker
				generateMap();

			});
		}
		// Process the results
		function callGeocode(callback){
			geocoder.geocode( { 'address': locAddress}, function(results, status) {
			  if (status !== google.maps.GeocoderStatus.OK){
			  	var alert = "Sorry, try adding more address details: " + status;
			  	updateAlert( alert,'warning');
			  }

			  if (status == google.maps.GeocoderStatus.OK){
			  	locPlace = results[0];
			  	var locComponents = locPlace.address_components;	
			  	// console.log(locPlace);

			  	// Flush previous values
				locName = '';
				locStreetNum = '';
				locStreet = '';
				locCity = '';
				locRegion = '';
				locRegion = '';
				locPostcode = '';
				locCountry = '';
				locIcon = '';
				// lat = '';
				// lng = '';
				locGooglePlaceID = '';

				// Name of location
				locName = processObject ( 'point_of_interest', locPlace.address_components );
				// have observed this as a return value with geocode api as well
				// Set Input, we dont want to clear the field if these return null
				if (locName !== '' && locName !== null ) jQuery('#locName').val(locName.trim());

				// Premise or building name
				var locPremise = processObject( 'premise', locPlace.address_components );
				// street number
				locStreetNum = processObject ( 'street_number', locPlace.address_components );
				// Street name
				locStreet = processObject ( 'route', locPlace.address_components  );
				// Set Input
				(locPremise)? locPremise = locPremise + ', ' : '';
				var streetCombined = (locPremise + locStreetNum + ' ' + locStreet).trim();
				jQuery('input#locStAdr').val(streetCombined);

				// City-Town
				locCity = processObject ( 'postal_town', locPlace.address_components  );
				if (!locCity) locCity = processObject ( 'locality', locPlace.address_components  );
				// Set Input
				jQuery('input#locCity').val(locCity);

				// State - Region
				locRegion = processObject ( 'administrative_area_level_1', locPlace.address_components );
				if (!locRegion) locRegion = processObject ( 'administrative_area_level_2', locPlace.address_components );
				// Set Input
				jQuery('input#locRegion').val(locRegion);
		
				// Postal
				locPostcode = processObject ( 'postal_code', locPlace.address_components );
				// Set Input
				jQuery('input#locPostcode').val(locPostcode);

				// Country
				locCountry = processObject ( 'country', locPlace.address_components );
				// Set Input
				jQuery('input#locCountry').val(locCountry);

				lat = locPlace.geometry.location.lat();
				jQuery('input#mapLat').val(lat)
				lng = locPlace.geometry.location.lng();
				jQuery('input#mapLng').val(lng);

				var alert = "Geocoding sucessfull,  Lng & Lat status: " + status;
				updateAlert( alert,'confirm');
				seed_vars ();
				callback();
			  }
		  	});
		}
	}
}
/*
 * Helper functions
 */ 

// process Address Components
function processObject ( needle, haystack ){
	var rtrn = '';
	for (var i = 0; i < haystack.length; i++){
		var addr = haystack[i];
		(addr.types[0] == needle ) ? rtrn = addr.long_name : '';
	}
	return rtrn;
}

// clean input on client side
function htmlEntities(str) {
	// we also convert script tags to spans - a start but not perfect. - also removes single quotes
    // return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '');
   	// http://css-tricks.com/snippets/javascript/htmlentities-for-javascript/ (see comment from james)
    return String(str).replace(/&amp;/g, '&').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
// seeing how this string is going to be encoded, and then 'baked' into the shortcode 
// lets try to do as much cleaning here as we can - the short code will clean it as well before output
function clean_html(str){
	str = String(str).replace(/'/g, '"');
	return jQuery.htmlClean(str, { removeTags:["script"], allowedAttributes : [["style"], ["href", ["a"]], ["target", ["a"]], ["title", ["a"]]], allowedTags: ["p", "a", "span", "ul", "ol", "li", "br", "hr", "em", "strong"], format:false } );
}


/* 
 * Assemble the infowindow interior
*/
function get_info_bubble(icon, name, street, city, state, post, country, phone, web, info) {
	var iconStyle = ((icon !== '') ? 'max-width: 150px; ' : 'max-width: 200px; ');
	infowindowPlace =  '<div class="marker-inside"  style="hight:auto;" >';
	infowindowPlace += '<b>' + name + '</b>';
	infowindowPlace +=  '<table>';
	infowindowPlace +=  '<tbody>';
	infowindowPlace +=  '<tr>';
	infowindowPlace +=  '<td>';
	infowindowPlace +=  '<div class="infowidow-address" style="'+ iconStyle +'" >';
	infowindowPlace += (street !== null && street !== undefined && street !=='') ? '<div>' + street + '</div>' : '';
	infowindowPlace += (city !== null && city !== undefined && city !=='') ? '<div>' + city + ', ' : '<div>';
	infowindowPlace += (state !== null && state !== undefined && state  !=='') ? state + '</div>' : '</div>';
	infowindowPlace += (post !== null && post !== undefined && post  !=='') ? '<div>' + post + '</div>' : '</div>';
	infowindowPlace += (country !== null && country !== undefined && country  !=='') ? '<div>' + country + '</div>' : '</div>';
	infowindowPlace += (phone !== null && phone !== undefined && phone  !=='') ? '<div>' + phone + '</div>' : '';
	infowindowPlace += (web !== null && web !== undefined && web  !=='') ? '<div style="max-width: 100%; white-space: nowrap; width: 100%; overflow: hidden;  -o-text-overflow: ellipsis;  text-overflow: ellipsis;"><a href="' + web +'" class="gmap_link" target="_blank" style="">'+ web + '</a></div>' : '';
	infowindowPlace +=  '</div>';		
	infowindowPlace +=  '</td>';
	infowindowPlace +=  '<td>';
	infowindowPlace += (icon !== null && icon !== undefined) ? '<img src="' + icon + '" class="marker-icon" style="margin: 0 5px 15px 5px; width: 60px; height: auto; " />' : '';
	infowindowPlace +=  '</td>';
	infowindowPlace +=  '</tr>';
	infowindowPlace +=  '</tbody>';
	infowindowPlace +=  '</table>';
	infowindowPlace += '</div>';
	infowindowPlace += (info !== null && info !==undefined && info !=='') ? '<div class="marker-extras" style="border-top: 1px dotted #949494; margin-top: 5px; max-width: 265px; min-height: 40px; overflow: hidden; white-space: pre-wrap;" >' + mapInfoWindowReturn + '</div>' : '';
  	return infowindowPlace;
}

/* 
 * load up our field values and output them as a short code
*/
tinyGmaps = {
	local_ed : 'ed',
	init : function(ed) {
        tinyGmaps.local_ed = ed;
		tinyMCEPopup.resizeToInnerSize();
	},
	insert : function insertButton(ed) {
		// load our input values
		seed_vars();
		// clean user input
		mapInfoWindowReturn = htmlEntities(mapInfoWindowReturn) // encode html entities 
		mapInfoWindowReturn = jQuery.base64.encode(mapInfoWindowReturn); // then base64 encode it as a string
		// output the shortcode
		markerOutput = '[TINYGMAPS ';
		markerOutput += 'z="' + mapZoomReturn +'" ';
		markerOutput += 'w="' + mapWidthReturn + '" ';
		markerOutput += 'h="' + mapHeightReturn + '" ';
		(mapControlsReturn == true ) ? markerOutput += 'hidecontrols="true" ' : '';

		
		if (locGooglePlaceID == null || locGooglePlaceID == '') {
			markerOutput += 'lat="' + lat + '" ';
			markerOutput += 'lng="' + lng + '" ';
			// assemble the address values
			(locName !== "" && locName !== "")			? markerOutput += 'name="' + htmlEntities(locName) + '" ' : '';
			(locStreet !== "" && locStreet !== "")		? markerOutput += 'street="' + htmlEntities(locStreet) + '" ' : '';
			(locCity !== "" && locCity !== "") 			? markerOutput += 'city="' + htmlEntities(locCity) + '" ' : '';
			(locRegion !== "" && locRegion !== "")		? markerOutput += 'region="' + htmlEntities(locRegion) + '" ': '' ;
			// (locCounty !== "" && locCounty !== "")		? markerOutput += 'county="' + locCounty + '" ': '' ;
			(locPostcode !== "" && locPostcode !== "")	? markerOutput += 'postcode="' + htmlEntities(locPostcode) + '" ' : '';
			(locCountry !== "" && locCountry !== "")		? markerOutput += 'country="' + htmlEntities(locCountry) + '" ' : '';
			(locWeb !== "" && locWeb !== "")				? markerOutput += 'website="' + locWeb + '" ' : '';
			(locPhone !== "" && locPhone !== "") 			? markerOutput += 'phone="' + htmlEntities(locPhone) + '" ' : '';

		}
		else markerOutput += 'placeid="' + locGooglePlaceID + '" ';
		(mapMarkerReturn !== "" && mapMarkerImageReturn !== "") ? markerOutput += 'marker="' + mapMarkerImageReturn + '" ' : '';
		(mapMarkerReturn !== "" && mapInfoWindowReturn !=="") ? markerOutput += 'infowindowb64="' + mapInfoWindowReturn + '" ' : '';
		markerOutput += ']';
		tinyMCEPopup.execCommand('mceReplaceContent', false, markerOutput);
		// Return
		tinyMCEPopup.close();
	}
};
tinyMCEPopup.onInit.add(tinyGmaps.init, tinyGmaps);