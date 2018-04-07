
### Tny gMaps Plugin Todo

####_0.0.3 Refactor Namespace, remove prefixes_

* [DONE] apply code style and design patterns established previously
* [DONE] Namespace all files and functions
* [DONE] remove any function prefixes
* [DONE] apply directory structure
* [DONE] normalise comments
* [DONE] code formatting

####_0.0.4 Refactor tinymce Modal_
* [DONE] Add modal string to js via wp_localize_script()
* [DONE] Remove the direct dependency on wp by moving constant refs to js vars via wp_localize_script() also
* [DONE] refactor js scripts to rely on wp_localize_script() globals instead

####_0.0.5 Refactor JS_    
* [DONE] jQuery remove deprecated functions: load(), live(), ready event, 
* [DONE NA] Deprecated TinyMCE API call: windowManager.createInstance(..)
    * http://wordpress.stackexchange.com/q/258920/13551
* [DONE - NO]Concat js into single lib?
* [DONE] Do we need to grey out interface with modal and spinner until js loads?


####_0.0.6 Settings Page_
* [DONE] Plugin settings page - principally the API Key instructions.
* [DONE] Add google maps api key without hard coding

* [DONE] Add alert messages to admin pages about missing API Keys
* [DONE] Refactor use of constants, API Key should not be a constant?


###_0.0.7 Install/Uninstall_
* [DONE] Add activation/uninstall hooks and PHP checks.

####_0.0.8_
 * [DONE - baked into activation / uploads] Admin: add ability to specify alternative icon folder.
 * [DONE] Admin: add ability to set default icon.
 * [DONE - PASS] prevent admin modal from insterting shortcode if placeid or address is blank.
 * [DONE - PASS] Disable insert button if the above is blank?
 * [DONE] When putting in custom address fields into the admin modal, (without place lookup) the infowindow won't populate on "update info window" button.
 * [DONE] Add comment to 'website' in custom address, about local links.
 * [DONE]Add check in modal logic for API key, and lock fields if no key.


 ####_0.0.9_
 * [IN PROGRESS] Prepare strings for translation.
 * https://developers.google.com/maps/documentation/javascript/localization
 * Lang dir and .pot file
 * Shortcode - fix debug mode and flush modes - they dont seem to work as expeced.

 * [DONE] Fix issues with admin map modal css and front end styles not matching
 * [DONE] Normalise front end styles with reset, make the style sheet filterable
 * [DONE - PASS FOR NOW ]Front end does not show icon in icon bubble
 * [DONE] Modal - custom address looses icon
 * [DONE] Remove http/s from the marker attribute, so that the protocall is not baked into the request, and post.
 * [DONE - PASSED]Settings page: Enable and disable debug mode
 * [DONE] Settings page: Set transient expiry (for?) -- SOLVED by adding a filter to the function instead.
