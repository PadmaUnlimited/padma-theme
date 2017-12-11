<?php
class BloxHead {
	
	/** 
	 * Set up hooks for <head>
	 **/
	public static function init() {
		
		if ( !BloxRoute::is_display() )
			return false;

		if ( function_exists('wp_get_document_title') ) {
			add_filter('pre_get_document_title', array('BloxSEO', 'output_title'));
		} else {
			add_filter('wp_title', array('BloxSEO', 'output_title'));
		}

		//Remove actions
		remove_action('wp_head', 'wp_print_styles', 8);
		remove_action('wp_head', 'wp_print_head_scripts', 9);
		remove_action('wp_head', 'rel_canonical');
		remove_action('wp_head', 'feed_links', 2);
		remove_action('wp_head', 'feed_links_extra', 3);
				
		//Set Up Actions
		if ( ! function_exists( '_wp_render_title_tag' ) ) {
			add_action( 'wp_head', array( __CLASS__, 'print_title' ), 1 );
		}

		add_action('wp_enqueue_scripts', array(__CLASS__, 'register_files'), 1);
		add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'), 1);
		
		add_action('wp_head', array('BloxSEO', 'output_meta'), 2);
		add_action('wp_head', array(__CLASS__, 'print_stylesheets'), 7);
		add_action('wp_head', array(__CLASS__, 'print_scripts'), 8);
		add_action('wp_head', array(__CLASS__, 'extras'), 9);
		
		add_action('blox_stylesheets', 'wp_print_styles');
		add_action('blox_stylesheets', array( __CLASS__, 'visual_editor_live_css' ), 11);
		add_action('blox_stylesheets', array(__CLASS__, 'child_theme_stylesheet'), 12);
		
		add_action('blox_scripts', 'wp_print_head_scripts');
		add_action('blox_scripts', array(__CLASS__, 'add_standards_compliance_js'));
		add_action('blox_scripts', array(__CLASS__, 'header_scripts'));
		
		add_action('blox_seo_meta', 'rel_canonical');
		
		add_action('blox_head_extras', 'feed_links');
		add_action('blox_head_extras', 'feed_links_extra');
		
		add_action('blox_body_close', array(__CLASS__, 'footer_scripts'), 15);
		
		add_action('wp_head', array(__CLASS__, 'favicon'), 9);
		
		add_filter('style_loader_src', array(__CLASS__, 'remove_dependency_query_vars'));
		add_filter('script_loader_src', array(__CLASS__, 'remove_dependency_query_vars'));
		
	}
	
	
	public static function print_title() {
		
		echo "\n<!-- Title -->\n<title>" . wp_title(false, false) . '</title>';
		
	}
	
	
	/**
	 * All general CSS and JS used across the site will be registered and/or enqueued here.
	 * 
	 * @return void
	 **/
	public static function register_files() {

		self::register_general_css();

		self::register_layout_css();

		self::register_responsive_css();
		
		self::register_visual_editor_css();
						
	}


