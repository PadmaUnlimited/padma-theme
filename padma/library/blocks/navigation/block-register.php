<?php

$class_file = __DIR__ . '/navigation.php';
padma_register_block_complex( 'PadmaNavigationBlock', padma_url() . '/library/blocks/navigation', $class_file );