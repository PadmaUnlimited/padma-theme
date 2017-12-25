<?php
function padma_storefront_wc_register_elements() {

	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-page-title',
		'parent' => 'block-content',
		'name' => 'Shop Title',
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content h1.page-title'
	));

	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-breadcrumbs',
		'parent' => 'block-content',
		'name' => 'Breadcrumbs',
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content .woocommerce-breadcrumb'
	));


	/* Product Listings */
	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-listings-product-containers',
		'parent' => 'block-content',
		'name' => 'Product Listings',
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content ul.products li.product'
	));

	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-listings-product-names',
		'parent' => 'block-content-wc-listings-product-containers',
		'name' => 'Product Listings: Names',
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content ul.products li.product h3'
	));

	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-listings-product-images',
		'parent' => 'block-content-wc-listings-product-containers',
		'name' => 'Product Listings: Images',
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content ul.products li.product img'
	));

	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-listings-product-prices',
		'parent' => 'block-content-wc-listings-product-containers',
		'name' => 'Product Listings: Prices',
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content ul.products li.product span.amount'
	));
	/* End Product Listings */


	/* Product Pages */
	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-product-page-name',
		'parent' => 'block-content',
		'name' => 'Product Page: Name',
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content div.product div.summary h1.product_title'
	));

	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-product-page-image',
		'parent' => 'block-content',
		'name' => 'Product Page: Image',
		'indent-in-selector' => true,
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content div.product div.images img'
	));

	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-product-page-price',
		'parent' => 'block-content',
		'name' => 'Product Page: Price',
		'indent-in-selector' => true,
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content div.product div.summary p.price'
	));

	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-product-page-short-description',
		'parent' => 'block-content',
		'name' => 'Product Page: Short Description',
		'indent-in-selector' => true,
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content div.product div.summary div[itemprop="description"]'
	));
	/* End Product Pages */


	/* Related Products */
	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-related-products',
		'parent' => 'block-content',
		'name' => 'Related Products Container',
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content div.related'
	));

	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-related-products-heading',
		'parent' => 'block-content',
		'name' => 'Related Products Heading',
		'indent-in-selector' => true,
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content div.related h2'
	));

	PadmaElementAPI::register_element(array(
		'group' => 'blocks',
		'id' => 'block-content-wc-related-products-product',
		'parent' => 'block-content',
		'name' => 'Related Product Container',
		'indent-in-selector' => true,
		'description' => 'Storefront: WooCommerce',
		'selector' => '.woocommerce .block-type-content div.related li.product'
	));
	/* End Related Products */


}