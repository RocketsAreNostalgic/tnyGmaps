Tny gMaps Plugin
---



This WordPress plugin integrates Google Maps into your posts and pages using the Google Maps API v3.

The plugin requires a Google API key, with the following services enabled: 
   - Google Maps JavaScript API
   - Static Maps API
   - Geocoding API
   - Places API

The api key is enabled site wide through the Tny gMaps options page available to admins in the WP dashboard.
For more informaion see: https://console.developers.google.com

### Short code generator
The plugin adds a button to the tinymce editor in posts and pages, which provides a pop-up map builder.
The map builder provides a live preview of what your map will look like, and once you are satisfied, it adds a custom shortcode into your post body. 



### Support for multiple maps
The plugin seeds each map with a custom identifier so more than one map can be displayed on a page without conflict.

### The preview features:
- live ajax address lookup - or manual address input. 
Rich set of parameters including: 
- Map size (pixels and percent)
- zoom level
- map type (ROADMAP, SATELLITE, HYBRID, TERRAIN)
- Full parameters listed below
- custom icons (You can include your own)
- custom info bubble input
- Extensive markup validation for correct syntax in info bubbles
- Advanced caching prevents repetitive calls to the Google API leveraging the WP transients API 

### Shortcode usage:
```
    // With google place_ID which is gathered using the shortcode builder
    [tnygmaps z="15" w="100%" h="450px" 
        placeid="ChIJ8XNL0uFzhlQRPXBQZ66Rfx4"
        marker="//url/to/map/icon/music_folk_map.png"]
```
```
    // Without place_ID, manually entered address
    [tnygmaps z="15" w="100%" h="450px" 
        name="Nice Cafe" 
        street="East 8th Avenue" 
        city="Vancouver" 
        region="BC" 
        postcode="V5T 1R7" 
        country="Canada" 
        web="https://plus.google.com/113856077276894937079/about" 
        phone="+1 604-874-4024" 
        marker="/url/to/map/icon/music_folk_map.png" 
        infowindow="My Fav Cafe!"]
```
```
    // Less accurate, but will still work
    [tnygmaps z="15" w="100%" h="450px" 
    address="Nice Cafe East 8th Avenue" Vancouver BC V5T 1R7 Canada"]
```
### Shortcode parameter list:
     z                 | Map zoom level 1 - 23
     w                 | Map width in pixels or percent
     h                 | Map height in pixels or percent
     maptype           | Map type: ROADMAP, SATELLITE, HYBRID, TERRAIN
     lat               | Location latitude
     lng               | Location longitude
     placeID           | A Google Places API reference if provided one.
     address           | An address to the location as a string
     name              | Business or location name
     street            | Street number and street name
     city              | City Name
     region            | State or Provence
     postcode          | Zip or regional post code
     country           | Country code, long or short
     web               | URL inclusive of http://
     phone             | Phone Number
     icon              | An image from the maps api for that location
     marker            | A url full or relative to the plugin (see example) to the icon png 
     default_marker    | (true : false) Use the global default marker set in the plugin options pannel 
     infowindowdefault | (yes : no) Show the infowindow on page load, or keep it hidden until the map icon is clicked. 
     infowindow        | Additional contents of the infowindow, but must be text only without any markup.
     infowindowb64     | Additional contents of the infowindow base 64 encoded so complex additional markup won't break the reading of the shortcode by WordPress.
     hidecontrols      | (true : false) Hides the zoom, street view and other controls
     scale             | (true : false) Is the map scale drawn?
     scrollwheel       | (true : false) Will the map zoom react to mouse scrollwheel?
     static            | DOM width for when a static image map should be drawn instead of a dynamic maps for small screens, empty or '0' will indicate no map is drawn
     static_w          | Width of static map in pixels
     static_h          | Height of of static map in pixels
     refresh           | (true : false) Will flush any cashed WP transient data for a given location (good for purging previous results during testing)
     debug             | (true : false) Will render the return values from the Google Maps API object for debugging

### Shoulders of Giants: 
Tny gMaps is indebted to the following projects:

* Bootstrap: https://getbootstrap.com/ [MIT]
* Image Picker: https://github.com/rvera/image-picker/ [MIT]
* jQuery Base64: https://github.com/carlo/jquery-base64/ [MIT]
* jQuery HTML Clean: https://code.google.com/archive/p/jquery-clean/ [3-Clause BSD]
* jQuery qTip2: http://qtip2.com/ [MIT GPL]
* Map Icons Collection: https://mapicons.mapsmarker.com/about/license/ [CC BY SA 3.0]
* Spin JS: https://spin.js.org/ [MIT]