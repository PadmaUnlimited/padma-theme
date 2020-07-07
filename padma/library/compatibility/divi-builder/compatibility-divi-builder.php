<?php

namespace Padma;
class PadmaCompatibilityDiviBuilder {

	/**
	 * Constructor
	 */
	private function __construct() {

	}

	/**
	 * Gets the instance of the class
	 */
	public static function init() {
		/*

		commented due DIVI Builder 4.2+ works with Padma.

		if(!class_exists('ET_Builder_Plugin')){
			return;
		}

		add_action('padma_whitewrap_open', array(__CLASS__, 'padma_whitewrap_close_whitewraps_tag'));
		add_action('padma_whitewrap_close', array(__CLASS__, 'padma_whitewrap_open_whitewraps_tag'));
		*/

	}

	public static function padma_whitewrap_close_whitewraps_tag(){
		echo '</div>';
	}
	public static function padma_whitewrap_open_whitewraps_tag(){
		echo '<div>';	
	}

}	