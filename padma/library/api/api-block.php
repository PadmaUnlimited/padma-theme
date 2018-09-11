<?php
/**
 * Padma blocks API.
 *
 * @package Padma
 * @subpackage API
 **/
function padma_register_block($class, $block_type_url = false) {	
	
	global $padma_unregistered_block_types;
	
	if ( !is_array($padma_unregistered_block_types) )
		$padma_unregistered_block_types = array();
	
	$padma_unregistered_block_types[$class] = $block_type_url;
	
	return true;

}


abstract class PadmaBlockAPI {
			
			
	public $id;	
	public $name;	
	public $block_type_url;	
	public $options_class 	= 'PadmaBlockOptionsAPI';		
	public $fixed_height 	= false;	
	public $html_tag 		= 'div';	
	public $attributes 		= array();
	public $description 	= false;
	public $allow_titles 	= true;
	

	/* System Properties (DO NOT USE OR TOUCH) */	
	protected $options;
	
	protected $show_content_in_grid = false;

	
	/* System Methods (DO NOT EXTEND OR MODIFY) */
	public function register() {
				
		global $padma_block_types;
		
		//If the Padma blocks array doesn't exist, create it.
		if ( !is_array($padma_block_types) )
			$padma_block_types = array();
				
		//Add block to array.  This array will be used for checking if certain blocks exist, the block type selector and so on.
		$padma_block_types[$this->id] = array(
			'name' => $this->name,
			'url' => $this->block_type_url,
			'class' => get_class($this),
			'fixed-height' => $this->fixed_height,
			'html-tag' => $this->html_tag,
			'attributes' => $this->attributes,
			'show-content-in-grid' => $this->show_content_in_grid,
			'allow-titles' => $this->allow_titles,
			'description' => $this->description
		);
		
		//Add the element for the block itself
		add_action('padma_register_elements', array($this, 'setup_main_block_element'));
				
		//Run init method if it exists
		if ( method_exists($this, 'init') )
			$this->init();
		
		//Run setup_elements if it exists
		if ( method_exists($this, 'setup_elements') )
			add_action('padma_register_elements', array($this, 'setup_elements'));

		//Setup hooks
		if ( $this->allow_titles )
			add_action('padma_block_content_' . $this->id, array($this, 'title_and_subtitle'));	

		add_action('padma_block_content_' . $this->id, array($this, 'content'));

		add_action('padma_block_options_' . $this->id, array($this, 'options_panel'), 10, 2);
		
	}
	
	
	public function setup_main_block_element() {
		
		PadmaElementAPI::register_element(array(
			'group' 		=> 'blocks',
			'id' 			=> 'block-' . $this->id,
			'name' 			=> $this->name,
			'selector' 		=> '.block-type-' . $this->id,
			'properties' 	=> array(
									'background',
									'borders',
									'fonts',
									'padding',
									'corners',
									'box-shadow',
									'overflow',
									'sizes',
									'animation',
									'transform',
									'effects',
								),
			));

		if ( $this->allow_titles ) {

			$this->register_block_element(array(
				'id' => 'block-title',
				'name' => 'Block Title',
				'selector' => '.block-title'
			));

			$this->register_block_element(array(
				'id' => 'block-title-span',
				'name' => 'Block Title Inner',
				'parent' => 'block-title',
				'selector' => '.block-title span',
				'inspectable' => false
			));


			$this->register_block_element(array(
				'id' => 'block-title-link',
				'name' => 'Block Title Link',
				'parent' => 'block-title',
				'selector' => '.block-title a',
				'inspectable' => false
			));

			$this->register_block_element(array(
				'id' => 'block-subtitle',
				'name' => 'Block Subtitle',
				'selector' => '.block-subtitle'
			));

		}
		
	}
	
	
	public function options_panel($block, $layout) {
							
		if ( !class_exists($this->options_class) )
			return new WP_Error('block_options_class_does_not_exist', __('Error: The block options class being registered does not exist.', 'padma'), $this->options_class);
			
		//Initiate options class
		$options = new $this->options_class($this);
		$options->display($block, $layout);
				
	}
	
	
	/* Methods to extend (you can modify these!) */
	public function content($block) {
		
	}


