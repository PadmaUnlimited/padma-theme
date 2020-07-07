<?php

namespace Padma;

$class_file = __DIR__ . '/embed.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/embed'
);
padma_register_block('PadmaEmbedBlock', padma_url() . '/library/blocks/embed', $class_file, $icons);