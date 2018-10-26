<?php
padma_register_block('PadmaImageBlock', padma_url() . '/library/blocks/image');

class PadmaImageBlock extends PadmaBlockAPI {
	
	public $id 				= 'image';	
	public $name 			= 'Image';		
	public $options_class 	= 'PadmaImageBlockOptions';	
	public $fixed_height 	= true;	
	public $html_tag 		= 'figure';
	public $attributes 		= array(
									'itemscope' => '',
									'itemtype' => 'http://schema.org/ImageObject'
								);
	public $description 	= 'Display an image';
	
	protected $show_content_in_grid = true;
	
	function setup_elements() {
		
		$this->register_block_element(array(
			'id' => 'image',
			'name' => 'Image',
			'selector' => 'img',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'animation', 'sizes')
		));

		$this->register_block_element(array(
			'id' => 'image-link',
			'name' => 'Image Link',
			'selector' => 'a img',
			'states' => array(
				'Hover' => 'a:hover img',
				'Clicked' => 'a:active img'
			)
		));
		
	}

	public static function dynamic_css($block_id, $block = false) {

		if ( !$block )
			$block = PadmaBlocksData::get_block($block_id);

		if ( !$position = parent::get_setting($block, 'image-position') )
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
			#block-' . $block['id'] . ' img {
				margin: auto;
			    position: absolute;  
			    ' . padma_get($position, $position_properties) . '
			}
		';

		return $css;
		
	}
	
	function content($block) {
		
		//Display image if there is one
		if ( $image_src = parent::get_setting($block, 'image') ) {
		
			$url = parent::get_setting($block, 'link-url');
			$alt = parent::get_setting($block, 'image-alt');
			$title = parent::get_setting($block, 'image-title');
			$target = parent::get_setting($block, 'link-target', false) ? $target = 'target="_blank"' : '';

			if ( parent::get_setting($block, 'resize-image', true) ) {
				
				$block_width = PadmaBlocksData::get_block_width($block);
				$block_height = PadmaBlocksData::get_block_height($block);
				
				$image_url = padma_resize_image($image_src, $block_width, $block_height);
				
			} else {
				
				$image_url = $image_src;
				
			}

			if ( $image_src = parent::get_setting($block, 'link-image', false) ) {

				echo '<a href="' . $url . '" class="image" '.$target.'><img src="' . padma_format_url_ssl($image_url) . '" alt="' . $alt . '" title="' . $title . '" itemprop="contentURL"/></a>';

			} else {

				echo '<img src="' . padma_format_url_ssl($image_url) . '" alt="' . $alt . '" title="' . $title . '" itemprop="contentURL"/>';

			}
			
		} else {

			echo '<div style="margin: 5px;" class="alert alert-yellow"><p>You have not added an image yet. Please upload and apply an image.</p></div>';
		}
		
		/* Output position styling for Grid mode */
			if ( padma_get('ve-live-content-query', $block) && padma_post('mode') == 'grid' ) {
				echo '<style type="text/css">';
					echo self::dynamic_css(false, $block);
				echo '</style>';
			}


	}
	
}


class PadmaImageBlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs = array(
		'general' => 'General'
	);

	public $inputs = array(
		'general' => array(

			'image-heading' => array(
				'name' => 'image-heading',
				'type' => 'heading',
				'label' => 'Add an Image'
			),

			'image' => array(
				'type' => 'image',
				'name' => 'image',
				'label' => 'Image',
				'default' => null
			),
			
			'resize-image' => array(
				'name' => 'resize-image',
				'label' => 'Automatically Resize Image',
				'type' => 'checkbox',
				'tooltip' => 'If you would like Padma to automatically scale and crop the image to the blocks dimensions, keep this checked.<br /><br /><em><strong>Important:</strong> In order for the image to be resized and cropped it must be uploaded <strong>From Computer</strong>. <strong>NOT</strong> <strong>From URL</strong>.</em>',
				'default' => true
			),

			'image-title' => array(
				'name' => 'image-title',
				'label' => 'Image Title',
				'type' => 'text',
				'tooltip' => 'This will be used as the "title" attribute for the image.  The title attribute is beneficial for SEO (Search Engine Optimization) and will allow your visitors to move their mouse over the image and read about it.',
			),

			'image-alt' => array(
				'name' => 'image-alt',
				'label' => 'Image Alternate Text',
				'type' => 'text',
				'tooltip' => 'This will be used as the "alt" attribute for the image.  The alt attribute is <em>hugely</em> beneficial for SEO (Search Engine Optimization) and for general accessibility.',
			),

			'link-heading' => array(
				'name' => 'link-heading',
				'type' => 'heading',
				'label' => 'Link Image'
			),

			'link-image' => array(
				'name' => 'link-image',
				'label' => 'Link the image?',
				'type' => 'checkbox',
				'tooltip' => 'If you would like to link the image to a url activate this setting. Must add http:// first',
				'default' => false,
				'toggle' => array(
					'true' => array(
						'show' => array(
							'#input-link-url',
							'#input-link-target'
						)
					),
					'false' => array(
						'hide' => array(
							'#input-link-url',
							'#input-link-target'
						)
					)
				)
			),

			'link-url' => array(
				'name' => 'link-url',
				'label' => 'Link image URL?',
				'type' => 'text',
				'tooltip' => 'Set the URL for the image to link to'
			),

			'link-target' => array(
				'name' => 'link-target',
				'label' => 'Open in a new window?',
				'type' => 'checkbox',
				'tooltip' => 'If you would like to open the link in a new window check this option',
				'default' => false,
			),

			'position-heading' => array(
				'name' => 'position-heading',
				'type' => 'heading',
				'label' => 'Position Image'
			),

			'image-position' => array(
				'name' => 'image-position',
				'label' => 'Position image inside container',
				'type' => 'select',
				'tooltip' => 'You can position this image in relation to the block using the positions provided',
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