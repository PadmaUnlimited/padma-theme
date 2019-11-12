<?php

class PadmaNavigationBlock extends PadmaBlockAPI {

	public $id;
	public $name;
	public $options_class;
	public $fixed_height;
	public $html_tag;
	public $attributes;
	public $description;
	public $categories;
	protected $show_content_in_grid;

	/* Use this to pass the block from static function to static function */
	static public $block = null;
	static private $menu_sub_check_cache = array();
	static private $wp_nav_menu_cache = array();



	function __construct(){

		$this->id = 'navigation';
		$this->name = 'Navigation';
		$this->options_class = 'PadmaNavigationBlockOptions';
		$this->fixed_height = false;
		$this->html_tag = 'nav';
		$this->attributes = array(
			'itemscope' => '',
			'itemtype' => 'http://schema.org/SiteNavigationElement'
		);
		$this->description = 'The navigation is the menu that will display all of the pages in your site.';
		$this->categories = array('core','navigation');
		$this->show_content_in_grid = true;

	}


	public static function init() {

		if ( is_admin() ) {
			return;
		}

		wp_register_script( 'jquery-hoverintent', padma_url() . '/library/media/js/jquery.hoverintent.js', array( 'jquery' ));
		wp_enqueue_style('padma-navigation-block', padma_url() . '/library/blocks/navigation/css/navigation.css');

	}


	public static function init_action( $block_id, $block = false ) {

		if ( ! $block ) {
			$block = PadmaBlocksData::get_block( $block_id );
		}

		$name = PadmaBlocksData::get_block_name( $block ) . ' &mdash; ' . 'Layout: ' . PadmaLayout::get_name( $block['layout'] );

		register_nav_menu( 'navigation_block_' . $block_id, $name );



	}


	public static function enqueue_action( $block_id, $block, $original_block = null ) {

		$dependencies = array();

		/* Handle sub menus with super fish */
		if ( self::does_menu_have_subs( $block ) ) {

			$dependencies[] = 'jquery';

			if ( parent::get_setting( $block, 'hover-intent', true ) ) {
				$dependencies[] = 'jquery-hoverintent';
			}

			wp_enqueue_script( 'padma-superfish', padma_url() . '/library/blocks/navigation/js/jquery.superfish.js', array_unique( $dependencies ) );

		}

		/* SelectNav... Responsive Select */
		if ( PadmaResponsiveGrid::is_active() && parent::get_setting( $block, 'responsive-select', true ) ) {

			switch ( parent::get_setting($block, 'responsive-method', 'select') ) {


				case 'vertical':
					wp_enqueue_script( 'padma-slicknav', padma_url() . '/library/media/js/jquery.slicknav.js', array( 'jquery' ) );
					wp_enqueue_style( 'padma-slicknav', padma_url() . '/library/media/css/slicknav.css' );
					break;

				case 'slide-out':
					wp_enqueue_script( 'padma-pushy', padma_url() . '/library/media/js/pushy.js', array( 'jquery' ) );
					wp_enqueue_style( 'padma-pushy', padma_url() . '/library/media/css/pushy.css' );
					break;

				default:
					wp_enqueue_script( 'padma-selectnav', padma_url() . '/library/blocks/navigation/js/selectnav.js', array( 'jquery' ) );
					break;
			}

		}

	}


