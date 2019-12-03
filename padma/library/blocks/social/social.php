<?php

class PadmaSocialBlock extends PadmaBlockAPI {

	public $id;
	public $name;
	public $options_class;
	public $fixed_height;
	public $html_tag;
	public $description;
	public $categories;
	protected $show_content_in_grid;


	function __construct(){

		$this->id = 'social';	
		$this->name = 'Social';		
		$this->options_class = 'PadmaSocialBlockOptions';	
		$this->fixed_height = true;	
		$this->html_tag = 'section';
		$this->description = __('Display a set of social icons','padma');
		$this->categories = array('core','social');
		$this->show_content_in_grid = false;

	}


	public function init() {

		add_filter( 'upload_mimes', array($this, 'add_uploader_svg_mime' ));

	}

	public function setup_elements() {

		$this->register_block_element(array(
			'id' => 'icons-wrapper',
			'name' => __('Icons Container','padma'),
			'selector' => 'ul.social-icons '
		));

		$this->register_block_element(array(
			'id' => 'icon',
			'name' => __('Icon Container','padma'),
			'selector' => 'li'
		));

		$this->register_block_element(array(
			'id' => 'icon-first',
			'name' => __('First Icon','padma'),
			'selector' => 'li:first-child'
		));

		$this->register_block_element(array(
			'id' => 'icon-last',
			'name' => __('Last Icon','padma'),
			'selector' => 'li:last-child'
		));

		$this->register_block_element(array(
			'id' => 'image',
			'name' => __('Image','padma'),
			'selector' => 'img'
		));

		$this->register_block_element(array(
			'id' => 'image-link',
			'name' => __('Image Link','padma'),
			'selector' => 'img a',
			'states' => array(
				'Hover' => 'img a:hover',
				'Clicked' => 'img a:active'
			)
		));

	}


