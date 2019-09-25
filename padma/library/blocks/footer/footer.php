<?php

class PadmaFooterBlock extends PadmaBlockAPI {
	
	
	public $id;
	public $name;
	public $options_class;
	public $html_tag;
	public $attributes;
	public $description;
	public $allow_titles;
	protected $show_content_in_grid;
	public $categories;
	public $inline_editable;
	

	function __construct(){

		$this->id = 'footer';	
		$this->name = __('Footer','padma');
		$this->options_class = 'PadmaFooterBlockOptions';	
		$this->html_tag = 'footer';	
		$this->attributes = array(
			'itemscope' => '',
			'itemtype' => 'http://schema.org/WPFooter'
		);
		$this->description = __('This typically goes at the bottom of your site and will display the copyright, and miscellaneous links.','padma');
		$this->allow_titles = false;	
		$this->show_content_in_grid = true;
		$this->categories 	= array('core','content');
		$this->inline_editable = array('custom-copyright');

	}

	
	function setup_elements() {
		
		$this->register_block_element(array(
			'id' => 'copyright',
			'name' => __('Copyright','padma'),
			'selector' => 'p.copyright',
			'properties' => array('fonts', 'animation')
		));
		
		$this->register_block_element(array(
			'id' => 'padma-attribution',
			'name' => __('Padma Attribution','padma'),
			'selector' => 'p.footer-padma-link',
			'properties' => array('fonts', 'animation')
		));
		
		$this->register_block_element(array(
			'id' => 'administration-panel',
			'name' => __('Administration Panel','padma'),
			'selector' => 'a.footer-admin-link',
			'properties' => array('fonts', 'animation')
		));
		
		$this->register_block_element(array(
			'id' => 'go-to-top',
			'name' => __('Go To Top Link','padma'),
			'selector' => 'a.footer-go-to-top-link',
			'states' => array(
				'Hover' => 'a.footer-go-to-top-link:hover'
			)
		));
		
		$this->register_block_element(array(
			'id' => 'responsive-grid-link',
			'name' => __('Responsive Grid Toggle Link','padma'),
			'selector' => 'a.footer-responsive-grid-link',
			'properties' => array('fonts', 'animation')
		));
		
	}
	
	
	function content($block) {
		
		//Add action for footer
		do_action('padma_before_footer');
		
		echo '<div class="footer-container">';
		
		echo '<div class="footer">';
		
		do_action('padma_footer_open');

		//Padma Attribution
		if ( parent::get_setting($block, 'hide-padma-attribution', false) == false )
			self::show_padma_link();
		
		//Go To Top Link
		if ( parent::get_setting($block, 'show-go-to-top-link', true) == true ){
			$go_to_top_text = parent::get_setting($block, 'custom-go-to-top-text', 'Go To Top');

			if( ! $go_to_top_text )
				$go_to_top_text = 'Go To Top';

			self::show_go_to_top_link($go_to_top_text);
		}
		
		//Admin Link
		if ( parent::get_setting($block, 'show-admin-link', true) == true )
			self::show_admin_link();
		 		
		//Copyright
		if ( parent::get_setting($block, 'show-copyright', true) == true )
			self::show_copyright(parent::get_setting($block, 'custom-copyright'));
		
		// Show or hide "Show full site" on mobile
		if ( parent::get_setting($block, 'show-responsive-grid-link', true) == false )
			self::show_responsive_grid_toggle_link();
		
		do_action('padma_footer_close');
		
		echo '</div>';
		
		echo '</div>';
		
		do_action('padma_after_footer');
		
	}
	
	
	/**
	 * Displays an admin link or admin login.
	 * 
	 * @uses PadmaOption::get()
	 *
	 * @return void
	 **/
	public static function show_admin_link() {

		if ( is_user_logged_in() )
		    echo apply_filters('padma_admin_link', '<a href="' . admin_url() . '" class="footer-right footer-admin-link footer-link">'.__('Administration Panel', 'padma') . '</a>');
		else
		    echo apply_filters('padma_admin_link', '<a href="' . admin_url() . '" class="footer-right footer-admin-link footer-link">'.__('Administration Login', 'padma') . '</a>');

	}
	
	
	/**
	 * Echos the Unlimited by Padma.
	 * 
	 * @uses PadmaOption::get()
	 *
	 * @param string $text The name of the program to be displayed.  Defaults to Padma (obviously).
	 * 
	 * @return mixed
	 **/
	public static function show_padma_link() {

		$padma_location = 'https://www.padmaunlimited.com/';
		echo apply_filters('padma_link', '<p class="footer-left footer-padma-link footer-link">' . ' <a href="' . $padma_location . '" title="Unlimited by Padma">' . __('Unlimited by Padma', 'padma') . '</a></p>' );
		
	}


