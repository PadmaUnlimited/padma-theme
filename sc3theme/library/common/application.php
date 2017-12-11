<?php
/**
 * All of the global functions to be used everywhere in Blox.
 *
 * @package Blox
 * @author Clay Griffiths
 **/

class Blox {


	public static $loaded_classes = array();


	/**
	 * Let's get Blox on the road!  We'll define constants here, run the setup function and do a few other fun things.
	 *
	 * @return void
	 **/
	public static function init() {

		global $wpdb;

		/* Legacy element default handling */
		$GLOBALS['blox_default_element_data'] = array();

		/* Define simple constants */
		define('THEME_FRAMEWORK', 'blox');
		define('BLOX_VERSION', '1.0.3');

		/* Define directories */
		define('BLOX_DIR', blox_change_to_unix_path(TEMPLATEPATH));
		define('BLOX_LIBRARY_DIR', blox_change_to_unix_path(BLOX_DIR . '/library'));

		/* Site URLs */
		define('BLOX_SITE_URL', 'http://bloxtheme.com/');
		define('BLOX_DASHBOARD_URL', BLOX_SITE_URL . 'dashboard');
		define('BLOX_EXTEND_URL', BLOX_SITE_URL . 'extend');

		/* Skins */
		define('BLOX_DEFAULT_SKIN', 'base');

		/* MySQL Table names */
		$wpdb->bt_blocks = $wpdb->prefix . 'bt_blocks';
		$wpdb->bt_wrappers = $wpdb->prefix . 'bt_wrappers';
		$wpdb->bt_snapshots = $wpdb->prefix . 'bt_snapshots';
		$wpdb->bt_layout_meta = $wpdb->prefix . 'bt_layout_meta';

		/* Handle child themes */
		if ( get_template_directory_uri() !== get_stylesheet_directory_uri() ) {
			define('BLOX_CHILD_THEME_ACTIVE', true);
			define('BLOX_CHILD_THEME_DIR', get_stylesheet_directory());
		} else {
			define('BLOX_CHILD_THEME_ACTIVE', false);
			define('BLOX_CHILD_THEME_DIR', null);
		}

		/* Handle uploads directory and cache */
		$uploads = wp_upload_dir();

		define('BLOX_UPLOADS_DIR', blox_change_to_unix_path($uploads['basedir'] . '/blox'));
		define('BLOX_CACHE_DIR', blox_change_to_unix_path(BLOX_UPLOADS_DIR . '/cache'));

		/* Make directories if they don't exist */
		if ( !is_dir(BLOX_UPLOADS_DIR) )
			wp_mkdir_p(BLOX_UPLOADS_DIR);

		if ( !is_dir(BLOX_CACHE_DIR) )
			wp_mkdir_p(BLOX_CACHE_DIR);

		self::add_index_files_to_uploads();

		/* Load locale */
		load_theme_textdomain('blox', blox_change_to_unix_path(BLOX_LIBRARY_DIR . '/languages'));

		/* Add support for WordPress features */
		add_action('after_setup_theme', array(__CLASS__, 'add_theme_support'), 1);

		/* Setup */
		add_action('after_setup_theme', array(__CLASS__, 'child_theme_setup'), 2);
		add_action('after_setup_theme', array(__CLASS__, 'load_dependencies'), 3);
		add_action('after_setup_theme', array(__CLASS__, 'maybe_db_upgrade'));
		add_action('after_setup_theme', array(__CLASS__, 'initiate_updater'));

	}


