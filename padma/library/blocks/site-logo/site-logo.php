<?php

class PadmaSiteLogoBlock extends PadmaBlockAPI {
	
	public $id 				= 'site-logo';	
	public $name 			= 'Site Logo';		
	public $options_class 	= 'PadmaSiteLogoBlockOptions';			
	public $attributes 		= array(
									'itemscope' => '',
									'itemtype' => 'https://schema.org/ImageObject'
								);
	public $description 	= 'Display custom site logo';
	public $categories 		= array('core','media');
	
	protected $show_content_in_grid = false;
	
	function setup_elements() {
		
		$this->register_block_element(array(
			'id' => 'site-logo',
			'name' => 'Site Logo',
			'selector' => 'img.site-logo',			
		));

		
	}

	public static function dynamic_css($block_id, $block = false) {
				
	}

	public static function dynamic_js($block_id, $block = false) {
		
	}
	
	function content($block) {
		
		$blog_id = (is_multisite()) ? get_current_blog_id(): 0;
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$site_image = wp_get_attachment_image_src( $custom_logo_id , 'full' );

		echo '<a href="' . home_url() . '" class="site-logo-link"><img class="site-logo" src="'.$site_image[0].'" alt="' . get_bloginfo('name') . '" /></a>';

	}
	
}


class PadmaSiteLogoBlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs = array(
		'site-logo-content' => 'Content'
	);

	public $inputs = array(
	);

	function modify_arguments($args = false) {

		$this->tab_notices['site-logo-content'] = 'To set the site custom logo go to <a href="' . admin_url('customize.php') . '" target="_blank">"Appearance > Customize > Site Identity"</a>';

	}
	
}