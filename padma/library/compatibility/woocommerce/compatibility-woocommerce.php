<?php

/**
 * Padma Compatibility with WooCommerce.
 */
class PadmaCompatibilityWooCommerce {


	/**
	 * Init class
	 *
	 * @return void
	 */
	public static function init() {

		/* Check requirements */
		if ( ! self::check_requirements() ) {
			return;
		}

		/* Load things */
		require_once PADMA_LIBRARY_DIR . '/compatibility/woocommerce/woocommerce-breadcrumbs.php';
		require_once PADMA_LIBRARY_DIR . '/compatibility/woocommerce/woocommerce-design-elements.php';
		require_once PADMA_LIBRARY_DIR . '/compatibility/woocommerce/woocommerce-design-defaults.php';

		/* Handle elements */
		add_action( 'padma_register_elements', 'padma_storefront_wc_register_elements', 50 );
		add_filter( 'padma_element_data_defaults', 'padma_storefront_wc_design_defaults', 50 );

		/* Remove WooCommerce Breadcrumbs */
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

		/* Setup hooks */
		add_action( 'init', array( __CLASS__, 'disallow_edit_of_shop_page' ) );
		add_action( 'wp', array( __CLASS__, 'enqueue_styles' ) );

		/* Add theme support for WooCommerce */
		add_theme_support( 'woocommerce' );

		/**
		 * https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)#enabling-the-gallery-in-themes-that-declare-wc-support
		 */
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

	}

	/**
	 * Check is Woocommer is installed and active
	 *
	 * @return bool
	 */
	public static function check_requirements() {

		/**
		 * Is multisite?
		 */
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			/**
			 * Woocommerce is active?
			 */
			if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			/**
			 * Woocommerce is active?
			 */
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
				return true;
			} else {
				return false;
			}
		}
	}


	public static function enqueue_styles() {

		if ( is_admin() ) {
			return;
		}

		add_filter(
			'padma_general_css',
			function( $general_css_fragments ) {
				$general_css_fragments['storefront-wooc'] = PADMA_LIBRARY_DIR . '/compatibility/woocommerce/padma-storefront-wooc.css';
				return $general_css_fragments;
			}
		);

	}


	/**
	 * Disallow edit of shop page
	 *
	 * @return void
	 */
	public static function disallow_edit_of_shop_page() {

		if ( function_exists( 'wc_get_page_id' ) ) {
			add_filter( 'padma_layout_selector_no_edit_item_single-page-' . wc_get_page_id( 'shop' ), '__return_true' );
		}
	}

}
