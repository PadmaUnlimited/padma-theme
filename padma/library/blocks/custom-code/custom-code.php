<?php

padma_register_block('PadmaCustomCodeBlock', padma_url() . '/library/blocks/custom-code');

class PadmaCustomCodeBlock extends PadmaBlockAPI {
	
	
	public $id 				= 'custom-code';	
	public $name 			= 'Custom Code';		
	public $options_class 	= 'PadmaCustomCodeBlockOptions';
	public $description 	= 'Place in custom HTML, PHP, or even WordPress shortcodes into this block.';
	public $categories 		= array('core','code');
	
	
	function content($block) {
		
		$content = parent::get_setting($block, 'content');

		if ( $content != null )
			
			echo '<div class="custom-code-content">'.padma_parse_php(do_shortcode(stripslashes($content))).'</div>';			
		
		else
			echo '<p>There is no custom code to display.</p>';
		
	}


	public function setup_elements() {
		$this->register_block_element(array(
			'id' => 'content',			
			'name' => 'Content',
			'selector' => '.custom-code-content p',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h1',
			'name' => 'Content H1',
			'selector' => '.custom-code-content h1',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h2',
			'name' => 'Content H2',
			'selector' => '.custom-code-content h2',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h3',
			'name' => 'Content H3',
			'selector' => '.custom-code-content h3',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h4',
			'name' => 'Content H4',
			'selector' => '.custom-code-content h4',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h5',
			'name' => 'Content H5',
			'selector' => '.custom-code-content h5',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h6',
			'name' => 'Content H6',
			'selector' => '.custom-code-content h6',
		));
		
		$this->register_block_element(array(
			'id' => 'content-p',
			'name' => 'Content p',
			'selector' => '.custom-code-content span',
		));
		
		$this->register_block_element(array(
			'id' => 'content-a',
			'name' => 'Content a',
			'selector' => '.custom-code-content a',
		));

		$this->register_block_element(array(
			'id' => 'content-ul',
			'name' => 'Content ul',
			'selector' => '.custom-code-content ul',
		));

		$this->register_block_element(array(
			'id' => 'content-ul-li',
			'name' => 'Content ul li',
			'selector' => '.custom-code-content ul li',
		));
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
				'default' 	=> null,
				'tooltip' => 'Write your custom code here. To enable PHP Execution please add define(\'PADMA_DISABLE_PHP_PARSING\', false); to your wp-config.php'
			),
		),
	);


	public function modify_arguments( $args = false ) {

		if ( defined('PADMA_DISABLE_PHP_PARSING') && PADMA_DISABLE_PHP_PARSING === true ){
			
			$this->tab_notices['content'] = 'PHP Parsing is currently disable, to enable PHP Execution please add: <br><pre>define(\'PADMA_DISABLE_PHP_PARSING\', false);</pre><br> to your wp-config.php';

		}
		

	}
	
}