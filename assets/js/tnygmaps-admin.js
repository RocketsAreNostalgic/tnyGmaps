jQuery(function () {
    // Fire up image picker
    jQuery("select").imagepicker({
        hide_select: true,
        show_label: false,
        limit: 1
    });
    // Add icon url preview
    var message = "";
    var current =  jQuery("#tnygmaps_custom_icon_url pre").html();
    var googlePin = jQuery("ul.image_picker_selector li:first div img").attr("alt");

    jQuery(".image_picker_selector li div").click(function () {

        var url = jQuery(this).find("img").attr("src");
        var alt = jQuery(this).find("img").attr("alt");
        if (alt === googlePin) {
            message = googlePin;
            jQuery("#tnygmaps_custom_icon_url pre").html(message);
        } else if (current !== url && alt !== googlePin) {
            message = url + " [pending save]";
            jQuery("#tnygmaps_custom_icon_url pre").html(message);
        } else if (current === url && alt !== googlePin) {
            message = current;
            jQuery("#tnygmaps_custom_icon_url pre").html(message);
        }
    });
});