<?php
class PadmaCompatibilityMailchimpForWP {


	public static function init() {

		if(!class_exists('mc4wp'))
			return;

		PadmaElementAPI::register_element(array(
			'group' => 'form',
			'id' => 'mc4wp-form',			
			'name' => 'Form',
			'description' => 'Form',
			'selector' => '.mc4wp-form'
		));

		PadmaElementAPI::register_element(array(
			'group' => 'form',
			'id' => 'mc4wp-form',			
			'name' => 'Form container',
			'description' => 'Form container',
			'selector' => '.mc4wp-form .mc4wp-form-fields'
		));

		PadmaElementAPI::register_element(array(
			'group' => 'form-p',
			'id' => 'mc4wp-form-p',			
			'name' => 'Paragraph',
			'description' => 'Paragraph',
			'selector' => '.mc4wp-form .mc4wp-form-fields p'
		));

		PadmaElementAPI::register_element(array(
			'group' => 'form-label',
			'id' => 'mc4wp-form-label',			
			'name' => 'Label',
			'description' => 'Label',
			'selector' => '.mc4wp-form .mc4wp-form-fields label'
		));

		PadmaElementAPI::register_element(array(
			'group' => 'form-input',
			'id' => 'mc4wp-form-input',			
			'name' => 'Input',
			'description' => 'Input',
			'selector' => '.mc4wp-form .mc4wp-form-fields input'
		));

	}

}