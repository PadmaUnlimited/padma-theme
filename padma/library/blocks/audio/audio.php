<?php
padma_register_block('PadmaAudioBlock', padma_url() . '/library/blocks/audio');

class PadmaAudioBlock extends PadmaBlockAPI {
	
	public $id 				= 'audio';	
	public $name 			= 'Audio';		
	public $options_class 	= 'PadmaAudioBlockOptions';	
	public $fixed_height 	= true;	
	public $html_tag 		= 'div';
	public $attributes 		= array(
									'itemscope' => '',
									'itemtype' => 'http://schema.org/AudioObject'
								);
	public $description 	= 'Display an audio';
	public $categories 		= array('core','media');
	
	protected $show_content_in_grid = false;
	
	function setup_elements() {
		
		$this->register_block_element(array(
			'id' => 'audio',
			'name' => 'Audio',
			'selector' => 'audio',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'animation', 'sizes', 'advanced', 'transition', 'outlines')
		));

		$this->register_block_element(array(
			'id' => 'audio-container',
			'name' => 'Audio container',
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

			$audioHTML .= 'Your browser does not support the audio tag.';
			$audioHTML .= '</audio></div>';

			echo $audioHTML;
		

		} else {

			echo '<div style="margin: 5px;" class="alert alert-yellow"><p>You have not added an audio yet. Please upload and apply an audio.</p></div>';
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
	
	public $tabs = array(
		'general' => 'General'
	);

	public $inputs = array(
		'general' => array(

			'audio-heading' => array(
				'name' => 'audio-heading',
				'type' => 'heading',
				'label' => 'Add an Audio'
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
				'tooltip' => 'Specifies that the audio will start playing as soon as it is ready'
			),

			'controls' => array(
				'name' => 'controls',
				'label' => 'Controls',
				'type' => 'checkbox',
				'default' => false,
				'tooltip' => 'Specifies that audio controls should be displayed (such as a play/pause button etc).'
			),

			'loop' => array(
				'name' => 'loop',
				'label' => 'Loop',
				'type' => 'checkbox',
				'default' => false,
				'tooltip' => 'Specifies that the audio will start over again, every time it is finished'
			),

			'muted' => array(
				'name' => 'muted',
				'label' => 'Muted',
				'type' => 'checkbox',
				'default' => false,
				'tooltip' => 'Specifies that the audio output of the audio should be muted'
			),

			'preload' => array(
				'name' => 'preload',
				'label' => 'Preload',
				'type' => 'select',
				'default' => 'none',
				'options' => array(
					''		=> 'none',
					'auto'		=> 'Auto',
					'metadata'	=> 'Metadata',
				),
				'tooltip' => 'Specifies if and how the author thinks the audio should be loaded when the page loads'
			),


			'position-heading' => array(
				'name' => 'position-heading',
				'type' => 'heading',
				'label' => 'Position Audio'
			),

			'audio-position' => array(
				'name' => 'audio-position',
				'label' => 'Position audio inside container',
				'type' => 'select',
				'tooltip' => 'You can position this audio in relation to the block using the positions provided',
				'default' => 'none',
				'options' => array(
					'' => 'None',
					'top_left' => 'Top Left',
					'top_center' => 'Top Center',
					'top_right' => 'Top Right',
					'center_left' => 'Center Left',
					'center_center' => 'Center Center',
					'center_right' => 'Center Right',
					'bottom_left' => 'Bottom Left',
					'bottom_center' => 'Bottom Center',
					'bottom_right' => 'Bottom Right'
				)
			)

		)
	);
	
}