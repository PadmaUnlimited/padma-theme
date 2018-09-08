<?php
padma_register_block('PadmaVideoBlock', padma_url() . '/library/blocks/video');

class PadmaVideoBlock extends PadmaBlockAPI {
	
	public $id 				= 'video';	
	public $name 			= 'Video';		
	public $options_class 	= 'PadmaVideoBlockOptions';	
	public $fixed_height 	= true;	
	public $html_tag 		= 'figure';
	public $attributes 		= array(
									'itemscope' => '',
									'itemtype' => 'http://schema.org/VideoObject'
								);
	public $description 	= 'Display an video';
	
	protected $show_content_in_grid = true;
	
	function setup_elements() {
		
		$this->register_block_element(array(
			'id' => 'video',
			'name' => 'Video',
			'selector' => 'video'
		));

		$this->register_block_element(array(
			'id' => 'video-container',
			'name' => 'Video container',
			'selector' => 'div.video'
		));

		
	}

	public static function dynamic_css($block_id, $block = false) {

		if ( !$block )
			$block = PadmaBlocksData::get_block($block_id);

		if ( !$position = parent::get_setting($block, 'video-position') )
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
			#block-' . $block['id'] . ' div.video video {
				margin: auto;
			    position: absolute;  
			    ' . padma_get($position, $position_properties) . '
			}
		';

		return $css;
		
	}
	
	function content($block) {
		
		//Display video if there is one
		if (parent::get_setting($block, 'video-mp4')||parent::get_setting($block, 'video-ogg') ) {

			$video_mp4 = parent::get_setting($block, 'video-mp4');
			$video_ogg = parent::get_setting($block, 'video-ogg');

			$videoHTML = '<div class="video"><video ';

			if(parent::get_setting($block, 'autoplay'))
				$videoHTML .= ' autoplay';

			if(parent::get_setting($block, 'controls'))
				$videoHTML .= ' controls';

			if(parent::get_setting($block, 'muted'))
				$videoHTML .= ' muted';

			if(parent::get_setting($block, 'poster'))
				$videoHTML .= ' poster="' . padma_format_url_ssl(parent::get_setting($block, 'poster')) . '"';

			if(parent::get_setting($block, 'width'))
				$videoHTML .= 'width="'.parent::get_setting($block, 'width').'"';

			if(parent::get_setting($block, 'height'))
				$videoHTML .= 'height="'.parent::get_setting($block, 'height').'"';

			
			$videoHTML .= '>';

			if(parent::get_setting($block, 'video-mp4'))
				$videoHTML .= '<source src="' . padma_format_url_ssl($video_mp4) . '" type="video/mp4">';


			if(parent::get_setting($block, 'video-ogg'))
				$videoHTML .= '<source src="' . padma_format_url_ssl($video_ogg) . '" type="video/ogg">';

			$videoHTML .= 'Your browser does not support the video tag.';
			$videoHTML .= '</video></div>';

			echo $videoHTML;
		

		} else {

			echo '<div style="margin: 5px;" class="alert alert-yellow"><p>You have not added an video yet. Please upload and apply an video.</p></div>';
		}
		
		/* Output position styling for Grid mode */
			if ( padma_get('ve-live-content-query', $block) && padma_post('mode') == 'grid' ) {
				echo '<style type="text/css">';
					echo self::dynamic_css(false, $block);
				echo '</style>';
			}


	}
	
}


class PadmaVideoBlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs = array(
		'general' => 'General'
	);

	public $inputs = array(
		'general' => array(

			'video-heading' => array(
				'name' => 'video-heading',
				'type' => 'heading',
				'label' => 'Add an Video'
			),

			'video-mp4' => array(
				'type' => 'video',
				'name' => 'video-mp4',
				'label' => 'Video MP4',
				'default' => null
			),

			'video-ogg' => array(
				'type' => 'video',
				'name' => 'video-ogg',
				'label' => 'Video OGG',
				'default' => null
			),

			'autoplay' => array(
				'name' => 'autoplay',
				'label' => 'Autoplay',
				'type' => 'checkbox',
				'default' => true,
				'tooltip' => 'Specifies that the video will start playing as soon as it is ready'
			),

			'controls' => array(
				'name' => 'controls',
				'label' => 'Controls',
				'type' => 'checkbox',
				'default' => true,
				'tooltip' => 'Specifies that video controls should be displayed (such as a play/pause button etc).'
			),

			'muted' => array(
				'name' => 'muted',
				'label' => 'Muted',
				'type' => 'checkbox',
				'default' => false,
				'tooltip' => 'Specifies that the audio output of the video should be muted'
			),

			'poster' => array(
				'name' => 'poster',
				'label' => 'Poster URL',
				'type' => 'image',
				'tooltip' => 'Specifies an image to be shown while the video is downloading, or until the user hits the play button'
			),

			'width' => array(
				'name' => 'width',
				'label' => 'Width',
				'type' => 'integer',
				'tooltip' => 'Sets the width of the video player'
			),

			'height' => array(
				'name' => 'height',
				'label' => 'Height',
				'type' => 'integer',
				'tooltip' => 'Sets the height of the video player'
			),

			'position-heading' => array(
				'name' => 'position-heading',
				'type' => 'heading',
				'label' => 'Position Video'
			),

			'video-position' => array(
				'name' => 'video-position',
				'label' => 'Position video inside container',
				'type' => 'select',
				'tooltip' => 'You can position this video in relation to the block using the positions provided',
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