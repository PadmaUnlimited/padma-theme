<?php
add_action('blox_register_elements', 'blox_register_structural_elements');
function blox_register_structural_elements() {
	
	//Structure
	BloxElementAPI::register_group('structure', array(
		'name' => 'Structure'
	));

		BloxElementAPI::register_element( array(
			'group'            => 'structure',
			'id'               => 'html',
			'name'             => 'HTML Document',
			'selector'         => 'html',
			'disallow-nudging' => true
		) );

		BloxElementAPI::register_element(array(
			'group' => 'structure',
			'id' => 'body',
			'name' => 'Body',
			'selector' => 'body',
			'properties' => array('background', 'borders', 'padding'),
			'disallow-nudging' => true
		));

		BloxElementAPI::register_element(array(
			'group' => 'structure',
			'id' => 'wrapper',
			'name' => 'Wrapper',
			'selector' => 'div.wrapper',
			'properties' => array('fonts', 'background', 'borders', 'padding', 'corners', 'box-shadow')
		));

	//Blocks
	BloxElementAPI::register_group('blocks', array(
		'name' => 'Blocks',
		'description' => 'Individual block types and block elements'
	));
	
}