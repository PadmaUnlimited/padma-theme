<?php
padma_register_block('PadmaHeaderBlock', padma_url() . '/library/blocks/header');

class PadmaHeaderBlock extends PadmaBlockAPI {
	
	
	public $id = 'header';
	
	public $name = 'Header';
		
	public $options_class = 'PadmaHeaderBlockOptions';
	
	public $fixed_height = true;
	
	public $html_tag = 'header';
	
	public $attributes = array(
		'itemscope' => '',
		'itemtype' => 'http://schema.org/WPHeader'
	);		

	public $description = 'Display your banner, logo, or site title and tagline.  This typically goes at the top of your website.';

	public $allow_titles = false;
	
	protected $show_content_in_grid = true;
	
	
	function setup_elements() {
		
		$this->register_block_element(array(
			'id' => 'site-title',
			'name' => 'Site Title',
			'selector' => 'span.banner a',
			'states' => array(
				'Hover' => 'span.banner a:hover',
				'Clicked' => 'span.banner a:active'
			)
		));

		$this->register_block_element(array(
			'id' => 'banner-image',
			'name' => 'Banner/Logo Link',
			'selector' => 'a.banner-image',
			'states' => array(
				'Clicked' => 'a.banner-image:active',
				'Hover' => 'a.banner-image:hover'
			)
		));

		$this->register_block_element(array(
			'id' => 'banner-image-img',
			'name' => 'Banner Image',
			'selector' => 'a.banner-image img'
		));

		$this->register_block_element(array(
			'id' => 'site-tagline',
			'name' => 'Site Tagline',
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

			echo '<a href="' . home_url() . '" class="banner-image"><img src="' . padma_format_url_ssl($header_image_url) . '" alt="' . get_bloginfo('name') . '" /></a>';
			
			do_action('padma_after_header_link');
			
			
		//No image present	
		} else {
			
			do_action('padma_before_header_link');
			
			echo '<span class="banner" itemprop="headline"><a href="' . home_url() . '">' . get_bloginfo('name') . '</a></span>';
			
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
	
}


class PadmaHeaderBlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs = array(
		'general' => 'General'
	);

	public $inputs = array(
		'general' => array(
			'header-image' => array(
				'type' => 'image',
				'name' => 'header-image',
				'label' => 'Banner/Logo',
				'default' => null
			),
			
			'resize-header-image' => array(
				'name' => 'resize-header-image',
				'label' => 'Automatically Resize Header Image',
				'type' => 'checkbox',
				'tooltip' => 'If you would like Padma to automatically scale and crop your header image to the correct dimensions, keep this checked.<br /><br /><em><strong>Important:</strong> In order for the image to be resized and cropped it must be uploaded <strong>From Computer</strong>. <strong>NOT</strong> <strong>From URL</strong>.</em>',
				'default' => true
			),
			
			'hide-tagline' => array(
				'name' => 'hide-tagline',
				'label' => 'Hide Tagline',
				'type' => 'checkbox',
				'tooltip' => 'Check this to hide the tagline in your header.  The tagline will sit beneath your site title.<br /><br /><em><strong>Important:</strong> The tagline will <strong>NOT</strong> show if a Header Image is added.</em>',
				'default' => false
			)
		)
	);
	
}