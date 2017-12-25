<?php
/* Prevent direct access to this file */
if ( !defined('WP_CONTENT_DIR') )
	die('Please do not access this file directly.');

/* WordPress and a lot of plugins require the function in this file, so I guess we have to use it :-(. */
wp_footer();

PadmaDisplay::body_close();

PadmaDisplay::html_close();