<?php
/**
 * Headway Compatibility Headway
 */
class PadmaCompatibilityHeadway {

	/**
	 * Init Method
	 *
	 * @return void
	 */
	public static function init() {

		if ( ! PadmaOption::get( 'headway-support' ) ) {
			return;
		}

		self::load();
	}

	/**
	 * Load
	 *
	 * @return void
	 */
	public static function load() {

		$GLOBALS['headway_default_element_data'] = $GLOBALS['padma_default_element_data'];

		self::padma_define_headway_constants();

		Padma::load(
			array(
				'abstract/api-admin-meta-box',
				'abstract/api-box',
				'admin/admin-write' => true,
				'admin/admin-pages',
				'admin/api-admin-inputs',
			)
		);

		require PADMA_LIBRARY_DIR . '/compatibility/headway/functions.php';
		require PADMA_LIBRARY_DIR . '/compatibility/headway/abstract.php';

		add_action(
			'after_setup_theme',
			function() {
				PadmaCompatibilityHeadway::padma_declare_headway_classes();
				Headway::init();
			}
		);
	}

	/**
	 * Headway constants.
	 *
	 * @return void
	 */
	public static function padma_define_headway_constants(){

		define( 'HEADWAY_VERSION', '3.8.9' );
		define( 'HEADWAY_DIR', PADMA_DIR );
		define( 'HEADWAY_LIBRARY_DIR', PADMA_LIBRARY_DIR );
		define( 'HEADWAY_SITE_URL', PADMA_SITE_URL );
		define( 'HEADWAY_DASHBOARD_URL', PADMA_DASHBOARD_URL );
		define( 'HEADWAY_EXTEND_URL', PADMA_EXTEND_URL );
		define( 'HEADWAY_DEFAULT_SKIN', PADMA_DEFAULT_SKIN );
		define( 'HEADWAY_CHILD_THEME_ACTIVE', PADMA_CHILD_THEME_ACTIVE );
		define( 'HEADWAY_CHILD_THEME_DIR', PADMA_CHILD_THEME_DIR );
		define( 'HEADWAY_UPLOADS_DIR', PADMA_UPLOADS_DIR );
		define( 'HEADWAY_CACHE_DIR', PADMA_CACHE_DIR );
	}

	/**
	 * Declare Headway Classes
	 *
	 * @return void
	 */
	public static function padma_declare_headway_classes() {

		$padma_core_classes = array(
			'PadmaUpdater',
			'PadmaLifeSaver',
			'PadmaLifeSaver\helpers\Plugin',
			'PadmaLifeSaver\helpers\json',
			'PadmaAdminMetaBoxAPI',
			'PadmaBlockAPI',
			'PadmaVisualEditorBoxAPI',
			'PadmaVisualEditorPanelAPI',
		);

		$padma_classes_array = array();

		foreach ( get_declared_classes() as $key => $padma_class ) {

			if ( strpos( $padma_class, 'Padma' ) !== false ) {

				if ( in_array( $padma_class, $padma_core_classes, true ) ) {
					continue;
				}

				$padma_classes_array[ $padma_class ] = get_class_methods( $padma_class );
			}
		}

		foreach ( $padma_classes_array as $padma_class => $methods ) {

			$headway_classname = str_replace( 'Padma', 'Headway', $padma_class );
			if ( ! class_exists( $headway_classname ) ) {
				class_alias( $padma_class, $headway_classname );
			}
		}
	}
}
