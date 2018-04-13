// var parent.tnygmaps.haveGPlaces_key;     // provided global
// var parent.tnygmaps.pluginURI;           // provided global
// var parent.tnygmaps.plugin_icons_URL;    // provided global
// var tnygmaps_api (bool) // api key test   // provided global
// var spinner = new Spinner(opts).spin(target); // provided global

var lat = null;
var lng = null;
var mapZoomReturn = null;
var mapWidthReturn = null;
var mapHeightReturn = null;
var mapTypeReturn = null;
var mapControlsReturn = null;
var mapAddressReturn = null;
// var mapAddressElement = null;
var locGooglePlaceID = null;
var mapMarkerReturn = null;
var mapMarkerImageReturn = null;
// var infowindow = null;
var infowindowPlace = null;
var mapInfoWindowReturn = null;
// var combinedInfoWindow = null;
var tnyGmapsAssembleShortcode = null;
var map = null;
var marker = null;
var markerImage = null;
var mapOptions = null;
var mapCurrCenter = null;
var markerOutput = null;
var locPlace = null;
var markerCustom = parent.tnygmaps.custom_icon;
var locName = null;
var locStreet = null;
var locStreetNum = null;
var locCity = null;
var locRegion = null;
var locPostcode = null;
var locCountry = null;
var locWeb = null;
var locPhone = null;
var locIcon = null;
var locAddress = null;
var geocoder = null;

/**
 * Enable trim in older browsers
 *
 * @author orionrush
 * @since 0.0.2
 */
if (!String.prototype.trim) {
    String.prototype.trim = function () {
        "use strict";
        return this.replace(/^\s+|\s+$/g,"");
    };
}

/**
 * Retrieve the marker image
 *
 * @author orionrush
 * @since 0.0.2
 *
 * @returns {default marker, or custom marker as string}
 */
function get_marker_image() {
    mapMarkerReturn = jQuery("select[id=mapMarker]").val();
    switch (mapMarkerReturn) {
        case "default":
            mapMarkerImageReturn = markerCustom;
            break;
        case "google":
            mapMarkerImageReturn = ""; // will fallback to Google marker icon
            break;
        case "custom":
            mapMarkerImageReturn = jQuery("#mapMarkerImage").val();
            break;
    }
    //console.log("Marker image: " + mapMarkerImageReturn);
    return mapMarkerImageReturn;
}

/**
 * Populate script variables with field values
 *
 * @author orionrush
 * @since 0.0.2
 *
 * @uses get_marker_image()
 */
function seed_vars() {
    lat = jQuery("input#mapLat").val();
    lng = jQuery("input#mapLng").val();
    //console.log("lat-lng: " + lat + " " + lng);
    mapZoomReturn = parseInt(jQuery("select[id=mapZoom]").val(), 10);
    //console.log("mapZoomReturn: " + mapZoomReturn);
    mapWidthReturn = jQuery("input[id=mapWidth]").val();
    //console.log("mapWidthReturn: " + mapWidthReturn);
    mapHeightReturn = jQuery("input[id=mapHeight]").val();
    //console.log("mapHeightReturn: " + mapHeightReturn);
    mapTypeReturn = jQuery("select[id=mapType]").val();
    //console.log("mapTypeReturn: " + mapTypeReturn);
    locGooglePlaceID = jQuery("input#locGooglePlaceID").val();
    //console.log("locGooglePlaceID: " + locGooglePlaceID);
    mapAddressReturn = jQuery("input[id=mapAddress]").val();
    //console.log("mapAddressReturn: " + mapAddressReturn);
    locName = jQuery("input#locName").val();
    //console.log("locName: " + locName);
    locPhone = jQuery("input#locPhone").val();
    //console.log("locPhone: " + locPhone);
    locStreet = jQuery("input#locStAdr").val();
    //console.log("locStreet: " + locStreet);
    locCity = jQuery("input#locCity").val();
    //console.log("locCity: " + locCity);
    locRegion = jQuery("input#locRegion").val();
    //console.log("locRegion: " + locRegion);
    locPostcode = jQuery("input#locPostcode").val();
    //console.log("locPostcode: " + locPostcode);
    locCountry = jQuery("input#locCountry").val();
    //console.log("locCountry: " + locCountry);
    locWeb = jQuery("input#locWebsite").val();
    //console.log("locWeb: " + locWeb);
    locIcon = "";
    locIcon = jQuery("input#locIconURL").val();
    //console.log("locIcon: " + locIcon);
    mapMarkerImageReturn = get_marker_image();
    //console.log("mapMarkerImageReturn: " + mapMarkerImageReturn);

    //console.log("mapInfoWindowReturn: " + mapInfoWindowReturn);
    mapControlsReturn = jQuery("select[id=mapControls]").val() === "true";
    //console.log("mapControlsReturn: " + mapControlsReturn);
}

