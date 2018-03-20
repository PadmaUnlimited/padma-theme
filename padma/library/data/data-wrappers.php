<?php
class PadmaWrappersData {


	public static function add_wrapper($layout_id, $args) {

		global $wpdb;

		/* Validate input */
		if ( !$args || !is_array($args) )
			return false;

		if ( $args['position'] === null || $args['position'] === false || $args['position'] === '' )
			return new WP_Error('pu_add_wrapper_missing_position');

		//Figure out mirror ID
		$mirror_id = padma_get('mirror-wrapper', padma_get('settings', $args, array()));

		//Unset old mirror ID
		if ( isset($args['settings']['mirror-wrapper']) )
			unset($args['settings']['mirror-wrapper']);

		$random_prefix = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 2)), 0, 2);

		//Build insert args
		$insert_args = array(
			'id' => uniqid('w' . strtolower(substr($random_prefix, 0, 2))),
			'template' => padma_get('template', $args, PadmaOption::$current_skin),
			'layout' => $layout_id,
			'position' => $args['position'],
			'settings' => padma_maybe_serialize(padma_get('settings', $args, array())),
			'mirror_id' => $mirror_id
		);

		if ( $insert_id = padma_get('insert_id', $args) )
			$insert_args['id'] = $insert_id;

		if ( $legacy_id = padma_get('legacy_id', $args) )
			$insert_args['legacy_id'] = $legacy_id;

		//Run the query
		$wpdb->insert($wpdb->pu_wrappers, $insert_args);

		//All done. Spit back ID of newly created wrapper.
		return $insert_args['id'];

	}


	public static function update_wrapper($wrapper_id, $args) {

		global $wpdb;

		$wrapper_to_be_updated = self::get_wrapper($wrapper_id);

		/* Make sure wrapper exists */
		if ( !$wrapper_to_be_updated )
			return null;

		/* Map mirror-wrapper setting to mirror_id column */
		if ( isset($args['settings']) && isset($args['settings']['mirror-wrapper']) ) {

			$args['mirror_id'] = $args['settings']['mirror-wrapper'];
			unset($args['settings']['mirror-wrapper']);

		}

		/* Handle template argument */
			$template = padma_get('template', $args, PadmaOption::$current_skin);

			if ( isset($args['template']) )
				unset($args['template']);

		/* Query */
		$query = $wpdb->update($wpdb->pu_wrappers, array_map('padma_maybe_serialize', $args), array(
			'template' => $template,
			'id' => $wrapper_id
		));

		return $query;

	}


	/**
	 * @todo remove design instances and remove blocks in that wrapper here
	 * @param $wrapper_id
	 *
	 * @return null
	 */
	public static function delete_wrapper($layout_id, $wrapper_id) {

		global $wpdb;

		$wrapper_to_be_deleted = self::get_wrapper($wrapper_id);

		/* Make sure wrapper exists */
		if ( !$wrapper_to_be_deleted )
			return null;

		/* Query for deletion */
		$wrapper_delete_query = $wpdb->delete( $wpdb->pu_wrappers, array(
			'template' => PadmaOption::$current_skin,
			'id' => $wrapper_id
		));

		/* Delete design settings */
		self::delete_wrapper_design_instances($layout_id, $wrapper_id);

		/* Unmirror the wrappers mirroring this wrappers */
		$wpdb->update( $wpdb->pu_wrappers, array(
			'mirror_id' => ''
		), array(
			'mirror_id' => $wrapper_id
		));

		/* Delete all blocks in that wrapper */
		$blocks_delete_query = PadmaBlocksData::delete_by_wrapper($layout_id, $wrapper_id);

		return array($wrapper_delete_query, $blocks_delete_query);

	}


	public static function delete_wrapper_design_instances($layout_id, $wrapper_id) {

		return PadmaElementsData::delete_special_element_properties(null, 'wrapper', 'instance', 'wrapper-' . PadmaWrappers::format_wrapper_id($wrapper_id));

	}


	public static function delete_by_layout($layout_id) {

		global $wpdb;

		$layout_wrappers = self::get_wrappers_by_layout($layout_id);
		$return = array();

		foreach ( $layout_wrappers as $wrapper_id => $options ) {
			$return[$wrapper_id] = self::delete_wrapper($layout_id, $wrapper_id);
		}

		return $return;

	}


	public static function delete_by_template($template) {

		global $wpdb;

		return $wpdb->delete( $wpdb->pu_wrappers, array(
			'template' => $template
		));

	}


	public static function get_wrapper($wrapper, $use_mirrored = false) {

		global $wpdb;

		/* If $wrapper is already an array then validate it and return it */
		if ( is_array($wrapper) ) {

			if ( isset($wrapper['id']) ) {
				return $wrapper;
			} else {
				return null;
			}

		}

		/* Build cache key */
		$cache_key = 'pu_wrapper_' . $wrapper;

		if ( $use_mirrored )
			$cache_key .= '_using_mirrored';

		/* Check cache */
		$wrapper_from_cache = wp_cache_get($cache_key);

		if ( $wrapper_from_cache !== false )
			return $wrapper_from_cache;

		/* Not cached... Retrieve wrapper  */
		if ( is_string($wrapper) || is_numeric($wrapper) ) {

			$wrapper_id = PadmaWrappers::format_wrapper_id($wrapper);

			$wrapper_query = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->pu_wrappers WHERE template = '%s' AND id = '%s'", PadmaOption::$current_skin, $wrapper_id), ARRAY_A);

			if ( is_array($wrapper_query) && !is_wp_error($wrapper_query) ) {

				$wrapper = array_map('padma_maybe_unserialize', $wrapper_query);

			} else {

				/* If no wrapper is found, try querying the legacy_id */
				$wrapper_from_legacy_id = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->pu_wrappers WHERE template = '%s' AND legacy_id = '%d'", PadmaOption::$current_skin, $wrapper_id), ARRAY_A);

				if ( is_array($wrapper_from_legacy_id) && ! is_wp_error( $wrapper_from_legacy_id ) ) {
					$wrapper = array_map( 'padma_maybe_unserialize', $wrapper_from_legacy_id );
				} else {
					$wrapper = null;
				}

			}

		}

		if ( is_array($wrapper) && !isset($wrapper['id']) )
			return null;

		/* Fetch the mirrored wrapper if $use_mirrored is true */
		if ( $use_mirrored === true && $mirrored_wrapper = self::get_wrapper_mirror($wrapper) )
			$wrapper = $mirrored_wrapper;

		wp_cache_set($cache_key, $wrapper);

		return $wrapper;

	}


	public static function get_wrappers_by_layout($layout_id, $include_styling = false) {

		global $wpdb;

		/* Build cache key */
		$cache_key = 'pu_wrappers_by_layout_' . $layout_id;

		if ( $include_styling )
			$cache_key = $cache_key . '_with_styling';

		/* Check cache */
		$layout_wrappers = wp_cache_get($cache_key);

		if ( $layout_wrappers === false ) {

			/* Retrieve all wrappers from layout */
			$layout_wrappers_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->pu_wrappers WHERE template = '%s' AND layout = '%s' ORDER BY position ASC", PadmaOption::$current_skin, $layout_id), ARRAY_A);

			/* Change results array into associative */
			$layout_wrappers = array();

			foreach ( $layout_wrappers_query as $layout_wrapper ) {
				$layout_wrappers[$layout_wrapper['id']] = array_map('padma_maybe_unserialize', $layout_wrapper);
			}

			/* If wrapper array is empty then load the default wrapper array */
			if ( !count($layout_wrappers) ) {
				$layout_wrappers = PadmaWrappers::$default_wrappers;
				$layout_wrappers['default']['layout'] = $layout_id;
			}

			if ( $include_styling ) {
				$layout_wrappers = self::add_wrapper_styling_to_array($layout_wrappers);
			}

			wp_cache_set($cache_key, $layout_wrappers);

		}

		return $layout_wrappers;

	}


	public static function get_all_wrappers($include_styling = false, $mirrored_only = false) {

		global $wpdb;

		/* Build cache key */
		$cache_key = 'pu_all_wrappers';

		if ( $include_styling )
			$cache_key .= '_with_styling';

		if ( $mirrored_only )
			$cache_key .= '_mirrored_only';

		/* Check cache */
		$wrappers_from_cache = wp_cache_get($cache_key);

		if ( $wrappers_from_cache !== false )
			return $wrappers_from_cache;

		/* Not cached... Retrieve all wrappers  */
		$query = "SELECT * FROM $wpdb->pu_wrappers WHERE template = '%s'";

		if ( $mirrored_only )
			$query .= " AND mirror_id <> ''";

		$wrapper_query = $wpdb->get_results($wpdb->prepare($query, PadmaOption::$current_skin), ARRAY_A);

		/* Change results array into associative */
		$wrappers = array();

		foreach ( $wrapper_query as $wrapper ) {
			$wrappers[$wrapper['id']] = array_map('padma_maybe_unserialize', $wrapper);
		}

		if ( $include_styling ) {
			$wrappers = self::add_wrapper_styling_to_array($wrappers);
		}

		wp_cache_set($cache_key, $wrappers);

		return $wrappers;

	}


	public static function add_wrapper_styling_to_array($wrappers) {

		if ( !is_array($wrappers) )
			return false;

		/* If the array provided is just one wrapper, then put it into an array */
			if ( isset($wrappers['id']) && isset($wrappers['position']) ) {
				$wrappers = array($wrappers);
			}

		foreach ( $wrappers as $wrapper_id => $wrapper ) {

			$wrappers[$wrapper_id]['styling'] = PadmaElementsData::get_special_element_properties(array(
				'element' => 'wrapper',
				'se_type' => 'instance',
				'se_meta' => 'wrapper-' . PadmaWrappers::format_wrapper_id($wrapper_id)
			));

		}

		return $wrappers;

	}


	public static function is_wrapper_mirrored($wrapper) {

		$wrapper = self::get_wrapper($wrapper);

		if ( $mirrored_wrapper_id = padma_get('mirror_id', $wrapper) )
			return $mirrored_wrapper_id;

		return false;

	}


	public static function get_wrapper_mirror($wrapper) {

		$wrapper = self::get_wrapper($wrapper);

		if ( $mirrored_wrapper_id = padma_get('mirror_id', $wrapper) )
			return self::get_wrapper($mirrored_wrapper_id);

		return false;

	}


	public static function wrapper_exists($id) {

		if ( self::get_wrapper($id) )
			return true;

		return false;

	}

	public static function get_wrapper_setting($wrapper, $option_name, $default_value = null) {

		if (!is_array($wrapper))
			$wrapper = PadmaWrappersData::get_wrapper($wrapper);

		return padma_get($option_name, padma_get('settings', $wrapper, array()), $default_value);

	}


	public static function get_legacy_id( $wrapper ) {

		return is_numeric( padma_get( 'legacy_id', $wrapper ) ) && padma_get( 'legacy_id', $wrapper ) > 0 ? padma_get( 'legacy_id', $wrapper ) : $wrapper['id'];

	}


}