<?php

class PadmaGravityFormsBlock extends PadmaBlockAPI {
	
	
	public $id = 'gravity-forms';	
	public $name = 'Gravity Forms';	
	public $options_class = 'PadmaGravityFormsBlockOptions';
	public $categories 	= array('core','forms');			

	public static function enqueue_action($block_id) {
								
		$block = PadmaBlocksData::get_block($block_id);
		
		return gravity_form_enqueue_scripts(parent::get_setting($block, 'form-id', null), parent::get_setting($block, 'use-ajax', false));
		
	}
	

	function content($block) {
		
		$form_id = parent::get_setting($block, 'form-id', null);
			
		//If no form ID is present, display the message and stop this function.
		if ( !$form_id ) {
			
			echo '<p>There is no form to display.</p>';
						
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
	
	
	public $tabs = array(
		'form-setup' => 'Form Setup'
	);

	public $inputs = array(
		'form-setup' => array(
			
			'form-id' => array(
				'type' => 'select',
				'name' => 'form-id',
				'label' => 'Form To Display',
				'default' => '',
				'tooltip' => 'Select which form you would like this block to display.',
				'options' => 'get_forms()'
			),
			
			'display-title' => array(
				'type' => 'checkbox',
				'name' => 'display-title',
				'label' => 'Display Form Title',
				'default' => true
			),
			
			'display-description' => array(
				'type' => 'checkbox',
				'name' => 'display-description',
				'label' => 'Display Form Description',
				'default' => true
			),
			
			'use-ajax' => array(
				'type' => 'checkbox',
				'name' => 'use-ajax',
				'label' => 'Use AJAX',
				'default' => false,
				'tooltip' => 'AJAX is a technology that will allow faster submission on your forms.'
			),
			
		)
	);
	
	
	function get_forms() {
		
		$forms = RGFormsModel::get_forms();
		
		$options = array('' => '&ndash; Select a Form &ndash;');
		
		foreach ( $forms as $form ) {
			
			$options[$form->id] = $form->title;
			
		}
		
		return $options;
		
	}
	
	
}