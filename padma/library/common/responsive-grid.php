<?php
class PadmaResponsiveGrid {
	
	
	public static function init() {

		if ( !self::is_enabled() )
			return false;

		add_action('padma_head_extras', array(__CLASS__, 'add_meta_viewport'));
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
		if ( PadmaRoute::is_visual_editor() || PadmaRoute::is_visual_editor_iframe() || padma_get('visual-editor-open') )
			return false;
			
		return true;
				
	}
	
	
	public static function is_user_disabled() {
		
		if ( padma_get('full-site') != 'false' )
			if ( padma_get('padma-full-site', $_COOKIE) == 1 || padma_get('full-site') == 'true' )
				return true;
				
		return false;
		
	}
	
	
	public static function is_enabled() {
		
		//If the theme doesn't support the responsive grid, then disable it.
		if ( !current_theme_supports('padma-grid') || !current_theme_supports('padma-responsive-grid') )
			return false;
		
		return PadmaSkinOption::get('enable-responsive-grid', false, true);
		
	}
	
	
	public static function add_meta_viewport() {
		
		if ( !self::is_active() )
			return false;

		if(PadmaOption::get('allow-mobile-zooming')){

			echo '<meta name="viewport" content="width=device-width, user-scalable=yes, minimum-scale=1.0, maximum-scale=10.0" />' . "\n";

		}else{

			echo '<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />' . "\n";
		}
		
		
	}
	
	
	public static function cookie_baker() {
				
		/* If headers were already sent, then don't follow through with this function or it will err. */
		if ( headers_sent() )
			return false;
				
		if ( padma_get('full-site') == 'true' )
			return setcookie('padma-full-site', 1, time() + 60 * 60 * 24 * 7, '/');
			
		if ( padma_get('full-site') == 'false' )
			return setcookie('padma-full-site', false, time() - 3600, '/');			
			
		return false;
		
	}
	
	
}