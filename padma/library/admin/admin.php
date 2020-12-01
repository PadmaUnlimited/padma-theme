<?php

class PadmaAdmin {

	public static function init() {

		self::setup_hooks();

		Padma::load(array(
			'abstract/api-admin-meta-box',
			'admin/admin-write' => true,
			'admin/admin-pages',
			'admin/api-admin-inputs'
		));
	}


	public static function setup_hooks() {

		/* Actions */
		add_action('admin_init', array(__CLASS__, 'activation'), 1);
		add_action('admin_init', array(__CLASS__, 'enqueue'));
		add_action('admin_init', array(__CLASS__, 'visual_editor_redirect'), 12);

		add_action('init', array(__CLASS__, 'form_action_save'), 12); // Init runs before admin_menu; admin_menu runs before admin_init
		add_action('init', array(__CLASS__, 'form_action_reset'), 12);
		add_action('init', array(__CLASS__, 'form_action_delete_snapshots'), 12);
		add_action('init', array(__CLASS__, 'form_action_replace_url'), 12);

		add_action('admin_menu', array(__CLASS__, 'add_menus'));

		add_action('padma_admin_save_message', array(__CLASS__, 'save_message'));
		add_action('padma_admin_save_error_message', array(__CLASS__, 'save_error_message'));

		add_action('admin_notices', array(__CLASS__, 'notice_no_widgets_or_menus'));
		add_action('admin_notices', array(__CLASS__, 'theme_install_template_notice'));
        add_action('admin_notices', array(__CLASS__, 'responsive_grid_notice'));

        add_action('wp_ajax_padma_dismiss_admin_notice', array(__CLASS__, 'ajax_dismiss_admin_notice'));
        add_action('wp_ajax_padma_enable_responsive_grid', array(__CLASS__, 'ajax_enable_responsive_grid'));

		add_filter('page_row_actions', array(__CLASS__, 'row_action_visual_editor'), 10, 2);
		add_filter('post_row_actions', array(__CLASS__, 'row_action_visual_editor'), 10, 2);
		add_filter('tag_row_actions', array(__CLASS__, 'row_action_visual_editor'), 10, 2);

		add_filter('mce_buttons_2', array(__CLASS__, 'tiny_mce_buttons'));
		add_filter('tiny_mce_before_init', array(__CLASS__, 'tiny_mce_formats'));

	}


	public static function form_action_save() {

		//Form action for all Padma configuration panels.  Not in function/hook so it can load before everything else.
		if ( !padma_post('padma-submit', false))
			return false;

		if ( !wp_verify_nonce(padma_post('padma-admin-nonce', false), 'padma-admin-nonce') ) {

			global $padma_admin_save_message;
			$padma_admin_save_message = 'Security nonce did not match.';

			return false;

		}

		foreach ( padma_post('padma-admin-input', array()) as $option => $value ) {

			PadmaOption::set($option, $value);

			// Automatic Updates
			if($option == 'disable-automatic-core-updates'){				
				update_option('padma-disable-automatic-core-updates',$value);
			}
			if($option == 'disable-automatic-plugin-updates'){				
				update_option('padma-disable-automatic-plugin-updates',$value);
			}

			// Developer version			
			if($option == 'use-developer-version'){				
				update_option('padma-use-developer-version',$value);
			}

		}

		global $padma_admin_save_message;
		$padma_admin_save_message = 'Settings saved.';

		return true;

	}

