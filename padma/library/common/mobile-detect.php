<?php
class PadmaMobileDetect {
	
	protected static $detect;

	function __construct(){


	}
	
	public static function init() {
		


	}

	public static function isMobile(){

		require_once PADMA_LIBRARY_DIR . '/common/lib/Mobile_Detect.php';
		self::$detect = new Mobile_Detect;
		return ( self::$detect->isMobile() || self::$detect->isTablet() );

	}
}