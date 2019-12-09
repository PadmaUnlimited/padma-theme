<?php
class PadmaBlocks {


	public static $block_actions = array(
		'init' => array(),
		'enqueue' => array(),
		'dynamic-js' => array(),
		'dynamic-css' => array(),
		'block-objects' => array()
	);

	public static $core_blocks = array(
		'audio',		
		'breadcrumbs',
		'contact-form-7',
		'content',
		'custom-code',
		'divider',
		'embed',
		'footer',
		'gravity-forms',
		'header',
		'image',
		'listings',
		'mailchimp-for-wp',
		'navigation',
		'onepage-navigation',
		'pin-board',
		'search',
		'slidedeck',
		'slider',
		'site-logo',
		'social',
		'text',
		'video',
		'widget-area',
	);

	public static function init() {

		Padma::load(array(
			'abstract/api-block'
		));

		//self::load_core_blocks();
		self::register_core_blocks();


		add_action('init', array(__CLASS__, 'register_block_types'), 8);

		add_action('init', array(__CLASS__, 'process_registered_blocks'), 9);

		/* Handle block-specific actions */
		add_action('init', array(__CLASS__, 'setup_block_actions'), 10);

		add_action('init', array(__CLASS__, 'run_block_init_actions'), 11);
		add_action('wp_head', array(__CLASS__, 'run_block_enqueue_actions'), 5);
		add_action('wp_head', array(__CLASS__, 'enqueue_block_dynamic_js_file'), 5);
		/* End block-specific actions */

		add_action('padma_register_elements_instances', array(__CLASS__, 'register_block_element_instances'), 11);

		add_action('padma_block_content_unknown', array(__CLASS__, 'unknown_block_content'));

		/* Clear the block actions cache upon Visual Editor save and others */
		add_action('padma_flush_cache', array(__CLASS__, 'clear_block_actions_cache'));

	}



	public static function register_block_types() {

		global $padma_unregistered_block_types;
		global $padma_registry;

		foreach ( $padma_unregistered_block_types as $class => $block_type_data ) {

			if( is_array($block_type_data) ){

				// Add this class and path to global class padma_registry to be loaded in loader.php
				$padma_registry = array_merge($padma_registry, array($class => $block_type_data['block_type_path']));

				if ( !class_exists($class) )
					return new WP_Error('block_class_does_not_exist', __('The block class being registered does not exist.', 'padma'), $class);

				$block = new $class();
				
				if ( $block_type_data['block_type_url'] )
					$block->block_type_url = untrailingslashit($block_type_data['block_type_url']);

				if ( $block_type_data['block_type_path'] )
					$block->block_type_path = untrailingslashit($block_type_data['block_type_path']);

				if ( $block_type_data['block_type_icons'] )
					$block->block_type_icons = $block_type_data['block_type_icons'];


				$block->register();

				unset($block);

			}else{


				if ( !class_exists($class) )
					return new WP_Error('block_class_does_not_exist', __('The block class being registered does not exist.', 'padma'), $class);

				$block = new $class();

				// Before Padma 1.2.0: block_type_url = block_type_data
				if ( $block_type_data )
					$block->block_type_url = untrailingslashit($block_type_data);

				$block->register();

				unset($block);

			}


		}
		unset($padma_unregistered_block_types);
		return true;

	}


	public static function process_registered_blocks() {

		do_action('padma_register_blocks');

	}


	public static function load_core_blocks() {

		foreach ( apply_filters('padma_core_block_types', self::$core_blocks) as $block ) {

			$block_path = '/blocks/' . $block . '/' . $block . '.php';

			/* Allow blocks to be overriden by child themes */
			if ( PADMA_CHILD_THEME_ACTIVE && file_exists( untrailingslashit(PADMA_CHILD_THEME_DIR) . $block_path ) )
				require_once untrailingslashit( PADMA_CHILD_THEME_DIR ) . $block_path;
			else
				require_once PADMA_LIBRARY_DIR . $block_path;

		}

	}


