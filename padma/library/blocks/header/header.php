<?php

class PadmaHeaderBlock extends PadmaBlockAPI {


	public $id;
	public $name;
	public $options_class;
	public $fixed_height;
	public $html_tag;
	public $attributes;
	public $description;
	public $allow_titles;
	protected $show_content_in_grid;
	public $categories;


	function __construct(){

		$this->id = 'header';	
		$this->name = __('Header','padma');
		$this->options_class = 'PadmaHeaderBlockOptions';	
		$this->fixed_height = true;	
		$this->html_tag = 'header';	
		$this->attributes = array(
			'itemscope' => '',
			'itemtype' => 'http://schema.org/WPHeader'
		);
		$this->description = __('Display your banner, logo, or site title and tagline.  This typically goes at the top of your website.','padma');
		$this->allow_titles = false;	
		$this->show_content_in_grid = true;
		$this->categories = array('core','content');

	}

	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'site-title',
			'name' => __('Site Title','padma'),
			'selector' => 'span.banner a',
			'states' => array(
				'Hover' => 'span.banner a:hover',
				'Clicked' => 'span.banner a:active',
				'Shrinked' => 'span.banner a.is_shrinked',
			)
		));

		$this->register_block_element(array(
			'id' => 'banner-image',
			'name' => __('Banner/Logo Link','padma'),
			'selector' => 'a.banner-image',
			'states' => array(
				'Clicked' => 'a.banner-image:active',
				'Hover' => 'a.banner-image:hover',
				'Shrinked' => 'a.banner-image.is_shrinked',
			)
		));

		$this->register_block_element(array(
			'id' => 'banner-image-img',
			'name' => __('Banner Image','padma'),
			'selector' => 'a.banner-image img',
			'states' => array(
				'Shrinked' => 'a.banner-image img.is_shrinked',
			)
		));

		$this->register_block_element(array(
			'id' => 'site-tagline',
			'name' => __('Site Tagline','padma'),
			'selector' => '.tagline'
		));

	}


	function content($block) {

		//Use header image if there is one	
		if ( $header_image_src = parent::get_setting($block, 'header-image') ) {

			do_action('padma_before_header_link');

			if ( parent::get_setting($block, 'resize-header-image', true) ) {

				$block_width = PadmaBlocksData::get_block_width($block);
				$block_height = PadmaBlocksData::get_block_height($block);

				$header_image_url = padma_resize_image($header_image_src, $block_width, $block_height);

			} else {

				$header_image_url = $header_image_src;

			}

			$link = apply_filters('padma_header_link', home_url() );

			echo '<a href="' . $link . '" class="banner-image"><img src="' . padma_format_url_ssl($header_image_url) . '" alt="' . get_bloginfo('name') . '" /></a>';

			do_action('padma_after_header_link');


		//No image present	
		} else {

			do_action('padma_before_header_link');

			$link = apply_filters('padma_header_link', home_url() );

			echo '<span class="banner" itemprop="headline"><a href="' . $link . '">' . get_bloginfo('name') . '</a></span>';

			do_action('padma_after_header_link');

			if ( !parent::get_setting($block, 'hide-tagline', false) ) {

				if ( (is_front_page() || is_home()) && get_option('show_on_front') != 'page' ) {

					echo '<h1 class="tagline" itemprop="headline">' . get_bloginfo('description') . '</h1>' . "\n";

				} else {

					echo '<span class="tagline" itemprop="description">' . get_bloginfo('description') . '</span>' . "\n";

				}

				do_action('padma_after_tagline');

			}

		}

	}

	
	public static function dynamic_css( $block_id, $block, $original_block = null ) {

		$selector = '#block-' . PadmaBlocksData::get_legacy_id($block);

		/* If this block is a mirror, then pull the settings from the block that's mirroring that way the dimensions are correct */
		if ( is_array( $original_block ) ) {

			$block_id = $original_block['id'];
			$block = $original_block;

			$selector .= '.block-original-' . PadmaBlocksData::get_legacy_id($block);

		}

		
		$css = $selector . ' {
				max-height: 100%;
				transition-property: all;
				transition-duration: 500ms;
				transition-timing-function: ease-out;
			}';
		$css .= $selector . ' img{
				transition-property: all;
				transition-duration: 500ms;
				transition-timing-function: ease-out;
			}';

		return $css;

	}

}


class PadmaHeaderBlockOptions extends PadmaBlockOptionsAPI {

	public $tabs;
	public $inputs;


	function __construct($block_type_object){

		parent::__construct($block_type_object);

		$this->tabs = array(
			'general' => 'General'
		);

		$this->inputs = array(
			'general' => array(
				'header-image' => array(
					'type' => 'image',
					'name' => 'header-image',
					'label' => __('Banner/Logo','padma'),
					'default' => null
				),

				'resize-header-image' => array(
					'name' => 'resize-header-image',
					'label' => __('Automatically Resize Header Image','padma'),
					'type' => 'checkbox',
					'tooltip' => __('If you would like Padma to automatically scale and crop your header image to the correct dimensions, keep this checked.<br /><br /><em><strong>Important:</strong> In order for the image to be resized and cropped it must be uploaded <strong>From Computer</strong>. <strong>NOT</strong> <strong>From URL</strong>.</em>','padma'),
					'default' => true
				),

				'hide-tagline' => array(
					'name' => 'hide-tagline',
					'label' => __('Hide Tagline','padma'),
					'type' => 'checkbox',
					'tooltip' => __('Check this to hide the tagline in your header.  The tagline will sit beneath your site title.<br /><br /><em><strong>Important:</strong> The tagline will <strong>NOT</strong> show if a Header Image is added.</em>','padma'),
					'default' => false
				)
			)
		);
	}

}