<?php
class PadmaElementsData {


	private static $raw_data = null;


	public static function init() {

		add_action('padma_visual_editor_save', array(__CLASS__, 'merge_core_default_design_data'));

	}


	/* Used to merge in the global defaults for backwards compatibility */
	public static function get_raw_data($defaults = array()) {

		if ( is_array(self::$raw_data) ) {
			return self::$raw_data;
		}

		self::$raw_data = padma_array_merge_recursive_simple(self::get_legacy_default_data(), PadmaSkinOption::get('properties', 'design', $defaults));

		return self::$raw_data;

	}

	
	
	/* Mass Get */
	public static function get_all_elements() {
				
		$elements = self::get_raw_data();
			
		//Move default elements to the top
		foreach ( $elements as $element_id => $element_options ) {
			
			$element = PadmaElementAPI::get_element($element_id);
			
			if ( !isset($element['default-element']) || $element['default-element'] === false )
				continue;
				
			$temp_id = $element_id;
			$temp_options = $element_options;
			
			unset($elements[$element_id]);
			
			$elements = array_merge(array($temp_id => $temp_options), $elements);
			
		}
					
		return $elements;
		
	}
	
	
	public static function get_element_properties($element) {
		
		//Get element ID
		$element_id = is_array($element) ? $element['id'] : $element;
			
		$element = padma_get($element_id, self::get_raw_data());
		
		if ( !isset($element['properties']) || !is_array($element['properties']) )
			$element['properties'] = array();
		
		$properties = $element['properties'];
		
		//Fetch the property
		return ( is_array($properties) && count($properties) > 0 ) ? $properties : array();
		
	}
	
	
	public static function get_special_element_properties($args) {

		$defaults = array(
			'element' => null,
			'se_type' => null,
			'se_meta' => null
		);
		
		extract(array_merge($defaults, $args));

		//Get element ID
		$element_id = is_array($element) ? $element['id'] : $element;
				
		$element = padma_get($element_id, self::get_raw_data(), array(
			'special-element-' . $se_type => array()
		));

		if ( !isset($element['special-element-' . $se_type][$se_meta]) || !is_array($element['special-element-' . $se_type][$se_meta]) )
			$element['special-element-' . $se_type][$se_meta] = array();
		
		$properties =& $element['special-element-' . $se_type][$se_meta];
			
		//Return the data
		return ( is_array($properties) && count($properties) > 0 ) ? $properties : array();
		
	}
	

	/* Single Get */
	public static function get_property($element_id, $property_id, $default = null, $element_group = null) {
		
		$properties = self::get_element_properties($element_id);
		
		if ( $properties !== null && !is_wp_error($properties) && isset($properties[$property_id]) && (padma_fix_data_type($properties[$property_id]) || padma_fix_data_type($properties[$property_id]) === 0) )
			return padma_fix_data_type($properties[$property_id]);
			
		else
			return $default;
		
	}
	
	
	public static function get_special_element_property($element_id, $se_type, $se_meta, $property_id, $default = null, $element_group = null) {
		
		$properties = self::get_special_element_properties(array(
			'element' => $element_id, 
			'se_type' => $se_type, 
			'se_meta' => $se_meta
		));
		
		if ( $properties !== null && !is_wp_error($properties) && isset($properties[$property_id]) && (padma_fix_data_type($properties[$property_id]) || padma_fix_data_type($properties[$property_id]) === 0) )
			return padma_fix_data_type($properties[$property_id]);
			
		else
			return $default;
		
	}

	
	/* Setting */
	public static function set_property($element_group = null, $element_id, $property_id, $value) {

		/* Pass the torch onto self::delete_property() if the value is 'delete' */
			if ( strtolower($value) == 'delete' )
				return self::delete_property($element_id, $property_id);

		$all_properties = PadmaSkinOption::get('properties', 'design', array());

		/* Insure array exists for element that property is being set for */
		if ( !isset($all_properties[$element_id]) || !is_array($all_properties[$element_id]) )
			$all_properties[$element_id] = array('properties' => array());

		/* Set the property */
		if ( $value == 'null' )
			$value = null;

		$all_properties[$element_id]['properties'][$property_id] = $value;
		
		/* Send it back to DB */
		return PadmaSkinOption::set('properties', $all_properties, 'design');
		
	}
	

