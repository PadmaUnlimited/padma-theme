<?php
class BloxCapabilities {
		
		
	public static function init() {
		
		add_filter('members_get_capabilities', 'BloxCapabilities::register');
				
	}
	
	
	public static function register($capabilities) {
		
		$capabilities[] = 'blox_visual_editor';

		return apply_filters('blox_capabilities', $capabilities);
		
	}
	
	
	public static function can_user($capability) {
		
		if ( !function_exists('members_check_for_cap') )
			 return ( current_user_can('manage_options') || is_super_admin() );

		return current_user_can($capability);
		
	}
	
	
	/**
	 * Checks if the user can access the visual editor.
	 * 
	 * @uses blox_user_level()
	 * @uses BloxOption::get()
	 *
	 * @return bool
	 **/
	public static function can_user_visually_edit($ignore_debug_mode = false) {

		if ( !$ignore_debug_mode && BloxOption::get('debug-mode') )
			return true;

		return is_user_logged_in() && self::can_user('blox_visual_editor');
		
	}
	
	
}