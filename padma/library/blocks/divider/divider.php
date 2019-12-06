<?php

class PadmaDividerBlock extends PadmaBlockAPI {


	public $id;
	public $name;
	public $options_class;
	public $description;
	public $categories;
	public $inline_editable;


	function __construct(){

		$this->id = 'divider';
		$this->name = __('Divider','padma');
		$this->options_class = 'PadmaDividerBlockOptions';
		$this->description = __('Implement a design that makes use of well-divided sections.','padma');
		$this->categories 	= array('core');
		$this->inline_editable = array('block-title', 'block-subtitle');

	}


	function content($block) {

		$style =  ( isset($block['settings']['style']) ) ? $block['settings']['style'] : 'style-1';
		echo '<div class="divider-'.$style.'"></div>';
	}

	
	public static function dynamic_css($block_id, $block) {

		if ( !$block )
			$block = PadmaBlocksData::get_block($block_id);

		$style =  ( isset($block['settings']['style']) ) ? $block['settings']['style'] : 'style-1';
		$height =  ( isset($block['settings']['height']) ) ? $block['settings']['height'] : '100';
		$main_color =  ( isset($block['settings']['main-color']) ) ? $block['settings']['main-color'] : '#c62040';
		$sec_color =  ( isset($block['settings']['secondary-color']) ) ? $block['settings']['secondary-color'] : '#73a6c0';
		$bg_color =  ( isset($block['settings']['bg-color']) ) ? $block['settings']['bg-color'] : '#fff';

		switch ($style) {

			case 'style-1':
				$attrs['width'] = '100%';
				$attrs['height'] = $height . 'px';
				$attrs['position'] = 'absolute';
				$attrs['left'] = '0px';
				$attrs['background'] = 'linear-gradient(to left bottom, ' . $bg_color . ' 49%, ' . $main_color . ' 50%);';				
				break;

			case 'style-2':
				$attrs['width'] = '100%';
				$attrs['height'] = $height . 'px';
				$attrs['position'] = 'absolute';
				$attrs['left'] = '0px';
				$attrs['background'] = 'linear-gradient(to right bottom, ' . $bg_color . ' 49%, ' . $main_color . ' 50%);';				
				break;

			case 'style-3':
				$attrs['width'] = '100%';
				$attrs['height'] = $height . 'px';
				$attrs['position'] = 'absolute';
				$attrs['left'] = '0px';
				$attrs['background'] = 'linear-gradient(to left top, ' . $bg_color . ' 49%, ' . $main_color . ' 50%);';				
				break;

			case 'style-4':
				$attrs['width'] = '100%';
				$attrs['height'] = $height . 'px';
				$attrs['position'] = 'absolute';
				$attrs['left'] = '0px';
				$attrs['background'] = 'linear-gradient(to right top, ' . $bg_color . ' 49%, ' . $main_color . ' 50%);';				
				break;

			case 'style-5':

				$attrs['height'] = $height . 'px';

				$attrs['before']['content'] = '""';
				$attrs['before']['position'] = 'absolute';
				$attrs['before']['left'] = '0px';
				$attrs['before']['width'] = '50%';
				$attrs['before']['height'] = $height . 'px';
				$attrs['before']['background'] ='linear-gradient(to left bottom, ' . $main_color . ' 49%, ' . $bg_color . ' 50%);';
				
				$attrs['after']['content'] = '""';
				$attrs['after']['position'] = 'absolute';
				$attrs['after']['right'] = '0px';
				$attrs['after']['width'] = '50%';
				$attrs['after']['height'] = $height . 'px';
				$attrs['after']['background'] ='linear-gradient(to right bottom, ' . $main_color . ' 49%, ' . $bg_color . ' 50%);';
				break;

			case 'style-6':
				$attrs['height'] = $height . 'px';

				$attrs['before']['content'] = '""';
				$attrs['before']['position'] = 'absolute';
				$attrs['before']['left'] = '0px';
				$attrs['before']['width'] = '50%';
				$attrs['before']['height'] = $height . 'px';
				$attrs['before']['background'] ='linear-gradient(to right bottom, ' . $bg_color . ' 49%, ' . $main_color . ' 50%);';
				
				$attrs['after']['content'] = '""';
				$attrs['after']['position'] = 'absolute';
				$attrs['after']['right'] = '0px';
				$attrs['after']['width'] = '50%';
				$attrs['after']['height'] = $height . 'px';
				$attrs['after']['background'] ='linear-gradient(to left bottom, ' . $bg_color . ' 49%, ' . $main_color . ' 50%);';
				break;	

			case 'style-7':			

				$attrs['before']['pointer-events'] = 'none';
				$attrs['before']['position'] = 'absolute';
				$attrs['before']['content'] = '""';
				$attrs['before']['left'] = '50%';
				$attrs['before']['width'] = '100px';
				$attrs['before']['height'] = $height . 'px';
				$attrs['before']['-webkit-transform'] = 'translateX(-50%) rotate(45deg);';
				$attrs['before']['transform'] = 'translateX(-50%) rotate(45deg);';
				$attrs['before']['top'] = '-50px';				
				$attrs['before']['background'] = $bg_color;

				$attrs['after']['pointer-events'] = 'none';
				$attrs['after']['position'] = 'absolute';
				$attrs['after']['content'] = '""';
				$attrs['after']['left'] = '50%';
				$attrs['after']['width'] = '100px';
				$attrs['after']['height'] = $height . 'px';
				$attrs['after']['-webkit-transform'] = 'translateX(-50%) rotate(45deg);';
				$attrs['after']['transform'] = 'translateX(-50%) rotate(45deg);';
				$attrs['after']['bottom'] = '-50px';				
				$attrs['after']['background'] = 'inherit';
				break;	

			case 'style-8':

				$attrs['z-index'] = '1';
				$attrs['padding-top'] = $height . 'px';
				$attrs['background'] = $bg_color;
				$attrs['position'] = 'relative';
				$attrs['overflow'] = 'hidden';

				$attrs['before']['pointer-events'] = 'none';
				$attrs['before']['position'] = 'absolute';
				$attrs['before']['content'] = '""';
				$attrs['before']['top'] = $height . 'px';
				$attrs['before']['left'] = '-25%';
				$attrs['before']['z-index'] = '-1';
				$attrs['before']['width'] = '150%';				
				$attrs['before']['height'] = '50%';
				$attrs['before']['background'] = $main_color;				
				$attrs['before']['content'] = '""';
				$attrs['before']['-webkit-transform'] = 'rotate(-3deg)';
				$attrs['before']['transform'] = 'rotate(-3deg)';
				$attrs['before']['-webkit-transform-origin'] = '3% 0';
				$attrs['before']['transform-origin'] = '3% 0';

				$attrs['after']['pointer-events'] = 'none';
				$attrs['after']['position'] = 'absolute';
				$attrs['after']['content'] = '""';
				$attrs['after']['top'] = $height . 'px';
				$attrs['after']['left'] = '-25%';
				$attrs['after']['z-index'] = '-1';
				$attrs['after']['width'] = '150%';				
				$attrs['after']['height'] = '75%';
				$attrs['after']['background'] = $sec_color;				
				$attrs['after']['content'] = '""';
				$attrs['after']['-webkit-transform'] = 'rotate(-2deg)';
				$attrs['after']['transform'] = 'rotate(-2deg)';
				$attrs['after']['-webkit-transform-origin'] = '0 0';
				$attrs['after']['transform-origin'] = '0 0';
				break;

			case 'style-9':

				$attrs['z-index'] = '1';
				$attrs['padding-top'] = $height . 'px';
				$attrs['background'] = $bg_color;
				$attrs['position'] = 'relative';
				$attrs['overflow'] = 'hidden';

				$attrs['before']['pointer-events'] = 'none';
				$attrs['before']['position'] = 'absolute';
				$attrs['before']['content'] = '""';
				$attrs['before']['top'] = $height . 'px';
				$attrs['before']['right'] = '-25%';
				$attrs['before']['z-index'] = '-1';
				$attrs['before']['width'] = '150%';				
				$attrs['before']['height'] = '50%';
				$attrs['before']['background'] = $main_color;				
				$attrs['before']['content'] = '""';
				$attrs['before']['-webkit-transform'] = 'rotate(3deg)';
				$attrs['before']['transform'] = 'rotate(3deg)';
				$attrs['before']['-webkit-transform-origin'] = '97% 0';
				$attrs['before']['transform-origin'] = '97% 0';

				$attrs['after']['pointer-events'] = 'none';
				$attrs['after']['position'] = 'absolute';
				$attrs['after']['content'] = '""';
				$attrs['after']['top'] = $height . 'px';
				$attrs['after']['right'] = '-25%';
				$attrs['after']['z-index'] = '-1';
				$attrs['after']['width'] = '150%';				
				$attrs['after']['height'] = '75%';
				$attrs['after']['background'] = $sec_color;				
				$attrs['after']['content'] = '""';
				$attrs['after']['-webkit-transform'] = 'rotate(2deg)';
				$attrs['after']['transform'] = 'rotate(2deg)';
				$attrs['after']['-webkit-transform-origin'] = '100% 0';
				$attrs['after']['transform-origin'] = '100% 0';
				break;
			
			default:
				# code...
				break;
		}


		$css = '';
		$css_before = '';
		$css_after = '';

		$css .= '#block-' . $block_id . ' .block-content{position: relative;}';
		$css .= '#block-' . $block_id . ' .divider-' . $style . '{';

		// main css
		foreach ($attrs as $rule => $value) {

			if( $rule == 'before' || $rule == 'after' )
				continue;

			$css .= $rule . ':' . $value . ';';

		}

		if( isset($attrs['before']) && is_array($attrs['before']) ){

			$css_before = '#block-' . $block_id . ' .divider-' . $style . ':before{';
			foreach ($attrs['before'] as $before_rule => $before_value) {
				$css_before .= $before_rule . ':' . $before_value . ';';	
			}
			$css_before .= '}';

		}

		if( isset($attrs['after']) && is_array($attrs['after']) ){

			$css_after = '#block-' . $block_id . ' .divider-' . $style . ':after{';
			foreach ($attrs['after'] as $after_rule => $after_value) {
				$css_after .= $after_rule . ':' . $after_value . ';';	
			}
			$css_after .= '}';

		}
	
		$css .= '}';
		$css .= $css_before;
		$css .= $css_after;

		return $css;
	}

