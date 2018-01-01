<?php
/**
 * Pre-3.2.3
 *
 * Change the old wrapper-horizontal-padding and wrapper-vertical-padding to design editor values
 **/
add_action( 'padma_do_upgrade_323', 'padma_do_upgrade_323' );
function padma_do_upgrade_323() {

	require_once PADMA_LIBRARY_DIR . '/maintenance/legacy-classes.php';

	$horizontal_padding = PadmaOption::get( 'wrapper-horizontal-padding', 'general', 15 );
	$vertical_padding = PadmaOption::get( 'wrapper-vertical-padding', 'general', 15 );

	PadmaElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'padding-top', $vertical_padding );
	PadmaElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'padding-bottom', $vertical_padding );

	PadmaElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'padding-left', $horizontal_padding );
	PadmaElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'padding-right', $horizontal_padding );

}