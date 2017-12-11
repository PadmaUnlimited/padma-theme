<?php
class BloxCompiler {
	
	
	private static $accepted_formats = array('css', 'js');


	public static function init() {

		add_action('after_setup_theme', array(__CLASS__, 'maybe_flush_cache'));

		add_action('blox_visual_editor_save', array(__CLASS__, 'signal_flush_cache'));
		add_action('blox_visual_editor_reset_layout', array(__CLASS__, 'signal_flush_cache'));
		add_action('blox_visual_editor_delete_template', array(__CLASS__, 'signal_flush_cache'));
		add_action('blox_visual_editor_assign_template', array(__CLASS__, 'signal_flush_cache'));
		add_action('blox_visual_editor_unassign_template', array(__CLASS__, 'signal_flush_cache'));

		add_action('blox_switch_skin', array(__CLASS__, 'signal_flush_cache'));
		add_action('switch_theme', array(__CLASS__, 'signal_flush_cache'));

		add_action('blox_db_upgrade', array(__CLASS__, 'signal_flush_cache'));
		add_action('blox_activation', array(__CLASS__, 'signal_flush_cache'));
		add_action('blox_global_reset', array(__CLASS__, 'signal_flush_cache'));

		add_action('blox_snapshot_rollback', array(__CLASS__, 'signal_flush_cache'));

	}


	/**
	 * @param string
	 * @param string
	 * @param mixed
	 * @param bool
	 * 
	 * @uses BloxCompiler::enqueue_file()
	 * 
	 * @return bool
	 **/
	public static function register_file($args) {

		$defaults = array(
			'name' => null,
			'format' => null,
			'fragments' => array(),
			'dependencies' => array(),
			'footer-js' => true,
			'enqueue' => true,
			'iframe-cache' => false,
			'output-inline' => false
		);
		
		$args = array_merge($defaults, $args);

		if ( !$cache = get_transient('bt_compiler_template_' . BloxOption::$current_skin) )
			$cache = array();

		if ( is_ssl() )
			$args['name'] = $args['name'] . '-https';

		$args['fragments'] = array_map('blox_change_to_unix_path', $args['fragments']);
		$args['dependencies'] = array_map('blox_change_to_unix_path', $args['dependencies']);

		if ( !in_array($args['format'], self::$accepted_formats) )
			wp_die('<strong>' . $args['format'] .'</strong> is not an accepted filetype for the BloxCompiler class.');

		/* Prep possibly already cached settings for comparison */
			$already_cached = isset($cache[$args['name']]) ? $cache[$args['name']] : array();

			unset($already_cached['filename']);
			unset($already_cached['hash']);

		/* If file is not registered or fragments are not the same, add it to the DB. */
			if ( $already_cached != $args ) {
				
				$cache[$args['name']] = $args;

				$cache[$args['name']]['filename'] = null;
				$cache[$args['name']]['hash'] = null;

				//Update cache option
				if ( !set_transient('bt_compiler_template_' . BloxOption::$current_skin, $cache) )
					return false;

			}

		/* Output or Enqueue script */
			if ( $args['output-inline'] && $args['format'] != 'js' ) {

				return add_action('wp_print_styles', create_function('', 'return BloxCompiler::output_inline(\'' . $args['name'] . '\');'));

			} else if ( $args['enqueue'] ) {

				return self::enqueue_file($args['name'], $args['footer-js']);

			}

		return true;

	}


	public static function output_inline($file) {

		$cache = get_transient('bt_compiler_template_' . BloxOption::$current_skin);

		if ( !isset($cache[$file]) )
			return false;

		echo "\n\n" . '<style type="text/css" id="' . $cache[$file]['name'] . '">' . "\n" . self::combine_fragments($cache[$file]) . "\n" . '</style>' . "\n\n";

		return true;

	}
	
