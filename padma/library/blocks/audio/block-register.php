<?php

$class_file = __DIR__ . '/audio.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/audio'
);
padma_register_block('PadmaAudioBlock', padma_url() . '/library/blocks/audio', $class_file, $icons);
