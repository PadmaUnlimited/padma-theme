<?php

class PadmaNotices extends PadmaNotice{
		

	public function __construct(  ) {

	}
		
	public static function init() {

		if(get_option('padma_deny_admin_notices'))
			return;
			
		// Support Us Notice	
		self::supportUsNotice();
				
	}

	private static function supportUsNotice(){

		self::notice( 'support-us', PADMA_LIBRARY_DIR . '/admin/partials/notices/support-us.php' );
		self::$defer_delay      = 4 * DAY_IN_SECONDS;		
		self::$first_time_delay = 14 * DAY_IN_SECONDS;

		add_action( 'load-plugins.php', array( __CLASS__, 'defer_first_time' ));
		add_action( 'admin_notices', array( __CLASS__, 'display_notice' ));
		add_action( 'admin_post_padma_dismiss_notice', array( __CLASS__, 'dismiss_notice' ));

	}


	public static function display_notice() {

		// Make sure this is the Plugins screen
		if ( self::get_current_screen_id() !== 'plugins' ) {
			return;
		}

		// Check user capability
		if ( ! self::current_user_can_view() ) {
			return;
		}

		// Make sure the notice is not dismissed
		if ( self::is_dismissed() ) {
			return;
		}

		// Display the notice
		self::include_template();
		

	}
	
	
	
	
}