		public static function delete_property($element_id, $property_id) {

			$all_properties = PadmaSkinOption::get('properties', 'design', array());

			/* Delete the property */
				if ( !empty($all_properties[$element_id]['properties']) && isset($all_properties[$element_id]['properties'][$property_id]) )
					unset($all_properties[$element_id]['properties'][$property_id]);

			/* Send it back to DB */
			return PadmaSkinOption::set('properties', $all_properties, 'design');
			
		}

	
	public static function set_special_element_property($element_group = null, $element_id, $special_element_type, $special_element_meta, $property_id, $value) {

		/* Pass the torch onto self::delete_special_element_property() if the value is 'delete' */
			if ( strtolower($value) == 'delete' )
				return self::delete_special_element_property(null, $element_id, $special_element_type, $special_element_meta, $property_id);

		$all_properties = PadmaSkinOption::get('properties', 'design', array());

		/* Insure array exists for element that property is being set for */
		if ( !isset($all_properties[$element_id]) || !is_array($all_properties[$element_id]) )
			$all_properties[$element_id] = array('special-element-' . $special_element_type => array(
				$special_element_meta => array()
			));

		/* Set the property */
		if ( $value == 'null' )
			$value = null;

		$all_properties[$element_id]['special-element-' . $special_element_type][$special_element_meta][$property_id] = $value;
		
		/* Send it back to DB */
		return PadmaSkinOption::set('properties', $all_properties, 'design');
		
	}


	public static function batch_set_special_element_properties($batch_data) {

		$all_properties = PadmaSkinOption::get('properties', 'design', array());

		foreach ( $batch_data as $element_data ) {

			/* Insure array exists for element that property is being set for */
			if (!isset($all_properties[$element_data['element_id']]) || !is_array($all_properties[$element_data['element_id']]))
				$all_properties[$element_data['element_id']] = array('special-element-' . $element_data['special_element_type'] => array(
					$element_data['special_element_meta'] => array()
				));

			/* Set the property */
			if ($element_data['value'] == 'null')
				$element_data['value'] = null;

			$all_properties[$element_data['element_id']]['special-element-' . $element_data['special_element_type']][$element_data['special_element_meta']][$element_data['property_id']] = $element_data['value'];

		}

		/* Send it back to DB */
		return PadmaSkinOption::set('properties', $all_properties, 'design');

	}


		public static function delete_special_element_property($element_group = null, $element_id, $special_element_type, $special_element_meta, $property_id) {

			$all_properties = PadmaSkinOption::get('properties', 'design', array());

			if ( isset($all_properties[$element_id]['special-element-' . $special_element_type][$special_element_meta][$property_id]) )
				unset($all_properties[$element_id]['special-element-' . $special_element_type][$special_element_meta][$property_id]);
			
			/* Send it back to DB */
			return PadmaSkinOption::set('properties', $all_properties, 'design');

		}


		public static function delete_special_element_properties($element_group = null, $element_id, $special_element_type, $special_element_meta) {

			$all_properties = PadmaSkinOption::get('properties', 'design', array());

			/* Delete all special elements matching the meta and type */
				if ( isset($all_properties[$element_id]['special-element-' . $special_element_type][$special_element_meta]) )
					unset($all_properties[$element_id]['special-element-' . $special_element_type][$special_element_meta]);

			/* Send it back to DB */
			return PadmaSkinOption::set('properties', $all_properties, 'design');

		}


		public static function batch_delete_special_element_properties($batch_data) {

			$all_properties = PadmaSkinOption::get('properties', 'design', array());

			foreach ( $batch_data as $element_data ) {

				if (isset($all_properties[$element_data['element_id']]['special-element-' . $element_data['special_element_type']][$element_data['special_element_meta']]))
					unset($all_properties[$element_data['element_id']]['special-element-' . $element_data['special_element_type']][$element_data['special_element_meta']]);
			}

			return PadmaSkinOption::set('properties', $all_properties, 'design');

		}