/**
 * Some preliminary sanitation, before the string is encoded.
 * The shortcode will of course clean things before output as well.
 *
 * @author orionrush
 * @since 0.0.2
 *
 * @param str
 */
function clean_html(str) {
    str = String(str).replace(/'/g, '"');
    return jQuery.htmlClean(str, {
        removeTags: ["script"],
        allowedAttributes: [["style"], ["href", ["a"]], ["target", ["a"]], ["title", ["a"]]],
        allowedTags: ["p", "a", "span", "ul", "ol", "li", "br", "hr", "em", "strong"],
        format: false
    });
}

/**
 *  Process Address Components, and retrieve the long_name
 *
 * @param needle
 * @param haystack
 * @returns {string}
 */
function processAddressObject(needle, haystack) {
    var rtrn = "";
    for (var i = 0; i < haystack.length; i++) {
        var addr = haystack[i];
        rtrn += (addr.types[0] === needle ? addr.long_name : '');
    }
    return rtrn;
}



/**
 * User isn"t using auto-complete
 * so clean the inputs fields so we can reverse lookup the address and cache the coordinates.
 *
 * @author orionrush
 * @since 0.0.2
 *
 */
function custom_location() {
    jQuery("#mapAddress").val(""); 	// empty autocomplete field
    jQuery("#locGooglePlaceID").val(""); 	// empty location ref
    jQuery("#locIconURL").val(""); 	// empty icon
    jQuery("#mapLat").val(""); 		// empty coordinates
    jQuery("#mapLng").val("");
}

/**
 * Initialize the map
 *
 * @author orionrush
 * @since 0.0.2
 *
 * @uses setFocus()
 * @uses generateMap();
 *
 * @param infowindow
 */
function initialize(infowindow) {
    /*
     * Form UX
     * We put this here so that we have access to map handlers
     *
     * jQuery Document ready
     * @uses setFocus()
     * @uses generateMap();
     *
     */
    jQuery(function ($) {
        // Test to see if the api key is loaded.
        if (tnygmaps_api === false) {
            jQuery(".accordion").hide().prop("disabled", true);
            return; // nope it isn't, so don't bother with the rest.
        }

        // Prevent the return key from submitting the form too soon
        jQuery(".noEnterSubmit").keypress(function (e) {
            if (e.which === 13) {
                return false;
            }
        });
        // Set focus
        setFocus(jQuery(this));
        // Accordion
        jQuery(".accordion").hide();
        jQuery(".heading").click(function () {
            // close it if its open
            if (jQuery(this).next().find(".accordion").hasClass("open")) {
                jQuery(this).next().find(".open").removeClass("open active").slideUp(function () {
                    jQuery("#button-dialog form").find("input:text").first().trigger("focus");
                });

                return;
            }
            // close all others
            jQuery(".heading").not(this).next(".group").find(".accordion").slideUp(function () {
                jQuery(this).removeClass("open active");
                setFocus(jQuery(this));
            });
            // open the clicked one if its closed
            jQuery(this).next().find(".accordion").slideDown(400, function () {
                setFocus(jQuery(this));
                jQuery(this).addClass("open active");
            });
        });
        // Custom Location details & regulate access to auto lookup field
        jQuery("input[name='custom_use_address_check']").click(function () {
            if (jQuery(this).is(":checked")) {
                jQuery("#mapAddress-group").slideUp(400, function () {
                    jQuery("#address_extras").find("input:text, button").addClass("active highlight").prop("disabled", false).delay(700).show(1, function () {
                        jQuery(this).removeClass("highlight");
                    });
                    jQuery("#locName").trigger("focus");
                });
            }
            if (jQuery(this).is(":checked") === false) {
                jQuery("#mapAddress-group").slideDown();
                jQuery("#address_extras").find("input:text, button").prop("disabled", true).val("").removeClass("active");
                // disable and clear
                jQuery(".autoMapAddress").parent().slideDown(400, function () {
                    setFocus(jQuery(this));
                    generateMap();
                });
            }
        });
        if (parent.tnygmaps.haveGPlaces_key === false) {
            jQuery("#tnygmaps.haveGPlaces_key").remove();
            jQuery("#address_extras").show();
            jQuery("#address_extras").find("input:text, button").prop("disabled", false);
        } else {
            jQuery("#address_extras").find("input:text, button").prop("disabled", true);
        }
        // Conditional display of custom map icon url
        jQuery(".mapMarkerImage_wrap").hide();
        jQuery("#mapMarker").change(function () {
            var selected = $("#mapMarker option:selected").text();
            if (selected === "Custom") {
                jQuery(".mapMarkerImage_wrap").slideDown();
                setFocus(jQuery(this));
            } else {
                jQuery(".mapMarkerImage_wrap").slideUp();
            }
        });
        // Clear fields
        jQuery("button[id='clear-fields']").click(function () {
            if (jQuery("#search-report").is(":animated")) {
                return false;
            } else {
                jQuery("#address_extras :input:not(input:checkbox)", "div").val([]);
                //console.log("fields cleared");
                updateAlert("Cleared!", "confirm");
            }
        });
        // Icon list interaction
        jQuery(".map-icon").click(function (e) {
            e.preventDefault();
            var icon = jQuery(this).attr("title");
            jQuery("input#mapMarkerImage").val(parent.tnygmaps.plugin_icons_URL + icon);
            update_marker();
        });
        // Notice
        jQuery(".fadeout").delay(2000).fadeOut(800);
        //Tool Tip
        jQuery("[data-tooltip !='']").qtip({
            position: {
                my: "top-left"
            },
            content: {
                attr: "data-tooltip"
            }
        });

        // remove the overlay
        removeOverlay();

    });

    /**
     * Set focus on first input or text area.
     *
     * @author orionrush
     * @since 0.0.2
     *
     * @param obj
     */
    function setFocus(obj) {
        jQuery(obj).find("input, textarea").first()
        ;
    }


    /**
     * Updates the alert popup with any new values, and fades it in/out.
     *
     * @author orionrush
     * @since 0.0.2
     *
     * @param string
     * @param alertClass
     */
    function updateAlert(string, alertClass) {
        alertClass = alertClass + " alert";
        jQuery("#search-report").each(function () {
            jQuery(this).addClass(alertClass);
            jQuery(this).width((
                jQuery(this).parent().width() - 15
            ));
            jQuery(this).append(string);
            jQuery("#search-report").fadeIn(800).delay(300).fadeOut(800, function () {
                jQuery(this).removeClass(alertClass).empty();
            });
        });
    }

    /**
     * Assemble the infowindow interior
     *
     * @author orionrush
     * @since 0.0.2
     *
     * @param icon
     * @param name
     * @param street
     * @param city
     * @param state
     * @param post
     * @param country
     * @param phone
     * @param web
     * @param info
     * @returns {*}
     */
    function get_info_bubble(icon, name, street, city, state, post, country, phone, web, info) {
        var iconStyle = (
            (
                icon !== ""
            ) ? "max-width: 150px; " : "max-width: 200px; "
        );
        infowindowPlace = '<div class="marker-inside"  style="hight:auto;" >';
        infowindowPlace += "<b>" + name + "</b>";
        infowindowPlace += "<table>";
        infowindowPlace += "<tbody>";
        infowindowPlace += "<tr>";
        infowindowPlace += "<td>";
        infowindowPlace += '<div class="infowidow-address" style="' + iconStyle + '" >';
        infowindowPlace += (
            street !== null && street !== undefined && street !== ""
        ) ? "<div>" + street + "</div>" : "";
        infowindowPlace += (
            city !== null && city !== undefined && city !== ""
        ) ? "<div>" + city + ", " : "<div>";
        infowindowPlace += (
            state !== null && state !== undefined && state !== ""
        ) ? state + "</div>" : "</div>";
        infowindowPlace += (
            post !== null && post !== undefined && post !== ""
        ) ? "<div>" + post + "</div>" : "</div>";
        infowindowPlace += (
            country !== null && country !== undefined && country !== ""
        ) ? "<div>" + country + "</div>" : "</div>";
        infowindowPlace += (
            phone !== null && phone !== undefined && phone !== ""
        ) ? "<div>" + phone + "</div>" : "";
        infowindowPlace += (
            web !== null && web !== undefined && web !== ""
        ) ? '<div style="max-width: 100%; white-space: nowrap; width: 100%; overflow: hidden;  -o-text-overflow: ellipsis;  text-overflow: ellipsis;"><a href="' + web + '" class="gmap_link" target="_blank" style="">' + web + '</a></div>' : "";
        infowindowPlace += "</div>";
        infowindowPlace += "</td>";
        infowindowPlace += "<td>";
        infowindowPlace += ((icon !== null && icon !== undefined) ? '<img src="' + icon + '" class="marker-icon" style="margin: 0 5px 15px 5px; width: 60px; height: auto; " />' : "");
        infowindowPlace += "</td>";
        infowindowPlace += "</tr>";
        infowindowPlace += "</tbody>";
        infowindowPlace += "</table>";
        infowindowPlace += "</div>";
        infowindowPlace += (
            info !== null && info !== undefined && info !== ""
        ) ? '<div class="marker-extras" style="border-top: 1px dotted #949494; margin-top: 5px; max-width: 265px; min-height: 40px; overflow: hidden; white-space: pre-wrap;" >' + mapInfoWindowReturn + '</div>' : "";
        return infowindowPlace;
    }


    /**
     * Generate a fresh map, and adds event listeners to detect when settings change.
     *
     * @author orionrush
     * @since 0.0.2
     *
     * @uses seed_vars()
     * @uses updateMapType()
     * @uses get_info_bubble()
     * @uses addListeners()
     *
     */
    function generateMap() {
        var initCenter;
        seed_vars();
        initCenter =  ( lat !== "" && lng !== "" ? new google.maps.LatLng(lat, lng) : new google.maps.LatLng(43.703793, -72.326187) );
        mapOptions = {
            center: initCenter,
            zoom: mapZoomReturn,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false,
            streetViewControl: false,
            panControl: false,
            disableDefaultUI: mapControlsReturn
        };
        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
        updateMapType(); // needed for when we cycle controls on/off
        marker = new google.maps.Marker({
            position: initCenter,
            map: map,
            icon: mapMarkerImageReturn
        });
        infowindowPlace = get_info_bubble(locIcon, locName, locStreet, locCity, locRegion, locPostcode, locCountry, locPhone, locWeb, mapInfoWindowReturn);
        infowindow.setContent(infowindowPlace);
        if (locName !== "") {
            infowindow.open(map, marker);
        }
        google.maps.event.addListener(marker, "click", function () {
            if (locName !== "") {
                infowindow.open(map, marker);
            }
        });

        // Add event listeners
        addListeners();
        //console.log("map created");
    }

    /**
     * Set up event listeners
     *
     * @author oronrush
     * @since 0.0.2
     *
     * @uses custom_location()
     * @uses updateCustomlocation()
     * @uses updateMapInfoWindow()
     * @uses updateDomMapType()
     * @uses generateMap()
     * @uses openInfoWindow()
     * @uses updateMapCustomDeets()
     * @uses update_marker()
     * @uses upadateMapFromDropdownZoomChange()
     * @uses updateMapAutocomplete()
     * @uses addMarkerClickHandler()
     */
    function addListeners() {
        // Custom Location, here we strip out any refrences to Auto lookup if any of these feilds are modified
        google.maps.event.addDomListener(document.getElementById("locName"), "change", custom_location);
        google.maps.event.addDomListener(document.getElementById("locPhone"), "change", custom_location);
        google.maps.event.addDomListener(document.getElementById("locStAdr"), "change", custom_location);
        google.maps.event.addDomListener(document.getElementById("locCity"), "change", custom_location);
        google.maps.event.addDomListener(document.getElementById("locRegion"), "change", custom_location);
        google.maps.event.addDomListener(document.getElementById("locPostcode"), "change", custom_location);
        google.maps.event.addDomListener(document.getElementById("locCountry"), "change", custom_location);
        google.maps.event.addDomListener(document.getElementById("locWebsite"), "change", custom_location);

        // Custom location button
        google.maps.event.addDomListener(document.getElementById("lookup-detials"), "click", updateCustomlocation);

        // Custom location update infowindow
        google.maps.event.addDomListener(document.getElementById("map-update"), "click", updateMapInfoWindow);

        // Controls DOM->MAP
        google.maps.event.addDomListener(document.getElementById("mapControls"), "change", generateMap);

        // Map Type DOM ->MAP
        google.maps.event.addDomListener(document.getElementById("mapType"), "change", updateMapType);

        // Map Type MAP ->DOM
        google.maps.event.addListener(map, "maptypeid_changed", updateDomMapType);

        // Info window DOM->MAP
        google.maps.event.addDomListener(document.getElementById("mapInfoWindow"), "change", updateMapInfoWindow);
        google.maps.event.addDomListener(document.getElementById("mapInfoWindow"), "click", openInfoWindow);
        setInterval(function () {
            updateMapCustomDeets(jQuery("#mapInfoWindow").val());
        }, 100);

        // Marker selection DOM->MAP
        google.maps.event.addDomListener(document.getElementById("mapMarker"), "change", update_marker);

        // Custom marker image DOM->MAP
        google.maps.event.addDomListener(document.getElementById("mapMarkerImage"), "change", update_marker);

        // Zoom DOM->MAP
        google.maps.event.addDomListener(document.getElementById("mapZoom"), "change", upadateMapFromDropdownZoomChange);

        // Zoom MAP->DOM
        google.maps.event.addListener(map, "zoom_changed", updateDropdownFromMapZoomChange);

        // Place Loop DOM -> MAP
        google.maps.event.addListener(autocomplete, "place_changed", updateMapAutocomplete);

        // Click handler for marker & infowindow
        addMarkerClickHandler();
    }

    /**
     * Removes the overlay and spinner from the modal
     *
     * @author orionrush
     * @since 0.0.3
     */
    function removeOverlay() {
        // Overlay
        if (tnygmaps_api === false) {
            // Remove spinner, leave overlay
            spinner.stop();
        } else {
            // Remove spinner and overlay
            spinner.stop();
            jQuery("div#overlay").fadeOut(300, function () {
                jQuery(this).remove();
            });
        }
    }

    /**
     * Adds a listener to the marker onclick and opens it only if there is content.
     *
     * @author orionrush
     * @since 0.0.2
     *
     * @global google.map
     */
    function addMarkerClickHandler() {
        // open the info window on marker click if there is one
        google.maps.event.addListener(marker, "click", function () {
            if (mapInfoWindowReturn !== "") {
                infowindow.open(map, marker);
            } // only if it has something in it
        });
    }

    /**
     * Updates the Map zoom based on the dropdown value.
     *
     * @author orionrush
     * @since 0.0.2
     *
     * @global map
     */
    function upadateMapFromDropdownZoomChange() {
        mapZoomReturn = parseInt(jQuery("select[id=mapZoom]").val(), 10);
        mapCurrCenter = map.getCenter(); // center on present location
        map.setZoom(mapZoomReturn);
        map.setCenter(mapCurrCenter);
        // console.log("update Map from Dropdown Zoom Change");
    }

    /**
     * Update zoom dropdown when map zoom changes
     *
     * @author orionrush
     * @since 0.0.2
     *
     * @global map
     */
    function updateDropdownFromMapZoomChange() {
        var zoomLevel = map.getZoom();
        mapCurrCenter = map.getCenter(); // center on present location
        map.setCenter(mapCurrCenter);
        jQuery("select[id=mapZoom] option").filter(function () {
            return (
                jQuery(this).text() === zoomLevel
            );
        }).prop("selected", true);
        // console.log("update Dropdown from Map Zoom Change");
    }

    /**
     * Update the map type when the user changes the dropdown menu
     *
     * @author orionush
     * @since 0.0.2
     *
     * @global map
     */
    function updateMapType() {
        mapTypeReturn = jQuery("select[id=mapType]").val();
        //map.setMapTypeId(mapTypeReturn);
        if (mapTypeReturn === "ROADMAP") {
            map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
        } else if (mapTypeReturn === "SATELLITE") {
            map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
        } else if (mapTypeReturn === "HYBRID") {
            map.setMapTypeId(google.maps.MapTypeId.HYBRID);
        } else if (mapTypeReturn === "TERRAIN") {
            map.setMapTypeId(google.maps.MapTypeId.TERRAIN);
        }
    }

    /**
     * Updates the map type drop down, when the users changes the map-type from the controls in the map
     *
     * @author orionush
     * @since 0.0.2
     *
     * @global map
     */
    function updateDomMapType() {
        var mapType = map.getMapTypeId();
        mapType = mapType.toUpperCase();
        jQuery("select[id=mapType] option").filter(function () {
            return (
                jQuery(this).text() === mapType
            );
        }).prop("selected", true);
    }

    /**
     * Updates the map info window bubble with the loaded data
     *
     * @author orionush
     * @since 0.0.2
     *
     * @uses seed_vars()
     * @uses clean_html()
     * @uses get_info_bubble()
     *
     * @global mapInfoWindowReturn
     * @global infowindowPlace
     * @global infowindow
     *
     */
    function updateMapInfoWindow() {
        seed_vars();
        mapInfoWindowReturn = jQuery("textarea#mapInfoWindow").val();
        if (mapInfoWindowReturn !== "null" && mapInfoWindowReturn !== "" && mapInfoWindowReturn !== null){
            mapInfoWindowReturn = clean_html(mapInfoWindowReturn);
        } else {
            mapInfoWindowReturn = "";
        }
        infowindowPlace = get_info_bubble(locIcon, locName, locStreet, locCity, locRegion, locPostcode, locCountry, locPhone, locWeb, mapInfoWindowReturn);
        infowindow.setContent(infowindowPlace);
        jQuery("textarea#mapInfoWindow").prop("disabled", true).addClass("highlight alert").delay(700).show(1, function () {
            jQuery(this).val(mapInfoWindowReturn).removeClass("highlight alert").prop("disabled", false);
            jQuery(this)[0].selectionStart = jQuery(this)[0].selectionEnd = jQuery(this).val().length; // reset the cursor
        });
        infowindow.open(map, marker);
    }

    /**
     * Opens the info window, if it isn't empty.
     *
     * @author orionush
     * @since 0.0.2
     *
     * @uses seed_vars()
     * @uses get_info_bubble()
     * @uses clearTimeout()
     * @uses setTimeout()
     * @uses updateMapInfoWindow()
     *
     * @global infowindow
     * @global map
     */
    function openInfoWindow() {
        if (jQuery("#mapInfoWindow").is(":visible")) {
            // Add marker extras

            var markerExtras = jQuery(".marker-extras").length > 0;
            if (!markerExtras) {
                seed_vars();
                infowindowPlace = get_info_bubble(locIcon, locName, locStreet, locCity, locRegion, locPostcode, locCountry, locPhone, locWeb, "...");
                infowindow.setContent(infowindowPlace);
            }
            // add the keystroke timer
            var timer = null;
            jQuery(document).on("keydown", "#mapInfoWindow", function () {
                if (timer) {
                    clearTimeout(timer);
                }
                timer = setTimeout(updateMapInfoWindow, 3000);
            });
        }
        if (!infowindow.open(map, marker)) {
            infowindow.open(map, marker);
        }
    }

    /**
     * Update the map with custom details supplied in the fields.
     *
     * @author orionush
     * @since 0.0.2
     *
     * @uses clean_html()
     *
     * @param value
     */
    function updateMapCustomDeets(value) {
        var content = jQuery(".marker-extras").html();
        if (content !== value) {
            content = clean_html(value);
        }
        jQuery(".marker-extras").html(content);
    }

    /**
     * Updates the map marker pin with a new image.
     *
     * @author orionush
     * @since 0.0.2
     *
     * @uses get_marker_image()
     *
     * @global marker
     * @global locPlace
     * @global geometry
     * @global map
     *
     */
    function update_marker() {
        markerImage = get_marker_image(); // returns URL as string
        marker.setIcon(markerImage);

        if (locPlace !== null) {
            marker.setPosition(locPlace.geometry.location);
        }
        marker.setMap(map);
    }

    /**
     * Update the map after the user selects a place from autocomplete
     *
     * @author orionush
     * @since 0.0.2
     *
     * @uses processAddressObject()
     * @uses trim()
     * @uses generateMap()
     *
     * @global infowindow
     * @global input
     * @global autocomplete
     * @global geometry
     * @global map
     *
     */
    function updateMapAutocomplete() {
        infowindow.close();// close the marker info window
        input.className = "";
        var locPlace = autocomplete.getPlace();
        //console.log(locPlace);
        if (!locPlace.geometry) {
            // Inform the user that the place was not found and return.
            input.className = "notfound";
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
            locName = "";
            locStreetNum = "";
            locStreet = "";
            locCity = "";
            locRegion = "";
            locPostcode = "";
            locCountry = "";
            locIcon = "";
            locPhone = "";
            locWeb = "";
            lat = "";
            lng = "";
            locGooglePlaceID = "";

            // these live at the top level of the object so we can access them easily
            // get the information and set the text field if it exsists, else clear the varriable and the associated field


            // Google's Places Reference ID
            locGooglePlaceID = (locPlace.place_id ? locPlace.place_id : "");
            jQuery("#locGooglePlaceID").val(locGooglePlaceID.trim());
            //Lng & Lat
            lat = locPlace.geometry.location.lat();
            jQuery("#mapLat").val(lat);
            lng = locPlace.geometry.location.lng();
            jQuery("#mapLng").val(lng);
            //Name
            locName = locPlace.name;
            jQuery("#locName").val(locName);
            // Icon
            locIcon = locPlace.icon;
            jQuery("#locIconURL").val(locIcon.trim());
            //Phone
            locPhone = locPlace.formatted_phone_number;
            jQuery("#locPhone").val(locPhone);
            //Website
            locWeb = locPlace.website;
            jQuery("#locWebsite").val(locWeb);

            // Do the same for nested address_components -- iterate through the array, then set the field value
            // Premise or building name
            var locPremise = processAddressObject("premise", locPlace.address_components);
            // street number
            locStreetNum = processAddressObject("street_number", locPlace.address_components);
            // Street name
            locStreet = processAddressObject("route", locPlace.address_components);

            // Set Input
            locPremise = (locPremise ? locPremise + ", " : "");
            var streetCombined = (
                locPremise + locStreetNum + " " + locStreet
            ).trim();
            jQuery("input#locStAdr").val(streetCombined);

            // City-Town
            if (processAddressObject("administrative_area3", locPlace.address_components)) {
                locCity = processAddressObject("administrative_area3", locPlace.address_components);
            }
            if (processAddressObject("locality", locPlace.address_components)) {
                locCity = processAddressObject("locality", locPlace.address_components);
            }
            if (processAddressObject("sublocality", locPlace.address_components)) {
                locCity = processAddressObject("sublocality", locPlace.address_components);
            }
            if (processAddressObject("postal_town", locPlace.address_components)) {
                locCity = processAddressObject("postal_town", locPlace.address_components);
            }
            // Set Input
            jQuery("input#locCity").val(locCity.trim());

            // state
            locRegion = processAddressObject("administrative_area_level_1", locPlace.address_components);
            if (!locRegion) {
                locRegion = processAddressObject("administrative_area_level_2", locPlace.address_components);
            }

            // if we don't have a state but do have a region, use that instead.
            if ((
                    locRegion !== "" || locRegion !== null
                ) && (
                    locRegion === "" || locRegion === null
                )) {
                locRegion = locRegion;
            }
            jQuery("#locCity").val(locCity.trim());
            jQuery("#locRegion").val(locRegion.trim());

            // Postal Code
            locPostcode = processAddressObject("postal_code", locPlace.address_components);
            jQuery("#locPostcode").val(locPostcode);

            locCountry = processAddressObject("country", locPlace.address_components);
            jQuery("#locCountry").val(locCountry);
            locAddress = locStreet + ", " + locCity + ", " + locRegion + ", " + locPostcode + ", " + locCountry;

        }
        generateMap();
    }

    // Here we are not using the places api, but instead geocoding api

    /**
     * Use the Gecoding API to look up a location and interpret results.
     *
     * @author orionush
     * @since 0.0.2
     *
     * @uses custom_location()
     * @uses seed_vars()
     * @uses callGeocode()
     * @uses generateMap()
     *
     * @returns {boolean}
     */
    function updateCustomlocation() {
        if (jQuery("#search-report").is(":animated")) {
            return false;
        } else {
            custom_location(); // clear the custom bits
            seed_vars(); // update the vars with the changes

            // use address fields to create a new address lookup using geocoding api
            locAddress = locName + "+" + locStreet + "+" + locCity + "+" + locRegion + "+" + locPostcode + "+" + locCountry;
            //console.log(locAddress);
            // locStreetNum = "";
            // locRegion = "";
            geocoder = new google.maps.Geocoder();
            callGeocode(function () {
                // update the map and marker
                generateMap();
                infowindow.open(map, marker);
            });
        }
    }

    /**
     * Process the results of the Geocode response.
     *
     * @author orionrush
     * @since 0.0.2
     *
     * @uses updateAlert()
     * @uses processAddressObject()
     * @uses seed_vars()
     *
     *
     * @param callback
     *
     * @global geocoder
     * @global maps
     *
     */
    function callGeocode(callback) {

        var alert = "";

        geocoder.geocode({"address": locAddress}, function (results, status) {
            if (status !== google.maps.GeocoderStatus.OK) {
                alert = "Sorry, try adding more address details: " + status;
                updateAlert(alert, "warning");
            }

            if (status === google.maps.GeocoderStatus.OK) {
                locPlace = results[0];

                // Flush previous values
                locName = "";
                locStreetNum = "";
                locStreet = "";
                locCity = "";
                locRegion = "";
                locRegion = "";
                locPostcode = "";
                locCountry = "";
                locIcon = "";
                // lat = "";
                // lng = "";
                locGooglePlaceID = "";

                // Name of location
                locName = processAddressObject("point_of_interest", locPlace.address_components);
                // have observed this as a return value with geocode api as well
                // Set Input, we don't want to clear the field if these return null
                if (locName !== "" && locName !== null) {
                    jQuery("#locName").val(locName.trim());
                }

                // Premise or building name
                var locPremise = processAddressObject("premise", locPlace.address_components);
                // street number
                locStreetNum = processAddressObject("street_number", locPlace.address_components);
                // Street name
                locStreet = processAddressObject("route", locPlace.address_components);

                // Set Input
                locPremise = (locPremise ? locPremise + ", " : "");

                var streetCombined = (
                    locPremise + locStreetNum + " " + locStreet
                ).trim();
                jQuery("input#locStAdr").val(streetCombined);

                // City-Town
                locCity = processAddressObject("postal_town", locPlace.address_components);
                if (!locCity) {
                    locCity = processAddressObject("locality", locPlace.address_components);
                }
                // Set Input
                jQuery("input#locCity").val(locCity);

                // State - Region
                locRegion = processAddressObject("administrative_area_level_1", locPlace.address_components);
                if (!locRegion) {
                    locRegion = processAddressObject("administrative_area_level_2", locPlace.address_components);
                }
                // Set Input
                jQuery("input#locRegion").val(locRegion);

                // Postal
                locPostcode = processAddressObject("postal_code", locPlace.address_components);
                // Set Input
                jQuery("input#locPostcode").val(locPostcode);

                // Country
                locCountry = processAddressObject("country", locPlace.address_components);
                // Set Input
                jQuery("input#locCountry").val(locCountry);

                // Location Icon
                locIcon = processAddressObject("icon", locPlace.address_components);
                // Set Input
                jQuery("input#locCountry").val(locIcon);

                // Set latitude
                lat = locPlace.geometry.location.lat();
                // Set Input
                jQuery("input#mapLat").val(lat);

                // Set longitude
                lng = locPlace.geometry.location.lng();
                // Set Input
                jQuery("input#mapLng").val(lng);

                alert = "Geocoding successful,  Lng & Lat status: " + status;
                updateAlert(alert, "confirm");
                seed_vars();
                callback();
            }
        });
    }


    /*
     * MAP Init
     */
    var input = document.getElementById("mapAddress"); // Grab the input element
    var autocomplete = new google.maps.places.Autocomplete(input); // instantiate autocomplete
    infowindow = new google.maps.InfoWindow(); // instanitate the info window

    generateMap(); // Draw the map

}

google.maps.event.addDomListener(window, "load", initialize);

/**
 * Encode entities on the client side
 *
 * @author orionrush
 * @since 0.0.2
 *
 * @param str
 * @returns {string}
 */
function htmlEntities(str) {
    // we  convert script tags to spans - also removes single quotes
    // http://css-tricks.com/snippets/javascript/htmlentities-for-javascript/ (see comment from james)
    return String(str).replace(/&amp;/g, "&").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
}



/**
 * load up our field values and output them as a short code
 *
 * @author orionrush
 * @since 0.0.2
 *
 * @uses seed_vars()
 * @uses htmlEntities()
 *
 * @type {{local_ed: string, init: init, insert: insertButton}}
 */
tnyGmapsAssembleShortcode = {
    local_ed: 'ed',
    init: function (ed) {
        tnyGmapsAssembleShortcode.local_ed = ed;
        tinyMCEPopup.resizeToInnerSize();
    },
    insert: function insertButton(ed) {
        // load our input values
        seed_vars();
        // clean user input
        if (mapInfoWindowReturn){
            mapInfoWindowReturn = htmlEntities(mapInfoWindowReturn); // encode html entities
            mapInfoWindowReturn = jQuery.base64.encode(mapInfoWindowReturn); // then base64 encode it as a string
        }
        // output the shortcode
        markerOutput = '[tnygmaps ';
        markerOutput += 'z="' + mapZoomReturn + '" ';
        markerOutput += 'w="' + mapWidthReturn + '" ';
        markerOutput += 'h="' + mapHeightReturn + '" ';
        markerOutput +=  'maptype="' + mapTypeReturn + '" ';
        markerOutput += (mapControlsReturn === true ? 'hidecontrols="true" ' : "");

        if (locGooglePlaceID === null || locGooglePlaceID === "") {
            markerOutput += 'lat="' + lat + '" ';
            markerOutput += 'lng="' + lng + '" ';
            // assemble the address values
            markerOutput += (locName !== "" ? 'name="' + htmlEntities(locName) + '" ' : "");
            markerOutput += (locStreet !== "" ? 'street="' + htmlEntities(locStreet) + '" ' : "");
            markerOutput += (locCity !== "" ? 'city="' + htmlEntities(locCity) + '" ' : "");
            markerOutput += (locRegion !== "" ? 'region="' + htmlEntities(locRegion) + '" ' : "");
            markerOutput += (locPostcode !== "" ? 'postcode="' + htmlEntities(locPostcode) + '" ' : "");
            markerOutput += (locCountry !== "" ? 'country="' + htmlEntities(locCountry) + '" ' : "");
            markerOutput += (locWeb !== "" ? 'website="' + locWeb + '" ' : "");
            markerOutput += (locPhone !== "" ? 'phone="' + htmlEntities(locPhone) + '" ' : "");
            markerOutput += (locIcon !== "" ? 'icon="' + htmlEntities(locIcon) + '" ' : "");
        } else {
            markerOutput += 'placeid="' + locGooglePlaceID + '" ';
        }

        if (parent.tnygmaps.custom_icon === mapMarkerImageReturn) {
            markerOutput += 'default_marker="true" ';
        } else {
            markerOutput += (mapMarkerReturn !== "" && mapMarkerImageReturn !== "" ? 'marker="' + mapMarkerImageReturn + '" ' : "");
        }
        markerOutput += (mapMarkerReturn !== "" && mapInfoWindowReturn !== "" ? 'infowindowb64="' + mapInfoWindowReturn + '" ' : "");

        markerOutput += ']';
        tinyMCEPopup.execCommand('mceReplaceContent', false, markerOutput);
        // Return
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(tnyGmapsAssembleShortcode.init, tnyGmapsAssembleShortcode);