		public static function register_general_css() {

			$general_css_fragments = array();
			
			/* Basic CSS */
				if ( current_theme_supports('blox-reset-css') )
					$general_css_fragments['reset.css'] = BLOX_LIBRARY_DIR . '/media/css/reset.css';
				
				if ( current_theme_supports('blox-grid') )
					$general_css_fragments['grid.css'] = BLOX_LIBRARY_DIR . '/media/css/grid.css';
							
				if ( current_theme_supports('blox-block-basics-css') )
					$general_css_fragments['block-basics.css'] = BLOX_LIBRARY_DIR . '/media/css/block-basics.css';
					
				if ( current_theme_supports('blox-content-styling-css') ) {
					
					$general_css_fragments['content-styling.css'] = BLOX_LIBRARY_DIR . '/media/css/content-styling.css';
					$general_css_fragments['alerts.css'] = BLOX_LIBRARY_DIR . '/media/css/alerts.css';
					
				}

			/* Block heights */
				$general_css_fragments['dynamic-block-heights'] = array('BloxDynamicStyle', 'block_heights');
				
			/* Design Editor CSS */
				if ( current_theme_supports('blox-design-editor') )
					$general_css_fragments['dynamic-design-editor'] = array('BloxDynamicStyle', 'design_editor');
			
			/* Allow filters to be applied to the general CSS fragments/dependencies before the count is made */
				$general_css_fragments = apply_filters('blox_general_css', $general_css_fragments);

			/* Live CSS */
				if ( current_theme_supports('blox-live-css') && BloxSkinOption::get('live-css') )
					$general_css_fragments['dynamic-live-css'] = array('BloxDynamicStyle', 'live_css');

			/* Have a separate filter after Live CSS is applied that way injecting into the 'blox_general_css' filter will insert stuff before Live CSS */
				$general_css_fragments = apply_filters('blox_general_css_live_css_applied', $general_css_fragments);
					
				$general_css_dependencies = array_unique(apply_filters('blox_general_css_dependencies', array(
					BLOX_LIBRARY_DIR . '/media/dynamic/style.php'
				)));

			/* Handle regular requests. */
			if ( !BloxRoute::is_visual_editor_iframe('design') || !current_theme_supports('blox-design-editor') ) {

				BloxCompiler::register_file(array(
					'name' => 'general',
					'format' => 'css',
					'fragments' => $general_css_fragments,
					'dependencies' => $general_css_dependencies
				));

			/* Handle design editor requests by stripping out the design editor fragment and separating it for better skin importing */
			} else {

				$general_fragments_excluding_design_editor = $general_css_fragments;
				unset($general_fragments_excluding_design_editor['dynamic-design-editor']);
				unset($general_fragments_excluding_design_editor['dynamic-live-css']);

				BloxCompiler::register_file(array(
					'name' => 'general-excluding-design-editor',
					'format' => 'css',
					'iframe-cache' => true,
					'fragments' => $general_fragments_excluding_design_editor,
					'dependencies' => $general_css_dependencies
				));

				BloxCompiler::register_file(array(
					'name' => 'general-design-editor',
					'format' => 'css',
					'fragments' => array(
						array('BloxDynamicStyle', 'design_editor')
					),
					'dependencies' => $general_css_dependencies
				));

			}


		}


		public static function register_layout_css() {

			$current_layout_in_use = BloxLayout::get_current_in_use(); 
			$css_name = str_replace(BloxLayout::$sep, '-', 'layout-' . BloxLayout::get_current_in_use());

			$fragments = array();

			/* If the grid is supported, then include the wrapper, grid, and block heights */
			if ( current_theme_supports('blox-grid') ) {
				
				$fragments['dynamic-wrapper'] = array('BloxDynamicStyle', 'wrapper');
							
			}

			/* Include dynamic CSS from blocks such as navigation block or any block that has per-block CSS */
			$fragments['dynamic-block-css'] = array('BloxBlocks', 'output_block_dynamic_css');

			return BloxCompiler::register_file(array(
				'name' => $css_name,
				'format' => 'css',
				'fragments' => $fragments,
				'dependencies' => array(
					BLOX_LIBRARY_DIR . '/media/dynamic/style.php'
				)
			));

		}


		public static function register_responsive_css() {

			if ( !BloxResponsiveGrid::is_enabled() )
				return;
				
			/* CSS */
			BloxCompiler::register_file(array(
				'name' => 'responsive-grid',
				'format' => 'css',
				'iframe-cache' => true,
				'fragments' => array(
					array('BloxResponsiveGridDynamicMedia', 'content')
				),
				'dependencies' => array(
					BLOX_LIBRARY_DIR . '/media/dynamic/responsive-grid.php'
				)
			));
			
			/* JS */
			if ( BloxResponsiveGrid::is_active() && apply_filters('blox_responsive_fitvids', BloxSkinOption::get('responsive-video-resizing', false, true)) ) {
				
				wp_enqueue_script('fitvids', blox_url() . '/library/media/js/jquery.fitvids.js', array('jquery'));
				
				BloxCompiler::register_file(array(
					'name' => 'responsive-grid-js',
					'format' => 'js',
					'fragments' => array(
						array('BloxResponsiveGridDynamicMedia', 'fitvids')
					),
					'dependencies' => array(
						BLOX_LIBRARY_DIR . '/media/dynamic/responsive-grid.php'
					)
				));
				
			}

		}


