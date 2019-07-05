<?php
/**
 * All of the global functions to be used everywhere in Padma.
 *
 * @package Padma
 * @author Padma Unlimited Team
 *
 **/

class Padma {


	public static $loaded_classes = array();


	/**
	 * Let's get Padma on the road!  We'll define constants here, run the setup function and do a few other fun things.
	 *
	 * @return void
	 *
	 **/
	
	public static function init() {

		global $wpdb;

		/* Legacy element default handling */
		$GLOBALS['padma_default_element_data'] = array();

		/* Define simple constants */
		if(!defined('THEME_FRAMEWORK')){
			define('THEME_FRAMEWORK', 'padma');			
		}
		if(!defined('PADMA_VERSION')){
			define('PADMA_VERSION', '1.1.25');			
		}

		/* Define directories */
		if(!defined('PADMA_DIR')){
			define('PADMA_DIR', get_template_directory());			
		}
		if(!defined('PADMA_LIBRARY_DIR')){
			define('PADMA_LIBRARY_DIR', padma_change_to_unix_path(PADMA_DIR . '/library'));
		}
		
		/* Dev Params	*/
		if(file_exists(PADMA_LIBRARY_DIR . '/dev-env.php')){
			require_once PADMA_LIBRARY_DIR . '/dev-env.php';
		}

		PadmaSettings::set_enviroment();

		/* Site URLs */
		if(!defined('PADMA_SITE_URL')){
			define('PADMA_SITE_URL', 'http://www.padmaunlimited.com/');
		}
		if(!defined('PADMA_API_URL')){
			define('PADMA_API_URL', 'https://api.padmaunlimited.com/');
		}
		if(!defined('PADMA_CDN_URL')){			
			define('PADMA_CDN_URL', 'https://cdn.padmaunlimited.com/');			
		}
		if(!defined('PADMA_DASHBOARD_URL')){
			define('PADMA_DASHBOARD_URL', 'https://dashboard.padmaunlimited.com/');			
		}
		if(!defined('PADMA_EXTEND_URL')){			
			define('PADMA_EXTEND_URL', PADMA_SITE_URL . 'extend');
		}



		/* Skins */
		if(!defined('PADMA_DEFAULT_SKIN')){
			define('PADMA_DEFAULT_SKIN', 'base');
		}
		

		/**
		 *
		 * Parse PHP
		 *
		 *	https://www.facebook.com/groups/padmaunlimitedEN/permalink/641292209707584/
		 * 	https://www.facebook.com/groups/padmaunlimitedES/permalink/383744532348603/
		 *
		 */
		if(!defined('PADMA_DISABLE_PHP_PARSING')){			
			define('PADMA_DISABLE_PHP_PARSING', false);
		}
		
		/* MySQL Table names */
		$wpdb->pu_blocks 		= $wpdb->prefix . 'pu_blocks';
		$wpdb->pu_wrappers 		= $wpdb->prefix . 'pu_wrappers';
		$wpdb->pu_snapshots 	= $wpdb->prefix . 'pu_snapshots';
		$wpdb->pu_layout_meta 	= $wpdb->prefix . 'pu_layout_meta';

		/* Handle child themes */
		if ( get_template_directory_uri() !== get_stylesheet_directory_uri() ) {

			if(!defined('PADMA_CHILD_THEME_ACTIVE')){
				define('PADMA_CHILD_THEME_ACTIVE', true);
			}
			if(!defined('PADMA_CHILD_THEME_DIR')){
				define('PADMA_CHILD_THEME_DIR', get_stylesheet_directory());				
			}
		} else {
			if(!defined('PADMA_CHILD_THEME_ACTIVE')){
				define('PADMA_CHILD_THEME_ACTIVE', false);				
			}
			if(!defined('PADMA_CHILD_THEME_DIR')){				
				define('PADMA_CHILD_THEME_DIR', null);
			}
		}

		/* Handle uploads directory and cache */
		$uploads = wp_upload_dir();

		if(!defined('PADMA_UPLOADS_DIR')){
			define('PADMA_UPLOADS_DIR', padma_change_to_unix_path($uploads['basedir'] . '/padma'));
		}
		if(!defined('PADMA_CACHE_DIR')){
			define('PADMA_CACHE_DIR', padma_change_to_unix_path(PADMA_UPLOADS_DIR . '/cache'));
		}

		/* Make directories if they don't exist */
		if ( !is_dir(PADMA_UPLOADS_DIR) )
			wp_mkdir_p(PADMA_UPLOADS_DIR);

		if ( !is_dir(PADMA_CACHE_DIR) )
			wp_mkdir_p(PADMA_CACHE_DIR);

		self::add_index_files_to_uploads();

		/* Load locale */
		load_theme_textdomain('padma', padma_change_to_unix_path(PADMA_LIBRARY_DIR . '/languages'));

		/* Add support for WordPress features */
		add_action('after_setup_theme', array(__CLASS__, 'add_theme_support'), 1);

		/* Setup */
		add_action('after_setup_theme', array(__CLASS__, 'child_theme_setup'), 2);
		add_action('after_setup_theme', array(__CLASS__, 'load_dependencies'), 3);
		add_action('after_setup_theme', array(__CLASS__, 'maybe_db_upgrade'));
		add_action('after_setup_theme', array(__CLASS__, 'initiate_updater'));


		// Activation hook
		add_action('after_switch_theme', array(__CLASS__, 'activate' ));

		// Deactivation hook
		add_action( 'switch_theme', array(__CLASS__, 'deactivate' ));

	}
	

