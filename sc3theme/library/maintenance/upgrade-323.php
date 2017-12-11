<?php
/**
 * Pre-3.2.3
 *
 * Change the old wrapper-horizontal-padding and wrapper-vertical-padding to design editor values
 **/
add_action( 'blox_do_upgrade_323', 'blox_do_upgrade_323' );
function blox_do_upgrade_323() {

	require_once BLOX_LIBRARY_DIR . '/maintenance/legacy-classes.php';

	$horizontal_padding = BloxOption::get( 'wrapper-horizontal-padding', 'general', 15 );
	$vertical_padding = BloxOption::get( 'wrapper-vertical-padding', 'general', 15 );

	BloxElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'padding-top', $vertical_padding );
	BloxElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'padding-bottom', $vertical_padding );

	BloxElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'padding-left', $horizontal_padding );
	BloxElementsData_Upgrade34::set_property( 'structure', 'wrapper', 'padding-right', $horizontal_padding );

}