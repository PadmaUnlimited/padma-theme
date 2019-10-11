<?php
class PadmaCapabilities {


	public static function init() {

		add_filter('members_get_capabilities', 'PadmaCapabilities::register');

	}


	public static function register($capabilities) {

		$capabilities[] = 'padma_visual_editor';

		return apply_filters('padma_capabilities', $capabilities);

	}


	public static function can_user($capability) {

		if ( !function_exists('members_check_for_cap') )
			 return ( current_user_can('manage_options') || is_super_admin() );

		return current_user_can($capability);

	}


	/**
	 * Checks if the user can access the visual editor.
	 * 
	 * @uses padma_user_level()
	 * @uses PadmaOption::get()
	 *
	 * @return bool
	 **/
	public static function can_user_visually_edit($ignore_debug_mode = false) {

		if ( !$ignore_debug_mode && PadmaOption::get('debug-mode') )
			return true;

		return is_user_logged_in() && self::can_user('padma_visual_editor');

	}


}