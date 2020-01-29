<?php
class PadmaMobileDetect {

	protected static $detect;

	function __construct(){


	}

	public static function init() {



	}

	public static function isMobile(){


		if( !class_exists('Mobile_Detect'))
			require_once PADMA_LIBRARY_DIR . '/common/lib/Mobile_Detect.php';


		self::$detect = new Mobile_Detect;

		$isMobile = false;

		if( method_exists('Mobile_Detect', 'isMobile') ){
			$isMobile = self::$detect->isMobile();
		}
		
		if( $isMobile === false && method_exists('Mobile_Detect', 'isTablet') ){
			$isMobile = self::$detect->isTablet();
		}

		return $isMobile;

	}
}