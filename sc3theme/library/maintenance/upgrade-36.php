<?php
/**
 * Pre-3.6
 *
 * - Merge all design settings into the one option
 **/
add_action('blox_do_upgrade_36', 'blox_do_upgrade_36');
function blox_do_upgrade_36() {

	global $wpdb;

	$combined_design_settings = array();
	$existing_design_settings = get_option('blox_option_group_design', array());

	foreach ( $wpdb->get_results("SELECT * FROM $wpdb->options") as $option ) {

		if ( strpos($option->option_name, 'blox_option_group_design-editor-group') !== 0 )
			continue;

		$combined_design_settings = array_merge($combined_design_settings, maybe_unserialize($option->option_value));

	}

	$existing_design_settings['properties'] = $combined_design_settings;

	update_option('blox_option_group_design', $existing_design_settings);

	BloxMaintenance::output_status('Successfully Upgraded Design Editor Data');

	return true;

}