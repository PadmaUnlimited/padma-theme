<?php
class PadmaJSProperties {


	protected static $js_properties;
	protected static $element_selectors;

	protected static $enqueued_js_properties = array(
		'parallax' => array()
	);


	public static function init() {

		add_action('wp_head', array(__CLASS__, 'prepare_js_properties'));
		add_action('wp_head', array(__CLASS__, 'do_parallax'));

	}


	public static function prepare_js_properties() {

		self::$js_properties = PadmaSkinOption::get( 'js-properties', 'design', array() );
		self::$element_selectors = PadmaElements::get_element_selectors();

		foreach ( self::$js_properties as $element_id => $js_properties ) {

			if ( ! isset( self::$element_selectors[ $element_id ] ) ) {
				continue;
			}
			
			$element_selector = self::$element_selectors[$element_id];

			foreach ( $js_properties as $js_property_id => $js_property_value ) {

				switch ( $js_property_id ) {

					case 'background-parallax':

						if ( $js_property_value == 'enable' ) {

							self::$enqueued_js_properties['parallax'][ $element_selector ] = array(
								'data-stellar-background-ratio' => padma_get( 'background-parallax-ratio', self::$js_properties[$element_id], 0.5 )
							);

						}

					break;

				}

			}

		}

	}


	public static function do_parallax() {

		if ( empty(self::$enqueued_js_properties['parallax']) ) {
			return false;
		}

		wp_enqueue_script( 'padma-stellar', padma_url() . '/library/media/js/jquery.stellar.js', array( 'jquery' ) );
		wp_localize_script( 'padma-stellar', 'PadmaParallax', self::$enqueued_js_properties['parallax'] );

	}


}