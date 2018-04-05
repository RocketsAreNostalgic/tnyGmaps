//http://codepen.io/anon/pen/zGxxaQ

function initialize(map_id, map_loc) {
    // an attribute added by google api after init has already been called
    var attr = jQuery('#'+map_id).attr('jstcache');
    // prevent init from being called on maps already drawn
    if (typeof attr === typeof undefined || attr === false || attr === "" ) {
        console.log('init');

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
        google.maps.event.addListenerOnce(map_id, 'tilesloaded', function () {
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
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.open(map_id, marker);
            });
        });

        google.maps.event.addDomListener(window, "resize", function () {
            // Centering on window resize
            var center = map_id.getCenter();
            google.maps.event.trigger(map_id, "resize");
            map_id.setCenter(center);
            console.log('resize center');

        });
    }
}

// Initialise on dom ready
/**
 * On Dom Ready, test if screen is large enough and init map
 * if not, adjust local css
 */
jQuery(document).ready(function(jQuery) {
    jQuery('.tnygmps_canvas').each(function () {
        var map_id = jQuery(this).attr('id');
        var map_loc = window[map_id + "_loc"]; // our the global var for the map
        console.log('map found');
        if (document.documentElement.clientWidth > map_loc.static) {
            console.log('dom loaded & large enough so init map');
            jQuery('#' + map_id).css('height', map_loc.h); // set the map height
            jQuery('#' + map_id +'> .tnygmps_staticimg').hide();
            jQuery('#' + map_id +'> .tnygmps_static_bubble').hide();
            initialize(map_id, map_loc);
        } else {
            console.log('screen too small');
            jQuery('#'+map_id).css('height', 'auto');
        }
    });
});

/**
 * On resize, load map if screen is large enough, also set container height for googlemaps api
 */
google.maps.event.addDomListener(window, "resize", function(){
    jQuery('.tnygmps_canvas').each(function () {
        var map_id = jQuery(this).attr('id');
        var map_loc = window[map_id + "_loc"]; // our the global var for this map
        if (document.documentElement.clientWidth > map_loc.static) {
            jQuery('#' + map_id).css('height', map_loc.h); // set the map height
            initialize(map_id, map_loc);
            console.log('dom resize and large enough so init map');
        }
    });
});