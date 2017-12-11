<?php
class BloxGoogleFonts extends BloxWebFontProvider {


	public $id = 'google';

	public $name = 'Google Web Fonts';

	public $webfont_provider = 'google';

	public $load_with_ajax = true;


	public $sorting_options = array(
		'popularity' => 'Popularity',
		'trending' => 'Trending',
		'alpha' => 'Alphabetically',
		'date' => 'Date Added',
		'style' => 'Style'
	);


	protected $api_url = 'http://www.bloxtheme.com/api/google-fonts';

	// ToDo: arrange backuplocation
        protected $backup_api_url = 'http://www.bloxtheme.com/api/google-fonts';


	public function query_fonts($sortby = 'date', $retry = false) {
		
		$fonts_query = wp_remote_get(add_query_arg(array(
			'license' => 'legacy', 
			'sortby' => $sortby,
		), trailingslashit($this->api_url)), array(
			'timeout' => 20
		));

		/* If the original query to Blox cannot connect, find a way to proxy to Blox's CDN */
		if ( is_wp_error($fonts_query) ) {

			$fonts_query = wp_remote_get(add_query_arg(array(
                                'license' => 'legacy', 
                                'sortby' => $sortby,
                        ), trailingslashit($this->api_url)), array(
                                'timeout' => 20
                        ));

		}

		return json_decode(wp_remote_retrieve_body($fonts_query), true);

	}


}
blox_register_web_font_provider('BloxGoogleFonts');