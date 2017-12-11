<?php
class BloxCompatibilityWooCommerce {


	public static function init() {

		/* Check requirements */
		if ( !self::check_requirements() )
			return;

		/* Load things */
		require_once BLOX_LIBRARY_DIR . '/compatibility/woocommerce/woocommerce-breadcrumbs.php';

		require_once BLOX_LIBRARY_DIR . '/compatibility/woocommerce/woocommerce-design-elements.php';
		require_once BLOX_LIBRARY_DIR . '/compatibility/woocommerce/woocommerce-design-defaults.php';

		/* Handle elements */
		add_action('blox_register_elements', 'blox_storefront_wc_register_elements', 50);
		add_filter('blox_element_data_defaults', 'blox_storefront_wc_design_defaults', 50);

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

		wp_enqueue_style('blox-storefront-wooc', blox_url() . '/library/compatibility/woocommerce/blox-storefront-wooc.css');

	}



	public static function disallow_edit_of_shop_page() {

		add_filter('blox_layout_selector_no_edit_item_single-page-' . woocommerce_get_page_id('shop'), '__return_true');

	}


}