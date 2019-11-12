<?php

class PadmaSiteLogoBlock extends PadmaBlockAPI {

	public $id;
	public $name;
	public $options_class;
	public $attributes;
	public $description;
	public $categories;

	protected $show_content_in_grid;


	function __construct(){

		$this->id 				= 'site-logo';
		$this->name 			= __('Site Logo','padma');
		$this->options_class 	= 'PadmaSiteLogoBlockOptions';			
		$this->attributes 		= array(
										'itemscope' => '',
										'itemtype' => 'https://schema.org/ImageObject'
									);
		$this->description 	= __('Display custom site logo','padma');
		$this->categories 		= array('core','media');

		$this->show_content_in_grid = false;

	}


	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'site-logo',
			'name' => __('Site Logo','padma'),
			'selector' => 'img.site-logo',			
		));

		$this->register_block_element(array(
			'id' => 'site-logo-img',
			'name' => __('Site Logo image','padma'),
			'selector' => 'img.site-logo img',		
			'states' => array(
				'Shrinked' => 'img.site-logo img.is_shrinked',
			)
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

	public $tabs;
	public $inputs;

	function __construct($block_type_object){

		parent::__construct($block_type_object);

		$this->tabs = array(
			'site-logo-content' => 'Content'
		);

		$this->inputs = array(
		);
	}

	function modify_arguments($args = false) {

		$this->tab_notices['site-logo-content'] = sprintf( __('To set the site custom logo go to <a href="%s" target="_blank">"Appearance > Customize > Site Identity"</a>','padma'), admin_url('customize.php') );

	}

}