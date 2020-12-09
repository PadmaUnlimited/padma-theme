<?php
/**
 * Padma Updater main function file
 *
 * @since 1.4.0
 * @package Padma/library
 */

/**
 * Updater class
 */
class PadmaCoreUpdater {

	/**
	 *
	 * Detect CMS
	 */
	private static function detect_cms() {
		return ( function_exists( 'classicpress_version_short' ) ) ? 'ClassicPress' : 'WordPress';
	}

	/**
	 * Run the updater
	 *
	 * @return void
	 */
	public static function updater() {

		/**
		 *
		 * Use "developer" version or "production" version
		 */
		$package_type = ( get_option( 'padma-use-developer-version' ) ) ? 'developer' : 'software';
		$target       = PADMA_DIR . '/functions.php';
		$token        = get_option( 'padma_service_token' );
		$slug         = 'padma';
		$url          = PADMA_CDN_URL . $package_type . '/?action=get_metadata&slug=' . $slug;

		if ( '' !== $token ) {
			$url .= '&token=' . $token;
		}
		$url .= '&cms=' . self::detect_cms();

		add_filter(
			'puc_is_slug_in_use-' . $slug,
			function() {
				return false;
			}
		);

		$update_checker = Puc_v4_Factory::buildUpdateChecker( $url, $target, $slug, 12 );
	}
}
