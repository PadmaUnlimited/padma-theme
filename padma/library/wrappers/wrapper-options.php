<?php
class PadmaWrapperOptions extends PadmaVisualEditorPanelAPI {


	public function register() {

		return true;

	}


	public function display($wrapper, $layout) {
		
		//Set block properties
		$this->wrapper = $wrapper;

		//Set up arguments
		$args = array(
			'wrapper' => $this->wrapper,
			'layoutID' => $layout
		);

		//Get and display panel
		$this->modify_arguments($args);
		$this->panel_content($args);
		
	}
	
	
	public $id = 'wrapper-options';
	public $name = 'Wrapper Options';
	public $mode = 'grid';
	
	public $tabs = array(
		'setup' => 'Grid &amp; Margins',
		'positioning' => 'Sticky Positioning',
		'config' => 'Mirroring &amp; Config',
		'responsive' => 'Responsive',
		'import-export' => 'Import/Export',
	);
	
	public $inputs = array(
		'setup' => array(		
			'grid-setup-heading' => array(
				'type' => 'heading',
				'name' => 'grid-setup-heading',
				'label' => 'Grid'
			),

				'column-count' => array(
					'type' => 'slider',
					'name' => 'columns',
					'label' => 'Columns',
					'default' => 24,
					'tooltip' => 'Number of columns in the Grid.  Suggested values 9, 12, 16, and 24.<br /><br /><strong>Note:</strong> The wrapper must be empty of all blocks prior to changing the column count.  Either move the blocks to another wrapper or delete them if they are not needed.',
					'slider-min' => 6,
					'slider-max' => 24,
					'slider-interval' => 1,
					'callback' => 'wrapperOptionCallbackColumnCount(input, value);'
				),

				'use-independent-grid' => array(
					'type' => 'checkbox',
					'name' => 'use-independent-grid',
					'label' => 'Use Independent Grid',
					'tooltip' => 'Check this if you would like this wrapper to have different Grid settings than the Global Grid settings.',
					'callback' => 'wrapperOptionCallbackIndependentGrid(input, value);',
					'toggle' => array(
						'true' => array(
							'show' => array(
								'#input-column-width',
								'#input-gutter-width',
								'#input-grid-width'
							)
						),
						'false' => array(
							'hide' => array(
								'#input-column-width',
								'#input-gutter-width',
								'#input-grid-width'
							)
						)
					)
				),

				'column-width' => array(
					'type' => 'slider',
					'name' => 'column-width',
					'label' => 'Column Width',
					'default' => 26,
					'tooltip' => 'The column width is the amount of space inside of each column.  This is represented by the grey regions on the grid.',
					'unit' => 'px',
					'slider-min' => 10,
					'slider-max' => 120,
					'slider-interval' => 1,
					'callback' => 'wrapperOptionCallbackColumnWidth(input, value);'
				),
				
				'gutter-width' => array(
					'type' => 'slider',
					'name' => 'gutter-width',
					'label' => 'Gutter Width',
					'default' => 22,
					'tooltip' => 'The gutter width is the amount of space between each column.  This is the space between each of the grey regions on the grid.',
					'unit' => 'px',
					'slider-min' => 0,
					'slider-max' => 60,
					'slider-interval' => 1,
					'callback' => 'wrapperOptionCallbackGutterWidth(input, value);'
				),
				
				'grid-width' => array(
					'type' => 'integer',
					'unit' => 'px',
					'default' => 1130,
					'name' => 'grid-width',
					'label' => 'Grid Width',
					'readonly' => true
				),

			'wrapper-margins-heading' => array(
				'type' => 'heading',
				'name' => 'wrapper-margins-heading',
				'label' => 'Wrapper Margins'
			),

				'wrapper-margin-top' => array(
					'type' => 'slider',
					'name' => 'wrapper-margin-top',
					'label' => 'Top Margin',
					'default' => 30,
					'tooltip' => 'Space in between the top of this wrapper and the top of the page or the wrapper above it.',
					'unit' => 'px',
					'slider-min' => 0,
					'slider-max' => 200,
					'slider-interval' => 1,
					'callback' => 'wrapperOptionCallbackMarginTop(input, value);',
					'data-handler-callback' => 'dataSetDesignEditorProperty({
						element: "wrapper", 
						property: "margin-top", 
						value: args.value.toString(), 
						specialElementType: "instance", 
						specialElementMeta: "wrapper-" + args.wrapper.id
					});'
				),

