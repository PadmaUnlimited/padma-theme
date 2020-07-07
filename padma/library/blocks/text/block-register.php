<?php

namespace Padma;

$class_file = __DIR__ . '/text.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/text'
);
padma_register_block('PadmaTextBlock', padma_url() . '/library/blocks/text', $class_file, $icons);