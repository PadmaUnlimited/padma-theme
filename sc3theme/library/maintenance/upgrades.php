<?php
class BloxMaintenance {

	public static $available_upgrades = array(
		'1.0.1',
		'1.0.2'
	);

	/**
	 * Over time, there may be issues to be corrected between updates or naming conventions to be changed between updates.
	 * All of that will be processed here.
	 **/
	static function do_upgrades($version_to_upgrade = false) {

		$blox_settings = get_option('blox', array('version' => 0));
		$db_version = $blox_settings['version'];

		if ( get_option('blox_upgrading') == 'upgrading' ) {

			if ( !is_admin() ) {
				return wp_die('Website upgrade in progress. Please check back again soon!');
			} else {
				return false;
			}

		}

		self::setup_upgrade_environment();
		BloxMaintenance::output_status('Current DB version is ' . $db_version);

		if ( $db_version == BLOX_VERSION ) {
			return false;
		}

		/* Add current version to upgrades if it's not there so the basic upgrade routine is still ran */
		if ( !in_array(BLOX_VERSION, self::$available_upgrades) ) {
			self::$available_upgrades[] = BLOX_VERSION;
		}

		if ( !$version_to_upgrade ) {

			foreach ( self::$available_upgrades as $possible_upgrade ) {

				if ( version_compare( $db_version, $possible_upgrade, '<' ) ) {

					$version_to_upgrade = $possible_upgrade;
					break;

				}

			}

		}

		/* Do specified upgrade routine */
		if ( $upgrade_in_progress = $version_to_upgrade ) {

			$version_filename = str_replace( '.', '', $upgrade_in_progress );

			if ( version_compare( $db_version, $upgrade_in_progress, '<' ) ) {

				self::start_upgrade($upgrade_in_progress);

				if ( file_exists(BLOX_LIBRARY_DIR . '/maintenance/upgrade-' . $version_filename . '.php') ) {
					require_once BLOX_LIBRARY_DIR . '/maintenance/upgrade-' . $version_filename . '.php';
				}

				do_action('blox_do_upgrade_' . $version_filename);

				self::after_upgrade($upgrade_in_progress);

			}

		}

		return true;

	}


	public static function setup_upgrade_environment() {

		global $wpdb;

		@ignore_user_abort( true );
		@set_time_limit( 0 );

		if ( function_exists('apc_clear_cache') ) {
			apc_clear_cache();
		}

		$wpdb->flush();
		$wpdb->query("SET SESSION query_cache_type=0;");

		/* Attempt to raise memory limit to max */
		@ini_set( 'memory_limit', apply_filters( 'blox_memory_limit', WP_MAX_MEMORY_LIMIT ) );

	}


	public static function output_status( $text ) {

		if ( function_exists('getmypid') ) {

			if ( $pid = @getmypid() ) {
				error_log('Blox Upgrade Status (PID = ' . $pid . '): ' . $text);
				return true;
			}

		}

		error_log('Blox Upgrade Status: ' . $text);
		return true;

	}


	public static function start_upgrade($version) {

		update_option( 'blox_upgrading', 'upgrading' );

		self::output_status('Currently Upgrading to ' . $version );

	}


	public static function after_upgrade($version) {

		/* Update the version here. */
		$blox_settings            = get_option( 'blox', array( 'version' => 0 ) );
		$blox_settings['version'] = $version;

		update_option( 'blox', $blox_settings );
		delete_option( 'blox_upgrading' );

		BloxMaintenance::output_status( 'Setting DB version to ' . $version );

		/* Flush caches */
		do_action( 'blox_db_upgrade' );

		Blox::set_autoload();

		/* Run next upgrade if available */
		$index_of_current_version = array_search($version, self::$available_upgrades);

		if ( isset(self::$available_upgrades[$index_of_current_version + 1]) ) {

			$next_upgrade = self::$available_upgrades[$index_of_current_version + 1];

			return self::do_upgrades($next_upgrade);

		} else {

			Blox::mysql_dbdelta();
			BloxElementsData::merge_core_default_design_data();

			if ( current_user_can('manage_options') ) {
				wp_safe_redirect( admin_url() );
			} else {
				wp_safe_redirect( home_url() );
			}

			die();

		}

	}

}
