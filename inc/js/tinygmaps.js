//http://codepen.io/anon/pen/zGxxaQ

function initialize(map_id, map_loc) {
    console.log ('init');

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

    // Centering on window resize
    google.maps.event.addDomListener(window, "resize", function(){
        var center = map_id.getCenter();
        google.maps.event.trigger(map_id, "resize");
        map_id.setCenter(center);
    });
}

google.maps.event.addDomListener(window, "resize", function(map_id, map_loc){
    if(document.documentElement.clientWidth > map_loc.static) {
        jQuery('.tnygmps_canvas').each(function() {
            var map_id = jQuery( this ).attr('id');
            var map_loc = window[map_id + "_loc"]; // our localised scope
            // Is the window large enough?
            if(document.documentElement.clientWidth > map_loc.static) {
                initialize(map_id, map_loc);
            }
        });
    }
});


// Initialise on dom ready
jQuery(document).ready(function(jQuery) {
    jQuery('.tnygmps_canvas').each(function() {
    var map_id = jQuery( this ).attr('id');
    var map_loc = window[map_id + "_loc"]; // our localised scope
        // Is the window large enough?
        if(document.documentElement.clientWidth > map_loc.static) {
            initialize(map_id, map_loc);
        }
    });
});

