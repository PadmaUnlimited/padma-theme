<?php

namespace Padma;
class PadmaGravityFormsBlock extends PadmaBlockAPI {


	public $id;
	public $name;
	public $options_class;
	public $categories;


	function __construct(){

		$this->id = 'gravity-forms';	
		$this->name = 'Gravity Forms';	
		$this->options_class = 'PadmaGravityFormsBlockOptions';
		$this->categories 	= array('core','forms');		
	}			


	public static function enqueue_action($block_id) {

		$block = PadmaBlocksData::get_block($block_id);

		return gravity_form_enqueue_scripts(parent::get_setting($block, 'form-id', null), parent::get_setting($block, 'use-ajax', false));

	}


	function content($block) {

		$form_id = parent::get_setting($block, 'form-id', null);

		//If no form ID is present, display the message and stop this function.
		if ( !$form_id ) {

			echo __('<p>There is no form to display.</p>','padma');

			return;

		}

		$display_title = parent::get_setting($block, 'display-title', true);
		$display_description = parent::get_setting($block, 'display-description', true);
		$force_display = true;
		$field_values = null;
		$use_ajax = parent::get_setting($block, 'use-ajax', false);

		echo RGForms::get_form($form_id, $display_title, $display_description, $force_display, null, $use_ajax);

	}


}


class PadmaGravityFormsBlockOptions extends PadmaBlockOptionsAPI {


	public $tabs;
	public $inputs;


	function __construct($block_type_object){

		parent::__construct($block_type_object);
		

		$this->tabs = array(
			'form-setup' => __('Form Setup','padma')
		);

		$this->inputs = array(
			'form-setup' => array(

				'form-id' => array(
					'type' => 'select',
					'name' => 'form-id',
					'label' => __('Form To Display','padma'),
					'default' => '',
					'tooltip' => __('Select which form you would like this block to display.','padma'),
					'options' => 'get_forms()'
				),

				'display-title' => array(
					'type' => 'checkbox',
					'name' => 'display-title',
					'label' => __('Display Form Title','padma'),
					'default' => true
				),

				'display-description' => array(
					'type' => 'checkbox',
					'name' => 'display-description',
					'label' => __('Display Form Description','padma'),
					'default' => true
				),

				'use-ajax' => array(
					'type' => 'checkbox',
					'name' => 'use-ajax',
					'label' => __('Use AJAX','padma'),
					'default' => false,
					'tooltip' => __('AJAX is a technology that will allow faster submission on your forms.','padma')
				),

			)
		);
	}


	function get_forms() {

		$forms = RGFormsModel::get_forms();

		$options = array('' => __('&ndash; Select a Form &ndash;','padma') );

		foreach ( $forms as $form ) {

			$options[$form->id] = $form->title;

		}

		return $options;

	}


}