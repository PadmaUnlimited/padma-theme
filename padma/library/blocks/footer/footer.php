<?php
padma_register_block('PadmaFooterBlock', padma_url() . '/library/blocks/footer');

class PadmaFooterBlock extends PadmaBlockAPI {
	
	
	public $id = 'footer';
	
	public $name = 'Footer';
		
	public $options_class = 'PadmaFooterBlockOptions';
	
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
			'id' => 'padma-attribution',
			'name' => 'Padma Attribution',
			'selector' => 'p.footer-padma-link',
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
		do_action('padma_before_footer');
		
		echo '<div class="footer-container">';
		
		echo '<div class="footer">';
		
		do_action('padma_footer_open');

		//Padma Attribution
		if ( parent::get_setting($block, 'hide-padma-attribution', false) == false )
			self::show_padma_link();
		
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
	 * Echos the Powered By Padma link.
	 * 
	 * @uses PadmaOption::get()
	 *
	 * @param string $text The name of the program to be displayed.  Defaults to Padma (obviously).
	 * 
	 * @return mixed
	 **/
	public static function show_padma_link() {

		if ( PadmaOption::get('affiliate-link') )
			$padma_location = strip_tags(PadmaOption::get('affiliate-link'));
		else
			$padma_location = 'https://www.padmaunlimited.com/';	

		echo apply_filters('padma_link', '<p class="footer-left footer-padma-link footer-link">' . __('Powered by Padma Unlimited, the ', 'padma') . ' <a href="' . $padma_location . '" title="Padma Premium WordPress Theme">drag and drop WordPress theme</a></p>');

	}


	/**
	 * Shows a simple copyright paragraph.
	 *
	 * @return mixed
	 **/
	public static function show_copyright($custom_copyright = false) {

		$default_copyright = __('Copyright', 'padma') . ' &copy; ' . date('Y') . ' ' . get_bloginfo('name');

		$copyright = $custom_copyright ? $custom_copyright : $default_copyright;

		echo apply_filters('padma_copyright', padma_parse_php('<p class="copyright footer-copyright">' . $copyright . '</p>'));

	}


	/**
	 * Shows a simple go to top link.
	 *
	 * @return mixed
	 **/
	public static function show_go_to_top_link() {

		echo apply_filters('padma_go_to_top_link', '<a href="#" class="footer-right footer-go-to-top-link footer-link">' . __('Go To Top', 'padma') . '</a>');

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
			
			'hide-padma-attribution' => array(
				'name' => 'hide-padma-attribution',
				'label' => 'Hide Padma Theme Attribution',
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