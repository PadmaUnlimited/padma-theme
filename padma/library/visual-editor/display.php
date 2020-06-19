<?php
class PadmaVisualEditorDisplay {


	public static function init() {


		Padma::load('visual-editor/layout-selector');

		//Load boxes
		Padma::load('abstract/api-box');
		require_once PADMA_LIBRARY_DIR . '/visual-editor/boxes/grid-manager.php';
		require_once PADMA_LIBRARY_DIR . '/visual-editor/boxes/snapshots.php';

		//Load panels
		if ( current_theme_supports('padma-grid') ) {
			require_once PADMA_LIBRARY_DIR . '/visual-editor/panels/grid/setup.php';
		}

		if ( current_theme_supports('padma-design-editor') ) {
			Padma::load('visual-editor/panels/design/side-panel-design-editor', 'SidePanelDesignEditor');
		}

		//Put in action so we can run top level functions
		do_action('padma_visual_editor_display_init');

		//System for scripts/styles
		add_action('padma_visual_editor_head', array(__CLASS__, 'print_scripts'), 12);
		add_action('padma_visual_editor_head', array(__CLASS__, 'print_styles'), 12);

		//Meta
		add_action('padma_visual_editor_head', array(__CLASS__, 'robots'));

		//Enqueue Styles
		remove_all_actions('wp_print_styles'); //Removes bad plugin CSS
		add_action('padma_visual_editor_styles', array(__CLASS__, 'enqueue_styles'));
		add_action('padma_visual_editor_head', array(__CLASS__, 'output_inline_loading_css'), 10);

		//Enqueue Scripts
		remove_all_actions('wp_print_scripts'); //Removes bad plugin JS

		add_filter( 'script_loader_tag', array( __CLASS__, 'require_js_attr' ), 15, 3 );
		add_action('padma_visual_editor_scripts', array(__CLASS__, 'require_js'));

		//Localize Scripts
		add_action('padma_visual_editor_scripts', array(__CLASS__, 'add_visual_editor_js_vars'));

		//Content
		add_action('padma_visual_editor_menu', array(__CLASS__, 'layout_selector'));

		//add_action('padma_visual_editor_menu', array(__CLASS__, 'content_selector')); // Disabled until other release


		add_action('padma_visual_editor_modes', array(__CLASS__, 'mode_navigation'));
		add_action('padma_visual_editor_menu_links', array(__CLASS__, 'menu_links'));
		add_action('padma_visual_editor_footer', array(__CLASS__, 'block_type_selector'));

		add_action('padma_visual_editor_panel_top_right', array(__CLASS__, 'panel_top_right'), 12);
		add_action('padma_visual_editor_menu_mode_buttons', array(__CLASS__, 'menu_mode_buttons'));

		//Prevent any type of caching on this page
		header( 'cache-control: private, max-age=0, no-cache' );

		if ( !defined('DONOTCACHEPAGE') ) { 
			define('DONOTCACHEPAGE', true);
		}

		if ( !defined('DONOTMINIFY') ) { 
			define('DONOTMINIFY', true);
		}

	}


	public static function robots() {

		echo '<meta name="robots" content="noindex" />' . "\n";

	}


	public static function display() {

		do_action('padma_visual_editor_display');

		require_once PADMA_LIBRARY_DIR . '/visual-editor/template.php';

	}


	public static function require_js() {

		$script_folder = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'scripts-src' : 'scripts-src';

		wp_enqueue_script('padma-editor', padma_url() . '/library/visual-editor/' . $script_folder . '/deps/require-and-jquery.js');

	}


	public static function require_js_attr( $tag, $handle, $src ) {

		$script_folder = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'scripts-src' : 'scripts-src';

		if ( false !== strpos( $src, 'require-and-jquery.js' ) ) {

			return "<script type='text/javascript' id='padma-editor' src='{$src}' data-main='" . padma_url() . "/library/visual-editor/{$script_folder}/app.js'></script>";

		}

		return str_replace( "></script>", " async='true'></script>", $tag );

	}


	public static function enqueue_styles() {

		$styles = array(
			'reset' => padma_url() . '/library/media/css/reset.css',
			'open-sans',
			'dashicons',
			'padma_visual_editor' => padma_url() . '/library/visual-editor/css/editor.css',
			'padma_visual_editor_night' => padma_url() . '/library/visual-editor/css/editor-night.css',			
		);

		wp_enqueue_multiple_styles($styles);
	}


