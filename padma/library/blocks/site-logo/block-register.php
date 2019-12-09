<?php

$class_file = __DIR__ . '/site-logo.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/site-logo'
);
padma_register_block('PadmaSiteLogoBlock', padma_url() . '/library/blocks/site-logo', $class_file, $icons);