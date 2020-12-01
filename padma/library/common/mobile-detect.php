<?php
/**
 * Padma Unlimited Theme.
 *
 * @package padma
 */

/**
 * Mobile detetion class
 */
class PadmaMobileDetect {

	/**
	 * Detect device
	 *
	 * @var Mobile_Detect
	 */
	protected static $detect;

	/**
	 * Detect Mobile
	 *
	 * @return boolean
	 */
	public static function is_mobile() {

		if ( ! class_exists( 'Mobile_Detect' ) ) {
			require_once PADMA_LIBRARY_DIR . '/common/lib/Mobile_Detect.php';
		}

		self::$detect = new Mobile_Detect();

		$is_mobile = false;

		if ( method_exists( 'Mobile_Detect', 'isMobile' ) ) {
			$is_mobile = self::$detect->isMobile();
		}

		if ( false === $is_mobile && method_exists( 'Mobile_Detect', 'isTablet' ) ) {
			$is_mobile = self::$detect->isTablet();
		}

		return $is_mobile;
	}
}