	function setup_elements() {
	}


}


class PadmaDividerBlockOptions extends PadmaBlockOptionsAPI {

	public $tabs;
	public $inputs;

	function __construct($block_type_object){

		parent::__construct($block_type_object);

		$this->tabs = array(
			'general' => __('General','padma')
		);

		$this->inputs = array(
			'general' => array(
				'style' => array(
					'type' => 'select',
					'name' => 'style',
					'label' => __('Divider style','padma'),
					'default' => 'style-1',
					'options' => array(
						'style-1' => 'Style 1 - Diagonal left to bottom',
						'style-2' => 'Style 2 - Diagonal right to bottom',
						'style-3' => 'Style 3 - Diagonal left to top',
						'style-4' => 'Style 4 - Diagonal right to top',
						'style-5' => 'Style 5 - Arrow to down',
						'style-6' => 'Style 6 - Arrow to up',
						'style-7' => 'Style 7 - Triangles',
						'style-8' => 'Style 8 - Double Diagonal to right',
						'style-9' => 'Style 9 - Double Diagonal to left',
					),
					'toggle' => array(
						'style-1' => array(
							'hide' => array(
								'#input-secondary-color',
							)
						),
						'style-2' => array(
							'hide' => array(
								'#input-secondary-color',
							)
						),
						'style-3' => array(
							'hide' => array(
								'#input-secondary-color',
							)
						),
						'style-4' => array(
							'hide' => array(
								'#input-secondary-color',
							)
						),
						'style-5' => array(
							'hide' => array(
								'#input-secondary-color',
							)
						),
						'style-6' => array(
							'hide' => array(
								'#input-secondary-color',
							)
						),
						'style-7' => array(
							'hide' => array(
								'#input-secondary-color',
							)
						),
						'style-8' => array(
							'show' => array(
								'#input-secondary-color',
							)
						),
					)
				),
				'height' => array(
					'type' => 'integer',
					'name' => 'height',
					'label' => __('Divider height','padma'),
					'default' => '100',					
				),
				'main-color' => array(
					'type' => 'colorpicker',
					'name' => 'main-color',
					'label' => __('Main color','padma'),
					'default' => '#c62040',
				),
				'secondary-color' => array(
					'type' => 'colorpicker',
					'name' => 'secondary-color',
					'label' => __('Secondary color','padma'),
					'default' => '#73a6c0',
				),
				'bg-color' => array(
					'type' => 'colorpicker',
					'name' => 'bg-color',
					'label' => __('background color','padma'),
					'default' => '#fff',
				),
			)
		);
	}

}