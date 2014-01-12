//set up variables to contain our input values
var mapIdReturn = null;
var mapZoomReturn = null;
var mapWidthReturn = null;
var mapHeightReturn = null;
var mapTypeReturn = null;
var mapAddressReturn = null;
var mapAddressElement = null;
var mapMarkerReturn = null;
var mapInfoWindowReturn = null;
var infowindowPlace = null;
var mapMarkerImageReturn = null;
var mapKMLReturn = null;
var map = null;
var mapOptions = null;
var tr_gmaps = null;
var output = null;
var canvasID = "map_canvas"
var icon = null;
var address = null;
var phone = null;
var web = null;
var image = null;
var place = null;
var mapCurrCenter = null;
var marker_output = '';
var combinedInfoWindow = '';

jQuery(document).ready(function(jQuery) {
	// populate initial values
	mapIdReturn = jQuery('input[id=mapId]').val();
	mapZoomReturn = parseInt(jQuery('select[id=mapZoom]').val(), 10);
	mapWidthReturn = parseInt(jQuery('input[id=mapWidth]').val(), 10);
	mapHeightReturn = parseInt(jQuery('input[id=mapHeight]').val(), 10);
	mapTypeReturn = jQuery('select[id=mapType]').val();
	mapAddressReturn = jQuery('input[id=mapAddress]').val();
	mapMarkerReturn = jQuery('select[id=mapMarker]').val();
	mapInfoWindowReturn = jQuery('textarea[id=mapInfoWindow]').val();
	mapMarkerImageReturn = jQuery('input[id=mapMarkerImage]').val();
	mapKMLReturn = jQuery('input[id=mapKML]').val();
});

