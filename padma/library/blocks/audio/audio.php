<?php

class PadmaAudioBlock extends PadmaBlockAPI {
	
	public $id;
	public $name;
	public $options_class;
	public $fixed_height;
	public $html_tag;
	public $attributes;
	public $description;
	public $categories;	
	protected $show_content_in_grid;


	public function __construct(){

		$this->id 				= 'audio';	
		$this->name 			= 'Audio';		
		$this->options_class 	= 'PadmaAudioBlockOptions';	
		$this->fixed_height 	= true;	
		$this->html_tag 		= 'div';
		$this->attributes 		= array(
										'itemscope' => '',
										'itemtype' => 'http://schema.org/AudioObject'
									);
		$this->description 	= __('Display an audio','padma');
		$this->categories 	= array('core','media');
		
		$this->show_content_in_grid = false;

	}
	
	function setup_elements() {
		
		$this->register_block_element(array(
			'id' => 'audio',
			'name' => 'Audio',
			'selector' => 'audio',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'animation', 'sizes', 'advanced', 'transition', 'outlines')
		));

		$this->register_block_element(array(
			'id' => 'audio-container',
			'name' => __('Audio container','padma'),
			'selector' => 'div.audio'
		));

		
	}

	public static function dynamic_css($block_id, $block = false) {

		if ( !$block )
			$block = PadmaBlocksData::get_block($block_id);

		if ( !$position = parent::get_setting($block, 'audio-position') )
			return;

		$position_properties = array(
			'top_left' => 'left: 0; top: 0;',
			'top_center' => 'left: 0; top: 0; right: 0;',
			'top_right' => 'top: 0; right: 0;',

			'center_center' => 'bottom: 0; left: 0; top: 0; right: 0;',
			'center_left' => 'bottom: 0; left: 0; top: 0;',
			'center_right' => 'bottom: 0; top: 0; right: 0;',
			
			'bottom_left' => 'bottom: 0; left: 0;',
			'bottom_center' => 'bottom: 0; left: 0; right: 0;',
			'bottom_right' => 'bottom: 0;right: 0;'
		);

		$position_fragments = explode('_', $position);
		$position_horizontal = $position_fragments[1];

		$css = '
			#block-' . $block['id'] . ' .block-content { position: relative; text-align: ' . $position_horizontal . '; }
			#block-' . $block['id'] . ' div.audio audio {
				margin: auto;
			    position: absolute;  
			    ' . padma_get($position, $position_properties) . '
			}
		';

		return $css;
		
	}

	public static function dynamic_js($block_id, $block = false) {

		if ( !$block )
			$block = PadmaBlocksData::get_block($block_id);


		if(parent::get_setting($block, 'width-dynamic') || parent::get_setting($block, 'height-dynamic')){
			
			$js 		= "jQuery(document).ready(function() {";
			$js_resize 	= "jQuery( window ).on( 'orientationchange resize', function( event ) {";
			$js_load 	= "";
			
			if(parent::get_setting($block, 'width-dynamic')){
				$js_resize 	.= "jQuery( 'div#block-". $block_id ." audio' ).attr('width',window.innerWidth);";
				$js_load  	.= "if(window.innerWidth < ".$block['settings']['width']."){
					jQuery( 'div#block-". $block_id ." audio' ).attr('width',window.innerWidth);
				}";
			}

			if(parent::get_setting($block, 'height-dynamic')){
				$js_resize .= "jQuery( 'div#block-". $block_id ." audio' ).attr('height',window.innerHeight);";
				$js_load  	.= "if(window.innerHeight < ".$block['settings']['height']."){
					jQuery( 'div#block-". $block_id ." audio' ).attr('height',window.innerHeight);
				}";
			}
			$js_resize .= "});";

			$js .= $js_resize;						
			$js .= $js_load;						
			$js .= "});";
		}

		return $js;

	}
	
	function content($block) {
		
		//Display audio if there is one
		if (parent::get_setting($block, 'audio-mp3')||parent::get_setting($block, 'audio-ogg')||parent::get_setting($block, 'audio-wav') ) {

			$audio_mp3 	= parent::get_setting($block, 'audio-mp3');
			$audio_ogg 	= parent::get_setting($block, 'audio-ogg');
			$audio_wav = parent::get_setting($block, 'audio-wav');

			$audioHTML = '<div class="audio"><audio ';

			if(parent::get_setting($block, 'autoplay'))
				$audioHTML .= ' autoplay';

			if(parent::get_setting($block, 'loop'))
				$audioHTML .= ' loop';

			switch (parent::get_setting($block, 'preload')) {
				
				case 'none':
					$audioHTML .= ' preload="none"';
					break;

				case 'metadata':
					$audioHTML .= ' preload="metadata"';
					break;

				case 'auto':
					$audioHTML .= ' preload="auto"';
					break;
				
				default:					
					break;
			}

			if(parent::get_setting($block, 'controls'))
				$audioHTML .= ' controls';

			if(parent::get_setting($block, 'muted'))
				$audioHTML .= ' muted';
						
			$audioHTML .= '>';

			if(parent::get_setting($block, 'audio-mp3'))
				$audioHTML .= '<source src="' . padma_format_url_ssl($audio_mp3) . '" type="audio/mp3">';

			if(parent::get_setting($block, 'audio-ogg'))
				$audioHTML .= '<source src="' . padma_format_url_ssl($audio_ogg) . '" type="audio/ogg">';

			if(parent::get_setting($block, 'audio-wav'))
				$audioHTML .= '<source src="' . padma_format_url_ssl($audio_ogg) . '" type="audio/wav">';

			$audioHTML .= __('Your browser does not support the audio tag.','padma');
			$audioHTML .= '</audio></div>';

			echo $audioHTML;
		

		} else {

			echo '<div style="margin: 5px;" class="alert alert-yellow"><p>' . __('You have not added an audio yet. Please upload and apply an audio.','padma') . '</p></div>';
		}
		
		/* Output position styling for Grid mode */
			if ( padma_get('ve-live-content-query', $block) && padma_post('mode') == 'grid' ) {
				echo '<style type="text/css">';
					echo self::dynamic_css(false, $block);
				echo '</style>';
			}


	}
	
}

class PadmaAudioBlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs;
	public $inputs;

	function __construct(){

		$this->tabs = array(
			'general' => 'General'
		);

		$this->inputs = array(
			
			'general' => array(

				'audio-heading' => array(
					'name' => 'audio-heading',
					'type' => 'heading',
					'label' => __('Add an Audio','padma')
				),

				'audio-mp3' => array(
					'type' => 'audio',
					'name' => 'audio-mp3',
					'label' => 'Audio MP3',
					'default' => null
				),

				'audio-ogg' => array(
					'type' => 'audio',
					'name' => 'audio-ogg',
					'label' => 'Audio OGG',
					'default' => null
				),

				'audio-wav' => array(
					'type' => 'audio',
					'name' => 'audio-wav',
					'label' => 'Audio WAV',
					'default' => null
				),

				'autoplay' => array(
					'name' => 'autoplay',
					'label' => 'Autoplay',
					'type' => 'checkbox',
					'default' => false,
					'tooltip' => __('Specifies that the audio will start playing as soon as it is ready','padma')
				),

				'controls' => array(
					'name' => 'controls',
					'label' =>  __('Controls','padma'),
					'type' => 'checkbox',
					'default' => false,
					'tooltip' => __('Specifies that audio controls should be displayed (such as a play/pause button etc).','padma')
				),

				'loop' => array(
					'name' => 'loop',
					'label' => __('Loop','padma'),
					'type' => 'checkbox',
					'default' => false,
					'tooltip' => __('Specifies that the audio will start over again, every time it is finished','padma')
				),

				'muted' => array(
					'name' => 'muted',
					'label' => __('Muted','padma'),
					'type' => 'checkbox',
					'default' => false,
					'tooltip' => __('Specifies that the audio output of the audio should be muted','padma')
				),

				'preload' => array(
					'name' => 'preload',
					'label' => __('Preload','padma'),
					'type' => 'select',
					'default' => 'none',
					'options' => array(
						''		=> 'none',
						'auto'		=> 'Auto',
						'metadata'	=> 'Metadata',
					),
					'tooltip' => __('Specifies if and how the author thinks the audio should be loaded when the page loads','padma')
				),


				'position-heading' => array(
					'name' => 'position-heading',
					'type' => 'heading',
					'label' => __('Position Audio','padma')
				),

				'audio-position' => array(
					'name' => 'audio-position',
					'label' => __('Position audio inside container','padma'),
					'type' => 'select',
					'tooltip' => __('You can position this audio in relation to the block using the positions provided','padma'),
					'default' => 'none',
					'options' => array(
						'' => 'None',
						'top_left' => __('Top Left','padma'),
						'top_center' => __('Top Center','padma'),
						'top_right' => __('Top Right','padma'),
						'center_left' => __('Center Left','padma'),
						'center_center' => __('Center Center','padma'),
						'center_right' => __('Center Right','padma'),
						'bottom_left' => __('Bottom Left','padma'),
						'bottom_center' => __('Bottom Center','padma'),
						'bottom_right' => __('Bottom Right','padma')
					)
				)

			)
		);

	}
	
}