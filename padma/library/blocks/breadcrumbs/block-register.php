<?php

$class_file = __DIR__ . '/breadcrumbs.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/breadcrumbs'
);
padma_register_block('PadmaBreadcrumbsBlock', padma_url() . '/library/blocks/breadcrumbs', $class_file, $icons);