/**
 * Logging function
 *
 * @param debug
 * @param message
 */
function tnygmapsDebug( debug, message ) {
	if ( debug === "1" ) {
		console.log( message );
	}
}

/**
 * Initialize map from jquery ready event, or google dom resize event listeners.
 *
 * @param mapID
 * @param mapLocation
 */
function initialize( mapID, mapLocation ) {
	var attr, latlng, mapTypeId, mapOptions;


	// an attribute added by google api after init has already been called
	attr = jQuery( "#" + mapID ).attr( "jstcache" );
	// prevent init from being called on maps already drawn
	if ( typeof attr === typeof undefined || attr === false || attr === "" ) {
		tnygmapsDebug( mapLocation.debug, "init" );

		 latlng = new google.maps.LatLng( mapLocation.lat, mapLocation.lng );
		 mapTypeId = mapLocation.maptype;
		 mapOptions = {
			zoom: parseInt( mapLocation.z ),
			mapTypeId: google.maps.MapTypeId[mapTypeId],
			center: latlng,
			scrollwheel: mapLocation.scrollwheel,
			scaleControl: mapLocation.scaleControl,
			disableDefaultUI: mapLocation.hidecontrols,
			gestureHandling: 'cooperative'
		};
		// Start the map
		mapID = new google.maps.Map( document.getElementById( mapID ), mapOptions );

		// Load the icon and bubble
		google.maps.event.addListenerOnce( mapID, "tilesloaded", function () {
			var marker = new google.maps.Marker( {
				map: mapID,
				position: mapID.getCenter(),
				icon: mapLocation.marker
			} );
			var infowindow = new google.maps.InfoWindow( {
				content: mapLocation.infowindow,
				position: latlng
			} );
			infowindow.open( mapID, marker );
			google.maps.event.addListener( marker, "click", function () {
				infowindow.open( mapID, marker );
			} );
		} );

		google.maps.event.addDomListener( window, "resize", function () {
			// Centering on window resize
			var center = mapID.getCenter();
			google.maps.event.trigger( mapID, "resize" );
			mapID.setCenter( center );
			tnygmapsDebug( mapLocation.debug, "resize center" );
		} );
	}
}

/**
 *
 * On Dom Ready init the map, if both the screen is large enough and we're not on a mobile device
 * If we're not big enough and on mobile, adjust the container css
 */
jQuery( function () {
	// https://stackoverflow.com/a/10364620
	var isMobile = /Mobi/i.test( navigator.userAgent ) || /Anroid/i.test( navigator.userAgent );

	jQuery( '.tnygmps_canvas' ).each( function () {
		var mapID, mapLocation, isSmallScreen, load_maps;
		 mapID = jQuery( this ).attr( "id" );
		 mapLocation = window[mapID + "_loc"]; // our global var array for this map
		 isSmallScreen = window.matchMedia( "only screen and (max-width: " + mapLocation.static_DOM_width + "px)" );
		 load_maps = false;
		if ( isSmallScreen ) {
			load_maps = (
				! isMobile ? true : false
			);
		}
		tnygmapsDebug( mapLocation.debug, "Tny gMaps: isSmallScreen ='" + isSmallScreen.matches + "'" );
		tnygmapsDebug( mapLocation.debug, "Tny gMaps: isMobile ='" + isMobile + "'" );
		tnygmapsDebug( mapLocation.debug, "Tny gMaps: DOM breakpoint: " + mapLocation.static_DOM_width + "' ." );
		tnygmapsDebug( mapLocation.debug, "map found" );

		if ( load_maps ) {
			tnygmapsDebug( mapLocation.debug, "Tny gMaps: DOM width larger than '" + mapLocation.static_DOM_width + "' so initialize map" );
			jQuery( "#" + mapID ).css( "height", mapLocation.h ); // set the map height
			jQuery( "#" + mapID + "> .tnygmps_staticimg" ).hide();
			jQuery( "#" + mapID + "> .tnygmps_static_bubble" ).hide();
			initialize( mapID, mapLocation );
		} else if ( ! isSmallScreen.matches && isMobile ) {
			// if it is mobile, but is a large enough screen run it anyway
			jQuery( "#" + mapID ).css( "height", mapLocation.h ); // set the map height
			jQuery( "#" + mapID + "> .tnygmps_staticimg" ).hide();
			jQuery( "#" + mapID + "> .tnygmps_static_bubble" ).hide();
			initialize( mapID, mapLocation );
		} else {
			tnygmapsDebug( mapLocation.debug, "Tny gMaps: DOM current width: '" + document.documentElement.clientWidth + "px'." );
			jQuery( "#" + mapID ).css( "height", "auto" );
		}
	} );
} );