	public static function form_action_delete_snapshots() {

		global $wpdb;

		if ( ! padma_post( 'padma-delete-snapshots', false ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( padma_post( 'padma-delete-snapshots-nonce', false ), 'padma-delete-snapshots-nonce' ) ) {

			$GLOBALS['padma_admin_save_message'] = 'Security nonce did not match.';

			return false;

		}

		/* Loop through WordPress options and delete the skin options */
		$wpdb->query( "TRUNCATE TABLE $wpdb->pu_snapshots" );

		do_action( 'padma_delete_all_snapshots' );

		$GLOBALS['padma_admin_save_message'] = 'Snapshots successfully deleted.';

		return true;

	}

	public static function form_action_replace_url() {

		global $wpdb;

		if ( ! padma_post( 'padma-replace-url', false ) ) {
			return false;
		}
		
		if ( ! wp_verify_nonce( padma_post( 'padma-replace-url-nonce', false ), 'padma-replace-url-nonce' ) ) {

			$GLOBALS['padma_admin_save_message'] = 'Security nonce did not match.';
			return false;

		}

	
		$from = ! empty( padma_post('from')) ? padma_post('from') : '';
		$to = ! empty( padma_post('to')) ? padma_post('to') : '';

		try {
			if( padma_replace_urls( $from, $to ) ){
				$GLOBALS['padma_admin_save_message'] = 'URL successfully replaced.';
				return true;		
			}else{
				return false;
			}

		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
			return false;
		}

	}


	public static function form_action_reset() {

		global $wpdb;

		if ( !defined('PADMA_ALLOW_RESET') || PADMA_ALLOW_RESET !== true )
			return false;

		//Form action for all Padma configuration panels.  Not in function/hook so it can load before everything else.
		if ( !padma_post('reset-padma', false) )
			return false;

		//Verify the nonce so other sites can't maliciously reset a Padma installation.
		if ( !wp_verify_nonce(padma_post('padma-reset-nonce', false), 'padma-reset-nonce') ) {

			$GLOBALS['padma_admin_save_message'] = 'Security nonce did not match.';

			return false;

		}

		/* Loop through WordPress options and delete the skin options */
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name = 'padma'" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'padma_%'" );

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_pu_%'" );

		/* Remove Padma post meta */
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_pu_%'" );

		/* Drop Padma tables */
		Padma::db_drop_tables();

		/* Flush WP cache */
		wp_cache_flush();

		do_action('padma_global_reset');

		$GLOBALS['padma_admin_save_message'] = 'Padma was successfully reset.';

		//This will hide the reset box if set to true.
		$GLOBALS['padma_reset_success'] = true;

		return true;

	}


	public static function activation() {

		if ( !is_admin() || !padma_get('activated') )
			return false;

		global $pagenow;

		if ( $pagenow !== 'themes.php' )
			return false;

		//Since they may be upgrading and files may change, let's clear the cache
		do_action('padma_activation');

		self::activation_redirect();

	}


	public static function activation_redirect() {

		do_action('padma_activation_redirect');

		//If a child theme has been activated rather than Padma, then don't redirect.
		//Let the child theme developer redirect if they want by using the hook above.
		if ( PADMA_CHILD_THEME_ACTIVE === true )
			return false;

		$parent_menu = self::parent_menu();

		//If header were sent, then don't do the redirect
		if ( headers_sent() )
			return false;

		//We're all good, redirect now
		wp_safe_redirect(admin_url('admin.php?page=padma-' . $parent_menu['id']));
		die();

	}


	public static function visual_editor_redirect() {

		if ( isset($_GET['page']) && strpos($_GET['page'], 'padma-visual-editor') !== false && !headers_sent() )
			wp_safe_redirect(home_url() . '/?visual-editor=true');

	}


	public static function add_admin_separator($position){

		global $menu;

		$menu[$position] = array('', 'read', 'separator-padma', '', 'wp-menu-separator padma-separator');

		ksort($menu);

	}


	public static function add_admin_submenu($name, $id, $callback) {

		$parent_menu = self::parent_menu();

		return add_submenu_page('padma-' . $parent_menu['id'], $name, $name, 'manage_options', $id, $callback);

	}


	public static function add_menus(){

		//If the hide menus constant is set to true, don't hide the menus!
		if (defined('PADMA_HIDE_MENUS') && PADMA_HIDE_MENUS === true)
		 	return false;

		//If user cannot access the admin panels, then don't bother running these functions
		if ( !PadmaCapabilities::can_user_visually_edit() )
			return false;

		$menu_name = ( PadmaOption::get('hide-menu-version-number', false, true) == true ) ? PadmaSettings::get('menu-name') : PadmaSettings::get('menu-name') . ' ' . PADMA_VERSION;

		$icon = (version_compare($GLOBALS['wp_version'], '3.8', '>=') && get_user_option('admin_color') != 'light') ? 'padma-32-grey.png' : 'padma-16.png';
		$icon_url = padma_url() . '/library/admin/images/' . $icon;

		$parent_menu = self::parent_menu();

		self::add_admin_separator(48);

		add_menu_page($parent_menu['name'], $menu_name, 'manage_options', 'padma-' . $parent_menu['id'], $parent_menu['callback'], $icon_url, 49);

			switch ( $parent_menu['id'] ) {

				case 'getting-started':
					self::add_admin_submenu( __('Getting Started','padma'), 'padma-getting-started', array('PadmaAdminPages', 'getting_started'));
					self::add_admin_submenu( __('Visual Editor','padma'), 'padma-visual-editor', array('PadmaAdminPages', 'visual_editor'));
					self::add_admin_submenu( __('Templates','padma'), 'padma-templates', array('PadmaAdminPages', 'templates'));
					self::add_admin_submenu( __('Options','padma'), 'padma-options', array('PadmaAdminPages', 'options'));
					self::add_admin_submenu( __('Tools','padma'), 'padma-tools', array('PadmaAdminPages', 'tools'));
				break;

				case 'visual-editor':
					self::add_admin_submenu( __('Visual Editor','padma'), 'padma-visual-editor', array('PadmaAdminPages', 'visual_editor'));
					self::add_admin_submenu( __('Templates','padma'), 'padma-templates', array('PadmaAdminPages', 'templates'));
					self::add_admin_submenu( __('Options','padma'), 'padma-options', array('PadmaAdminPages', 'options'));
					self::add_admin_submenu( __('Tools','padma'), 'padma-tools', array('PadmaAdminPages', 'tools'));
				break;

				case 'options':
					self::add_admin_submenu( __('Options','padma'), 'padma-options', array('PadmaAdminPages', 'options'));
					self::add_admin_submenu( __('Visual Editor','padma'), 'padma-visual-editor', array('PadmaAdminPages', 'visual_editor'));
					self::add_admin_submenu( __('Templates','padma'), 'padma-templates', array('PadmaAdminPages', 'templates'));
					self::add_admin_submenu( __('Tools','padma'), 'padma-tools', array('PadmaAdminPages', 'tools'));
				break;

			}

	}


