<?php
class PadmaMaintenance {

	public static $available_upgrades = array(
		'1.3.1',
		'1.3.9',
	);

	/**
	 * Over time, there may be issues to be corrected between updates or naming conventions to be changed between updates.
	 * All of that will be processed here.
	 **/
	static function do_upgrades($version_to_upgrade = false) {

		$padma_settings = get_option('padma', array('version' => 0));
		$db_version = $padma_settings['version'];

		if ( get_option('padma_upgrading') == 'upgrading' ) {

			if ( !is_admin() ) {
				return wp_die('Website upgrade in progress. Please check back again soon!');
			} else {
				return false;
			}

		}

		self::setup_upgrade_environment();
		PadmaMaintenance::output_status('Current DB version is ' . $db_version);

		if ( $db_version == PADMA_VERSION ) {
			return false;
		}

		/* Add current version to upgrades if it's not there so the basic upgrade routine is still ran */
		if ( !in_array(PADMA_VERSION, self::$available_upgrades) ) {
			self::$available_upgrades[] = PADMA_VERSION;
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

				if ( file_exists(PADMA_LIBRARY_DIR . '/maintenance/upgrade-' . $version_filename . '.php') ) {
					require_once PADMA_LIBRARY_DIR . '/maintenance/upgrade-' . $version_filename . '.php';
				}

				do_action('padma_do_upgrade_' . $version_filename);

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

	}


	public static function output_status( $text ) {

		if ( function_exists('getmypid') ) {

			if ( $pid = @getmypid() ) {
				error_log('Padma Upgrade Status (PID = ' . $pid . '): ' . $text);
				return true;
			}

		}

		error_log('Padma Upgrade Status: ' . $text);
		return true;

	}


	public static function start_upgrade($version) {

		update_option( 'padma_upgrading', 'upgrading' );

		self::output_status('Currently Upgrading to ' . $version );

	}


	public static function after_upgrade($version) {

		/* Update the version here. */
		$padma_settings            = get_option( 'padma', array( 'version' => 0 ) );
		$padma_settings['version'] = $version;

		update_option( 'padma', $padma_settings );
		delete_option( 'padma_upgrading' );

		PadmaMaintenance::output_status( 'Setting DB version to ' . $version );

		/* Flush caches */
		do_action( 'padma_db_upgrade' );

		if (PadmaOption::get('headway-support')) {
			do_action('headway_db_upgrade');
		}

		if (PadmaOption::get('bloxtheme-support')) {
			do_action('blox_db_upgrade');
		}

		Padma::set_autoload();

		/* Run next upgrade if available */
		$index_of_current_version = array_search($version, self::$available_upgrades);

		if ( isset(self::$available_upgrades[$index_of_current_version + 1]) ) {

			$next_upgrade = self::$available_upgrades[$index_of_current_version + 1];

			return self::do_upgrades($next_upgrade);

		} else {

			Padma::db_dbdelta();
			PadmaElementsData::merge_core_default_design_data();

			if ( current_user_can('manage_options') && !is_front_page() ) {
				wp_safe_redirect( admin_url(), 302 );
			} else {
				wp_safe_redirect( home_url(), 302 );
			}

			die();

		}

	}

}
