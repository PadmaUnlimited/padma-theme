<?php

namespace Padma;
$class_file = __DIR__ . '/header.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/header'
);
padma_register_block('PadmaHeaderBlock', padma_url() . '/library/blocks/header', $class_file, $icons);