	public static function parent_menu() {

		$menu_setup = PadmaOption::get('menu-setup', false, 'getting-started');

		/* Figure out the primary page */
		switch ( $menu_setup ) {

			case 'getting-started':
				$parent_menu = array(
					'id' => 'getting-started',
					'name' => 'Getting Started',
					'callback' => array('PadmaAdminPages', 'getting_started')
				);
			break;

			case 'options':
				$parent_menu = array(
					'id' => 'options',
					'name' => 'Options',
					'callback' => array('PadmaAdminPages', 'options')
				);
			break;

			default:
				$parent_menu = array(
					'id' => 'visual-editor',
					'name' => 'Visual Editor',
					'callback' => array( 'PadmaAdminPages', 'visual_editor' )
				);
			break;

		}

		return $parent_menu;

	}


	public static function enqueue() {

		global $pagenow;

		/* Global */
		wp_enqueue_style('padma_admin_global', padma_url() . '/library/admin/css/admin-padma-global.css');
        wp_enqueue_script('padma_admin_js', padma_url() . '/library/admin/js/admin-padma.js', array('jquery'));



		wp_localize_script('padma_admin_js', 'Padma', array(
			'ajaxURL' 			=> admin_url('admin-ajax.php'),				
			'security' 			=> wp_create_nonce('padma-visual-editor-ajax'),				
		));

		/**
		 * General Padma admin CSS/JS
		 */
		if ( false !== strpos( padma_get( 'page' ), 'padma' ) ) {

			wp_enqueue_script('padma_jquery_scrollto', padma_url() . '/library/admin/js/jquery.scrollto.js', array('jquery'));
			wp_enqueue_script('padma_jquery_tabby', padma_url() . '/library/admin/js/jquery.tabby.js', array('jquery'));
			wp_enqueue_script('padma_jquery_qtip', padma_url() . '/library/admin/js/jquery.qtip.js', array('jquery'));
            wp_enqueue_script('padma_admin_js', padma_url() . '/library/admin/js/admin-padma.js', array('jquery', 'padma_jquery_qtip'));

            wp_enqueue_style('padma_admin', padma_url() . '/library/admin/css/admin-padma.css');
			wp_enqueue_style('padma_alerts', padma_url() . '/library/media/css/alerts.css');

		}

		/* Templates */
		if ( padma_get( 'page' ) === 'padma-templates' ) {

			wp_enqueue_script( 'padma_knockout', padma_url() . '/library/admin/js/knockout.js', array( 'jquery' ), PADMA_VERSION, true );
			wp_enqueue_script( 'padma_admin_templates', padma_url() . '/library/admin/js/admin-templates.js', array( 'jquery' ), PADMA_VERSION, true );

			wp_localize_script(
				'padma_admin_templates',
				'Padma',
				array(
					'ajaxURL'        => admin_url( 'admin-ajax.php' ),
					'apiURL'         => PADMA_API_URL,
					'security'       => wp_create_nonce( 'padma-visual-editor-ajax' ),
					'templates'      => PadmaTemplates::get_all(),
					'templateActive' => PadmaTemplates::get_active(),
					'viewModels'     => array(),
				)
			);

			add_thickbox();
			wp_enqueue_media();

		}


		/* Meta Boxes */
		wp_enqueue_style('padma_admin_write', padma_url() . '/library/admin/css/admin-write.css');
		wp_enqueue_style('padma_alerts', padma_url() . '/library/media/css/alerts.css');
		wp_enqueue_script('padma_admin_write', padma_url() . '/library/admin/js/admin-write.js', array('jquery'));
                $css_src = includes_url('css/') . 'editor.css';
                wp_register_style('tinymce_css', $css_src);
                wp_enqueue_style('tinymce_css');


		/* Auto Updater */
		if ( $pagenow === 'update-core.php' ) {

			wp_enqueue_style('padma_admin', padma_url() . '/library/admin/css/admin-padma.css');
			wp_enqueue_style('padma_alerts', padma_url() . '/library/media/css/alerts.css');

		}

	}


