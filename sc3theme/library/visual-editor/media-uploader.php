<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>

<head>

<meta charset="<?php echo get_bloginfo('charset'); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />


<?php
$GLOBALS['wp_media_view_strings'] = array();
$GLOBALS['wp_media_view_settings'] = array();

/* Begin kludge to fix issue with WP string localize in some cases.  See: https://core.trac.wordpress.org/ticket/24724 */
function blox_media_view_settings_grabber( $settings ) {

	$GLOBALS['wp_media_view_settings'] = $settings;

	return $settings;

}

add_filter( 'media_view_settings', 'blox_media_view_settings_grabber' );

function blox_media_view_strings_grabber($strings) {

	$GLOBALS['wp_media_view_strings'] = $strings;

	return $strings;

}
add_filter('media_view_strings', 'blox_media_view_strings_grabber');

wp_enqueue_media();

$GLOBALS['wp_media_view_strings']['settings'] = $GLOBALS['wp_media_view_settings'];

wp_localize_script( 'media-editor', '_wpMediaViewsL10n', $GLOBALS['wp_media_view_strings'] );
wp_localize_script( 'media-views', '_wpMediaViewsL10n', $GLOBALS['wp_media_view_strings'] );
/* End Kludge */

wp_enqueue_style('open-sans');

wp_print_scripts();
wp_print_styles();
?>

<style type="text/css">
	.media-modal {
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
	}

	.media-modal-close {
		display: none;
	}

	.media-frame-router {
		top: 5px;
	}

	.media-frame-content {
		top: 39px;
	}
</style>


</head>

<body>

<?php
wp_print_media_templates();
?>

</body>
</html>