	public static function output_inline_loading_css() {

		$css = '';
		$path = PADMA_LIBRARY_DIR . '/visual-editor/css-src/_loading.scss';

		/* Insure file exists */
			if ( !file_exists($path) )
				return false;

		/* Load in editor-loading.css */
			$temp_handler = fopen($path, 'r');
			$css .= fread($temp_handler, filesize($path));
			fclose($temp_handler);

		/* Echo content */
			echo "\n" . '<style type="text/css">' . PadmaCompiler::strip_whitespace($css) . '</style>' . "\n\n";

	}


	public static function print_scripts() {

		/* Remove all other enqueued scripts from plugins that don't use 'padma_visual_editor_scripts' to reduce conflicts */
			global $wp_scripts;
			$wp_scripts = null;
			remove_all_actions('wp_print_scripts');

		echo "\n<!-- Scripts -->\n";

		do_action('padma_visual_editor_scripts');

		if (PadmaOption::get('headway-support')) {
			do_action('headway_visual_editor_scripts');
		}

		if (PadmaOption::get('bloxtheme-support')) {
			do_action('blox_visual_editor_scripts');
		}

		wp_print_scripts();

		echo "\n";

	}


	public static function print_styles() {

		/* Remove all other enqueued styles from plugins that don't use 'padma_visual_editor_styles' to reduce conflicts */
			global $wp_styles;
			$wp_styles = null;
			remove_all_actions('wp_print_styles');

		echo "\n<!-- Styles -->\n";

		do_action('padma_visual_editor_styles');

		wp_print_styles();

		echo "\n";

	}


	public static function add_visual_editor_js_vars() {

		global $wp_scripts;

		//Gather the URLs for the block types
		$block_types = PadmaBlocks::get_block_types();
		$block_type_urls = array();

		foreach ( $block_types as $block_type => $block_type_options )
			$block_type_urls[$block_type] = $block_type_options['url'];

		$current_layout_status = PadmaLayout::get_status(PadmaLayout::get_current());

		wp_localize_script('padma-editor', 'Padma', array(
			'ajaxURL' => admin_url('admin-ajax.php'),
			'security' => wp_create_nonce('padma-visual-editor-ajax'),

			'currentLayout' => PadmaLayout::get_current(),
			'currentLayoutName' => PadmaLayout::get_name( PadmaLayout::get_current() ),
			'currentLayoutInUse' => PadmaLayout::get_current_in_use(true),
			'currentLayoutInUseName' => PadmaLayout::get_name( PadmaLayout::get_current_in_use(true) ),
			'currentLayoutCustomized' => $current_layout_status['customized'],
			'currentLayoutTemplate' => $current_layout_status['template'],
			'currentLayoutTemplateName' => PadmaLayout::get_name('template-' . $current_layout_status['template']),

			'siteName' => get_bloginfo('name'),
			'siteDescription' => get_bloginfo('description'),
			'padmaURL' => get_template_directory_uri(),
			'scriptFolder' => ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'scripts-src' : 'scripts-src',
			'siteURL' => site_url(),
			'homeURL' => home_url(),
			'adminURL' => admin_url(),
			'frontPage' => get_option('show_on_front', 'posts'),

			'mode' => PadmaVisualEditor::get_current_mode(),
			'designEditorSupport' => current_theme_supports('padma-design-editor'),
			'gridSupported' => current_theme_supports('padma-grid'),

			'disableTooltips' => PadmaOption::get('disable-visual-editor-tooltips', false, false),

			'designEditorProperties' => PadmaVisualEditor::is_mode('design') ? json_encode(PadmaElementProperties::get_properties()) : json_encode(array()),
			'colorpickerSwatches' => PadmaSkinOption::get('colorpicker-swatches', false, array()),
			'gridSafeMode' => PadmaOption::get('grid-safe-mode', false, false),

			'ranTour' => json_encode(array(
				'legacy' => PadmaOption::get('ran-tour', false, false),
				'grid' => PadmaOption::get('ran-tour-grid', false, false),
				'design' => PadmaOption::get('ran-tour-design', false, false)
			)),

			'blockTypeURLs' => json_encode($block_type_urls),
			'allBlockTypes' => json_encode($block_types),

			'defaultGridColumnCount' => PadmaWrappers::$default_columns,
			'globalGridColumnWidth' => PadmaWrappers::$global_grid_column_width,
			'globalGridGutterWidth' => PadmaWrappers::$global_grid_gutter_width,

			'responsiveGrid' => PadmaResponsiveGrid::is_enabled(),

			'touch' => PadmaMobileDetect::isMobile(),

			'layouts' => json_encode(array(
				'pages' => PadmaLayoutSelector::get_basic_pages(),
				'shared' => PadmaLayoutSelector::get_templates()
			)),


			'snapshots' => PadmaDataSnapshots::list_snapshots(),

			'viewModels' => array(),

            'rJSCacheBuster' => ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? true : false
		));

	}


