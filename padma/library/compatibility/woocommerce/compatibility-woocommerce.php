<?php
class PadmaCompatibilityWooCommerce {


	public static function init() {

		/* Check requirements */
		if ( !self::check_requirements() )
			return;

		/* Load things */
		require_once PADMA_LIBRARY_DIR . '/compatibility/woocommerce/woocommerce-breadcrumbs.php';
		require_once PADMA_LIBRARY_DIR . '/compatibility/woocommerce/woocommerce-design-elements.php';
		require_once PADMA_LIBRARY_DIR . '/compatibility/woocommerce/woocommerce-design-defaults.php';

		/* Handle elements */
		add_action('padma_register_elements', 'padma_storefront_wc_register_elements', 50);
		add_filter('padma_element_data_defaults', 'padma_storefront_wc_design_defaults', 50);

		/* Remove WooCommerce Breadcrumbs */
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

		/* Setup hooks */
		add_action('init', array(__CLASS__, 'disallow_edit_of_shop_page'));
		add_action('wp', array(__CLASS__, 'enqueue_styles'));

		/* Add theme support for WooCommerce */
		add_theme_support('woocommerce');

	}


	public static function check_requirements() {

		if ( !class_exists('WooCommerce') )
			return false;

		return true;

	}


	public static function enqueue_styles() {

		if ( is_admin() )
			return;

		add_filter('padma_general_css',function($general_css_fragments){
			$general_css_fragments['storefront-wooc'] = PADMA_LIBRARY_DIR . '/compatibility/woocommerce/padma-storefront-wooc.css';
			return $general_css_fragments;
		});		

	}



	public static function disallow_edit_of_shop_page() {

		add_filter('padma_layout_selector_no_edit_item_single-page-' . wc_get_page_id('shop'), '__return_true');

	}


}