	public static function dynamic_css($block_id, $block = false) {

		if ( !$block )
			$block = PadmaBlocksData::get_block($block_id);

		$position = parent::get_setting($block, 'icons-position', '');
		$orientation = parent::get_setting($block, 'orientation', 'vertical');

		$css = '';

		/* Stack vertical add only bottom margin */
	  	if ( $orientation === 'vertical' ) {

	  		$css .= '
	  			#block-' . $block_id . ' ul.social-icons li { 
	  				margin-bottom: '. parent::get_setting($block, 'vertical-spacing', '10') .'px
	  			}

	  			#block-' . $block_id . ' ul.social-icons li:last-child { 
	  				margin-bottom: 0;
	  			}
	  		';


	  	}

		/* Float horizontal images and add right margin on all but last*/
		if ( $orientation === 'horizontal' ) {

	  		$css .= '
	  			#block-' . $block_id . ' ul.social-icons li {
	  			    display: inline-block;
	  				margin-right: '. parent::get_setting($block, 'horizontal-spacing', '10') .'px
	  			}

	  			#block-' . $block_id . ' ul.social-icons li:last-child { 
	  				margin-right: 0;
	  			}
	  		';

	  	}


		if ( $position ) {

	    $position_fragments = explode('_', $position);

           $horizontal_position = $position_fragments[1];
           $vertical_position = str_replace('center', 'middle', $position_fragments[0]);

	    $css .= '
	        #block-' . $block_id . ' div.social-icons-container {
	            display: table;
	            width: 100%;
	            height: 100%;
	        }

               #block-' . $block_id . ' ul.social-icons {
                   display: table-cell;
                   text-align: ' . $horizontal_position . ';
                   vertical-align: ' . $vertical_position . ';
               }
           ';

       }

		return $css;

	}

	public function content($block) {

		$icon_set 	= PadmaBlockAPI::get_setting($block, 'icon-set', 'peel-icons');
		$use_svg 	= parent::get_setting($block, 'use-svg', false);
		$svg_width 	= ($use_svg && parent::get_setting($block, 'svg-width')) ? 'width="' . parent::get_setting($block, 'svg-width') . '"' : '';

		if ($icon_set == 'custom') {
			$icons = parent::get_setting($block, 'icons' , array());
		} else {
			$icons = parent::get_setting($block, 'icons'.$icon_set , array());
		}

		$block_width 	= PadmaBlocksData::get_block_width($block);
		$block_height 	= PadmaBlocksData::get_block_height($block);			
		$has_icons 		= false;

		foreach ( $icons as $icon ) {

			if ( padma_get('image', $icon) || padma_get('network', $icon) ) {
				$has_icons = true;
				break;
			}

		}

		if ( !$has_icons) {

			echo '<div class="alert alert-yellow"><p>' . __('There are no icons to display.','padma') . '</p></div>';

			return;

		}


		echo '<div class="social-icons-container">';
		echo '<ul class="social-icons">';

			$i = 0;
		  	foreach ( $icons as $icon ) {

		  		if ( !padma_get('image', $icon) && !padma_get('network', $icon) )
		  			continue;

		  		if ($icon_set == 'custom') {
		  			$img_url = $icon['image'];
		  		} else {
		  			$img_url = padma_url().'/library/blocks/social/icons/' . $icon_set . '/' . padma_fix_data_type(padma_get('network', $icon));
		  		}

		  		$i++;
		  		$output = array(
		  			'image' => array(
		  				'src' => $img_url,
		  				'alt' => padma_fix_data_type(padma_get('image-alt', $icon, false)) ? ' alt="' . padma_fix_data_type(padma_get('image-alt', $icon, false)) . '"' : null,
		  				'title' => padma_fix_data_type(padma_get('image-title', $icon)) ? ' title="' . padma_fix_data_type(padma_get('image-title', $icon)) . '"' : null,
		  			),

		  			'hyperlink' => array(
		  				'href' => padma_fix_data_type(padma_get('link-url', $icon)),
		  				'alt' => padma_fix_data_type(padma_get('link-alt', $icon, false)) ? ' alt="' . padma_fix_data_type(padma_get('link-alt', $icon, false)) . '"' : null,
		  				'target' => padma_fix_data_type(padma_get('link-target', $icon, false)) ? ' target="_blank"' : null
		  			)
		  		);

		  			echo '<li>';

		  			/* Open hyperlink if user added one for image */
		  			if ( $output['hyperlink']['href'] )
		  				echo '<a href="' . $output['hyperlink']['href'] . '"' . $output['hyperlink']['target'] . $output['hyperlink']['alt'] . '>';

				  			/* Don't forget to display the ACTUAL IMAGE */
				  			echo '<img src="' . $output['image']['src'] . '"' . $output['image']['alt'] . $output['image']['title'] . ' class="img-' . $i . '" ' . $svg_width . ' />';

		  			/* Closing tag for hyperlink */
		  			if ( $output['hyperlink']['href'] )
		  				echo '</a>';

		  			echo '</li>';

		  	}

	  	echo '</ul>';
		echo '</div>';

	}

	public function add_uploader_svg_mime( $mimes ){
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

}

class PadmaSocialBlockOptions extends PadmaBlockOptionsAPI {

	public $tabs;
	public $inputs;