	public static function activate(){

		// Allow automatic Theme Updates
		add_option('padma-disable-automatic-core-updates','0','','no');
		add_option('padma-disable-automatic-plugin-updates','0','','no');

	}

	public static function deactivate(){

		delete_option('padma-disable-automatic-core-updates');
		delete_option('padma-disable-automatic-plugin-updates');

	}


	public static function add_index_files_to_uploads() {

		$content = '<?php' . "\n" .
		'/* Disallow directory browsing */';

		$uploads_index = trailingslashit( PADMA_UPLOADS_DIR ) . 'index.php';
		$cache_index = trailingslashit( PADMA_CACHE_DIR ) . 'index.php';

		if ( ! is_file( $uploads_index  ) ) {

			$file_handle = @fopen( $uploads_index, 'w' );
			@fwrite( $file_handle, $content );
			@chmod( $uploads_index, 0644 );

		}

		if ( ! is_file( $cache_index ) ) {

			$file_handle = @fopen( $cache_index, 'w' );
			@fwrite( $file_handle, $content );
			@chmod( $cache_index, 0644 );

		}

	}


	/**
	 * Loads all of the required core classes and initiates them.
	 *
	 * Dependency array setup: class (string) => init (bool)
	 *
	 **/
	public static function load_dependencies() {

		//Load route right away so we can optimize dependency loading below
		Padma::load(array('common/route' => true));

		//Core loading set
		$dependencies = array(

			// abstract
			'abstract/notice',
			'abstract/api-panel',
			'abstract/web-fonts-api',

			'defaults/default-design-settings',

			'data/data-options' 			=> 'Option',
			'data/data-layout-options' 		=> 'LayoutOption',
			'data/data-skin-options',
			'data/data-blocks',
			'data/data-wrappers',
			'data/data-snapshots',
			'common/layout' 				=> true,
			'common/capabilities' 			=> true,
			'common/responsive-grid' 		=> true,
			'common/schema' 				=> true,
			'common/seo' 					=> true,
			'common/social-optimization' 	=> true,
			'common/feed' 					=> true,
			'common/compiler' 				=> true,
			'common/plugins' 				=> true,
			'common/templates',
			'common/http2-server-push'		=> true,
			'common/blocks-anywhere'		=> true,
			'admin/admin-bar' 				=> true,
			'blocks' 						=> true,
			'wrappers' 						=> true,
			'elements' 						=> true,
			'fonts/web-fonts-loader' 		=> true,
			'fonts/traditional-fonts',
			'fonts/google-fonts',
			'display' 						=> true,
			'widgets' 						=> true,

			/*
				Query Class
			*/
			'common/query',

			/*
				Notices
			*/			
			'common/notices' 		=> true,

			/*	
				Compatiblity
			*/
			'compatibility/woocommerce/compatibility-woocommerce' => 'CompatibilityWooCommerce',

			/*		Headway Classes support	*/
			'compatibility/headway/compatibility-headway'	=> true,

			/*		Compatiblity with Divi Builder */
			'compatibility/divi-builder/compatibility-divi-builder'	=> true,

			/*		Compatiblity with aMember Plugin */
			'compatibility/amember/compatibility-amember'	=> true,

			/*		Compatiblity with  WPML Multilingual CMS Plugin */
			'compatibility/wpml/compatibility-wpml'	=> true,

			/*	Gutenberg Compatibility	*/
			'common/gutenberg-blocks'	=> true,

		);
			
		//Child theme API
		if ( PADMA_CHILD_THEME_ACTIVE === true )
			$dependencies['api/api-child-theme'] = 'ChildThemeAPI';


		//Visual editor classes
		if ( PadmaRoute::is_visual_editor() || (defined('DOING_AJAX') && DOING_AJAX && strpos($_REQUEST['action'], 'padma') !== false ) )
			$dependencies['visual-editor'] = true;

		//Admin classes
		if ( is_admin() )
			$dependencies['admin'] = true;

		
		// Load stuff now
		Padma::load(apply_filters('padma_dependencies', $dependencies));
		do_action('padma_setup');
		
	}


