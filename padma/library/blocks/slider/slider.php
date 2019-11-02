<?php

class PadmaSliderBlock extends PadmaBlockAPI {


	public $id;
	public $name;
	public $options_class;
	public $fixed_height;
	public $description;
	public $categories;


	function __construct(){

		$this->id = 'slider';	
		$this->name = 'Slider';		
		$this->options_class = 'PadmaSliderBlockOptions';	
		$this->fixed_height = false;
		$this->description = __('Create effective responsive image slideshows.','padma');
		$this->categories = array('core','content', 'media');

	}


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

		foreach ( $images as $image ){
			if ( $image['image'] ) {
				$has_images = true;
				break;
			}
		}

		if ( !$has_images ) {

			echo '<div class="alert alert-yellow"><p>' . __('There are no images to display.','padma') . '</p></div>';

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
			'name' => __('Slider Container','padma'),
			'description' => __('Contains Viewport, Paging','padma'),
			'selector' => '.flexslider',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'advanced', 'transition', 'outlines')
		));

		$this->register_block_element(array(
			'id' => 'slider-viewport',
			'name' => __('Slider Viewport','padma'),
			'description' => __('Contains Images','padma'),
			'selector' => '.flex-viewport',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'overflow', 'advanced', 'transition', 'outlines')
		));

		$this->register_block_element(array(
			'id' => 'slider-caption',
			'name' => __('Slider Caption','padma'),
			'selector' => '.flex-caption',
			'properties' => array('background', 'padding', 'fonts')
		));

		$this->register_block_element(array(
			'id' => 'slider-direction-nav-link',
			'name' => __('Slider Direction Nav Link','padma'),
			'selector' => '.flex-direction-nav a',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow')
		));

		$this->register_block_element(array(
			'id' => 'slider-direction-nav-next',
			'name' => __('Slider Direction Next','padma'),
			'selector' => '.flex-direction-nav a.flex-next',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow')
		));

		$this->register_block_element(array(
			'id' => 'slider-direction-nav-prev',
			'name' => __('Slider Direction Prev','padma'),
			'selector' => '.flex-direction-nav a.flex-prev',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow')
		));

		$this->register_block_element(array(
			'id' => 'slider-paging',
			'name' => __('Slider Paging','padma'),
			'selector' => '.flex-control-nav',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow')
		));

		$this->register_block_element(array(
			'id' => 'slider-paging-link',
			'name' => __('Slider Paging Link','padma'),
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

	public $tabs;
	public $inputs;


	function __construct($block_type_object){

		parent::__construct($block_type_object);

		$this->tabs = array(
			'slider-images' => __('Slider Images','padma'),
			'animation' => __('Animation','padma'),
			'ui' => __('User Interface','padma'),
			'advanced' => __('Advanced','padma')
		);

		$this->inputs = array(
			'slider-images' => array(
				'images' => array(
					'type' => 'repeater',
					'name' => 'images',
					'label' => __('Images','padma'),
					'tooltip' => __('Upload the images that you would like to add to the image rotator here.  You can even drag and drop the images to change the order.','padma'),
					'inputs' => array(
						array(
							'type' => 'image',
							'name' => 'image',
							'label' => __('Image','padma'),
							'default' => null
						),

						array(
							'type' => 'text',
							'name' => 'image-hyperlink',
							'label' => __('Hyperlink','padma'),
							'default' => null
						),

						array(
							'type' => 'checkbox',
							'name' => 'image-open-link-in-new-window',
							'label' => __('Open Link in New Window','padma'),
							'default' => false
						),

						array(
							'type' => 'text',
							'name' => 'image-description',
							'label' => __('Caption','padma'),
							'placeholder' => __('Describe the Image','padma'),
							'tooltip' => __('This will be displayed underneath the image.','padma')
						),

						array(
							'type' => 'text',
							'name' => 'image-title',
							'label' => __('"title" Attribute','padma'),
							'tooltip' => __('This will be used as the "title" attribute for the image.  The title attribute is beneficial for SEO (Search Engine Optimization) and will allow your visitors to move their mouse over the image and read about it.','padma')
						),

						array(
							'type' => 'text',
							'name' => 'image-alt',
							'label' => __('"alt" Attribute','padma'),
							'tooltip' => __('This will be used as the "alt" attribute for the image.  The alt attribute is <em>hugely</em> beneficial for SEO (Search Engine Optimization) and for general accessibility.','padma')
						)
					),
					'sortable' => true,
					'limit' => false
				),

				'randomize-order' => array(
					'type' => 'checkbox',
					'name' => 'randomize-order',
					'label' => __('Randomize Image Order','padma'),
					'default' => false
				),

				'image-sizing-header' => array(
					'type' => 'heading',
					'name' => 'image-sizing-header',
					'label' => __('Image Sizing','padma')
				),

					'crop-resize-images' => array(
						'type' => 'checkbox',
						'name' => 'crop-resize-images',
						'label' => __('Crop and Resize Images','padma'),
						'default' => true,
						'tooltip' => __('The Slider block has the ability to automatically resize and crop images to fit in the Slider if the images are too big.  This will improve loading times and make the image fit better in the Slider.<br /><br />If you do not want the Slider block to do this, uncheck this option and the Slider block will insert your original uploaded images into the slider.  <strong>Please note:</strong> Even with this unchecked the images will still be resized with CSS.','padma')
					),

				'content-types-heading' => array(
					'type' => 'heading',
					'name' => 'content-types-heading',
					'label' => __('Other Content Types','padma'),
				),

					'content-types-text' => array(
						'type' => 'notice',
						'name' => 'content-types-text',
						'notice' => __('This Slider block is only capable of displaying images.  If you wish to insert more content such as text, videos, etc., we recommend <a href="http://padmaunlimited.com/go/slidedeck-lite" target="_blank">SlideDeck</a> and <a href="http://padmaunlimited.com/extend/addon/sliderplus/" target="_blank">SliderPlus</a>.','padma')
					)
			),

			'animation' => array(
				'animation' => array(
					'type' => 'select',
					'name' => 'animation',
					'label' => __('Animation','padma'),
					'default' => 'slide-horizontal',
					'options' => array(
						'slide-horizontal' => __('Slide Horizontal','padma'),
						'slide-vertical' => __('Slide Vertical','padma'),
						'fade' => 'Fade'
					)
				),

				'animation-speed' => array(
					'type' => 'slider',
					'name' => 'animation-speed',
					'label' => __('Animation Speed','padma'),
					'default' => 500,
					'slider-min' => 50,
					'slider-max' => 5000,
					'slider-interval' => 10,
					'tooltip' => __('Adjust this to change how long the animation lasts when fading between images.','padma'),
					'unit' => 'ms'
				),

				'slideshow' => array(
					'type' => 'checkbox',
					'name' => 'slideshow',
					'label' => __('Automatic Slide Advancement','padma'),
					'default' => true,
					'tooltip' => __('Act as a slideshow and automatically move to the next slide.','padma')
				),

				'animation-timeout' => array(
					'type' => 'slider',
					'name' => 'animation-timeout',
					'label' => __('Time Between Slides','padma'),
					'default' => 6,
					'slider-min' => 1,
					'slider-max' => 20,
					'slider-interval' => 1,
					'tooltip' => __('This is the amount of time each image will stay visible.','padma'),
					'unit' => 's'
				)
			),

			'ui' => array(
				'show-pager-nav' => array(
					'type' => 'checkbox',
					'name' => 'show-pager-nav',
					'label' => __('Show Pager Navigation','padma'),
					'default' => true,
					'tooltip' => __('Show dots below slider to choose specific slides.','padma')
				),

				'show-direction-nav' => array(
					'type' => 'checkbox',
					'name' => 'show-direction-nav',
					'label' => __('Show Next/Previous Arrows','padma'),
					'default' => true,
					'tooltip' => __('Show arrows to advance to the next/previous slides.','padma')
				)
			),

			'advanced' => array(
				'content-types-text' => array(
					'type' => 'notice',
					'name' => 'content-types-text',
					'notice' => __('This Slider block is only capable of displaying images.  If you wish to insert more content such as text, videos, etc., we recommend <a href="http://padmaunlimited.com/go/slidedeck-lite" target="_blank">SlideDeck</a> and <a href="http://padmaunlimited.com/extend/addon/sliderplus/" target="_blank">SliderPlus</a>.','padma')
				)
			)
		);
	}

}