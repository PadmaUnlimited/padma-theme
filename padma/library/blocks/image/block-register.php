<?php

$class_file = __DIR__ . '/image.php';
$icons      = array(
	'path' => __DIR__ . '/',
	'url'  => padma_url() . '/library/blocks/image',
);
padma_register_block( 'PadmaImageBlock', padma_url() . '/library/blocks/image', $class_file, $icons );
