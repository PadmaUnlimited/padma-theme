<?php

class PadmaOnePageNavBlock extends PadmaBlockAPI {


	public $id;
	public $name;
	public $options_class;
	public $description;
	public $categories;
	public $inline_editable;


	function __construct(){

		$this->id = 'onepagenav';
		$this->name = __('OnePage Navigation','padma');
		$this->options_class = 'PadmaOnePageNavBlockOptions';
		$this->description = __('Create a floating navigation menu.','padma');
		$this->categories 	= array('core','navigation');
		$this->inline_editable = array('block-title', 'block-subtitle');

	}


	function content($block) {

		if( empty($block['settings']['nav-options']) ){
			return;
		}

		$html = '<ul>';
		foreach ($block['settings']['nav-options'] as $key => $link_options) {
			
			$link_content = ( !empty($link_options['link-text']) ) ? $link_options['link-text'] : $link_options['wrapper'];
			$img_alt = $link_options['link-alt'];
			if( !empty( $link_options['link-image'] ) ){				
				$link_content = '<img alt="'.$img_alt.'" src="' . $link_options['link-image'] . '">';
			}
			
			$html .= '<li><a href="#wrapper-' . $link_options['wrapper'] . '">' . $link_content . '</a></li>';
			
		}
		$html .= '</ul>';
		echo $html;
		
	}

	
	public static function dynamic_css($block_id, $block) {

		if ( !$block )
			$block = PadmaBlocksData::get_block($block_id);

		$position = ( isset($block['settings']['position']) ) ? $block['settings']['position'] : 'right';
		$border = ( isset($block['settings']['border']) ) ? $block['settings']['border'] : '50';
		$css = '';
		$attrs = array();


		$attrs['position'] = 'fixed';
		$attrs['top'] = '50%';
		$attrs['z-index'] = '999';
		$attrs['width'] = 'auto';
		
		if( $position == 'right' ){					
			$attrs['right'] = $border . 'px';
		
		}elseif ( $position == 'left' ) {
			$attrs['left'] = $border . 'px';

		}

		// main css
		$css .= '#block-' . $block_id . '{';
		foreach ($attrs as $rule => $value) {
			$css .= $rule . ':' . $value . ';';
		}
		$css .= '}';

		$css .= '#block-' . $block_id . ' img { width: 25px; }';
		return $css;
		

	}


	public static function dynamic_js($block_id, $block) {

		if ( !$block )
			$block = PadmaBlocksData::get_block($block_id);

		$js = '';
		$js .= "jQuery(document).ready(function($) {";
		$js	.= "jQuery(document).on( 'click','#block-". $block_id ." a', function( event ) {";
		$js .= "$('#block-". $block_id." li').removeClass('current');";
		$js .= "$(this).parent().addClass('current')";
		$js .= "});";
		$js .= "});";
		
		return $js;
	}

	function setup_elements() {
		$this->register_block_element(array(
			'id' => 'menu-item',
			'name' => 'Item',
			'selector' => 'ul li',
			'states' => array(
				'Selected' => 'ul li.current',
			)
		));
		$this->register_block_element(array(
			'id' => 'menu-image',
			'name' => 'Image',
			'selector' => 'ul li img',
			'states' => array(
				'Selected' => 'ul li.current img',
			)
		));
	}


}


class PadmaOnePageNavBlockOptions extends PadmaBlockOptionsAPI {

	public $tabs;
	public $inputs;

	function __construct($block_type_object){

		parent::__construct($block_type_object);

		$this->tabs = array(
			'general' => __('General','padma')
		);

		$this->inputs = array(
			'general' => array(
				'position' => array(
					'type' => 'select',
					'name' => 'position',
					'label' => __('Position','padma'),
					'default' => 'right',
					'options' => array(
						'left' => 'Left',
						'right' => 'Right',
					),
				),
				/*
				'style' => array(
					'type' => 'select',
					'name' => 'style',
					'label' => __('Style','padma'),
					'default' => 'style-1',
					'options' => array(
						'style-1' => 'Style 1'
					),
				),*/
				'border' => array(
					'type' => 'integer',
					'name' => 'border',
					'label' => __('Distance from border (px)','padma'),
					'default' => '50',
				),
				'nav-options' => array(
					'type' => 'repeater',
					'name' => 'nav-options',
					'label' => __('OnePage Nav','padma'),
					'default' => '',
					'inputs' => array(
						array(
							'type' => 'select',
							'name' => 'wrapper',
							'label' => __('Wrapper','padma'),
							'options' => 'get_wrappers_lists()',
						),
						array(
							'type' => 'text',
							'name' => 'link-text',
							'label' => __('Custom text','padma'),
						),
						array(
							'type' => 'text',
							'name' => 'link-alt',
							'label' => __('Custom alt','padma'),
						),
						array(
							'type' => 'image',							
							'name' => 'link-image',
							'label' => __('Custom image','padma'),
							'default' => null
						),
					),
				),

			),
		);
	}

	function get_wrappers_lists() {

		$wrappers 	= PadmaWrappersData::get_all_wrappers();
		$options 	= array('' => '&ndash; Select a wrapper &ndash;');

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
			$options[$layout_name][$wrapper_id] = 'Wrapper' . $wrapper_alias . $wrapper_info_str;

		}


		return $options;

	}

}