	public static function register_core_blocks(){

		foreach ( apply_filters('padma_core_block_types', self::$core_blocks) as $block ) {

			$register_file = '/blocks/' . $block . '/block-register.php';

			/* Allow blocks to be overriden by child themes */
			if ( PADMA_CHILD_THEME_ACTIVE && file_exists( untrailingslashit(PADMA_CHILD_THEME_DIR) . $register_file ) )
				require_once untrailingslashit( PADMA_CHILD_THEME_DIR ) . $register_file;
			else
				require_once PADMA_LIBRARY_DIR . $register_file;

		}

	}


	public static function setup_block_actions() {

		/* If cache exists then use it */
			if ( ($block_actions_transient = get_transient('pu_block_actions_template_' . PadmaOption::$current_skin)) && !(padma_get('ve-iframe') && PadmaCapabilities::can_user_visually_edit()) ) {

				self::$block_actions = $block_actions_transient;

				return self::$block_actions;

			}

		/* Build the cache */
			$block_types = self::get_block_types();

			foreach ( $block_types as $block_type => $block_type_options ) {

				//Make sure that the block type has at least one of the following: init_action, enqueue_action, or dynamic_js
				if (
					!method_exists($block_type_options['class'], 'init_action')
					&& !method_exists($block_type_options['class'], 'enqueue_action')
					&& !(method_exists($block_type_options['class'], 'dynamic_js') || method_exists($block_type_options['class'], 'js_content'))
					&& !method_exists($block_type_options['class'], 'dynamic_css')
				)
					continue;

				$blocks = PadmaBlocksData::get_blocks_by_type($block_type);

				/* If there are no blocks for this type, skip it */
				if ( !is_array($blocks) || count($blocks) === 0 )
					continue;

				/* Go through each type and add a flag if the method exists */
				foreach ( $blocks as $block_id => $block ) {

					$layout_id = $block['layout'];

					/* Make sure that the layout is set to customized and not using a template */
					if ( !PadmaLayout::is_customized($layout_id) && strpos($layout_id, 'template-') === false )
						continue;

					/* Check that the block's wrapper isn't mirrored */
					if ( PadmaWrappersData::is_wrapper_mirrored(PadmaWrappersData::get_wrapper(padma_get('wrapper_id', $block))) )
						continue;

					/* If layout ID is numeric (a post), change it to the single-POSTTYPE-ID format */
					if ( is_numeric($layout_id) )
						$layout_id = 'single-' . get_post_type($layout_id) . '-' . $layout_id;

					/* Init */
						if ( method_exists($block_type_options['class'], 'init_action') ) {

							if ( !isset(self::$block_actions['init'][$layout_id]) )
								self::$block_actions['init'][$layout_id] = array();

							if ( !PadmaBlocksData::is_block_mirrored($block) )
								self::$block_actions['init'][$layout_id][] = $block_id;

						}
					/* End Init */

					/* Enqueue */
						if ( method_exists($block_type_options['class'], 'enqueue_action') ) {

							if ( !isset(self::$block_actions['enqueue'][$layout_id]) )
								self::$block_actions['enqueue'][$layout_id] = array();

							self::$block_actions['enqueue'][$layout_id][] = $block_id;

						}
					/* End Enqueue */

					/* Dynamic JS */
						if ( method_exists($block_type_options['class'], 'dynamic_js') || method_exists($block_type_options['class'], 'js_content') ) {

							if ( !isset(self::$block_actions['dynamic-js'][$layout_id]) )
								self::$block_actions['dynamic-js'][$layout_id] = array();

							self::$block_actions['dynamic-js'][$layout_id][] = $block_id;

						}
					/* End JS Content */

					/* Dynamic CSS */
						if ( method_exists($block_type_options['class'], 'dynamic_css') ) {

							if ( !isset(self::$block_actions['dynamic-css'][$layout_id]) )
								self::$block_actions['dynamic-css'][$layout_id] = array();

							self::$block_actions['dynamic-css'][$layout_id][] = $block_id;

						}
					/* End Dynamic CSS */

					/* Add block to Block Objects Array */
						if ( !isset(self::$block_actions['block-objects']) || !is_array(self::$block_actions['block-objects']) )
							self::$block_actions['block-objects'] = array();

						if ( !padma_get($block_id, self::$block_actions['block-objects']) ) {

							self::$block_actions['block-objects'][$block_id] = $block;
							self::$block_actions['block-objects'][$block_id]['class'] = $block_type_options['class'];
							self::$block_actions['block-objects'][$block_id]['layout'] = $layout_id;

						}
					/* End block objects array */

				}

			}

		/* Pull block actions from blocks in mirrored wrappers */
			$mirrored_wrappers = PadmaWrappersData::get_all_wrappers(false, true);

			self::$block_actions['enqueue']     = array_merge_recursive( self::$block_actions['enqueue'], self::get_block_actions_from_mirrored_wrappers( $mirrored_wrappers, 'enqueue' ) );
			self::$block_actions['dynamic-js']  = array_merge_recursive( self::$block_actions['dynamic-js'], self::get_block_actions_from_mirrored_wrappers( $mirrored_wrappers, 'dynamic-js' ) );
			self::$block_actions['dynamic-css'] = array_merge_recursive( self::$block_actions['dynamic-css'], self::get_block_actions_from_mirrored_wrappers( $mirrored_wrappers, 'dynamic-css' ) );

		/* Set the cache */
			set_transient('pu_block_actions_template_' . PadmaOption::$current_skin, self::$block_actions);

			return self::$block_actions;


	}


