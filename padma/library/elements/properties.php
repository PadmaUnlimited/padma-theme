<?php
/**
 * Design CSS properties main file.
 *
 * @package Padma
 */

/**
 * Properties class
 */
class PadmaElementProperties {

	/**
	 * Properties to design mode.
	 *
	 * @var array
	 */
	protected static $properties = array(

		/* Smoth Scrolling */
		'scroll-behavior' => array(
			'group' => 'Scroll',
			'name' => 'Scroll',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"scroll-behavior": params.value});',
			'options' => array(
				'auto' => 'Auto',
				'smooth' => 'Smooth',
				'initial' => 'Initial',
				'inherit' => 'Inherit',
			),
			'default' => 'initial',
		),

		/* Fonts */
		'font-family' => array(
			'group'         => 'Fonts',
			'name'          => 'Font Family',
			'type'          => 'font-family-select',
			'js-callback'   => 'propertyInputCallbackFontFamily( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_font_family',
		),

		'font-size' => array(
			'group' => 'Fonts',
			'name' => 'Font Size',
			'type' => 'integer',
			'default' => '12',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"font-size": params.value + params.unit});',
			'unit' => array(),
		),

		'color' => array(
			'group' => 'Fonts',
			'name' => 'Font Color',
			'type' => 'color',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"color": params.value});',
			'default' => '000000',
		),

		'line-height' => array(
			'group' => 'Fonts',
			'name' => 'Line Height',
			'type' => 'integer',
			'default' => 100,
			'js-callback' => 'stylesheet.update_rule( params.selector, {"line-height": params.value + params.unit});',
			'unit' => array( 'default' => '%' ),
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
				'bold-italic' => 'Bold Italic',
			),
			'js-callback' => 'propertyInputCallbackFontStyling( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_font_styling',
		),

		'text-align' => array(
			'group' => 'Fonts',
			'name' => 'Text Alignment',
			'type' => 'select',
			'options' => array(
				'left' => 'Left',
				'center' => 'Center',
				'right' => 'Right',
				'justify' => 'Justify',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"text-align": params.value});',
		),

		'text-align-last' => array(
			'group' => 'Fonts',
			'name' => 'Text Alignment Last',
			'type' => 'select',
			'options' => array(
				'auto'    => 'Auto',
				'start'   => 'Start',
				'end'     => 'End',
				'left'    => 'Left',
				'right'   => 'Right',
				'center'  => 'Center',
				'justify' => 'Justify',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"text-align-last": params.value});',
		),

		'text-indent' => array(
			'group' => 'Fonts',
			'name' => 'Text indent',
			'type' => 'integer',
			'default' => '0',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"text-indent": params.value + params.unit});',
			'unit' => array(),
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
			'js-callback' => 'propertyInputCallbackCapitalization( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_capitalization',
		),

		'letter-spacing' => array(
			'group' => 'Fonts',
			'name' => 'Letter Spacing',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"letter-spacing": params.value + params.unit});',
			'unit' => 'px',
			'options' => array(
				'0' => '0',
				'1' => '1px',
				'2' => '2px',
				'3' => '3px',
				'-1' => '-1px',
				'-2' => '-2px',
				'-3' => '-3px',
			),
		),

		'text-decoration-line' => array(
			'group' => 'Fonts',
			'name' => 'Text Decoration Line',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"text-decoration-line": params.value});',
			'options' => array(
				'none' => 'No Underline',
				'underline' => 'Underline',
				'overline' => 'Overline',
				'line-through' => 'Line Through',
			),
		),

		'text-decoration-color' => array(
			'group' => 'Fonts',
			'name' => 'Text Decoration Color',
			'type' => 'color',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"text-decoration-color": params.value});',
			'default' => '000000'
		),

		'text-decoration-style' => array(
			'group' => 'Fonts',
			'name' => 'Text Decoration Style',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"text-decoration-style": params.value});',
			'options' => array(
				'none' => 'None',
				'solid' => 'Solid',
				'wavy' => 'Wavy',
				'double' => 'Double',
			),
		),

		'text-overflow' => array(
			'group' => 'Fonts',
			'name' => 'Text Overflow',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"text-overflow": params.value});',
			'options' => array(
				'clip' => 'Clip',
				'ellipsis' => 'Ellipsis',
				'string' => 'String',
				'initial' => 'Initial',
				'inherit' => 'Inherit',
			),
		),

		'white-space' => array(
			'group' => 'Fonts',
			'name' => 'White Space',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"white-space": params.value});',
			'options' => array(
				'normal' => 'Normal',
				'nowrap' => 'Nowrap',
				'pre'    => 'Pre',
				'pre-line' => 'Pre-line',
				'pre-wrap' => 'Pre-wrap',
				'initial' => 'Initial',
				'inherit' => 'Inherit',
			),
		),

		'writing-mode' => array(
			'group' => 'Fonts',
			'name' => 'Writing Mode',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"writing-mode": params.value});',
			'options' => array(
				'horizontal-tb' => 'Horizontal from left to right',
				'vertical-rl' => 'Vertically from top to bottom, horizontally from right to left',
				'vertical-lr' => 'Vertically from top to bottom, horizontally from left to right',
			),
		),

		'word-wrap' => array(
			'group' => 'Fonts',
			'name' => 'Word Wrap',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"word-wrap": params.value + params.unit});',
			'options' => array(
				'normal' => 'Normal',
				'break-word' => 'Break word',
			),
		),

		'word-spacing' => array(
			'group' => 'Fonts',
			'name' => 'Word Spacing',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"word-spacing": params.value});',
			'unit' => 'px',
			'options' => array(
				'0' => '0',
				'1' => '1px',
				'2' => '2px',
				'3' => '3px',
				'-1' => '-1px',
				'-2' => '-2px',
				'-3' => '-3px',
			),
		),

		'direction' => array(
			'group' => 'Fonts',
			'name' => 'Text Direction',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"direction": params.value});',
			'options' => array(
				'ltr' => 'Left to right',
				'rtl' => 'Right to left',
			),
			'default' => 'ltr',
		),

		/* Fonts/Text Shadow */
		'text-shadow-horizontal-offset' => array(
			'group' => 'Fonts',
			'name' => 'Shadow: Horizontal Offset',
			'type' => 'integer',
			'unit' => array(),
			'js-callback' => 'propertyInputCallbackShadow( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_shadow',
			'default' => 0,
		),

		'text-shadow-vertical-offset' => array(
			'group' => 'Fonts',
			'name' => 'Shadow: Vertical Offset',
			'type' => 'integer',
			'unit' => array(),
			'js-callback' => 'propertyInputCallbackShadow( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_shadow',
			'default' => 0,
		),

		'text-shadow-blur' => array(
			'group' => 'Fonts',
			'name' => 'Shadow: Blur',
			'type' => 'integer',
			'unit' => array(),
			'js-callback' => 'propertyInputCallbackShadow( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_shadow',
			'default' => 0,
		),

		'text-shadow-color' => array(
			'group' => 'Fonts',
			'name' => 'Shadow: Color',
			'type' => 'color',
			'js-callback' => 'propertyInputCallbackShadow( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_shadow',
			'default' => '000000',
		),

		/* Background */
		'background-color' => array(
			'group' => 'Background',
			'name' => 'Color',
			'type' => 'color',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"background-color": params.value});',
			'default' => 'ffffff',
		),

		'background-image' => array(
			'group' => 'Background',
			'name' => 'Image',
			'type' => 'image',
			'js-callback' => 'propertyInputCallbackBackgroundImage( params );',
			'default' => 'none',
		),

		'background-repeat' => array(
			'group' => 'Background',
			'name' => '&nbsp;&ndash; Repeat',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"background-repeat": params.value});',
			'options' => array(
				'repeat' => 'Tile',
				'no-repeat' => 'No Tiling',
				'repeat-x' => 'Tile Horizontally',
				'repeat-y' => 'Tile Vertically',
			),
		),

		'background-position' => array(
			'group' => 'Background',
			'name' => '&nbsp;&ndash; Position',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"background-position": params.value});',
			'options' => array(
				'left top' => 'Left Top',
				'left center' => 'Left Center',
				'left bottom' => 'Left Bottom',
				'right top' => 'Right Top',
				'right center' => 'Right Center',
				'right bottom' => 'Right Bottom',
				'center top' => 'Center Top',
				'center center' => 'Center Center',
				'center bottom' => 'Center Bottom',
			),
		),

		'background-attachment' => array(
			'group' => 'Background',
			'name' => '&nbsp;&ndash; Behavior',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"background-attachment": params.value});',
			'options' => array(
				'scroll' => 'Stay at top of document (Scroll )',
				'fixed' => 'Stay in same position as you scroll (Fixed )',
			),
		),

		'background-size' => array(
			'group' => 'Background',
			'name' => '&nbsp;&ndash; Size',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"background-size": params.value});',
			'options' => array(
				'auto' => 'Default',
				'cover' => 'Cover &ndash; Scales the background image so that the smallest dimension reaches the maximum width/height of the element.',
				'contain' => 'Contain &ndash; Ensures that the entire background-image will display by showing the image at a scaled size.',
			),
		),

		'background-parallax' => array(
			'group' => 'Background',
			'name' => '&nbsp;&ndash; Parallax',
			'type' => 'select',
			'js-callback' => '',
			'js-property' => true,
			'options' => array(
				'disable' => 'Disable',
				'enable' => 'Enable',
			),
		),

		'background-parallax-ratio' => array(
			'group' => 'Background',
			'name' => '&nbsp;&ndash; Parallax Ratio',
			'type' => 'integer',
			'js-callback' => '',
			'js-property' => true,
			'step' => '0.1',
			'default' => '0.5',
		),

		/* Borders */
		'border-color' => array(
			'group' => 'Borders',
			'name' => 'Border Color',
			'type' => 'color',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"border-color": params.value});',
			'default' => '000000',
		),

		'border-style' => array(
			'group' => 'Borders',
			'name' => 'Border Style',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"border-style": params.value});',
			'options' => array(
				'none' => 'Hidden',
				'solid' => 'Solid',
				'dashed' => 'Dashed',
				'dotted' => 'Dotted',
				'double' => 'Double',
				'groove' => 'Grooved',
				'inset' => 'Inset',
				'outset' => 'Outset',
				'ridge' => 'Ridged',
			),
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
				'border-left-width',
			),
		),

		'border-top-width' => array(
			'group' => 'Borders',
			'name' => 'Top Border Width',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"border-top-width": params.value + params.unit});',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'border-right-width' => array(
			'group' => 'Borders',
			'name' => 'Right Border Width',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"border-right-width": params.value + params.unit});',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'border-bottom-width' => array(
			'group' => 'Borders',
			'name' => 'Bottom Border Width',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"border-bottom-width": params.value + params.unit});',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'border-left-width' => array(
			'group' => 'Borders',
			'name' => 'Left Border Width',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"border-left-width": params.value + params.unit});',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		/* Outline */
		'outline-color' => array(
			'group' => 'Outlines',
			'name' => 'Outline Color',
			'type' => 'color',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"outline-color": params.value});',
			'default' => '000000',
		),

		'outline-style' => array(
			'group' => 'Outlines',
			'name' => 'Outline Style',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"outline-style": params.value});',
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
			),
		),

		'outline-width' => array(
			'group' => 'Outlines',
			'name' => 'Outline Width',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"outline-width": params.value + params.unit});',
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
				'padding-left',
			),
		),

		'padding-top' => array(
			'group' => 'Padding',
			'name' => 'Top',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"padding-top": params.value + params.unit});updateInspectorVisibleBoxModal();',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'padding-right' => array(
			'group' => 'Padding',
			'name' => 'Right',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"padding-right": params.value + params.unit});updateInspectorVisibleBoxModal();',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'padding-bottom' => array(
			'group' => 'Padding',
			'name' => 'Bottom',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"padding-bottom": params.value + params.unit});updateInspectorVisibleBoxModal();',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'padding-left' => array(
			'group' => 'Padding',
			'name' => 'Left',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"padding-left": params.value + params.unit});updateInspectorVisibleBoxModal();',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
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
				'margin-left',
			),
		),

		'margin-top' => array(
			'group' => 'Margins',
			'name' => 'Top',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"margin-top": params.value + params.unit});updateInspectorVisibleBoxModal();',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'margin-right' => array(
			'group' => 'Margins',
			'name' => 'Right',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"margin-right": params.value + params.unit});updateInspectorVisibleBoxModal();',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'margin-bottom' => array(
			'group' => 'Margins',
			'name' => 'Bottom',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"margin-bottom": params.value + params.unit});updateInspectorVisibleBoxModal();',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'margin-left' => array(
			'group' => 'Margins',
			'name' => 'Left',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"margin-left": params.value + params.unit});updateInspectorVisibleBoxModal();',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'margin-top-auto' => array(
			'group' => 'Margins',
			'name' => 'Margin Top Auto',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"margin-top": params.value});',
			'options' => array(
				'none' => 'initial',
				'auto' => 'auto',
			),
			'default' => 'initial',
		),

		'margin-right-auto' => array(
			'group' => 'Margins',
			'name' => 'Margin Right Auto',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"margin-right": params.value});',
			'options' => array(
				'none' => 'initial',
				'auto' => 'auto',
			),
			'default' => 'initial',
		),

		'margin-bottom-auto' => array(
			'group' => 'Margins',
			'name' => 'Margin Bottom Auto',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"margin-bottom": params.value});',
			'options' => array(
				'none' => 'initial',
				'auto' => 'auto',
			),
			'default' => 'initial',
		),

		'margin-left-auto' => array(
			'group' => 'Margins',
			'name' => 'Margin Left Auto',
			'type' => 'select',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"margin-left": params.value});',
			'options' => array(
				'none' => 'initial',
				'auto' => 'auto',
			),
			'default' => 'initial',
		),

		/* Corners (Border Radius ) */
		'border-radius' => array(
			'group' => 'Corners',
			'type' => 'box-model',
			'position' => 'corners',
			'lockable' => true,

			'box-model-inputs' => array(
				'border-top-left-radius',
				'border-top-right-radius',
				'border-bottom-left-radius',
				'border-bottom-right-radius',
			),
		),

		'border-top-left-radius' => array(
			'group' => 'Corners',
			'name' => 'Top Left',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"border-top-left-radius": params.value + params.unit});',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'border-top-right-radius' => array(
			'group' => 'Corners',
			'name' => 'Top Right',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"border-top-right-radius": params.value + params.unit});',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'border-bottom-left-radius' => array(
			'group' => 'Corners',
			'name' => 'Bottom Left',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"border-bottom-left-radius": params.value + params.unit});',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		'border-bottom-right-radius' => array(
			'group' => 'Corners',
			'name' => 'Bottom Right',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"border-bottom-right-radius": params.value + params.unit});',
			'unit' => array(),
			'display' => false,
			'lockable' => true,
			'default' => 0,
		),

		/* Box Shadow */
		'box-shadow-horizontal-offset' => array(
			'group' => 'Box Shadow',
			'name' => 'Horizontal Offset',
			'type' => 'integer',
			'unit' => 'px',
			'js-callback' => 'propertyInputCallbackShadow( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_shadow',
			'default' => 0,
		),

		'box-shadow-vertical-offset' => array(
			'group' => 'Box Shadow',
			'name' => 'Vertical Offset',
			'type' => 'integer',
			'unit' => 'px',
			'js-callback' => 'propertyInputCallbackShadow( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_shadow',
			'default' => 0,
		),

		'box-shadow-blur' => array(
			'group' => 'Box Shadow',
			'name' => 'Blur',
			'type' => 'integer',
			'unit' => 'px',
			'js-callback' => 'propertyInputCallbackShadow( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_shadow',
			'default' => 0,
		),

		'box-shadow-spread' => array(
			'group' => 'Box Shadow',
			'name' => 'Spread',
			'type' => 'integer',
			'unit' => 'px',
			'js-callback' => 'propertyInputCallbackShadow( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_shadow',
			'default' => 0,
		),

		'box-shadow-color' => array(
			'group' => 'Box Shadow',
			'name' => 'Color',
			'type' => 'color',
			'js-callback' => 'propertyInputCallbackShadow( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_shadow',
			'default' => '000000',
		),

		'box-shadow-position' => array(
			'group' => 'Box Shadow',
			'name' => 'Position',
			'type' => 'select',
			'js-callback' => 'propertyInputCallbackShadow( params );',
			'complex-property' => 'PadmaElementProperties::complex_property_shadow',
			'options' => array(
				'outside' => 'Outside',
				'inset' => 'Inset',
			),
		),

		/* List Styling */
		'list-style-image' => array(
			'group' => 'Lists',
			'name' => 'Image',
			'type' => 'image',
			'js-callback' => 'propertyInputCallbackListImage( params );',
			'default' => 'none',
		),

		'list-style-position' => array(
			'group' => 'Lists',
			'name' => 'Position',
			'type' => 'select',
			'default' => 'outside',
			'options' => array(
				'outside' => 'Outside',
				'inside' => 'Inside',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"list-style-position": params.value});',
		),


		'list-style-type' => array(
			'group' => 'Lists',
			'name' => 'Type',
			'type' => 'select',
			'default' => 'disc',
			'options' => array(
				'disc' => 'Disc',
				'armenian' => 'Armenian',
				'circle' => 'Circle',
				'cjk-ideographic' => 'CJK Ideographic',
				'decimal' => 'Decimal',
				'decimal-leading-zero' => 'Decimal Leading Zero',
				'georgian' => 'Georgian',
				'hebrew' => 'Hebrew',
				'hiragana' => 'Hiragana',
				'hiragana-iroha' => 'Hiragana Iroha',
				'katakana' => 'Katakana',
				'katakana-iroha' => 'Katakana Iroha',
				'lower-alpha' => 'Lower alpha',
				'lower-greek' => 'Lower greek',
				'lower-latin' => 'Lower latin',
				'lower-roman' => 'Lower roman',
				'none' => 'none',
				'square' => 'Square',
				'upper-alpha' => 'Upper alpha',
				'upper-greek' => 'Upper greek',
				'upper-latin' => 'Upper latin',
				'upper-roman' => 'Upper roman',
				'initial' => 'Initial',
				'inherit' => 'Inherit',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"list-style-position": params.value});',
		),

		/* Nudging */
		'top' => array(
			'group' => 'Nudging',
			'name' => 'Top',
			'type' => 'integer',
			'unit' => array(),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"top": params.value + params.unit});',
			'default' => 0,
		),

		'left' => array(
			'group' => 'Nudging',
			'name' => 'Left',
			'type' => 'integer',
			'unit' => array(),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"left": params.value + params.unit});',
			'default' => 0,
		),
		'right' => array(
			'group' => 'Nudging',
			'name' => 'Right',
			'type' => 'integer',
			'unit' => array(),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"right": params.value + params.unit});',
			'default' => 0,
		),

		'bottom' => array(
			'group' => 'Nudging',
			'name' => 'Bottom',
			'type' => 'integer',
			'unit' => array(),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"bottom": params.value + params.unit});',
			'default' => 0,
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
				'fixed' => 'Floating (Fixed )',
				'sticky' => 'Sticky',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"position": params.value});',
		),

		'z-index' => array(
			'group' => 'Nudging',
			'name' => 'Layer Index ( z-index )',
			'type' => 'integer',
			'js-callback' => 'stylesheet.update_rule( params.selector, {"z-index": params.value});',
			'default' => 1,
		),

		/* Overflow */
		'overflow' => array(
			'group' => 'Overflow',
			'name' => 'Visibility',
			'type' => 'select',
			'options' => array(
				'visible' => 'Visible',
				'hidden' => 'Hidden',
				'scroll' => 'Scroll',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"overflow": params.value});',
		),

		/* Sizes */
		'width' => array(
			'group' => 'Sizes',
			'name'  => 'Width',
			'type'  => 'integer',
			'unit'  => array(),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"width": params.value + params.unit});',
		),
		'min-width' => array(
			'group' => 'Sizes',
			'name'  => 'Min width',
			'type'  => 'integer',
			'unit'  => array(),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"min-width": params.value + params.unit});',
		),
		'max-width' => array(
			'group' => 'Sizes',
			'name'  => 'Max width',
			'type'  => 'integer',
			'unit'  => array(),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"max-width": params.value + params.unit});',
		),
		'height' => array(
			'group' => 'Sizes',
			'name'  => 'Height',
			'type'  => 'integer',
			'unit'  => array(),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"height": params.value + params.unit});',
		),
		'min-height' => array(
			'group' => 'Sizes',
			'name'  => 'Min-height',
			'type'  => 'integer',
			'unit'  => array(),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"min-height": params.value + params.unit});',
		),
		'max-height' => array(
			'group' => 'Sizes',
			'name'  => 'Max-height',
			'type'  => 'integer',
			'unit'  => array(),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"max-height": params.value + params.unit});',
		),
		'object-fit' => array(
			'group' => 'Sizes',
			'name'  => 'Object-fit',
			'type'  => 'select',
			'default' => 'static',
			'options' => array(
				'' => 'None',
				'fill' => 'Fill',
				'contain' => 'Contain',
				'cover' => 'Cover',
				'scale-down'  => 'Scale-down',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"object-fit": params.value});',
		),
		'object-position' => array(
			'group' => 'Sizes',
			'name'  => 'Object-position',
			'type'  => 'select',
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
				'center bottom' => 'Center Bottom',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, {"object-position": params.value});',
		),

		/* Animation */
		'animation-name' => array(
			'group' => 'Animation',
			'name'  => 'CSS Animation',
			'type'  => 'select',
			'options' => array(
				// Attention Seekers.
				'bounce'  => 'bounce',
				'flash'  => 'flash',
				'pulse'  => 'pulse',
				'rubberBand'  => 'rubberBand',
				'shake'  => 'shake',
				'swing'  => 'swing',
				'tada'  => 'tada',
				'wobble'  => 'wobble',
				'jello'  => 'jello',
				'heartBeat'  => 'heartBeat',

				// Bouncing Entrances.
				'bounceIn'  => 'bounceIn',
				'bounceInDown'  => 'bounceInDown',
				'bounceInLeft'  => 'bounceInLeft',
				'bounceInRight' => 'bounceInRight',
				'bounceInUp'  => 'bounceInUp',

				// Bouncing Exits.
				'bounceOut'  => 'bounceOut',
				'bounceOutDown'  => 'bounceOutDown',
				'bounceOutLeft'  => 'bounceOutLeft',
				'bounceOutRight'  => 'bounceOutRight',
				'bounceOutUp'  => 'bounceOutUp',

				// Fading Entrances.
				'fadeIn'  => 'fadeIn',
				'fadeInDown'  => 'fadeInDown',
				'fadeInDownBig'  => 'fadeInDownBig',
				'fadeInLeft'  => 'fadeInLeft',
				'fadeInLeftBig'  => 'fadeInLeftBig',
				'fadeInRight'  => 'fadeInRight',
				'fadeInRightBig'  => 'fadeInRightBig',
				'fadeInUp'  => 'fadeInUp',
				'fadeInUpBig'  => 'fadeInUpBig',

				// Fading Exits.
				'fadeOut'  => 'fadeOut',
				'fadeOutDown'  => 'fadeOutDown',
				'fadeOutDownBig'  => 'fadeOutDownBig',
				'fadeOutLeft'  => 'fadeOutLeft',
				'fadeOutLeftBig'  => 'fadeOutLeftBig',
				'fadeOutRight'  => 'fadeOutRight',
				'fadeOutRightBig'  => 'fadeOutRightBig',
				'fadeOutUp'  => 'fadeOutUp',
				'fadeOutUpBig'  => 'fadeOutUpBig',

				// Flippers.
				'flip'  => 'flip',
				'flipInX'  => 'flipInX',
				'flipInY'  => 'flipInY',
				'flipOutX'  => 'flipOutX',
				'flipOutY'  => 'flipOutY',

				// Lightspeed.
				'lightSpeedIn'  => 'lightSpeedIn',
				'lightSpeedOut'  => 'lightSpeedOut',

				// Rotating Entrances.
				'rotateIn'  => 'rotateIn',
				'rotateInDownLeft'  => 'rotateInDownLeft',
				'rotateInDownRight' => 'rotateInDownRight',
				'rotateInUpLeft'  => 'rotateInUpLeft',
				'rotateInUpRight'  => 'rotateInUpRight',

				// Rotating Exits.
				'rotateOut'  => 'rotateOut',
				'rotateOutDownLeft'  => 'rotateOutDownLeft',
				'rotateOutDownRight'  => 'rotateOutDownRight',
				'rotateOutUpLeft'  => 'rotateOutUpLeft',
				'rotateOutUpRight'  => 'rotateOutUpRight',

				// Sliding Entrances.
				'slideInUp'  => 'slideInUp',
				'slideInDown'  => 'slideInDown',
				'slideInLeft'  => 'slideInLeft',
				'slideInRight'  => 'slideInRight',

				// Sliding Exits.
				'slideOutUp'  => 'slideOutUp',
				'slideOutDown'  => 'slideOutDown',
				'slideOutLeft'  => 'slideOutLeft',
				'slideOutRight' => 'slideOutRight',


				// Zoom Entrances.
				'zoomIn'  => 'zoomIn',
				'zoomInDown'  => 'zoomInDown',
				'zoomInLeft'  => 'zoomInLeft',
				'zoomInRight'  => 'zoomInRight',
				'zoomInUp'  => 'zoomInUp',

				// Zoom Exits.
				'zoomOut'  => 'zoomOut',
				'zoomOutDown'  => 'zoomOutDown',
				'zoomOutLeft'  => 'zoomOutLeft',
				'zoomOutRight'  => 'zoomOutRight',
				'zoomOutUp'  => 'zoomOutUp',

				// Specials.
				'hinge'  => 'hinge',
				'jackInTheBox'  => 'jackInTheBox',
				'rollIn'  => 'rollIn',
				'rollOut'  => 'rollOut',

			),
			'js-callback' => 'propertyInputCallbackAnimation( params );',
		),

		'animation-iteration-count' => array(
			'group' => 'Animation',
			'name'  => 'Animation loop',
			'type'  => 'select',
			'options' => array(
				'infinite'  => 'Infinite loop',
				'1'  => 'Run once',
				'2'  => 'Twice',
				'3'  => '3 Times',
				'4'  => '4 Times',
			),
			'js-callback' => 'stylesheet.update_rule( selector, {"animation-iteration-count": params.value});',
		),

		'animation-duration' => array(
			'group' => 'Animation',
			'name'  => 'Duration',
			'type'  => 'select',
			'options' => array(
				'100ms' => '100ms',
				'200ms' => '200ms',
				'300ms' => '300ms',
				'400ms' => '400ms',
				'500ms' => '500ms',
				'1s'  => '1 second',
				'2s'  => '2 seconds',
				'3s'  => '3 seconds',
				'4s'  => '4 seconds',
				'5s'  => '5 seconds',
				'6s'  => '6 seconds',
				'7s'  => '7 seconds',
				'8s'  => '8 seconds',
				'9s'  => '9 seconds',
				'10s'  => '10 seconds',
			),
			'js-callback' => 'stylesheet.update_rule( selector, {"animation-duration": params.value});',
		),

		'animation-delay' => array(
			'group' => 'Animation',
			'name'  => 'Delay',
			'type'  => 'select',
			'options' => array(
				'100ms' => '100ms',
				'200ms' => '200ms',
				'300ms' => '300ms',
				'400ms' => '400ms',
				'500ms' => '500ms',
				'1s'  => '1 second',
				'2s'  => '2 seconds',
				'3s'  => '3 seconds',
				'4s'  => '4 seconds',
				'5s'  => '5 seconds',
				'6s'  => '6 seconds',
				'7s'  => '7 seconds',
				'8s'  => '8 seconds',
				'9s'  => '9 seconds',
				'10s'  => '10 seconds',
			),
			'js-callback' => 'stylesheet.update_rule( selector, {"animation-delay": params.value});',
		),

		'animation-fill-mode' => array(
			'group' => 'Animation',
			'name'  => 'Fill Mode',
			'type'  => 'select',
			'options' => array(
				'none' => 'None',
				'forwards' => 'Forwards',
				'backwards' => 'Backwards',
				'both' => 'Both',
				'initial' => 'Initial',
			),
			'js-callback' => 'stylesheet.update_rule( selector, {"animation-fill-mode": params.value});',
		),

		'animation-play-state' => array(
			'group' => 'Animation',
			'name'  => 'Play state',
			'type'  => 'select',
			'options' => array(
				'running' => 'Running',
				'paused' => 'Paused',
			),
			'js-callback' => 'stylesheet.update_rule( selector, {"animation-play-state": params.value});',
		),


		'animation-rule' => array(
			'group' => 'Animation',
			'name'  => 'When animate',
			'type'  => 'select',
			'options' => array(
				'initial' => 'Initial',
				'always' => 'Always',
				'when-visible' => 'When visible',
				'on-mouse-over' => 'On Mouse over',
			),
			'js-callback' => 'propertyInputCallbackAnimationRules( params,block );',
		),

		/* Transform */
		'transform' => array(
			'group' => 'Transform',
			'name'  => 'Transform',
			'type'  => 'select',
			'default' => 'none',
			'options' => array(
				'rotate'  => 'Rotate',
				'rotateX'  => 'Rotate X',
				'rotateY'  => 'Rotate Y',
				'scale'  => 'Scale',
				'scaleX'  => 'Scale X',
				'scaleY'  => 'Scale Y',
				'skew'  => 'Skew',
				'skewX'  => 'Skew X',
				'skewY'  => 'Skew Y',
				'translate'  => 'Translate',
				'translateX'  => 'Translate X',
				'translateY'  => 'Translate Y',
			),
			'js-callback' => 'propertyInputCallbackTransform( params );',
		),
		'transform-angle' => array(
			'group' => 'Transform',
			'name'  => 'Angle',
			'type'  => 'integer',
			'default' => '45',
			'js-callback' => 'propertyInputCallbackTransformAngle( params );',
		),

		/* Transition */
		'transition-delay' => array(
			'group' => 'Transition',
			'name'  => 'Delay',
			'type'  => 'select',
			'options' => array(
				'100ms' => '100ms',
				'200ms' => '200ms',
				'300ms' => '300ms',
				'400ms' => '400ms',
				'500ms' => '500ms',
				'1s'  => '1 second',
				'2s'  => '2 seconds',
				'3s'  => '3 seconds',
				'4s'  => '4 seconds',
				'5s'  => '5 seconds',
				'6s'  => '6 seconds',
				'7s'  => '7 seconds',
				'8s'  => '8 seconds',
				'9s'  => '9 seconds',
				'10s'  => '10 seconds',
			),
			'js-callback' => 'stylesheet.update_rule( selector, {"transition-delay": params.value});',
		),
		'transition-duration' => array(
			'group' => 'Transition',
			'name'  => 'Duration',
			'type'  => 'select',
			'options' => array(
				'100ms' => '100ms',
				'200ms' => '200ms',
				'300ms' => '300ms',
				'400ms' => '400ms',
				'500ms' => '500ms',
				'1s'  => '1 second',
				'2s'  => '2 seconds',
				'3s'  => '3 seconds',
				'4s'  => '4 seconds',
				'5s'  => '5 seconds',
				'6s'  => '6 seconds',
				'7s'  => '7 seconds',
				'8s'  => '8 seconds',
				'9s'  => '9 seconds',
				'10s'  => '10 seconds',
			),
			'js-callback' => 'stylesheet.update_rule( selector, {"transition-duration": params.value});',
		),
		'transition-property' => array(
			'group' => 'Transition',
			'name'  => 'Property',
			'type'  => 'select',
			'options' => array(
				'all' => 'All',
				'none'  => 'None',
				'initial'  => 'Initial',
				'inherit'  => 'Inherit',
			),
			'js-callback' => 'stylesheet.update_rule( selector, {"transition-property": params.value});',
		),
		'transition-timing-function' => array(
			'group' => 'Transition',
			'name'  => 'Timing function',
			'type'  => 'select',
			'options' => array(
				'initial'  => 'Initial',
				'inherit'  => 'Inherit',
				'ease'  => 'Ease',
				'linear'  => 'Linear',
				'ease-in'  => 'Ease-in',
				'ease-out'  => 'Ease-out',
				'ease-in-out'  => 'Ease-in-out',
				'step-start'  => 'Step-start',
				'step-end'  => 'Step-end',
			),
			'js-callback' => 'stylesheet.update_rule( selector, {"transition-timing-function": params.value});',
		),

		/* Advanced */
		'display' => array(
			'group' => 'Advanced',
			'name' => 'Display',
			'type' => 'select',
			'options' => array(
				'none' => 'None (Hide )',
				'initial' => 'Initial',
				'inherit' => 'Inherit',
				'inline' => 'Inline',
				'block' => 'Block',
				'contents' => 'Contents',
				'flex' => 'Flex',
				'grid' => 'Grid',
				'inline-block' => 'Inline block',
				'inline-flex' => 'Inline flex',
				'inline-grid' => 'inline grid',
				'inline-table' => 'Inline table',
				'list-item' => 'List item',
				'run-in' => 'Run-in',
				'table' => 'Table',
				'table-caption' => 'Table caption',
				'table-column-group' => 'Table column group',
				'table-header-group' => 'Table header group',
				'table-footer-group' => 'Table footer group',
				'table-row-group' => 'Table row group',
				'table-cell' => 'Table cell',
				'table-column' => 'Table colums',
				'table-row' => 'Table row',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, { "display": params.value });',
			'default' => 'initial',
		),
		'float' => array(
			'group' => 'Advanced',
			'name' => 'Float',
			'type' => 'select',
			'options' => array(
				'initial' => 'Initial',
				'inherit' => 'Inherit',
				'none' => 'none',
				'left' => 'Left',
				'right' => 'Right',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, { "float": params.value });',
			'default' => 'initial',
		),
		'clear' => array(
			'group' => 'Advanced',
			'name' => 'Clear',
			'type' => 'select',
			'options' => array(
				'initial' => 'Initial',
				'inherit' => 'Inherit',
				'none' => 'None',
				'both' => 'Both',
				'left' => 'Left',
				'right' => 'Right',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, { "clear": params.value });',
			'default' => 'initial',
		),
		'visibility' => array(
			'group' => 'Advanced',
			'name' => 'Visibility',
			'type' => 'select',
			'options' => array(
				'visible' => 'Visible',
				'initial' => 'Initial',
				'inherit' => 'Inherit',
				'hidden' => 'Hidden',
				'collapse' => 'Collapse',
			),
			'js-callback' => 'stylesheet.update_rule( params.selector, { "visibility": params.value });',
			'default' => 'visible',
		),
		'opacity' => array(
			'group' => 'Advanced',
			'name' => 'Opacity',
			'type' => 'integer',
			'unit' => '',
			'default' => 100,
			'js-callback' => 'stylesheet.update_rule( params.selector, { "opacity": ( params.value/100) });',
		),
		'vertical-align' => array(
			'group' => 'Advanced',
			'name' => 'Vertical align',
			'type' => 'select',
			'options' => array(
				'baseline' => 'Baseline',
				'bottom' => 'Bottom',
				'middle' => 'Middle',
				'length' => 'Length',
				'sub' => 'Sub',
				'super' => 'Super',
				'text-bottom' => 'Text bottom',
				'text-top' => 'Text top',
				'top' => 'Top',
			),
			'default' => 'baseline',
		),

		/* Filter */
		'filter' => array(
			'group' => 'Filter',
			'name'  => 'Filter',
			'type'  => 'select',
			'options' => array(
				'none'  => 'none',
				'blur'  => 'Blur',
				'brightness' => 'Brightness',
				'contrast' => 'Contrast',
				'grayscale' => 'Grayscale',
				'hue-rotate' => 'Hue-Rotate',
				'invert' => 'Invert',
				'opacity' => 'Opacity',
				'saturate' => 'Saturate',
				'sepia' => 'Sepia',
			),
			'js-callback'  => 'propertyInputCallbackFilter( params,block );',
			'complex-property' => 'PadmaElementProperties::complex_property_filter',
		),

		'filter-value' => array(
			'group' => 'Filter',
			'name'  => 'Value',
			'max'  => 100,
			'min'  => 0,
			'type'  => 'integer',
			'js-callback'  => 'propertyInputCallbackFilterValue( params,block );',
			'complex-property'  => 'PadmaElementProperties::complex_property_filter',
		),

		/* Flex Box */
		'align-items' => array(
			'group' => 'Flexbox',
			'name' => 'Align items',
			'type' => 'select',
			'options' => array(
				'initial' => 'Initial',
				'inherit' => 'Inherit',
				'stretch' => 'Stretch',
				'center' => 'Center',
				'flex-start' => 'Flex-start',
				'flex-end' => 'Flex-end',
				'baseline' => 'Baseline',
			),
			'default' => 'initial',
		),
		'align-content' => array(
			'group' => 'Flexbox',
			'name' => 'Align content',
			'type' => 'select',
			'options' => array(
				'initial' => 'Initial',
				'inherit' => 'Inherit',
				'stretch' => 'Stretch',
				'center' => 'Center',
				'flex-start' => 'Flex-start',
				'flex-end' => 'Flex-end',
				'space-between' => 'Space Between',
				'space-around' => 'Space Around',
			),
			'default' => 'initial',
		),
		'align-self' => array(
			'group' => 'Flexbox',
			'name' => 'Align self',
			'type' => 'select',
			'options' => array(
				'initial' => 'Initial',
				'inherit' => 'Inherit',
				'auto' => 'auto',
				'baseline' => 'baseline',
				'stretch' => 'stretch',
				'center' => 'center',
				'flex-start' => 'flex-start',
				'flex-end' => 'flex-end',
			),
			'default' => 'initial',
		),
		'flex-basis' => array(
			'group' => 'Flexbox',
			'name' => 'Flex basis',
			'type' => 'integer',
			'default' => '0',
			'unit' => array(),
		),
		'flex-direction' => array(
			'group' => 'Flexbox',
			'name' => 'Flex direction',
			'type' => 'select',
			'options' => array(
				'initial' => 'Initial',
				'inherit' => 'Inherit',
				'row' => 'Row',
				'row-reverse' => 'Row Reverse',
				'column' => 'Column',
				'column-reverse' => 'Column Reverse',
			),
			'default' => 'initial',
		),
		'flex-flow' => array(
			'group' => 'Flexbox',
			'name' => 'Flex flow',
			'type' => 'select',
			'options' => array(
				'initial' => 'Initial',
				'row nowrap' => 'row nowrap',
				'row-reverse nowrap' => 'row-reverse nowrap',
				'column nowrap' => 'column nowrap',
				'column-reverse nowrap' => 'column-reverse nowrap',
				'row wrap' => 'row wrap',
				'row-reverse wrap' => 'row-reverse wrap',
				'column wrap' => 'column wrap',
				'column-reverse wrap' => 'column-reverse wrap',
				'row wrap-reverse' => 'row wrap-reverse',
				'row-reverse wrap-reverse' => 'row-reverse wrap-reverse',
				'column wrap-reverse' => 'column wrap-reverse',
				'column-reverse wrap-reverse;' => 'column-reverse wrap-reverse',
			),
			'default' => 'initial',
		),
		'flex-grow' => array(
			'group' => 'Flexbox',
			'name' => 'Flex grow',
			'type' => 'integer',
			'default' => '0',
		),
		'flex-shrink' => array(
			'group' => 'Flexbox',
			'name' => 'Flex shrink',
			'type' => 'integer',
			'default' => '0',
		),
		'flex-wrap' => array(
			'group' => 'Flexbox',
			'name' => 'Flex wrap',
			'type' => 'select',
			'options' => array(
				'initial' => 'Initial',
				'inherit' => 'Inherit',
				'wrap' => 'Wrap',
				'nowrap' => 'Nowrap',
				'wrap-reverse' => 'Wrap reverse',
			),
			'default' => 'initial',
		),
		'justify-content' => array(
			'group' => 'Flexbox',
			'name' => 'Flex direction',
			'type' => 'select',
			'options' => array(
				'initial' => 'Initial',
				'inherit' => 'Inherit',
				'flex-start' => 'Flex Start',
				'flex-end' => 'Flex End',
				'center' => 'Center',
				'space-between' => 'Space Between',
				'space-around' => 'Space Around',
			),
			'default' => 'initial',
		),
		'order' => array(
			'group' => 'Flexbox',
			'name' => 'Order',
			'type' => 'integer',
			'default' => '0',
		),
	);

	/**
	 * Get property
	 *
	 * @param string $property CSS Property.
	 * @return string
	 */
	public static function get_property( $property ) {

		return isset( self::$properties[ $property ] ) ? self::$properties[ $property ] : null;

	}

	/**
	 * Get Properties by group
	 *
	 * @param string $group Group.
	 * @return mixed
	 */
	public static function get_properties_by_group( $group ) {

		// Filter though all of the properties to make sure they are in the selected group.
		$filtered_properties = array_filter(
			self::$properties,
			function( $property ) use ( $group ) {
				return ( $property['group'] === $group );
			}
		);

		if ( ! is_array( $filtered_properties ) || count( $filtered_properties ) === 0 ) {
			return null;
		} else {
			return $filtered_properties;
		}
	}

	/**
	 * Get Properties
	 *
	 * @return array
	 */
	public static function get_properties() {
		return self::$properties;
	}


	/**
	 * Output CSS
	 *
	 * @param string $selector Item selector.
	 * @param array  $properties Item properties.
	 * @return string.
	 */
	public static function output_css( $selector, $properties = array() ) {

		if ( ! isset( $selector ) || false === $selector ) {
			return null;
		}

		if ( ! is_array( $properties ) || count( $properties ) === 0 ) {
			return null;
		}

		$output = '';

		// Animation fix.
		if ( isset( $properties['animation-name'] ) && empty( $properties['animation-duration'] ) ) {
			$properties['animation-duration'] = '1s';
		}

		// Animation fix for when animate.
		if ( isset( $properties['animation-rule'] ) && ( 'always' === $properties['animation-rule'] || 'initial' === $properties['animation-rule'] ) ) {
			$properties['animation-play-state'] = 'running';
			unset( $properties['animation-rule'] );
		}

		if ( isset( $properties['animation-rule'] ) && ( 'when-visible' === $properties['animation-rule'] || 'on-mouse-over' === $properties['animation-rule'] ) ) {
			$properties['animation-play-state'] = 'paused';
			unset( $properties['animation-rule'] );
		}

		// Loop through properties.
		foreach ( $properties as $property_id => $value ) {

			// If the value is an empty string, false, or null, don't attempt to put anything.
			if ( ( ! isset( $value ) || '' === $value || false === $value || null === $value || 'null' === $value || 'DELETE' === $value ) && ( '0' !== $value && 0 !== $value ) ) {
				continue;
			}
			// Look up the property to figure out how to handle it.
			$property = self::get_property( $property_id );

			// If the property does not exist, skip it.
			if ( ! $property ) {
				continue;
			}

			// Dont evaluate transform angle param.
			if ( 'transform-angle' === $property_id ) {
				continue;
			}

			/* Everything's good, inject the selector in if it hasn't already been that way the selector isn't added when an element doesn't have any properties */
			if ( empty( $output ) ) {
				$output .= $selector . ' {' . "\n";
			}

			// If it's a complex property, pass everything through it.
			if ( padma_get( 'complex-property', $property ) && is_callable( padma_get( 'complex-property', $property ) ) ) {
				$output .= call_user_func(
					padma_get( 'complex-property', $property ),
					array(
						'selector' => $selector,
						'property_id' => $property_id,
						'value' => $value,
						'properties' => $properties,
						'property' => $property,
					)
				);

				continue;

			} else if ( padma_get( 'js-property', $property ) ) {
				continue;
			}

			// Format the $value by adding the unit or hex indicator if it's a color.
			if ( padma_get( 'unit', $property ) !== null ) {

				/* we get the unit property value if the unit is customizable */
				if ( is_array( padma_get( 'unit', $property ) ) ) {

					$unit = trim( str_replace( array_merge( range( 0, 9 ), array( '.', '-' ) ), '', $value ) );
					$unit_settings = padma_get( 'unit', $property );

					/* If there's no unit in the string then pull the default */
					if ( ! $unit ) {
						$value = $value . padma_get( 'default', $unit_settings, 'px' );
					}
				} else {
					$value = $value . $property['unit'];
				}
			}

			if ( padma_get( 'type', $property ) === 'color' ) {
				$value = padma_format_color( $value );
			}

			if ( 'image' === padma_get( 'type', $property ) && 'none' !== $value ) {
				$value = 'url( ' . $value . ' )';
			}

			// Transform support.
			if ( padma_get( 'group', $property ) === 'Transform' ) {

				if ( 'scale' === $properties['transform'] || 'scaleX' === $properties['transform'] || 'scaleY' === $properties['transform'] ) {
					$unit = '';
				} elseif ( 'translate' === $properties['transform'] || 'translateX' === $properties['transform'] || 'translateY' === $properties['transform'] ) {
					$unit = 'px';
				} else {
					$unit = 'deg';
				}
				$value = $properties['transform'] . '( ' . $properties['transform-angle'] . $unit . ' )';
			}

			// Filter fix.
			if ( 'filter-value' === $property_id ) {
				continue;
			}

			$output .= $property_id . ': ' . $value . ';' . "\n";

		} //foreach: Regular Properties

		/* Only close if there's actual output */
		if ( ! empty( $output ) ) {
			$output .= '}' . "\n";
		}

		return $output;

	}

	/**
	 * Complex property filter
	 *
	 * @param array $args Params.
	 * @return string
	 */
	public static function complex_property_filter( $args ) {

		$filter = $args['properties']['filter'];
		$value  = $args['properties']['filter-value'];
		$unit   = '%';

		if ( 'blur' === $filter ) {
			$unit = 'px';
		} elseif ( 'hue-rotate' === $filter ) {
			$unit = 'deg';
		}

		return 'filter: ' . $filter . '( ' . $value . $unit . ' );';
	}


	/**
	 * Complex property Font Family
	 *
	 * @param array $args Params.
	 * @return string
	 */
	public static function complex_property_font_family( $args ) {

		extract( $args );

		$font_fragments = explode( '|', $value );

		/* Web Font */
		if ( count( $font_fragments ) >= 2 ) {

			if ( \strpos( $font_fragments[1], ':' ) !== false ) {
				$stack = explode( ':', $font_fragments[1] )[0];
			}else{
				$stack = $font_fragments[1];
			}
		} else {
			/* Traditional Font */
			$stack = PadmaFonts::get_stack( $value );
		}

		return 'font-family: ' . $stack . ';';

	}

	/**
	 * Complex property Shadow
	 *
	 * @param array $args Params.
	 * @return string
	 */
	public static function complex_property_shadow( $args ) {

		extract( $args );

		$shadow_type = ( strpos( $property_id, 'box-shadow' ) !== false ) ? 'box-shadow' : 'text-shadow';

		global $padma_complex_property_check;

		// If the complex property check isn't even set, make it an empty array.
		if ( ! is_array( $padma_complex_property_check ) ) {
			$padma_complex_property_check = array( $shadow_type => array() );
		}

		// Since the complex property is a combination of a bunch of properties, we only want it to output once.
		if ( isset( $padma_complex_property_check[ $shadow_type ][ $selector ] ) && true === $padma_complex_property_check[ $shadow_type ][ $selector ] ) {
			return;
		}

		$padma_complex_property_check[ $shadow_type ][ $selector ] = true;

		if ( ! isset( $properties[ $shadow_type . '-color' ] ) ) {
			return null;
		}

		$shadow_color = padma_format_color( $properties[ $shadow_type . '-color' ] );

		if ( 'transparent' === $shadow_color ) {
			return null;
		}

		$shadow_hoffset = isset( $properties[ $shadow_type . '-horizontal-offset' ] ) ? $properties[ $shadow_type . '-horizontal-offset' ] : '0';
		$shadow_voffset = isset( $properties[ $shadow_type . '-vertical-offset' ] ) ? $properties[ $shadow_type . '-vertical-offset' ] : '0';
		$shadow_blur    = isset( $properties[ $shadow_type . '-blur' ] ) ? $properties[ $shadow_type . '-blur' ] : '0';
		$shadow_spread  = isset( $properties[ $shadow_type . '-spread' ] ) ? $properties[ $shadow_type . '-spread' ] : '0';
		$shadow_inset   = ( padma_get( $shadow_type . '-position', $properties, 'outside' ) == 'inset' ) ? ' inset' : null;

		$shadow_hoffset_unit = '';
		if ( is_numeric( $shadow_hoffset ) ) {
			$shadow_hoffset_unit = 'px';
		}

		$shadow_voffset_unit = '';
		if ( is_numeric( $shadow_voffset ) ) {
			$shadow_voffset_unit = 'px';
		}

		$shadow_blur_unit = '';
		if ( is_numeric( $shadow_blur ) ) {
			$shadow_blur_unit = 'px';
		}

		$shadow_spread_unit = '';
		if ( is_numeric( $shadow_spread ) ) {
			$shadow_spread_unit = 'px';
		}

		if ( 'box-shadow' === $shadow_type ) {
			return $shadow_type . ': ' . $shadow_color . ' ' . $shadow_hoffset . $shadow_hoffset_unit . ' ' . $shadow_voffset . $shadow_voffset_unit . ' ' . $shadow_blur . $shadow_blur_unit . ' ' . $shadow_spread . $shadow_spread_unit . $shadow_inset . ';';
		} else {
			return $shadow_type . ': ' . $shadow_color . ' ' . $shadow_hoffset . $shadow_hoffset_unit . ' ' . $shadow_voffset . $shadow_voffset_unit . ' ' . $shadow_blur . $shadow_blur_unit . $shadow_inset . ';';
		}
	}

	/**
	 * Complex property capitalization
	 *
	 * @param array $args Params.
	 * @return string
	 */
	public static function complex_property_capitalization( $args ) {

		extract( $args );

		$data = '';

		if ( 'none' === $value ) {

			$data .= 'text-transform: none;';
			$data .= 'font-variant: normal;';

		} elseif ( 'small-caps' === $value ) {

			$data .= 'text-transform: none;';
			$data .= 'font-variant: small-caps;';

		} else {

			$data .= 'text-transform: ' . $value . ';';
			$data .= 'font-variant: normal;';

		}

		return $data;

	}

	/**
	 * Complex property font styling
	 *
	 * @param array $args Params.
	 * @return string
	 */
	public static function complex_property_font_styling( $args ) {

		extract( $args );

		$data = '';

		if ( 'normal' === $value ) {

			$data .= 'font-style: normal;';
			$data .= 'font-weight: normal;';

		} elseif ( 'bold' === $value ) {

			$data .= 'font-style: normal;';
			$data .= 'font-weight: bold;';

		} elseif ( 'light' === $value ) {

			$data .= 'font-style: normal;';
			$data .= 'font-weight: lighter;';

		} elseif ( 'italic' === $value ) {

			$data .= 'font-style: italic;';
			$data .= 'font-weight: normal;';

		} elseif ( 'bold-italic' === $value ) {

			$data .= 'font-style: italic;';
			$data .= 'font-weight: bold;';

		}

		return $data;

	}
}
