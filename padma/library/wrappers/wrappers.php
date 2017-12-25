<?php
class PadmaWrappers {


	public static $default_wrappers = array(
		'default' => array(
			'id' => 'default',
			'position' => 0,
			'settings' => array(
				'fluid' => false,
				'fluid-grid' => false,
				'columns' => null,
				'column-width' => null,
				'gutter-width' => null
			)
		)
	);

	public static $default_wrapper_id = 'default';

	public static $default_columns = 24;

	public static $default_column_width = 20;

	public static $default_gutter_width = 20;

	public static $default_wrapper_margin_top = 30;

	public static $default_wrapper_margin_bottom = 30;


	public static $global_grid_column_width = null;

	public static $global_grid_gutter_width = null;


	public static function init() {

		/* Set defaults */
			self::$default_columns = PadmaSkinOption::get('columns', false, self::$default_columns);
			self::$global_grid_column_width = PadmaSkinOption::get('column-width', false, self::$default_column_width);
			self::$global_grid_gutter_width = PadmaSkinOption::get('gutter-width', false, self::$default_gutter_width);

			self::$default_wrappers['default']['settings']['use-independent-grid'] = false;
			self::$default_wrappers['default']['settings']['columns'] = self::$default_columns;
			self::$default_wrappers['default']['settings']['column-width'] = self::$default_column_width;
			self::$default_wrappers['default']['settings']['gutter-width'] = self::$default_gutter_width;

		/* Setup hooks */
		add_action('padma_register_elements_instances', array(__CLASS__, 'register_wrapper_instances'), 11);
		add_action('padma_wrapper_options', array(__CLASS__, 'options_panel'), 10, 2);

		add_action('wp_head', array(__CLASS__, 'sticky_wrapper_js'));

	}


	public static function sticky_wrapper_js() {

		$layout_wrappers = PadmaWrappersData::get_wrappers_by_layout( PadmaLayout::get_current_in_use() );
		$sticky_wrappers = array();

		foreach ( $layout_wrappers as $wrapper ) {

            if ( $mirrored_wrapper = PadmaWrappersData::get_wrapper_mirror($wrapper) ) {
                $original_wrapper = $wrapper;

                $wrapper = $mirrored_wrapper;
                $wrapper['id'] = padma_get('id', $original_wrapper);
                $wrapper['legacy_id'] = padma_get('legacy_id', $original_wrapper);
            }

			$wrapper_settings = padma_get('settings', $wrapper, array());

			if ( padma_get('enable-sticky-positioning', $wrapper_settings) ) {

				$sticky_wrappers['#wrapper-' . PadmaWrappersData::get_legacy_id( $wrapper )] = array(
					'offset_top' => padma_get( 'sticky-position-top-offset', $wrapper_settings, 0 )
				);

			}


		}

		if ( !$sticky_wrappers ) {
			return false;
		}

		wp_enqueue_script( 'padma-sticky', padma_url() . '/library/media/js/sticky.js', array( 'jquery' ) );
		wp_localize_script( 'padma-sticky', 'BTStickyWrappers', $sticky_wrappers );


	}


	public static function format_wrapper_id($wrapper_id) {

		return str_replace('wrapper-', '', $wrapper_id);

	}


	public static function register_wrapper_instances() {

		$all_wrappers = PadmaWrappersData::get_all_wrappers();

		if ( !$all_wrappers )
			return false;

		foreach ( $all_wrappers as $wrapper_id => $wrapper_options ) {

			/* Do NOT register the default wrapper instance */
			if ( $wrapper_id == 'default' )
				continue;

			/* Do not register instance for mirrored wrapper */
			if ( PadmaWrappersData::is_wrapper_mirrored($wrapper_options) )
				continue;

			$wrapper_id_for_selector    = PadmaWrappersData::get_legacy_id( $wrapper_options );

			$wrapper_name = padma_get('alias', padma_get('settings', $wrapper_options, array())) ? 'Wrapper: ' . padma_get( 'alias', padma_get( 'settings', $wrapper_options, array() ) ) : 'Wrapper (Unnamed)';

			PadmaElementAPI::register_element_instance(array(
				'group' => 'structure',
				'element' => 'wrapper',
				'id' => 'wrapper-' . PadmaWrappers::format_wrapper_id($wrapper_id),
				'name' => $wrapper_name,
				'selector' => '#wrapper-' . self::format_wrapper_id( $wrapper_id_for_selector) . ', div#whitewrap div.wrapper-mirroring-' . self::format_wrapper_id($wrapper_id_for_selector),
				'layout' => $wrapper_options['layout']
			));

		}

	}