		public static function clear_block_actions_cache() {

			return delete_transient('pu_block_actions_template_' . PadmaOption::$current_skin);

		}


	public static function get_block_actions_from_mirrored_wrappers($wrappers, $block_action) {

		$blocks = array();

		foreach ( $wrappers as $wrapper_id => $wrapper ) {

			if ( !$wrapper_being_mirrored = PadmaWrappersData::get_wrapper_mirror($wrapper) )
				continue;

			foreach ( padma_get($wrapper_being_mirrored['layout'], self::$block_actions[$block_action], array()) as $block_id_on_layout_of_mirrored_wrapper ) {

				/* Make sure the block on the layout from the mirrored wrapper is in the actual wrapper that's mirrored otherwise potentially a ton of extra CSS/JS will be loaded from blocks not in use */
				if ( padma_get('wrapper_id', padma_get($block_id_on_layout_of_mirrored_wrapper, self::$block_actions['block-objects'])) == $wrapper_being_mirrored['id'] ) {

						if ( !isset($blocks[$wrapper['layout']]) )
							$blocks[$wrapper['layout']] = array();

						$blocks[$wrapper['layout']][] = $block_id_on_layout_of_mirrored_wrapper;

				}

			}

		}

		return $blocks;

	}


	public static function run_block_init_actions() {

		foreach ( self::$block_actions['init'] as $layout_id => $blocks ) {

			foreach ( $blocks as $block_id ) {

				$block_options = padma_get($block_id, self::$block_actions['block-objects']);

				/* Use legacy ID if present */
				$block_id            = PadmaBlocksData::get_legacy_id($block_options);
				$block_options['id'] = $block_id;

				if ( $block_options && is_callable(array($block_options['class'], 'init_action')) )
					call_user_func(array($block_options['class'], 'init_action'), $block_id, $block_options);

			}

		}

	}


	public static function run_block_enqueue_actions() {

		//Do not run these if it's the admin page or the visual editor is open
		if ( is_admin() || PadmaRoute::is_visual_editor() )
			return false;

		$layout_id = PadmaLayout::get_current_in_use();

		$enqueue_action_blocks = padma_get($layout_id, self::$block_actions['enqueue'], array());

		if ( !isset($enqueue_action_blocks) || empty($enqueue_action_blocks) )
			return;

		foreach ( $enqueue_action_blocks as $block_id ) {

			$block_options = padma_get($block_id, self::$block_actions['block-objects']);
			$original_block = null;

			if ( !$block_options )
				continue;

			/* If the block is mirrored, then use that ID instead */
				if ( $possible_mirror_id = PadmaBlocksData::is_block_mirrored($block_options) ) {

					$original_block = $block_options;

					$block_id = $possible_mirror_id;
					$block_options = padma_get($block_id, self::$block_actions['block-objects']);

				}

			/* Use legacy ID if present */
			$block_id    = PadmaBlocksData::get_legacy_id( $block_options );
			$block_options['id'] = $block_id;

			if ( is_callable(array(padma_get('class', $block_options), 'enqueue_action')) )
				call_user_func(array(padma_get('class', $block_options), 'enqueue_action'), $block_id, $block_options, $original_block);

		}

	}


