//http://codepen.io/anon/pen/zGxxaQ

/**
 * Common logging function
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

        var latlng = new google.maps.LatLng(map_loc.lat, map_loc.lng);
        var mapTypeId = map_loc.maptype;
        var mapOptions = {
            zoom: parseInt(map_loc.z),
            mapTypeId: google.maps.MapTypeId[mapTypeId],
            center: latlng,
            scrollwheel: map_loc.scrollwheel,
            scaleControl: map_loc.scaleControl,
            disableDefaultUI: map_loc.disableDefaultUI
        };
        // Start the map
        map_id = new google.maps.Map(document.getElementById(map_id), mapOptions);

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

// Initialise on dom ready
/**
 * On Dom Ready, test if screen is large enough and init map
 * if not, adjust local css
 */
jQuery(function(jQuery) {
    jQuery('.tnygmps_canvas').each(function () {
        var map_id = jQuery(this).attr("id");
        var map_loc = window[map_id + "_loc"]; // our global var array for this map
        tnygmaps_debug(map_loc.debug, "map found");

        if (document.documentElement.clientWidth > map_loc.static_DOM_width) {
            tnygmaps_debug(map_loc.debug, "dom loaded & large enough so init map");
            jQuery("#" + map_id).css("height", map_loc.h); // set the map height
            jQuery("#" + map_id + "> .tnygmps_staticimg").hide();
            jQuery("#" + map_id + "> .tnygmps_static_bubble").hide();
            initialize(map_id, map_loc);
        } else {
            tnygmaps_debug(map_loc.debug, "jQuery: screen too small");
            tnygmaps_debug(map_loc.debug, map_loc.static_DOM_width);
            jQuery("#" + map_id).css("height", "auto");
        }
    });
});

/**
 * On resize, load map if screen is large enough, also set container height for googlemaps api
 */
google.maps.event.addDomListener(window, "resize", function(){
    jQuery('.tnygmps_canvas').each(function () {
        var map_id = jQuery(this).attr("id");
        var map_loc = window[map_id + "_loc"]; // our the global var array for this map
        if (document.documentElement.clientWidth > map_loc.static_DOM_width) {
            jQuery("#" + map_id).css("height", map_loc.h); // set the map height
            initialize(map_id, map_loc);
            tnygmaps_debug(map_loc.debug, "dom resize and large enough so init map");
        } else {
            tnygmaps_debug(map_loc.debug, "GOOGLE: dom resize too small to init map");
            tnygmaps_debug(map_loc.debug, "GOOGLE: map_loc.static_DOM_width:");
            tnygmaps_debug(map_loc.debug, "document.documentElement.clientWidth");
            tnygmaps_debug(map_loc.debug, document.documentElement.clientWidth);
        }
    });
});

