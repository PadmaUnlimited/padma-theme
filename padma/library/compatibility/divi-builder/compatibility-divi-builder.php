<?php

class PadmaCompatibilityDiviBuilder {
	
	/**
	 * Unique instance of class
	 */
	public static $instance;

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}
	
	/**
	 * Gets the instance of the class
	 */
	public static function init() {

		if(!class_exists('ET_Builder_Plugin')){
			return;
		}

		if ( null === self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/**
	 * Hooking methods into WordPress actions and filters
	 * @return void
	 */
	public static function init_hooks(){

		if ( is_admin() ) {

			$priority = defined( 'DOING_AJAX' ) && DOING_AJAX ? 10 : 1000;

			// Adding script for UX enhancement in dashboard needs earlier hook registration
			add_action( 'wp_loaded', array( __CLASS__, 'load_theme_compat' ), $priority );
		
		} else {
			// Add after $post object has been set up so it can only load theme compat on page
			// which uses Divi Builder only
			add_action( 'wp', array( __CLASS__, 'load_theme_compat' ) );

			// Load compatibility scripts
			add_action( 'wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts' ), 15 );
		}		

		//add_action('after_setup_theme', array(__CLASS__, 'start'));
		add_action( 'wp_head', array(__CLASS__, 'divi_js_meta') );	

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ), 10 );

		add_filter( 'et_fb_bundle_dependencies', array( __CLASS__, 'add_fb_bundle_dependencies' ) );

	}

	public static function divi_js_meta(){
		echo "<script type='text/javascript'> document.documentElement.className = 'js'; </script>";
	}

	// Copied from Divi Builder 2.19.1 / theme-compat.php
	/**
	 * Load compatibility style & scripts
	 * @return void
	 */	
	public static function enqueue_scripts() {

		// Add Elegant Shortcode Support
		$shortcode_file_path = get_template_directory() . '/epanel/shortcodes/shortcodes.php';

		if ( 'Elegant Themes' === wp_get_theme( 'Author' ) && file_exists( $shortcode_file_path ) ) {
			// Dequeue standard Elegant Shortcode styling
			wp_dequeue_style( 'et-shortcodes-css' );

			// Enqueue modified (more-specific) Elegant Shortcode styling
			wp_enqueue_style(
				'et-builder-compat-elegant-shortcodes',
				ET_BUILDER_PLUGIN_URI . '/theme-compat/css/elegant-shortcodes.css',
				array( 'et-builder-modules-style' ),
				ET_BUILDER_PLUGIN_VERSION
			);
		}
	}

	public static function load_theme_compat(){

	}


	/**
	 * Description
	 * @since 1.0
	 * @return void
	 */
	function admin_enqueue_scripts() {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$current_screen = get_current_screen();

		// Only load in post-editing screen
		if ( isset( $current_screen->base ) && 'post' === $current_screen->base ) {
			wp_enqueue_script( 'et_pb_theme_flatsome_editor', ET_BUILDER_PLUGIN_URI . '/theme-compat/js/flatsome-editor.js', array( 'et_pb_admin_js', 'jquery' ), ET_BUILDER_VERSION, true );
		}
	}


	/**
	 * Register theme compat script as bundle.js' dependency so it is being loaded before bundle.js
	 */
	function add_fb_bundle_dependencies( $deps ) {

		$deps[] = 'et_fb_theme_the7';

		return $deps;
	}

}	