	function __construct($block_type_object){

		parent::__construct($block_type_object);

		$this->tabs = array(
			'general' => 'General',
			'custom-icons-set' => __('Custom Icons','padma')
		);

		$this->inputs = array(
			'general' => array(

				'icon-set' => array(
					'type' => 'select',
					'name' => 'icon-set',
					'label' => __('Icon Set','padma'),
					'default' => 'peel-icons',
					'options' => 'get_icon_sets()',
					'tooltip' => __('Select custom to add your own icons or select one of these sets','padma'),
					'toggle'    => array(
						'custom' => array(
							'hide' => array(
								'li[id*="-set"]:not(#sub-tab-custom-icons-set)'
							),
							'show' => array(
								'li#sub-tab-custom-icons-set'
							)
						),
						'peel-icons' => array(
							'hide' => array(
								'li[id*="-set"]:not(#sub-tab-peel-icons-set)'
							),
							'show' => array(
								'li#sub-tab-peel-icons-set'
							)
						),
						'soft-social' => array(
							'hide' => array(
								'li[id*="-set"]:not(#sub-tab-soft-social-set)'
							),
							'show' => array(
								'li#sub-tab-soft-social-set'
							)
						)
					),
					'callback' => '
						reloadBlockOptions()'

				),

				'layout-heading' => array(
					'name' => 'layout-heading',
					'type' => 'heading',
					'label' => __('Layout','padma'),
					'tooltip' => __('Set the position of all icons in the block and the orientation before you add your icons.','padma')
				),

				'icons-position' => array(
					'name' => 'icons-position',
					'label' => __('Position icons inside container','padma'),
					'type' => 'select',
					'tooltip' => __('You can position the social icons in relation to the block using the positions provided','padma'),
					'default' => 'none',
					'options' => array(
						'' => 'None',
						'top_left' => __('Top Left','padma'),
						'top_center' => __('Top Center','padma'),
						'top_right' => __('Top Right','padma'),
						'center_left' => __('Center Left','padma'),
						'center_center' => __('Center Center','padma'),
						'center_right' => __('Center Right','padma'),
						'bottom_left' => __('Bottom Left','padma'),
						'bottom_center' => __('Bottom Center','padma'),
						'bottom_right' => __('Bottom Right','padma')
					)
				),

				'orientation' => array(
					'type' => 'select',
					'name' => 'orientation',
					'label' => __('Orientation','padma'),
					'tooltip' => '',
					'options' => array(
						'vertical' => 'Vertical',
						'horizontal' => 'Horizontal'
					),
					'toggle'    => array(
						'vertical' => array(
							'show' => array(
								'#input-vertical-spacing'
							),
							'hide' => array(
								'#input-horizontal-spacing'
							),
						),
						'horizontal' => array(
							'show' => array(
								'#input-horizontal-spacing'
							),
							'hide' => array(
								'#input-vertical-spacing'
							),
						)
					),
					'tooltip' => __('Display articles on top of each other (vertical) or side by side as a grid (horizontal)','padma')
				),

				'horizontal-spacing' => array(
					'type' => 'text',
					'name' => 'horizontal-spacing',
					'label' => __('Horizontal Spacing','padma'),
					'default' => '10',
					'unit' => 'px',
					'tooltip' => __('Set the px horizontal spacing between the icons.','padma')
				),

				'vertical-spacing' => array(
					'type' => 'text',
					'name' => 'vertical-spacing',
					'label' => __('Vertical Spacing','padma'),
					'default' => '10',
					'unit' => 'px',
					'tooltip' => __('Set the px vertical spacing between the icons.','padma')
				),

				'svg-heading' => array(
					'name' => 'svg-heading',
					'type' => 'heading',
					'label' => __('SVG Images','padma'),
					'tooltip' => __('Allows you to upload SVG Images. Many icons come with SVG versions of the icons. Using an SVG means it is easier to size the icons. With images like .png and .gif you need to manually size them in a graphics program.','padma')
				),

				'use-svg' => array(
					'name' => 'use-svg',
					'label' => __('Use SVG?','padma'),
					'type' => 'checkbox',
					'tooltip' => __('If you would like to upload SVG images check this option','padma'),
					'default' => false,
					'toggle'    => array(
						'true' => array(
							'show' => array(
								'#input-svg-width',
								'#input-svg-height'
							)
						),
						'false' => array(
							'hide' => array(
								'#input-svg-width',
								'#input-svg-height'
							)
						)
					),
				),

				'svg-width' => array(
					'type' => 'text',
					'name' => 'svg-width',
					'label' => __('SVG Image Width','padma'),
					'tooltip' => __('Set the width of all SVG\'s in the block. This also controls the width with a 1:1 ratio','padma')
				)

			),

			'custom-icons-set' => array(
				'icons' => array(
					'type' => 'repeater',
					'name' => 'icons',
					'label' => __('Icons','padma'),
					'inputs' => array(
						array(
							'type' => 'image',
							'name' => 'image',
							'label' => __('Image','padma'),
							'default' => null
						),

						array(
							'type' => 'text',
							'name' => 'image-title',
							'label' => '"title"',
							'tooltip' => __('This will be used as the "title" attribute for the image.  The title attribute is beneficial for SEO (Search Engine Optimization) and will allow your visitors to move their mouse over the image and read about it.','padma')
						),

						array(
							'type' => 'text',
							'name' => 'image-alt',
							'label' => '"alt"',
							'tooltip' => __('This will be used as the "alt" attribute for the image.  The alt attribute is <em>hugely</em> beneficial for SEO (Search Engine Optimization) and for general accessibility.','padma')
						),

						array(
							'name' => 'link-heading',
							'type' => 'heading',
							'label' => __('Link Image','padma')
						),

						array(
							'name' => 'link-url',
							'label' => __('Link URL?','padma'),
							'type' => 'text',
							'tooltip' => __('Set the URL for the image to link to','padma')
						),

						array(
							'name' => 'link-alt',
							'label' => '"alt"',
							'type' => 'text',
							'tooltip' => __('Set alternative text for the link','padma')
						),

						array(
							'name' => 'link-target',
							'label' => __('New window?','padma'),
							'type' => 'checkbox',
							'tooltip' => __('If you would like to open the link in a new window check this option','padma'),
							'default' => false,
						)

					),
					'tooltip' => __('Upload the images that you would like to add to the image block.','padma'),
					'sortable' => true,
					'limit' => false
				),
			),
		);

	}

