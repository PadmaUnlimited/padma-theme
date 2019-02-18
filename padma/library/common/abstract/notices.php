<?php

/*
	Based on Class Shortcodes_Ultimate_Notice
	Plugin URI: https://getshortcodes.com/
 	Version: 5.2.0
 	Author: Vladimir Anokhin
 	Author URI: https://vanokhin.com/

*/

/**
 * The abstract class for creating admin notices.
 *
 * @since        1.0.0
 */
abstract class PadmaNotice {

	/**
	 * The ID of the notice.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $notice_id    The ID of the notice.
	 */
	protected static $notice_id;

	/**
	 * The full path to the notice template file.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $template_file    The full path to the notice template file.
	 */
	protected static $template_file;

	/**
	 * The delay before displaying the notice at the first time (in seconds).
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $first_time_delay   The delay before displaying the notice at the first time (in seconds).
	 */
	protected static $first_time_delay;

	/**
	 * The delay for deferring the notice (in seconds).
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $defer_delay   The delay for deferring the notice (in seconds).
	 */
	protected static $defer_delay;

	/**
	 * The required user capability to view the notice.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $capability   The required user capability to view the notice.
	 */
	protected static $capability;

	/**
	 * The name of the option to store dismissed notices.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $option_name   The name of the option to store dismissed notices.
	 */
	private static $option_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function __construct() {

	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param string  $notice_id     The ID of the notice.
	 * @param string  $template_file The full path to the notice template file.
	 */
	public static function notice($notice_id, $template_file){

		self::$notice_id        = $notice_id;
		self::$template_file    = $template_file;
		self::$first_time_delay = 0;
		self::$defer_delay      = 1 * DAY_IN_SECONDS;
		self::$capability       = 'manage_options';
		self::$option_name      = 'padma_option_dismissed_notices';

	}

	/**
	 * This method should be implemented by childs.
	 *
	 * @since  1.0.0
	 */
	abstract static function display_notice();

	/**
	 * Include template file.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param mixed   $data The data to pass to the template.
	 */
	protected function include_template( $data = null ) {
		
		if ( file_exists( self::$template_file ) ) {
			include self::$template_file;
		}

	}

	/**
	 * Set new status for the notice.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param string  $status New status. Can be 'dismissed' or 'deferred'
	 */
	protected function update_notice_status( $status ) {

		$dismissed = self::get_dismissed_notices();
		$id        = self::$notice_id;

		if ( $status === 'dismissed' ) {
			$dismissed[$id] = true;
		}

		elseif ( $status === 'deferred' ) {
			$dismissed[$id] = time() + (int) self::$defer_delay;
		}

		elseif ( is_numeric( $status ) ) {
			$dismissed[$id] = time() + (int) $status;
		}

		update_option( self::$option_name, $dismissed );

	}

	/**
	 * Dismiss the notice.
	 *
	 * @since  1.0.0
	 */
	public static function dismiss_notice() {

		if ( ! self::current_user_can_view() ) {
			return;
		}

		if ( ! isset( $_GET['nonce'], $_GET['id'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_GET['nonce'], 'padma_dismiss_notice' ) ) {
			return;
		}

		if ( $_GET['id'] !== self::$notice_id ) {
			return;
		}

		$status = empty( $_GET['defer'] ) ? 'dismissed' : 'deferred';

		self::update_notice_status( $status );

		if ( isset( $_GET['redirect_to'] ) ) {
			wp_safe_redirect( $_GET['redirect_to'] );
			exit;
		}

		if ( ! wp_get_referer() ) {
			return;
		}

		wp_safe_redirect( wp_get_referer() );
		exit;

	}

	/**
	 * Retrieve the link to dismiss the notice.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param bool    $defer    Defer the notice instead of dismissing.
	 * @param string  $redirect Custom redirect URL.
	 * @return string           The admin url.
	 */
	protected function get_dismiss_link( $defer = false, $redirect = '' ) {

		$link = admin_url( sprintf(
				'admin-post.php?action=%s&nonce=%s&id=%s',
				'padma_dismiss_notice',
				wp_create_nonce( 'padma_dismiss_notice' ),
				self::$notice_id
			) );

		if ( $defer ) {
			$link = add_query_arg( 'defer', 1, $link );
		}

		if ( $redirect ) {
			$link = add_query_arg( 'redirect_to', esc_url( $redirect ), $link );
		}

		return $link;

	}

	/**
	 * This conditional tag checks if the notice has been dismissed.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return boolean True if the notice has been dismissed, false if not.
	 */
	protected function is_dismissed() {

		$dismissed = self::get_dismissed_notices();
		$id        = self::$notice_id;

		// No data about the notice (not dismissed/deferred)
		if ( ! isset( $dismissed[$id] ) ) {
			return false;
		}

		// Notice deferred
		if ( is_numeric( $dismissed[$id] ) && time() < $dismissed[$id] ) {
			return true;
		}

		// Notice dismissed
		if ( $dismissed[$id] === true ) {
			return true;
		}

		// Default behavior
		return false;

	}

	/**
	 * Defer the notice at the first time it should be displayed.
	 *
	 * @since  1.0.0
	 */
	public static function defer_first_time() {

		$dismissed = self::get_dismissed_notices();
		$id        = self::$notice_id;

		if ( ! isset( $dismissed[$id] ) ) {
			self::update_notice_status( self::$first_time_delay );
		}

	}

	/**
	 * Helper function to retrieve dismissed notices.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return mixed Dismissed notices.
	 */
	protected static function get_dismissed_notices() {
		return get_option( self::$option_name, array() );
	}

	/**
	 * Helper function to retrieve the ID of the current screen.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string  The ID of the current screen.
	 */
	protected static function get_current_screen_id() {

		$screen = get_current_screen();
		return $screen->id;

	}

	/**
	 * Check if current user can view the notice.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return bool       True if current user can view the notice, False otherwise.
	 */
	protected static function current_user_can_view() {
		return current_user_can( self::$capability );
	}

}
