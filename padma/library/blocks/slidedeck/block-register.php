<?php

if ( isset($GLOBALS['SlideDeckPlugin']) && is_object($GLOBALS['SlideDeckPlugin']) ){
	$class_file = __DIR__ . '/slidedeck.php';
	$icons = array(
		'path' => __DIR__ . '/',
		'url' => padma_url() . '/library/blocks/slidedeck'
	);
	padma_register_block('PadmaSlideDeckBlock', padma_url() . '/library/blocks/slidedeck', $class_file, $icons);
}
