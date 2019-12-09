<?php

$class_file = __DIR__ . '/mailchimp-for-wp.php';
$icons = array(
	'path' => __DIR__ . '/',
	'url' => padma_url() . '/library/blocks/mailchimp-for-wp'
);
padma_register_block('PadmaMailchimpForWPBlock', padma_url() . '/library/blocks/mailchimp-for-wp', $class_file, $icons);