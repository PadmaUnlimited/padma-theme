<?php

$class_file = __DIR__ . '/footer.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/footer'
);
padma_register_block('PadmaFooterBlock', padma_url() . '/library/blocks/footer', $class_file, $icons);