	function content( $block ) {


		self::$block = $block;

		/* Variables */
		$vertical = parent::get_setting( $block, 'vert-nav-box', false );
		$alignment = parent::get_setting( $block, 'alignment', 'left' );

		$search = parent::get_setting( $block, 'enable-nav-search', false );
		$search_position = parent::get_setting( $block, 'nav-search-position', 'right' );


		/* Classes */
		$nav_classes = array();

		$nav_classes[] = $vertical ? 'nav-vertical' : 'nav-horizontal';
		$nav_classes[] = 'nav-align-' . $alignment;
		$nav_classes[] = 'responsive-menu-align-' . parent::get_setting($block, 'responsive-menu-label-position', 'right');


		if ( $search && ! $vertical ) {

			$nav_classes[] = 'nav-search-active';
			$nav_classes[] = 'nav-search-position-' . $search_position;

		}

		$nav_classes = trim( implode( ' ', array_unique( $nav_classes ) ) );


		/* Use legacy ID */
		$block['id'] = PadmaBlocksData::get_legacy_id( $block );

		$nav_location = 'navigation_block_' . $block['id'];

		echo '<div class="' . $nav_classes . '">';

		echo self::get_wp_nav_menu( $block );

		if ( parent::get_setting($block, 'responsive-method', 'select') == 'slide-out' ) {

			switch ( parent::get_setting($block, 'responsive-menu-label-position', 'right') ) {
				case 'right':
					$toggle_class = ' pushy-menu-toggle-right';
					break;

				case 'left':
					$toggle_class = ' pushy-menu-toggle-left';
					break;

				case 'center':
					$toggle_class = ' pushy-menu-toggle-center';
					break;
			}


			echo '<span class="pushy-menu-toggle' . $toggle_class . '">
					<span class="pushy-menu-toggle-text">' . parent::get_setting($block, 'responsive-menu-label', __('Menu','padma')) . '</span>
					<span class="pushy-menu-toggle-icon">
                        <span class="pushy-menu-toggle-icon-bar"></span>
                        <span class="pushy-menu-toggle-icon-bar"></span>
                        <span class="pushy-menu-toggle-icon-bar"></span>
                    </span>
				</span>';
		}

		if ( $search && ! $vertical ) {

			echo '<div class="nav-search">';

			echo padma_get_search_form( parent::get_setting( $block, 'nav-search-placeholder', null ) );

			echo '</div>';

		}

		echo '</div>';

	}


	public static function dynamic_css( $block_id, $block, $original_block = null ) {

		$selector = '#block-' . PadmaBlocksData::get_legacy_id($block);

		/* If this block is a mirror, then pull the settings from the block that's mirroring that way the dimensions are correct */
		if ( is_array( $original_block ) ) {

			$block_id = $original_block['id'];
			$block = $original_block;

			$selector .= '.block-original-' . PadmaBlocksData::get_legacy_id($block);

		}

		$item_height = parent::get_setting($block, 'item-height', null) ? parent::get_setting($block, 'item-height', null) : PadmaBlocksData::get_block_height($block);

		$css = $selector . ' .nav-horizontal {
				line-height: ' . $item_height . 'px;
				float: left;
				width: 100%;
			}

			' . $selector . ' .nav-horizontal ul.menu {
				line-height: ' . $item_height . 'px;
				width: 100%;
			}

			' . $selector . ' .nav-horizontal ul.menu > li > a, 
			' . $selector . ' .nav-search-active .nav-search { 
				height: ' . $item_height . 'px;
				line-height: ' . $item_height . 'px;
			}';


		$use_breakpoint = parent::get_setting($block, 'use-responsive-menu-breakpoint', true);
		$breakpoint = parent::get_setting($block, 'responsive-menu-breakpoint', 600);

		switch ( parent::get_setting($block, 'responsive-method', 'select') ) {
			case 'vertical':
				$css .= "\n\n";

				if ( $use_breakpoint ) {
					$css .= '@media only screen and (max-width: ' . $breakpoint . 'px) {';
					$css .= $selector . ' ul.menu {
							display: none; 
						}

						' . $selector . ' .slicknav_menu {
							display: block;
							background-color: #fff;
						}';


					$css .= '}';
				}

				$css .= "\n\n";

				break;

			case 'slide-out':
				$css .= "\n\n";

				if ( $use_breakpoint ) {
					$css .= '@media only screen and (max-width: ' . $breakpoint . 'px) {';
					$css .= $selector . ' ul.menu {
					    display: none;
					  }

					  ' . $selector . ' .pushy-menu-toggle {
					    display: inline-block;
					  }';

