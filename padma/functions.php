<?php

/***********************************************************
 *
 * Package   : Padma Theme
 * Author    : Original by Clay Griffiths - Headway Themes
 *             New files by Maarten Schraven - UNITED 7
 			   Padma by Plasma Dev Team - Plasma Soluciones
 * Copywrite : Copyright 2009-2019 Plasma Soluciones M.S.V S.A.
 *
 ***********************************************************/


/**
 *
 * Automatic Updates
 *
 * Must go before Padma::init();
 *
 */

if ( get_option('padma-disable-automatic-core-updates') != '1'){	

	add_filter( 'auto_update_theme', '__return_true');
	
}

/**
 *
 * Load Padma
 *
 */
 			   
/* Prevent direct access to this file */
if ( !defined('WP_CONTENT_DIR') )
	die('Please do not access this file directly.');

/* Make sure PHP 7.0 or newer is installed and WordPress 3.2 or newer is installed. */
require_once get_template_directory() . '/library/common/compatibility-checks.php';

/* Load Padma! */
require_once get_template_directory() . '/library/common/functions.php';
require_once get_template_directory() . '/library/common/application.php';

Padma::init();


/**
 *
 * Plugin templates support
 *
 */
/*
add_filter( 'template_include', function($template){

	global $post;

    if (!$post) {
        return $template;
    }
    
    $template_id = get_post_meta($post->ID, '_wp_page_template', true);

    if(!$template_id)
    	return $template;


	$path = locate_template(array($template_id),true);


	debug(array(
		'template' => $template,
		'template_id' => $template_id,
		'path' => $path,
	));

	//load_template( $template );

	/*

	if(!PadmaOption::get('allow-plugin-templates', false))
		return $template;

	if(!PadmaDisplay::is_plugin_template())
		return $template;

	if(!file_exists($template))		
		return $template;

	return PadmaDisplay::load_plugin_template($template);
	*/
/*
	return $template;
});*/