<?php
/**
 * Padma Unlimited Theme.
 *
 * @package padma
 */

add_action( 'padma_register_elements', 'padma_register_structural_elements' );

/**
 * Register structural elements in the Visual Editor
 *
 * @return void
 */
function padma_register_structural_elements() {

	// Structure.
	PadmaElementAPI::register_group(
		'structure',
		array(
			'name' => __( 'Structure', 'padma' ),
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'            => 'structure',
			'id'               => 'html',
			'name'             => __( 'HTML Document', 'padma' ),
			'selector'         => 'html',
			'disallow-nudging' => true,
			'properties'       => array( 'fonts', 'background', 'borders', 'padding', 'corners', 'box-shadow', 'sizes', 'advanced', 'transition', 'outlines', 'animation', 'scroll' ),
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'            => 'structure',
			'id'               => 'body',
			'name'             => __( 'Body', 'padma' ),
			'selector'         => 'body',
			'properties'       => array( 'background', 'borders', 'padding', 'fonts' ),
			'disallow-nudging' => true,
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'      => 'structure',
			'id'         => 'wrapper',
			'name'       => __( 'Wrapper', 'padma' ),
			'selector'   => 'div.wrapper',
			'properties' => array( 'fonts', 'background', 'borders', 'padding', 'corners', 'box-shadow', 'sizes', 'advanced', 'transition', 'outlines', 'animation' ),
			'states'     => array(
				'Shrinked' => 'div.wrapper.is_shrinked',
				'Stuck'    => 'div.wrapper.is_stuck',
			),
		)
	);
	PadmaElementAPI::register_element(
		array(
			'group'      => 'structure',
			'id'         => 'wrapper-container',
			'name'       => __( 'Grid Container', 'padma' ),
			'selector'   => 'div.wrapper.css-grid .grid-container',
			'properties' => array( 'grid' ),
		)
	);

	PadmaElementAPI::register_group(
		'blocks',
		array(
			'name'        => 'Blocks',
			'description' => __( 'Individual block types and block elements', 'padma' ),
		)
	);

}