<?php

class PadmaContactForm7Block extends PadmaBlockAPI {
	
	public $id;
	public $name;
	public $options_class;
	public $description;
	public $categories;
	

	function __construct(){

		$this->id = 'contact-form-7';	
		$this->name = 'Contact Form 7';		
		$this->options_class = 'PadmaContactForm7BlockOptions';
		$this->description = __('Display Contact Form 7','padma');
		$this->categories = array('core','forms');

	}

	public function init() {

		if(!class_exists('WPCF7'))
			return false;

	}
	
	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'wpcf7',
			'name' => __('Form Container','padma'),
			'selector' => '.wpcf7',
		));

		$this->register_block_element(array(
			'id' => 'form-paragraph',
			'name' => __('Form paragraph','padma'),
			'selector' => '.wpcf7 form p',
		));

		$this->register_block_element(array(
			'id' => 'form-h1',
			'name' => __('Form H1','padma'),
			'selector' => '.wpcf7 form h1',
		));

		$this->register_block_element(array(
			'id' => 'form-h2',
			'name' => __('Form H2','padma'),
			'selector' => '.wpcf7 form h2',
		));

		$this->register_block_element(array(
			'id' => 'form-h3',
			'name' => __('Form H3','padma'),
			'selector' => '.wpcf7 form h3',
		));

		$this->register_block_element(array(
			'id' => 'form-h4',
			'name' => __('Form H4','padma'),
			'selector' => '.wpcf7 form h4',
		));

		$this->register_block_element(array(
			'id' => 'form-h5',
			'name' => __('Form H5','padma'),
			'selector' => '.wpcf7 form h5',
		));

		$this->register_block_element(array(
			'id' => 'form-h6',
			'name' => __('Form H6','padma'),
			'selector' => '.wpcf7 form h6',
		));

		$this->register_block_element(array(
			'id' => 'form-new-line',
			'name' => __('Form new line','padma'),
			'selector' => '.wpcf7 form br',
		));

		$this->register_block_element(array(
			'id' => 'form-label',
			'name' => __('Form label','padma'),
			'selector' => '.wpcf7 form label',
		));

		$this->register_block_element(array(
			'id' => 'form-span',
			'name' => __('Form span','padma'),
			'selector' => '.wpcf7 form span',
		));

		$this->register_block_element(array(
			'id' => 'form-input',
			'name' => __('Form input','padma'),
			'selector' => '.wpcf7 form input',
		));

		$this->register_block_element(array(
			'id' => 'form-select',
			'name' => __('Form select','padma'),
			'selector' => '.wpcf7 form select',
		));

		$this->register_block_element(array(
			'id' => 'form-textarea',
			'name' => __('Form textarea','padma'),
			'selector' => '.wpcf7 form textarea',
		));

		$this->register_block_element(array(
			'id' => 'form-submit',
			'name' => __('Form submit','padma'),
			'selector' => '.wpcf7 form input[type="submit"]',
		));
	}


	public static function dynamic_css($block_id, $block = false) {
			
	}


	public static function dynamic_js($block_id, $block = false) {
	
	}
	
	public function content($block) {

		$form_id = parent::get_setting($block, 'form-id', '');		
		echo do_shortcode('[contact-form-7 id="'.$form_id.'" title="'.$this->get_form_title($form_id).'"]');
	}

	public static function enqueue_action($block_id, $block = false) {
	
	}


	function get_form_title($form_id){

		$args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1, );

		return get_post($form_id, OBJECT, 'raw')->post_title;
	}
	
}


class PadmaContactForm7BlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs;	
	public $sets;
	public $inputs;

	function __construct(){

		$this->tabs = array(
			'general' => 'General'
		);
		
		$this->sets = array(
			
		);

		$this->inputs = array(
			'general' => array(
				'form-id' => array(
					'type' => 'select',
					'name' => 'form-id',
					'label' => __('Select form','padma'),
					'options' => 'get_forms()',
					'tooltip' => '',
				),
			)
		);
	}

	public function modify_arguments($args = false) {
	}


	function get_forms() {

		$args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
		$forms = array(
			'0' => __('Select a form','padma')
		);
		
		if( $data = get_posts($args)){
			
			foreach($data as $key){
				$forms[$key->ID] = $key->post_title;
			}

		}
		
		return $forms;
	}

	
}