	public function title_and_subtitle($block) {

		if ( !$this->allow_titles )
			return;

		/* Output Block Titles */
		if ( padma_get('original', $block) )
			$block = padma_get('original', $block);

		$block_settings          = padma_get( 'settings', $block );
		$block_title             = padma_get( 'block-title', padma_get( 'settings', $block, array() ) );
		$block_title_tag         = padma_get( 'block-title-tag', padma_get( 'settings', $block, array() ) );
		$block_subtitle          = padma_get( 'block-subtitle', padma_get( 'settings', $block, array() ) );
		$block_subtitle_tag      = padma_get( 'block-subtitle-tag', padma_get( 'settings', $block, array() ) );
		$block_title_link_url    = padma_get( 'block-title-link-url', padma_get( 'settings', $block, array() ) );
		$block_title_link_target = padma_get( 'block-title-link-target', $block_settings, false, true ) ? $target = ' target="_blank"' : '';


		if ( $block_title || $block_subtitle ) {

			/* Open hgroup if necessary */
			if ( $block_title && $block_subtitle ) {
				echo '<hgroup>';
			}

			/* Title */
			if ( $block_title ) {
				if ( padma_get( 'block-title-link-check', $block_settings, false ) ) {
					if($block_title_tag == ''){
						echo '<h1 class="block-title"><a href="' . padma_fix_data_type( $block_title_link_url ) . '"' . $block_title_link_target . '><span>' . padma_fix_data_type( $block_title ) . '</span></a></h1>';
					}else{
						echo '<'.$block_title_tag.' class="block-title"><a href="' . padma_fix_data_type( $block_title_link_url ) . '"' . $block_title_link_target . '><span>' . padma_fix_data_type( $block_title ) . '</span></a></'.$block_title_tag.'>';
					}
				} else {
					if($block_title_tag == ''){
						echo '<h1 class="block-title"><span>' . padma_fix_data_type( $block_title ) . '</span></h1>';
					}else{
						echo '<'.$block_title_tag.' class="block-title"><span>' . padma_fix_data_type( $block_title ) . '</span></'.$block_title_tag.'>';
					}
				}
			}

			/* Subtitle */
			if ( $block_subtitle ) {
				if($block_subtitle_tag == ''){
					echo '<h2 class="block-subtitle">' . padma_fix_data_type( $block_subtitle ) . '</h2>';
				}else{
					echo '<'.$block_subtitle_tag.' class="block-subtitle">' . padma_fix_data_type( $block_subtitle ) . '</'.$block_subtitle_tag.'>';				
				}
			}

			/* Close hgroup */
			if ( $block_title && $block_subtitle ) {
				echo '</hgroup>';
			}

		}

	}
	
	
	/**
	 * The following are commented out so they are not detected 
	 * 
	 *  public static function init_action($block_id, $block) {
	 *  
	 *  }
	 * 
	 * 
	 *  public static function enqueue_action($block_id, $block, $original_block = null) {
	 *  
	 *  }
	 *
	 *
	 *  public static function dynamic_css($block_id, $block, $original_block = null) {
	 *  
	 *  }
 	 *
     *
	 *  public static function dynamic_js($block_id, $block, $original_block = null) {
	 *  
	 *  }
	 * 
	 **/
	
	
	/* Methods to use, but not modify! */
	public function register_block_element($args) {
				
		/* Add the selector prefix to the selector and even handle commas */
		$selector_prefix = '.block-type-' . $this->id . ' ';
		
		$selector_array = explode(',', $args['selector']);
		
		foreach ( $selector_array as $selector_index => $selector ) {
						
			if ( strpos(trim($selector_array[$selector_index]), '.block-type-') === 0 )
				continue;
			
			$selector_array[$selector_index] = $selector_prefix . trim($selector);
			
		}
		
		$modified_selector = implode(',', $selector_array);	
		/* End Selector Modification */
		
		$defaults = array(
			'group' 	=> 'blocks',
			'parent' 	=> 'block-' . $this->id,
			'id' 		=> 'block-' . $this->id . '-' . $args['id'],
			'name' 		=> $args['name'],
			'selector'	=> $modified_selector
		);
		
		//Unset the following so they don't overwrite the defaults
		unset($args['id']);
		unset($args['name']);
		unset($args['selector']);

		//If the parent isn't the default then put on block type prefix
		if ( !empty($args['parent']) && $args['parent'] != 'block-' . $this->id )
			$args['parent'] = 'block-' . $this->id . '-' . $args['parent'];
		
		$element = array_merge($defaults, $args);
		
		//Go through states and add the selector prefix to each state
		if ( isset($element['states']) && is_array($element['states']) ) {
			
			foreach ( $element['states'] as $state_name => $state_selector ) {
				
				$state_selector_array = explode(',', $state_selector);

				foreach ( $state_selector_array as $selector_index => $selector ) {

					if ( strpos(trim($state_selector_array[$selector_index]), '.block-type-') === 0 )
						continue;

					$state_selector_array[$selector_index] = $selector_prefix . trim($selector);

				}
				
				$element['states'][$state_name] = trim(implode(',', $state_selector_array));
				
			}
				
		}
		
		return PadmaElementAPI::register_element($element);
		
	}
	

