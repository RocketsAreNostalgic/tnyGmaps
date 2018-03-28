
### Tny gMaps Plugin Todo

####_0.0.3 Refactor Namespace, remove prefixes_

* [DONE] apply code style and design patterns established previously
* [DONE] Namespace all files and functions
* [DONE] remove any function prefixes
* [DONE] apply directory structure
* [DONE] normalise comments
* [DONE] code formatting

####_0.0.4 Refactor tinymce Modal_
* Prepare strings for translation.
* Add modal string to js via wp_localize_script()
* Remove the direct dependency on wp by moving constant refs to js vars via wp_localize_script() also
* refactor js scripts to rely on wp_localize_script() globals instead

####_0.0.5 Refactor JS_    
* jQuery removed deprecated functions: load(), live(), ready event, 
* Deprecated TinyMCE API call: windowManager.createInstance(..)
    * http://wordpress.stackexchange.com/q/258920/13551
* Concat js into single lib?
* Do we need to grey out interface with modal and spinner until js loads?


####_0.0.6 Settings Page_
* Plugin settings page - principally the API Key instructions.
* Add google maps api key without hard coding
* Enable and disable debug mode
* Set transient expiry (for?)
* Add alert messages to admin pages about missing API Keys
* Refactor use of constants, API Key should not be a constant?


###_0.0.7 Install/Uninstall_
* Lang dir and .pot file
* Add activation/uninstall hooks and PHP checks.

####_0.0.8_
 * Fix any issues with admin map modal css
 * Normalise front end styles with reset, make the style sheet filterable.
 * Remove http/s from the marker attribute, so that the protocall is not baked into the request, and post.
 * Admin: add ability to specify alternative icon folder.
 * Admin: add ability to set default icon.
 * prevent admin modal from insterting shortcode if placeid or address is blank.
 * Disable insert button if the above is blank?
 * https://developers.google.com/maps/documentation/javascript/localization
 * When putting in custom address fields into the admin modal, (without place lookup) the infowindow won't populate on "update info window" button.\
 * [DONE] Add comment to 'website' in custom address, about local links.
 * Add check in modal logic for API key, and lock fields if no key.