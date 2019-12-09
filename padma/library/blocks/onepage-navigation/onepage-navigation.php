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
		$this->name = __('OnePageNav','padma');
		$this->options_class = 'PadmaOnePageNavBlockOptions';
		$this->description = __('','padma');
		$this->categories 	= array('core','navigation');
		$this->inline_editable = array('block-title', 'block-subtitle');

	}


	function content($block) {

		$html = '<ul>';
		foreach ($block['settings']['nav-options'] as $key => $link_options) {
			
			$link_content = ( !empty($link_options['link-text']) ) ? $link_options['link-text'] : $link_options['wrapper'];
			if( !empty( $link_options['link-image'] ) ){
				$link_content = '<img src="' . $link_options['link-image'] . '">';
			}
			


			$html .= '<li><a href="#wrapper-' . $link_options['wrapper'] . '">' . $link_content . '</a></li>';
			
		}
		$html .= '</ul>';
		echo $html;
		
	}

	
	public static function dynamic_css($block_id, $block) {

		if ( !$block )
			$block = PadmaBlocksData::get_block($block_id);

		debug($block);

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
		return $css;
		

	}

	function setup_elements() {
		$this->register_block_element(array(
			'id' => 'menu-item',
			'name' => 'Item',
			'selector' => 'ul li',
		));
		$this->register_block_element(array(
			'id' => 'menu-image',
			'name' => 'Image',
			'selector' => 'ul li img',
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
				'style' => array(
					'type' => 'select',
					'name' => 'style',
					'label' => __('Style','padma'),
					'default' => 'style-1',
					'options' => array(
						'style-1' => 'Style 1'
					),
				),
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
		$current_layout_in_use 	= PadmaLayout::get_current_in_use();
		$wrappers_lists = array();

		foreach ($wrappers as $wrappers_id => $wrappers_data) {

			if( $wrappers_data['layout'] !== $current_layout_in_use )
				continue;

			$wrappers_lists[$wrappers_id] = ( isset($wrappers_data['settings']['alias']) ) ? $wrappers_data['settings']['alias'] : $wrappers_id;
			
		}
		return $wrappers_lists;
		
	}

}