	/**
	 * Shows a simple copyright paragraph.
	 *
	 * @return mixed
	 **/
	public static function show_copyright($custom_copyright = false) {

		$default_copyright = __('Copyright', 'padma') . ' &copy; ' . date('Y') . ' ' . get_bloginfo('name');

		$custom_copyright = preg_replace( '/%Y%/', date('Y'), $custom_copyright );  //Change %Y% for current year

		$copyright = $custom_copyright ? $custom_copyright : $default_copyright;

		echo apply_filters('padma_copyright', padma_parse_php('<p class="copyright footer-copyright custom-copyright">' . $copyright . '</p>'));

	}


	/**
	 * Shows a simple go to top link.
	 *
	 * @return mixed
	 **/
	public static function show_go_to_top_link($text) {

		echo apply_filters('padma_go_to_top_link', '<a href="#" class="footer-right footer-go-to-top-link footer-link">' . __($text, 'padma') . '</a>');

	}
	
	
	/**
	 * Shows a link to either view the full site or view the mobile site.
	 * 
	 * This will only show if the responsive grid is enabled.
	 **/
	public static function show_responsive_grid_toggle_link() {
		
		if ( !PadmaResponsiveGrid::is_enabled() )
			return false;
			
		$current_url = padma_get_current_url();	
			
		if ( PadmaResponsiveGrid::is_active() ) {
			
			$url = add_query_arg(array('full-site' => 'true'), $current_url);
			$classes = 'footer-responsive-grid-link footer-responsive-grid-disable footer-link';
			
			echo apply_filters('padma_responsive_disable_link', '<p class="footer-responsive-grid-link-container footer-responsive-grid-link-disable-container"><a href="' . $url . '" rel="nofollow" class="' . $classes . '">' . __('View Full Site', 'padma') . '</a></p>');
			
		} elseif ( PadmaResponsiveGrid::is_user_disabled() ) {
			
			$url = add_query_arg(array('full-site' => 'false'), $current_url);
			$classes = 'footer-responsive-grid-link footer-responsive-grid-enable footer-link';
			
			echo apply_filters('padma_responsive_enable_link', '<p class="footer-responsive-grid-link-container footer-responsive-grid-link-enable-container"><a href="' . $url . '" rel="nofollow" class="' . $classes . '">' . __('View Mobile Site', 'padma') . '</a></p>');
			
		}
		
	}
	
	
}

class PadmaFooterBlockOptions extends PadmaBlockOptionsAPI {
	
	public $tabs;
	public $inputs;


	function __construct(){
		
		$this->tabs = array(
			'nav-menu-content' => __('Content','padma')
		);

		$this->inputs = array(
			'nav-menu-content' => array(
				'show-admin-link' => array(
					'type' => 'checkbox',
					'name' => 'show-admin-link',
					'label' => __('Show Admin Link/Login','padma'),
					'default' => true
				),
				
				'show-go-to-top-link' => array(
					'name' => 'show-go-to-top-link',
					'label' => __('Show Go To Top Link','padma'),
					'type' => 'checkbox',
					'default' => true,
					'toggle'    => array(
						'true' => array(
							'show' => array(
								'#input-custom-go-to-top-text'
							)
						),
						'false' => array(
							'hide' => array(
								'#input-custom-go-to-top-text'
							)
						)
					)
				),

				'custom-go-to-top-text' => array(
					'name' => 'custom-go-to-top-text',
					'label' => __('Custom "Go to Top" text','padma'),
					'type' => 'text',
					'tooltip' => __('Custom "Go to Top" text','padma')
				),
				
				'hide-padma-attribution' => array(
					'name' => 'hide-padma-attribution',
					'label' => __('Hide Padma Theme Attribution','padma'),
					'type' => 'checkbox',
					'default' => false
				),
				
				'show-copyright' => array(
					'name' => 'show-copyright',
					'label' => __('Show Copyright','padma'),
					'type' => 'checkbox',
					'default' => true
				),
				
				'custom-copyright' => array(
					'name' => 'custom-copyright',
					'label' => __('Custom Copyright','padma'),
					'type' => 'text',
					'tooltip' => __('If you would like to change the copyright in the footer to say something different, enter it here. Use %Y% for current year.','padma')
				),
				
				'show-responsive-grid-link' => array(
					'name' => 'show-responsive-grid-link',
					'label' => __('Hide a link to view the full site on mobile.','padma'),
					'type' => 'checkbox',
					'tooltip' => __('Shows a link to either view the full site or view the mobile site.','padma'),
					'default' => false
				)
			)
		);
	}
		
}