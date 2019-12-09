<?php

$class_file = __DIR__ . '/slider.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/slider'
);
padma_register_block('PadmaSliderBlock', padma_url() . '/library/blocks/slider', $class_file, $icons);