<?php

namespace Padma;
class PadmaCompatibilityElementor {
	/*
	private static $instance = null;

	public static function get_instance() {
		
		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}*/


	public static function init() {

		return;

		/*
		if(!PadmaOption::get('elementor-support'))
			return;
		*/

		//add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) );

		//self::load();

	}
	/*
	public function widgets_registered() {

		// We check if the Elementor plugin has been installed / activated.
		if(defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')){

			debug(PadmaBlocks::$core_blocks);

		 // We look for any theme overrides for this custom Elementor element.
		 // If no theme overrides are found we use the default one in this plugin.

			$widget_file = 'plugins/elementor/my-widget.php';
			$template_file = locate_template($widget_file);

			if ( !$template_file || !is_readable( $template_file ) ) {
				$template_file = plugin_dir_path(__FILE__).'my-widget.php';
			}
			if ( $template_file && is_readable( $template_file ) ) {
				require_once $template_file;
			}
		}
	}*/
		
}