	/**
	 * @param string
	 * 
	 * @return string
	 **/
	public static function enqueue_file($file, $footer_js = true) {
				
		$cache = get_transient('bt_compiler_template_' . BloxOption::$current_skin);

		if ( $cache[$file]['format'] == 'js' )
			return wp_enqueue_script('blox-' . $file, self::get_url($file), false, false, false, $footer_js);
		elseif ( $cache[$file]['format'] == 'css' )
			return wp_enqueue_style('blox-' . $file, self::get_url($file));
			
		return false;	
			
	}
	
	
	/**
	 * @param string
	 * 
	 * @return string
	 **/
	public static function get_url($file) {
				
		$cache = get_transient('bt_compiler_template_' . BloxOption::$current_skin);

		if ( is_ssl() && strpos($file, '-https') === false )
			$file = $file . '-https';									
										
		//If the file isn't in the DB at all								
		if ( !isset($cache[$file]) )
			return false;
													
		//If cache exists
		if ( 
			self::caching_enabled() /* Make sure caching is enabled and possible */
			&& blox_get('filename', $cache[$file]) /* Filename in DB must be present */
			&& file_exists(BLOX_CACHE_DIR . '/' . blox_get('filename', $cache[$file]))  /* Cached file must be present */
			&& !(BloxRoute::is_visual_editor_iframe() && !blox_get('iframe-cache', $cache[$file])) /* Either not be iframe or if is iframe, iframe-cache must be true */
		) {
									
			return apply_filters('blox_compiler_file_url', blox_cache_url() . '/' . blox_get('filename', $cache[$file]));
		
		//Cache doesn't exist	
		} else {
			
			//If file doesn't exist, but we can still cache, let's cache the damn thing.
			if ( self::caching_enabled() && !(BloxRoute::is_visual_editor_iframe() && !blox_get('iframe-cache', $cache[$file])) ) {
												
				return self::cache_file($file) ? self::get_url($file) : null;
			
			//No caching available, now we have to use fallback method.
			} else {

				$query_args = array(
					'blox-trigger' => 'compiler', 
					'file' => $file,
					'layout-in-use' => BloxLayout::get_current_in_use(),
					'rand' => rand()
				);

				if ( BloxRoute::is_visual_editor_iframe() ) {
					$query_args['visual-editor-open'] = 'true';
				}

				if ( BloxRoute::is_visual_editor_iframe() && blox_get('ve-preview') )
					$query_args['ve-preview'] = 'true';

				if ( BloxRoute::is_theme_preview() ) {
					$query_args['preview'] = '1';
					$query_args['template'] = 'blox';
					$query_args['stylesheet'] = 'blox';
					$query_args['preview_iframe'] = '1';
					$query_args['TB_iframe'] = 'true';
				}

				return apply_filters('blox_compiler_trigger_url', add_query_arg($query_args, home_url('/')));
								
			}
						
		}
		
	}
	
	
	/**
	 * @param string
	 * 
	 * @return bool
	 **/
	public static function cache_file($file) {
				
		$cache = get_transient('bt_compiler_template_' . BloxOption::$current_skin);
		
		//Get the current layout here directly and set is as GET since the output trigger can use POST, but this cannot.
		$_GET['layout-in-use'] = BloxLayout::get_current_in_use(); 
		$_GET['compiler-cache'] = true;
		
		$content = self::combine_fragments($cache[$file]);
		
		//If existing cache file exists, delete it.		
		self::delete_cache_file($cache[$file]['filename']);

		//MD5 the contents that way we can check for differences down the road
		$cache[$file]['hash'] = md5($content);
		$cache[$file]['filename'] = $file . '-' . substr($cache[$file]['hash'], 0, 7) . '.' . $cache[$file]['format'];

		//Build file
		$file_handle = @fopen(BLOX_CACHE_DIR . '/' . $cache[$file]['filename'], 'w');
		
		if ( !@fwrite($file_handle, $content) )
			return false;
	
		@chmod(BLOX_CACHE_DIR . '/' . $cache[$file]['filename'], 0644);
			
		@fclose($file_handle);

		set_transient('bt_compiler_template_' . BloxOption::$current_skin, $cache);

		return true;		
				
	}
	

