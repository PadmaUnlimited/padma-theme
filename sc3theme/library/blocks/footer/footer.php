<?php
blox_register_block('BloxFooterBlock', blox_url() . '/library/blocks/footer');

class BloxFooterBlock extends BloxBlockAPI {
	
	
	public $id = 'footer';
	
	public $name = 'Footer';
		
	public $options_class = 'BloxFooterBlockOptions';
	
	public $html_tag = 'footer';
	
	public $attributes = array(
		'itemscope' => '',
		'itemtype' => 'http://schema.org/WPFooter'
	);

	public $description = 'This typically goes at the bottom of your site and will display the copyright, and miscellaneous links.';

	public $allow_titles = false;
	
	protected $show_content_in_grid = true;
		
	
	function setup_elements() {
		
		$this->register_block_element(array(
			'id' => 'copyright',
			'name' => 'Copyright',
			'selector' => 'p.copyright',
			'properties' => array('fonts')
		));
		
		$this->register_block_element(array(
			'id' => 'blox-attribution',
			'name' => 'Blox Attribution',
			'selector' => 'p.footer-blox-link',
			'properties' => array('fonts')
		));
		
		$this->register_block_element(array(
			'id' => 'administration-panel',
			'name' => 'Administration Panel',
			'selector' => 'a.footer-admin-link',
			'properties' => array('fonts')
		));
		
		$this->register_block_element(array(
			'id' => 'go-to-top',
			'name' => 'Go To Top Link',
			'selector' => 'a.footer-go-to-top-link',
			'states' => array(
				'Hover' => 'a.footer-go-to-top-link:hover'
			)
		));
		
		$this->register_block_element(array(
			'id' => 'responsive-grid-link',
			'name' => 'Responsive Grid Toggle Link',
			'selector' => 'a.footer-responsive-grid-link',
			'properties' => array('fonts')
		));
		
	}
	
	
	function content($block) {
		
		//Add action for footer
		do_action('blox_before_footer');
		
		echo "\n" . '<div class="footer-container">' . "\n";
		
		echo "\n" . '<div class="footer">' . "\n";
		
		do_action('blox_footer_open');

		//Blox Attribution
		if ( parent::get_setting($block, 'hide-blox-attribution', false) == false )
			self::show_blox_link();
		
		//Go To Top Link
		if ( parent::get_setting($block, 'show-go-to-top-link', true) == true )
			self::show_go_to_top_link();
		
		//Admin Link
		if ( parent::get_setting($block, 'show-admin-link', true) == true )
			self::show_admin_link();
		 		
		//Copyright
		if ( parent::get_setting($block, 'show-copyright', true) == true )
			self::show_copyright(parent::get_setting($block, 'custom-copyright'));
		
		if ( parent::get_setting($block, 'show-responsive-grid-link', true) == true )
			self::show_responsive_grid_toggle_link();
		
		do_action('blox_footer_close');
		
		echo "\n" . '</div>';
		
		echo "\n" . '</div>';
		
		do_action('blox_after_footer');
		
	}
	
	
	/**
	 * Displays an admin link or admin login.
	 * 
	 * @uses BloxOption::get()
	 *
	 * @return void
	 **/
	public static function show_admin_link() {

		if ( is_user_logged_in() )
		    echo apply_filters('blox_admin_link', '<a href="' . admin_url() . '" class="footer-right footer-admin-link footer-link">'.__('Administration Panel', 'blox') . '</a>');
		else
		    echo apply_filters('blox_admin_link', '<a href="' . admin_url() . '" class="footer-right footer-admin-link footer-link">'.__('Administration Login', 'blox') . '</a>');

	}
	
	
	/**
	 * Echos the Powered By Blox link.
	 * 
	 * @uses BloxOption::get()
	 *
	 * @param string $text The name of the program to be displayed.  Defaults to Blox (obviously).
	 * 
	 * @return mixed
	 **/
	public static function show_blox_link() {

		if ( BloxOption::get('affiliate-link') )
			$blox_location = strip_tags(BloxOption::get('affiliate-link'));
		else
			$blox_location = 'http://bloxtheme.com/';	

		echo apply_filters('blox_link', '<p class="footer-left footer-blox-link footer-link">' . __('Powered by Blox, the ', 'blox') . ' <a href="' . $blox_location . '" title="Blox Premium WordPress Theme">drag and drop WordPress theme</a></p>');

	}


