<?php
class BloxAdmin {


	public static function init() {

		self::setup_hooks();

		Blox::load(array(
			'api/api-admin-meta-box',
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
		add_action('init', array(__CLASS__, 'form_action_licenses'), 12);
		add_action('init', array(__CLASS__, 'form_action_reset'), 12);
		add_action('init', array(__CLASS__, 'form_action_delete_snapshots'), 12);

		add_action('admin_menu', array(__CLASS__, 'add_menus'));

		add_action('blox_admin_save_message', array(__CLASS__, 'save_message'));
		add_action('blox_admin_save_error_message', array(__CLASS__, 'save_error_message'));

		add_action('admin_notices', array(__CLASS__, 'notice_no_widgets_or_menus'));
		add_action('admin_notices', array(__CLASS__, 'theme_install_template_notice'));
        add_action('admin_notices', array(__CLASS__, 'responsive_grid_notice'));

        add_action('wp_ajax_blox_dismiss_admin_notice', array(__CLASS__, 'ajax_dismiss_admin_notice'));
        add_action('wp_ajax_blox_enable_responsive_grid', array(__CLASS__, 'ajax_enable_responsive_grid'));

		add_filter('page_row_actions', array(__CLASS__, 'row_action_visual_editor'), 10, 2);
		add_filter('post_row_actions', array(__CLASS__, 'row_action_visual_editor'), 10, 2);
		add_filter('tag_row_actions', array(__CLASS__, 'row_action_visual_editor'), 10, 2);

		add_filter('mce_buttons_2', array(__CLASS__, 'tiny_mce_buttons'));
		add_filter('tiny_mce_before_init', array(__CLASS__, 'tiny_mce_formats'));

	}


	public static function form_action_save() {

		//Form action for all Blox configuration panels.  Not in function/hook so it can load before everything else.
		if ( !blox_post('blox-submit', false))
			return false;

		if ( !wp_verify_nonce(blox_post('blox-admin-nonce', false), 'blox-admin-nonce') ) {

			global $blox_admin_save_message;
			$blox_admin_save_message = 'Security nonce did not match.';

			return false;

		}

		foreach ( blox_post('blox-admin-input', array()) as $option => $value ) {

			BloxOption::set($option, $value);

		}

		global $blox_admin_save_message;
		$blox_admin_save_message = 'Settings saved.';

		return true;

	}


	public static function form_action_licenses() {

		if ( !blox_post('blox-licenses', false))
			return false;

		if ( !wp_verify_nonce(blox_post('blox-admin-nonce', false), 'blox-admin-nonce') )
			return false;

		if ( !is_array(blox_post('blox-licenses')) )
			return false;

		global $blox_admin_save_message;
		global $blox_admin_save_error_message;


		/* Save and activations */
			if ( $save_and_activations = blox_get('save-and-activate', blox_post('blox-licenses')) ) {

				if ( is_array($save_and_activations) && count($save_and_activations) ) {

					foreach ( $save_and_activations as $item_slug_to_activate => $submit_value ) {

						BloxOption::set('license-key-' . $item_slug_to_activate, blox_get('license-key-' . $item_slug_to_activate, blox_post('blox-admin-input')));
						$activation_request = blox_activate_license($item_slug_to_activate);

						self::set_license_activation_message($activation_request);

					}

				}

			}

		/* Activations */
			if ( $activations = blox_get('activate', blox_post('blox-licenses')) ) {

				if ( is_array($activations) && count($activations) ) {

					foreach ( blox_get('activate', blox_post('blox-licenses')) as $item_slug_to_activate => $submit_value ) {

						$activation_request = blox_activate_license($item_slug_to_activate);

						self::set_license_activation_message($activation_request);

					}

				}

			}

		/* Deactivations */
			if ( $deactivations = blox_get('deactivate', blox_post('blox-licenses')) ) {

				if ( is_array($deactivations) && count($deactivations) ) {

					foreach ( blox_get('deactivate', blox_post('blox-licenses')) as $item_slug_to_deactivate => $submit_value ) {

						$deactivation_request = blox_deactivate_license($item_slug_to_deactivate);

						if ( $deactivation_request == 'deactivated' ) {

							$blox_admin_save_message = 'License deactivated.';

						} else if ( !is_wp_error($deactivation_request) ) {

							$blox_admin_save_error_message = '<strong>Whoops!</strong> Could not deactivate license. Please check that you have entered your license correctly.';

						} else {

							$blox_admin_save_error_message = '
								<strong>Error While Deactivating:</strong> (' . $deactivation_request->get_error_code() . ') ' . $deactivation_request->get_error_message() . '<br /><br />
								'  . __('Please contact Blox Support if this error persists.', 'blox') . '
							';

						}

					}

				}

			}


		return true;

	}


		public static function set_license_activation_message($activation_request) {

			global $blox_admin_save_message;
			global $blox_admin_save_error_message;

			if ( $activation_request == 'active' || $activation_request == 'valid' ) {

				$blox_admin_save_message = __('License saved and activated.', 'blox');

			} else if ( $activation_request == 'invalid' || $activation_request == 'expired' ) {

				$blox_admin_save_error_message = __('
					<strong>Whoops!</strong> Could not activate license.  Please check that you have entered your license correctly and that it has not expired.<br /><br />
					Make sure you copied your license correctly from the <a href="http://bloxtheme.com/dashboard" target="_blank">Blox Dashboard</a>.
				', 'blox');

			} else if ( is_wp_error($activation_request) ) {

				$blox_admin_save_error_message = '
					<strong>Error While Activating:</strong> (' . $activation_request->get_error_code() . ') ' . $activation_request->get_error_message() . '<br /><br />
					'  . __('Please contact Blox Support if this error persists.', 'blox') . '
				';

			}

		}

	public static function form_action_delete_snapshots() {

		global $wpdb;

		if ( ! blox_post( 'blox-delete-snapshots', false ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( blox_post( 'blox-delete-snapshots-nonce', false ), 'blox-delete-snapshots-nonce' ) ) {

			$GLOBALS['blox_admin_save_message'] = 'Security nonce did not match.';

			return false;

		}

		/* Loop through WordPress options and delete the skin options */
		$wpdb->query( "TRUNCATE TABLE $wpdb->bt_snapshots" );

		do_action( 'blox_delete_all_snapshots' );

		$GLOBALS['blox_admin_save_message'] = 'Snapshots successfully deleted.';

		return true;

	}


	public static function form_action_reset() {

		global $wpdb;

		if ( !defined('BLOX_ALLOW_RESET') || BLOX_ALLOW_RESET !== true )
			return false;

		//Form action for all Blox configuration panels.  Not in function/hook so it can load before everything else.
		if ( !blox_post('reset-blox', false) )
			return false;

		//Verify the nonce so other sites can't maliciously reset a Blox installation.
		if ( !wp_verify_nonce(blox_post('blox-reset-nonce', false), 'blox-reset-nonce') ) {

			$GLOBALS['blox_admin_save_message'] = 'Security nonce did not match.';

			return false;

		}

		/* Loop through WordPress options and delete the skin options */
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name = 'blox'" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'blox_%'" );

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_bt_%'" );

		/* Remove Blox post meta */
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_bt_%'" );

		/* Drop Blox tables */
		Blox::mysql_drop_tables();

		/* Flush WP cache */
		wp_cache_flush();

		do_action('blox_global_reset');

		$GLOBALS['blox_admin_save_message'] = 'Blox was successfully reset.';

		//This will hide the reset box if set to true.
		$GLOBALS['blox_reset_success'] = true;

		return true;

	}


	public static function activation() {

		if ( !is_admin() || !blox_get('activated') )
			return false;

		global $pagenow;

		if ( $pagenow !== 'themes.php' )
			return false;

		//Since they may be upgrading and files may change, let's clear the cache
		do_action('blox_activation');

		self::activation_redirect();

	}


	public static function activation_redirect() {

		do_action('blox_activation_redirect');

		//If a child theme has been activated rather than Blox, then don't redirect.
		//Let the child theme developer redirect if they want by using the hook above.
		if ( BLOX_CHILD_THEME_ACTIVE === true )
			return false;

		$parent_menu = self::parent_menu();

		//If header were sent, then don't do the redirect
		if ( headers_sent() )
			return false;

		//We're all good, redirect now
		wp_safe_redirect(admin_url('admin.php?page=blox-' . $parent_menu['id']));
		die();

	}


	public static function visual_editor_redirect() {

		if ( isset($_GET['page']) && strpos($_GET['page'], 'blox-visual-editor') !== false && !headers_sent() )
			wp_safe_redirect(home_url() . '/?visual-editor=true');

	}


	public static function add_admin_separator($position){

		global $menu;

		$menu[$position] = array('', 'read', 'separator-blox', '', 'wp-menu-separator blox-separator');

		ksort($menu);

	}


	public static function add_admin_submenu($name, $id, $callback) {

		$parent_menu = self::parent_menu();

		return add_submenu_page('blox-' . $parent_menu['id'], $name, $name, 'manage_options', $id, $callback);

	}


	public static function add_menus(){

		//If the hide menus constant is set to true, don't hide the menus!
		if (defined('BLOX_HIDE_MENUS') && BLOX_HIDE_MENUS === true)
		 	return false;

		//If user cannot access the admin panels, then don't bother running these functions
		if ( !BloxCapabilities::can_user_visually_edit() )
			return false;

		$menu_name = ( BloxOption::get('hide-menu-version-number', false, true) == true ) ? 'Blox Theme' : 'Blox Theme ' . BLOX_VERSION;

		$icon = (version_compare($GLOBALS['wp_version'], '3.8', '>=') && get_user_option('admin_color') != 'light') ? 'blox-32-white.png' : 'blox-16.png';
		$icon_url = blox_url() . '/library/admin/images/' . $icon;

		$parent_menu = self::parent_menu();

		self::add_admin_separator(48);

		add_menu_page($parent_menu['name'], $menu_name, 'manage_options', 'blox-' . $parent_menu['id'], $parent_menu['callback'], $icon_url, 49);

			switch ( $parent_menu['id'] ) {

				case 'getting-started':
					self::add_admin_submenu('Getting Started', 'blox-getting-started', array('BloxAdminPages', 'getting_started'));
					self::add_admin_submenu('Visual Editor', 'blox-visual-editor', array('BloxAdminPages', 'visual_editor'));

					self::add_admin_submenu('Templates', 'blox-templates', array('BloxAdminPages', 'templates'));
					self::add_admin_submenu('Options', 'blox-options', array('BloxAdminPages', 'options'));
					self::add_admin_submenu('Tools', 'blox-tools', array('BloxAdminPages', 'tools'));
					self::add_admin_submenu('License', 'blox-license', array('BloxAdminPages', 'license'));
				break;

				case 'visual-editor':
					self::add_admin_submenu('Visual Editor', 'blox-visual-editor', array('BloxAdminPages', 'visual_editor'));

					self::add_admin_submenu('Templates', 'blox-templates', array('BloxAdminPages', 'templates'));
					self::add_admin_submenu('Options', 'blox-options', array('BloxAdminPages', 'options'));
					self::add_admin_submenu('Tools', 'blox-tools', array('BloxAdminPages', 'tools'));
					self::add_admin_submenu('License', 'blox-license', array('BloxAdminPages', 'license'));
				break;

				case 'options':
					self::add_admin_submenu('Options', 'blox-options', array('BloxAdminPages', 'options'));
					self::add_admin_submenu('Visual Editor', 'blox-visual-editor', array('BloxAdminPages', 'visual_editor'));
					self::add_admin_submenu('Templates', 'blox-templates', array('BloxAdminPages', 'templates'));
					self::add_admin_submenu('Tools', 'blox-tools', array('BloxAdminPages', 'tools'));
					self::add_admin_submenu('License', 'blox-license', array('BloxAdminPages', 'license'));
				break;

			}

	}


	public static function parent_menu() {

		$menu_setup = BloxOption::get('menu-setup', false, 'getting-started');

		/* Figure out the primary page */
		switch ( $menu_setup ) {

			case 'getting-started':
				$parent_menu = array(
					'id' => 'getting-started',
					'name' => 'Getting Started',
					'callback' => array('BloxAdminPages', 'getting_started')
				);
			break;

			case 'options':
				$parent_menu = array(
					'id' => 'options',
					'name' => 'Options',
					'callback' => array('BloxAdminPages', 'options')
				);
			break;

			default:
				$parent_menu = array(
					'id' => 'visual-editor',
					'name' => 'Visual Editor',
					'callback' => array( 'BloxAdminPages', 'visual_editor' )
				);
			break;

		}

		return $parent_menu;

	}


	public static function enqueue() {

		global $pagenow;

		/* Global */
		wp_enqueue_style('blox_admin_global', blox_url() . '/library/admin/css/admin-blox-global.css');
                wp_enqueue_script('blox_admin_js', blox_url() . '/library/admin/js/admin-blox.js', array('jquery'));

        /* General Blox admin CSS/JS */
		if ( strpos(blox_get('page'), 'blox') !== false ) {

			wp_enqueue_script('blox_jquery_scrollto', blox_url() . '/library/admin/js/jquery.scrollto.js', array('jquery'));
			wp_enqueue_script('blox_jquery_tabby', blox_url() . '/library/admin/js/jquery.tabby.js', array('jquery'));
			wp_enqueue_script('blox_jquery_qtip', blox_url() . '/library/admin/js/jquery.qtip.js', array('jquery'));
            wp_enqueue_script('blox_admin_js', blox_url() . '/library/admin/js/admin-blox.js', array('jquery', 'blox_jquery_qtip'));

            wp_enqueue_style('blox_admin', blox_url() . '/library/admin/css/admin-blox.css');
			wp_enqueue_style('blox_alerts', blox_url() . '/library/media/css/alerts.css');

		}

		/* Templates */
		if ( blox_get('page') == 'blox-templates' ) {

			wp_enqueue_script('blox_knockout', blox_url() . '/library/admin/js/knockout.js', array('jquery'));
			wp_enqueue_script('blox_admin_templates', blox_url() . '/library/admin/js/admin-templates.js', array('jquery'));

			wp_localize_script('blox_admin_templates', 'Blox', array(
				'ajaxURL' => admin_url('admin-ajax.php'),
				'security' => wp_create_nonce('blox-visual-editor-ajax'),

				'templates' => BloxTemplates::get_all(),
				'templateActive' => BloxTemplates::get_active(),

				'viewModels' => array()
			));

			add_thickbox();
			wp_enqueue_media();

		}

		/* Meta Boxes */
		wp_enqueue_style('blox_admin_write', blox_url() . '/library/admin/css/admin-write.css');
		wp_enqueue_style('blox_alerts', blox_url() . '/library/media/css/alerts.css');
		wp_enqueue_script('blox_admin_write', blox_url() . '/library/admin/js/admin-write.js', array('jquery'));
                $css_src = includes_url('css/') . 'editor.css';
                wp_register_style('tinymce_css', $css_src);
                wp_enqueue_style('tinymce_css');


		/* Auto Updater */
		if ( $pagenow === 'update-core.php' ) {

			wp_enqueue_style('blox_admin', blox_url() . '/library/admin/css/admin-blox.css');
			wp_enqueue_style('blox_alerts', blox_url() . '/library/media/css/alerts.css');

		}

	}


	public static function save_message() {

		global $blox_admin_save_message;

		if ( !isset($blox_admin_save_message) || $blox_admin_save_message == false )
			return false;

		echo '<div id="setting-error-settings_updated" class="updated settings-error"><p>' . $blox_admin_save_message . '</p></div>';

	}


	public static function save_error_message() {

		global $blox_admin_save_error_message;

		if ( !isset($blox_admin_save_error_message) || $blox_admin_save_error_message == false )
			return false;

		echo '<div id="setting-error-settings_error" class="error settings-error"><p>' . $blox_admin_save_error_message . '</p></div>';

	}


	public static function notice_no_widgets_or_menus() {

		global $pagenow;

		if ( $pagenow != 'widgets.php' && $pagenow != 'nav-menus.php' )
			return false;

		$grid_mode_url = add_query_arg(array('visual-editor' => 'true', 'visual-editor-mode' => 'grid'), home_url());

		//Show the widgets message if no widget blocks exist.
		if ( $pagenow == 'widgets.php' ) {

			$widget_area_blocks = BloxBlocksData::get_blocks_by_type('widget-area');

			if ( !empty($widget_area_blocks) )
				return;

			if ( !current_theme_supports('blox-grid') )
				return;

			echo '<div class="updated" style="margin-top: 15px;">
			       <p>Blox has detected that you have no Widget Area blocks.  If you wish to use the WordPress widgets system with Blox, please add a Widget Area block in the <a href="' . $grid_mode_url . '" target="_blank">Visual Editor: Grid</a>.</p>

					<style type="text/css">
						div.error.below-h2 { display: none; }
						div.error.below-h2 + p { display: none; }
					</style>
			    </div>';

		}

		//Show the navigation menus message if no navigation blocks exist.
		if ( $pagenow == 'nav-menus.php' ) {

			$navigation_blocks = BloxBlocksData::get_blocks_by_type('navigation');

			if ( !empty($navigation_blocks) )
				return;

			if ( !current_theme_supports('blox-grid') )
				return;

			echo '<div class="updated">
			       <p>Blox has detected that you have no Navigation blocks.  If you wish to use the WordPress menus system with Blox, please add a Navigation block in the <a href="' . $grid_mode_url . '" target="_blank">Visual Editor: Grid</a>.</p>
			    </div>';

		}

	}


	public static function theme_install_template_notice() {

		global $pagenow;

		if ( $pagenow != 'theme-install.php' )
			return false;

		echo '<div class="error">
				<h3>Are you trying to install a Blox Template?</h3>
			  	 <p>Please go to <a href="' . admin_url('admin.php?page=blox-templates') . '">Blox &rsaquo; Templates</a> to install Templates.</p>
			</div>';


	}


    public static function responsive_grid_notice() {

        $dismissed_notices = BloxOption::get('dismissed-notices', false, array());

        if ( BloxSkinOption::get('enable-responsive-grid', false, true) || in_array('responsive-grid', $dismissed_notices) ) {
            return false;
        }

        echo '<div id="blox-responsive-grid-notice" data-blox-notice="responsive-grid" class="notice notice-warning is-dismissible" style="padding-top: 0.5em;padding-bottom: 0.5em;">
				<h3 style="margin: 0.5em 0">Important! Your site is currently not mobile-friendly.</h3>
                <p>Google now penalizes websites that are not mobile-friendly. Enabling the Responsive Grid will make your website mobile-friendly in most cases.</p>
                <p><strong>Please note:</strong> Enabling the responsive grid can cause styling and layout changes for some websites. You can always disable Responsive Grid under the Grid mode in the Visual Editor.</p>
                <p><button class="button-primary">Enable Responsive Grid</button>&emsp;&emsp;<button class="button-secondary blox-dismiss-notice">Dismiss</button></p>
			</div>';

    }


	public static function show_header($title = false) {

		echo '<div class="wrap blox-page">';

		if ( $title )
			echo '<h2>' . $title . '</h2>';

	}


	public static function show_footer() {

		echo '</div><!-- #wrapper -->';

	}


	public static function row_action_visual_editor($actions, $item) {

		if ( !BloxCapabilities::can_user_visually_edit() )
			return $actions;

		/* Post */
		if ( isset($item->post_status) ) {

			if ( $item->post_status != 'publish' )
				return $actions;

			$post_type = get_post_type_object($item->post_type);

			if ( !$post_type->public )
				return $actions;

			$layout_id = 'single' . BloxLayout::$sep . $item->post_type . BloxLayout::$sep . $item->ID;

			if ( get_option('show_on_front') === 'page' ) {

				if ( $item->ID == get_option('page_on_front') )
					$layout_id = 'front_page';

				if ( $item->ID == get_option('page_for_posts') )
					$layout_id = 'index';

			}

		/* Category */
		} elseif ( isset($item->term_id) && $item->taxonomy == 'category' ) {

			$layout_id = 'archive' . BloxLayout::$sep . 'category' . BloxLayout::$sep . $item->term_id;

		/* Post Tag */
		} elseif ( isset($item->term_id) && $item->taxonomy == 'post_tag' ) {

			$layout_id = 'archive' . BloxLayout::$sep . 'post_tag' . BloxLayout::$sep . $item->term_id;

		/* Taxonomy */
		} elseif ( isset($item->term_id) ) {

			$layout_id = 'archive' . BloxLayout::$sep . 'taxonomy' . BloxLayout::$sep . $item->taxonomy . BloxLayout::$sep . $item->term_id;

		}

		$visual_editor_url = home_url('/?visual-editor=true&ve-layout=' . urlencode($layout_id));

		$actions['bt-visual-editor'] = '<a href="' . $visual_editor_url . '" title="Open in Blox Visual Editor" rel="permalink" target="_blank">Open in Visual Editor</a>';

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

        $notice_to_dismiss = blox_post('notice-to-dismiss');

        $dismissed_notices = BloxOption::get('dismissed-notices', false, array());
        $dismissed_notices[] = $notice_to_dismiss;

        return BloxOption::set('dismissed-notices', array_unique($dismissed_notices));

    }


    public static function ajax_enable_responsive_grid() {

        return BloxSkinOption::set('enable-responsive-grid', true);

    }


}