				'wrapper-margin-bottom' => array(
					'type' => 'slider',
					'name' => 'wrapper-margin-bottom',
					'label' => 'Bottom Margin',
					'default' => 0,
					'tooltip' => 'Space in between this wrapper and the bottom of the page.',
					'unit' => 'px',
					'slider-min' => 0,
					'slider-max' => 200,
					'slider-interval' => 1,
					'callback' => 'wrapperOptionCallbackMarginBottom(input, value);',
					'data-handler-callback' => 'dataSetDesignEditorProperty({
						element: "wrapper", 
						property: "margin-bottom", 
						value: args.value.toString(), 
						specialElementType: "instance", 
						specialElementMeta: "wrapper-" + args.wrapper.id
					});'
				)
		),

		'positioning' => array(
			'enable-sticky-positioning' => array(
				'type' => 'checkbox',
				'name' => 'enable-sticky-positioning',
				'label' => 'Enable Sticky Positioning',
				'default' => false,
				'tooltip' => '',
				'toggle' => array(
					'true' => array(
						'show' => '#input-sticky-position-top-offset'
					),
					'false' => array(
						'hide' => '#input-sticky-position-top-offset'
					)
				)
			),

			'sticky-position-top-offset' => array(
				'type' => 'slider',
				'name' => 'sticky-position-top-offset',
				'label' => 'Top Offset',
				'slider-min' => 0,
				'slider-max' => 200,
				'slider-interval' => 1,
				'unit' => 'px',
				'default' => '0'
			),


			'enable-shrink-on-scroll' => array(
				'type' => 'checkbox',
				'name' => 'enable-shrink-on-scroll',
				'label' => 'Enable Shrink on Scroll',
				'default' => false,
				'tooltip' => '',
				'toggle' => array(
					'true' => array(
						'show' => array(
							'#input-shrink-on-scroll-ratio',
							'#input-shrink-contained-elements'
						),
					),
					'false' => array(
						'hide' => array(
							'#input-shrink-on-scroll-ratio',
							'#input-shrink-contained-elements'
						),
					)
				)
			),

			'shrink-on-scroll-ratio' => array(
				'type' => 'slider',
				'name' => 'shrink-on-scroll-ratio',
				'label' => 'Shrink ratio',
				'slider-min' => 0,
				'slider-max' => 100,
				'slider-interval' => 1,
				'unit' => '%',
				'default' => '50'
			),

			'shrink-contained-elements' => array(
				'type' => 'checkbox',
				'name' => 'shrink-contained-elements',
				'label' => 'Attempt with child elements',
				'tooltip' => 'Attempt to shrink contained elements',
				'default' => false,
			),
		),

		'config' => array(
			'mirror-wrapper' => array(
				'type' => 'select',
				'chosen' => true,
				'name' => 'mirror-wrapper',
				'label' => 'Mirror Blocks From Another Wrapper',
				'default' => '',
				'tooltip' => 'By using this option, you can tell a wrapper to "mirror" another wrapper and all of its blocks.  This option is useful if you are wanting to share a wrapper&mdash;such as a header&mdash;across layouts on your site.  Select the wrapper you wish to mirror the content from in the select box to the right.',
				'options' => 'get_wrappers_select_options_for_mirroring()',
				'callback' => 'updateWrapperMirrorStatus(args.wrapper.id, value, input);'
			),
			
			'do-not-mirror-wrapper-styles' => array(
				'type' => 'checkbox',
				'chosen' => false,
				'name' => 'do-not-mirror-wrapper-styles',
				'label' => 'Do not mirror styles',
				'default' => '',
				'tooltip' => 'Use this option to prevent styles mirroring'
			),

			'alias' => array(
				'type' => 'text',
				'name' => 'alias',
				'label' => 'Wrapper Alias',
				'default' => '',
				'tooltip' => 'Enter an easily recognizable name for the wrapper alias and it will be used throughout your site admin.  Aliases are used in the Design Editor, mirroring menu, and are a great way of keeping track of a specific wrapper.'
			),

			'css-classes' => array(
				'type' => 'text',
				'name' => 'css-classes',
				'callback' => 'updateWrapperCustomClasses(args.wrapper.id, value);',
				'label' => 'Custom CSS Class(es)',
				'default' => '',
				'tooltip' => 'Need more finite control?  Enter the custom CSS class selectors here and they will be added to the wrappers\'s class attribute. <strong>DO NOT</strong> put regular CSS in here.  Use the Live CSS editor for that.'
			)
		),

		'responsive' => array(
			
			array(
				'type' => 'repeater',
				'name' => 'responsive-wrapper-options',
				'label' => 'Configure Breakpoints.',
				'inputs' => array(

					array(
					'type' => 'select',
					'name' => 'breakpoint',
					'label' => 'Set Breakpoint',
					'options' => array(
						'' => 'Off - No Breakpoint',
						'custom' 	=> 'Custom Width',
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
					'toggle'    => array(
						'' => array(
							'hide' => array(
								'#input-max-width'
							),
						),
						'custom' => array(
							'show' => array(
								'#input-max-width'
							),
						),
						'1824px' => array(
							'hide' => array(
								'#input-max-width'
							),
						),
						'1224px' => array(
							'hide' => array(
								'#input-max-width'
							),
						),
						'1024px' => array(
							'hide' => array(
								'#input-max-width'
							),
						),
						'768px' => array(
							'hide' => array(
								'#input-max-width'
							),
						),
						'600px' => array(
							'hide' => array(
								'#input-max-width'
							),
						),
						'568px' => array(
							'hide' => array(
								'#input-max-width'
							),
						),
						'480px' => array(
							'hide' => array(
								'#input-max-width'
							),
						),
						'320px' => array(
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
						'tooltip' => 'Add px value as well. eg: 600px'
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
						'name' => 'stretch',
						'label' => 'Stretch blocks for mobile',
						'default' => false,
						'tooltip' => 'Enable this option to make all blocks in this wrapper stretch the full wrapper width on smaller screens. Blocks placed side by side may not look good on smaller screens as they fight for the horizontal space. Setting this option makes each block go full width at the set break point.'
					),

					array(
						'type' => 'checkbox',
						'name' => 'auto-center',
						'label' => 'Attempt to Center items',
						'default' => false,
						'tooltip' => 'This will attempt to center the elements in this block at the set breakpoint. NOTE: This will not work for all elements but give it a try and if it works for you then great. More complex html like menus will require custom code.'
					),

					array(
						'type' => 'checkbox',
						'name' => 'hide-wrapper',
						'label' => 'Hide this wrapper',
						'default' => false,
						'tooltip' => 'This will hide this wrapper for the set breakpoint.'
					)

				),
				'sortable' => true,
				'limit' => false,
				'callback' => ''
			)

		),

		/**
		 *
		 * Import / Export Wrappers
		 *
		 */
		'import-export' => array(
			'import-heading' => array(
				'name' => 'import-heading',
				'type' => 'heading',
				'label' => 'Import Wrapper Settings'
			),

			'wrapper-import-settings-file' => array(
				'type' => 'import-file',
				'name' => 'wrapper-import-settings-file',
				'button-label' => 'Select File to Import',
				'no-save' => true
			),

			/*
			'wrapper-import-include-options' => array(
				'type' => 'checkbox',
				'name' => 'wrapper-import-settings-include-options',
				'label' => 'Include Wrapper Options',
				'default' => true,
				'no-save' => true
			),
			'wrapper-import-include-design' => array(
				'type' => 'checkbox',
				'name' => 'wrapper-import-settings-include-design',
				'label' => 'Include Wrapper Design',
				'default' => true,
				'no-save' => true
			),*/

			'wrapper-import-settings' => array(
				'type' => 'button',
				'name' => 'wrapper-import-settings',
				'button-label' => 'Import Wrapper Settings',
				'no-save' => true,
				'callback' => 'initiateWrapperSettingsImport(args);'
			),

			'export-heading' => array(
				'name' => 'export-heading',
				'type' => 'heading',
				'label' => 'Export Wrapper Settings'
			),

			'wrapper-export-settings' => array(
				'type' => 'button',
				'name' => 'wrapper-export-settings',
				'button-label' => 'Download Export File',
				'no-save' => true,
				'callback' => 'exportWrapperSettingsButtonCallback(args);'
			)
		)

	);

	
	
	function modify_arguments($args = false) {

		/* Do not show Wrapper Setup tab in the Design Mode */
		if ( padma_post('mode') == 'design')  {

			unset($this->tabs['setup']);
			unset($this->inputs['setup']);

			return;

		}
		
		/* Grid Settings Defaults */
			$this->inputs['setup']['column-width']['default'] = PadmaWrappers::$default_column_width; 
			$this->inputs['setup']['gutter-width']['default'] = PadmaWrappers::$default_gutter_width; 
		/* End Grid Settings Defaults */

		/* Margins */
			$wrapper_instance_id = 'wrapper-' . $args['wrapper']['id'];

			$this->inputs['setup']['wrapper-margin-top']['value'] = PadmaElementsData::get_special_element_property('wrapper', 'instance', $wrapper_instance_id, 'margin-top', PadmaWrappers::$default_wrapper_margin_top, 'structure'); 
			$this->inputs['setup']['wrapper-margin-bottom']['value'] = PadmaElementsData::get_special_element_property('wrapper', 'instance', $wrapper_instance_id, 'margin-bottom', PadmaWrappers::$default_wrapper_margin_bottom, 'structure'); 
		/* End Margins */

		/* Wrapper Mirror Value */
		$this->inputs['config']['mirror-wrapper']['value'] = PadmaWrappersData::is_wrapper_mirrored($args['wrapper']);
		
	}


	public function get_wrappers_select_options_for_mirroring() {
							
		$wrappers 	= PadmaWrappersData::get_all_wrappers();
		$options 	= array('' => '&ndash; Do Not Mirror &ndash;');
		
		//If there are no wrappers to mirror, then just return the Do Not Mirror option.
		if ( empty($wrappers) || !is_array($wrappers) )
			return $options;
		
		foreach ( $wrappers as $wrapper_id => $wrapper ) {
			
			/* If we can't get a name for the layout, then things probably aren't looking good.  Just skip this wrapper. */
			if ( !($layout_name = PadmaLayout::get_name($wrapper['layout'])) )
				continue;

			/* Check for mirroring here */
			if ( PadmaWrappersData::is_wrapper_mirrored($wrapper) )
				continue;

			if ( isset($this->wrapper['id']) && $this->wrapper['id'] && $wrapper_id == $this->wrapper['id'] )
				continue;

			$current_layout_suffix = ( $this->wrapper['layout'] == $wrapper['layout'] ) ? ' (Warning: Same Layout)' : null;
			$wrapper_alias = padma_get('alias', $wrapper['settings']) ? ' &ndash; ' . padma_get('alias', $wrapper['settings']) : null;

			/* Build info that shows if wrapper is fixed or fluid since a wrapper may not have alias and that can be confusing if it just says "Wrapper - Some Layout" over and over */
			$wrapper_info = array();

			if ( padma_fix_data_type($wrapper['settings']['fluid']) )
				$wrapper_info[] = 'Fluid';

			if ( padma_fix_data_type($wrapper['settings']['fluid-grid']) )
				$wrapper_info[] = 'Fluid Grid';

			$wrapper_info_str = $wrapper_info ? ' &ndash; (' . implode( ', ', $wrapper_info ) . ')' : '';

			if ( ! isset( $options[ $layout_name ] ) ) {
				$options[ $layout_name ] = array();
			}
			
			//Get alias if it exists, otherwise use the default name
			$options[$layout_name][$wrapper_id] = 'Wrapper' . $wrapper_alias . $wrapper_info_str  . $current_layout_suffix;
			
		}

		//Remove the current wrapper from the list
		if ( isset($this->wrapper['id']) && $this->wrapper['id'] )
			unset($options[$this->wrapper['id']]);

		return $options;
		
	}
	
	
}