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
class PadmaUpdater {

	/**
	 * Init Class
	 *
	 * @return void
	 */
	public static function init() {
		// Named global variable to make access for other scripts easier.
		$GLOBALS[ __CLASS__ ] = new self();
		self::update_padma_plugins();
	}

	/**
	 *
	 * Detect CMS
	 */
	private static function detect_cms() {
		return ( function_exists( 'classicpress_version_short' ) ) ? 'ClassicPress' : 'WordPress';
	}


	/**
	 *
	 * Padma Plugins
	 */
	public static function plugins() {
		return array(
			'padma-content-slider',
			'padma-example',
			'padma-filter-gallery',
			'padma-gallery',
			'padma-lifesaver',
			'padma-lottiefiles',
			'padma-post-slider',
			'padma-services',
			'padma-shortcode-block',
			'padma-slider-revolution',
			'padma-sociable',
			'padma-store',
			'padma-visual-elements',
			'padma-updater',
		);
	}


	/**
	 *
	 * Update Padma plugins
	 */
	private static function update_padma_plugins() {

		foreach ( self::plugins() as $key => $slug ) {
			$path = ABSPATH . 'wp-content/plugins/' . $slug;
			self::updater( $slug, $path, false );
		}
	}

	/**
	 *
	 * Is a Padma Plugin?
	 *
	 * @param string $slug Plugin Slug.
	 * @return boolean
	 */
	public static function is_padma_plugin( $slug ) {
		return in_array( $slug, self::plugins(), true );
	}

	/**
	 * Run the updater
	 *
	 * @param string  $slug   Plugin or Theme slug to update.
	 * @param string  $dir    Plugin or Theme DIR.
	 * @param boolean $theme Is Padma Theme?.
	 * @param integer $check_period Time period in hours.
	 * @return void
	 */
	public static function updater( $slug = 'padma-updater', $dir = __DIR__, $theme = false, $check_period = 12 ) {

		if ( file_exists( $dir ) ) {

			/**
			 *
			 * Use "developer" version or "production" version
			 */
			$package_type = ( get_option( 'padma-use-developer-version' ) ) ? 'developer' : 'software';

			if ( $theme ) {
				$target = $dir . '/functions.php';
			} else {
				$target = $dir . '/' . $slug . '.php';
			}

			$token = get_option( 'padma_service_token' );

			$url = PADMA_CDN_URL . $package_type . '/?action=get_metadata&slug=' . $slug;
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

			$update_checker = Puc_v4_Factory::buildUpdateChecker( $url, $target, $slug, $check_period );
		}
	}
}