	public static function get_setting($block, $setting, $default = null) {
				
		return PadmaBlocksData::get_block_setting($block, $setting, $default);
		
	}
	
	
}


class PadmaBlockOptionsAPI extends PadmaVisualEditorPanelAPI {
	
	
	public $block_type_object;
	public $block 		= false;
	public $block_id 	= false;


	public function __construct($block_type_object) {

		/* Accept the block type as an argument that way its properties are available for use in this class */
		$this->block_type_object = $block_type_object;

	}
	
	
	public function register() {
		
		return true;
		
	}
		
	
	public function display($block, $layout) {
		
		//Set block properties
		$this->block = $block;

		//Args for modify_arguments and block_content
		$args = array(
			'block' => $this->block,
			'blockID' => $this->block['id'],
			'layoutID' => $this->block['layout'],

			/* Backwards Compatibility */
			'block_id' => $this->block['id']
		);

		//Allow developers to modify the properties of the class and use functions since doing a property 
		//outside of a function will not allow you to.
		$this->modify_arguments($args);
		
		//Add the standard block tabs
		$this->add_standard_block_config();
		$this->add_standard_block_import_export();

		if ( PadmaResponsiveGrid::is_enabled() )
			$this->add_standard_block_responsive();
		
		//Display it
		$this->panel_content($args);
		
	}