	/**
	 * Tell WordPress that Padma supports its features.
	 **/
	public static function add_theme_support() {

		/* Padma Functionality */
		add_theme_support( 'padma-grid' );
		add_theme_support( 'padma-responsive-grid' );
		add_theme_support( 'padma-design-editor' );

		/* Padma CSS */
		add_theme_support( 'padma-reset-css' );
		add_theme_support( 'padma-live-css' );
		add_theme_support( 'padma-block-basics-css' );
		add_theme_support( 'padma-dynamic-block-css' );
		add_theme_support( 'padma-content-styling-css' );
		add_theme_support( 'padma-animation-css' );

		/* WordPress Functionality */
		add_theme_support( 'html5', array( 'caption' ) );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_theme_support( 'widgets' );
		add_theme_support( 'editor-style' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-logo' );


		/*	Gutenberg	*/
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'dark-editor-style' );
		add_theme_support( 'editor-font-sizes', array() );
		add_theme_support( 'editor-color-palette', array() );
		add_editor_style( 'style-editor.css' );

		/* Loop Standard by PluginBuddy */
		require_once PADMA_LIBRARY_DIR . '/resources/dynamic-loop.php';
		add_theme_support('loop-standard');

	}


	/**
	 **/
	public static function child_theme_setup() {

		if ( !PADMA_CHILD_THEME_ACTIVE )
			return false;

		do_action('padma_setup_child_theme');		

	}


	/**
	 * This will process upgrades from one version to another.
	 **/
	public static function maybe_db_upgrade() {

		global $wpdb;

		$padma_settings = get_option('padma', array('version' => 0));
		$db_version 	= $padma_settings['version'];

		/* If this is a fresh install then we need to merge in the default design editor settings */
			if ( $db_version === 0 && !get_option('padma_option_group_general') ) {

				PadmaElementsData::merge_core_default_design_data();

				self::db_dbdelta();

				/* Update the version here. */
				$padma_settings = get_option('padma', array('version' => 0));
				$padma_settings['version'] = PADMA_VERSION;

				update_option('padma', $padma_settings);

				return $padma_settings;

			}

		/* If the version in the database is already up to date, then there are no upgrade functions to be ran. */
		if ( version_compare($db_version, PADMA_VERSION, '>=') ) {
			if ( get_option('padma_upgrading') ) {
				delete_option('padma_upgrading');
			}

			return false;
		}

		Padma::load('maintenance/upgrades');

		return PadmaMaintenance::do_upgrades();

	}


