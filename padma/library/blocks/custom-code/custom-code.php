<?php

padma_register_block('PadmaCustomCodeBlock', padma_url() . '/library/blocks/custom-code');

class PadmaCustomCodeBlock extends PadmaBlockAPI {
	
	
	public $id 				= 'custom-code';	
	public $name 			= 'Custom Code';		
	public $options_class 	= 'PadmaCustomCodeBlockOptions';
	public $description 	= 'Place in custom HTML, PHP, or even WordPress shortcodes into this block.';
	
	
	function content($block) {
		
		$content = parent::get_setting($block, 'content');	
			
		if ( $content != null )
			echo padma_parse_php(do_shortcode(stripslashes($content)));
		else
			echo '<p>There is no custom code to display.</p>';
		
	}
	
	
}


class PadmaCustomCodeBlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs = array(
		'content' => 'Content'
	);

	public $inputs = array(
		'content' => array(
			'content' => array(
				'type' 		=> 'code',
				'mode' 		=> 'html',
				'name' 		=> 'content',
				'label' 	=> 'Content',
				'default' 	=> null
			)
		)
	);
	
}