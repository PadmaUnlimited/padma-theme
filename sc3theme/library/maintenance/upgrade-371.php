<?php
/**
 * 3.7.1
 *
 * Change wrapper instance IDs and change MySQL collate and charset
 */
add_action('blox_do_upgrade_371', 'blox_do_upgrade_371');
function blox_do_upgrade_371() {

	global $wpdb;

	/* Alter MySQL schema */
	Blox::mysql_dbdelta();

	/* Loop through installed Templates and fix wrapper instance IDs */
	$templates = BloxTemplates::get_all(true, true);

	foreach ( $templates as $template_id => $template ) {

		$template_design_settings = get_option( 'blox_|template=' . $template_id . '|_option_group_design', array() );

		if ( !empty($template_design_settings) ) {

			$template_design_settings = blox_preg_replace_json( "/-layout-[\w-]*/", '', $template_design_settings );
			update_option( 'blox_|template=' . $template_id . '|_option_group_design', $template_design_settings );

		}

	}

}