	public static function is_fluid($wrapper) {

		return padma_get('fluid', padma_get('settings', $wrapper, array()), false, true);

	}


	public static function is_grid_fluid($wrapper) {

		$wrapper_settings = padma_get('settings', $wrapper, array());

		return padma_get('fluid', $wrapper_settings, false, true) && padma_get('fluid-grid', $wrapper_settings, false, true);

	}


	public static function is_independent_grid($wrapper) {

		return padma_get('use-independent-grid', padma_get('settings', $wrapper, array()), false, true);

	}


	public static function get_columns($wrapper) {

		return padma_get('columns', padma_get('settings', $wrapper, array()), false, true);

	}


	public static function get_column_width($wrapper) {

		$wrapper_settings = padma_get('settings', $wrapper, array());

		return padma_get('use-independent-grid', $wrapper_settings, false, true) ? padma_get('column-width', $wrapper_settings, false, true) : PadmaWrappers::$global_grid_column_width;

	}


	public static function get_gutter_width($wrapper) {

		$wrapper_settings = padma_get('settings', $wrapper, array());

		return padma_get('use-independent-grid', $wrapper_settings, false, true) ? padma_get('gutter-width', $wrapper_settings, false, true) : PadmaWrappers::$global_grid_gutter_width;

	}


	public static function get_grid_width($wrapper) {

		if ( !is_array($wrapper) )
			return false;

		/* If wrapper is mirrored then use settings from it for the grid */
		if ( $potential_wrapper_mirror = PadmaWrappersData::get_wrapper_mirror($wrapper) )
			$wrapper = $potential_wrapper_mirror;

		$columns = self::get_columns($wrapper);

		$column_width = self::get_column_width($wrapper);
		$gutter_width = self::get_gutter_width($wrapper);

		return ($column_width * $columns) + (($columns - 1) * $gutter_width);

	}


	public static function options_panel($wrapper, $layout) {

		require_once BLOX_LIBRARY_DIR . '/wrappers/wrapper-options.php';

		//Initiate options class
		$options = new PadmaWrapperOptions;
		$options->display($wrapper, $layout);

	}


	public static function get_layout_wrappers( $layout ) {

		_deprecated_function( __FUNCTION__, '3.7', 'PadmaDataWrappers::get_wrappers_by_layout()' );

		$wrappers = PadmaWrappersData::get_wrappers_by_layout( $layout );

		if ( ! $wrappers )
			return $wrappers;

		/* Merge settings array with each wrapper so it's single dimension */
		foreach ( $wrappers as $wrapper_id => $wrapper ) {

			$wrappers[ $wrapper_id ]['mirror-wrapper'] = padma_get( 'mirror_id', $wrapper );

			$wrappers[ $wrapper_id ] = array_merge( $wrappers[ $wrapper_id ], padma_get( 'settings', $wrappers[ $wrapper_id ], array() ) );

		}

		return $wrappers;

	}


	public static function get_all_wrappers() {

		_deprecated_function( __FUNCTION__, '3.7', 'PadmaWrappersData::get_all_wrappers()' );

		$wrappers = PadmaWrappersData::get_all_wrappers();

		return $wrappers;

	}


	public static function get_wrapper($wrapper_id, $deprecated = null) {

		_deprecated_function( __FUNCTION__, '3.7', 'PadmaWrappersData::get_wrapper()' );

		return PadmaWrappersData::get_wrapper($wrapper_id);

	}


}