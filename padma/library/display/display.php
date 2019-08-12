<?php
class PadmaDisplay {
	

	public static $plugin_template_generic_content = null;
	
	public static function init() {

		if ( is_admin() )
			return;
		
		Padma::load(array(
			'display/head' => true,
			'display/grid-renderer',
			'display/layout-renderer'
		));
				
		add_filter('body_class', array(__CLASS__, 'body_class'));

		if ( PadmaRoute::is_visual_editor_iframe() ) {

			header( 'cache-control: private, max-age=0, no-cache' );

			Padma::load('visual-editor', 'VisualEditor');
            Padma::load('visual-editor/dummy-content', 'IframeDummyContent');

			PadmaAdminBar::remove_admin_bar();

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

		if ( current_theme_supports('padma-grid') ) {

			$layout = new PadmaLayoutRenderer;
			$layout->display();
	
		} else {

			echo '<div class="alert alert-yellow"><p>The Padma Grid is not supported in this Child Theme.</p></div>';

		}

	}

	/**
	 * Plugin Template handling system.
	 * 
	 * If the template file isn't Padma's index.php, then fetch the contents and put them into the Content Block 
	 **/
		public static function handle_plugin_template() {


			if ( !self::is_plugin_template() )
				return false;

			add_action('padma_whitewrap_open', array(__CLASS__, 'padma_whitewrap_open_ob_start'));
			add_action('wp_footer', array(__CLASS__, 'padma_close_ob_get_clean'), -99999);

		}


		public static function is_plugin_template() {

			global $template;

			/* Replace backslashes with forward slashes for Windows compatibility */
			if ( strpos(str_replace('\\', '/', $template), WP_PLUGIN_DIR) !== false || !$template )
				return true;

			return false;

		}


			public static function padma_whitewrap_open_ob_start() {

				ob_start();

			}


			public static function padma_close_ob_get_clean() {

				self::$plugin_template_generic_content = ob_get_clean();

				/* Hook generic content */
					add_action('generic_content', array(__CLASS__, 'display_generic_content'));

				/* Display grid in between header and footer */
					self::grid();

			}


				public static function display_generic_content() {

					echo self::$plugin_template_generic_content;

				}


		/**
		 *
		 * This methond allow to load the plugin template into the content block, the template need to be related to a CPT
		 *
		 */
		
		public static function load_plugin_template($template){

			global $post;

		    if (!$post)
		        return $template;
		    
		    $template_id = get_post_meta($post->ID, '_wp_page_template', true);

		    if(!$template_id)
		    	return $template;

			if(!PadmaOption::get('allow-plugin-templates', false))
				return $template;

			if(!PadmaDisplay::is_plugin_template())
				return $template;

			if(!file_exists($template))		
				return $template;

			add_action('padma_whitewrap_open', array(__CLASS__, 'padma_whitewrap_open_ob_start'));
			add_action('wp_footer', array(__CLASS__, 'padma_close_ob_get_clean'), -99999);

			load_template( $template );

			return $template;

		}
		
	/* End Plugin Template Handling System */


	
	/**
	 * Assembles the classes for the body element.
	 **/
	public static function body_class($c) {

		global $wp_query, $authordata;
		
		$c[] = 'custom';

		/* User Agents */
			if ( !PadmaCompiler::is_plugin_caching() ) {
				
				$user_agent = $_SERVER['HTTP_USER_AGENT'];
			
				/* IE */
				if ( $ie_version = padma_is_ie() ) {
									
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
			if ( PadmaResponsiveGrid::is_enabled() )
				$c[] = 'responsive-grid-enabled';

			if ( PadmaResponsiveGrid::is_active() )
				$c[] = 'responsive-grid-active';

		/* Pages */			
			if ( is_page() && isset($wp_query->post) && isset($wp_query->post->ID) ) {
								
				$c[] = 'pageid-' . $wp_query->post->ID;
				$c[] = 'page-slug-' . $wp_query->post->post_name;
							
			}

		/* Posts & Pages */
			if ( is_singular() && isset($wp_query->post) && isset($wp_query->post->ID)  ) {

				//Add the custom classes from the meta box
				if ( $custom_css_class = PadmaLayoutOption::get($wp_query->post->ID, 'css-class', null, true) ) {
					
					$custom_css_classes = str_replace('  ', ' ', str_replace(',', ' ', esc_attr(strip_tags($custom_css_class))));

					$c = array_merge($c, array_filter(explode(' ', $custom_css_classes)));
					
				}

			}

		/* Layout IDs, etc */
		$c[] = 'layout-' . str_replace(PadmaLayout::$sep, '-', PadmaLayout::get_current());
		$c[] = 'layout-using-' . str_replace( PadmaLayout::$sep, '-', PadmaLayout::get_current_in_use());

		if ( PadmaRoute::is_visual_editor_iframe() )
			$c[] = 've-iframe';
		
		if ( padma_get('ve-iframe-mode') && PadmaRoute::is_visual_editor_iframe() )
			$c[] = 'visual-editor-mode-' . padma_get('ve-iframe-mode');

		if ( !current_theme_supports('padma-design-editor') )
			$c[] = 'design-editor-disabled';

		$c = array_unique(array_filter($c));

		return $c;
		
	}

	
	public static function html_open() {
				
		echo apply_filters('padma_doctype', '<!DOCTYPE HTML>');
		echo '<html '; language_attributes(); echo '>';
		
		do_action('padma_html_open');
		
		echo '<head>';
		echo '<meta charset="' . get_bloginfo('charset') . '" />';
		echo '<link rel="profile" href="http://gmpg.org/xfn/11" />';

	}


	public static function html_close() {		
		
		do_action('padma_html_close');
		echo '</html>';
		
	}
	
	
	public static function body_open() {	
		
		$classes = '';

		echo '</head>';
		
		if( padma_get('ve-iframe') == 'true')
			$classes .= 'iframe-loaded';

		echo '<body '; body_class($classes); echo ' itemscope itemtype="http://schema.org/WebPage">';

		do_action('padma_body_open');
		
		echo '<div id="whitewrap">';
		
		do_action('padma_whitewrap_open');
		do_action('padma_page_start');
		
	}


	public static function body_close() {
		
		
		do_action('padma_whitewrap_close');
		echo '</div>';		
		do_action('padma_body_close');
		echo '</body>';
			
	}
	
	
}