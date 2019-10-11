<?php

class PadmaTextBlock extends PadmaBlockAPI {


	public $id;
	public $name;
	public $options_class;
	public $description;
	public $categories;
	public $inline_editable;


	function __construct(){

		$this->id = 'text';
		$this->name = __('Text','padma');
		$this->options_class = 'PadmaTextBlockOptions';
		$this->description = __('Use the built-in rich text editor to insert titles, text, and more!','padma');
		$this->categories 	= array('core','content');		
		$this->inline_editable = array('block-title', 'block-subtitle', 'content');

	}


	function content($block) {

		$content = parent::get_setting($block, 'content');	

		echo '<div class="entry-content content">';
			if ( $content != null )
				echo do_shortcode(stripslashes($content));
			else
				echo '<p class="content">' . __('There is no content to display.','padma') . '</p>';
		echo '</div>';

	}


	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'text',
			'name' => __('Text','padma'),
			'selector' => '.entry-content',
			'properties' => array('fonts', 'padding'),
			'inspectable' => false
		));

		$this->register_block_element(array(
			'id' => 'strong',
			'parent' => 'text',
			'name' => __('Bold text','padma'),
			'description' => '&lt;strong&gt;',
			'selector' => 'div.entry-content strong'
		));

		$this->register_block_element(array(
			'id' => 'emphasized',
			'parent' => 'text',
			'name' => __('Italic text','padma'),
			'selector' => 'div.entry-content em'
		));

		$this->register_block_element(array(
			'id' => 'paragraphs',
			'name' => __('Paragraphs','padma'),
			'selector' => '.entry-content p'
		));

		$this->register_block_element(array(
			'id' => 'paragraphs-first',
			'parent' => 'paragraphs',
			'name' => __('First Paragraphs','padma'),
			'selector' => '.entry-content p:first-of-type',
			'inspectable' => false
		));

		$this->register_block_element(array(
			'id' => 'paragraphs-last',
			'parent' => 'paragraphs',
			'name' => __('Last Paragraphs','padma'),
			'selector' => '.entry-content p:last-of-type',
			'inspectable' => false
		));

		$this->register_block_element(array(
			'id' => 'hyperlinks',
			'name' => __('Hyperlinks','padma'),
			'selector' => '.entry-content a',
			'properties' => array('fonts'),
			'states' => array(
				'Hover' => '.entry-content a:hover', 
				'Clicked' => '.entry-content a:active'
			)
		));

		$this->register_block_element(array(
			'id' => 'heading',
			'name' => __('Headings','padma'),
			'description' => '&lt;H3&gt;, &lt;H2&gt;, &lt;H1&gt;',
			'selector' => '.entry-content h3, div.entry-content h2, div.entry-content h1'
		));

		$this->register_block_element(array(
			'id' => 'heading-h1',
			'parent' => 'heading',
			'name' => __('Heading 1','padma'),
			'description' => '&lt;H1&gt;',
			'selector' => 'div.entry-content h1'
		));

		$this->register_block_element(array(
			'id' => 'heading-h2',
			'parent' => 'heading',
			'name' => __('Heading 2','padma'),
			'description' => '&lt;H2&gt;',
			'selector' => 'div.entry-content h2'
		));

		$this->register_block_element(array(
			'id' => 'heading-h3',
			'parent' => 'heading',
			'name' => __('Heading 3','padma'),
			'description' => '&lt;H3&gt;',
			'selector' => 'div.entry-content h3'
		));

		$this->register_block_element(array(
			'id' => 'sub-heading',
			'name' => __('Sub Heading','padma'),
			'description' => '&lt;H4&gt;',
			'selector' => '.entry-content h4'
		));

		$this->register_block_element(array(
			'id' => 'image',
			'name' => __('Images','padma'),
			'selector' => 'div.entry-content img'
		));

		$this->register_block_element(array(
			'id' => 'form',
			'name' => __('Forms','padma'),
			'selector' => 'div.entry-content form'
		));

		$this->register_block_element(array(
			'id' => 'buttons',
			'name' => __('Button','padma'),
			'parent' => 'form',
			'selector' => '
				.entry-content input[type="submit"],
				.entry-content input[type="button"],
				.entry-content button,
				.entry-content .button',
			'states' => array(
				'Hover' => '
					.entry-content input[type="submit"]:hover,
					.entry-content input[type="button"]:hover,
					.entry-content button:hover,
					.entry-content .button:hover',
				'Active' => '
					.entry-content input[type="submit"]:active,
					.entry-content input[type="button"]:active,
					.entry-content button:active,
					.entry-content .button:active',
			)
		));

		$this->register_block_element(array(
			'id' => 'inputs',
			'name' => __('Inputs','padma'),
			'parent' => 'form',
			'selector' => '
				.entry-content input[type="text"],
				.entry-content input[type="password"],
				.entry-content input[type="email"],
				.entry-content textarea,
				.entry-content select',
			'states' => array(
				'Focus' => '
					.entry-content input[type="text"]:focus,
					.entry-content input[type="password"]:focus,
					.entry-content input[type="email"]:focus,
					.entry-content textarea:focus'
			)
		));


	}


}


class PadmaTextBlockOptions extends PadmaBlockOptionsAPI {

	public $tabs;
	public $inputs;

	function __construct(){

		$this->tabs = array(
			'content' => __('Content','padma')
		);

		$this->inputs = array(
			'content' => array(
				'content' => array(
					'type' => 'wysiwyg',
					'name' => 'content',
					'label' => __('Content','padma'),
					'default' => null
				)
			)
		);
	}

}