	//////////////////    Content   ///////////////////////


	public static function panel_top_right() {


		/**
		 *
		 * Minimize option
		 *
		 */
		$html = '';
		$html .= '<li id="minimize">
					<span title="' . __('Minimize Panel &lt;strong&gt;Shortcut: Ctrl + P&lt;/strong&gt;','padma') . '" class="tooltip-bottom-right">g</span>
				</li>';

		echo $html;

	}


	public static function menu_mode_buttons() {

		switch ( PadmaVisualEditor::get_current_mode() ) {

			case 'design':

				if ( current_theme_supports('padma-design-editor') ) {

					$tooltip = '<strong>' . __('Toggle Inspector','padma') . '</strong><br />
						<em>' . __('Shortcut:</em> Ctrl + I','padma') . '<br /><br />
						' . __('<strong>How to use:</strong> <em>Right-click</em> highlighted elements to style them.  Once an element is selected, you may nudge it using your arrow keys.','padma') . '<br /><br />
						' . __('The faded orange and purple are the margins and padding. These colors are only visible when the inspector is active.','padma');

					echo '<div class="menu-mode-buttons">';
						echo '<span class="menu-mode-button tooltip-bottom-right" id="toggle-inspector" title="' . esc_attr($tooltip) . '"></span>';
						echo '<span class="menu-mode-button tooltip-bottom-right" id="open-live-css" title="Open CSS Editor"></span>';
					echo '</div>';

				}

			break;

		}

	}


	public static function block_type_selector() {

		$block_types = PadmaBlocks::get_block_types();



		echo "\n". '<div class="block-type-selector block-type-selector-original" style="display: none;">' . "\n";

		echo '<div class="block-type-selector-filter">
				<div class="filter-search">
					<input type="text" id="block-type-selector-filter-text" placeholder="Filter" title="Filter blocks">
					<a class="block-type-selector-filter-reset"><span>x</span></a>
				</div>';

		echo '<ul class="block-type-selector-filter-categories">';
		echo '<li><a class="active" data-filter="all">All</a></li>';

		foreach (PadmaBlocks::get_registered_blocks_categories() as $categorie => $blocks) {
			echo '<li><a class="" data-filter="'.$categorie.'">' . ucfirst(str_replace('-', ' ', $categorie)) . '</a></li>';
		}
		echo '</ul>';
		echo '</div>';
		echo '<div class="block-type-selector-items">';

			
			/*
			usort( $block_types , function($a, $b){								
				return strcmp($a["name"], $b["name"]);
			});*/			

			foreach ( $block_types as $block_type_id => $block_type ) {

				$filter_categories = '';
				foreach ($block_type['categories'] as $key => $value) {
					$filter_categories .= 'filter-' . $value . ' '; 
				}

				$icon = '/icon.svg';				
				if( is_array($block_type['icons']) && !empty($block_type['icons']) ){
					
					$icon = $block_type['icons']['url'] . $icon;

				}else{
					
					if( !file_exists( PADMA_LIBRARY_DIR . '/blocks/' . $block_type_id . '/' .  $icon) ){
						$icon =  '/icon.png';
					}					
					if (!filter_var($icon, FILTER_VALIDATE_URL)){
						$icon = $block_type['url'] . '/icon.png';
					}
				}
				

				echo '<div id="block-type-' . $block_type_id . '" class="block-type '.$filter_categories.'" title="' . $block_type['description'] . '">';
				echo '<div class="block-detail" style="background-image: url(' . $icon . ');">
						<div class="block-detail-name" >' . $block_type['name'] . '</div>
						</div>';
				echo '</div>';
								

			}

			echo '<div id="get-more-blocks" class="block-type filter-core filter-media tooltip" title="' . __('Get more blocks', 'padma') . '">';
				echo '<div class="block-detail" style="background-image: url('.get_template_directory_uri().'/library/media/img/get-more-blocks.svg);">
						<div class="block-detail-name" >
							<a target="_blank" href="https://dashboard.padmaunlimited.com/login"> ' . __('Get more blocks', 'padma') .'</a>
						</div>
					</div>
				</div>';

		echo '</div>';



		echo '</div>' . "\n\n";

	}