	/* Functions used for Dynamic JS and Dynamic CSS */
		/* Get blocks for the specific layout as well as the blocks in mirrored wrappers */
		public static function get_blocks_for_dynamic_asset($css_or_js, $layout_id) {

			return padma_get($layout_id, self::$block_actions['dynamic-' . $css_or_js], array());

		}


		/* Used to loop through block IDs that need JS or CSS to be outputted and does mirror checks */
		public static function get_dynamic_asset_data($blocks, $css_or_js) {

			if ( empty($blocks) )
				return;

			$data = '';

			/* Loop through the blocks */
			foreach ( $blocks as $block_id ) {

				$block_options = padma_get($block_id, self::$block_actions['block-objects']);
				$original_block = null;

				if ( !$block_options )
					continue;

				/* If the block is mirrored, then use that ID instead */
					if ( $possible_mirror_id = PadmaBlocksData::is_block_mirrored($block_options) ) {

						$original_block = $block_options;

						/* Use legacy ID if present */
						$original_block['id'] = PadmaBlocksData::get_legacy_id( $original_block );

						$block_id = $possible_mirror_id;
						$block_options = padma_get($block_id, self::$block_actions['block-objects']);

					}

				/* Use legacy ID if present */
					$block_id = PadmaBlocksData::get_legacy_id( $block_options );
					$block_options['id'] = $block_id;

				/* Output the CSS or JS */
					switch ( $css_or_js ) {

						case 'js':
							if ( is_callable(array(padma_get('class', $block_options), 'dynamic_js')) )
								$data .= call_user_func(array(padma_get('class', $block_options), 'dynamic_js'), $block_id, $block_options, $original_block);
							elseif ( is_callable(array(padma_get('class', $block_options), 'js_content')) )
								$data .= call_user_func(array(padma_get('class', $block_options), 'js_content'), $block_id, $block_options, $original_block);
						break;

						case 'css':
							if ( is_callable(array(padma_get('class', $block_options), 'dynamic_css')) )
								$data .= call_user_func(array(padma_get('class', $block_options), 'dynamic_css'), $block_id, $block_options, $original_block);
						break;

					}

			}

			return $data;

		}


	/* Dynamic JS */
		public static function output_block_dynamic_js($layout_id = false) {

			$layout_id = !$layout_id ? padma_get('layout-in-use') : $layout_id;
			$blocks = self::get_blocks_for_dynamic_asset('js', $layout_id);

			if ( empty($blocks) )
				return;

			return self::get_dynamic_asset_data($blocks, 'js');

		}

		public static function enqueue_block_dynamic_js_file() {

			//Do not run these if it's the admin page or the visual editor is open
			if ( is_admin() || PadmaRoute::is_visual_editor() )
				return false;

			$current_layout_in_use 	= PadmaLayout::get_current_in_use();
			$script_name 			= str_replace(PadmaLayout::$sep, '-', 'block-dynamic-js-layout-' . PadmaLayout::get_current_in_use());			
			$block_actions 			= self::get_blocks_for_dynamic_asset('js', $current_layout_in_use);

			if ( empty($block_actions) )
				return;

			PadmaCompiler::register_file(array(
				'name' => $script_name,
				'format' => 'js',
				'fragments' => array(
					array('PadmaBlocks', 'output_block_dynamic_js')
				),
				'enqueue' => false
			));

			if ( strlen((string)self::output_block_dynamic_js($current_layout_in_use)) > 0 )
				wp_enqueue_script($script_name, PadmaCompiler::get_url($script_name), array('jquery'));

		}
	/* End Dynamic JS */


