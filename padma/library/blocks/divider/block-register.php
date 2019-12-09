<?php

$class_file = __DIR__ . '/divider.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/divider'
);
padma_register_block('PadmaDividerBlock', padma_url() . '/library/blocks/divider', $class_file, $icons);