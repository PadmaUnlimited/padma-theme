<?php

/***********************************************************
 *
 * Package   : Padma Theme
 * Author    : Original by Clay Griffiths - Headway Themes
 *             New files by Maarten Schraven - UNITED 7
 			   ReBorn by Plasma Dev Team - Plasma Soluciones
 * Copywrite : Copyright 2009-2018 Plasma Soluciones M.S.V S.A.
 * Version 	 : v0.0.1
 *
 ***********************************************************/

/* Prevent direct access to this file */
if ( !defined('WP_CONTENT_DIR') )
	die('Please do not access this file directly.');

/* Make sure PHP 5.2 or newer is installed and WordPress 3.2 or newer is installed. */
require_once get_template_directory() . '/library/common/compatibility-checks.php';

/* Load Padma! */
require_once get_template_directory() . '/library/common/functions.php';
require_once get_template_directory() . '/library/common/application.php';

Padma::init();