	public static function save_message() {

		global $padma_admin_save_message;

		if ( !isset($padma_admin_save_message) || $padma_admin_save_message == false )
			return false;

		echo '<div id="setting-error-settings_updated" class="updated settings-error"><p>' . $padma_admin_save_message . '</p></div>';

	}


	public static function save_error_message() {

		global $padma_admin_save_error_message;

		if ( !isset($padma_admin_save_error_message) || $padma_admin_save_error_message == false )
			return false;

		echo '<div id="setting-error-settings_error" class="error settings-error"><p>' . $padma_admin_save_error_message . '</p></div>';

	}


	public static function notice_no_widgets_or_menus() {

		global $pagenow;

		if ( $pagenow != 'widgets.php' && $pagenow != 'nav-menus.php' )
			return false;

		$grid_mode_url = add_query_arg(array('visual-editor' => 'true', 'visual-editor-mode' => 'grid'), home_url());

		//Show the widgets message if no widget blocks exist.
		if ( $pagenow == 'widgets.php' ) {

			$widget_area_blocks = PadmaBlocksData::get_blocks_by_type('widget-area');

			if ( !empty($widget_area_blocks) )
				return;

			if ( !current_theme_supports('padma-grid') )
				return;

			echo '<div class="updated" style="margin-top: 15px;">
			       <p>Padma has detected that you have no Widget Area blocks.  If you wish to use the WordPress widgets system with Padma, please add a Widget Area block in the <a href="' . $grid_mode_url . '" target="_blank">Visual Editor: Grid</a>.</p>

					<style type="text/css">
						div.error.below-h2 { display: none; }
						div.error.below-h2 + p { display: none; }
					</style>
			    </div>';

		}

		//Show the navigation menus message if no navigation blocks exist.
		if ( $pagenow == 'nav-menus.php' ) {

			$navigation_blocks = PadmaBlocksData::get_blocks_by_type('navigation');

			if ( !empty($navigation_blocks) )
				return;

			if ( !current_theme_supports('padma-grid') )
				return;

			echo '<div class="updated">
			       <p>' . sprintf( __('Padma has detected that you have no Navigation blocks. If you wish to use the WordPress menus system with Padma, please add a Navigation block in the <a href="%s" target="_blank">Visual Editor: Grid</a>.', 'padma'), $grid_mode_url ) . '</p>
			    </div>';

		}

	}


	public static function theme_install_template_notice() {

		global $pagenow;

		if ( $pagenow != 'theme-install.php' )
			return false;

		echo '<div class="error">
				<h3>' . __('Are you trying to install a Padma Template?','padma') . '</h3>
			  	 <p>' . sprintf( __('Please go to <a href="%s">Padma &rsaquo; Templates</a> to install Templates.','padma'), admin_url('admin.php?page=padma-templates') ) . '</p>
			</div>';


	}


    public static function responsive_grid_notice() {

        $dismissed_notices = PadmaOption::get('dismissed-notices', false, array());

        if ( PadmaSkinOption::get('enable-responsive-grid', false, true) || in_array('responsive-grid', $dismissed_notices) ) {
            return false;
        }

        echo '<div id="padma-responsive-grid-notice" data-padma-notice="responsive-grid" class="notice notice-warning is-dismissible" style="padding-top: 0.5em;padding-bottom: 0.5em;">
				<h3 style="margin: 0.5em 0">' . __('Important! Your site is currently not mobile-friendly.','padma') . '</h3>
                <p>' . __('Google now penalizes websites that are not mobile-friendly. Enabling the Responsive Grid will make your website mobile-friendly in most cases.','padma') . '</p>
                <p>' . __('<strong>Please note:</strong> Enabling the responsive grid can cause styling and layout changes for some websites. You can always disable Responsive Grid under the Grid mode in the Visual Editor.','padma') . '</p>
                <p><button class="button-primary">' . __('Enable Responsive Grid','padma') . '</button>&emsp;&emsp;<button class="button-secondary padma-dismiss-notice">' . __('Dismiss','padma') . '</button></p>
			</div>';

    }


