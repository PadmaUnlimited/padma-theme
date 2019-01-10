<?php

class PadmaCompatibilityDiviBuilder {
	
	/**
	 * Unique instance of class
	 */
	public static $instance;
	
	public static function init() {

		if(!class_exists('ET_Builder_Plugin')){
			return;
		}

		//add_action('after_setup_theme', array(__CLASS__, 'start'));

		add_action( 'wp_head', array(__CLASS__, 'divi_js_meta') );		
		
	}

	public static function divi_js_meta(){
		echo "<script type='text/javascript'> document.documentElement.className = 'js'; </script>";
	}


	/*
	public static function start(){
		// load after $post is initiated. Cannot load before `init` hook
		if ( is_admin() ) {
			$priority = defined( 'DOING_AJAX' ) && DOING_AJAX ? 10 : 1000;

			// Adding script for UX enhancement in dashboard needs earlier hook registration
			add_action( 'wp_loaded', array( __CLASS__, 'load_theme_compat' ), $priority );
		} else {
			// Add after $post object has been set up so it can only load theme compat on page
			// which uses Divi Builder only
			add_action( 'wp', array( __CLASS__, 'load_theme_compat' ) );

			// Load compatibility scripts
			//add_action( 'wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts' ), 15 );
		}

		if ( is_null( self::$instance ) ) {
	      self::$instance = new self();
	    }

		return self::$instance;
	}
	*/

	// Copied from Divi Builder 2.19.1 / theme-compat.php
	/**
	 * Load compatibility style & scripts
	 * @return void
	 */
	/*
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
	*/
}	