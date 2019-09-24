<?php

class PadmaAdminPages {


	/**
	 * @see PadmaAdmin::visual_editor_redirect
	 *
	 * This function is here strictly for backup.  The PHP header location should replace all of this.
	 **/
	public static function visual_editor() {

		PadmaAdmin::show_header('Padma Visual Editor');

			echo sprintf( 
				__('<p>You are now being redirected. If you are not redirected within 3 seconds, click <a href="%s"><strong>here</strong></a>.</p>','padma'), 
				home_url() . '/?visual-editor=true'
			);

			echo '<meta http-equiv="refresh" content="3;URL=' . home_url() . '/?visual-editor=true">';
		
		PadmaAdmin::show_footer();

	}


	public static function getting_started() {

		PadmaAdmin::show_header();

			require_once PADMA_LIBRARY_DIR . '/admin/pages/getting-started.php';

		PadmaAdmin::show_footer();

	}


	public static function templates() {

		PadmaAdmin::show_header();

			require_once PADMA_LIBRARY_DIR . '/admin/pages/templates.php';

		PadmaAdmin::show_footer();

	}


	public static function options() {

		PadmaAdmin::show_header();

			require_once PADMA_LIBRARY_DIR . '/admin/pages/options.php';

		PadmaAdmin::show_footer();

	}


	public static function tools() {

		PadmaAdmin::show_header();

			require_once PADMA_LIBRARY_DIR . '/admin/pages/tools.php';

		PadmaAdmin::show_footer();

	}

	public static function license() {

		PadmaAdmin::show_header();

			require_once PADMA_LIBRARY_DIR . '/admin/pages/license.php';

		PadmaAdmin::show_footer();

	}

}
