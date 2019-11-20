<?php

$class_file = __DIR__ . '/mailchimp-for-wp.php';
padma_register_block_complex('PadmaMailchimpForWPBlock', padma_url() . '/library/blocks/mailchimp-for-wp', $class_file);