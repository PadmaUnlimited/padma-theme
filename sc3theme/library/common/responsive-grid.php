<?php
class BloxResponsiveGrid {
	
	
	public static function init() {

		if ( !self::is_enabled() )
			return false;

		add_action('blox_head_extras', array(__CLASS__, 'add_meta_viewport'));
		add_action('init', array(__CLASS__, 'cookie_baker'));
		
	}
	
	
	/**
	 * Checks if the responsive grid is active or not.
	 * 
	 * Will check against the main option that's set in the Grid mode of the visual editor 
	 * and the cookie that disables the responsive grid if the visitor wishes to do so.
	 **/
	public static function is_active() {
		
		//If the responsive grid isn't enabled then don't bother.
		if ( !self::is_enabled() )
			return false;
			
		//If the user has clicked on the full site link in the footer block then it'll set this cookie that's being checked.
		if ( self::is_user_disabled() )
			return false;
			
		//If it's the visual editor or the visual editor iframe
		if ( BloxRoute::is_visual_editor() || BloxRoute::is_visual_editor_iframe() || blox_get('visual-editor-open') )
			return false;
			
		return true;
				
	}
	
	
	public static function is_user_disabled() {
		
		if ( blox_get('full-site') != 'false' )
			if ( blox_get('blox-full-site', $_COOKIE) == 1 || blox_get('full-site') == 'true' )
				return true;
				
		return false;
		
	}
	
	
	public static function is_enabled() {
		
		//If the theme doesn't support the responsive grid, then disable it.
		if ( !current_theme_supports('blox-grid') || !current_theme_supports('blox-responsive-grid') )
			return false;
		
		return BloxSkinOption::get('enable-responsive-grid', false, true);
		
	}
	
	
	public static function add_meta_viewport() {
		
		if ( !self::is_active() )
			return false;
		
		echo '<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />' . "\n";
		
	}
	
	
	public static function cookie_baker() {
				
		/* If headers were already sent, then don't follow through with this function or it will err. */
		if ( headers_sent() )
			return false;
				
		if ( blox_get('full-site') == 'true' )
			return setcookie('blox-full-site', 1, time() + 60 * 60 * 24 * 7, '/');
			
		if ( blox_get('full-site') == 'false' )
			return setcookie('blox-full-site', false, time() - 3600, '/');			
			
		return false;
		
	}
	
	
}