<?php
function blox_storefront_wc_design_defaults($existing_defaults) {

	return array_merge($existing_defaults, array(
		'block-content-wc-breadcrumbs' => array(
			'properties' => array(
				'margin-top' => '10'
			)
		),

		'block-content-wc-page-title' => array(
			'properties' => array(
				'font-size' => '24',
				'line-height' => '160'
			)
		),

		'block-content-wc-product-page-short-description' => array(
			'properties' => array(
				'line-height' => '130'
			)
		),

		'block-content-wc-related-products-heading' => array(
			'properties' => array(
				'font-size' => '20',
				'line-height' => '150',
				'margin-bottom' => '10'
			)
		)
	));

}