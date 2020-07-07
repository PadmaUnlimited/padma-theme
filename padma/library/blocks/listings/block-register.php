<?php

namespace Padma;
$class_file = __DIR__ . '/listings.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/listings'
);
padma_register_block('PadmaListingsBlock', padma_url() . '/library/blocks/listings', $class_file, $icons);