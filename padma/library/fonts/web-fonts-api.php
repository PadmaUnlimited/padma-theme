<?php
function padma_register_web_font_provider($class) {

	return new $class;

}


abstract class PadmaWebFontProvider {


	public $id 					= null;
	public $name 				= null;
	public $search 				= true;
	public $sorting_options 	= array();
	public $webfont_provider 	= false;
	public $load_with_ajax 		= false;


	protected $transient_id;


	/* Initiate the web font provider... Add hooks, etc */
	public function __construct() {

		if ( !$this->id || !$this->name )
			return;

		$this->transient_id = 'padma_webfonts_' . $this->id;

		add_action('padma_fonts_browser_tabs', array($this, 'tab'));
		add_action('padma_fonts_browser_content', array($this, 'content'));

		if ( $this->load_with_ajax )
			add_action('padma_fonts_ajax_list_fonts_' . $this->id, array($this, 'list_fonts'));

	}


	public function tab() {

		echo '<li><a href="#' . $this->id . '-fonts">' . $this->name . '</a></li>';

	}


	public function content() {

		$attrs = array(
			'search' 	=> $this->search ? 'true' : 'false',
			'sorting' 	=> (is_array($this->sorting_options) && !empty($this->sorting_options)) ? 'true' : 'false',
			'provider' 	=> $this->webfont_provider ? $this->webfont_provider : 'false',
			'ajax' 		=> $this->load_with_ajax ? 'true' : 'false'
		);

		echo '<div id="' . $this->id . '-fonts" class="tab-content font-provider-tab-content" data-font-allow-search="' . $attrs['search'] . '" data-font-allow-sorting="' . $attrs['sorting'] . '" data-font-webfont-provider="' . $attrs['provider'] . '" data-font-load-with-ajax="' . $attrs['ajax'] . '">';
			
			if ( $this->search )
				$this->content_search();

			$this->content_fonts_list();

		echo '</div><!-- #-fonts -->';

	}


	public function content_search() {

		echo '<form class="fonts-search">';

	      	echo '<input class="fonts-filter" type="text" placeholder="Search ' . $this->name . '" name="search-input">';

	      	if ( is_array($this->sorting_options) && !empty($this->sorting_options) ) {

	      		echo '<div class="select-container"><select name="choices">';
	      			echo '<option value="" disabled="disabled">&ndash; Sort By &ndash;</option>';
	      			
	      			foreach ( $this->sorting_options as $sorting_option_value => $sorting_option_text )
	      				echo '<option value="' . $sorting_option_value . '">' . $sorting_option_text . '</option>';

	      		echo '</select></div><!-- .select-container -->';

	      	}

		echo '</form>';

	}


	public function content_fonts_list() {

		echo '
			<div class="fonts-list webfonts-list">
				<ul></ul>

				<div class="loading fonts-loading"><p>Loading Fonts...</p></div>
				<div class="fonts-noresults" style="display:none;">No Results</div>
			</div><!-- .fonts-list -->
		';

	}


	/* Retrieves the fonts from the provider */
	public function query_fonts($sortby) {

		return array(
			array(
				'id' 		=> 'font-family',
				'name' 		=> 'Font Family',
				'stack' 	=> 'font family',
				'variants' 	=> 'variants',
			)
		);

	}

 
 	/* Gets the fonts using the transient if possible.  Otherwise it'll query the fonts and set the transient */
	public function retrieve_fonts($sortby = false) {

		$fonts = get_transient($this->transient_id, array());

		if (!$fonts || !is_array($fonts) || empty($fonts) ) {

     		$fonts[$sortby] 	= $this->query_fonts($sortby);     		

     		/* Only set the transient if the fonts are returned properly and there's no error */
     		if ( !empty($fonts) && empty($fonts[$sortby]['error']) ){				
				set_transient($this->transient_id, $fonts, 60 * 60 * 24);
     		}

		}

		/* If there's an error, delete the transient */
		if ( !empty($fonts[$sortby]['error']) || empty($fonts[$sortby]) || is_wp_error($fonts[$sortby]) ){
			$this->reset_transients();
		}

		return isset($fonts[$sortby]) ? $fonts[$sortby] : null;

	}

	public function retrieve_font_variant($font){
		$fonts = get_transient($this->transient_id, array());
	}


	/* Resets font provider transient */
	public function reset_transients() {

		return delete_transient($this->transient_id);

	}


	/* Outputs HTML for fonts list */
	public function list_fonts($sortby = false) {

		if ( padma_post('sortby') )
			$sortby = padma_post('sortby');

		$fonts = $this->retrieve_fonts($sortby);

		if ( !$fonts ) {
			echo '<p class="error">Unable to retrieve fonts at this time.</p>';
			return;
		}


		/* Display possible error */
			if ( isset($fonts['error']) ) {

				echo '<p class="error">' . $fonts['error'] . '</p>';
				return;

			}

		/*
		    [911] => Array
		        (
		            [id] => Dokdo
		            [name] => Dokdo
		            [stack] => Dokdo
		            [variants] => Array
		                (
		                    [0] => regular
		                )

		        )

		    [912] => Array
		        (
		            [id] => Srisakdi
		            [name] => Srisakdi
		            [stack] => Srisakdi
		            [variants] => Array
		                (
		                    [0] => regular
		                    [1] => 700
		                )

		        )

		    [913] => Array
		        (
		            [id] => B612 Mono
		            [name] => B612 Mono
		            [stack] => B612 Mono
		            [variants] => Array
		                (
		                    [0] => regular
		                    [1] => italic
		                    [2] => 700
		                    [3] => 700italic
		                )

		        )

		    [914] => Array
		        (
		            [id] => B612
		            [name] => B612
		            [stack] => B612
		            [variants] => Array
		                (
		                    [0] => regular
		                    [1] => italic
		                    [2] => 700
		                    [3] => 700italic
		                )

		        )
		        */
		/* Output the fonts */
		foreach ( $fonts as $font ) {
			
			$variants = '';
			foreach ($font['variants'] as $key => $value) {
				$variants .= $value . ',';
			}
			$variants = rtrim($variants,',');
			
			$html = '<li data-value="' . $font['id'] . '" style="font-family:' . $font['stack'] . ';" data-variants="[';			
			$html .= $variants;
			$html .= ']">
					<span class="font-family">' . $font['name'] . '</span> 
					<span class="font-preview-text">The quick brown fox jumps over the lazy dog.</span> 

					<span title="Use Font" class="use-font action"></span>
					<span title="Preview Font" class="preview-font action"></span>
				</li>';
			
			echo $html;

		}

		return true;

	}


}