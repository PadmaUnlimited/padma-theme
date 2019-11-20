<?php

if ( isset($GLOBALS['SlideDeckPlugin']) && is_object($GLOBALS['SlideDeckPlugin']) ){
	$class_file = __DIR__ . '/slidedeck.php';
	padma_register_block_complex('PadmaSlideDeckBlock', padma_url() . '/library/blocks/slidedeck', $class_file);
}
