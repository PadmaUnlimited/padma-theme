<?php

//Check that Gravity Forms is even enabled
if ( class_exists('RGForms')){
	$class_file = __DIR__ . '/gravity-forms.php';
	padma_register_block('PadmaGravityFormsBlock', padma_url() . '/library/blocks/gravity-forms', $class_file);	
}