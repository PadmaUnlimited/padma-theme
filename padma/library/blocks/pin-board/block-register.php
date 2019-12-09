<?php

$class_file = __DIR__ . '/pin-board.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/pin-board'
);
padma_register_block('PadmaPinBoardCoreBlock', padma_url() . '/library/blocks/pin-board', $class_file, $icons);