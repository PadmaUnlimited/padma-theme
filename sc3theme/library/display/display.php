<?php
class BloxDisplay {
	

	public static $plugin_template_generic_content = null;

	
	public static function init() {

		if ( is_admin() )
			return;
		
		Blox::load(array(
			'display/head' => true,
			'display/grid-renderer',
			'display/layout-renderer'
		));
				
		add_filter('body_class', array(__CLASS__, 'body_class'));

		if ( BloxRoute::is_visual_editor_iframe() ) {

			header( 'cache-control: private, max-age=0, no-cache' );

			Blox::load('visual-editor', 'VisualEditor');
            Blox::load('visual-editor/dummy-content', 'IframeDummyContent');

			BloxAdminBar::remove_admin_bar();

		}

		/* If it's a plugin template, then route all of the content to the content block */
		add_action('get_header', array(__CLASS__, 'handle_plugin_template'), 1);
	
	}
	
	
	public static function layout() {
	
		get_header();
		
		self::grid();
						
		get_footer();
		
	}
	

		public static function grid() {

			echo "\n\n";
							
				if ( current_theme_supports('blox-grid') ) {
			
					$layout = new BloxLayoutRenderer;
					$layout->display();
							
				} else {
				
					echo '<div class="alert alert-yellow"><p>The Blox Grid is not supported in this Child Theme.</p></div>';
				
				}
				
			echo "\n\n";

		}

	/**
	 * Plugin Templateh andling sstem.
	 * 
	 * If the template file isn't Blox's index.php, then fetch the contents and put them into the Content Block 
	 **/
		public static function handle_plugin_template() {

			if ( !self::is_plugin_template() )
				return false;

			add_action('blox_whitewrap_open', array(__CLASS__, 'blox_whitewrap_open_ob_start'));
			add_action('wp_footer', array(__CLASS__, 'blox_close_ob_get_clean'), -99999);

		}


		public static function is_plugin_template() {

			global $template;

			/* Replace backslashes with forward slashes for Windows compatibility */
			if ( strpos(str_replace('\\', '/', $template), WP_PLUGIN_DIR) !== false || !$template )
				return true;

			return false;

		}


			public static function blox_whitewrap_open_ob_start() {

				ob_start();

			}


			public static function blox_close_ob_get_clean() {

				self::$plugin_template_generic_content = ob_get_clean();

				/* Hook generic content */
					add_action('generic_content', array(__CLASS__, 'display_generic_content'));

				/* Display grid in between header and footer */
					self::grid();

			}


				public static function display_generic_content() {

					echo self::$plugin_template_generic_content;

				}
	/* End Plugin Template Handling System */


	
	/**
	 * Assembles the classes for the body element.
	 **/
	public static function body_class($c) {

		global $wp_query, $authordata;
		
		$c[] = 'custom';

		/* User Agents */
			if ( !BloxCompiler::is_plugin_caching() ) {
				
				$user_agent = $_SERVER['HTTP_USER_AGENT'];
			
				/* IE */
				if ( $ie_version = blox_is_ie() ) {
									
					$c[] = 'ie';
					$c[] = 'ie' . $ie_version;
					
				}
				
				/* Modern Browsers */
				if ( stripos($user_agent, 'Safari') !== false )
					$c[] = 'safari';
					
				elseif ( stripos($user_agent, 'Firefox') !== false )
					$c[] = 'firefox';
					
				elseif ( stripos($user_agent, 'Chrome') !== false )
					$c[] = 'chrome';
					
				elseif ( stripos($user_agent, 'Opera') !== false )
					$c[] = 'opera';

				/* Rendering Engines */
				if ( stripos($user_agent, 'WebKit') !== false )
					$c[] = 'webkit';
					
				elseif ( stripos($user_agent, 'Gecko') !== false )
					$c[] = 'gecko';
					
				/* Mobile */
				if ( stripos($user_agent, 'iPhone') !== false )
					$c[] = 'iphone';
				
				elseif ( stripos($user_agent, 'iPod') !== false )
					$c[] = 'ipod';
				
				elseif ( stripos($user_agent, 'iPad') !== false )
					$c[] = 'ipad';
					
				elseif ( stripos($user_agent, 'Android') !== false )
					$c[] = 'android';
				
			}
		/* End User Agents */		

		/* Responsive Grid */
			if ( BloxResponsiveGrid::is_enabled() )
				$c[] = 'responsive-grid-enabled';

			if ( BloxResponsiveGrid::is_active() )
				$c[] = 'responsive-grid-active';

		/* Pages */			
			if ( is_page() && isset($wp_query->post) && isset($wp_query->post->ID) ) {
								
				$c[] = 'pageid-' . $wp_query->post->ID;
				$c[] = 'page-slug-' . $wp_query->post->post_name;
							
			}

		/* Posts & Pages */
			if ( is_singular() && isset($wp_query->post) && isset($wp_query->post->ID)  ) {

				//Add the custom classes from the meta box
				if ( $custom_css_class = BloxLayoutOption::get($wp_query->post->ID, 'css-class', null, true) ) {
					
					$custom_css_classes = str_replace('  ', ' ', str_replace(',', ' ', esc_attr(strip_tags($custom_css_class))));

					$c = array_merge($c, array_filter(explode(' ', $custom_css_classes)));
					
				}

			}

		/* Layout IDs, etc */
		$c[] = 'layout-' . str_replace(BloxLayout::$sep, '-', BloxLayout::get_current());
		$c[] = 'layout-using-' . str_replace( BloxLayout::$sep, '-', BloxLayout::get_current_in_use());

		if ( BloxRoute::is_visual_editor_iframe() )
			$c[] = 've-iframe';
		
		if ( blox_get('ve-iframe-mode') && BloxRoute::is_visual_editor_iframe() )
			$c[] = 'visual-editor-mode-' . blox_get('ve-iframe-mode');

		if ( !current_theme_supports('blox-design-editor') )
			$c[] = 'design-editor-disabled';

		$c = array_unique(array_filter($c));

		return $c;
		
	}

	
	public static function html_open() {
				
		echo apply_filters('blox_doctype', '<!DOCTYPE HTML>');
		echo '<html '; language_attributes(); echo '>' . "\n";
		
		do_action('blox_html_open');
		
		echo "\n" . '<head>' . "\n";

			echo '<meta charset="' . get_bloginfo('charset') . '" />' . "\n";
			echo '<link rel="profile" href="http://gmpg.org/xfn/11" />' . "\n";

	}


	public static function html_close() {
		
		echo "\n\n";
		
		do_action('blox_html_close');

		echo "\n" . '</html>';
		
	}
	
	
	public static function body_open() {	
			
		echo "\n" . '</head>' . "\n\n";
		
		echo '<body '; body_class(); echo ' itemscope itemtype="http://schema.org/WebPage">' . "\n\n";

		do_action('blox_body_open');

		echo "\n" . '<div id="whitewrap">' . "\n";
		
		do_action('blox_whitewrap_open');

		do_action('blox_page_start');
		
	}


	public static function body_close() {
		
		echo "\n\n";
		
		do_action('blox_whitewrap_close');

		echo '</div>' . "\n";
		
		do_action('blox_body_close');
		
		echo "\n" . '</body>';
			
	}
	
	
}