	public function add_standard_block_config() {
		
		if ( !isset($this->tabs) )
			$this->tabs = array();
			
		if ( !isset($this->inputs) )
			$this->inputs = array();
		
		//Add the tab
		$this->tabs['config'] = 'Config';
		
		/* Add the inputs */

		$this->inputs['config']['mirror-block'] = array(
			'type' => 'select',
			'name' => 'mirror-block',
			'label' => 'Mirror Block',
			'chosen' => true,
			'default' => '',
			'tooltip' => 'By using this option, you can tell a block to "mirror" another block and its content.  This option is useful if you are wanting to share a block&mdash;such as a header&mdash;across layouts on your site.  Select the block you wish to mirror the content from in the select box to the right.',
			'options' => 'get_blocks_select_options_for_mirroring()',
			'callback' => 'updateBlockMirrorStatus(input, block.id, value);',
			'value' => PadmaBlocksData::is_block_mirrored($this->block)
		);
		
		$this->inputs['config']['alias'] = array(
			'type' => 'text',
			'name' => 'alias',
			'label' => 'Block Alias',
			'default' => '',
			'callback' => 'var $block = $i("#block-" + block.id); $block.data("alias", value); updateBlockContentCover($block);',
			'tooltip' => 'Enter an easily recognizable name for the block alias and it will be used throughout your site admin.  For instance, if you add an alias to a widget area block, that alias will be used in the Widgets panel.',
		);

		$this->inputs['config']['css-classes'] = array(
			'type' => 'text',
			'name' => 'css-classes',
			'callback' => 'updateBlockCustomClasses(input, block.id, value);',
			'label' => 'Custom CSS Class(es)',
			'default' => '',
			'tooltip' => 'Need more finite control?  Enter the custom CSS class selectors here and they will be added to the block\'s class attribute. <strong>DO NOT</strong> put regular CSS in here.  Use the Live CSS editor for that.',
		);

		$this->inputs['config']['css-classes-bubble'] = array(
			'type' => 'checkbox',
			'name' => 'css-classes-bubble',
			'label' => '<em style="color: #666; font-style: italic;">Advanced:</em> Add Custom CSS Class(es) to Row/Column',
			'default' => '',
			'tooltip' => 'Copy any custom CSS classes added to this block and add them to the parent row and column &lt;section&gt;\'s',
		);

		/* Titles */
			if ( isset($this->block_type_object->allow_titles) && $this->block_type_object->allow_titles ) {

				$this->inputs['config']['titles-heading'] = array(
					'name' => 'titles-heading',
					'type' => 'heading',
					'label' => 'Block Title'
				);

					$this->inputs['config']['block-title'] = array(
						'name' => 'block-title',
						'type' => 'text',
						'label' => 'Block Title',
						'tooltip' => 'Add a custom title above the block content.'
					);

					$this->inputs['config']['block-title-tag'] = array(
						'name' => 'block-title-tag',
						'type' => 'select',
						'options' => array(
							'h1' => 'H1',
							'h2' => 'H2',
							'h3' => 'H3',
							'h4' => 'H4',
							'h5' => 'H5',
							//'h6' => 'H6',
						),
						'label' => 'Block Title Tag',
						'tooltip' => 'Custom title tag.'
					);


					$this->inputs['config']['block-subtitle'] = array(
						'name' => 'block-subtitle',
						'type' => 'text',
						'label' => 'Block Subtitle',
						'tooltip' => 'Add a custom sub title above the block content and below the block title.'
					);


					$this->inputs['config']['block-subtitle-tag'] = array(
						'name' => 'block-subtitle-tag',
						'type' => 'select',
						'options' => array(
							//'h1' => 'H1',
							'h2' => 'H2',
							'h3' => 'H3',
							'h4' => 'H4',
							'h5' => 'H5',
							'h6' => 'H6',
						),
						'label' => 'Block Subtitle Tag',
						'tooltip' => 'Custom subtitle tag.'
					);

					$this->inputs['config']['block-title-link-check'] = array(
						'name' => 'block-title-link-check',
						'type' => 'checkbox',
						'label' => 'Link Block Title?',
						'tooltip' => 'Choose whether the block title should be a link or not',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-block-title-link-url',
									'#input-block-title-link-target'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-block-title-link-url',
									'#input-block-title-link-target'
								)
							)
						)
					);

					$this->inputs['config']['block-title-link-url'] = array(
						'name' => 'block-title-link-url',
						'type' => 'text',
						'label' => 'Block Title Link URL',
						'tooltip' => 'Add a url for the block title'
					);

					$this->inputs['config']['block-title-link-target'] = array(
						'name' => 'block-title-link-target',
						'type' => 'checkbox',
						'label' => 'Open in a new window?',
						'tooltip' => 'If you would like to open the link in a new window check this option',
						'default' => false
					);

			}
		/* End Titles */
		
	}

	public function add_standard_block_responsive() {
		
		if ( !isset($this->tabs) )
			$this->tabs = array();
			
		if ( !isset($this->inputs) )
			$this->inputs = array();
		
		//Add the tab
		$this->tabs['responsive'] = 'Responsive Control';

		/* Add the inputs */
		$this->inputs['responsive']['responsive-options'] = array(
			'type' => 'repeater',
			'name' => 'responsive-options',
			'label' => 'Configure Breakpoints.',
			'inputs' => array(

				array(
					'type' => 'select',
					'name' => 'blocks-breakpoint',
					'label' => 'Set Breakpoint',
					'options' => array(
						'off' => 'Off - No Breakpoint',
						'custom' => 'Custom Width',						
						'1920px' 	=> '1920px - Very Large Screens',
						'1824px' 	=> '1824px - Large Screens',
						'1224px' 	=> '1224px - Desktop and Laptop',
						'1024px' 	=> '1024px - Popular Tablet Landscape',
						'812px' 	=> '812px - iPhone X Landscape',
						'768px' 	=> '768px - Popular Tablet Portrait',
						'736px' 	=> '736px - iPhone 6+ & 7+ & 8+ Landscape',
						'667px' 	=> '667px - iPhone 6 & 7 & 8 & Android Landscape',
						'600px' 	=> '600px - Popular Breakpoint in Padma',
						'568px' 	=> '568px - iPhone 5 Landscape',
						'480px' 	=> '480px - iPhone 3 & 4 Landscape',
						'414px' 	=> '414px - iPhone 6+ & 7+ & 8+ Landscape',
						'375px' 	=> '375px - iPhone 6 & 7 & 8 & X & Android Portrait',
						'320px' 	=> '320px - iPhone 3 & 4 & 5 & Android Portrait',
					),
					'toggle' => array(
						'' => array(
							'hide' => array(
								'.input:not(#input-blocks-breakpoint)'
							)
						),
						'off' => array(
							'hide' => array(
								'.input:not(#input-blocks-breakpoint)'
							)
						),
						'custom' => array(
							'show' => array(
								'.input'
							)
						),
						'1824px' => array(
							'show' => array(
								'.input:not(#input-max-width)'
							),
							'hide' => array(
								'#input-max-width'
							),
						),
						'1224px' => array(
							'show' => array(
								'.input:not(#input-max-width)'
							),
							'hide' => array(
								'#input-max-width'
							),
						),
						'1024px' => array(
							'show' => array(
								'.input:not(#input-max-width)'
							),
							'hide' => array(
								'#input-max-width'
							),
						),
						'768px' => array(
							'show' => array(
								'.input:not(#input-max-width)'
							),
							'hide' => array(
								'#input-max-width'
							),
						),
						'600px' => array(
							'show' => array(
								'.input:not(#input-max-width)'
							),
							'hide' => array(
								'#input-max-width'
							),
						),
						'568px' => array(
							'show' => array(
								'.input:not(#input-max-width)'
							),
							'hide' => array(
								'#input-max-width'
							),
						),
						'480px' => array(
							'show' => array(
								'.input:not(#input-max-width)'
							),
							'hide' => array(
								'#input-max-width'
							),
						),
						'320px' => array(
							'show' => array(
								'.input:not(#input-max-width)'
							),
							'hide' => array(
								'#input-max-width'
							),
						)
					),
					'tooltip' => 'Select a screen width for these change to take effect.',
					'default' => ''
				),

				array(
					'type' => 'text',
					'name' => 'max-width',
					'label' => 'Custom Width',
					'default' => ''
				),

				array(
					'type' => 'select',
					'name' => 'breakpoint-min-or-max',
					'label' => 'Min or Max width',
					'options' => array(
						'min' => 'Min Width (applies to screens that are wider than breakpoint)',
						'max' => 'Max Width (applies to screens that are narrower than breakpoint)'
					),
					'default' => 'max'
				),

				array(
					'name' => 'adaptive-heading',
					'type' => 'heading',
					'label' => 'Adaptive Options'
				),

				array(
					'type' => 'checkbox',
					'name' => 'disable-block-height',
					'label' => 'Disable blocks height',
					'tooltip'=> 'Disable the height for smaller screens if the block displays too high for smaller screens',
					'default' => false
				),

				array(
					'type' => 'checkbox',
					'name' => 'mobile-center-elements',
					'label' => 'Attempt to center block elements',
					'default' => false
				),

				array(
					'type' => 'checkbox',
					'name' => 'griddify-lists',
					'label' => 'Griddify Lists',
					'default' => false,
					'tooltip' => 'Any kind of list, such as categories, latest posts, even menus etc work fine on large screens in the sidebar. But on smaller screens where the sidebar drops below the content. The lists can look empty due to mass of whitespace. This will put the list items into 2 columns side by side.'
				),

				array(
					'type' => 'checkbox',
					'name' => 'hide-block',
					'label' => 'Hide this block',
					'default' => false,
					'tooltip' => 'This will hide this block for the set breakpoint.'
				)

			),
			'sortable' => true,
			'limit' => false,
			'callback' => ''
		);


		if ( PadmaBlocksData::get_block_setting($this->block, 'responsive-block-hiding') ) {

			$this->inputs['responsive']['responsive-block-hiding'] = array(
				'type' => 'multi-select',
				'name' => 'responsive-block-hiding',
				'label' => 'Legacy Responsive Grid Block Hiding',
				'default' => '',
				'tooltip' => 'If you have the responsive grid enabled and the user views your website on an iPhone (or equivalent device), the grid may be cluttered do to so many blocks being in a small area.  If you wish to limit the blocks that are shown on mobile devices, you can use this setting to hide certain blocks for the devices you choose.  <strong>If no options are selected, then responsive block hiding will not be active for this block.</strong>',
				'options' => array(
					'smartphones' => 'iPhone/Smartphones',
					'tablets-landscape' => 'iPad/Tablets (Landscape)',
					'tablets-portrait' => 'iPad/Tablets (Portrait)',
					'computers' => 'Laptops & Desktops (Not Recommended)'
				)
			);

		}
		
	}


	public function add_standard_block_import_export() {

		if ( !isset($this->tabs) )
			$this->tabs = array();
			
		if ( !isset($this->inputs) )
			$this->inputs = array();
		
		//Add the tab
		$this->tabs['import-export'] = 'Import/Export';
		
		/* Add the inputs */

		$this->inputs['import-export']['import-heading'] = array(
			'name' => 'import-heading',
			'type' => 'heading',
			'label' => 'Import Block Settings'
		);

			$this->inputs['import-export']['block-import-settings-file'] = array(
				'type' => 'import-file',
				'name' => 'block-import-settings-file',
				'button-label' => 'Select File to Import',
				'no-save' => true
			);

			$this->inputs['import-export']['block-import-include-options'] = array(
				'type' => 'checkbox',
				'name' => 'block-import-settings-include-options',
				'label' => 'Include Block Options',
				'default' => true,
				'no-save' => true
			);

			$this->inputs['import-export']['block-import-include-design'] = array(
				'type' => 'checkbox',
				'name' => 'block-import-settings-include-design',
				'label' => 'Include Block Design',
				'default' => true,
				'no-save' => true
			);

			$this->inputs['import-export']['block-import-settings'] = array(
				'type' => 'button',
				'name' => 'block-import-settings',
				'button-label' => 'Import Block Settings',
				'no-save' => true,
				'callback' => 'initiateBlockSettingsImport(args);'
			);

		$this->inputs['import-export']['export-heading'] = array(
			'name' => 'export-heading',
			'type' => 'heading',
			'label' => 'Export Block Settings'
		);

			$this->inputs['import-export']['block-export-settings'] = array(
				'type' => 'button',
				'name' => 'block-export-settings',
				'button-label' => 'Download Export File',
				'no-save' => true,
				'callback' => 'exportBlockSettingsButtonCallback(args);'
			);
		
	}
	
	
	public function get_blocks_select_options_for_mirroring() {
			
		$block_type = $this->block['type'];	
				
		$blocks = PadmaBlocksData::get_blocks_by_type($block_type);
		
		$options = array('' => '&ndash; Do Not Mirror &ndash;');
		
		//If there are no blocks, then just return the Do Not Mirror option.
		if ( !isset($blocks) || !is_array($blocks) )
			return $options;
		
		foreach ( $blocks as $block_id => $block ) {

			if ( $this->block['id'] == $block_id ) {
				continue;
			}

			//If the block is mirrored, skip it
			if ( PadmaBlocksData::is_block_mirrored( $block ) ) {
				continue;
			}

			/* Do not show block that's in a mirrored wrapper */
			if ( PadmaWrappersData::is_wrapper_mirrored( PadmaWrappersData::get_wrapper( padma_get( 'wrapper_id', $block ) ) ) ) {
				continue;
			}

			//Create the default name by using the block type and ID
			$default_name = PadmaBlocks::block_type_nice( $block['type'] ) . ' Block';

			//If we can't get a name for the layout, then things probably aren't looking good.  Just skip this block.
			if ( ! ( $layout_name = PadmaLayout::get_name( $block['layout'] ) ) ) {
				continue;
			}

			//Make sure the block exists
			if ( ! PadmaBlocksData::block_exists( $block['id'] ) ) {
				continue;
			}

			$layout_name = PadmaLayout::get_layout_parents_names( $block['layout'] ) . $layout_name;

			if ( ! isset( $options[ $layout_name ] ) ) {
				$options[ $layout_name ] = array();
			}

			$options[ $layout_name ][ $block['id'] ] = padma_get( 'alias', $block['settings'], $default_name );
			
		}

		return $options;
		
	}

	
}