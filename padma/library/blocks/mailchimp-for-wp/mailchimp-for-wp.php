<?php

class PadmaMailchimpForWPBlock extends PadmaBlockAPI {

	public $id;
	public $name;
	public $options_class;
	public $description;
	public $categories;


	function __construct(){

		$this->id = 'mailchimp-for-wp';
		$this->name	= __('Mailchimp for WordPress','padma');
		$this->options_class = 'PadmaMailchimpForWPBlockOptions';
		$this->description = __('Display Mailchimp for WordPress','padma');
		$this->categories = array('core','forms');

	}


	public function init() {

		if(!class_exists('mc4wp'))
			return false;

	}

	function setup_elements() {

		$this->register_block_element(array(			
			'id' => 'mc4wp-form',			
			'name' => __('Form','padma'),
			'description' => __('Form','padma'),
			'selector' => '.mc4wp-form'
		));

		$this->register_block_element(array(			
			'id' => 'mc4wp-form',			
			'name' => __('Form container','padma'),
			'description' => __('Form container','padma'),
			'selector' => '.mc4wp-form .mc4wp-form-fields'
		));

		$this->register_block_element(array(
			'id' => 'mc4wp-form-p',			
			'name' => __('Paragraph','padma'),
			'description' => __('Paragraph','padma'),
			'selector' => '.mc4wp-form .mc4wp-form-fields p'
		));

		$this->register_block_element(array(
			'id' => 'mc4wp-form-label',			
			'name' => __('Label','padma'),
			'description' => __('Label','padma'),
			'selector' => '.mc4wp-form .mc4wp-form-fields label'
		));

		$this->register_block_element(array(
			'id' => 'mc4wp-form-input',			
			'name' => 'Input',
			'description' => 'Input',
			'selector' => '.mc4wp-form .mc4wp-form-fields input'
		));

		$this->register_block_element(array(
			'id' => 'form-submit',
			'name' => __('Form submit','padma'),
			'selector' => '.mc4wp-form .mc4wp-form-fields input[type="submit"]',
		));

	}


	public static function dynamic_css($block_id, $block = false) {

	}


	public static function dynamic_js($block_id, $block = false) {

	}

	public function content($block) {

		$form_id = parent::get_setting($block, 'form-id', '');		
		echo do_shortcode('[mc4wp_form id="'.$form_id.'" title="'.$this->get_form_title($form_id).'"]');
	}

	public static function enqueue_action($block_id, $block = false) {

	}


	function get_form_title($form_id){

		$args = array('post_type' => 'mc4wp-form', 'posts_per_page' => -1, );

		return get_post($form_id, OBJECT, 'raw')->post_title;
	}

}


class PadmaMailchimpForWPBlockOptions extends PadmaBlockOptionsAPI {


	public $tabs;
	public $sets;
	public $inputs;

	function __construct($block_type_object){

		parent::__construct($block_type_object);

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
					'label' => 'Select form',
					'options' => 'get_forms()',
					'tooltip' => '',
				),
			)
		);

	}

	public function modify_arguments($args = false) {
	}


	function get_forms() {

		$args = array('post_type' => 'mc4wp-form', 'posts_per_page' => -1);
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