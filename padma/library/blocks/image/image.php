<?php

class PadmaImageBlock extends PadmaBlockAPI {

	public $id;
	public $name;
	public $options_class;
	public $fixed_height;
	public $html_tag;
	public $attributes;
	public $description;
	public $categories;	
	protected $show_content_in_grid;


	function __construct(){

		$this->id 				= 'image';
		$this->name 			= __('Image','padma');
		$this->options_class 	= 'PadmaImageBlockOptions';	
		$this->fixed_height 	= true;	
		$this->html_tag 		= 'figure';
		$this->attributes 		= array(
										'itemscope' => '',
										'itemtype' => 'http://schema.org/ImageObject'
									);
		$this->description 	= __('Display an image','padma');
		$this->categories 		= array('core','media');		
		$this->show_content_in_grid = true;

	}

	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'image',
			'name' => __('Image','padma'),
			'selector' => 'img',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'animation', 'sizes', 'advanced', 'transition', 'outlines', 'filter')
		));

		$this->register_block_element(array(
			'id' => 'image-link',
			'name' => __('Image Link','padma'),
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

			echo '<div style="margin: 5px;" class="alert alert-yellow"><p>' . __('You have not added an image yet. Please upload and apply an image.','padma') . '</p></div>';
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


	public $tabs;
	public $inputs;


	function __construct(){
		$this->tabs = array(
			'general' => 'General'
		);

		$this->inputs = array(
			'general' => array(

				'image-heading' => array(
					'name' => 'image-heading',
					'type' => 'heading',
					'label' => __('Add an Image','padma')
				),

				'image' => array(
					'type' => 'image',
					'name' => 'image',
					'label' => __('Image','padma'),
					'default' => null
				),

				'resize-image' => array(
					'name' => 'resize-image',
					'label' => __('Automatically Resize Image','padma'),
					'type' => 'checkbox',
					'tooltip' => __('If you would like Padma to automatically scale and crop the image to the blocks dimensions, keep this checked.<br /><br /><em><strong>Important:</strong> In order for the image to be resized and cropped it must be uploaded <strong>From Computer</strong>. <strong>NOT</strong> <strong>From URL</strong>.</em>','padma'),
					'default' => true
				),

				'image-title' => array(
					'name' => 'image-title',
					'label' => 'Image Title',
					'type' => 'text',
					'tooltip' => __('This will be used as the "title" attribute for the image.  The title attribute is beneficial for SEO (Search Engine Optimization) and will allow your visitors to move their mouse over the image and read about it.','padma'),
				),

				'image-alt' => array(
					'name' => 'image-alt',
					'label' => 'Image Alternate Text',
					'type' => 'text',
					'tooltip' => __('This will be used as the "alt" attribute for the image.  The alt attribute is <em>hugely</em> beneficial for SEO (Search Engine Optimization) and for general accessibility.','padma'),
				),

				'link-heading' => array(
					'name' => 'link-heading',
					'type' => 'heading',
					'label' => __('Link Image','padma')
				),

				'link-image' => array(
					'name' => 'link-image',
					'label' => __('Link the image?','padma'),
					'type' => 'checkbox',
					'tooltip' => __('If you would like to link the image to a url activate this setting. Must add http:// first','padma'),
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
					'label' => __('Link image URL?','padma'),
					'type' => 'text',
					'tooltip' => __('Set the URL for the image to link to','padma')
				),

				'link-target' => array(
					'name' => 'link-target',
					'label' => __('Open in a new window?','padma'),
					'type' => 'checkbox',
					'tooltip' => __('If you would like to open the link in a new window check this option','padma'),
					'default' => false,
				),

				'position-heading' => array(
					'name' => 'position-heading',
					'type' => 'heading',
					'label' => __('Position Image','padma')
				),

				'image-position' => array(
					'name' => 'image-position',
					'label' => __('Position image inside container','padma'),
					'type' => 'select',
					'tooltip' => __('You can position this image in relation to the block using the positions provided','padma'),
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