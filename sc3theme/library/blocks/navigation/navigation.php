<?php
blox_register_block('BloxNavigationBlock', blox_url() . '/library/blocks/navigation');

class BloxNavigationBlock extends BloxBlockAPI {
	
	
	public $id = 'navigation';
	
	public $name = 'Navigation';
		
	public $options_class = 'BloxNavigationBlockOptions';
	
	public $fixed_height = false;
	
	public $html_tag = 'nav';
	
	public $attributes = array(
		'itemscope' => '',
		'itemtype' => 'http://schema.org/SiteNavigationElement'
	);
	
	public $description = 'The navigation is the menu that will display all of the pages in your site.';

	protected $show_content_in_grid = true;
	
	/* Use this to pass the block from static function to static function */
	static public $block = null;

	static private $menu_sub_check_cache = array();

	static private $wp_nav_menu_cache = array();


	public static function init() {

		if ( is_admin() ) {
			return;
		}

		wp_register_script('jquery-hoverintent', blox_url() . '/library/media/js/jquery.hoverintent.js', array('jquery'));

	}

	
	public static function init_action($block_id, $block = false) {

		if ( !$block )
			$block = BloxBlocksData::get_block($block_id);
								
		$name = BloxBlocksData::get_block_name($block) . ' &mdash; ' . 'Layout: ' . BloxLayout::get_name($block['layout']);
		
		register_nav_menu('navigation_block_' . $block_id, $name);

	}
	
	
	public static function enqueue_action($block_id, $block, $original_block = null) {
		
		$dependencies = array();

		/* Handle sub menus with super fish */
			if ( self::does_menu_have_subs($block) ) {

				$dependencies[] = 'jquery';

				if ( parent::get_setting($block, 'hover-intent', true) )
					$dependencies[] = 'jquery-hoverintent';

				wp_enqueue_script('blox-superfish', blox_url() . '/library/blocks/navigation/js/jquery.superfish.js', array_unique($dependencies));

			}

		/* SelectNav... Responsive Select */
			if ( BloxResponsiveGrid::is_active() && parent::get_setting($block, 'responsive-select', true) ) {

				wp_enqueue_script('blox-selectnav', blox_url() . '/library/blocks/navigation/js/selectnav.js', array('jquery'));

			}

	}
	
	
	function content($block) {
		
		self::$block = $block;

		/* Variables */
		$vertical = parent::get_setting($block, 'vert-nav-box', false);
		$alignment = parent::get_setting($block, 'alignment', 'left');
		
		$search = parent::get_setting($block, 'enable-nav-search', false);
		$search_position = parent::get_setting($block, 'nav-search-position', 'right');
		$hide_home_link = parent::get_setting($block, 'hide-home-link', false);
		
		/* Classes */
		$nav_classes = array();
		
		$nav_classes[] = $vertical ? 'nav-vertical' : 'nav-horizontal';
		$nav_classes[] = 'nav-align-' . $alignment;
		
		if ( $search && !$vertical ) {
			
			$nav_classes[] = 'nav-search-active';
			$nav_classes[] = 'nav-search-position-' . $search_position;
			
		}
			
		$nav_classes = trim(implode(' ', array_unique($nav_classes)));

		/* Use legacy ID */
		$block['id'] = BloxBlocksData::get_legacy_id( $block );

		$nav_location = 'navigation_block_' . $block['id'];
		
		echo '<div class="' . $nav_classes . '">';
		
				echo self::get_wp_nav_menu($block);
				
				if ( $search && !$vertical ) {
				
					echo '<div class="nav-search">';

						echo blox_get_search_form(parent::get_setting($block, 'nav-search-placeholder', null));

					echo '</div>';
					
				}
		
		echo '</div>';
		
	}
	
	
	public static function dynamic_css($block_id, $block, $original_block = null) {

		$selector = '#block-' . $block_id;

		/* If this block is a mirror, then pull the settings from the block that's mirroring that way the dimensions are correct */
			if ( is_array($original_block) ) {

				$block_id = $original_block['id'];
				$block = $original_block;

				$selector .= '.block-original-' . $block_id;

			}
				
		$block_height = BloxBlocksData::get_block_height($block);
		
		return '
			' . $selector . ' .nav-horizontal ul.menu > li > a, 
			' . $selector . ' .nav-search-active .nav-search { 
				height: ' . $block_height . 'px; 
				line-height: ' . $block_height . 'px; 
			}';
		
	}
	
	
	public static function dynamic_js($block_id, $block, $original_block = null) {

		$js = null;

		$selector = !is_array( $original_block ) ? '#block-' . $block_id : '.block-original-' . $original_block['id'];

		/* Superfish */
			if ( self::does_menu_have_subs($block) ) {

				switch ( parent::get_setting($block, 'effect', 'fade') ) {
					case 'none':
						$animation = '{height:"show"}';
						$speed = '0';
					break;

					case 'fade':
						$animation = '{opacity:"show"}';
						$speed = "'fast'";
					break;

					case 'slide':
						$animation = '{height:"show"}';
						$speed = "'fast'";
					break;
				}

				$js .= 'jQuery(document).ready(function(){ 
					if ( typeof jQuery().superfish != "function" )
						return false;

					jQuery("' . $selector . '").find("ul.menu").superfish({
						delay: 200,
						animation: ' . $animation . ',
						speed: ' . $speed . ',
						onBeforeShow: function() {
							var parent = jQuery(this).parent();
							
							var subMenuParentLink = jQuery(this).siblings(\'a\');
							var subMenuParents = jQuery(this).parents(\'.sub-menu\');

							if ( subMenuParents.length > 0 || jQuery(this).parents(\'.nav-vertical\').length > 0 ) {
								jQuery(this).css(\'marginLeft\',  parent.outerWidth());
								jQuery(this).css(\'marginTop\',  -subMenuParentLink.outerHeight());
							}
						}
					});		
				});' . "\n\n";

			}

		/* SelectNav */
			if ( BloxResponsiveGrid::is_active() && parent::get_setting($block, 'responsive-select', true) ) {

				$js .= 'jQuery(document).ready(function(){

					if ( typeof window.selectnav != "function" )
						return false;

					selectnav(jQuery("' . $selector . '").find("ul.menu")[0], {
						label: "-- ' . __('Navigation', 'blox') . ' --",
						nested: true,
						indent: "-",
						activeclass: "current-menu-item"
					});

					jQuery("' . $selector . '").find("ul.menu").addClass("selectnav-active");

				});' . "\n\n";

			}
		
		return $js;
		
	}


	public static function get_wp_nav_menu($block) {

		$nav_location = 'navigation_block_' . BloxBlocksData::get_legacy_id($block);

		if ( blox_get($nav_location, self::$wp_nav_menu_cache) !== null ) {
			return blox_get($nav_location, self::$wp_nav_menu_cache);
		}

		/* Add filter to add home link */
		self::$block = $block;

		add_filter('wp_nav_menu_items', array(__CLASS__, 'home_link_filter'));
		add_filter('wp_list_pages', array(__CLASS__, 'home_link_filter'));
		add_filter('wp_page_menu', array(__CLASS__, 'fix_legacy_nav'));

		$nav_menu_args = array(
			'theme_location' => $nav_location,
			'container' => false,
			'echo' => false
		);

		if ( BloxRoute::is_grid() || blox_get('ve-live-content-query', $block) ) {

			$nav_menu_args['link_before'] = '<span>';
			$nav_menu_args['link_after'] = '</span>';

		}

		self::$wp_nav_menu_cache[$nav_location] = wp_nav_menu(apply_filters('blox_navigation_block_query_args', $nav_menu_args, $block));

		/* Remove filter for home link so other non-navigation blocks are modified */
		remove_filter('wp_nav_menu_items', array(__CLASS__, 'home_link_filter'));
		remove_filter('wp_list_pages', array(__CLASS__, 'home_link_filter'));
		remove_filter('wp_page_menu', array(__CLASS__, 'fix_legacy_nav'));

		return self::$wp_nav_menu_cache[$nav_location];

	}

	
	public static function does_menu_have_subs($block) {

		$nav_location = 'navigation_block_' . BloxBlocksData::get_legacy_id($block);

		/*
		 * Running wp_nav_menu() is a little taxing when not needed.
		 * Sometimes self::does_menu_have_subs() is called multiple times on the same location and this is wasting resources.
		 * This is what the cache is here to resolve.
		 */
		if ( blox_get($nav_location, self::$menu_sub_check_cache) !== null ) {
			return blox_get($nav_location, self::$menu_sub_check_cache);
		}
		
		$menu = self::get_wp_nav_menu($block);

		$result = false;
				
		if ( preg_match('/class=[\'"]sub-menu[\'"]/', $menu) || preg_match('/class=[\'"]children[\'"]/', $menu) )
			$result = true;

		self::$menu_sub_check_cache[$nav_location] = $result;
			
		return self::$menu_sub_check_cache[$nav_location];
		
	}
	
	
	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'menu-item',
			'name' => 'Menu Item',
			'selector' => 'ul.menu li > a',
			'properties' => array(
				'fonts', 
				'background', 
				'borders', 
				'padding', 
				'corners', 
				'box-shadow', 
				'text-shadow'
			),
			'states' => array(
				'Selected' => '
					ul.menu li.current_page_item > a, 
					ul.menu li.current_page_parent > a, 
					ul.menu li.current_page_ancestor > a, 
					ul.menu li.current_page_item > a:hover, 
					ul.menu li.current_page_parent > a:hover, 
					ul.menu li.current_page_ancestor > a:hover,
					ul.menu li.current-menu-item > a, 
					ul.menu li.current-menu-parent > a, 
					ul.menu li.current-menu-ancestor > a, 
					ul.menu li.current-menu-item > a:hover, 
					ul.menu li.current-menu-parent > a:hover, 
					ul.menu li.current-menu-ancestor > a:hover
				', 
				'Hover' => 'ul.menu li > a:hover', 
				'Clicked' => 'ul.menu li > a:active',
				'Dropdown Open' => 'ul.menu li.sfHover > a'
			)
		));
		
		
		$this->register_block_element(array(
			'id' => 'sub-nav-menu',
			'name' => 'Sub Menu',
			'selector' => 'ul.sub-menu',
			'properties' => array('background', 'borders', 'padding', 'corners', 'box-shadow', 'margins'),
			'disallow-nudging' => true
		));


		$this->register_block_element(array(
			'id' => 'sub-menu-item',
			'name' => 'Sub Menu Item',
			'selector' => 'ul.sub-menu li > a',
			'properties' => array(
				'fonts', 
				'background', 
				'borders', 
				'padding', 
				'corners', 
				'box-shadow', 
				'text-shadow'
			),
			'states' => array(
				'Selected' => '
					ul.sub-menu li.current_page_item > a, 
					ul.sub-menu li.current_page_parent > a, 
					ul.sub-menu li.current_page_ancestor > a, 
					ul.sub-menu li.current_page_item > a:hover, 
					ul.sub-menu li.current_page_parent > a:hover, 
					ul.sub-menu li.current_page_ancestor > a:hover
				', 
				'Hover' => 'ul.sub-menu li > a:hover', 
				'Clicked' => 'ul.sub-menu li > a:active',
				'Dropdown Open' => 'ul.sub-menu li.sfHover > a'
			)
		));

		$this->register_block_element(array(
			'id' => 'search-input',
			'name' => 'Search Input',
			'selector' => '#searchform input[type="text"]',
			'states' => array(
				'Focused' => '#searchform input[type="text"]:focus'
			)
		));
		
	}
	

	public static function home_link_filter($menu) {
		
		$block = self::$block;

		if ( parent::get_setting($block, 'hide-home-link') )
			return $menu;
		
		if ( get_option('show_on_front') == 'posts' ) {

			$current = (is_home() || is_front_page()) ? ' current_page_item' : null;
			$home_text = ( parent::get_setting($block, 'home-link-text') ) ? parent::get_setting($block, 'home-link-text') : 'Home';

			/* If it's not the grid, then do not add the extra <span>'s */
			if ( !BloxRoute::is_grid() && !blox_get('ve-live-content-query', $block) )
				$home_link = '<li class="menu-item-home blox-home-link' . $current . '"><a href="' . home_url() . '">' . $home_text . '</a></li>';
			
			/* If it IS the grid, add extra <span>'s so it can be automatically vertically aligned */
			else
				$home_link = '<li class="menu-item-home blox-home-link' . $current . '"><a href="' . home_url() . '"><span>' . $home_text . '</span></a></li>';
			
		} else {
			
			$home_link = null;
			
		}

		return $home_link . $menu;
		
	}
	
	
	public static function fix_legacy_nav($menu) {
		
		$menu = preg_replace('/<ul class=[\'"]children[\'"]/', '<ul class="sub-menu"', trim($menu)); //Change sub menu class
		$menu = preg_replace('/<div class=[\'"]menu[\'"]>/', '', $menu, 1); //Remove opening <div>
		$menu = str_replace('<ul>', '<ul class="menu">', $menu); //Add menu class to main <ul>
		$menu = str_replace('current_page_item', 'current_page_item current-menu-item', $menu); //Add current-menu-item wherever current_page_item is to make legacy nav more consistent with wp_nav_menu()
				
		return substr(trim($menu), 0, -6); //Remove the closing </div>
		
	}
	
	
}


