<?php
/**
 * Functions to get, update, and delete data from the database.
 *
 * @package Blox
 * @subpackage Data Handling
 * @author Clay Griffiths
 **/

class BloxLayoutOption {


	/**
	 * Flag for fetching from skin options.
	 **/
	public static $is_skin_option = false;

	public static $current_skin;
	

	public static function init() {

		self::$current_skin = BloxTemplates::get_active_id();

	}


	public static function is_post($layout) {

		/* If only number is provided for layout then it's a WP post */
		if ( is_numeric($layout) )
			return $layout;

		$layout_fragments = explode(BloxLayout::$sep, $layout);

		if ( reset($layout_fragments) == 'single' && is_numeric(end($layout_fragments)) )
			return end($layout_fragments);

		return false;

	}


	public static function postmeta_option($option, $global, $template = false) {

		if ( !$template )
			$template = self::$current_skin;

		/* Global option */
		if ( $global ) {

			return '_bt_' . $option;

		/* Template-specific option */
		} else {

			return '_bt_|template=' . $template . '|_' . $option;

		}

	}


	public static function get($layout, $option, $default = null, $global = false, $group_prefix = false) {

		if ( $group_prefix && $group_prefix != 'general' )
			$option = $group_prefix . '_' . $option;

		/* If it's post meta compatible, then use WP functions */
		if ( $post_id = self::is_post($layout) ) {

			$options = get_post_custom($post_id);
			$option = self::postmeta_option($option, $global);

			if ( !$options || !isset($options[$option]) )
				return $default;

			$return = $options[$option][0];

		/* Otherwise use the Blox layout meta table */
		} else {

			global $wpdb;

			$template = $global ? '' : self::$current_skin;

			$cache_key = 'bt_layout_options_|template=' . $template . '|_' . $layout;
			$layout_options = wp_cache_get($cache_key);

			if ( $layout_options === false ) {

				$options = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->bt_layout_meta WHERE layout = '%s' AND template = '%s'", $layout, $template), ARRAY_A);

				if ( is_wp_error($options) || !is_array($options) || !count($options) ) {
					wp_cache_set($cache_key, array());
					return $default;
				}

				/* Loop through results and make it into an associative array */
				$layout_options = array();

				foreach ( $options as $option_row ) {
					$layout_options[$option_row['meta_key']] = $option_row['meta_value'];
				}

				wp_cache_set($cache_key, $layout_options);

			}

			if ( !isset($layout_options[$option]) )
				return $default;

			$return = $layout_options[$option];

		}

		return blox_fix_data_type($return);
		
	}

	
	public static function set($layout, $option, $value, $global = false, $group_prefix = false, $template = null) {

		if ( !$template )
			$template = self::$current_skin;

		if ( $group_prefix && $group_prefix != 'general' )
			$option = $group_prefix . '_' . $option;

		/* If it's post meta compatible, then use WP functions */
		if ( $post_id = self::is_post($layout) ) {

			$option = self::postmeta_option($option, $global, $template);

			return update_post_meta($post_id, $option, $value);

		/* Otherwise use the Blox layout meta table */
		} else {

			global $wpdb;

			$template = $global ? '' : $template;

			/* Check if option exists.  If it does, use $wpdb->update, otherwise use $wpdb->insert */
			if ( $meta_id = $wpdb->get_var($wpdb->prepare("SELECT meta_id FROM $wpdb->bt_layout_meta WHERE layout = '%s' AND template = '%s' AND meta_key = '%s'", $layout, $template, $option)) ) {

				return $wpdb->update($wpdb->bt_layout_meta, array(
					'meta_value' => blox_maybe_serialize($value)
				), array(
					'meta_id' => $meta_id
				));

			} else {

				return $wpdb->insert($wpdb->bt_layout_meta, array(
					'meta_key' => $option,
					'meta_value' => blox_maybe_serialize($value),
					'layout' => $layout,
					'template' => $template
				));

			}

		}
		
	}
	
	
	public static function delete($layout, $option, $global = false) {

		/* If it's post meta compatible, then use WP functions */
		if ( $post_id = self::is_post($layout) ) {

			$option = self::postmeta_option($option, $global);

			return delete_post_meta($post_id, $option);

		/* Otherwise use the Blox layout meta table */
		} else {

			global $wpdb;

			$template = $global ? '' : self::$current_skin;

			return $wpdb->delete($wpdb->bt_layout_meta, array(
				'layout' => $layout,
				'meta_key' => $option,
				'template' => $template
			));

		}
		
	}


	public static function delete_all_from_layout($layout, $global = false) {

		/* If it's post meta compatible, then use WP functions */
		if ( $post_id = self::is_post($layout) ) {

			$options = get_post_custom($post_id);
			$options_deleted = array();

			$key_prefix_to_check = $global ? '_bt_' : '_bt_|template=' . self::$current_skin . '|_';

			foreach ( $options as $meta_key => $meta_id ) {

				if ( strpos($meta_key, $key_prefix_to_check) !== 0 )
					continue;

				$options_deleted = delete_post_meta($post_id, $meta_key);

			}

			return $options_deleted;

		/* Otherwise use the Blox layout meta table */
		} else {

			global $wpdb;

			if ( $global ) {

				return $wpdb->delete($wpdb->bt_layout_meta, array(
					'layout' => $layout
				));

			} else {

				return $wpdb->delete($wpdb->bt_layout_meta, array(
					'layout' => $layout,
					'template' => self::$current_skin
				));

			}

		}

	}


	public static function delete_by_template($template) {

		global $wpdb;

		$wpdb->delete($wpdb->bt_layout_meta, array(
			'template' => $template
		));

		$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%s'", '_bt_|template=' . $template . '|_%'));

	}

	
}