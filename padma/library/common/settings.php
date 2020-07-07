<?php
/**
 * All settings of Padma Unlimited and Padma Lite
 *
 * @package Padma
 * @author Padma Unlimited Team
 *
 **/

namespace Padma;
class PadmaSettings {

	private static $settings = array(
		'padma-branch' => 'unlimited',
		'menu-name' => 'Padma Theme',
		'slug' => 'padma',
	);

	/**
	 *
	 * Construct
	 *
	 */
	function __construct(){
	}


	/**
	 *
	 * return the setting
	 *
	 */

	public static function get($key){
		return self::$settings[$key];
	}


	/**
	 *
	 * Settings from application class
	 *
	 */
	public static function set_enviroment(){

		/*	Errors	*/
		error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
		@ini_set('display_errors', 'Off');

	}

	/**
	 *
	 * Visual Editor settings
	 *
	 */
	public static function set_visual_editor_settings(){

		//Attempt to raise memory limit to max
		@ini_set('memory_limit', apply_filters('padma_memory_limit', WP_MAX_MEMORY_LIMIT));

	}


}