<?php

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

		$this->add_anywhere_tab($args);

		//Display it
		$this->panel_content($args);

	}

	public function add_anywhere_tab($args){

		if ( !isset($this->tabs) )
			$this->tabs = array();

		//Add the tab
		$this->tabs['anywhere'] = 'Anywhere';
		$shortcode_txt = "[padma-block id='" . $args['block']['id'] ."']";

		$this->tab_notices['anywhere'] = __('<strong>Use this block anywhere.</strong><p>To insert this block into your post or page use this shortcode:<p>','padma').'<input class="shortcode-anywhere" value="'.$shortcode_txt.'">';

		if(PadmaOption::get('padma-blocks-as-gutenberg-blocks')){
			$this->inputs['anywhere']['show-as-gutenberg-block'] = array(
					'name' => 'show-as-gutenberg-block',
					'type' => 'checkbox',
					'label' => 'Show as Gutenberg Block',
					'default' => false
				);
		}

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
			'tooltip' => __('By using this option, you can tell a block to "mirror" another block and its content.  This option is useful if you are wanting to share a block&mdash;such as a header&mdash;across layouts on your site.  Select the block you wish to mirror the content from in the select box to the right.','padma'),
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
			'tooltip' => __('Enter an easily recognizable name for the block alias and it will be used throughout your site admin.  For instance, if you add an alias to a widget area block, that alias will be used in the Widgets panel.','padma'),
		);

		$this->inputs['config']['css-classes'] = array(
			'type' => 'text',
			'name' => 'css-classes',
			'callback' => 'updateBlockCustomClasses(input, block.id, value);',
			'label' => 'Custom CSS Class(es)',
			'default' => '',
			'tooltip' => __('Need more finite control?  Enter the custom CSS class selectors here and they will be added to the block\'s class attribute. <strong>DO NOT</strong> put regular CSS in here.  Use the Live CSS editor for that.','padma'),
		);

		$this->inputs['config']['css-classes-bubble'] = array(
			'type' => 'checkbox',
			'name' => 'css-classes-bubble',
			'label' => '<em style="color: #666; font-style: italic;">Advanced:</em> Add Custom CSS Class(es) to Row/Column',
			'default' => '',
			'tooltip' => __('Copy any custom CSS classes added to this block and add them to the parent row and column &lt;section&gt;\'s','padma'),
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
						'tooltip' => __('Add a custom title above the block content.','padma')
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
						'tooltip' => __('Custom title tag.','padma')
					);


					$this->inputs['config']['block-subtitle'] = array(
						'name' => 'block-subtitle',
						'type' => 'text',
						'label' => 'Block Subtitle',
						'tooltip' => __('Add a custom sub title above the block content and below the block title.','padma')
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
						'tooltip' => __('Custom subtitle tag.','padma')
					);

					$this->inputs['config']['block-title-link-check'] = array(
						'name' => 'block-title-link-check',
						'type' => 'checkbox',
						'label' => 'Link Block Title?',
						'tooltip' => __('Choose whether the block title should be a link or not','padma'),
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
						'tooltip' => __('Add a url for the block title','padma')
					);

					$this->inputs['config']['block-title-link-target'] = array(
						'name' => 'block-title-link-target',
						'type' => 'checkbox',
						'label' => 'Open in a new window?',
						'tooltip' => __('If you would like to open the link in a new window check this option','padma'),
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
					'tooltip' => __('Select a screen width for these change to take effect.','padma'),
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
						'min' => __('Min Width (applies to screens that are wider than breakpoint)','padma'),
						'max' => __('Max Width (applies to screens that are narrower than breakpoint)','padma')
					),
					'default' => 'max'
				),

				array(
					'name' => 'adaptive-heading',
					'type' => 'heading',
					'label' => __('Adaptive Options','padma')
				),

				array(
					'type' => 'checkbox',
					'name' => 'disable-block-height',
					'label' => 'Disable blocks height',
					'tooltip'=> __('Disable the height for smaller screens if the block displays too high for smaller screens','padma'),
					'default' => false
				),

				array(
					'type' => 'checkbox',
					'name' => 'mobile-center-elements',
					'label' => __('Attempt to center block elements','padma'),
					'default' => false
				),

				array(
					'type' => 'checkbox',
					'name' => 'griddify-lists',
					'label' => __('Griddify Lists','padma'),
					'default' => false,
					'tooltip' => __('Any kind of list, such as categories, latest posts, even menus etc work fine on large screens in the sidebar. But on smaller screens where the sidebar drops below the content. The lists can look empty due to mass of whitespace. This will put the list items into 2 columns side by side.','padma')
				),

				array(
					'type' => 'checkbox',
					'name' => 'hide-block',
					'label' => __('Hide this block','padma'),
					'default' => false,
					'tooltip' => __('This will hide this block for the set breakpoint.','padma')
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
				'label' => __('Legacy Responsive Grid Block Hiding','padma'),
				'default' => '',
				'tooltip' => __('If you have the responsive grid enabled and the user views your website on an iPhone (or equivalent device), the grid may be cluttered do to so many blocks being in a small area.  If you wish to limit the blocks that are shown on mobile devices, you can use this setting to hide certain blocks for the devices you choose.  <strong>If no options are selected, then responsive block hiding will not be active for this block.</strong>','padma'),
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
		$this->tabs['import-export'] = __('Import/Export','padma');

		/* Add the inputs */

		$this->inputs['import-export']['import-heading'] = array(
			'name' => 'import-heading',
			'type' => 'heading',
			'label' => __('Import Block Settings','padma')
		);

			$this->inputs['import-export']['block-import-settings-file'] = array(
				'type' => 'import-file',
				'name' => 'block-import-settings-file',
				'button-label' => __('Select File to Import','padma'),
				'no-save' => true
			);

			$this->inputs['import-export']['block-import-include-options'] = array(
				'type' => 'checkbox',
				'name' => 'block-import-settings-include-options',
				'label' => __('Include Block Options','padma'),
				'default' => true,
				'no-save' => true
			);

			$this->inputs['import-export']['block-import-include-design'] = array(
				'type' => 'checkbox',
				'name' => 'block-import-settings-include-design',
				'label' => __('Include Block Design','padma'),
				'default' => true,
				'no-save' => true
			);

			$this->inputs['import-export']['block-import-settings'] = array(
				'type' => 'button',
				'name' => 'block-import-settings',
				'button-label' => __('Import Block Settings','padma'),
				'no-save' => true,
				'callback' => 'initiateBlockSettingsImport(args);'
			);

		$this->inputs['import-export']['export-heading'] = array(
			'name' => 'export-heading',
			'type' => 'heading',
			'label' => __('Export Block Settings','padma')
		);

			$this->inputs['import-export']['block-export-settings'] = array(
				'type' => 'button',
				'name' => 'block-export-settings',
				'button-label' => __('Download Export File','padma'),
				'no-save' => true,
				'callback' => 'exportBlockSettingsButtonCallback(args);'
			);

	}

	public function get_blocks_select_options_for_mirroring() {

		$block_type = $this->block['type'];	

		$blocks = PadmaBlocksData::get_blocks_by_type($block_type);

		$options = array('' => '&ndash; '. __('Do Not Mirror','padma') . ' &ndash;');

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