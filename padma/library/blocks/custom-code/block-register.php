<?php

$class_file = __DIR__ . '/custom-code.php';
padma_register_block_complex('PadmaCustomCodeBlock', padma_url() . '/library/blocks/custom-code', $class_file);