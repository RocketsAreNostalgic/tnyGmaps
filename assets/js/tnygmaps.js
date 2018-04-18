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
// https://stackoverflow.com/a/10364620
var tnyGmapsIsMobile = /Mobi/i.test( navigator.userAgent ) || /Anroid/i.test( navigator.userAgent );

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
			if ( mapLocation.infowindowdefault === 'yes' ) {
				infowindow.open( mapID, marker );
			}
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
 *
 * @param isMobile
 */
function initTest( isMobile ) {

	jQuery( '.tnygmps_canvas' ).each( function () {
		var mapID, mapLocation, isSmallScreen, load_maps, mapLoaded;

		mapID = jQuery( this ).attr( "id" );

		// Check flag to see if we have already init the map
		mapLoaded = jQuery( this ).hasClass( 'mapLoaded' );
		if ( mapLoaded ) {
			return false;
		}
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

		if ( (load_maps && ! mapLoaded) || (! isSmallScreen.matches && tnyGmapsIsMobile && ! mapLoaded ) ) {
			tnygmapsDebug( mapLocation.debug, "Tny gMaps: initialize map" );
			jQuery( "#" + mapID ).css( "height", mapLocation.h ); // set the map height
			jQuery( "#" + mapID + "> .tnygmps_staticimg" ).hide();
			jQuery( "#" + mapID + "> .tnygmps_static_bubble" ).hide();
			// Set map loaded class
			jQuery( this ).addClass( 'mapLoaded' );
			initialize( mapID, mapLocation );
		} else {
			tnygmapsDebug( mapLocation.debug, "Tny gMaps: DOM current width: '" + document.documentElement.clientWidth + "px'." );
			tnygmapsDebug( mapLocation.debug, "Tny gMaps: isSmallScreen flag: '" + isSmallScreen + "'" );
			tnygmapsDebug( mapLocation.debug, "Tny gMaps: tnyGmapsIsMobile flag: '" + tnyGmapsIsMobile + "'" );
			tnygmapsDebug( mapLocation.debug, "Tny gMaps: mapLoaded flag: '" + mapLoaded + "'" );
			jQuery( "#" + mapID ).css( "height", "auto" );
		}
	} );
}

/**
 * On DOM Ready, init the map.
 *
 * @global jQuery
 * @global tnyGmapsIsMobile
 */
jQuery( function () {
	initTest( tnyGmapsIsMobile );
} );
/**
 * On Screen Orientation Change, init the map.
 *
 * @global tnyGmapsIsMobile
 *
 */
window.addEventListener( "orientationchange", function () {
	initTest( tnyGmapsIsMobile );
}, false );