class BloxNavigationBlockOptions extends BloxBlockOptionsAPI {
	
	public $tabs = array(
		'nav-menu-content' => 'Content',
		'search' => 'Search',
		'home-link' => 'Home Link',
		'orientation' => 'Orientation',
		'dropdowns' => 'Dropdowns',
		'responsiveness' => 'Responsiveness'
	);

	public $inputs = array(
		'search' => array(
			'enable-nav-search' => array(
				'type' => 'checkbox',
				'name' => 'enable-nav-search',
				'label' => 'Enable Navigation Search',
				'default' => false,
				'tooltip' => 'If you wish to have a simple search form in the navigation bar, then check this box.  <em><strong>Note:</strong> the search form will not show if the Vertical Navigation option is enabled for this block.</em>'
			),
			
			'nav-search-position' => array(
				'type' => 'select',
				'name' => 'nav-search-position',
				'label' => 'Search Position',
				'default' => 'right',
				'options' => array(
					'left' => 'Left',
					'right' => 'Right'
				),
				'tooltip' => 'If you would like the navigation search input to snap to the left instead of the right, you can use this option.'
			),

			'nav-search-placeholder' => array(
				'type' => 'text',
				'name' => 'nav-search-placeholder',
				'label' => 'Search Placeholder',
				'default' => 'Type to search, then press enter',
				'tooltip' => 'This will be the text inside the search input telling the visitor how to interact with the search input.'
			)
		),
		
		'home-link' => array(
			'hide-home-link' => array(
				'type' => 'checkbox',
				'name' => 'hide-home-link',
				'label' => 'Hide Home Link',
				'default' => false,
				'tooltip' => 'If you do not have a static page as the front page, Blox will add a home item to the navigation menu by default.',
			),

			'home-link-text' => array(
				'name' => 'home-link-text',
				'label' => 'Home Link Text',
				'type' => 'text',
				'tooltip' => 'If you would like the link to your homepage to say something other than <em>Home</em>, enter it here!',
				'default' => 'Home'
			)
		),
		
		'orientation' => array(
			'alignment' => array(
				'type' => 'select',
				'name' => 'alignment',
				'label' => 'Alignment',
				'default' => 'left',
				'options' => array(
					'left' => 'Left',
					'right' => 'Right',
					'center' => 'Center'
				)
			),
			
			'vert-nav-box' => array(
				'type' => 'checkbox',
				'name' => 'vert-nav-box',
				'label' => 'Vertical Navigation',
				'default' => false,
				'tooltip' => 'Instead of showing navigation horizontally, you can make the navigation show vertically.  <em><strong>Note:</strong> You may have to resize the block to make the navigation items fit correctly.</em>'
			)
		),

		'dropdowns' => array(
			'effect' => array(
				'type' => 'select',
				'name' => 'effect',
				'label' => 'Drop Down Effect',
				'default' => 'fade',
				'options' => array(
					'none' => 'No Effect',
					'fade' => 'Fade',
					'slide' => 'Slide'
				),
				'tooltip' => 'This is the effect that will be used when the drop downs are shown and hidden.'
			),

			'hover-intent' => array(
				'type' => 'checkbox',
				'name' => 'hover-intent',
				'label' => 'Hover Intent',
				'default' => true,
				'tooltip' => 'Hover Intent makes it so if a navigation item with a drop down is hovered then the drop down will only be shown if the visitor has their mouse over the item for more than a split second.<br /><br />This reduces drop-downs from sporatically showing if the visitor makes fast movements over the navigation.'
			)
		),

		'responsiveness' => array(
			'responsiveness-notice' => array(
				'name' => 'responsiveness-notice',
				'type' => 'notice',
				'notice' => 'You must have Responsive Grid enabled to take advantage of these options.  Responsive Grid can be enabled under Setup &raquo; Responsive Grid in the Grid mode.'
			),

			'responsive-select' => array(
				'type' => 'checkbox',
				'name' => 'responsive-select',
				'label' => 'Responsive Select',
				'default' => true,
				'tooltip' => 'When enabled, your navigation will turn into a mobile-friendly select menu when your visitors are viewing your site on a mobile device (phones, not tablets).'
			)
		)
	);
	
	
	function modify_arguments($args = false) {
		
		$this->tab_notices['nav-menu-content'] = 'To add items to this navigation menu, go to <a href="' . admin_url('nav-menus.php') . '" target="_blank">WordPress Admin &raquo; Appearance &raquo; Menus</a>.  Then, create a menu and assign it to <em>' . BloxBlocksData::get_block_name($args['blockID']) . '</em> in the <strong>Theme Locations</strong> box.';
		
	}
	
}