					$css .= '}';
				}

				$css .= "\n\n";

				break;
		}

		return $css;

	}


	public static function dynamic_js( $block_id, $block, $original_block = null ) {

		$js = null;

		$selector = ! is_array( $original_block ) ? '#block-' . $block_id : '.block-original-' . $original_block['id'];

		/* Superfish */
		if ( self::does_menu_have_subs( $block ) ) {

			switch ( parent::get_setting( $block, 'effect', 'fade' ) ) {
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

			$js .= 'jQuery(function() {

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
		if ( PadmaResponsiveGrid::is_active() && parent::get_setting( $block, 'responsive-select', true ) ) {

			if ( padma_get('padma-trigger') == 'load-block-content' ) {
				$js .= 'jQuery(document).ready(function($){
					$(".pushy").remove();						
					$(".pushy-site-overlay").remove();
					$(".block-type-navigation .slicknav_menu").remove();
				});';
			}

			switch ( parent::get_setting($block, 'responsive-method', 'select') ) {
				case 'vertical':
					switch ( parent::get_setting($block, 'responsive-menu-label-position', 'right') ) {
						case 'right':
							$toggle_class = ' slicknav_btn-right';
							$menu_class = ' slicknav-right';
							break;

						case 'left':
							$toggle_class = ' slicknav_btn-left';
							$menu_class = ' slicknav-left';
							break;

						case 'center':
							$toggle_class = ' slicknav_btn-center';
							$menu_class = ' slicknav-center';
							break;
					}

					$js .= 'jQuery(document).ready(function($){

							$("' . $selector . ' ul.menu").slicknav({
								prependTo: "' . $selector . ' .block-content",
								label: "' . parent::get_setting($block, 'responsive-menu-label', 'Menu') . '",
								additionalBtnClass: "' . $toggle_class . '",
								additionalMenuClass: "' . $menu_class . '"
							});

						});' . "\n\n";

					break;

				case 'slide-out':
					$slide_out_pos = parent::get_setting($block, 'slide-out-menu-position', 'left');

					$js .= 'jQuery(document).ready(function($){

						if ( typeof window.hwPushy != "function" )
							return false;


						var $pushyMenu = $("' . $selector . '").find("ul.menu").first().clone();
							$pushyMenu.addClass("pushy pushy-'  . $slide_out_pos . '").removeClass("menu");

						var id = "slide-out-" + "'  . $slide_out_pos . '";

						$pushyMenu.attr("id", id);

						$pushyMenu.find("li").addClass("pushy-link");
						$pushyMenu.find("ul").each(function() {
							$(this).removeAttr("style");
							$(this).siblings("a").attr("href", "#");
							$(this).closest("li").addClass("pushy-submenu");
						});

						$(".pushy-site-overlay").remove();
						$(\'<div class="pushy-site-overlay" />\').appendTo("body");

						$(".pushy").remove();
						$pushyMenu.prependTo(\'body\');

						$("#wpadminbar").appendTo("body");

						window.hwPushy();

						});' . "\n\n";

					break;

				default:

					$js .= 'jQuery(document).ready(function($){

						if ( typeof window.selectnav != "function" )
							return false;

						selectnav($("' . $selector . '").find("ul.menu")[0], {
							label: "-- ' . esc_html__( 'Navigation', 'padma' ) . ' --",
							nested: true,
							indent: "-",
							activeclass: "current-menu-item"
						});

						$("' . $selector . '").find("ul.menu").addClass("selectnav-active");

						});' . "\n\n";


					break;
			}

		}

		return $js;

	}


	public static function get_wp_nav_menu( $block ) {

		$nav_location = 'navigation_block_' . PadmaBlocksData::get_legacy_id( $block );

		if ( padma_get( $nav_location, self::$wp_nav_menu_cache ) !== null ) {
			return padma_get( $nav_location, self::$wp_nav_menu_cache );
		}

		/* Add filter to add home link */
		self::$block = $block;

		add_filter( 'wp_nav_menu_items', array( __CLASS__, 'home_link_filter' ));
		add_filter( 'wp_list_pages', array( __CLASS__, 'home_link_filter' ) );
		add_filter( 'wp_page_menu', array( __CLASS__, 'fix_legacy_nav' ) );

		$nav_menu_args = array(
			'theme_location' => $nav_location,
			'container' => false,
			'echo' => false
		);

		if ( padma_get( 've-live-content-query', $block ) ) {

			$nav_menu_args['link_before'] = '<span>';
			$nav_menu_args['link_after'] = '</span>';

		}

		self::$wp_nav_menu_cache[ $nav_location ] = wp_nav_menu( apply_filters( 'padma_navigation_block_query_args', $nav_menu_args, $block ) );

		/* Remove filter for home link so other non-navigation blocks are modified */
		remove_filter( 'wp_nav_menu_items', array( __CLASS__, 'home_link_filter' ) );
		remove_filter( 'wp_list_pages', array( __CLASS__, 'home_link_filter' ) );
		remove_filter( 'wp_page_menu', array( __CLASS__, 'fix_legacy_nav' ) );

		return self::$wp_nav_menu_cache[ $nav_location ];

	}


	public static function does_menu_have_subs( $block ) {

		$nav_location = 'navigation_block_' . PadmaBlocksData::get_legacy_id( $block );

		/*
		 * Running wp_nav_menu() is a little taxing when not needed.
		 * Sometimes self::does_menu_have_subs() is called multiple times on the same location and this is wasting resources.
		 * This is what the cache is here to resolve.
		 */
		if ( padma_get( $nav_location, self::$menu_sub_check_cache ) !== null ) {
			return padma_get( $nav_location, self::$menu_sub_check_cache );
		}

		$menu = self::get_wp_nav_menu( $block );

		$result = false;

		if ( preg_match( '/class=[\'"]sub-menu[\'"]/', $menu ) || preg_match( '/class=[\'"]children[\'"]/', $menu ) ) {
			$result = true;
		}

		self::$menu_sub_check_cache[ $nav_location ] = $result;

		return self::$menu_sub_check_cache[ $nav_location ];

	}


	function setup_elements() {

		$this->register_block_element( array(
			'name' => __('Menu Container','padma'),
			'selector' => '.nav-horizontal'
		) );

		$this->register_block_element( array(
			'name' => __('Menu Container - Vertical','padma'),
			'selector' => '.nav-vertical'
		) );

		$this->register_block_element( array(
			'name' => __('Menu','padma'),
			'selector' => 'ul.menu'
		) );

		$this->register_block_element( array(
			'name' => __('Menu Item','padma'),
			'selector' => 'ul.menu li'
		) );

		$this->register_block_element(array(
			'name' => __('Menu Item Shrinked','padma'),
			'selector' => 'ul.menu li',
			'states' => array(
				'Shrinked' => 'ul.menu li.is_shrinked',
			)
		));

		$this->register_block_element( array(
			'name' => __('Menu Item Link','padma'),
			'selector' => 'ul.menu li a'
		) );

		$this->register_block_element( array(
			'name' => __('Menu Item - Active','padma'),
			'selector' => 'ul.menu li.current-menu-item a'
		) );

		$this->register_block_element( array(
			'name' => __('Sub Menu','padma'),
			'selector' => 'ul.sub-menu'
		) );

		$this->register_block_element( array(
			'name' => __('Sub Menu Item','padma'),
			'selector' => 'ul.sub-menu li'
		) );

		$this->register_block_element( array(
			'name' => __('Sub Menu Item Link','padma'),
			'selector' => 'ul.sub-menu li a'
		) );

		$this->register_block_element( array(
			'name' => __('Search Input','padma'),
			'selector' => '#searchform input[type="text"]'
		) );

		$this->register_block_element( array(
			'name' => __('Horizontal SlideOut Tag','padma'),
			'selector' => '.nav-horizontal .pushy-menu-toggle',
			'states' => array(
					'Hover' => '.nav-horizontal .pushy-menu-toggle:hover', 
					'Clicked' => '.nav-horizontal .pushy-menu-toggle:active'
				)
		) );

		$this->register_block_element( array(
			'name' => __('Horizontal SlideOut Icon','padma'),
			'selector' => '.nav-horizontal .pushy-menu-toggle .pushy-menu-toggle-icon',
			'states' => array(
					'Hover' => '.nav-horizontal .pushy-menu-toggle .pushy-menu-toggle-icon:hover', 
					'Clicked' => '.nav-horizontal .pushy-menu-toggle .pushy-menu-toggle-icon:active'
				)
		) );

		$this->register_block_element( array(
			'name' => __('Horizontal SlideOut Icon Line','padma'),
			'selector' => '.nav-horizontal .pushy-menu-toggle .pushy-menu-toggle-icon .pushy-menu-toggle-icon-bar',
			'states' => array(
					'Hover' => '.nav-horizontal .pushy-menu-toggle .pushy-menu-toggle-icon .pushy-menu-toggle-icon-bar:hover', 
					'Clicked' => '.nav-horizontal .pushy-menu-toggle .pushy-menu-toggle-icon .pushy-menu-toggle-icon-bar:active'
				)
		) );

		$this->register_pre_4_elements();

	}


	function register_pre_4_elements() {

		$this->register_block_element( array(
				'legacy-only' => true,
				'id' => 'menu-item',
				'name' => __('Menu Item','padma'),
				'selector' => 'ul.menu li > a',
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
						'Dropdown Open' => 'ul.menu li.sfHover > a',
						'Shrinked' => 'ul.menu li.is_shrinked',
			
				)
		) );


		$this->register_block_element( array(
				'legacy-only' => true,
				'id' => 'sub-nav-menu',
				'name' => __('Sub Menu','padma'),
				'selector' => 'ul.sub-menu'
		) );


		$this->register_block_element( array(
				'legacy-only' => true,
				'id' => 'sub-menu-item',
				'name' => __('Sub Menu Item','padma'),
				'selector' => 'ul.sub-menu li > a',
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
						'Dropdown Open' => 'ul.sub-menu li.sfHover > a',
						'Shrinked' => 'ul.menu li.is_shrinked',
				)
		) );

		$this->register_block_element( array(
				'legacy-only' => true,
				'id' => 'search-input',
				'name' => __('Search Input','padma'),
				'selector' => '#searchform input[type="text"]',
				'states' => array(
						'Focused' => '#searchform input[type="text"]:focus'
				)
		) );

	}


	public static function home_link_filter( $menu ) {

		$block = self::$block;

		if ( parent::get_setting( $block, 'hide-home-link' ) ) {
			return $menu;
		}

		if ( get_option( 'show_on_front' ) == 'posts' ) {

			$current = ( is_home() || is_front_page() ) ? ' current-menu-item current_page_item' : null;
			$home_text = ( parent::get_setting( $block, 'home-link-text' ) ) ? parent::get_setting( $block, 'home-link-text' ) : __('Home','padma');

			/* If it's not the grid, then do not add the extra <span>'s */
			if ( ! padma_get( 've-live-content-query', $block ) ) {
				$home_link = '<li class="menu-item-home padma-home-link' . $current . '"><a href="' . home_url() . '">' . $home_text . '</a></li>';
			} /* If it IS the grid, add extra <span>'s so it can be automatically vertically aligned */
			else {
				$home_link = '<li class="menu-item-home padma-home-link' . $current . '"><a href="' . home_url() . '"><span>' . $home_text . '</span></a></li>';
			}

		} else {

			$home_link = null;

		}

		return $home_link . $menu;

	}


	public static function fix_legacy_nav( $menu ) {

		$menu = preg_replace( '/<ul class=[\'"]children[\'"]/', '<ul class="sub-menu"', trim( $menu ) ); //Change sub menu class
		$menu = preg_replace( '/<div class=[\'"]menu[\'"]>/', '', $menu, 1 ); //Remove opening <div>
		$menu = str_replace( '<ul>', '<ul class="menu">', $menu ); //Add menu class to main <ul>
		$menu = str_replace( 'current_page_item', 'current_page_item current-menu-item', $menu ); //Add current-menu-item wherever current_page_item is to make legacy nav more consistent with wp_nav_menu()

		return substr( trim( $menu ), 0, - 6 ); //Remove the closing </div>

	}


}