	/* Dynamic CSS */
		public static function output_block_dynamic_css($layout_id = false) {

			$layout_id = !$layout_id ? padma_get('layout-in-use') : $layout_id;
			$blocks = self::get_blocks_for_dynamic_asset('css', $layout_id);

			if ( empty($blocks) )
				return;

			return self::get_dynamic_asset_data($blocks, 'css');

		}
	/* End Dynamic CSS */


	public static function register_block_element_instances() {

		if ( !($blocks = PadmaBlocksData::get_all_blocks()) )
			return false;

		foreach ( $blocks as $block ) {

			/* Do not register instance for block that's in a mirrored wrapper */
			if ( PadmaWrappersData::is_wrapper_mirrored(PadmaWrappersData::get_wrapper(padma_get('wrapper_id', $block))) )
				continue;

			/* Do not register instance for mirrored block */
			if ( $block_mirror = PadmaBlocksData::is_block_mirrored($block) )
				continue;

			$default_name = self::block_type_nice($block['type']) . ' (Unnamed)';
			$name = padma_get('alias', $block['settings']) ? padma_get('alias', $block['settings']) : $default_name;

			$block_id_for_selector = PadmaBlocksData::get_legacy_id( $block );

			PadmaElementAPI::register_element_instance(array(
				'group' => 'blocks',
				'element' => 'block-' . $block['type'],
				'id' => $block['type'] . '-block-' . $block['id'],
				'name' => $name,
				'selector' => '#block-' . $block_id_for_selector,
				'layout' => $block['layout']
			));

				/* Register sub elements */
				foreach ( PadmaElementAPI::get_block_elements($block['type']) as $block_element_sub_element ) {

					/* Make sure that the element supports instances */
					if ( !padma_get('supports-instances', $block_element_sub_element) )
						continue;

					/* Register instance */
						$instance_selector = str_replace('.block-type-' . $block['type'], '#block-' . $block_id_for_selector, $block_element_sub_element['selector']);

						PadmaElementAPI::register_element_instance(array(
							'group' => 'blocks',
							'element' => $block_element_sub_element['id'],
							'id' => $block_element_sub_element['id'] . '-block-' . $block['id'],
							'name' => $name . ' &ndash; ' . $block_element_sub_element['name'],
							'selector' => $instance_selector,
							'layout' => $block['layout']
						));

						/* Register instance states as instances */
							if ( !empty($block_element_sub_element['states']) && is_array($block_element_sub_element['states']) ) {

								foreach ( $block_element_sub_element['states'] as $instance_state_id => $instance_state_info ) {

									PadmaElementAPI::register_element_instance(array(
										'group' => 'blocks',
										'element' => $block_element_sub_element['id'],
										'id' => $block_element_sub_element['id'] . '-block-' . $block['id'] . '-state-' . $instance_state_id,
										'name' => $name . ' - ' . $block_element_sub_element['name'] . ' (State: ' . $instance_state_info['name'] . ')',
										'selector' => str_replace('.block-type-' . $block['type'], '#block-' . $block_id_for_selector, $instance_state_info['selector']),
										'layout' => $block['layout'],
										'state-of' => $block_element_sub_element['id'] . '-block-' . $block['id'],
										'state-name' => $instance_state_info['name']
									));

								}

							}

				} /* /foreach */

		}

	}