	public static function db_drop_tables() {

		global $wpdb;

		/* Drop tables first */
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->pu_blocks" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->pu_wrappers" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->pu_layout_meta" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->pu_snapshots" );

	}

	public static function db_dbdelta() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		$pu_blocks_sql = "CREATE TABLE $wpdb->pu_blocks (
					  id char(20) NOT NULL,
					  template varchar(100) NOT NULL,
					  layout varchar(80) NOT NULL,
					  type varchar(30) NOT NULL,
					  wrapper_id char(20) NOT NULL,
					  position blob NOT NULL,
					  dimensions blob NOT NULL,
					  settings mediumblob,
					  mirror_id char(20) DEFAULT NULL,
					  legacy_id int(11) unsigned DEFAULT NULL,
					  PRIMARY KEY  (id,template),
					  KEY layout (layout),
					  KEY type (type)
					) $charset_collate;";

		dbDelta($pu_blocks_sql);


		$pu_wrappers_sql = "CREATE TABLE $wpdb->pu_wrappers (
					  id char(20) NOT NULL,
					  template varchar(100) NOT NULL,
					  layout varchar(80) NOT NULL,
					  position tinyint(2) unsigned DEFAULT NULL,
					  settings mediumblob,
					  mirror_id char(20) DEFAULT NULL,
					  legacy_id int(11) unsigned DEFAULT NULL,
					  PRIMARY KEY  (id,template),
					  KEY layout (layout)
					) $charset_collate;";

		dbDelta($pu_wrappers_sql);


		$pu_layout_meta_sql = "CREATE TABLE $wpdb->pu_layout_meta (
					  meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					  template varchar(100) NOT NULL,
					  layout varchar(80) NOT NULL,
					  meta_key varchar(255),
					  meta_value mediumblob,
					  PRIMARY KEY  (meta_id,template),
					  KEY template (layout)
					) $charset_collate;";

		dbDelta($pu_layout_meta_sql);


		$pu_snapshots_sql = "CREATE TABLE $wpdb->pu_snapshots (
					  id int(11) unsigned NOT NULL AUTO_INCREMENT,
					  template varchar(100) NOT NULL,
					  timestamp datetime NOT NULL,
					  comments text,
					  data_wp_options longblob,
					  data_wp_postmeta longblob,
					  data_pu_layout_meta longblob,
					  data_pu_wrappers longblob,
					  data_pu_blocks longblob,
					  data_other longblob,
					  PRIMARY KEY  (id),
					  KEY template (template)
					) $charset_collate;";

		dbDelta($pu_snapshots_sql);

		if ( function_exists('maybe_convert_table_to_utf8mb4') ) {

			maybe_convert_table_to_utf8mb4( $wpdb->pu_blocks );
			maybe_convert_table_to_utf8mb4( $wpdb->pu_wrappers );
			maybe_convert_table_to_utf8mb4( $wpdb->pu_layout_meta );
			maybe_convert_table_to_utf8mb4( $wpdb->pu_snapshots );

		}

	}


	public static function set_autoload($template = null) {

		global $wpdb;

		if ( !$template ) {
			$template = PadmaOption::$current_skin;
		}

		$wpdb->query( "UPDATE $wpdb->options SET autoload = 'no' WHERE option_name LIKE 'padma_%'" );

		$wpdb->update( $wpdb->options, array(
			'autoload' => 'yes'
		), array(
			'option_name' => 'padma_option_group_general'
		) );

		$wpdb->update( $wpdb->options, array(
			'autoload' => 'yes'
		), array(
			'option_name' => 'pu_|template=' . $template . '|_option_group_general'
		) );

	}


	/**
	 * Initiate the Padma Theme updater checker class for Padma itself.
	 **/
	public static function initiate_updater() {

		if(class_exists('PadmaUpdater')){

			PadmaUpdater::updater(PadmaSettings::get('slug'),PADMA_DIR,true);

		}
	
	}


	/**
	 * Here's our function to load classes and files when needed from the library.
	 **/
	public static function load($classes, $init = false) {

		//Build in support to either use array or a string
		if ( !is_array($classes) ) {
			$load[$classes] = $init;
		} else {
			$load = $classes;
		}
		
		$classes_to_init = array();

		//Remove already loaded classes from the array
		foreach ( Padma::$loaded_classes as $class ) {
			unset($load[$class]);
		}
		
		foreach ( $load as $file => $init ) {


			//Check if only value is used instead of both key and value pair
			if ( is_numeric($file) ){
				$file = $init;
				$init = false;
			}

			//Add the class to the main variable so we know that it has been loaded
			Padma::$loaded_classes[] = $file;

			//Set up init, if init is true, just figure out the class name from filename.  If argument is string, use that.
			if ( $init === true ) {

				$class = array_reverse(explode('/', str_replace('.php', '', $file)));

				//Check for hyphens/underscores and CamelCase it
				$class = str_replace(' ', '', ucwords(str_replace('-', ' ', str_replace('_', ' ', $class[0]))));

				$classes_to_init[] = $class;

			} else if ( is_string($init) ) {

				$classes_to_init[] = $init;

			}else {
				
				//Handle anything and automatically insert .php if need be
				if ( strpos($file, '/') !== false )
					require_once PADMA_LIBRARY_DIR . '/' . $file . '.php';

			}

		}
		//debug($classes_to_init);
		
		//Init everything after dependencies have been loaded
		foreach($classes_to_init as $class){

			if ( method_exists('Padma' . $class, 'init') ) {

				call_user_func(array('Padma' . $class, 'init'));

			} else {

				trigger_error('Padma' . $class . '::init is not a valid method', E_USER_WARNING);

			}

		}

	}


	public static function get() {
		_deprecated_function(__FUNCTION__, '3.1.3', 'padma_get()');
		$args = func_get_args();
		return call_user_func_array('padma_get', $args);
	}


	public static function post() {
		_deprecated_function(__FUNCTION__, '3.1.3', 'padma_post()');
		$args = func_get_args();
		return call_user_func_array('padma_post', $args);
	}

}
