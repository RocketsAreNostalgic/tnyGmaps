/**
 * Logging function
 *
 * @param debug
 * @param message
 */
function tnygmaps_debug (debug, message){
    if (debug === "1") {
        console.log(message);
    }
}

/** 
 * Initialize map from jquery ready event, or google dom resize event listeners.
 *
 * @param map_id
 * @param map_loc
 */
function initialize(map_id, map_loc) {
    // an attribute added by google api after init has already been called
    var attr = jQuery("#" + map_id).attr("jstcache");
    // prevent init from being called on maps already drawn
    if (typeof attr === typeof undefined || attr === false || attr === "") {
        tnygmaps_debug(map_loc.debug, "init");

		var latlng = new google.maps.LatLng( map_loc.lat, map_loc.lng );
		var mapTypeId = map_loc.maptype;
		var mapOptions = {
			zoom: parseInt( map_loc.z ),
			mapTypeId: google.maps.MapTypeId[mapTypeId],
			center: latlng,
			scrollwheel: map_loc.scrollwheel,
			scaleControl: map_loc.scaleControl,
			disableDefaultUI: map_loc.hidecontrols,
			gestureHandling: 'cooperative'
		};
		// Start the map
		map_id = new google.maps.Map( document.getElementById( map_id ), mapOptions );

        // Load the icon and bubble
        google.maps.event.addListenerOnce(map_id, "tilesloaded", function () {
            var marker = new google.maps.Marker({
                map: map_id,
                position: map_id.getCenter(),
                icon: map_loc.marker
            });
            var infowindow = new google.maps.InfoWindow({
                content: map_loc.infowindow,
                position: latlng
            });
            infowindow.open(map_id, marker);
            google.maps.event.addListener(marker, "click", function () {
                infowindow.open(map_id, marker);
            });
        });

        google.maps.event.addDomListener(window, "resize", function () {
            // Centering on window resize
            var center = map_id.getCenter();
            google.maps.event.trigger(map_id, "resize");
            map_id.setCenter(center);
            tnygmaps_debug(map_loc.debug, "resize center");
        });
    }
}

/**
 *
 * On Dom Ready init the map, if both the screen is large enough and we're not on a mobile device
 * If we're not big enough and on mobile, adjust the container css
 */
jQuery(function() {
    // https://stackoverflow.com/a/10364620
    var isMobile = /Mobi/i.test(navigator.userAgent) || /Anroid/i.test(navigator.userAgent);

    jQuery('.tnygmps_canvas').each(function () {
        var map_id = jQuery(this).attr("id");
        var map_loc = window[map_id + "_loc"]; // our global var array for this map
        var isSmallScreen = window.matchMedia("only screen and (max-width: " + map_loc.static_DOM_width + "px)");
        var load_maps = false;
        if (isSmallScreen) {
            load_maps =  (!isMobile ? true : false);
        }
        tnygmaps_debug(map_loc.debug, "Tny gMaps: isSmallScreen ='" + isSmallScreen.matches + "'");
        tnygmaps_debug(map_loc.debug, "Tny gMaps: isMobile ='" + isMobile + "'");
        tnygmaps_debug(map_loc.debug, "Tny gMaps: DOM breakpoint: " + map_loc.static_DOM_width + "' .");
        tnygmaps_debug(map_loc.debug, "map found");

        if ( load_maps ) {
            tnygmaps_debug(map_loc.debug, "Tny gMaps: DOM width larger then '" + map_loc.static_DOM_width +"' so initialize map");
            jQuery("#" + map_id).css("height", map_loc.h); // set the map height
            jQuery("#" + map_id + "> .tnygmps_staticimg").hide();
            jQuery("#" + map_id + "> .tnygmps_static_bubble").hide();
            initialize(map_id, map_loc);
        } else if (!isSmallScreen.matches && isMobile ){
            // if it is mobile, but is a large enough screen run it anyway
            jQuery("#" + map_id).css("height", map_loc.h); // set the map height
            jQuery("#" + map_id + "> .tnygmps_staticimg").hide();
            jQuery("#" + map_id + "> .tnygmps_static_bubble").hide();
            initialize(map_id, map_loc);
        } else {
            tnygmaps_debug(map_loc.debug, "Tny gMaps: DOM current width: '" + document.documentElement.clientWidth + "px'.");
            jQuery("#" + map_id).css("height", "auto");
        }
    });
});
