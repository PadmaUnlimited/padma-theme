<?php

namespace Padma;
//Check that Gravity Forms is even enabled
if ( class_exists('RGForms')){
	$class_file = __DIR__ . '/gravity-forms.php';
	$icons = array(
		'path' => __DIR__ . '/',
		'url' => padma_url() . '/library/blocks/gravity-forms'
	);
	padma_register_block('PadmaGravityFormsBlock', padma_url() . '/library/blocks/gravity-forms', $class_file, $icons);	
}