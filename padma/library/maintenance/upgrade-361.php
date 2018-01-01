<?php
/**
 * 3.6.1
 *
 * Do 3.6 design conversion if the Padma 3.6 design options is the same as the default.  This is to fix the bad 3.6 upgrade bug
 **/
add_action('padma_do_upgrade_361', 'padma_do_upgrade_361');
function padma_do_upgrade_361() {

	global $wpdb;

	$existing_design_settings = get_option('padma_option_group_design', array());

	if ( PadmaOption::$current_skin == PADMA_DEFAULT_SKIN && $existing_design_settings == PadmaElementsData::get_default_data() ) {

		$combined_design_settings = array();

		foreach ( $wpdb->get_results("SELECT * FROM $wpdb->options") as $option ) {

			if ( strpos($option->option_name, 'padma_option_group_design-editor-group') !== 0 )
				continue;

			$combined_design_settings = array_merge($combined_design_settings, maybe_unserialize($option->option_value));

		}

		$existing_design_settings['properties'] = $combined_design_settings;

		update_option('padma_option_group_design', $existing_design_settings);

	}

	PadmaMaintenance::output_status('Successfully Upgraded Design Editor Data');

}