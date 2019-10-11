<?php
/**
 * Padma Theme main function file
 *
 * @since        1.0.0
 *
 * @package      Padma
 * @subpackage   Padma/header
 */

/* Prevent direct access to this file */
if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	die( 'Please do not access this file directly.' );
}

PadmaDisplay::html_open();

wp_head();

PadmaDisplay::body_open();
