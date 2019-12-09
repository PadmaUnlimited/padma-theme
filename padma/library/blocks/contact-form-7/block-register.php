<?php

$class_file = __DIR__ . '/contact-form-7.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/contact-form-7'
);
padma_register_block('PadmaContactForm7Block', padma_url() . '/library/blocks/contact-form-7', $class_file, $icons);