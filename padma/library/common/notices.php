<?php
/**
 * Padma Unlimited Theme.
 *
 * @package padma
 */

/**
 * Padma Notices class.
 */
class PadmaNotices extends PadmaNotice {

	/**
	 *
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Init method
	 *
	 * @return void
	 */
	public static function init() {

		if ( get_option( 'padma_deny_admin_notices' ) ) {
			return;
		}

		// Support Us Notice.
		self::supportUsNotice();

	}

	/**
	 * Support Us Notice.
	 *
	 * @return void
	 */
	private static function supportUsNotice(){

		self::notice( 'support-us', PADMA_LIBRARY_DIR . '/admin/partials/notices/support-us.php' );
		self::$defer_delay      = 7 * DAY_IN_SECONDS;
		self::$first_time_delay = 10 * MINUTE_IN_SECONDS; // 10 minutes

		add_action( 'load-plugins.php', array( __CLASS__, 'defer_first_time' ) );
		add_action( 'admin_notices', array( __CLASS__, 'display_notice' ) );
		add_action( 'admin_post_padma_dismiss_notice', array( __CLASS__, 'dismiss_notice' ) );

	}

	/**
	 * Display Notice
	 *
	 * @return void
	 */
	public static function display_notice() {

		// Check user capability.
		if ( ! self::current_user_can_view() ) {
			return;
		}

		// Make sure the notice is not dismissed.
		if ( self::is_dismissed() ) {
			return;
		}

		// Display the notice.
		self::include_template();
	}
}
