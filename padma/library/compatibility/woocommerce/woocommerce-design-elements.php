<?php
/**
 * Register Woocommerce selectors for Visual Editor
 *
 * @return void
 */
function padma_storefront_wc_register_elements() {

	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-page-title',
			'parent'      => 'block-content',
			'name'        => __( 'Shop Title', 'padma' ),
			'description' => 'Storefront: WooCommerce',
			'selector'    => 'body.woocommerce .block-type-content h1.page-title',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-result-count',
			'parent'      => 'block-content',
			'name'        => __( 'Result Count', 'padma' ),
			'description' => 'Storefront: WooCommerce',
			'selector'    => 'body.woocommerce .block-type-content p.woocommerce-result-count',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-ordering',
			'parent'      => 'block-content',
			'name'        => __( 'Ordering', 'padma' ),
			'description' => __( 'Storefront: WooCommerce', 'padma' ),
			'selector'    => 'body.woocommerce .block-type-content form.woocommerce-ordering',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-breadcrumbs',
			'parent'      => 'block-content',
			'name'        => __( 'Breadcrumbs', 'padma' ),
			'description' => __( 'Storefront: WooCommerce', 'padma' ),
			'selector'    => 'body.woocommerce .block-type-content .woocommerce-breadcrumb',
		)
	);

	/* Product Listings */
	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-listings-product-containers',
			'parent'      => 'block-content',
			'name'        => __( 'Product Listings', 'padma' ),
			'description' => 'Storefront: WooCommerce',
			'selector'    => 'body.woocommerce .block-type-content ul.products li.product',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-listings-product-names',
			'parent'      => 'block-content-wc-listings-product-containers',
			'name'        => __( 'Product Listings: Names', 'padma' ),
			'description' => __( 'Storefront: WooCommerce', 'padma' ),
			'selector'    => 'body.woocommerce .block-type-content ul.products li.product h3',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-listings-product-images',
			'parent'      => 'block-content-wc-listings-product-containers',
			'name'        => __( 'Product Listings: Images', 'padma' ),
			'description' => __( 'Storefront: WooCommerce', 'padma' ),
			'selector'    => 'body.woocommerce .block-type-content ul.products li.product img',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-listings-product-onsale',
			'parent'      => 'block-content-wc-listings-product-containers',
			'name'        => __( 'Product Listings: Onsale', 'padma' ),
			'description' => __( 'Storefront: WooCommerce', 'padma' ),
			'selector'    => 'body.woocommerce .block-type-content ul.products li.product span.onsale',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-listings-product-price',
			'parent'      => 'block-content-wc-listings-product-containers',
			'name'        => __( 'Product Listings: Price', 'padma' ),
			'description' => __( 'Storefront: WooCommerce', 'padma' ),
			'selector'    => 'body.woocommerce .block-type-content ul.products li.product span.price',
		)
	);
	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-listings-product-prices',
			'parent'      => 'block-content-wc-listings-product-containers',
			'name'        => __( 'Product Listings: Prices', 'padma' ),
			'description' => __( 'Storefront: WooCommerce', 'padma' ),
			'selector'    => 'body.woocommerce .block-type-content ul.products li.product span.amount',
		)
	);
	/* End Product Listings */

	/* Product Pages */
	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-product-page-name',
			'parent'      => 'block-content',
			'name'        => __( 'Product Page: Name', 'padma' ),
			'description' => 'Storefront: WooCommerce',
			'selector'    => 'body.woocommerce .block-type-content div.product div.summary h1.product_title',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-image',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Image', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce .block-type-content div.product div.images img',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-price',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Price', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce .block-type-content div.product div.summary p.price',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-short-description',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Short Description', 'padma' ),
			'indent-in-selector' => true,
			'description'        => __( 'Storefront: WooCommerce', 'padma' ),
			'selector'           => 'body.woocommerce .block-type-content div.product div.summary div[itemprop="description"]',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-short-description-details',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Short Description', 'padma' ),
			'indent-in-selector' => true,
			'description'        => __( 'Storefront: WooCommerce Description', 'padma' ),
			'selector'           => 'body.woocommerce .block-type-content div.product div.woocommerce-product-details__short-description',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-short-description-details-ul',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Short Description List', 'padma' ),
			'indent-in-selector' => true,
			'description'        => __( 'Storefront: WooCommerce Description List', 'padma' ),
			'selector'           => 'body.woocommerce .block-type-content div.product div.woocommerce-product-details__short-description ul',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-short-description-details-ul-li',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Short Description List Item', 'padma' ),
			'indent-in-selector' => true,
			'description'        => __( 'Storefront: WooCommerce Description List', 'padma' ),
			'selector'           => 'body.woocommerce .block-type-content div.product div.woocommerce-product-details__short-description ul li',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-short-description-details-a',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Short Description Link', 'padma' ),
			'indent-in-selector' => true,
			'description'        => __( 'Storefront: WooCommerce Description List', 'padma' ),
			'selector'           => 'body.woocommerce .block-type-content div.product div.woocommerce-product-details__short-description a',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-short-description-out-of-stock',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Short Description Stock', 'padma' ),
			'indent-in-selector' => true,
			'description'        => __( 'Storefront: WooCommerce Stock', 'padma' ),
			'selector'           => 'body.woocommerce .block-type-content div.product .stock.out-of-stock',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-short-description-meta',
			'parent'             => 'block-content',
			'name'               => __( 'Product Pag e: Short Description Meta', 'padma' ),
			'indent-in-selector' => true,
			'description'        => __( 'Storefront: WooCommerce Meta', 'padma' ),
			'selector'           => 'body.woocommerce .block-type-content div.product .product_meta',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-short-description-meta-a',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page:  Short Description Meta Link', 'padma' ),
			'indent-in-selector' => true,
			'description'        => __( 'Storefront: WooCommerce Meta link', 'padma' ),
			'selector'           => 'body.woocommerce .block-type-content div.product .product_meta a',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-button',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Button', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce .button a.product_type_simple.add_to_cart_button.ajax_add_to_cart',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-button-item',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Button', 'padma' ),
			'indent-in-selector' => true,
			'description'        => __( 'Storefront: WooCommerce Button', 'padma' ),
			'selector'           => 'body.woocommerce ul.products li.product .button',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-button-text',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Button text', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce a.button.product_type_simple.add_to_cart_button.ajax_add_to_cart',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-product-title',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Product Title', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce ul.products li.product h2.woocommerce-loop-product__title',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-single-button',
			'parent'             => 'block-content',
			'name'               => __( 'Product  Page: Single Button', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce .button.single_add_to_cart_button.button.alt',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-single-button-text',
			'parent'             => 'block-content',
			'name'               => __( 'Product Pag e: Single Button Text', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce a.button.single_add_to_cart_button.button.alt',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-input-text',
			'parent'             => 'block-content',
			'name'               => __( 'Product  Page: Input Text', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce .input-text.qty.text',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-product-page-woocommerce-message',
			'parent'             => 'block-content',
			'name'               => __( 'Product Page: Woocommerce  Message', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce .woocommerce-message',
		)
	);
	/* End Product Pages */

	/* Related Products */
	PadmaElementAPI::register_element(
		array(
			'group'       => 'blocks',
			'id'          => 'block-content-wc-related-products',
			'parent'      => 'block-content ',
			'name'        => __( 'Related Products Container', 'padma' ),
			'description' => 'Storefront: WooCommerce',
			'selector'    => 'body.woocommerce .block-type-content div.related',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-related-products-heading',
			'parent'             => 'block-content',
			'name'               => __( 'Related Products Heading', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce .block-type-content div.related h2',
		)
	);

	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-related-products-product',
			'parent'             => 'block-content',
			'name'               => __( 'Related Product Container', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce .block-type-content div.related li.product',
		)
	);
	/* End Related Products */

	/*	Place order	*/
	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-place-order-single-button',
			'parent'             => 'block-content',
			'name'               => __( 'Place order: Single Button', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce button#place_order.button',
		)
	);
	PadmaElementAPI::register_element(
		array(
			'group'              => 'blocks',
			'id'                 => 'block-content-wc-info',
			'parent'             => 'block-content',
			'name'               => __( 'Woocommerce Info', 'padma' ),
			'indent-in-selector' => true,
			'description'        => 'Storefront: WooCommerce',
			'selector'           => 'body.woocommerce .woocommerce-info',
		)
	);
	/*	End Place order	*/

}