	/* JS Properties */
		public static function set_js_property($element_id, $js_property_id, $options) {

			$all_js_properties = PadmaSkinOption::get( 'js-properties', 'design', array() );

			if ( is_string($options) && strtolower( $options ) == 'delete' ) {

				if ( isset( $all_js_properties[ $element_id ][ $js_property_id ]) ) {
					unset( $all_js_properties[ $element_id ][ $js_property_id ]);
				}

			} else {

				$all_js_properties[ $element_id ][ $js_property_id ] = $options;

			}

			/* Send it back to DB */
			return PadmaSkinOption::set( 'js-properties', $all_js_properties, 'design' );

		}


	/* Defaults */
		public static function get_default_data() {

			global $padma_core_default_element_data;

			return $padma_core_default_element_data;

		}


			public static function get_legacy_default_data() {

				global $padma_default_element_data;

				if ( !isset($padma_default_element_data) || !is_array($padma_default_element_data) )
					$padma_default_element_data = array();

				return apply_filters('padma_element_data_defaults', $padma_default_element_data);

			}


		/**
		 * Merge in default design data.  This will be ran upon save and upgrade to Padma 3.6
		 */
		public static function merge_core_default_design_data() {

			self::merge_default_design_data(PadmaElementsData::get_default_data(), 'core');

			self::merge_default_design_data( array(
				'block-pin-board-pin'               => array(
					'properties' => array(
						'padding-top'                  => 1,
						'padding-right'                => 1,
						'padding-bottom'               => 1,
						'padding-left'                 => 1,
						'background-color'             => 'ffffff',
						'border-color'                 => 'eeeeee',
						'border-style'                 => 'solid',
						'border-top-width'             => 1,
						'border-right-width'           => 1,
						'border-bottom-width'          => 1,
						'border-left-width'            => 1,
						'box-shadow-color'             => 'eee',
						'box-shadow-blur'              => 3,
						'box-shadow-horizontal-offset' => 0,
						'box-shadow-vertical-offset'   => 2
					)
				),
				'block-pin-board-pin-title'         => array(
					'properties'            => array(
						'padding-top'     => 15,
						'padding-right'   => 15,
						'padding-left'    => 15,
						'font-size'       => 18,
						'line-height'     => 120,
						'text-decoration' => 'none'
					),
					'special-element-state' => array(
						'hover' => array(
							'text-decoration' => 'underline'
						)
					)
				),
				'block-pin-board-pin-text'          => array(
					'properties' => array(
						'font-size'     => 12,
						'line-height'   => 150,
						'padding-right' => 15,
						'padding-left'  => 15
					)
				),
				'block-pin-board-pin-meta'          => array(
					'properties' => array(
						'font-size'     => 12,
						'line-height'   => 120,
						'padding-right' => 15,
						'padding-left'  => 15,
						'color'         => '888888'
					)
				),
				'block-pin-board-pagination-button' => array(
					'properties'            => array(
						'text-decoration'            => 'none',
						'background-color'           => 'eeeeee',
						'border-top-left-radius'     => 4,
						'border-top-right-radius'    => 4,
						'border-bottom-right-radius' => 4,
						'border-bottom-left-radius'  => 4,
						'padding-top'                => 5,
						'padding-right'              => 9,
						'padding-bottom'             => 5,
						'padding-left'               => 9
					),
					'special-element-state' => array(
						'hover' => array(
							'background-color' => 'e7e7e7'
						)
					)
				)
			), 'core-37-pin-board' );

		}


			/* This function accepts data as well as ID that way it can be used by Padma plugins */
			public static function merge_default_design_data($default_data, $id) {

				$merge_id = 'merged-default-design-data-' . strtolower(str_replace(array(' ', '-'), '_', $id));

				/* Only merge if it hasn't been merged before. */
				if ( !PadmaSkinOption::get($merge_id, 'general', false) ) {

					$design_data = PadmaSkinOption::get('properties', 'design', array());
					$design_data_with_defaults = padma_array_merge_recursive_simple($default_data, $design_data);

					PadmaSkinOption::set($merge_id, true, 'general');

					return PadmaSkinOption::set('properties', $design_data_with_defaults, 'design');

				}

				/* Already merged, return false */
				return false;

			}

}