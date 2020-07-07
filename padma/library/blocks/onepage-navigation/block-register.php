<?php

namespace Padma;

$class_file = __DIR__ . '/onepage-navigation.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/onepage-navigation'
);
padma_register_block( 'PadmaOnePageNavBlock', padma_url() . '/library/blocks/onepage-navigation', $class_file, $icons );