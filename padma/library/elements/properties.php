<?php
class PadmaElementProperties {
	
	
	protected static $properties = array(

		/* Fonts */
			'font-family' => array(
				'group' 			=> 'Fonts',
				'name' 				=> 'Font Family',
				'type' 				=> 'font-family-select',
				'js-callback' 		=> 'propertyInputCallbackFontFamily(params);',
				'complex-property' 	=> 'PadmaElementProperties::complex_property_font_family'
			),

			'font-size' => array(
				'group' => 'Fonts',
				'name' => 'Font Size',
				'type' => 'integer',
				'default' => '12',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"font-size": params.value + params.unit});',
				'unit' => array(),
			),

			'color' => array(
				'group' => 'Fonts',
				'name' => 'Font Color',
				'type' => 'color',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"color": params.value});',
				'default' => '000000'
			),

			'line-height' => array(
				'group' => 'Fonts',
				'name' => 'Line Height',
				'type' => 'integer',
				'default' => 100,
				'js-callback' => 'stylesheet.update_rule(params.selector, {"line-height": params.value + params.unit});',
				'unit' => array('default' => '%')
			),

			'font-styling' => array(
				'group' => 'Fonts',
				'name' => 'Font Styling',
				'type' => 'select',
				'options' => array(
					'normal' => 'Normal',
					'light' => 'Light',
					'bold' => 'Bold',
					'italic' => 'Italic',
					'bold-italic' => 'Bold Italic'
				),
				'js-callback' => 'propertyInputCallbackFontStyling(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_font_styling'
			),
			
			'text-align' => array(
				'group' => 'Fonts',
				'name' => 'Text Alignment',
				'type' => 'select',
				'options' => array(
					'left' => 'Left',
					'center' => 'Center',
					'right' => 'Right'
				),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"text-align": params.value});'
			),

			'capitalization' => array(
				'group' => 'Fonts',
				'name' => 'Capitalization',
				'type' => 'select',
				'options' => array(
					'none' => 'Normal',
					'uppercase' => 'Uppercase',
					'lowercase' => 'Lowercase',
					'small-caps' => 'Small Caps',
				),
				'js-callback' => 'propertyInputCallbackCapitalization(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_capitalization'
			),
			
			'letter-spacing' => array(
				'group' => 'Fonts',
				'name' => 'Letter Spacing',
				'type' => 'select',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"letter-spacing": params.value + params.unit});',
				'unit' => 'px',
				'options' => array(
					'0' => '0',
					'1' => '1px',
					'2' => '2px',
					'3' => '3px',
					'-1' => '-1px',
					'-2' => '-2px',
					'-3' => '-3px'
				)
			),
			
			'text-decoration' => array(
				'group' => 'Fonts',
				'name' => 'Text Underline',
				'type' => 'select',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"text-decoration": params.value});',
				'options' => array(
					'none' => 'No Underline',
					'underline' => 'Underline',
				)
			),

		/* Fonts/Text Shadow */
			'text-shadow-horizontal-offset' => array(
				'group' => 'Fonts',
				'name' => 'Shadow: Horizontal Offset',
				'type' => 'integer',
				'unit' => array(),
				'js-callback' => 'propertyInputCallbackShadow(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_shadow',
				'default' => 0
			),

			'text-shadow-vertical-offset' => array(
				'group' => 'Fonts',
				'name' => 'Shadow: Vertical Offset',
				'type' => 'integer',
				'unit' => array(),
				'js-callback' => 'propertyInputCallbackShadow(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_shadow',
				'default' => 0
			),

			'text-shadow-blur' => array(
				'group' => 'Fonts',
				'name' => 'Shadow: Blur',
				'type' => 'integer',
				'unit' => array(),
				'js-callback' => 'propertyInputCallbackShadow(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_shadow',
				'default' => 0
			),

			'text-shadow-color' => array(
				'group' => 'Fonts',
				'name' => 'Shadow: Color',
				'type' => 'color',
				'js-callback' => 'propertyInputCallbackShadow(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_shadow',
				'default' => '000000'
			),

		/* Background */
			'background-color' => array(
				'group' => 'Background',
				'name' => 'Color',
				'type' => 'color',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"background-color": params.value});',
				'default' => 'ffffff'
			),

			'background-image' => array(
				'group' => 'Background',
				'name' => 'Image',
				'type' => 'image',
				'js-callback' => 'propertyInputCallbackBackgroundImage(params);',
				'default' => 'none'
			),

			'background-repeat' => array(
				'group' => 'Background',
				'name' => '&nbsp;&ndash; Repeat',
				'type' => 'select',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"background-repeat": params.value});',
				'options' => array(
					'repeat' => 'Tile',
					'no-repeat' => 'No Tiling',
					'repeat-x' => 'Tile Horizontally',
					'repeat-y' => 'Tile Vertically'
				)
			),

			'background-position' => array(
				'group' => 'Background',
				'name' => '&nbsp;&ndash; Position',
				'type' => 'select',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"background-position": params.value});',
				'options' => array(
					'left top' => 'Left Top',
					'left center' => 'Left Center',
					'left bottom' => 'Left Bottom',
					'right top' => 'Right Top',
					'right center' => 'Right Center',
					'right bottom' => 'Right Bottom',
					'center top' => 'Center Top',
					'center center' => 'Center Center',
					'center bottom' => 'Center Bottom'
				)
			),

			'background-attachment' => array(
				'group' => 'Background',
				'name' => '&nbsp;&ndash; Behavior',
				'type' => 'select',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"background-attachment": params.value});',
				'options' => array(
					'scroll' => 'Stay at top of document (Scroll)',
					'fixed' => 'Stay in same position as you scroll (Fixed)'
				)
			),

			'background-size' => array(
				'group' => 'Background',
				'name' => '&nbsp;&ndash; Size',
				'type' => 'select',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"background-size": params.value});',
				'options' => array(
					'auto' => 'Default',
					'cover' => 'Cover &ndash; Scales the background image so that the smallest dimension reaches the maximum width/height of the element.',
					'contain' => 'Contain &ndash; Ensures that the entire background-image will display by showing the image at a scaled size.'
				)
			),

			'background-parallax' => array(
				'group' => 'Background',
				'name' => '&nbsp;&ndash; Parallax',
				'type' => 'select',
				'js-callback' => '',
				'js-property' => true,
				'options' => array(
					'disable' => 'Disable',
					'enable' => 'Enable'
				)
			),

			'background-parallax-ratio' => array(
				'group' => 'Background',
				'name' => '&nbsp;&ndash; Parallax Ratio',
				'type' => 'integer',
				'js-callback' => '',
				'js-property' => true,
				'step' => '0.1',
				'default' => '0.5'
			),

		/* Borders */
			'border-color' => array(
				'group' => 'Borders',
				'name' => 'Border Color',
				'type' => 'color',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"border-color": params.value});',
				'default' => '000000'
			),

			'border-style' => array(
				'group' => 'Borders',
				'name' => 'Border Style',
				'type' => 'select',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"border-style": params.value});',
				'options' => array(
					'none' => 'Hidden',
					'solid' => 'Solid',
					'dashed' => 'Dashed',
					'dotted' => 'Dotted',
					'double' => 'Double',
					'groove' => 'Grooved',
					'inset' => 'Inset',
					'outset' => 'Outset',
					'ridge' => 'Ridged'
				)
			),

			'border-width' => array(
				'group' => 'Borders',
				'name' => 'Border Width',
				'type' => 'box-model',
				'position' => 'sides',
				'lockable' => true,

				'box-model-inputs' => array(
					'border-top-width',
					'border-right-width',
					'border-bottom-width',
					'border-left-width'
				)
			),

				'border-top-width' => array(
					'group' => 'Borders',
					'name' => 'Top Border Width',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"border-top-width": params.value + params.unit});',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),
				
				'border-right-width' => array(
					'group' => 'Borders',
					'name' => 'Right Border Width',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"border-right-width": params.value + params.unit});',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'border-bottom-width' => array(
					'group' => 'Borders',
					'name' => 'Bottom Border Width',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"border-bottom-width": params.value + params.unit});',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'border-left-width' => array(
					'group' => 'Borders',
					'name' => 'Left Border Width',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"border-left-width": params.value + params.unit});',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),
		
		/* Outline */
			'outline-color' => array(
				'group' => 'Outlines',
				'name' => 'Outline Color',
				'type' => 'color',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"outline-color": params.value});',
				'default' => '000000'
			),

			'outline-style' => array(
				'group' => 'Outlines',
				'name' => 'Outline Style',
				'type' => 'select',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"outline-style": params.value});',
				'options' => array(
					'none' => 'None',
					'hidden' => 'Hidden',
					'solid' => 'Solid',
					'dashed' => 'Dashed',
					'dotted' => 'Dotted',
					'double' => 'Double',
					'groove' => 'Grooved',
					'inset' => 'Inset',
					'outset' => 'Outset',
					'ridge' => 'Ridged',
					'initial' => 'Initial',
					'inherit' => 'Inherit',
				)				 
			),

			'outline-width' => array(
				'group' => 'Outlines',
				'name' => 'Outline Width',
				'type' => 'integer',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"outline-width": params.value + params.unit});',
				'unit' => array(),
			),

				

		/* Padding */
			'padding' => array(
				'group' => 'Padding',
				'type' => 'box-model',
				'position' => 'sides',
				'lockable' => true,

				'box-model-inputs' => array(
					'padding-top',
					'padding-right',
					'padding-bottom',
					'padding-left'
				)
			),

				'padding-top' => array(
					'group' => 'Padding',
					'name' => 'Top',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"padding-top": params.value + params.unit});updateInspectorVisibleBoxModal();',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'padding-right' => array(
					'group' => 'Padding',
					'name' => 'Right',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"padding-right": params.value + params.unit});updateInspectorVisibleBoxModal();',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'padding-bottom' => array(
					'group' => 'Padding',
					'name' => 'Bottom',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"padding-bottom": params.value + params.unit});updateInspectorVisibleBoxModal();',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'padding-left' => array(
					'group' => 'Padding',
					'name' => 'Left',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"padding-left": params.value + params.unit});updateInspectorVisibleBoxModal();',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

		/* Margin */
			'margins' => array(
				'group' => 'Margins',
				'type' => 'box-model',
				'position' => 'sides',
				'lockable' => true,

				'box-model-inputs' => array(
					'margin-top',
					'margin-right',
					'margin-bottom',
					'margin-left'
				)
			),

				'margin-top' => array(
					'group' => 'Margins',
					'name' => 'Top',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"margin-top": params.value + params.unit});updateInspectorVisibleBoxModal();',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'margin-right' => array(
					'group' => 'Margins',
					'name' => 'Right',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"margin-right": params.value + params.unit});updateInspectorVisibleBoxModal();',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'margin-bottom' => array(
					'group' => 'Margins',
					'name' => 'Bottom',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"margin-bottom": params.value + params.unit});updateInspectorVisibleBoxModal();',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'margin-left' => array(
					'group' => 'Margins',
					'name' => 'Left',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"margin-left": params.value + params.unit});updateInspectorVisibleBoxModal();',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'margin-top-auto' => array(
					'group' => 'Margins',
					'name' => 'Margin Top Auto',
					'type' => 'select',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"margin-top": params.value});',
					'options' => array(
						'none' => 'initial',
						'auto' => 'auto',
					),
					'default' => 'initial'
				),

				'margin-right-auto' => array(
					'group' => 'Margins',
					'name' => 'Margin Right Auto',
					'type' => 'select',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"margin-right": params.value});',
					'options' => array(
						'none' => 'initial',
						'auto' => 'auto',
					),
					'default' => 'initial'
				),

				'margin-bottom-auto' => array(
					'group' => 'Margins',
					'name' => 'Margin Bottom Auto',
					'type' => 'select',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"margin-bottom": params.value});',
					'options' => array(
						'none' => 'initial',
						'auto' => 'auto',
					),
					'default' => 'initial'
				),

				'margin-left-auto' => array(
					'group' => 'Margins',
					'name' => 'Margin Left Auto',
					'type' => 'select',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"margin-left": params.value});',
					'options' => array(
						'none' => 'initial',
						'auto' => 'auto',
					),
					'default' => 'initial'
				),

		/* Corners (Border Radius) */
			'border-radius' => array(
				'group' => 'Corners',
				'type' => 'box-model',
				'position' => 'corners',
				'lockable' => true,

				'box-model-inputs' => array(
					'border-top-left-radius',
					'border-top-right-radius',
					'border-bottom-left-radius',
					'border-bottom-right-radius'
				)
			),

				'border-top-left-radius' => array(
					'group' => 'Corners',
					'name' => 'Top Left',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"border-top-left-radius": params.value + params.unit});',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'border-top-right-radius' => array(
					'group' => 'Corners',
					'name' => 'Top Right',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"border-top-right-radius": params.value + params.unit});',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'border-bottom-left-radius' => array(
					'group' => 'Corners',
					'name' => 'Bottom Left',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"border-bottom-left-radius": params.value + params.unit});',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

				'border-bottom-right-radius' => array(
					'group' => 'Corners',
					'name' => 'Bottom Right',
					'type' => 'integer',
					'js-callback' => 'stylesheet.update_rule(params.selector, {"border-bottom-right-radius": params.value + params.unit});',
					'unit' => array(),
					'display' => false,
					'lockable' => true,
					'default' => 0
				),

		/* Box Shadow */
			'box-shadow-horizontal-offset' => array(
				'group' => 'Box Shadow',
				'name' => 'Horizontal Offset',
				'type' => 'integer',
				'unit' => 'px',
				'js-callback' => 'propertyInputCallbackShadow(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_shadow',
				'default' => 0
			),

			'box-shadow-vertical-offset' => array(
				'group' => 'Box Shadow',
				'name' => 'Vertical Offset',
				'type' => 'integer',
				'unit' => 'px',
				'js-callback' => 'propertyInputCallbackShadow(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_shadow',
				'default' => 0
			),

			'box-shadow-blur' => array(
				'group' => 'Box Shadow',
				'name' => 'Blur',
				'type' => 'integer',
				'unit' => 'px',
				'js-callback' => 'propertyInputCallbackShadow(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_shadow',
				'default' => 0
			),

			'box-shadow-color' => array(
				'group' => 'Box Shadow',
				'name' => 'Color',
				'type' => 'color',
				'js-callback' => 'propertyInputCallbackShadow(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_shadow',
				'default' => '000000'
			),

			'box-shadow-position' => array(
				'group' => 'Box Shadow',
				'name' => 'Position',
				'type' => 'select',
				'js-callback' => 'propertyInputCallbackShadow(params);',
				'complex-property' => 'PadmaElementProperties::complex_property_shadow',
				'options' => array(
					'outside' => 'Outside',
					'inset' => 'Inset'
				)
			),

		/* List Styling */
			'list-style' => array(
				'group' => 'Lists',
				'name' => 'List Style',
				'type' => 'select',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"list-style": params.value});',
				'options' => array(
					'none' => 'No Bullet/Style',
					'disc' => 'Bullet',
					'circle' => 'Circle',
					'square' => 'Square',
					'decimal' => 'Decimal Numbers (1., 2., etc)',
					'lower-alpha' => 'Alphabetical (Lowercase)',
					'upper-alpha' => 'Alphabetical (Uppercase)',
					'lower-roman' => 'Roman Numerals (Lowercase)',
					'upper-roman' => 'Roman Numerals (Uppercase)',
				),
				'default' => 'disc'
			),

		/* Nudging */
			'top' => array(
				'group' => 'Nudging',
				'name' => 'Top',
				'type' => 'integer',
				'unit' 	=> array(),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"top": params.value + params.unit});',
				'default' => 0
			),

			'left' => array(
				'group' => 'Nudging',
				'name' => 'Left',
				'type' => 'integer',
				'unit' => array(),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"left": params.value + params.unit});',
				'default' => 0
			),
			'right' => array(
				'group' => 'Nudging',
				'name' => 'Right',
				'type' => 'integer',
				'unit' 	=> array(),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"right": params.value + params.unit});',
				'default' => 0
			),

			'bottom' => array(
				'group' => 'Nudging',
				'name' => 'Bottom',
				'type' => 'integer',
				'unit' => array(),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"bottom": params.value + params.unit});',
				'default' => 0
			),

			'position' => array(
				'group' => 'Nudging',
				'name' => 'Method',
				'type' => 'select',
				'default' => 'static',
				'options' => array(
					'static' => 'Static',
					'relative' => 'Relative',
					'absolute' => 'Absolute',
					'fixed' => 'Floating (Fixed)'
				),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"position": params.value});',
			),

			'z-index' => array(
				'group' => 'Nudging',
				'name' => 'Layer Index (z-index)',
				'type' => 'integer',
				'js-callback' => 'stylesheet.update_rule(params.selector, {"z-index": params.value});',
				'default' => 1
			),

		/* Overflow */
			'overflow' => array(
				'group' => 'Overflow',
				'name' 	=> 'Visibility',
				'type' 	=> 'select',
				'options' => array(
					'visible' => 'Visible',
					'hidden' => 'Hidden',
					'scroll' => 'Scroll',
				),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"overflow": params.value});',
			),

		/*	Sizes	*/
			'width' => array(
				'group' => 'Sizes',
				'name' 	=> 'Width',
				'type' 	=> 'integer',
				'unit' 	=> array(),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"width": params.value + params.unit});',
			),
			'min-width' => array(
				'group' => 'Sizes',
				'name' 	=> 'Min width',
				'type' 	=> 'integer',
				'unit' 	=> array(),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"min-width": params.value + params.unit});',
			),
			'max-width' => array(
				'group' => 'Sizes',
				'name' 	=> 'Max width',
				'type' 	=> 'integer',
				'unit' 	=> array(),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"max-width": params.value + params.unit});',
			),
			'height' => array(
				'group' => 'Sizes',
				'name' 	=> 'Height',
				'type' 	=> 'integer',
				'unit' 	=> array(),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"height": params.value + params.unit});',
			),
			'min-height' => array(
				'group' => 'Sizes',
				'name' 	=> 'Min-height',
				'type' 	=> 'integer',
				'unit' 	=> array(),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"min-height": params.value + params.unit});',
			),
			'max-height' => array(
				'group' => 'Sizes',
				'name' 	=> 'Max-height',
				'type' 	=> 'integer',
				'unit' 	=> array(),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"max-height": params.value + params.unit});',
			),
			'object-fit' => array(
				'group' => 'Sizes',
				'name' 	=> 'Object-fit',
				'type' 	=> 'select',
				'default' => 'static',
				'options' => array(
					'' 				=> 'None',
					'fill' 			=> 'Fill',
					'contain' 		=> 'Contain',
					'cover' 		=> 'Cover',
					'scale-down' 	=> 'Scale-down'
				),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"object-fit": params.value});',
			),
			'object-position' => array(
				'group' => 'Sizes',
				'name' 	=> 'Object-position',
				'type' 	=> 'select',
				'default' => 'static',
				'options' => array(
					'left top' => 'Left Top',
					'left center' => 'Left Center',
					'left bottom' => 'Left Bottom',
					'right top' => 'Right Top',
					'right center' => 'Right Center',
					'right bottom' => 'Right Bottom',
					'center top' => 'Center Top',
					'center center' => 'Center Center',
					'center bottom' => 'Center Bottom'
				),
				'js-callback' => 'stylesheet.update_rule(params.selector, {"object-position": params.value});',
			),

		/*	Animation	*/
			
			'animation-name' => array(
				'group' => 'Animation',
				'name' 	=> 'CSS Animation',
				'type' 	=> 'select',
				'options' => array(
					// Attention Seekers
					'bounce' 		=> 'bounce',
					'flash' 		=> 'flash',
					'pulse' 		=> 'pulse',
					'rubberBand' 	=> 'rubberBand',
					'shake' 		=> 'shake',
					'swing' 		=> 'swing',
					'tada' 			=> 'tada',
					'wobble' 		=> 'wobble',
					'jello' 		=> 'jello',
					'heartBeat' 	=> 'heartBeat',

					// Bouncing Entrances
					'bounceIn' 		=> 'bounceIn',
					'bounceInDown' 	=> 'bounceInDown',
					'bounceInLeft' 	=> 'bounceInLeft',
					'bounceInRight' => 'bounceInRight',
					'bounceInUp' 	=> 'bounceInUp',

					// Bouncing Exits
					'bounceOut' 		=> 'bounceOut',
					'bounceOutDown' 	=> 'bounceOutDown',
					'bounceOutLeft' 	=> 'bounceOutLeft',
					'bounceOutRight' 	=> 'bounceOutRight',
					'bounceOutUp' 		=> 'bounceOutUp',

					// Fading Entrances
					'fadeIn' 			=> 'fadeIn',
					'fadeInDown' 		=> 'fadeInDown',
					'fadeInDownBig' 	=> 'fadeInDownBig',
					'fadeInLeft' 		=> 'fadeInLeft',
					'fadeInLeftBig' 	=> 'fadeInLeftBig',
					'fadeInRight' 		=> 'fadeInRight',
					'fadeInRightBig' 	=> 'fadeInRightBig',
					'fadeInUp' 			=> 'fadeInUp',
					'fadeInUpBig' 		=> 'fadeInUpBig',

					// Fading Exits
					'fadeOut' 			=> 'fadeOut',
					'fadeOutDown' 		=> 'fadeOutDown',
					'fadeOutDownBig' 	=> 'fadeOutDownBig',
					'fadeOutLeft' 		=> 'fadeOutLeft',
					'fadeOutLeftBig' 	=> 'fadeOutLeftBig',
					'fadeOutRight' 		=> 'fadeOutRight',
					'fadeOutRightBig' 	=> 'fadeOutRightBig',
					'fadeOutUp' 		=> 'fadeOutUp',
					'fadeOutUpBig' 		=> 'fadeOutUpBig',

					// Flippers
					'flip' 		=> 'flip',
					'flipInX' 	=> 'flipInX',
					'flipInY' 	=> 'flipInY',
					'flipOutX' 	=> 'flipOutX',
					'flipOutY' 	=> 'flipOutY',

					// Lightspeed
					'lightSpeedIn' 		=> 'lightSpeedIn',
					'lightSpeedOut' 	=> 'lightSpeedOut',

					// Rotating Entrances
					'rotateIn' 			=> 'rotateIn',
					'rotateInDownLeft' 	=> 'rotateInDownLeft',
					'rotateInDownRight' => 'rotateInDownRight',
					'rotateInUpLeft' 	=> 'rotateInUpLeft',
					'rotateInUpRight' 	=> 'rotateInUpRight',

					// Rotating Exits
					'rotateOut' 			=> 'rotateOut',
					'rotateOutDownLeft' 	=> 'rotateOutDownLeft',
					'rotateOutDownRight' 	=> 'rotateOutDownRight',
					'rotateOutUpLeft' 		=> 'rotateOutUpLeft',
					'rotateOutUpRight' 		=> 'rotateOutUpRight',

					// Sliding Entrances
					'slideInUp' 	=> 'slideInUp',
					'slideInDown' 	=> 'slideInDown',
					'slideInLeft' 	=> 'slideInLeft',
					'slideInRight' 	=> 'slideInRight',

					// Sliding Exits
					'slideOutUp' 	=> 'slideOutUp',
					'slideOutDown' 	=> 'slideOutDown',
					'slideOutLeft' 	=> 'slideOutLeft',
					'slideOutRight' => 'slideOutRight',


					// Zoom Entrances
					'zoomIn' 		=> 'zoomIn',
					'zoomInDown' 	=> 'zoomInDown',
					'zoomInLeft' 	=> 'zoomInLeft',
					'zoomInRight' 	=> 'zoomInRight',
					'zoomInUp' 		=> 'zoomInUp',

					// Zoom Exits
					'zoomOut' 		=> 'zoomOut',
					'zoomOutDown' 	=> 'zoomOutDown',
					'zoomOutLeft' 	=> 'zoomOutLeft',
					'zoomOutRight' 	=> 'zoomOutRight',
					'zoomOutUp' 	=> 'zoomOutUp',

					// Specials
					'hinge' 		=> 'hinge',
					'jackInTheBox' 	=> 'jackInTheBox',
					'rollIn' 		=> 'rollIn',
					'rollOut' 		=> 'rollOut',

				),
				'js-callback' => 'propertyInputCallbackAnimation(params);',
			),

			'animation-iteration-count' => array(
				'group' => 'Animation',
				'name' 	=> 'Animation loop',
				'type' 	=> 'select',
				'options' => array(
					'infinite' 	=> 'Infinite loop',
					'1' 		=> 'Run once',
					'2' 		=> 'Twice',
					'3' 		=> '3 Times',
					'4' 		=> '4 Times',
				),
				'js-callback' => 'stylesheet.update_rule(selector, {"animation-iteration-count": params.value});',
			),

			'animation-duration' => array(
				'group' => 'Animation',
				'name' 	=> 'Duration',
				'type' 	=> 'select',
				'options' => array(
					'500ms' => '500ms',
					'1s' 	=> '1 second',
					'2s' 	=> '2 seconds',
					'3s' 	=> '3 seconds',
					'4s' 	=> '4 seconds',
					'5s' 	=> '5 seconds',
				),
				'js-callback' => 'stylesheet.update_rule(selector, {"animation-duration": params.value});',
			),

			'animation-delay' => array(
				'group' => 'Animation',
				'name' 	=> 'Delay',
				'type' 	=> 'select',
				'options' => array(
					'500ms' => '500ms',
					'1s' 	=> '1 second',
					'2s' 	=> '2 seconds',
					'3s' 	=> '3 seconds',
					'4s' 	=> '4 seconds',
					'5s' 	=> '5 seconds',
				),
				'js-callback' => 'stylesheet.update_rule(selector, {"animation-delay": params.value});',
			),

		/*	Transform	*/
			'transform' => array(
				'group' => 'Transform',
				'name' 	=> 'Transform',
				'type' 	=> 'select',
				'default' => 'none',
				'options' => array(
					'rotate' 			=> 'Rotate',
					'rotateX' 			=> 'Rotate X',
					'rotateY' 			=> 'Rotate Y',
					'scale' 			=> 'Scale',
					'scaleX' 			=> 'Scale X',
					'scaleY' 			=> 'Scale Y',
					'skew' 				=> 'Skew',
					'skewX' 			=> 'Skew X',
					'skewY' 			=> 'Skew Y',
					'translate' 		=> 'Translate',
					'translateX' 		=> 'Translate X',
					'translateY' 		=> 'Translate Y',
				),
				'js-callback' => 'propertyInputCallbackTransform(params);',
			),
			'transform-angle' => array(
				'group' => 'Transform',
				'name' 	=> 'Angle',
				'type' 	=> 'integer',
				'default' => '45',
				'js-callback' => 'propertyInputCallbackTransformAngle(params);',
			),


		/*	Transition	*/
			'transition-delay' => array(
				'group' => 'Transition',
				'name' 	=> 'Delay',
				'type' 	=> 'select',
				'options' => array(
					'500ms' => '500ms',
					'1s' 	=> '1 second',
					'2s' 	=> '2 seconds',
					'3s' 	=> '3 seconds',
					'4s' 	=> '4 seconds',
					'5s' 	=> '5 seconds',
				),
				'js-callback' => 'stylesheet.update_rule(selector, {"transition-delay": params.value});',
			),
			'transition-duration' => array(
				'group' => 'Transition',
				'name' 	=> 'Duration',
				'type' 	=> 'select',
				'options' => array(
					'500ms' => '500ms',
					'1s' 	=> '1 second',
					'2s' 	=> '2 seconds',
					'3s' 	=> '3 seconds',
					'4s' 	=> '4 seconds',
					'5s' 	=> '5 seconds',
				),
				'js-callback' => 'stylesheet.update_rule(selector, {"transition-duration": params.value});',
			),
			'transition-property' => array(
				'group' => 'Transition',
				'name' 	=> 'Property',
				'type' 	=> 'select',
				'options' => array(
					'all' => 'All',
					'none' 	=> 'None',
					'initial' 	=> 'Initial',
					'inherit' 	=> 'Inherit',
				),
				'js-callback' => 'stylesheet.update_rule(selector, {"transition-property": params.value});',
			),
			'transition-timing-function' => array(
				'group' => 'Transition',
				'name' 	=> 'Timing function',
				'type' 	=> 'select',
				'options' => array(
					'initial' 	=> 'Initial',
					'inherit' 	=> 'Inherit',
					'ease' 		=> 'Ease',
					'linear' 	=> 'Linear',
					'ease-in' 	=> 'Ease-in',
					'ease-out' 	=> 'Ease-out',
					'ease-in-out' 	=> 'Ease-in-out',
					'step-start' 	=> 'Step-start',
					'step-end' 		=> 'Step-end',
				),
				'js-callback' => 'stylesheet.update_rule(selector, {"transition-timing-function": params.value});',
			),

		/* Advanced */
			'display' => array(
				'group' => 'Advanced',
				'name' => 'Display',
				'type' => 'select',
				'options' => array(
					"none" => "None (Hide)",
					"initial" => "Initial",
					"inherit" => "Inherit",
					"inline" => "Inline",
					"block" => "Block",
					"contents" => "Contents",
					"flex" => "Flex",
					"grid" => "Grid",
					"inline-block" => "Inline block",
					"inline-flex" => "Inline flex",
					"inline-grid" => "inline grid",
					"inline-table" => "Inline table",
					"list-item" => "List item",
					"run-in" => "Run-in",
					"table" => "Table",
					"table-caption" => "Table caption",
					"table-column-group" => "Table column group",
					"table-header-group" => "Table header group",
					"table-footer-group" => "Table footer group",
					"table-row-group" => "Table row group",
					"table-cell" => "Table cell",
					"table-column" => "Table colums",
					"table-row" => "Table row",
				),
				'js-callback' => 'stylesheet.update_rule(params.selector, { "display": params.value });',
				'default' => 'initial'
			),
			'float' => array(
				'group' => 'Advanced',
				'name' => 'Float',
				'type' => 'select',
				'options' => array(
					"initial" => "Initial",
					"inherit" => "Inherit",
					"none" => "none",
					"left" => "Left",
					"right" => "Right",
				),
				'js-callback' => 'stylesheet.update_rule(params.selector, { "float": params.value });',
				'default' => 'initial'
			),
			'clear' => array(
				'group' => 'Advanced',
				'name' => 'Clear',
				'type' => 'select',
				'options' => array(
					"initial" => "Initial",
					"inherit" => "Inherit",
					"none" => "None",
					"both" => "Both",
					"left" => "Left",
					"right" => "Right",
				),
				'js-callback' => 'stylesheet.update_rule(params.selector, { "clear": params.value });',
				'default' => 'initial'
			),
			'visibility' => array(
				'group' => 'Advanced',
				'name' => 'Visibility',
				'type' => 'select',
				'options' => array(
					"visible" => "Visible",
					"initial" => "Initial",
					"inherit" => "Inherit",
					"hidden" => "Hidden",
					"collapse" => "Collapse",
				),
				'js-callback' => 'stylesheet.update_rule(params.selector, { "visibility": params.value });',
				'default' => 'visible'
			),
			'opacity' => array(
				'group' => 'Advanced',
				'name' => 'Opacity',
				'type' => 'integer',
				'unit' => '',				
				'default' => 100,
				'js-callback' => 'stylesheet.update_rule(params.selector, { "opacity": (params.value/100) });',
			),
			'vertical-align' => array(
				'group' => 'Advanced',
				'name' => 'Vertical align',
				'type' => 'select',
				'options' => array(
					"baseline" => "Baseline",
					"bottom" => "Bottom",
					"middle" => "Middle",
					"length" => "Length",
					"sub" => "Sub",
					"super" => "Super",
					"text-bottom" => "Text bottom",					
					"text-top" => "Text top",
					"top" => "Top",
				),	
				'default' => 'baseline',				
			),

		/*		Effects		*/
		/*
		'effect' => array(
				'group' 			=> 'Effects',
				'name' 				=> 'Effects',
				'type' 				=> 'select',
				'default' 			=> 'none',				
				'complex-options' 	=> 'PadmaElementProperties::get_effects_list',
				'js-callback' 		=> 'propertyInputCallbackEffects(params);',
				'complex-property' 	=> 'PadmaElementProperties::complex_property_effect_content'
			),
		*/

	);

	
	public static function get_property($property) {
				
		return isset(self::$properties[$property]) ? self::$properties[$property] : null;
		
	}
	
	
	public static function get_properties_by_group($group) { 
		
		//Filter though all of the properties to make sure they are in the selected group
		$filtered_properties = array_filter(self::$properties, function($property) use ($group){			
			return ($property['group'] === $group);
		});

		if ( !is_array($filtered_properties) || count($filtered_properties) === 0 )
			return null;
		else
			return $filtered_properties;
	
	}


	public static function get_properties() {

		return self::$properties;

	}
	
	
	public static function output_css($selector, $properties = array()) {
				
		if ( !isset($selector) || $selector == false )
			return null;
			
		if ( !is_array($properties) || count($properties) === 0 )
			return null;
					
		$output = '';		
		$effects = array();
		//$transformData = array();
			
			/*	
			if(in_array( 'transform', $properties, true ) && in_array( 'transform-angle', $properties, true ) ){
				$transformData['type'] 	= $properties['transform'];
				$transformData['angle'] = $properties['transform-angle'];				
			}*/


			//Loop through properties
			foreach ( $properties as $property_id => $value ) {

			
				//If the value is an empty string, false, or null, don't attempt to put anything.
				if ( (!isset($value) || $value === '' || $value === false || $value === null || $value === 'null' || $value === 'DELETE') && ($value !== '0' && $value !== 0) )
					continue;
			
				//Look up the property to figure out how to handle it
				$property = self::get_property($property_id);
				
				//If the property does not exist, skip it.
				if ( !$property )
					continue;

				// Dont evaluate transform angle param
				if($property_id == 'transform-angle'){
					continue;
				}


				// If its a effect get css and continue
				if($property['name'] === 'Effects'){

					$effects[] = array(
						'selector' 		=> $selector, 
						'property_id' 	=> $property_id, 
						'value' 		=> $value, 
						'properties' 	=> $properties, 
						'property' 		=> $property
					);
					continue;

				}

				/* Everything's good, inject the selector in if it hasn't already been that way the selector isn't added when an element doesn't have any properties */
					if ( empty($output) )
						$output .= $selector . ' {' . "\n";
								

				//If it's a complex property, pass everything through it.
				if ( padma_get('complex-property', $property) && is_callable(padma_get('complex-property', $property)) ) {
					$output .= call_user_func(padma_get('complex-property', $property), array(
						'selector' => $selector, 
						'property_id' => $property_id, 
						'value' => $value, 
						'properties' => $properties, 
						'property' => $property
					));
					
					continue;

				} else if ( padma_get('js-property', $property) ) {
					continue;
				}
				
				//Format the $value by adding the unit or hex indicator if it's a color				
				if ( padma_get('unit', $property) !== null ) {

					/* we get the unit property value if the unit is customizable */
					if ( is_array(padma_get('unit', $property)) ) {

						$unit = trim( str_replace( array_merge(range( 0, 9 ), array('.', '-')), '', $value ) );
						$unit_settings = padma_get( 'unit', $property );

						/* If there's no unit in the string then pull the default */
						if ( !$unit ) {
							$value = $value . padma_get('default', $unit_settings, 'px');
						}

					} else {
						$value = $value . $property['unit'];
					}

				}
				
				if ( padma_get('type', $property) === 'color' )
					$value = padma_format_color($value);
				
				if ( padma_get('type', $property) === 'image' && $value != 'none' )
					$value = 'url(' . $value . ')';

				
				// Transform support
				if ( padma_get('group', $property) === 'Transform' ){
					
					if($properties['transform'] == 'scale' || $properties['transform'] == 'scaleX' || $properties['transform'] == 'scaleY'){
						$unit = '';
					}elseif($properties['transform'] == 'translate' || $properties['transform'] == 'translateX' || $properties['transform'] == 'translateY'){
						$unit = 'px';
					}else{
						$unit = 'deg';						
					}
					$value =  $properties['transform'] . '(' . $properties['transform-angle'] . $unit . ')';
				}
			
				$output .= $property_id . ': ' . $value . ';' . "\n";
			
			} //foreach: Regular Properties
	
		/* Only close if there's actual output */
		if ( !empty($output) )
			$output .= '}' . "\n";



		// Add effects css code if have to
		foreach ($effects as $key => $data) {

			$selector 		= $data['selector'];
			$property_id 	= $data['property_id'];
			$value 			= $data['value'];
			$properties 	= $data['properties'];
			$property 		= $data['property'];
			
			if(padma_get('complex-property', $property) && is_callable(padma_get('complex-property', $property))){

				$output .= call_user_func(padma_get('complex-property', $property), array(
					'selector' 		=> $selector, 
					'property_id' 	=> $property_id, 
					'value'		 	=> $value, 
					'properties' 	=> $properties, 
					'property' 		=> $property
				));

			}
		}


		return $output;
		
	}
	

	public static function complex_property_font_family($args) {

		extract($args);

		$font_fragments = explode('|', $value);

		/* Web Font */
		if ( count($font_fragments) >= 2 ){

			if (\strpos($font_fragments[1], ':') !== false) {
				$stack = explode(':', $font_fragments[1])[0];
			}else{
				$stack = $font_fragments[1];
			}


		}else{
			/* Traditional Font */
			$stack = PadmaFonts::get_stack($value);

		}

		return 'font-family: ' . $stack . ';';

	}

	
	public static function complex_property_shadow($args) {
		
		extract($args);
												
		$shadow_type = (strpos($property_id, 'box-shadow') !== false) ? 'box-shadow' : 'text-shadow';		
		
		global $padma_complex_property_check;
		
		//If the complex property check isn't even set, make it an empty array.
		if ( !is_array($padma_complex_property_check) )
			$padma_complex_property_check = array($shadow_type => array());
						
		//Since the complex property is a combination of a bunch of properties, we only want it to output once.
		if ( isset($padma_complex_property_check[$shadow_type][$selector]) && $padma_complex_property_check[$shadow_type][$selector] == true )
			return;
			
		$padma_complex_property_check[$shadow_type][$selector] = true;
		
		if ( !isset($properties[$shadow_type . '-color']) )
			return null;

		$shadow_color = padma_format_color($properties[$shadow_type . '-color']);

		if ( $shadow_color == 'transparent' )
			return null;

		$shadow_hoffset = isset($properties[$shadow_type . '-horizontal-offset']) ? $properties[$shadow_type . '-horizontal-offset'] : '0';
		$shadow_voffset = isset($properties[$shadow_type . '-vertical-offset']) ? $properties[$shadow_type . '-vertical-offset'] : '0';
		$shadow_blur 	= isset($properties[$shadow_type . '-blur']) ? $properties[$shadow_type . '-blur'] : '0';
		$shadow_inset 	= (padma_get($shadow_type . '-position', $properties, 'outside') == 'inset') ? ' inset' : null;
				
		return $shadow_type . ': ' . $shadow_color . ' ' . $shadow_hoffset . ' ' . $shadow_voffset . ' ' . $shadow_blur . $shadow_inset . ';';
		
	}
			
		
	public static function complex_property_capitalization($args) {
		
		extract($args);
		
		$data = '';
		
		if ( $value == 'none' ) {
			
			$data .= 'text-transform: none;';
			$data .= 'font-variant: normal;';
			
		} elseif ( $value == 'small-caps' ) {
			
			$data .= 'text-transform: none;';
			$data .= 'font-variant: small-caps;';

		} else {
			
			$data .= 'text-transform: ' . $value . ';';
			$data .= 'font-variant: normal;';
			
		}
		
		return $data;
		
	}
	
	
	public static function complex_property_font_styling($args) {
		
		extract($args);
		
		$data = '';
		
		if ( $value == 'normal' ) {
			
			$data .= 'font-style: normal;';
			$data .= 'font-weight: normal;';
			
		} elseif ( $value == 'bold' ) {
			
			$data .= 'font-style: normal;';
			$data .= 'font-weight: bold;';

		} elseif ( $value == 'light' ) {
			
			$data .= 'font-style: normal;';
			$data .= 'font-weight: lighter;';

		} elseif ( $value == 'italic' ) {

			$data .= 'font-style: italic;';
			$data .= 'font-weight: normal;';

		} elseif ( $value == 'bold-italic' ) {
			
			$data .= 'font-style: italic;';
			$data .= 'font-weight: bold;';
			
		}
		
		return $data;
		
	}


	public static function complex_property_effect_content($args){

		global $wp_filesystem;
		WP_Filesystem();
		
		$effect 	= $args['value'];
		$selector 	= $args['selector'];

		$id = explode('-',$selector)[1];
		$id = explode(' ',$id)[0];

		if($id)
			$block = PadmaBlocksData::get_block($id);

		$path 		= PADMA_LIBRARY_DIR . '/visual-editor/effects-css/' . $effect . '.txt';					
		$selector 	= preg_replace('/\ img/', '', $selector);

		if($path !== false && file_exists($path)){

			$data = preg_replace("/%selector%/", $selector, $wp_filesystem->get_contents($path));

			if($block){				
				$data = preg_replace("/%path%/", $block['settings']['image'], $data);
			}

			return $data;
		}

		return;
	}


	public static function get_effects_list($args){

		$options = array();

		/*		Image effects		*/
		$options['img']	= array(
			'' => '',
			
			'emboss' 				=> 'Emboss',
			'airbrush' 				=> 'Airbrush',
			'chalkboard' 			=> 'Chalkboard',
			'colored-chalkboard' 	=> 'Colored chalkboard',
			'corner-on-hover' 		=> 'Corner on hover',
			'flannel' 				=> 'Flannel',
			'hallucination' 		=> 'Hallucination',
			'infrared' 				=> 'Infrared',
			'low-ink-x' 			=> 'Low-ink horizontal',
			'low-ink-y' 			=> 'Low-ink vertical',
			'mirror-x' 				=> 'Mirror horizontal',
			'mirror-y' 				=> 'Mirror vertical',
			'mosaic' 				=> 'Mosaic',
			'night-vision' 			=> 'Night vision',
			'pencil' 				=> 'Pencil',
			'colored-pencil' 		=> 'Colored pencil',
			'photo-border' 			=> 'Photo border',
			'selective-color-red' 	=> 'Selective color Red',
			'selective-color-blue' 	=> 'Selective color Blue',
			'selective-color-green' => 'Selective color Green',
			'targeting-color' 		=> 'Targeting color',
			'targeting-white' 		=> 'Targeting white',
			'warhol' 				=> 'Warhol',
			'watercolor' 			=> 'Watercolor',
			'zoom-in' 				=> 'Zoom In',
			'zoom-in-and-rotate' 	=> 'Zoom in and rotate',			
			
			'effect-1' 				=> 'Effect 1',
			'effect-2' 				=> 'Effect 2',
			'effect-3' 				=> 'Effect 3',
			'effect-4' 				=> 'Effect 4',
			'effect-5' 				=> 'Effect 5',
			'effect-6' 				=> 'Effect 6',
			'effect-7' 				=> 'Effect 7',
			'effect-8' 				=> 'Effect 8',
			'effect-9' 				=> 'Effect 9',
			'effect-10' 			=> 'Effect 10',
		);

		/*		Pin effects		*/
		/*
		$options['pin']	= array(
			'rotate-effect-on-hover' 			=> 'Rotate effect on hover',
			'image-with-title-on-hover' 		=> 'Image with title on hover',
		);
		*/
		
		switch ($args['element']['name']) {
			case 'Image':
				$opts = $options['img'];
				break;

			case 'Pin':			
				$opts = $options['pin'];
				break;
			
			default:
				$opts = array(
							'none' => 'There is not effects for this element',
						);
				break;
		}

		return $opts;
	}

	
}