	public static function add_index_files_to_uploads() {

		$content = '<?php' . "\n" .
		'/* Disallow directory browsing */';

		$uploads_index = trailingslashit( BLOX_UPLOADS_DIR ) . 'index.php';
		$cache_index = trailingslashit( BLOX_CACHE_DIR ) . 'index.php';

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
	 **/
	public static function load_dependencies() {

		//Load route right away so we can optimize dependency loading below
		Blox::load(array('common/route' => true));

		//Core loading set
		$dependencies = array(
			'defaults/default-design-settings',

			'data/data-options' => 'Option',
			'data/data-layout-options' => 'LayoutOption',
			'data/data-skin-options',
			'data/data-blocks',
			'data/data-wrappers',
			'data/data-snapshots',

			'common/layout' => true,
			'common/capabilities' => true,
			'common/responsive-grid' => true,
			'common/seo' => true,
			'common/social-optimization' => true,
			'common/feed' => true,
			'common/compiler' => true,
			'common/templates',

			'admin/admin-bar' => true,

			'api/api-panel',

			'updater/plugin-updater',
			'updater/theme-updater',

			'blocks' => true,
			'wrappers' => true,
			'elements' => true,

			'fonts/web-fonts-api',
			'fonts/web-fonts-loader' => true,
			'fonts/traditional-fonts',
			'fonts/google-fonts',

			'display' => true,

			'widgets' => true,

			'compatibility/woocommerce/compatibility-woocommerce' => 'CompatibilityWooCommerce'
		);

		//Child theme API
		if ( BLOX_CHILD_THEME_ACTIVE === true )
			$dependencies['api/api-child-theme'] = 'ChildThemeAPI';

		//Visual editor classes
		if ( BloxRoute::is_visual_editor() || (defined('DOING_AJAX') && DOING_AJAX && strpos($_REQUEST['action'], 'blox') !== false ) )
			$dependencies['visual-editor'] = true;

		//Admin classes
		if ( is_admin() )
			$dependencies['admin'] = true;

		//Load stuff now
		Blox::load(apply_filters('blox_dependencies', $dependencies));

		do_action('blox_setup');

	}


	/**
	 * Tell WordPress that Blox supports its features.
	 **/
	public static function add_theme_support() {

		/* Blox Functionality */
		add_theme_support( 'blox-grid' );
		add_theme_support( 'blox-responsive-grid' );
		add_theme_support( 'blox-design-editor' );

		/* Blox CSS */
		add_theme_support( 'blox-reset-css' );
		add_theme_support( 'blox-live-css' );
		add_theme_support( 'blox-block-basics-css' );
		add_theme_support( 'blox-dynamic-block-css' );
		add_theme_support( 'blox-content-styling-css' );

		/* WordPress Functionality */
		add_theme_support( 'html5', array( 'caption' ) );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_theme_support( 'widgets' );
		add_theme_support( 'editor-style' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );

		/* Loop Standard by PluginBuddy */
		require_once BLOX_LIBRARY_DIR . '/resources/dynamic-loop.php';
		add_theme_support('loop-standard');

	}


	/**
	 **/
	public static function child_theme_setup() {

		if ( !BLOX_CHILD_THEME_ACTIVE )
			return false;

		do_action('blox_setup_child_theme');

	}


	/**
	 * This will process upgrades from one version to another.
	 **/
	public static function maybe_db_upgrade() {

		global $wpdb;

		$blox_settings = get_option('blox', array('version' => 0));
		$db_version = $blox_settings['version'];

		/* If this is a fresh install then we need to merge in the default design editor settings */
			if ( $db_version === 0 && !get_option('blox_option_group_general') ) {

				BloxElementsData::merge_core_default_design_data();

				self::mysql_dbdelta();

				/* Update the version here. */
				$blox_settings = get_option('blox', array('version' => 0));
				$blox_settings['version'] = BLOX_VERSION;

				update_option('blox', $blox_settings);

				return $blox_settings;

			}

		/* If the version in the database is already up to date, then there are no upgrade functions to be ran. */
		if ( version_compare($db_version, BLOX_VERSION, '>=') ) {
			if ( get_option('blox_upgrading') ) {
				delete_option('blox_upgrading');
			}

			return false;
		}

		Blox::load('maintenance/upgrades');

		return BloxMaintenance::do_upgrades();

	}


	public static function mysql_drop_tables() {

		global $wpdb;

		/* Drop tables first */
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->bt_blocks" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->bt_wrappers" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->bt_layout_meta" );
		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->bt_snapshots" );

	}

	public static function mysql_dbdelta() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		$bt_blocks_sql = "CREATE TABLE $wpdb->bt_blocks (
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

		dbDelta($bt_blocks_sql);


		$bt_wrappers_sql = "CREATE TABLE $wpdb->bt_wrappers (
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

		dbDelta($bt_wrappers_sql);


		$bt_layout_meta_sql = "CREATE TABLE $wpdb->bt_layout_meta (
					  meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					  template varchar(100) NOT NULL,
					  layout varchar(80) NOT NULL,
					  meta_key varchar(255),
					  meta_value mediumblob,
					  PRIMARY KEY  (meta_id,template),
					  KEY template (layout)
					) $charset_collate;";