	/**
	 * Shows a simple copyright paragraph.
	 *
	 * @return mixed
	 **/
	public static function show_copyright($custom_copyright = false) {

		$default_copyright = __('Copyright', 'blox') . ' &copy; ' . date('Y') . ' ' . get_bloginfo('name');

		$copyright = $custom_copyright ? $custom_copyright : $default_copyright;

		echo apply_filters('blox_copyright', blox_parse_php('<p class="copyright footer-copyright">' . $copyright . '</p>'));

	}


	/**
	 * Shows a simple go to top link.
	 *
	 * @return mixed
	 **/
	public static function show_go_to_top_link() {

		echo apply_filters('blox_go_to_top_link', '<a href="#" class="footer-right footer-go-to-top-link footer-link">' . __('Go To Top', 'blox') . '</a>');

	}
	
	
	/**
	 * Shows a link to either view the full site or view the mobile site.
	 * 
	 * This will only show if the responsive grid is enabled.
	 **/
	public static function show_responsive_grid_toggle_link() {
		
		if ( !BloxResponsiveGrid::is_enabled() )
			return false;
			
		$current_url = blox_get_current_url();	
			
		if ( BloxResponsiveGrid::is_active() ) {
			
			$url = add_query_arg(array('full-site' => 'true'), $current_url);
			$classes = 'footer-responsive-grid-link footer-responsive-grid-disable footer-link';
			
			echo apply_filters('blox_responsive_disable_link', '<p class="footer-responsive-grid-link-container footer-responsive-grid-link-disable-container"><a href="' . $url . '" rel="nofollow" class="' . $classes . '">' . __('View Full Site', 'blox') . '</a></p>');
			
		} elseif ( BloxResponsiveGrid::is_user_disabled() ) {
			
			$url = add_query_arg(array('full-site' => 'false'), $current_url);
			$classes = 'footer-responsive-grid-link footer-responsive-grid-enable footer-link';
			
			echo apply_filters('blox_responsive_enable_link', '<p class="footer-responsive-grid-link-container footer-responsive-grid-link-enable-container"><a href="' . $url . '" rel="nofollow" class="' . $classes . '">' . __('View Mobile Site', 'blox') . '</a></p>');
			
		}
		
	}
	
	
}

class BloxFooterBlockOptions extends BloxBlockOptionsAPI {
	
	public $tabs = array(
		'nav-menu-content' => 'Content'
	);

	public $inputs = array(
		'nav-menu-content' => array(
			'show-admin-link' => array(
				'type' => 'checkbox',
				'name' => 'show-admin-link',
				'label' => 'Show Admin Link/Login',
				'default' => true
			),
			
			'show-go-to-top-link' => array(
				'name' => 'show-go-to-top-link',
				'label' => 'Show Go To Top Link',
				'type' => 'checkbox',
				'default' => true
			),
			
			'hide-blox-attribution' => array(
				'name' => 'hide-blox-attribution',
				'label' => 'Hide Blox Theme Attribution',
				'type' => 'checkbox',
				'default' => false
			),
			
			'show-copyright' => array(
				'name' => 'show-copyright',
				'label' => 'Show Copyright',
				'type' => 'checkbox',
				'default' => true
			),
			
			'custom-copyright' => array(
				'name' => 'custom-copyright',
				'label' => 'Custom Copyright',
				'type' => 'text',
				'tooltip' => 'If you would like to change the copyright in the footer to say something different, enter it here.'
			)
		)
	);
		
}