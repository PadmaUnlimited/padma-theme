<?php
/**
 * 3.7.1
 *
 * Change wrapper instance IDs and change MySQL collate and charset
 */
add_action('padma_do_upgrade_371', 'padma_do_upgrade_371');
function padma_do_upgrade_371() {

	global $wpdb;

	/* Alter MySQL schema */
	Padma::mysql_dbdelta();

	/* Loop through installed Templates and fix wrapper instance IDs */
	$templates = PadmaTemplates::get_all(true, true);

	foreach ( $templates as $template_id => $template ) {

		$template_design_settings = get_option( 'pu_|template=' . $template_id . '|_option_group_design', array() );

		if ( !empty($template_design_settings) ) {

			$template_design_settings = padma_preg_replace_json( "/-layout-[\w-]*/", '', $template_design_settings );
			update_option( 'pu_|template=' . $template_id . '|_option_group_design', $template_design_settings );

		}

	}

}