class PadmaNavigationBlockOptions extends PadmaBlockOptionsAPI {

	public $tabs;
	public $inputs;


	function __construct($block_type_object){

		parent::__construct($block_type_object);

		$this->tabs = array(
			'nav-menu-content' => __('Content','padma'),
			'setup' => __('Setup','padma'),
			'home-link' => __('Home Link','padma'),
			'search' => __('Search','padma'),
			'orientation' => __('Orientation','padma'),
			'dropdowns' => __('Dropdowns','padma'),
		);

		$this->inputs = array(
			'setup' => array(
				'item-height' => array(
					'type' => 'slider',
					'name' => 'item-height',
					'label' => __('Navigation Item Height','padma'),
					'default' => 40,
					'slider-min' => 0,
					'slider-max' => 250,
					'slider-interval' => 1,
					'unit' => 'px'
				),

				'responsiveness-notice' => array(
					'name' => 'responsiveness-notice',
					'type' => 'notice',
					'notice' => __('You must have Responsive Grid enabled to take advantage of these options. Responsive Grid can be enabled under Setup &raquo; Responsive Grid in the Grid mode.','padma')
				),

				'responsive-method' => array(
					'type' => 'select',
					'name' => 'responsive-method',
					'label' => __('Responsive Method','padma'),
					'default' => 'vertical',
					'options' => array(
						'vertical' => __('Vertical Menu','padma'),
						'slide-out' => __('Horizontal Slideout','padma'),
						'select' => __('Basic Select Input','padma')
					),
					'toggle' => array(
						'vertical' => array(
							'show' => array(
								'#input-responsive-menu-label',
								'#input-responsive-menu-label-position',
								'#input-use-responsive-menu-breakpoint'
							),
							'hide' => array(
								'#input-slide-out-menu-position',
								'#input-responsive-select',
							)
						),
						'slide-out' => array(
							'show' => array(
								'#input-responsive-menu-label',
								'#input-responsive-menu-label-position',
								'#input-use-responsive-menu-breakpoint',
								'#input-slide-out-menu-position'
							),
							'hide' => array(
								'#input-responsive-select',
							)
						),
						'select' => array(
							'show' => array(
								'#input-responsive-select',
							),
							'hide' => array(
								'#input-responsive-menu-label',
								'#input-responsive-menu-label-position',
								'#input-use-responsive-menu-breakpoint',
								'#input-responsive-menu-breakpoint',
								'#input-slide-out-menu-position'
							)
						)
					)
				),

				'responsive-menu-label' => array(
					'type' => 'text',
					'name' => 'responsive-menu-label',
					'label' => 'Responsive Menu Label',
					'default' => __('Menu','padma')
				),

				'responsive-menu-label-position' => array(
					'type' => 'select',
					'name' => 'responsive-menu-label-position',
					'label' => __('Responsive Menu Label Position','padma'),
					'options' => array(
						'left' => __('Left','padma'),
						'right' => __('Right','padma'),
						'center' => __('Center','padma')
					),
					'default' => 'right'
				),

				'slide-out-menu-position' => array(
					'type' => 'select',
					'name' => 'slide-out-menu-position',
					'label' => __('Slide Out Position','padma'),
					'options' => array(
						'left' => __('Left','padma'),
						'right' => __('Right','padma')
					),
					'default' => 'left'
				),

				'use-responsive-menu-breakpoint' => array(
					'type' => 'checkbox',
					'name' => 'use-responsive-menu-breakpoint',
					'label' => __('Use Responsive Menu Breakpoint','padma'),
					'tooltip' => __('If this is unchecked then the slide out or vertical navigation will show for all devices.','padma'),
					'default' => true,
					'toggle' => array(
						'true' => array(
							'show' => '#input-responsive-menu-breakpoint'
						),
						'false' => array(
							'hide' => '#input-responsive-menu-breakpoint'
						)
					)
				),

				'responsive-menu-breakpoint' => array(
					'type' => 'slider',
					'name' => 'responsive-menu-breakpoint',
					'label' => __('Menu Breakpoint','padma'),
					'tooltip' => __('This is the device width at which the navigation block should hide its own navigation and display the slide out or vertical navigation.','padma'),
					'unit' => 'px',
					'default' => 600,
					'slider-min' => 200,
					'slider-max' => 1200
				),

				'responsiveness-notice' => array(
					'name' => 'responsiveness-notice',
					'type' => 'notice',
					'notice' => __('You must have Responsive Grid enabled to take advantage of these options.  Responsive Grid can be enabled under Setup &raquo; Responsive Grid in the Grid mode.','padma')
				),

				'responsive-select' => array(
					'type' => 'checkbox',
					'name' => 'responsive-select',
					'label' => __('Responsive Select','padma'),
					'default' => true,
					'tooltip' => __('When enabled, your navigation will turn into a mobile-friendly select menu when your visitors are viewing your site on a mobile device (phones, not tablets).','padma')
				)
			),

			'home-link' => array(
				'hide-home-link' => array(
					'type' => 'checkbox',
					'name' => 'hide-home-link',
					'label' => __('Hide Home Link','padma'),
					'default' => false,
					'tooltip' => __('If you do not have a static page as the front page, Padma will add a home item to the navigation menu by default.','padma'),
				),
				'home-link-text' => array(
					'name' => 'home-link-text',
					'label' => __('Home Link Text','padma'),
					'type' => 'text',
					'tooltip' => __('If you would like the link to your homepage to say something other than <em>Home</em>, enter it here!','padma'),
					'default' => __('Home','padma')
				)
			),

			'search' => array(
				'enable-nav-search' => array(
					'type' => 'checkbox',
					'name' => 'enable-nav-search',
					'label' => __('Enable Navigation Search','padma'),
					'default' => false,
					'tooltip' => __('If you wish to have a simple search form in the navigation bar, then check this box. <em><strong>Note:</strong> the search form will not show if the Vertical Navigation option is enabled for this block.</em>','padma')
				),

				'nav-search-position' => array(
					'type' => 'select',
					'name' => 'nav-search-position',
					'label' => __('Search Position','padma'),
					'default' => 'right',
					'options' => array(
						'left' => __('Left','padma'),
						'right' => __('Right','padma')
					),
					'tooltip' => __('If you would like the navigation search input to snap to the left instead of the right, you can use this option.','padma')
				),

				'nav-search-placeholder' => array(
					'type' => 'text',
					'name' => 'nav-search-placeholder',
					'label' => __('Search Placeholder','padma'),
					'default' => __('Type to search, then press enter','padma'),
					'tooltip' => __('This will be the text inside the search input telling the visitor how to interact with the search input.','padma')
				)
			),

			'orientation' => array(
				'alignment' => array(
					'type' => 'select',
					'name' => 'alignment',
					'label' => __('Alignment','padma'),
					'default' => 'left',
					'options' => array(
						'left' => __('Left','padma'),
						'right' => __('Right','padma'),
						'center' => __('Center','padma')
					)
				),

				'vert-nav-box' => array(
					'type' => 'checkbox',
					'name' => 'vert-nav-box',
					'label' => __('Vertical Navigation','padma'),
					'default' => false,
					'tooltip' => __('Instead of showing navigation horizontally, you can make the navigation show vertically. <em><strong>Note:</strong> You may have to resize the block to make the navigation items fit correctly.</em>','padma')
				)
			),

			'dropdowns' => array(
				'effect' => array(
					'type' => 'select',
					'name' => 'effect',
					'label' => __('Drop Down Effect','padma'),
					'default' => 'fade',
					'options' => array(
						'none' => __('No Effect','padma'),
						'fade' => __('Fade','padma'),
						'slide' => __('Slide','padma')
					),
					'tooltip' => __('This is the effect that will be used when the drop downs are shown and hidden.','padma')
				),

				'hover-intent' => array(
					'type' => 'checkbox',
					'name' => 'hover-intent',
					'label' => __('Hover Intent','padma'),
					'default' => true,
					'tooltip' => __('Hover Intent makes it so if a navigation item with a drop down is hovered then the drop down will only be shown if the visitor has their mouse over the item for more than a split second.<br /><br />This reduces drop-downs from sporatically showing if the visitor makes fast movements over the navigation.','padma')
				)
			),
		);
	}


	function modify_arguments( $args = false ) {

		$this->tab_notices['nav-menu-content'] = sprintf( __('To add items to this navigation menu, go to <a href="%s" target="_blank">WordPress Admin &raquo; Appearance &raquo; Menus</a>. Then, create a menu and assign it to <em>%s</em> in the <strong>Theme Locations</strong> box.','padma'), admin_url( 'nav-menus.php' ), PadmaBlocksData::get_block_name( $args['blockID'] ));

		if ( $block_height = PadmaBlocksData::get_block_height( $args['blockID'] ) ) {
			$this->inputs['setup']['item-height']['default'] = $block_height;
		}

		if ( PadmaResponsiveGrid::is_enabled() ) {
			unset( $this->inputs['setup']['responsiveness-notice'] );
		}

	}

}