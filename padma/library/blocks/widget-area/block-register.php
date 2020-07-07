<?php

namespace Padma;

$class_file = __DIR__ . '/widget-area.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/widget-area'
);
padma_register_block('PadmaWidgetAreaBlock', padma_url() . '/library/blocks/widget-area', $class_file, $icons);