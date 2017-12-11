<?php
class BloxAdminPages {


	/**
	 * @see BloxAdmin::visual_editor_redirect
	 *
	 * This function is here strictly for backup.  The PHP header location should replace all of this.
	 **/
	public static function visual_editor() {

		BloxAdmin::show_header('Blox Visual Editor');
			echo '<p>You are now being redirected.  If you are not redirected within 3 seconds, click <a href="' . home_url() . '/?visual-editor=true"><strong>here</strong></a>.</p>';
			echo '<meta http-equiv="refresh" content="3;URL=' . home_url() . '/?visual-editor=true">';
		BloxAdmin::show_footer();

	}


	public static function getting_started() {

		BloxAdmin::show_header();

			require_once BLOX_LIBRARY_DIR . '/admin/pages/getting-started.php';

		BloxAdmin::show_footer();

	}


	public static function templates() {

		BloxAdmin::show_header();

			require_once BLOX_LIBRARY_DIR . '/admin/pages/templates.php';

		BloxAdmin::show_footer();

	}


	public static function options() {

		BloxAdmin::show_header();

			require_once BLOX_LIBRARY_DIR . '/admin/pages/options.php';

		BloxAdmin::show_footer();

	}


	public static function tools() {

		BloxAdmin::show_header();

			require_once BLOX_LIBRARY_DIR . '/admin/pages/tools.php';

		BloxAdmin::show_footer();

	}

	public static function license() {

		BloxAdmin::show_header();

			require_once BLOX_LIBRARY_DIR . '/admin/pages/license.php';

		BloxAdmin::show_footer();

	}

}
