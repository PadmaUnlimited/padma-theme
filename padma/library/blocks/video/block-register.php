<?php

$class_file = __DIR__ . '/video.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/video'
);
padma_register_block('PadmaVideoBlock', padma_url() . '/library/blocks/video', $class_file, $icons);