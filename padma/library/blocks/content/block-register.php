<?php

namespace Padma;

$class_file = __DIR__ . '/content.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/content'
);
padma_register_block('PadmaContentBlock', padma_url() . '/library/blocks/content', $class_file, $icons);