	public static function display_block($block, $where = null) {

		//We'll allow this function to take either an integer argument to look up the block or to use the existing
		if ( !is_array($block) )
			$block = PadmaBlocksData::get_block($block);

		//Check that the block exists
		if ( !is_array($block) || !$block )
			return false;

		$block_types = PadmaBlocks::get_block_types();

		//Set the original block for future use
		$original_block = $block;
		$original_block_id = $block['id'];

		//Set the block style to null so we don't get an ugly notice down the road if it's not used.
		$block_style_attr = null;

		//Check if the block type exists		
		if ( !$block_type_settings = padma_get($block['type'], $block_types, array()) ) {

			$block['requested-type'] 	= $block['type'];
			$block['type'] 				= 'unknown';

		}

		//Get the custom CSS classes and change commas to spaces and remove double spaces and remove HTML
		$custom_css_classes = str_replace('  ', ' ', str_replace(',', ' ', esc_attr(strip_tags(padma_get('css-classes', $block['settings'], '')))));


		$block_classes = array_unique(array_filter(explode(' ', $custom_css_classes)));

		$block_classes[] = 'block';
		$block_classes[] = 'block-type-' . $block['type'];

		$block_classes[] = ( padma_get('fixed-height', $block_type_settings, false) !== true ) ? 'block-fluid-height' : 'block-fixed-height';

		//If the block is being displayed in the Grid, then we need to make it work with absolute positioning.
		if ( $where == 'grid' ) {

			$block_classes[] = 'grid-width-' . $original_block['dimensions']['width'];
			$block_classes[] = 'grid-left-' . $original_block['position']['left'];

			$block_style_attr = ' style="height: ' . $original_block['dimensions']['height'] . 'px; top: ' . $original_block['position']['top'] . 'px;"';

		}

		//If the responsive grid is active, then add the responsive block hiding classes
		if ( PadmaResponsiveGrid::is_enabled() ) {

			$responsive_block_hiding = padma_get('responsive-block-hiding', $block['settings'], array());

			if ( is_array($responsive_block_hiding) && count($responsive_block_hiding) > 0 ) {

				foreach ( $responsive_block_hiding as $device )
					$block_classes[] = 'responsive-block-hiding-device-' . $device;

			}

		}

		//If it's a mirrored block, change $block to the mirrored block
		if ( $mirrored_block = PadmaBlocksData::get_block_mirror($block) ) {

			$block = $mirrored_block;
			$block['original'] = $original_block;

			//Add Classes for the mirroring
			$block_classes[] = 'block-mirrored';

			if ( $where != 'grid' ) {

				$block_classes[] = 'block-mirroring-' . $mirrored_block['id'];
				$block_classes[] = 'block-original-' . PadmaBlocksData::get_legacy_id( $block['original'] );

			}

		}

		//Fetch the HTML tag for the block
		$block_tag = ( $html_tag = padma_get('html-tag', $block_type_settings) ) ? $html_tag : 'div';

		//The ID attribute for the block.  This will change if mirrored.
		$block_id_for_id_attr = $where != 'grid' ? PadmaBlocksData::get_legacy_id( $block ) : $block['id'];

		//Original block ID to be used in the Visual Editor
		if ( PadmaRoute::is_visual_editor_iframe() ) {

			if( isset($mirrored_block['id']) && !empty($mirrored_block['id']) ){
				$mirrored_block_id = $mirrored_block['id'];
			}else{
				$mirrored_block_id = '';
			}

			if( isset($mirrored_block['layout']) && !empty($mirrored_block['layout']) ){
				$mirrored_block_layout = $mirrored_block['layout'];
			}else{
				$mirrored_block_layout = '';
			}



			$block_data_attrs = implode(' ', array(
				'data-id="' . str_replace('block-', '', $original_block_id) . '"',
				'data-type="' . padma_get('type', $block) . '"',
				'data-block-mirror="' . $mirrored_block_id . '"',
				'data-block-mirror-layout-name="' . $mirrored_block_layout . '"',
				'data-grid-left="' . $original_block['position']['left'] . '"',
				'data-grid-top="' . $original_block['position']['top'] . '"',
				'data-width="' . $original_block['dimensions']['width'] . '"',
				'data-height="' . $original_block['dimensions']['height'] . '"',
				'data-alias="' . esc_attr(stripslashes(padma_get('alias', padma_get('settings', $block, array())))) . '"',
				'data-custom-classes="' . trim($custom_css_classes) . '"',
				'data-inline-editable="' . padma_get('inline-editable', $block_type_settings) . '"'
			));

		} else {

			$block_data_attrs = implode( ' ', array(
				' data-alias="' . esc_attr(padma_get( 'alias', padma_get( 'settings', $block, array() ) )) . '"'
			) );

		}

		//The grid will display blocks entirely differently and not use hooks.
		if ( $where != 'grid' ) {

			/* Use legacy ID if present */
			$block['id'] = PadmaBlocksData::get_legacy_id( $block );


			do_action('padma_before_block', $block);
			do_action('padma_before_block_' . $block['id'], $block);

			echo '<' . $block_tag . ' id="block-' . $block_id_for_id_attr . '" class="' . implode(' ', array_filter(apply_filters('padma_block_class', $block_classes, $block))) . '"' . $block_style_attr . $block_data_attrs . self::block_attr( $block_type_settings ) . '>';

				do_action('padma_block_open', $block);
				do_action('padma_block_open_' . $block['id'], $block);

				echo '<div class="block-content">';

					do_action('padma_block_content_open', $block);
					do_action('padma_block_content_open_' . $block['id'], $block);

					do_action('padma_block_content_' . $block['type'], $block);

					do_action('padma_block_content_close', $block);
					do_action('padma_block_content_close_' . $block['id'], $block);

				echo '</div>';

				do_action('padma_block_close', $block);
				do_action('padma_block_close_' . $block['id'], $block);

			echo '</' . $block_tag . '>';

			do_action('padma_after_block', $block);
			do_action('padma_after_block_' . $block['id'], $block);

		//Show the block in the grid
		} else {

			$show_content_in_grid = self::block_type_exists($block['type']) ? padma_get('show-content-in-grid', $block_type_settings, false) : false;

			if ( !$show_content_in_grid )
				$block_classes[] = 'hide-content-in-grid';

			if ( !self::block_type_exists($block['type']) )
				$block_classes[] = 'block-error';

			echo '<' . $block_tag . ' id="block-' . $block_id_for_id_attr . '" class="' . implode(' ', array_filter($block_classes)) . '"' . $block_style_attr . $block_data_attrs . '>';

				echo '<div class="block-content-fade block-content">';

					if ( !self::block_type_exists($block['type']) ) {

						self::unknown_block_content($block);

					}

				echo '</div>';

			echo '</' . $block_tag . '>';

		}

		//Spit the ID back out
		return $block['id'];

	}

