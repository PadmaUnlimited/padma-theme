<?php
padma_register_block('PadmaContactForm7Block', padma_url() . '/library/blocks/contact-form-7');

class PadmaContactForm7Block extends PadmaBlockAPI {
	
	public $id 				= 'contact-form-7';	
	public $name 			= 'Contact Form 7';		
	public $options_class 	= 'PadmaContactForm7BlockOptions';
	public $description 	= 'Display Contact Form 7';
	public $categories 		= array('core','forms');	
	
	public function init() {

		if(!class_exists('WPCF7'))
			return false;

	}
	
	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'wpcf7',
			'name' => 'Form Container',
			'selector' => '.wpcf7',
		));

		$this->register_block_element(array(
			'id' => 'form-paragraph',
			'name' => 'Form paragraph',
			'selector' => '.wpcf7 form p',
		));

		$this->register_block_element(array(
			'id' => 'form-h1',
			'name' => 'Form H1',
			'selector' => '.wpcf7 form h1',
		));

		$this->register_block_element(array(
			'id' => 'form-h2',
			'name' => 'Form H2',
			'selector' => '.wpcf7 form h2',
		));

		$this->register_block_element(array(
			'id' => 'form-h3',
			'name' => 'Form H3',
			'selector' => '.wpcf7 form h3',
		));

		$this->register_block_element(array(
			'id' => 'form-h4',
			'name' => 'Form H4',
			'selector' => '.wpcf7 form h4',
		));

		$this->register_block_element(array(
			'id' => 'form-h5',
			'name' => 'Form H5',
			'selector' => '.wpcf7 form h5',
		));

		$this->register_block_element(array(
			'id' => 'form-h6',
			'name' => 'Form H6',
			'selector' => '.wpcf7 form h6',
		));

		$this->register_block_element(array(
			'id' => 'form-new-line',
			'name' => 'Form new line',
			'selector' => '.wpcf7 form br',
		));

		$this->register_block_element(array(
			'id' => 'form-label',
			'name' => 'Form label',
			'selector' => '.wpcf7 form label',
		));

		$this->register_block_element(array(
			'id' => 'form-span',
			'name' => 'Form span',
			'selector' => '.wpcf7 form span',
		));

		$this->register_block_element(array(
			'id' => 'form-input',
			'name' => 'Form input',
			'selector' => '.wpcf7 form input',
		));

		$this->register_block_element(array(
			'id' => 'form-select',
			'name' => 'Form select',
			'selector' => '.wpcf7 form select',
		));

		$this->register_block_element(array(
			'id' => 'form-textarea',
			'name' => 'Form textarea',
			'selector' => '.wpcf7 form textarea',
		));

		$this->register_block_element(array(
			'id' => 'form-submit',
			'name' => 'Form submit',
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
	
	public $tabs = array(
		'general' => 'General'
	);
	
	public $sets = array(
		
	);

	public $inputs = array(
		'general' => array(
			'form-id' => array(
				'type' => 'select',
				'name' => 'form-id',
				'label' => 'Select form',
				'options' => 'get_forms()',
				'tooltip' => '',
			),
		)
	);

	public function modify_arguments($args = false) {
	}


	function get_forms() {

		$args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
		$forms = array(
			'0' => 'Select a form'
		);
		
		if( $data = get_posts($args)){
			
			foreach($data as $key){
				$forms[$key->ID] = $key->post_title;
			}

		}
		
		return $forms;
	}

	
}