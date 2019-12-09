<?php

$class_file = __DIR__ . '/custom-code.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/custom-code'
);
padma_register_block('PadmaCustomCodeBlock', padma_url() . '/library/blocks/custom-code', $class_file, $icons);