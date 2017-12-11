<?php
class BloxWidgets {
	
	
	public static function init() {
		
		add_filter('get_search_form', array(__CLASS__, 'search_form'));
		
		add_filter('widget_title', array(__CLASS__, 'remove_unnecessary_nbsp_from_titles'));
		
	}
	
	
	public static function search_form() {

		return blox_get_search_form();
		
	}
	
	
	public static function remove_unnecessary_nbsp_from_titles($title) {
		
		if ( $title == '&nbsp;' )
			return null;
			
		return $title;
		
	}
	
	
}