		dbDelta($bt_layout_meta_sql);


		$bt_snapshots_sql = "CREATE TABLE $wpdb->bt_snapshots (
					  id int(11) unsigned NOT NULL AUTO_INCREMENT,
					  template varchar(100) NOT NULL,
					  timestamp datetime NOT NULL,
					  comments text,
					  data_wp_options longblob,
					  data_wp_postmeta longblob,
					  data_bt_layout_meta longblob,
					  data_bt_wrappers longblob,
					  data_bt_blocks longblob,
					  data_other longblob,
					  PRIMARY KEY  (id),
					  KEY template (template)
					) $charset_collate;";

		dbDelta($bt_snapshots_sql);

		if ( function_exists('maybe_convert_table_to_utf8mb4') ) {

			maybe_convert_table_to_utf8mb4( $wpdb->bt_blocks );
			maybe_convert_table_to_utf8mb4( $wpdb->bt_wrappers );
			maybe_convert_table_to_utf8mb4( $wpdb->bt_layout_meta );
			maybe_convert_table_to_utf8mb4( $wpdb->bt_snapshots );

		}

	}


	public static function set_autoload($template = null) {

		global $wpdb;

		if ( !$template ) {
			$template = BloxOption::$current_skin;
		}

		$wpdb->query( "UPDATE $wpdb->options SET autoload = 'no' WHERE option_name LIKE 'blox_%'" );

		$wpdb->update( $wpdb->options, array(
			'autoload' => 'yes'
		), array(
			'option_name' => 'blox_option_group_general'
		) );

		$wpdb->update( $wpdb->options, array(
			'autoload' => 'yes'
		), array(
			'option_name' => 'blox_|template=' . $template . '|_option_group_general'
		) );

	}


	/**
	 * Initiate the BloxUpdaterAPI class for Blox itself.
	 **/
	public static function initiate_updater() {

		$GLOBALS['blox_updater'] = new Blox_Theme_Updater(array(
			'remote_api_url' 	=> BLOX_SITE_URL,
			'version' 			=> BLOX_VERSION,
			'license' 			=> blox_get_license_key('blox'),
			'slug'				=> '',
			'item_name'			=> 'Unlimited',
			'author'			=> 'Blox Theme'
		));

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
		foreach ( Blox::$loaded_classes as $class ) {
			unset($load[$class]);
		}

		foreach ( $load as $file => $init ) {

			//Check if only value is used instead of both key and value pair
			if ( is_numeric($file) ){
				$file = $init;
				$init = false;
			}

			//Handle anything with .php or a full path
			if ( strpos($file, '.php') !== false )
				require_once BLOX_LIBRARY_DIR . '/' . $file;

			//Handle main-helpers such as admin, data, etc.
			elseif ( strpos($file, '/') === false )
				require_once BLOX_LIBRARY_DIR . '/' . $file . '/' . $file . '.php';

			//Handle anything and automatically insert .php if need be
			elseif ( strpos($file, '/') !== false )
				require_once BLOX_LIBRARY_DIR . '/' . $file . '.php';

			//Add the class to the main variable so we know that it has been loaded
			Blox::$loaded_classes[] = $file;

			//Set up init, if init is true, just figure out the class name from filename.  If argument is string, use that.
			if ( $init === true ) {

				$class = array_reverse(explode('/', str_replace('.php', '', $file)));

				//Check for hyphens/underscores and CamelCase it
				$class = str_replace(' ', '', ucwords(str_replace('-', ' ', str_replace('_', ' ', $class[0]))));

				$classes_to_init[] = $class;

			} else if ( is_string($init) ) {

				$classes_to_init[] = $init;

			}

		}

		//Init everything after dependencies have been loaded
		foreach($classes_to_init as $class){

			if ( method_exists('Blox' . $class, 'init') ) {

				call_user_func(array('Blox' . $class, 'init'));

			} else {

				trigger_error('Blox' . $class . '::init is not a valid method', E_USER_WARNING);

			}

		}

	}


	public static function get() {
		_deprecated_function(__FUNCTION__, '3.1.3', 'blox_get()');
		$args = func_get_args();
		return call_user_func_array('blox_get', $args);
	}


	public static function post() {
		_deprecated_function(__FUNCTION__, '3.1.3', 'blox_post()');
		$args = func_get_args();
		return call_user_func_array('blox_post', $args);
	}


}
