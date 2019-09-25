<?php

class PadmaCustomCodeBlock extends PadmaBlockAPI {
	
	
	public $id;
	public $name;
	public $options_class;
	public $description;
	public $categories;
	public $inline_editable;
	
	
	function __construct(){
		
		$this->id 				= 'custom-code';	
		$this->name 			= 'Custom Code';		
		$this->options_class 	= 'PadmaCustomCodeBlockOptions';
		$this->description 		= __('Place in custom HTML, PHP, or even WordPress shortcodes into this block.','padma');
		$this->categories 		= array('core','code');
		$this->inline_editable 	= array('block-title', 'block-subtitle', 'content');

	}


	function content($block) {
		
		$content = parent::get_setting($block, 'content');

		if ( $content != null )
			
			echo '<div class="custom-code-content content">'.padma_parse_php(do_shortcode(stripslashes($content))).'</div>';			
		
		else
			echo '<p class="content">' . __('There is no custom code to display.','padma') .'</p>';
		
	}


	public function setup_elements() {
		$this->register_block_element(array(
			'id' => 'content',			
			'name' => __('Content','padma'),
			'selector' => '.custom-code-content p',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h1',
			'name' => __('Content H1','padma'),
			'selector' => '.custom-code-content h1',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h2',
			'name' => __('Content H2','padma'),
			'selector' => '.custom-code-content h2',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h3',
			'name' => __('Content H3','padma'),
			'selector' => '.custom-code-content h3',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h4',
			'name' => __('Content H4','padma'),
			'selector' => '.custom-code-content h4',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h5',
			'name' => __('Content H5','padma'),
			'selector' => '.custom-code-content h5',
		));
		
		$this->register_block_element(array(
			'id' => 'content-h6',
			'name' => __('Content H6','padma'),
			'selector' => '.custom-code-content h6',
		));
		
		$this->register_block_element(array(
			'id' => 'content-p',
			'name' => __('Content p','padma'),
			'selector' => '.custom-code-content span',
		));
		
		$this->register_block_element(array(
			'id' => 'content-a',
			'name' => __('Content a','padma'),
			'selector' => '.custom-code-content a',
		));

		$this->register_block_element(array(
			'id' => 'content-ul',
			'name' => __('Content ul','padma'),
			'selector' => '.custom-code-content ul',
		));

		$this->register_block_element(array(
			'id' => 'content-ul-li',
			'name' => __('Content ul li','padma'),
			'selector' => '.custom-code-content ul li',
		));
	}

}


class PadmaCustomCodeBlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs;
	public $inputs;

	public function __construct(){
		
		$this->tabs = array(
			'content' => __('Content','padma')
		);

		$this->inputs = array(
			'content' => array(
				'content' => array(
					'type' 		=> 'code',
					'mode' 		=> 'html',
					'name' 		=> __('content','padma'),
					'label' 	=> __('Content','padma'),
					'default' 	=> null,
					'tooltip' => __('Write your custom code here. To enable PHP Execution please add define(\'PADMA_DISABLE_PHP_PARSING\', false); to your wp-config.php','padma')
				),
			),
		);
	}


	public function modify_arguments( $args = false ) {

		if ( defined('PADMA_DISABLE_PHP_PARSING') && PADMA_DISABLE_PHP_PARSING === true ){
			
			$this->tab_notices['content'] = __('PHP Parsing is currently disable, to enable PHP Execution please add: <br><pre>define(\'PADMA_DISABLE_PHP_PARSING\', false);</pre><br> to your wp-config.php','padma');

		}

	}
	
}