	public static function show_header($title = false) {

		echo '<div class="wrap padma-page">';

		if ( $title )
			echo '<h2>' . $title . '</h2>';

	}


	public static function show_footer() {

		echo '</div><!-- #wrapper -->';

	}


	public static function row_action_visual_editor($actions, $item) {

		if ( !PadmaCapabilities::can_user_visually_edit() )
			return $actions;

		/* Post */
		if ( isset($item->post_status) ) {

			if ( $item->post_status != 'publish' )
				return $actions;

			$post_type = get_post_type_object($item->post_type);

			if ( !$post_type->public )
				return $actions;

			$layout_id = 'single' . PadmaLayout::$sep . $item->post_type . PadmaLayout::$sep . $item->ID;

			if ( get_option('show_on_front') === 'page' ) {

				if ( $item->ID == get_option('page_on_front') )
					$layout_id = 'front_page';

				if ( $item->ID == get_option('page_for_posts') )
					$layout_id = 'index';

			}

		/* Category */
		} elseif ( isset($item->term_id) && $item->taxonomy == 'category' ) {

			$layout_id = 'archive' . PadmaLayout::$sep . 'category' . PadmaLayout::$sep . $item->term_id;

		/* Post Tag */
		} elseif ( isset($item->term_id) && $item->taxonomy == 'post_tag' ) {

			$layout_id = 'archive' . PadmaLayout::$sep . 'post_tag' . PadmaLayout::$sep . $item->term_id;

		/* Taxonomy */
		} elseif ( isset($item->term_id) ) {

			$layout_id = 'archive' . PadmaLayout::$sep . 'taxonomy' . PadmaLayout::$sep . $item->taxonomy . PadmaLayout::$sep . $item->term_id;

		}

		$visual_editor_url = home_url('/?visual-editor=true&ve-layout=' . urlencode($layout_id));

		$actions['pu-visual-editor'] = '<a href="' . $visual_editor_url . '" title="' . __('Open in Padma Visual Editor','padma') . '" rel="permalink" target="_blank">' . __('Open in Padma Visual Editor','padma') . '</a>';

		return $actions;

	}


	public static function tiny_mce_buttons($buttons) {

		array_unshift( $buttons, 'styleselect' );
		return $buttons;

	}


	public static function tiny_mce_formats($init_array) {

		$style_formats = array(
			array(
				'title' => 'Alerts',
				'items' => array(
					array(
						'title' => 'Red',
						'block' => 'div',
						'classes' => 'alert alert-red',
						'wrapper' => true
					),

					array(
						'title' => 'Yellow',
						'block' => 'div',
						'classes' => 'alert alert-yellow',
						'wrapper' => true
					),

					array(
						'title' => 'Green',
						'block' => 'div',
						'classes' => 'alert alert-green',
						'wrapper' => true
					),

					array(
						'title' => 'Blue',
						'block' => 'div',
						'classes' => 'alert alert-blue',
						'wrapper' => true
					),

					array(
						'title' => 'Gray',
						'block' => 'div',
						'classes' => 'alert alert-gray',
						'wrapper' => true
					)
				)
			)
		);

		if ( !empty( $init_array['style_formats'] ) ) {

			// json decode wp array
			$jd_orig_array = json_decode( $init_array['style_formats'], true );

			// merge new array with wp array (json encoded)
			$new_merge = json_encode( array_merge( $jd_orig_array, $style_formats ) );

			// populate back into function
			$init_array['style_formats'] = $new_merge;

		} else {

			$init_array['style_formats'] = json_encode($style_formats);

		}

		return $init_array;

	}


    public static function ajax_dismiss_admin_notice() {

        $notice_to_dismiss 		= padma_post('notice-to-dismiss');
        $dismissed_notices 		= PadmaOption::get('dismissed-notices', false, array());
        $dismissed_notices[] 	= $notice_to_dismiss;

        return PadmaOption::set('dismissed-notices', array_unique($dismissed_notices));

    }


    public static function ajax_enable_responsive_grid() {

        return PadmaSkinOption::set('enable-responsive-grid', true);

    }


}
