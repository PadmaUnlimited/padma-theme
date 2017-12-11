<?php
blox_register_block('BloxTextBlock', blox_url() . '/library/blocks/text');

class BloxTextBlock extends BloxBlockAPI {
	
	
	public $id = 'text';
	
	public $name = 'Text';
		
	public $options_class = 'BloxTextBlockOptions';

	public $description = 'Use the built-in rich text editor to insert titles, text, and more!';
	
	
	function content($block) {
		
		$content = parent::get_setting($block, 'content');	
			
		echo '<div class="entry-content">';
			if ( $content != null )
				echo do_shortcode(stripslashes($content));
			else
				echo '<p>There is no content to display.</p>';
		echo '</div>';
		
	}


	function setup_elements() {
		
		$this->register_block_element(array(
			'id' => 'text',
			'name' => 'Text',
			'selector' => '.entry-content',
			'properties' => array('fonts', 'padding'),
			'inspectable' => false
		));

		$this->register_block_element(array(
			'id' => 'strong',
			'parent' => 'text',
			'name' => 'Bold text',
			'description' => '&lt;strong&gt;',
			'selector' => 'div.entry-content strong'
		));

		$this->register_block_element(array(
			'id' => 'emphasized',
			'parent' => 'text',
			'name' => 'Italic text',
			'selector' => 'div.entry-content em'
		));

		$this->register_block_element(array(
			'id' => 'paragraphs',
			'name' => 'Paragraphs',
			'selector' => '.entry-content p'
		));

		$this->register_block_element(array(
			'id' => 'paragraphs-first',
			'parent' => 'paragraphs',
			'name' => 'First Paragraphs',
			'selector' => '.entry-content p:first-of-type',
			'inspectable' => false
		));

		$this->register_block_element(array(
			'id' => 'paragraphs-last',
			'parent' => 'paragraphs',
			'name' => 'Last Paragraphs',
			'selector' => '.entry-content p:last-of-type',
			'inspectable' => false
		));

		$this->register_block_element(array(
			'id' => 'hyperlinks',
			'name' => 'Hyperlinks',
			'selector' => '.entry-content a',
			'properties' => array('fonts'),
			'states' => array(
				'Hover' => '.entry-content a:hover', 
				'Clicked' => '.entry-content a:active'
			)
		));
		
		$this->register_block_element(array(
			'id' => 'heading',
			'name' => 'Headings',
			'description' => '&lt;H3&gt;, &lt;H2&gt;, &lt;H1&gt;',
			'selector' => '.entry-content h3, div.entry-content h2, div.entry-content h1'
		));

		$this->register_block_element(array(
			'id' => 'heading-h1',
			'parent' => 'heading',
			'name' => 'Heading 1',
			'description' => '&lt;H1&gt;',
			'selector' => 'div.entry-content h1'
		));

		$this->register_block_element(array(
			'id' => 'heading-h2',
			'parent' => 'heading',
			'name' => 'Heading 2',
			'description' => '&lt;H2&gt;',
			'selector' => 'div.entry-content h2'
		));

		$this->register_block_element(array(
			'id' => 'heading-h3',
			'parent' => 'heading',
			'name' => 'Heading 3',
			'description' => '&lt;H3&gt;',
			'selector' => 'div.entry-content h3'
		));

		$this->register_block_element(array(
			'id' => 'sub-heading',
			'name' => 'Sub Heading',
			'description' => '&lt;H4&gt;',
			'selector' => '.entry-content h4'
		));

		$this->register_block_element(array(
			'id' => 'image',
			'name' => 'Images',
			'selector' => 'div.entry-content img'
		));

		$this->register_block_element(array(
			'id' => 'form',
			'name' => 'Forms',
			'selector' => 'div.entry-content form'
		));

		$this->register_block_element(array(
			'id' => 'buttons',
			'name' => 'Button',
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
			'name' => 'Inputs',
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


class BloxTextBlockOptions extends BloxBlockOptionsAPI {
	
	public $tabs = array(
		'content' => 'Content'
	);

	public $inputs = array(
		'content' => array(
			'content' => array(
				'type' => 'wysiwyg',
				'name' => 'content',
				'label' => 'Content',
				'default' => null
			)
		)
	);
	
}