	public static function block_attr( $block_type_settings ) {

		if ( !$attributes = padma_get('attributes', $block_type_settings) )
			return;

		$output = '';

	    foreach ( $attributes as $key => $value ) {

	        if ( is_numeric($key) )
	            $output .= esc_html($value);
	        else   
	        	$output .= sprintf( ' %s="%s"', esc_html($key), esc_attr($value) );

	    }

	    return $output;

	}


	public static function get_block_types() {

		global $padma_block_types;		

		if ( !isset($padma_block_types) || empty($padma_block_types) )
			return null;

		return $padma_block_types;

	}

	public static function get_registered_blocks_categories(){

		global $padma_block_types;

		if ( !isset($padma_block_types) || empty($padma_block_types) )
			return null;

		$categories = array();
		foreach ($padma_block_types as $block_type => $parameters) {
			foreach ($parameters['categories'] as $param => $value) {
				$categories[$value][] = $block_type;
			}
		}

		ksort($categories);

		return $categories;

	}


	public static function block_type_nice($type) {

		$block_types = self::get_block_types();

		return padma_get('name', padma_get($type, $block_types));

	}


	public static function block_type_exists($type) {

		$block_types = self::get_block_types();

		//If, for some reason, the blocks array isn't set, just return false.
		if ( !is_array($block_types) )
			return new WP_Error('blocks_array_does_not_exist', __('The Padma blocks array does not exist.', 'padma'));

		//Check for the actual block type
		if ( isset($block_types[$type]) )
			return true;

		//Return false if everything else fails
		return false;

	}


	public static function unknown_block_content($block = null) {

		echo '<div class="alert alert-red block-type-unknown-notice"><p>' . sprintf( __('The requested block type of \'%s\' does not exist. Please re-activate the block plugin or child theme if you wish to use this block again.','padma'), $block['requested-type']) . '</p></div>';

	}


}