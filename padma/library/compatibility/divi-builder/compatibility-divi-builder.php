<?php

class PadmaCompatibilityDiviBuilder {
	
	
	public static function init() {

		add_filter( 'et_fb_bundle_dependencies', array( __CLASS__ , 'add_fb_bundle_dependencies' ) );
		
	}
		
	/**
	 * Register theme compat script as bundle.js' dependency so it is being loaded before bundle.js
	 */
	public static function add_fb_bundle_dependencies( $deps ) {

		$deps[] = 'et_fb_theme_the7';

		return $deps;
	}

}	