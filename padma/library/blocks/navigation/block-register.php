<?php

namespace Padma;

$class_file = __DIR__ . '/navigation.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/navigation'
);
padma_register_block( 'PadmaNavigationBlock', padma_url() . '/library/blocks/navigation', $class_file, $icons);