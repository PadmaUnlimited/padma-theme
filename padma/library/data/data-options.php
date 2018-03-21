<?php
/**
 * Functions to get, update, and delete data from the database.
 *
 * @package Padma
 * @subpackage Data Handling
 * @author Clay Griffiths
 **/

class PadmaOption {


	/**
	 * Set the default group for all of the database functions to get, set, and delete from.
	 **/
	protected static $default_group = 'general';


	/**
	 * Flag for fetching from skin options.
	 **/
	public static $is_skin_option = false;

	public static $current_skin;


	public static function init() {

		self::$current_skin = PadmaTemplates::get_active_id();

	}


	/**
	 * Retrieve a value from the database.
	 *
	 * @param string Option to retrieve
	 * @param string Option group to fetch from
	 * @param mixed Default value to be returned.  This will be returned if the requested option does not exist.
	 *
	 * @return mixed
	 **/
	public static function get($option = null, $group_name = false, $default = null, $main_site = false, $fix_data_type = true) {

		if ( $option === null )
			return false;

		if ( !$group_name )
			$group_name = self::$default_group;

		$group_data = self::get_group($group_name, $main_site);

		/* If option doesn't exist, return default. */
		if ( !isset($group_data[$option]) )
			return $default;

		if ( !$fix_data_type ) {
			return $group_data[$option];
		}
		
		return padma_fix_data_type($group_data[$option]);

	}


		public static function get_group($group_name, $main_site = false) {

			/* Query for the option group */
				$group_data = self::get_wp_option('padma_option_group_' . $group_name, $main_site);

			return $group_data;

		}


		/**
		 * Format option name
		 **/
		public static function format_wp_option($option) {

			/* Format option name */
				if ( self::$is_skin_option )
					$option = str_replace('padma_', 'pu_|template=' . self::$current_skin . '|_', $option);

			return $option;

		}


		/**
		 * Function for using get_option() or get_blog_option() depending on second argument
		 **/
		public static function get_wp_option($option, $main_site = false) {

			global $current_site;

			$alloptions = wp_load_alloptions();

			/* Format option name */
				$option = self::format_wp_option($option);

			/* Pull option */
			if ( $main_site && is_multisite() ) {
				return get_blog_option( $current_site->blog_id, $option );
			}

			if ( isset($alloptions[$option]) ) {
				return padma_maybe_unserialize($alloptions[$option]);
			} else {
				return get_option( $option );
			}

		}


		public static function get_from_main_site($option = null, $group_name = false, $default = null) {

			return self::get($option, $group_name, $default, false, true);

		}


	/**
	 * Add or update an option on the database.
	 *
	 * @param string Option to set
	 * @param mixed Value to attach to option
	 * @param string Group to add/update the option to
	 *
	 * @return bool
	 **/
	public static function set($option = null, $value = null, $group_name = false) {

		if ( $option === null )
			return false;

		if ( $value === null )
			return false;

		if ( !$group_name )
			$group_name = self::$default_group;

		$group_option_name = self::format_wp_option('padma_option_group_' . $group_name);

		/* Pull in existing data so we can add on top of it */
		$group_data = get_option($group_option_name, array());

		/* Handle boolean values */
		if ( is_bool($value) )
			$value = ( $value === true ) ? 'true' : 'false';

		/* Add option */
		$group_data[$option] = $value;

		/* Send group option to DB */
		return update_option($group_option_name, $group_data);

	}


		public static function set_group($group_name, $group_data) {

			$group_option_name = self::format_wp_option('padma_option_group_' . $group_name);

			return update_option($group_option_name, $group_data);;

		}


	/**
	 * Delete option from database.
	 *
	 * @param string Option to delete
	 * @param string Group to delete from
	 *
	 * @return bool
	 **/
	public static function delete($option = null, $group_name = false) {

		//return wp_die('Deleting some shit right now.  Trying: ' . $option);

		if ( $option === null )
			return false;

		if ( !$group_name )
			$group_name = self::$default_group;

		$option_name = self::format_wp_option('padma_option_group_' . $group_name);
		$group_data = get_option($option_name);

		//If the group isn't in the DB or the option doesn't exist
		if( !is_array($group_data) || !isset($group_data[$option]) )
			return false;

		//Delete option from group
		unset($group_data[$option]);

		//If the array is still fine and not empty, just update the group on the DB
		if ( count($group_data) !== 0 )
			return update_option($option_name, $group_data);

		//Remove group from DB
		return delete_option($option_name);

	}


}