		public static function register_visual_editor_css() {

			if ( !BloxRoute::is_visual_editor_iframe() )
				return;

			wp_enqueue_style('blox-ve-iframe', blox_url() . '/library/visual-editor/css/iframe.css');

		}
	
	
	/**
	 * Add extra junk into <head>.
	 **/
	public static function extras() {
	?>

<!-- Extras -->
<link rel="alternate" type="application/rss+xml" href="<?php echo get_bloginfo('rss2_url'); ?>" title="<?php echo get_bloginfo('name')?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
	<?php
		do_action('blox_head_extras');
	}
	

	/**
	 * Enqueues the Blox JS for blocks.
	 * 
	 * @uses wp_enqueue_script()
	 **/
	public static function enqueue_scripts() {

		if (
			is_singular() 
			&& comments_open(get_the_id())
			&& !(get_post_type() == 'page' && !BloxOption::get('allow-page-comments'))
		) {
			wp_enqueue_script('comment-reply');
                }
		
	}
	

	public static function print_scripts() {
		echo "\n<!-- Scripts -->\n";

		do_action('blox_scripts');
	}


	public static function add_standards_compliance_js() {

		$standards_compliance_js = apply_filters('blox_standards_compliance_js', '
<!--[if lt IE 9]>
<script src="' . blox_url() . '/library/media/js/html5shiv.js"></script>
<![endif]-->

<!--[if lt IE 8]>
<script src="' . blox_url() . '/library/media/js/ie8.js"></script>
<![endif]-->
');
		
		echo $standards_compliance_js;

	}

	
	/**
	 * Adds all of the links for the Blox stylesheets.
	 **/
	public static function print_stylesheets() {
		
		echo "\n\n" . '<!-- Stylesheets -->' . "\n";

		do_action('blox_stylesheets');
		
		echo "\n";
		
	}

	
	public static function child_theme_stylesheet() {
		
		/* If no child theme is active, then we won't use the style.css file. */
		if ( BLOX_CHILD_THEME_ACTIVE === false )
			return false;
			
		echo '<link rel="stylesheet" type="text/css" media="all" href="' . get_stylesheet_uri() . '" />';
			
		
	}

	
	public static function visual_editor_live_css() {
		
		if ( BloxRoute::is_visual_editor_iframe() )
			echo '<style id="live-css-holder">' . BloxSkinOption::get( 'live-css', false, null, false, false ) . '</style>';
		
	}
	

	/**
	 * Adds the link to the favicon to the <head>.
	 **/
	public static function favicon() {

		if ( !$favicon_url = BloxOption::get('favicon') )
			return null;
			
		if ( is_ssl() )
			$favicon_url = str_replace('http:', 'https:', $favicon_url);
			
		echo "\n\n<!-- Favicon -->\n" . '<link rel="shortcut icon" type="image/ico" href="' . $favicon_url . '" />' . "\n\n\n";
			
	}


	/**
	 * Callback function to be used for displaying the header scripts.
	 * 
	 * @uses blox_parse_php()
	 **/
	public static function header_scripts() {
		
		echo "\n" . blox_parse_php(BloxOption::get('header-scripts')) . "\n";
		
	}


	/**
	 * Callback function to be used for displaying the footer scripts.
	 * 
	 * @uses blox_parse_php()
	 **/
	public static function footer_scripts() {
		echo "\n" . blox_parse_php(BloxOption::get('footer-scripts')) . "\n";
	}
	
	
	/**
	 * To promote caching on browsers, Blox can tell WordPress to not put in the query variables on the style and script URLs.
	 **/
	public static function remove_dependency_query_vars($query) {
		
		if ( !BloxOption::get('remove-dependency-query-vars', 'general', false) && !BloxRoute::is_visual_editor_iframe() )
			return $query;
		
		return remove_query_arg('ver', $query);
		
	}


}