	public static function layout_selector() {

		require_once PADMA_LIBRARY_DIR . '/visual-editor/template-layout-selector.php';

	}


	public static function content_selector() {

		if(PadmaVisualEditor::is_mode('design') && strpos(PadmaLayout::get_current_in_use(), 'template') == 0){
			require_once PADMA_LIBRARY_DIR . '/visual-editor/template-content-selector.php';
		}

	}


	public static function is_any_layout_child_customized($children) {

		if ( !is_array($children) || count($children) == 0 )
			return false;

		foreach ( $children as $id => $grand_children ) {

			$status = PadmaLayout::get_status($id);

			if ( padma_get('customized', $status) || padma_get('template', $status) )
				return true;

			if ( is_array($grand_children) && count($grand_children) > 0 && self::is_any_layout_child_customized($grand_children) === true )
				return true;

		}

		return false;

	}


	public static function mode_navigation() {

		foreach ( PadmaVisualEditor::get_modes() as $mode => $tooltip ) {

			$current = ( PadmaVisualEditor::is_mode($mode) ) ? ' class="active"' : null;
			$mode_id = strtolower($mode);

			echo '<li' . $current . ' id="mode-'. $mode_id . '">
					<a href="' . home_url() . '/?visual-editor=true&amp;visual-editor-mode=' . $mode_id . '" title="' . esc_attr($tooltip) . '" class="tooltip-top-left">
						<span>' . ucwords($mode) . '</span>
					</a>
				</li>';

		}

	}


	public static function menu_links() {

		echo '<li id="menu-link-tools" class="has-submenu">
				<span>Tools</span>

				<ul>';

					if ( PadmaVisualEditor::is_mode('grid') )
						echo '<li id="tools-grid-manager"><span>' . __('Grid Manager', 'padma') . '</span></li>';

					if ( PadmaCompiler::can_cache() )
						echo '<li id="tools-clear-cache"><span>' . __('Clear Cache', 'padma') . ' ' . (!PadmaCompiler::caching_enabled() ? ' (' . __('Disabled', 'padma') . ')' : '') . '</span></li>';

					echo '<li id="tools-tour"><span>Tour</span></li>
				</ul>

			</li>';


		echo '<li id="menu-link-admin" class="has-submenu">
				<span>Admin</span>

				<ul>
					<li><a href="' . admin_url()  . '" target="_blank">' . __('Dashboard', 'padma') . '</a></li>
					<li><a href="' . admin_url('widgets.php')  . '" target="_blank">' . __('Widgets', 'padma') . '</a></li>
					<li><a href="' . admin_url('nav-menus.php')  . '" target="_blank">' . __('Menus', 'padma') . '</a></li>
					<li><a href="' . admin_url('admin.php?page=padma-options')  . '" target="_blank">' . __('Padma Options', 'padma') . '</a></li>
					<li><a href="' . admin_url('admin.php?page=padma-templates')  . '" target="_blank">' . __('Padma Templates', 'padma') . '</a></li>
					<li><a href="' . admin_url('admin.php?page=padma-tools')  . '" target="_blank">' . __('Padma Tools', 'padma') . '</a></li>
					<li><a href="https://docs.padmaunlimited.com" target="_blank" rel="noopener">' . __('Documentation', 'padma') . '</a></li>
					<li><a href="mailto:support@padmaunlimited.com" target="_blank">' . __('Support', 'padma') . '</a></li>
					<li><a href="https://www.padmaunlimited.com/community/" target="_blank" rel="noopener">' . __('Community', 'padma') . '</a></li>
					<li><a href="https://www.padmaunlimited.com/get-started/how-to-collaborate/" target="_blank" rel="noopener">' . __('Get Involved','padma') . '</a></li>
				</ul>

			</li>';


		echo '<li id="menu-link-view-site"><a href="' . home_url() . '" target="_blank">' . __('View Site','padma') . '</a></li>';

	}
}