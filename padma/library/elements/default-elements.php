<?php
add_action('padma_register_elements', 'padma_register_default_elements');
function padma_register_default_elements() {

	PadmaElementAPI::register_group('default-elements', __('Global Styling','padma') );

	PadmaElementAPI::register_element(array(
		'id' => 'default-text',
		'name' => __('Text','padma'),
		'description' => __('&lt;body&gt;','padma'),
		'properties' => array('fonts'),
		'default-element' => true,
		'selector' => 'body'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-paragraph',
		'name' => __('Paragraph','padma'),
		'description' => __('All &lt;p&gt; elements','padma'),
		'properties' => array('margins'),
		'default-element' => true,
		'selector' => 'body p'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-hyperlink',
		'name' => __('Hyperlink','padma'),
		'default-element' => true,
		'selector' => 'a'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-headings',
		'name' => __('Headings','padma'),
		'description' => '&lt;H3&gt;, &lt;H2&gt;, &lt;H1&gt;',
		'default-element' => true,
		'selector' => 'h1, h2, h3'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-heading-h1',
		'name' => __('Heading 1','padma'),
		'description' => '&lt;H1&gt;',
		'default-element' => true,
		'selector' => 'h1',
		'parent' => 'default-headings'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-heading-h2',
		'name' => __('Heading 2','padma'),
		'description' => '&lt;H2&gt;',
		'default-element' => true,
		'selector' => 'h2',
		'parent' => 'default-headings'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-heading-h3',
		'name' => __('Heading 3','padma'),
		'description' => '&lt;H3&gt;',
		'default-element' => true,
		'selector' => 'h3',
		'parent' => 'default-headings'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-sub-headings',
		'name' => __('Sub Headings','padma'),
		'description' => '&lt;H4&gt;, &lt;H5&gt;, &lt;H6&gt;',
		'default-element' => true,
		'selector' => 'h4, h5, h6'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-sub-heading-h4',
		'parent' => 'default-sub-headings',
		'name' => __('Heading 4','padma'),
		'description' => '&lt;H4&gt;',
		'default-element' => true,
		'selector' => 'h4'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-sub-heading-h5',
		'parent' => 'default-sub-headings',
		'name' => __('Heading 5','padma'),
		'description' => '&lt;H5&gt;',
		'default-element' => true,
		'selector' => 'h5'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-sub-heading-h6',
		'parent' => 'default-sub-headings',
		'name' => __('Heading 6','padma'),
		'description' => '&lt;H6&gt;',
		'default-element' => true,
		'selector' => 'h6'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-form',
		'name' => __('Form','padma'),
		'description' => '&lt;form&gt;',
		'default-element' => true,
		'selector' => 'form'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-form-label',
		'name' => __('Label','padma'),
		'description' => __('Form Label','padma'),
		'default-element' => true,
		'selector' => 'form label',
		'parent' => 'default-form'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-form-input',
		'name' => __('Input','padma'),
		'description' => __('Inputs & Textareas','padma'),
		'default-element' => true,
		'selector' => 'input[type="text"], input[type="password"], input[type="email"], input[type="tel"], input[type="number"], input[type="month"], input[type="time"], input[type="url"], input[type="week"], textarea, select',
		'states' => array(
			'Focus' => 'input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus, input[type="tel"]:focus, input[type="number"]:focus, input[type="month"]:focus, input[type="time"]:focus, input[type="url"]:focus, input[type="week"]:focus, textarea:focus'
		),
		'parent' => 'default-form'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-form-button',
		'name' => __('Button','padma'),
		'description' => __('Buttons & Submit Inputs','padma'),
		'default-element' => true,
		'selector' => 'input[type="submit"], input[type="button"], button, .button',
		'states' => array(
			'Hover' => 'input[type="submit"]:hover, input[type="button"]:hover, button:hover',
			'Active' => 'input[type="submit"]:active, input[type="button"]:active, button:active'
		),
		'parent' => 'default-form'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-blockquote',
		'name' => __('Blockquote','padma'),
		'properties' => array('background', 'borders', 'fonts', 'padding', 'corners', 'box-shadow', 'overflow'),
		'default-element' => true,
		'selector' => 'blockquote'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'default-block',
		'name' => __('Block','padma'),
		'properties' => array('background', 'borders', 'fonts', 'padding', 'corners', 'box-shadow', 'overflow'),
		'default-element' => true,
		'selector' => '.block'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'block-title',
		'name' => __('Block Title','padma'),
		'selector' => '.block-title',
		'default-element' => true
	));

	PadmaElementAPI::register_element(array(
		'id' => 'block-title-inner',
		'name' => __('Block Title Inner','padma'),
		'selector' => '.block-title span',
		'default-element' => true,
		'parent' => 'block-title'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'block-title-link',
		'name' => __('Block Title Link','padma'),
		'selector' => '.block-title a',
		'default-element' => true,
		'parent' => 'block-title'
	));

	PadmaElementAPI::register_element(array(
		'id' => 'block-subtitle',
		'name' => __('Block Subtitle','padma'),
		'selector' => '.block-subtitle',
		'default-element' => true
	));

}