	public function modify_arguments($args = false) {

		foreach ( self::get_icon_sets() as $icon_set => $icon_set_name ) {

			if ( $icon_set == 'custom' )
				continue;

			$this->tabs[$icon_set . '-set'] = ucwords(str_replace('-', ' ', $icon_set));

			$this->inputs[$icon_set . '-set'] = array(
				'icons'.$icon_set => array(
					'type' => 'repeater',
					'name' => 'icons' . $icon_set,
					'label' => __('Icons','padma'),
					'inputs' => array(
						array(
							'type' => 'select',
							'name' => 'network',
							'label' => __('Network','padma'),
							'default' => null,
							'options' => self::get_icons( $icon_set )
						),

						array(
							'type' => 'text',
							'name' => 'image-title',
							'label' => '"title"',
							'tooltip' => __('This will be used as the "title" attribute for the image.  The title attribute is beneficial for SEO (Search Engine Optimization) and will allow your visitors to move their mouse over the image and read about it.','padma')
						),

						array(
							'type' => 'text',
							'name' => 'image-alt',
							'label' => '"alt"',
							'tooltip' => __('This will be used as the "alt" attribute for the image.  The alt attribute is <em>hugely</em> beneficial for SEO (Search Engine Optimization) and for general accessibility.','padma')
						),

						array(
							'name' => 'link-heading',
							'type' => 'heading',
							'label' => __('Link Image','padma')
						),

						array(
							'name' => 'link-url',
							'label' => __('Link URL?','padma'),
							'type' => 'text',
							'tooltip' => __('Set the URL for the image to link to','padma')
						),

						array(
							'name' => 'link-alt',
							'label' => '"alt"',
							'type' => 'text',
							'tooltip' => __('Set alternative text for the link','padma')
						),

						array(
							'name' => 'link-target',
							'label' => __('New window?','padma'),
							'type' => 'checkbox',
							'tooltip' => __('If you would like to open the link in a new window check this option','padma'),
							'default' => false,
						)

					),
					'tooltip' => __('Upload the images that you would like to add to the image block.','padma'),
					'sortable' => true,
					'limit' => false
				)
			);

		}

	}

	public static function get_icon_sets() {

		$path 			= PADMA_LIBRARY_DIR.'/blocks/social/icons';
		$results 		= scandir($path);
		$icons_options 	= array();

		foreach ($results as $result) {

		    if ( $result === '.' || $result === '..' || $result === '.DS_Store') {
			    continue;
		    }

		    if ( is_dir($path . '/' . $result) ) {
		        $icons_options[$result] = ucwords(str_replace('-', ' ', $result));
		    }

		}

		$icons_options['custom'] = __('Custom Icons','padma');

		return $icons_options;

	}

	public static function get_icons( $icon_set ) {

		if ( $icon_set != 'custom' ) {

			$path = PADMA_LIBRARY_DIR.'/blocks/social/icons/' . $icon_set . '/';

			$results = scandir($path);

			$icons = array();

			foreach ($results as $result) {
		    	if ($result === '.' or $result === '..' or $result === '.DS_Store') continue;

			    if (!is_dir($path . '/' . $result)) {

			        $icons[$result] = preg_replace("/\\.[^.\\s]{3,4}$/", "", $result);

			    }
			}

			return $icons;

		}
	}

}