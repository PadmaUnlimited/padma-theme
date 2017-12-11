<?php
blox_register_block('BloxCustomCodeBlock', blox_url() . '/library/blocks/custom-code');

class BloxCustomCodeBlock extends BloxBlockAPI {
	
	
	public $id = 'custom-code';
	
	public $name = 'Custom Code';
		
	public $options_class = 'BloxCustomCodeBlockOptions';

	public $description = 'Place in custom HTML, PHP, or even WordPress shortcodes into this block.';
	
	
	function content($block) {
		
		$content = parent::get_setting($block, 'content');	
			
		if ( $content != null )
			echo blox_parse_php(do_shortcode(stripslashes($content)));
		else
			echo '<p>There is no custom code to display.</p>';
		
	}
	
	
}


class BloxCustomCodeBlockOptions extends BloxBlockOptionsAPI {
	
	public $tabs = array(
		'content' => 'Content'
	);

	public $inputs = array(
		'content' => array(
			'content' => array(
				'type' => 'code',
				'mode' => 'html',
				'name' => 'content',
				'label' => 'Content',
				'default' => null
			)
		)
	);
	
}