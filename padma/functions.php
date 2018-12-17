<?php

/***********************************************************
 *
 * Package   : Padma Theme
 * Author    : Original by Clay Griffiths - Headway Themes
 *             New files by Maarten Schraven - UNITED 7
 			   Padma by Plasma Dev Team - Plasma Soluciones
 * Copywrite : Copyright 2009-2018 Plasma Soluciones M.S.V S.A.
 *
 ***********************************************************/

 			   
/* Prevent direct access to this file */
if ( !defined('WP_CONTENT_DIR') )
	die('Please do not access this file directly.');

/* Make sure PHP 7.0 or newer is installed and WordPress 3.2 or newer is installed. */
require_once get_template_directory() . '/library/common/compatibility-checks.php';

/* Load Padma! */
require_once get_template_directory() . '/library/common/functions.php';
require_once get_template_directory() . '/library/common/application.php';

Padma::init();


if ( get_option('padma-disable-automatic-core-updates') != '1'){	
	add_filter( 'auto_update_theme', '__return_true');
}



/*
add_action('after_setup_theme','test');
function test(){

	$settings = array(
		'codeEditor' => wp_enqueue_code_editor( array( 'file' => $real_file ) ),
	);
	wp_enqueue_script( 'wp-theme-plugin-editor' );
	wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { wp.themePluginEditor.init( $( "#template" ), %s ); } )', wp_json_encode( $settings ) ) );

	debug($settings);
	
}*/