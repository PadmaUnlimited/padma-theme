<?php

namespace Padma;

$class_file = __DIR__ . '/social.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/social'
);
padma_register_block('PadmaSocialBlock', padma_url() . '/library/blocks/social', $class_file, $icons);