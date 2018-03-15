<?php
class PadmaWebFontsLoader {


	public static function init() {

		add_action( 'wp', array( __CLASS__, 'enqueue_webfont_api_for_design_editor' ) );
		add_action( 'padma_stylesheets', array( __CLASS__, 'google_fonts_stylesheet' ), 1 );

		add_action( 'padma_flush_cache', array( __CLASS__, 'flush_cache' ) );

	}


	/* Google Web Fonts */
	public static function enqueue_webfont_api_for_design_editor() {

		if ( ! PadmaRoute::is_visual_editor_iframe( 'design' ) )
			return;

		wp_enqueue_script( 'webfont', padma_format_url_ssl( 'http:///ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js' ) );

	}


	public static function google_check_if_should_load() {

		$webfonts_in_use = self::get_fonts_in_use();

		if ( !is_array($webfonts_in_use) || count($webfonts_in_use) == 0 || !isset($webfonts_in_use['google']) )
			return false;

		return $webfonts_in_use;

	}


	public static function google_fonts_stylesheet() {

		$webfonts_in_use = self::google_check_if_should_load();

		if ( ! $webfonts_in_use || !is_array(padma_get('google', $webfonts_in_use)) )
			return;

		foreach ( $webfonts_in_use['google'] as $key => $font ){
			$webfonts_in_use['google'][ $key ] = urlencode( $font );
		}

		$font = implode( '|', array_filter($webfonts_in_use['google']) );

		if(strlen($font) > 0 ){
			$stylesheet_url = '//fonts.googleapis.com/css?family=' . $font ;
			echo "<link rel='stylesheet' id='padma-google-fonts' href='$stylesheet_url' type='text/css' media='all' />\n";
		}

	}
	/* End Google Web Fonts */


	public static function get_fonts_in_use() {

		/* If cache exists then use it */
		$cache = get_transient( 'bt_webfont_cache_template_' . PadmaOption::$current_skin );

		if ( is_array($cache) )
			return $cache;

		/* Build cache otherwise */
		self::cache();

		return get_transient( 'bt_webfont_cache_template_' . PadmaOption::$current_skin );

	}


	public static function cache() {

		$raw_webfonts = self::pluck_webfonts(PadmaElementsData::get_all_elements());
		$sorted_webfonts = array();

		foreach ( $raw_webfonts as $webfont ) {

			$fragments = explode('|', $webfont);

			$sorted_webfonts[$fragments[0]][] = !empty($fragments[2]) ? $fragments[1] . ':' . $fragments[2] : $fragments[1]; /* $fragments[2] are the variants */
			$sorted_webfonts[$fragments[0]] = array_unique($sorted_webfonts[$fragments[0]]);

		}

		return set_transient( 'bt_webfont_cache_template_' . PadmaOption::$current_skin, $sorted_webfonts );

	}


		public static function pluck_webfonts($array) {

			$web_fonts = array();

			foreach ( $array as $key => $value ) {

				/* If the value is an array, then loop this function to pluck the font values out of instances, states, etc */
				if ( is_array($value) ) {

					$web_fonts = array_merge($web_fonts, self::pluck_webfonts($value));

				/* We've found a font family property.  Now make sure that the font is a web font by checking for the | delimiter */
				} else if ( $key === 'font-family' && strpos($value, '|') ) {

					$web_fonts[] = $value;

				}

			}

			return array_unique($web_fonts);

		}


	public static function flush_cache() {

		return delete_transient( 'bt_webfont_cache_template_' . PadmaOption::$current_skin );

	}


}