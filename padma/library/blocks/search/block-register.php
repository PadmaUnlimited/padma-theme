<?php

$class_file = __DIR__ . '/search.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/search'
);
padma_register_block('PadmaSearchBlock', padma_url() . '/library/blocks/search', $class_file, $icons);