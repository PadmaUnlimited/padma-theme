<?php
add_action('padma_register_elements', 'padma_register_structural_elements');
function padma_register_structural_elements() {
	
	//Structure
	PadmaElementAPI::register_group('structure', array(
		'name' => 'Structure'
	));

		PadmaElementAPI::register_element( array(
			'group'            => 'structure',
			'id'               => 'html',
			'name'             => 'HTML Document',
			'selector'         => 'html',
			'disallow-nudging' => true
		) );

		PadmaElementAPI::register_element(array(
			'group' => 'structure',
			'id' => 'body',
			'name' => 'Body',
			'selector' => 'body',
			'properties' => array('background', 'borders', 'padding'),
			'disallow-nudging' => true
		));

		PadmaElementAPI::register_element(array(
			'group' => 'structure',
			'id' => 'wrapper',
			'name' => 'Wrapper',
			'selector' => 'div.wrapper',
			'properties' => array('fonts', 'background', 'borders', 'padding', 'corners', 'box-shadow', 'sizes', 'advanced', 'transition', 'outlines')
		));

	//Blocks
	PadmaElementAPI::register_group('blocks', array(
		'name' => 'Blocks',
		'description' => 'Individual block types and block elements'
	));
	
}