	/**
	 * @return void
	 **/
	public static function output_trigger() {
		
		$file = blox_get('file');
		
		//No GET parameter set		
		if ( !$file )
			return false;

		$cache = get_transient('bt_compiler_template_' . BloxOption::$current_skin);

		//File does not exist
		if ( !isset($cache[$file]))
			return;
			
		$format = $cache[$file]['format'];
		$expires = 60 * 60 * 24 * 30;
				
		header("Pragma: public");
		header("Cache-Control: max-age=".$expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
		
		if ( $format == 'css' )
			header("Content-type: text/css");
		elseif ( $format == 'js' )
			header("content-type: application/x-javascript");
										
		echo self::combine_fragments($cache[$file]);
		
 	}


	/**
	 * @param array
	 * @param string
	 **/
	public static function combine_fragments($file) {
				
		extract($file);		

		$num_fragments = (int)count($fragments);

		$data = '';
		
		//Load dependencies if there are dependents
		if ( is_array($dependencies) && count($dependencies) > 0 ) {
			
			foreach ( $dependencies as $dependent ) {
				
				if ( !is_file($dependent) )
					continue;
					
				include_once $dependent;
				
			}
			
		}

		//Go through and merge the fragments
		foreach ( $fragments as $fragment_key => $fragment ) {
					
			//Determine if it's a function or file
			if ( !is_array($fragment) && strpos($fragment, '.') !== false && strpos($fragment, '()') === false && file_exists($fragment) ) {
				
				if ( filesize($fragment) === 0 ) 
					continue;

				$temp_handler = fopen($fragment, 'r');
				$data .= fread($temp_handler, filesize($fragment));
				fclose($temp_handler);
				
			//It's a function	
			} else {
				
				//Remove unneeded paratheses if is a string
				if ( is_string($fragment) )
					$fragment = str_replace('()', '', $fragment);
				
				//Check if method or function
				if ( !is_callable($fragment) ) 
					continue;
					
				$data .= call_user_func($fragment);
				
			}	
					
			if ( $format == 'js' && count($fragments) > 1 )
				$data .= "\n\n;";
			else
				$data .= "\n\n";
			
		}
		
		return self::format_content($data, $file);
		
	}
	
	
	/**
	 * @param string
	 * @param string
	 **/
	public static function format_content($content, $file) {
		
		extract($file);
		
		if ( $format == 'css' ) {
			
			$content = self::strip_whitespace($content);
			
		}
	
		$search = array(
			'%BLOX_URL%',
			'%BLOX_LIBRARY_URL%',
			'%VISUALEDITOR%',
			'%SITE_URL%',
			'%HOME_URL%'
		);

		$replace = array(
			blox_url(),
			blox_url() . '/library',
			blox_url() . '/library/visual-editor',
			site_url(),
			home_url()
		);

		$content = str_replace($search, $replace, $content);

		//SSL URL fixing
		if ( is_ssl() || strpos($file['name'], '-https') !== false )
			$content = str_replace('http://', 'https://', $content);

		return $content;
		
	}


		public static function strip_whitespace($content) {

			if ( defined('BLOX_COMPILER_STRIP_WHITESPACE') && BLOX_COMPILER_STRIP_WHITESPACE === false )
				return $content;

			$replace = array(
				"#/\*.*?\*/#s" => '',  // Strip comments.
				"#\s\s+#"      => ' ', // Strip excess whitespace.
			);
			
			$search = array_keys($replace);
			$content = preg_replace($search, $replace, $content);

			$replace = array(
				": "  => ":",
				"; "  => ";",
				" {"  => "{",
				" }"  => "}",
				", "  => ",",
				"{ "  => "{",
				";}"  => "}", // Strip optional semicolons.
				",\n" => ",", // Don't wrap multiple selectors.
				"\n}" => "}", // Don't wrap closing braces.
				"} "  => "}\n", // Put each rule on it's own line.
				"\n" => "" //Take out all line breaks
			);

			$search = array_keys($replace);

			return trim(str_replace($search, $replace, $content));

		}


	/**
	 * @return bool
	 **/
	public static function caching_enabled() {
		
		if ( defined('BLOX_DISABLE_CACHE') && BLOX_DISABLE_CACHE === true )
			return false;
			
		if ( defined('BLOX_FORCE_CACHE') && BLOX_FORCE_CACHE === true )
			return true;
		
		if ( defined('WP_DEBUG') && WP_DEBUG === true )
			return false;
			
		if ( BloxOption::get('disable-caching') )
			return false;

		if ( !self::can_cache() )
			return false;

		return true;
		
	}
	
	
	/**
	 * @return bool
	 **/
	public static function can_cache() {
		
		if ( !is_dir(BLOX_CACHE_DIR) || !is_writable(BLOX_CACHE_DIR) )
			return false;

		return true;
		
	}


	/**
	 * @return bool
	 */
	public static function flush_cache() {
		
		if ( self::can_cache() ) {
			
			delete_transient('bt_compiler_template_' . BloxOption::$current_skin);

			$no_delete = array(
				'..',
				'.'
			);
				
			if ( $handle = opendir(BLOX_CACHE_DIR) ) {
			
			    while (false !== ($file = readdir($handle)) ) {

					if ( in_array($file, $no_delete) )
						continue;
					
					@unlink(BLOX_CACHE_DIR . '/' . $file);
		
			    }
		
			    closedir($handle);
		
			}
			
		}

		wp_cache_flush();

		do_action('blox_flush_cache');

		delete_transient('blox_signal_flush_cache');

	}


	public static function signal_flush_cache() {

		self::flush_plugin_caches();

		return set_transient('blox_signal_flush_cache', true);

	}


	public static function maybe_flush_cache() {

		if ( get_transient('blox_signal_flush_cache') ) {
			return self::flush_cache();
		}

		return false;

	}

	
	/**
	 * @param string
	 * @param string
	 * 
	 * @return bool
	 **/
	public static function delete_cache_file($filename) {

		if ( !$filename || !file_exists(BLOX_CACHE_DIR . '/' . $filename) )
			return false;
		
		return @unlink(BLOX_CACHE_DIR . '/' . $filename);
		
	}

	
	/**
	 * Check if W3 Total Cache or if WP Super Cache are running.
	 *
	 * @return bool
	 **/
	public static function is_plugin_caching() {
		
		if ( class_exists('W3_Plugin_TotalCache') )
			return 'W3 Total Cache';

		elseif ( function_exists( 'rocket_clean_domain' ) )
			return 'WP Rocket';

		elseif ( function_exists('prune_super_cache'))
			return 'WP Super Cache';

		elseif ( class_exists( 'WpeCommon' ) )
			return 'WPEngine';

		elseif ( class_exists('GD_System_Plugin_Cache_Purge') )
			return 'GoDaddy or MediaTemple';

		elseif ( isset($GLOBALS['quick_cache']) )
			return 'Quick Cache';

		else
			return false;
		
	}
	
	
	/**
	 * Flush Super Cache and W3 Total Cache
	 * 
	 * @return void
	 **/
	public static function flush_plugin_caches() {

		if ( function_exists('prune_super_cache') ) {
			
			global $cache_path;
			prune_super_cache($cache_path . 'supercache/', true );
			prune_super_cache($cache_path, true );
			
		}

		if ( class_exists('W3_Plugin_TotalCache') ) {
			
			if ( function_exists('w3_instance') )
				$w3_plugin_totalcache = w3_instance('W3_Plugin_TotalCache');
			elseif ( is_callable(array('W3_Plugin_TotalCache', 'instance')) )
				$w3_plugin_totalcache = W3_Plugin_TotalCache::instance();

			if ( method_exists($w3_plugin_totalcache, 'flush') )
				$w3_plugin_totalcache->flush();
			elseif ( method_exists($w3_plugin_totalcache, 'flush_all') )
				$w3_plugin_totalcache->flush_all();

			if ( function_exists('w3tc_flush_all') ) {
				w3tc_flush_all();
			}

			/* Flush varnish */
			if ( function_exists('w3tc_varnish_flush') )
				w3tc_varnish_flush();
		
		}

		if ( class_exists( 'WpeCommon' ) ) {

			if ( method_exists( 'WpeCommon', 'purge_memcached' ) )
				WpeCommon::purge_memcached();

			if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) )
				WpeCommon::purge_varnish_cache();

		}

		if ( class_exists('GD_System_Plugin_Cache_Purge') ) {

			if ( method_exists( 'GD_System_Plugin_Cache_Purge', 'do_ban_cache' ) )
				GD_System_Plugin_Cache_Purge::do_ban_cache();

		}

		if ( function_exists('rocket_clean_domain') ) {
			rocket_clean_domain();
		}

		if ( isset( $GLOBALS['quick_cache'] ) && method_exists($GLOBALS['quick_cache'], 'auto_clear_cache') ) {

			$GLOBALS['quick_cache']->auto_clear_cache();

		}
		
	}

}