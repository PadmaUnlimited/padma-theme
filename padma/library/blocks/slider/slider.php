<?php
padma_register_block('PadmaSliderBlock', padma_url() . '/library/blocks/slider');
class PadmaSliderBlock extends PadmaBlockAPI {
	
	
	public $id = 'slider';
	
	public $name = 'Slider';
		
	public $options_class = 'PadmaSliderBlockOptions';
	
	public $fixed_height = false;

	public $description = 'Create effective responsive image slideshows.';
	
	
	public static function enqueue_action($block_id, $block) {

		$images = parent::get_setting($block, 'images', array());

		wp_enqueue_style('flexslider', padma_url() . '/library/blocks/slider/assets/flexslider.css');	

		//If there are no images or only 1 image, do not load FlexSlider JS.
		if ( count($images) <= 1 )
			return false;

		wp_enqueue_script('flexslider', padma_url() . '/library/blocks/slider/assets/jquery.flexslider-min.js', array('jquery'));

	}
	
	
	public static function dynamic_js($block_id, $block) {
		
		$images = parent::get_setting($block, 'images', array());
			
		//If there are no images or only 1 image, do not load FlexSlider.
		if ( count($images) <= 1 )
			return false;

		return '
jQuery(window).load(function(){
	jQuery(\'#block-' . $block['id'] . ' .flexslider\').flexslider({
	   animation: "' . (parent::get_setting($block, 'animation', 'slide-horizontal') == 'fade' ? 'fade' : 'slide') . '",
	   direction: "' . (parent::get_setting($block, 'animation', 'slide-horizontal') == 'slide-vertical' ? 'vertical' : 'horizontal') . '",
	   slideshow: ' . (parent::get_setting($block, 'slideshow', true) ? 'true' : 'false') . ',
	   slideshowSpeed: ' . (parent::get_setting($block, 'animation-timeout', 6) * 1000) . ',
	   animationSpeed: ' . (parent::get_setting($block, 'animation-speed', 500)) . ', 
	   randomize: false,     
	   controlNav: ' . (parent::get_setting($block, 'show-pager-nav', true) ? 'true' : 'false') . ',
	   directionNav: ' . (parent::get_setting($block, 'show-direction-nav', true) ? 'true' : 'false') . ',
	   randomize: ' . (parent::get_setting($block, 'randomize-order', false) ? 'true' : 'false') . '
	});
});' . "\n";
		
	}

	
	function content($block) {
				
		$images = parent::get_setting($block, 'images', array());

		$block_width = PadmaBlocksData::get_block_width($block);
		$block_height = PadmaBlocksData::get_block_height($block);
			
		$has_images = false;

		foreach ( $images as $image )
			if ( $image['image'] ) {
				$has_images = true;
				break;
			}

		if ( !$has_images ) {

			echo '<div class="alert alert-yellow"><p>There are no images to display.</p></div>';
			
			return;

		}
		
		$no_slide_class = count($images) === 1 ? ' flexslider-no-slide' : '';

		echo '<div class="flexslider' . $no_slide_class . '">';

			/* Put in viewport div for sliders that only have 1 image and don't slide */
			if ( count($images) === 1 )
				echo '<div class="flex-viewport">';

			echo '<ul class="slides">';

			  	foreach ( $images as $image ) {

			  		if ( !$image['image'] )
			  			continue;

			  		$output = array(
			  			'image' => array(
			  				'src' => parent::get_setting($block, 'crop-resize-images', true) ? padma_resize_image($image['image'], $block_width, $block_height) : $image['image'],
			  				'alt' => padma_fix_data_type(padma_get('image-alt', $image)),
			  				'title' => padma_fix_data_type(padma_get('image-title', $image)),
			  				'caption' => padma_fix_data_type(padma_get('image-description', $image))
			  			),

			  			'hyperlink' => array(
			  				'href' => padma_fix_data_type(padma_get('image-hyperlink', $image)),
			  				'target' => padma_fix_data_type(padma_get('image-open-link-in-new-window', $image, false)) ? ' target="_blank"' : null
			  			)
			  		);

			  		echo '<li>';

			  			/* Open hyperlink if user added one for image */
			  			if ( $output['hyperlink']['href'] )
			  				echo '<a href="' . $output['hyperlink']['href'] . '"' . $output['hyperlink']['target'] . '>';

			  			/* Don't forget to display the ACTUAL IMAGE */
			  			echo '<img src="' . $output['image']['src'] . '" alt="' . $output['image']['alt'] . '" title="' . $output['image']['title'] . '" />';

			  			/* Closing tag for hyperlink */
			  			if ( $output['hyperlink']['href'] )
			  				echo '</a>';
			  		
			  			/* Caption */
				  		if ( !empty($output['image']['caption']) )
				  			echo '<p class="flex-caption">' . $output['image']['caption'] . '</p>';

			  		echo '</li>';
			  		
			  	}
		  
		  	echo '</ul>';

		  	/* Put in viewport div for sliders that only have 1 image and don't slide */
		  	if ( count($images) === 1 )
		  		echo '</div>';

		echo '</div>';

	}


	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'slider-container',
			'name' => 'Slider Container',
			'description' => 'Contains Viewport, Paging',
			'selector' => '.flexslider',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow')
		));

		$this->register_block_element(array(
			'id' => 'slider-viewport',
			'name' => 'Slider Viewport',
			'description' => 'Contains Images',
			'selector' => '.flex-viewport',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'overflow')
		));

		$this->register_block_element(array(
			'id' => 'slider-caption',
			'name' => 'Slider Caption',
			'selector' => '.flex-caption',
			'properties' => array('background', 'padding', 'fonts')
		));

		$this->register_block_element(array(
			'id' => 'slider-direction-nav-link',
			'name' => 'Slider Direction Nav Link',
			'selector' => '.flex-direction-nav a',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow')
		));

		$this->register_block_element(array(
			'id' => 'slider-direction-nav-next',
			'name' => 'Slider Direction Next',
			'selector' => '.flex-direction-nav a.flex-next',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow')
		));

		$this->register_block_element(array(
			'id' => 'slider-direction-nav-prev',
			'name' => 'Slider Direction Prev',
			'selector' => '.flex-direction-nav a.flex-prev',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow')
		));

		$this->register_block_element(array(
			'id' => 'slider-paging',
			'name' => 'Slider Paging',
			'selector' => '.flex-control-nav',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow')
		));

		$this->register_block_element(array(
			'id' => 'slider-paging-link',
			'name' => 'Slider Paging Link',
			'selector' => '.flex-control-paging li a',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow'),
			'states' => array(
				'Hover' => '.flex-control-paging li a:hover', 
				'Active' => '.flex-control-paging li a.flex-active'
			)
		));

	}

	
}


class PadmaSliderBlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs = array(
		'slider-images' => 'Slider Images',
		'animation' => 'Animation',
		'ui' => 'User Interface',
		'advanced' => 'Advanced'
	);

	public $inputs = array(
		'slider-images' => array(
			'images' => array(
				'type' => 'repeater',
				'name' => 'images',
				'label' => 'Images',
				'tooltip' => 'Upload the images that you would like to add to the image rotator here.  You can even drag and drop the images to change the order.',
				'inputs' => array(
					array(
						'type' => 'image',
						'name' => 'image',
						'label' => 'Image',
						'default' => null
					),

					array(
						'type' => 'text',
						'name' => 'image-hyperlink',
						'label' => 'Hyperlink',
						'default' => null
					),

					array(
						'type' => 'checkbox',
						'name' => 'image-open-link-in-new-window',
						'label' => 'Open Link in New Window',
						'default' => false
					),

					array(
						'type' => 'text',
						'name' => 'image-description',
						'label' => 'Caption',
						'placeholder' => 'Describe the Image',
						'tooltip' => 'This will be displayed underneath the image.'
					),

					array(
						'type' => 'text',
						'name' => 'image-title',
						'label' => '"title" Attribute',
						'tooltip' => 'This will be used as the "title" attribute for the image.  The title attribute is beneficial for SEO (Search Engine Optimization) and will allow your visitors to move their mouse over the image and read about it.'
					),

					array(
						'type' => 'text',
						'name' => 'image-alt',
						'label' => '"alt" Attribute',
						'tooltip' => 'This will be used as the "alt" attribute for the image.  The alt attribute is <em>hugely</em> beneficial for SEO (Search Engine Optimization) and for general accessibility.'
					)
				),
				'sortable' => true,
				'limit' => false
			),

			'randomize-order' => array(
				'type' => 'checkbox',
				'name' => 'randomize-order',
				'label' => 'Randomize Image Order',
				'default' => false
			),

			'image-sizing-header' => array(
				'type' => 'heading',
				'name' => 'image-sizing-header',
				'label' => 'Image Sizing'
			),

				'crop-resize-images' => array(
					'type' => 'checkbox',
					'name' => 'crop-resize-images',
					'label' => 'Crop and Resize Images',
					'default' => true,
					'tooltip' => 'The Slider block has the ability to automatically resize and crop images to fit in the Slider if the images are too big.  This will improve loading times and make the image fit better in the Slider.<br /><br />If you do not want the Slider block to do this, uncheck this option and the Slider block will insert your original uploaded images into the slider.  <strong>Please note:</strong> Even with this unchecked the images will still be resized with CSS.'
				),

			'content-types-heading' => array(
				'type' => 'heading',
				'name' => 'content-types-heading',
				'label' => 'Other Content Types',
			),

				'content-types-text' => array(
					'type' => 'notice',
					'name' => 'content-types-text',
					'notice' => 'This Slider block is only capable of displaying images.  If you wish to insert more content such as text, videos, etc., we recommend <a href="http://padmatheme.com/go/slidedeck-lite" target="_blank">SlideDeck</a> and <a href="http://padmatheme.com/extend/addon/sliderplus/" target="_blank">SliderPlus</a>.'
				)
		),

		'animation' => array(
			'animation' => array(
				'type' => 'select',
				'name' => 'animation',
				'label' => 'Animation',
				'default' => 'slide-horizontal',
				'options' => array(
					'slide-horizontal' => 'Slide Horizontal',
					'slide-vertical' => 'Slide Vertical',
					'fade' => 'Fade'
				)
			),

			'animation-speed' => array(
				'type' => 'slider',
				'name' => 'animation-speed',
				'label' => 'Animation Speed',
				'default' => 500,
				'slider-min' => 50,
				'slider-max' => 5000,
				'slider-interval' => 10,
				'tooltip' => 'Adjust this to change how long the animation lasts when fading between images.',
				'unit' => 'ms'
			),

			'slideshow' => array(
				'type' => 'checkbox',
				'name' => 'slideshow',
				'label' => 'Automatic Slide Advancement',
				'default' => true,
				'tooltip' => 'Act as a slideshow and automatically move to the next slide.'
			),
			
			'animation-timeout' => array(
				'type' => 'slider',
				'name' => 'animation-timeout',
				'label' => 'Time Between Slides',
				'default' => 6,
				'slider-min' => 1,
				'slider-max' => 20,
				'slider-interval' => 1,
				'tooltip' => 'This is the amount of time each image will stay visible.',
				'unit' => 's'
			)
		),

		'ui' => array(
			'show-pager-nav' => array(
				'type' => 'checkbox',
				'name' => 'show-pager-nav',
				'label' => 'Show Pager Navigation',
				'default' => true,
				'tooltip' => 'Show dots below slider to choose specific slides.'
			),

			'show-direction-nav' => array(
				'type' => 'checkbox',
				'name' => 'show-direction-nav',
				'label' => 'Show Next/Previous Arrows',
				'default' => true,
				'tooltip' => 'Show arrows to advance to the next/previous slides.'
			)
		),

		'advanced' => array(
			'content-types-text' => array(
				'type' => 'notice',
				'name' => 'content-types-text',
				'notice' => 'This Slider block is only capable of displaying images.  If you wish to insert more content such as text, videos, etc., we recommend <a href="http://padmatheme.com/go/slidedeck-lite" target="_blank">SlideDeck</a> and <a href="http://padmatheme.com/extend/addon/sliderplus/" target="_blank">SliderPlus</a>.'
			)
		)
	);
	
}