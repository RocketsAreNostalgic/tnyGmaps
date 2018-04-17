<?php
namespace OrionRush\TnyGmaps\Modal;
use OrionRush\TnyGmaps\ModalSupport as ModalSupport;
use OrionRush\TnyGmaps\Support as Support;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/************************************************************************
 * Markup for modal window for tny gMaps tinyMCE editor
 * This modal is a shortcode builder with preview functionality for the TnyGmaps plugin.
 * @link http://www.orionrush.com
 * @since 0.0.1
 *
 * @package WordPress
 * @subpackage TnygMaps
 *
 * @copyright (c) 2012-`date "+%Y"` orionrush. All rights reserved.
 * @license GPL
 * **********************************************************************
 */

// do we have the google places api key?
$api_test = Support\test_google_key();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo esc_html( TNYGMAPS_NAME ) ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
	<?php echo apply_filters( 'tnygmaps_add_header_scripts', ModalSupport\addFooterScripts($api_test) ); ?>
</head>
<body>
<div id="overlay" class=""></div>
<?php if ( ! $api_test ) { ?>
    <div class="alert warning floating"> <?php echo sprintf( __( 'GOOGLE API KEY NOT SET: Visit the %splugin settings page%s to get one.', 'orionrush-tnygmaps' ), '<a href="' . admin_url() . 'options-general.php?page=tnygmaps" target="_parent">', '</a>' ); ?> </div>
<?php } ?>
<div id="tnygmaps" class="wrap">
    <form action="/" method="get" accept-charset="utf-8">
	    <?php echo apply_filters( 'tnygmaps_add_google_lookup_group', ModalSupport\addGoogleLookupGroup($api_test) ); ?>
	    <?php echo apply_filters( 'tnygmaps_add_custom_details_group', ModalSupport\addCutsomDetailsGroup($api_test) ); ?>
	    <?php echo apply_filters( 'tnygmaps_add_window_extras_group', ModalSupport\addWindowExtrasGroup($api_test)); ?>
	    <?php echo apply_filters( 'tnygmaps_add_map_attr_group', ModalSupport\addMapAttributesGroup($api_test)); ?>
    </form>
</div>
<?php echo  apply_filters( 'tnygmaps_add_map_preview', ModalSupport\addMapPreview($api_test)); ?>
</body>
<?php echo apply_filters( 'tnygmaps_add_footer_scripts', ModalSupport\addFooterScripts($api_test) ); ?>
</html>