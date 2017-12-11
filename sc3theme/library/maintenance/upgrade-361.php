<?php
/**
 * 3.6.1
 *
 * Do 3.6 design conversion if the Blox 3.6 design options is the same as the default.  This is to fix the bad 3.6 upgrade bug
 **/
add_action('blox_do_upgrade_361', 'blox_do_upgrade_361');
function blox_do_upgrade_361() {

	global $wpdb;

	$existing_design_settings = get_option('blox_option_group_design', array());

	if ( BloxOption::$current_skin == BLOX_DEFAULT_SKIN && $existing_design_settings == BloxElementsData::get_default_data() ) {

		$combined_design_settings = array();

		foreach ( $wpdb->get_results("SELECT * FROM $wpdb->options") as $option ) {

			if ( strpos($option->option_name, 'blox_option_group_design-editor-group') !== 0 )
				continue;

			$combined_design_settings = array_merge($combined_design_settings, maybe_unserialize($option->option_value));

		}

		$existing_design_settings['properties'] = $combined_design_settings;

		update_option('blox_option_group_design', $existing_design_settings);

	}

	BloxMaintenance::output_status('Successfully Upgraded Design Editor Data');

}