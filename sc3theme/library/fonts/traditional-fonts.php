<?php
class BloxTraditionalFonts extends BloxWebFontProvider {

	public $id = 'traditional';

	public $name = 'Traditional Fonts';

	public $webfont_provider = false;

	public $search = false;



	public function retrieve_fonts($sortby = false) {

		return $this->query_fonts($sortby);

	}


	public function query_fonts($sortby) {

		$fonts = array();

		foreach ( BloxFonts::get_fonts() as $font_id => $font_name )
			$fonts[] = array(
				'id' => $font_id,
				'name' => $font_name,
				'stack' => BloxFonts::get_stack($font_id) 
			);

		return $fonts;

	}


	public function content_fonts_list() {

		echo '<div class="fonts-list">';
				
			echo '<ul>';
				$this->list_fonts('traditional');
			echo '</ul>';
		
		echo '</div><!-- .fonts-list -->';

	}


}


class BloxFonts {


	public static $fonts = array(
		'georgia' 			=> 'Georgia',
		'cambria' 			=> 'Cambria',
		'palatino' 			=> 'Palatino',
		'times' 			=> 'Times',
		'times new roman' 	=> 'Times New Roman',
	
		'arial' 			=> 'Arial',
		'arial black' 		=> 'Arial Black',
		'arial narrow' 		=> 'Arial Narrow',
		'century gothic' 	=> 'Century Gothic',
		'gill sans' 		=> 'Gill Sans',
		'helvetica' 		=> 'Helvetica',
		'impact' 			=> 'Impact',
		'lucida grande' 	=> 'Lucida Grande',
		'tahoma' 			=> 'Tahoma',
		'trebuchet ms' 		=> 'Trebuchet MS',
		'verdana' 			=> 'Verdana',
	
		'courier' 			=> 'Courier',
		'courier new' 		=> 'Courier New',
	
		'papyrus' 			=> 'Papyrus',
		'copperplate' 		=> 'Copperplate'
	);


	public static $font_stacks = array(
		'georgia' 			=> 'georgia, serif',
		'cambria' 			=> 'cambria, georgia, serif',
		'palatino' 			=> 'palatino linotype, palatino, serif',
		'times' 			=> 'times, serif',
		'times new roman' 	=> 'times new roman, serif',
		'arial' 			=> 'arial, sans-serif',
		'arial black' 		=> 'arial black, sans-serif',
		'arial narrow' 		=> 'arial narrow, sans-serif',
		'century gothic' 	=> '\'Century Gothic\', CenturyGothic, AppleGothic, sans-serif',
		'gill sans' 		=> 'gill sans, sans-serif',
		'helvetica' 		=> 'helvetica, sans-serif',
		'impact' 			=> 'impact, sans-serif',
		'lucida grande' 	=> 'lucida grande, sans-serif',
		'tahoma' 			=> 'tahoma,  sans-serif',
		'trebuchet ms' 		=> 'trebuchet ms,  sans-serif',
		'verdana' 			=> 'verdana, sans-serif',
		'courier' 			=> 'courier, monospace',
		'courier new' 		=> 'courier new, monospace',
		'papyrus' 			=> 'papyrus, fantasy',
		'copperplate' 		=> 'copperplate, copperplate gothic bold, fantasy'
	);


	public static function get_fonts() {

		return apply_filters('blox_fonts', self::$fonts); 

	}


	public static function get_stack($font_id) {

		return blox_get($font_id, apply_filters('blox_fonts_stacks', self::$font_stacks));

	}


	public static function register_font(array $args) {

		extract($args);

		/* Check args */
		if ( !isset($id) || !isset($stack) || !isset($name) )
			return new WP_Error('bt_fonts_register_font_invalid_args', __('To register a font, the argument array must include an "id", "stack", and "name".', 'blox'), $args);

		/* Add the font to the stacks first */
		self::$font_stacks[$id] = $stack;

		/* Add the font */
		self::$fonts[$id] = $name;

		return true;

	}


}

blox_register_web_font_provider('BloxTraditionalFonts');