function initialize(infowindow) {
	mapOptions = {
	  center: new google.maps.LatLng(43.703793, -72.326187),
	  zoom: parseFloat(mapZoomReturn),
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

	var input = document.getElementById('mapAddress');
	var autocomplete = new google.maps.places.Autocomplete(input);
	var infowindow = new google.maps.InfoWindow();
	var marker = new google.maps.Marker({ map: map });

	// Zoom DOM>MAP
	google.maps.event.addDomListener(document.getElementById('mapZoom'),
        'change', function() {
        mapZoomReturn = parseInt(jQuery('select[id=mapZoom]').val(), 10);
        mapCurrCenter = map.getCenter(); // center on present location
		map.setZoom(mapZoomReturn);
		map.setCenter(mapCurrCenter);
    });
	// Zoom MAP>DOM
	 google.maps.event.addListener(map, 'zoom_changed', function() {
		var zoomLevel = map.getZoom();
		mapCurrCenter = map.getCenter(); // center on present location
		map.setCenter(mapCurrCenter);
		jQuery('select[id=mapZoom] option').filter(function() {
		        return (jQuery(this).text() == zoomLevel);
		    }).prop('selected', true);
	});
	 // Map Type DOM>MAP
	 google.maps.event.addDomListener(document.getElementById('mapType'),
        'change', function() {
        mapTypeReturn = jQuery('select[id=mapType]').val();
        //map.setMapTypeId(mapTypeReturn);
        if (mapTypeReturn == "ROADMAP"){ map.setMapTypeId(google.maps.MapTypeId.ROADMAP); };
        if (mapTypeReturn == "SATELLITE"){ map.setMapTypeId(google.maps.MapTypeId.SATELLITE); };
        if (mapTypeReturn == "HYBRID"){ map.setMapTypeId(google.maps.MapTypeId.HYBRID); };
        if (mapTypeReturn == "TERRAIN"){ map.setMapTypeId(google.maps.MapTypeId.TERRAIN); };
    });
	// Map Type MAP>DOM
	 google.maps.event.addListener(map, 'maptypeid_changed', function() {
		var mapType = map.getMapTypeId();
		mapType = mapType.toUpperCase();
		//alert(mapType);
		jQuery('select[id=mapType] option').filter(function() {
		        return ( jQuery(this).text() == mapType);
		    }).prop('selected', true);
	});
	 // Info window DOM>MAP
		google.maps.event.addDomListener(document.getElementById('mapInfoWindow'),
        'change', function() {
        mapInfoWindowReturn = jQuery('textarea[id=mapInfoWindow]').val();
        // update the value of the info window
		//infowindow.setContent(mapInfoWindowReturn);

	  infowindowPlace = '<div class="marker inside">';
	  infowindowPlace += (icon !== null && icon !== undefined) ? '<img src="' + icon + '" class="marker icon"/>' : '';
	  infowindowPlace += '<strong>' + place.name + '</strong><br>';
	  infowindowPlace += (address !== null && address !== undefined) ? address + '<br>' : '';
	  infowindowPlace += (phone !== null && phone !== undefined) ? phone + '<br>' : '';
	  infowindowPlace += (web !== null && web !== undefined) ? '<a href="' + web +'" class="" target="_blank">'+ web +'</a><br>' : '';
	  infowindowPlace += (mapInfoWindowReturn !== null && mapInfoWindowReturn !==undefined) ? '<span class="marker extras">' + mapInfoWindowReturn + '</span>' : '';
	  infowindowPlace += marker_output;
	  infowindowPlace +=  '</div>';
	  infowindowPlace += '<a href="' + place.url +'" class="marker jumplink" target="_blank">external map</a>';
	  infowindow.setContent(infowindowPlace);
	  infowindow.open(map, marker);
    });

	google.maps.event.addListener(autocomplete, 'place_changed', function() {
	  infowindowPlace = "";
	  infowindow.close();
	  marker.setVisible(false);
	  input.className = '';
	  place = autocomplete.getPlace();
	  if (!place.geometry) {
	    // Inform the user that the place was not found and return.
	    input.className = 'notfound';
	    return;
	  }
	  // If the place has a geometry, then present it on a map.
	  if (place.geometry.viewport) {
	    map.fitBounds(place.geometry.viewport);
	    mapCurrCenter = map.getCenter();
	  } else {
	    map.setCenter(place.geometry.location);
	    map.setZoom(parseFloat(mapZoomReturn));
	    mapCurrCenter = map.getCenter();
	  }
	  image = new google.maps.MarkerImage(
	      place.icon,
	      new google.maps.Size(71, 71),
	      new google.maps.Point(0, 0),
	      new google.maps.Point(17, 34),
	      new google.maps.Size(35, 35));
	  marker.setIcon(image);
	  marker.setPosition(place.geometry.location);
	  if (place.address_components) {
	  //console.log(place.address_components);
	  	icon = place.icon;
	  	address = place.formatted_address;
	  	phone	= place.formatted_phone_number;
	  	web = place.website;
	  }
	  infowindowPlace = ""; // clear any previous values
	  infowindowPlace = '<div class="marker inside">';
	  infowindowPlace += (icon !== null && icon !== undefined) ? '<img src="' + icon + '" class="marker icon"/>' : '';
	  infowindowPlace += '<strong>' + place.name + '</strong><br/>';
	  infowindowPlace += (address !== null && address !== undefined) ? address + '<br/>' : '';
	  infowindowPlace += (phone !== null && phone !== undefined) ? phone + '<br/>' : '';
	  infowindowPlace += (web !== null && web !== undefined) ? '<a href="' + web +'" class="" target="_blank">'+ web +'</a><br/>' : '';
	  infowindowPlace += (mapInfoWindowReturn !== null && mapInfoWindowReturn !==undefined) ? '<span class="marker extras">' + mapInfoWindowReturn + '</span>' : '';
	  infowindowPlace += marker_output;
	  infowindowPlace +=  '</div>';
	  infowindowPlace += '<a href="' + place.url +'" class="marker jumplink" target="_blank">external map</a>';
	  infowindow.setContent(infowindowPlace);
	  infowindow.open(map, marker);
	});
	// update the value of the info window
	combinedInfoWindow = infowindowPlace;
}
tr_gmaps = {
	// load up our inputs and output them to a short code
	local_ed : 'ed',
	init : function(ed) {
		tr_gmaps.local_ed = ed;
		tinyMCEPopup.resizeToInnerSize();
	},
	insert : function insertButton(ed) {
		// load our input values
		mapIdReturn = jQuery('input[id=mapId]').val();
		mapZoomReturn = jQuery('select[id=mapZoom]').val();
		mapWidthReturn = jQuery('input[id=mapWidth]').val();
		mapHeightReturn = jQuery('input[id=mapHeight]').val();
		mapTypeReturn = jQuery('select[id=mapType]').val();
		mapAddressReturn = jQuery('input[id=mapAddress]').val();
		mapMarkerReturn = jQuery('select[id=mapMarker]').val();
		//mapInfoWindowReturn = infowindowPlace;
		mapMarkerImageReturn = jQuery('input[id=mapMarkerImage]').val();
		mapKMLReturn = jQuery('input[id=mapKML]').val();
		// Control how the map marker is inserted
		// Also location of Default marker image
		alert(infowindowPlace);
		if (mapMarkerReturn == 'concert'){
			mapMarkerReturn = 'true';
			mapMarkerImageReturn = '<?php  echo $pluginfolder; ?>/../img/icons/music_folk_map.png';
		}
		if (mapMarkerReturn == 'none'){
			mapMarkerReturn = '';
			mapMarkerImageReturn = '';
		}
		if (mapMarkerReturn == 'default'){
			mapMarkerReturn = 'true';
			mapMarkerImageReturn = '';
		}
		if (mapMarkerReturn == 'custom'){
			mapMarkerReturn = 'true';
			mapMarkerImageReturn = mapMarkerImageReturn;
		}
		// output the shortcode
		marker_output = '[tr_gmaps ';
		(mapIdReturn != "") ? marker_output += 'id="' + mapIdReturn + '" ' : '';
		marker_output += 'z="' + mapZoomReturn +'" ';
		marker_output += 'w="' + mapWidthReturn + '" ';
		marker_output += 'h="' + mapHeightReturn + '" ';
		marker_output += 'scrollwheel="false" ';
		(mapTypeReturn !="roadmap") ? marker_output += 'maptype="' + mapTypeReturn + '" ' : '';
		marker_output += 'address="' + mapAddressReturn + '" ';
		(mapMarkerReturn != "") ? marker_output += 'marker="' + mapMarkerReturn + '" ' : '';
		(mapMarkerReturn != "" && mapMarkerImageReturn != "") ? marker_output += 'markerimage="' + mapMarkerImageReturn + '" ' : '';
		(mapMarkerReturn != "" && mapInfoWindowReturn !="") ? marker_output += 'infowindow="' + infowindowPlace + '" ' : '';
		(mapKMLReturn != "" ) ? marker_output += 'kml="' + mapKMLReturn + '"' : '';
		marker_output += ']';
		tinyMCEPopup.execCommand('mceReplaceContent', false, marker_output);
		// Return
		tinyMCEPopup.close();
	}
};
google.maps.event.addDomListener(window, 'load', initialize);
tinyMCEPopup.onInit.add(tr_gmaps.init, tr_gmaps);