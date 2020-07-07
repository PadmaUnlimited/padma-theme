<?php

namespace Padma;
class PadmaCompatibilityWPML {

	/**
	 * Constructor
	 */
	private function __construct() {

	}

	/**
	 * Gets the instance of the class
	 */
	public static function init() {


		if(!class_exists('SitePress')){
			return;
		}

		add_filter( 'wpml_get_home_url', array(__CLASS__,'filter_home_url'), 99, 5);

	}


	public static function filter_home_url($home_url, $url, $path, $orig_scheme, $blog_id){		
		return $url;
	}

}
