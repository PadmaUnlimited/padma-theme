<?php
class BloxDynamicStyle {
	
	
	static function design_editor() {

		/* Action used for registering elements */
		do_action('blox_dynamic_style_design_editor_init');
		
		$elements = BloxElementsData::get_all_elements();
		
		$return = "/* DESIGN EDITOR STYLING */\n";
		
		foreach ( $elements as $element_id => $element_options ) {
			
			$element = BloxElementAPI::get_element($element_id);
			$selector = $element['selector'];
			$nudging_properties = array('top', 'left', 'position', 'z-index');
			
			//Continue to next element if the element/selector does not exist
			if ( !isset($selector) || $selector == false )
				continue;
			
			/* Regular Element */
			if ( isset($element_options['properties']) ) 
				$return .= BloxElementProperties::output_css($selector, self::filter_nudging_properties($element_options['properties'], $element));
			
			/* Layout-specific elements */
			if ( isset($element_options['special-element-layout']) && is_array($element_options['special-element-layout']) ) {
				
				//Handle every layout
				foreach ( $element_options['special-element-layout'] as $layout => $layout_properties ) {

					if ( BloxLayout::is_customized($layout) ) {
						$selector_prefix = 'body.layout-using-' . str_replace( BloxLayout::$sep, '-', $layout ) . ' ';
					} else {
						$selector_prefix = 'body.layout-' . str_replace( BloxLayout::$sep, '-', $layout ) . ' ';
					}

					$selector_array = explode(',', $selector);
					
					foreach ( $selector_array as $selector_index => $selector )
						$selector_array[$selector_index] = $selector_prefix . trim($selector);
											
					$layout_element_selector = implode(',', $selector_array);
					
					//Since the layout selectors are targeted by the body element, we can't do anything body to style the actual body element.  Let's fix that.
					if ( $selector == 'body' )
						$layout_element_selector = str_replace(' body', '', $layout_element_selector); //The space inside str_replace is completely intentional.
					
					$return .= BloxElementProperties::output_css($layout_element_selector, self::filter_nudging_properties($layout_properties, $element));
					
				}
				
			}
			
			/* Instances */
			if ( isset($element_options['special-element-instance']) && is_array($element_options['special-element-instance']) ) {
				
				//Handle every instance
				foreach ( $element_options['special-element-instance'] as $instance => $instance_properties ) {
					
					//Make sure the instance exists
					if ( !isset($element['instances'][$instance]) )
						continue;
					
					//Get the selector for the instance
					$instance_selector = $element['instances'][$instance]['selector'];
					
					$return .= BloxElementProperties::output_css($instance_selector, self::filter_nudging_properties($instance_properties, $element));
					
				}
				
			}

			/* States */
			if ( isset($element_options['special-element-state']) && is_array($element_options['special-element-state']) ) {
				
				//Handle every instance
				foreach ( $element_options['special-element-state'] as $state => $state_properties ) {
					
					//Make sure the state exists
					if ( !isset($element['states'][$state]) )
						continue;
					
					//Get the selector for the layout
					$state_info = $element['states'][$state];

					$return .= BloxElementProperties::output_css($state_info['selector'], self::filter_nudging_properties($state_properties, $element));
					
				}
				
			}

		} //End main $elements foreach
		
		return $return;
		
	}



		private static function filter_nudging_properties($properties, $element) {

			if ( !isset($element['disallow-nudging']) || !$element['disallow-nudging'] )
				return $properties;

			/* If nudging is disallowed (e.g. sub menu element or body element), then do not even output the CSS */
			foreach ( array('top', 'left', 'position', 'z-index') as $blocked_nudging_property )
				unset($properties[$blocked_nudging_property]);

			return $properties;

		}


	static function wrapper() {

		$layout_id = blox_get('layout-in-use');
		$wrappers = BloxWrappersData::get_wrappers_by_layout($layout_id);

		$return = '';

		/* Default Wrapper Margins */
			if ( blox_get('file') == 've-iframe-grid-dynamic' && blox_get('visual-editor-open') ) {

				$return .= BloxElementProperties::output_css('div.wrapper', array(
					'margin-top' => BloxElementsData::get_property('wrapper', 'margin-top', BloxWrappers::$default_wrapper_margin_top, 'structure'),
					'margin-bottom' => BloxElementsData::get_property('wrapper', 'margin-bottom', BloxWrappers::$default_wrapper_margin_bottom, 'structure'),
					'padding-top' => BloxElementsData::get_property('wrapper', 'padding-top', null, 'structure'),
					'padding-right' => BloxElementsData::get_property('wrapper', 'padding-right', null, 'structure'),
					'padding-bottom' => BloxElementsData::get_property('wrapper', 'padding-bottom', null, 'structure'),
					'padding-left' => BloxElementsData::get_property('wrapper', 'padding-left', null, 'structure')
				));

			}

		/* Wrappers for Layout */
		foreach ( $wrappers as $wrapper_id => $wrapper ) {

			$wrapper_settings = blox_get('settings', $wrapper, array());

			if ( $mirrored_wrapper = BloxWrappersData::get_wrapper_mirror( $wrapper ) ) {
				$wrapper_settings = $mirrored_wrapper['settings'];
			}

			$wrapper_grid_width = BloxWrappers::get_grid_width($wrapper);

			$wrapper_id    = BloxWrappersData::get_legacy_id( $wrapper );
			$wrapper['original-id'] = $wrapper['id'];
			$wrapper['id'] = BloxWrappersData::get_legacy_id( $wrapper );

			/* Set up variables for wrapper */
			if ( blox_get('file') == 've-iframe-grid-dynamic' && blox_get('visual-editor-open') ) {
				$wrapper_selector = 'div#wrapper-' . BloxWrappers::format_wrapper_id($wrapper['original-id']);
			} else {
				$wrapper_selector = 'div#wrapper-' . BloxWrappers::format_wrapper_id($wrapper_id);
			}

			/* Fixed Wrapper */
				if ( !blox_get('fluid', $wrapper_settings, false, true) ) {

					/* Wrapper */
						$return .= $wrapper_selector . ' {
							width: ' . $wrapper_grid_width . 'px;
						}';

						if ( BloxResponsiveGrid::is_enabled() ) {

							$return .= $wrapper_selector . '.responsive-grid {
								width: auto;
								max-width: ' . $wrapper_grid_width . 'px;
							}';

						}

					/* Grid */
						if ( blox_get('file') != 've-iframe-grid-dynamic' || !blox_get('visual-editor-open') )
							$return .= BloxResponsiveGrid::is_enabled() ? self::responsive_grid($wrapper) : self::fixed_grid($wrapper);

			/* Fluid Wrapper */
				} else {

					/* Grid Container */
						/* Fixed Grid */
							if ( !(blox_get('fluid', $wrapper_settings, false, true) && blox_get('fluid-grid', $wrapper_settings, false, true)) ) {

								$return .= $wrapper_selector . ' {
									min-width: ' . $wrapper_grid_width . 'px;
								}';

								$return .= $wrapper_selector . ' div.grid-container {
									width: ' . $wrapper_grid_width . 'px;
								}';

								if ( BloxResponsiveGrid::is_enabled() ) {

									$return .= $wrapper_selector . '.responsive-grid {
										min-width: 0 !important;
									}';

									$return .= $wrapper_selector . '.responsive-grid div.grid-container {
										width: auto !important;
										max-width: ' . $wrapper_grid_width . 'px;
									}';

								}

							}

					/* Grid */
						if ( blox_get('file') != 've-iframe-grid-dynamic' || !blox_get('visual-editor-open') ) {

							if ( BloxResponsiveGrid::is_enabled() || ( blox_get( 'fluid', $wrapper_settings, false, true ) && blox_get( 'fluid-grid', $wrapper_settings, false, true ) ) ) {
								$return .= self::responsive_grid( $wrapper );
							} else {
								$return .= self::fixed_grid( $wrapper );
							}

						}

				}

			/* Both Fixed and Fluid: Margin in Grid Mode */
				if ( blox_get('file') == 've-iframe-grid-dynamic' && blox_get('visual-editor-open') ) {

					$wrapper_instance_id = 'wrapper-' . BloxWrappers::format_wrapper_id($wrapper['original-id']);

					$return .= BloxElementProperties::output_css($wrapper_selector, array(
						'margin-top' => BloxElementsData::get_special_element_property('wrapper', 'instance', $wrapper_instance_id, 'margin-top', null, 'structure'),
						'margin-bottom' => BloxElementsData::get_special_element_property('wrapper', 'instance', $wrapper_instance_id, 'margin-bottom', null, 'structure'),
						'padding-top' => BloxElementsData::get_special_element_property('wrapper', 'instance', $wrapper_instance_id, 'padding-top', null, 'structure'),
						'padding-right' => BloxElementsData::get_special_element_property('wrapper', 'instance', $wrapper_instance_id, 'padding-right', null, 'structure'),
						'padding-bottom' => BloxElementsData::get_special_element_property('wrapper', 'instance', $wrapper_instance_id, 'padding-bottom', null, 'structure'),
						'padding-left' => BloxElementsData::get_special_element_property('wrapper', 'instance', $wrapper_instance_id, 'padding-left', null, 'structure')
					));

				}

			/* Responsive Break Points */
				if ( !BloxRoute::is_visual_editor_iframe('grid') && !(blox_get( 'file' ) == 've-iframe-grid-dynamic') ) {

					$responsive_options = blox_get( 'responsive-wrapper-options', $wrapper_settings, array() );

					$options = self::get_repeater_options( $responsive_options, 'breakpoint' );

					if ( $options ) {

						foreach ( $options as $option ) {

							/* Responsive CSS - some magic to make the columns work with the smartphone setting */
							$breakpoint = blox_fix_data_type( blox_get( 'breakpoint', $option, 'off' ) );
							$max_width  = blox_fix_data_type( blox_get( 'max-width', $option, '' ) );


							if ( $max_width && $breakpoint == 'custom' )
								$breakpoint = $max_width;

							$breakpoint_min_max = blox_fix_data_type( blox_get( 'breakpoint-min-or-max', $option, 'max' ) );
							$stretch            = blox_fix_data_type( blox_get( 'stretch', $option, false ) );
							$auto_center        = blox_fix_data_type( blox_get( 'auto-center', $option, false ) );
							$hide_wrapper       = blox_fix_data_type( blox_get( 'hide-wrapper', $option, false ) );

							/* Output Responsive CSS */
							$return .= '@media screen and (' . $breakpoint_min_max . '-width: ' . $breakpoint . ' ) { ';

							if ( $stretch )
								$return .= $wrapper_selector . ' .column {
									width: 100%;
									clear: both;
									margin-left: 0;
									margin-right: 0;
								}';

							if ( $auto_center )
								$return .=
									$wrapper_selector . ' .block, #whitewrap ' . $wrapper_selector . ' .block ul {
										text-align: center;
									}';

							if ( $hide_wrapper )
								$return .= $wrapper_selector . ' { display: none!important; }';


							$return .= '}'; //close media query


						}

					}

				}

		}
		
		return $return;
		
	}
	

			static function fixed_grid(array $wrapper) {

				global $wrapper_css_flags;

				$wrapper_settings = blox_get('settings', $wrapper, array());

				/* If wrapper is mirrored then use settings from it for the grid */
				if ( $potential_wrapper_mirror = BloxWrappersData::get_wrapper_mirror($wrapper_settings) )
					$wrapper_settings = $potential_wrapper_mirror;
					
				$grid_number = BloxWrappers::get_columns($wrapper);
					
				$column_width = BloxWrappers::get_column_width($wrapper);
				$gutter_width = BloxWrappers::get_gutter_width($wrapper);

				/* Keep extraneous CSS from be created by wrappers that have the same settings */
					$grid_class = 'grid-fixed-' . $grid_number . '-' . $column_width . '-' . $gutter_width;

					if ( isset($wrapper_css_flags[$grid_class]) )
						return;
				/* End extraneous CSS check */

				$grid_wrapper_width = ($column_width * $grid_number) + ($grid_number * $gutter_width);

				/* Add CSS prefix */
				$prefix = 'div.' . $grid_class . ' ';
							
				/* Column left margins */
				$return = $prefix . '.column { margin-left: ' . ($gutter_width) . 'px; }';
			
				/* Widths and Lefts */
				for ( $i = 1; $i <= $grid_number; $i++ ) {
				
					/* Vars */
					$grid_width = $column_width * $i + (($i - 1) * $gutter_width);
					$grid_left_margin = (($column_width + $gutter_width) * $i) + $gutter_width;
			
					$return .= $prefix . '.grid-width-' . $i . ' { width:' . ($grid_width) . 'px; }';
					$return .= $prefix . '.grid-left-' . $i . ' { margin-left: ' . ($grid_left_margin) . 'px; }';
			
					/**
					 * If it's the first column in a row and the column doesn't start on the far left,
					 * then the additional gutter doesn't have to be taken into consideration
					 **/
					$return .= $prefix . '.column-1.grid-left-' . $i . ' { margin-left: ' . ($grid_left_margin - $gutter_width) . 'px; }';				

				}

				/* Create a flag keeping this same Grid CSS from being outputted */
					$wrapper_css_flags['grid-fixed-' . $grid_number . '-' . $column_width . '-' . $gutter_width] = true;
					
				return $return;
			
			}
		
			
			static function responsive_grid(array $wrapper) {

				global $wrapper_css_flags;

				$wrapper_settings = blox_get('settings', $wrapper, array());

				/* If wrapper is mirrored then use settings from it for the grid */
				if ( $potential_wrapper_mirror = BloxWrappersData::get_wrapper_mirror($wrapper_settings) )
					$wrapper_settings = $potential_wrapper_mirror;
				
				$round_precision = 9;
				$return = '';

				$grid_number = BloxWrappers::get_columns($wrapper);
					
				$column_width = BloxWrappers::get_column_width($wrapper);
				$gutter_width = BloxWrappers::get_gutter_width($wrapper);

				/* Render the Grid into arrays to see if sub column CSS will be needed */
					if ( $wrapper_mirror = BloxWrappersData::get_wrapper_mirror( $wrapper['id'] ) ) {
						$wrapper_blocks = BloxBlocksData::get_blocks_by_wrapper( $wrapper_mirror['layout'], $wrapper_mirror['id'] );
					} else {
						$wrapper_blocks = BloxBlocksData::get_blocks_by_wrapper( blox_get( 'layout-in-use' ), $wrapper['original-id'] );
					}

					$wrapper_rendered = new BloxGridRenderer($wrapper_blocks, $wrapper_settings);

					/* Process the blocks into arrays */
						$wrapper_rendered->process();

					$blocks_in_sub_columns = !empty($wrapper_rendered->blocks_in_sub_columns) ? true : false;

				/* Keep extraneous CSS from be created by wrappers that have the same settings */
					$grid_class = 'grid-fluid-' . $grid_number . '-' . $column_width . '-' . $gutter_width;

					/* If there are no sub columns and the main CSS has already been outputted, just stop here */
					if ( isset($wrapper_css_flags[$grid_class]) && !$blocks_in_sub_columns )
						return;
				/* End extraneous CSS check */

				/* Make calculations for the percentages */
					$grid_wrapper_width = ($column_width * $grid_number) + (($grid_number - 1) * $gutter_width);
					
					$resp_width_ratio = ($column_width * $grid_number) / $grid_wrapper_width;
					$resp_gutter_ratio = ($gutter_width * $grid_number) / $grid_wrapper_width;
					$resp_single_column_width = (100 / $grid_number) * $resp_width_ratio;
					$resp_single_column_margin = (100 / $grid_number) * $resp_gutter_ratio;

				/* Add CSS prefix */
					$prefix = 'div.' . $grid_class . ' ';

				/* Generate the main Grid CSS */
					if ( !isset($wrapper_css_flags[$grid_class]) ) {

						$return .= $prefix . '.column { margin-left: ' . round($resp_single_column_margin, $round_precision) . '%; }' . "\n";

						for ( $i = 1; $i <= $grid_number; $i++ ) {
											
							/* Vars */
							$resp_grid_width = ($resp_single_column_width * $i) + ($i * $resp_single_column_margin);
							$resp_grid_left_margin = (($resp_single_column_width + $resp_single_column_margin) * $i) + $resp_single_column_margin;
						
							/* Output */
							$return .= $prefix . '.grid-width-' . $i . ' { width: ' . round($resp_grid_width - $resp_single_column_margin, $round_precision) . '%; }' . "\n";					
							
							if ( $i < $grid_number ) {
								
								$return .= $prefix . '.grid-left-' . $i . ' { margin-left: ' . round($resp_grid_left_margin, $round_precision) . '%; }' . "\n";

								/**
								 * If it's the first column in a row and the column doesn't start on the far left,
								 * then the additional gutter doesn't have to be taken into consideration
								 **/
								$return .= $prefix . '.column-1.grid-left-' . $i . ' { margin-left: ' . round($resp_grid_left_margin - $resp_single_column_margin, $round_precision) . '%; }';		
								
							}
												
						}

						/* Create a flag keeping this same Grid CSS from being outputted */
							$wrapper_css_flags['grid-fluid-' . $grid_number . '-' . $column_width . '-' . $gutter_width] = true;

					}
				/* End main grid CSS */

				/* Responsive Sub Column CSS */
				if ( $blocks_in_sub_columns ) {

						/* Get the columns required for sub columns */
							$required_columns_for_sub_columns = array();

							foreach ( $wrapper_rendered->blocks_in_sub_columns as $block_in_sub_column_id ) {

								if ( isset( $wrapper_rendered->blocks[ $block_in_sub_column_id ]['parent-column-width'] ) ) {

									$required_columns_for_sub_columns[] = $wrapper_rendered->blocks[ $block_in_sub_column_id ]['parent-column-width'];

								}

							}

							$required_columns_for_sub_columns = array_filter(array_unique($required_columns_for_sub_columns));
						/* End getting columns required for sub columns */

						for ( $i = 1; $i <= $grid_number; $i++ ) {

							/* Don't output the sub column CSS if there's no column of this number with sub columns and don't output it if has already by a previous wrapper. */
							if ( !in_array($i, $required_columns_for_sub_columns) || isset($wrapper_css_flags['grid-fluid-' . $grid_number . '-' . $column_width . '-' . $gutter_width . '-sub-columns-column-' . $i]) )
								continue;

							/* Vars */
							$resp_grid_width = ($resp_single_column_width * $i) + ($i * $resp_single_column_margin);
							$resp_grid_left_margin = (($resp_single_column_width + $resp_single_column_margin) * $i) + $resp_single_column_margin;

							$sub_column_single_width = ($resp_single_column_width / $resp_grid_width) * 100;
							$sub_column_single_margin = ($resp_single_column_margin / $resp_grid_width) * 100;

							$return .= $prefix . '.grid-width-' . $i . ' .sub-column { margin-left: ' . round($sub_column_single_margin, $round_precision) . '%; }' . "\n";

							for ( $sub_column_i = 1; $sub_column_i < $i; $sub_column_i++ ) {
													
								/* Sub column vars */
								$sub_column_width = ($sub_column_single_width * $sub_column_i) + ($sub_column_i * $sub_column_single_margin);
								$sub_column_margin = (($sub_column_single_width + $sub_column_single_margin) * $sub_column_i) + $sub_column_single_margin;
							
								$return .= $prefix . '.grid-width-' . $i . ' .sub-column.grid-width-' . $sub_column_i . ' { width: ' . round($sub_column_width - $sub_column_single_margin, $round_precision) . '%; }' . "\n";
								$return .= $prefix . '.grid-width-' . $i . ' .sub-column.grid-width-' . $sub_column_i . '.column-1 { width: ' . round($sub_column_width, $round_precision) . '%; }' . "\n";
								
								$return .= $prefix . '.grid-width-' . $i . ' .sub-column.grid-left-' . $sub_column_i . ' { margin-left: ' . round($sub_column_margin, $round_precision) . '%; }' . "\n";
								$return .= $prefix . '.grid-width-' . $i . ' .sub-column.grid-left-' . $sub_column_i . '.column-1 { margin-left: ' . round($sub_column_margin - $sub_column_single_margin, $round_precision) . '%; }' . "\n";
								
							}

							/* Create a flag keeping this same sub column CSS from being outputted */
								$wrapper_css_flags['grid-fluid-' . $grid_number . '-' . $column_width . '-' . $gutter_width . '-sub-columns-column-' . $i] = true;

						}

					}
				/* End responsive sub column CSS */
							
				return $return;
							
			}
			
			
			static function block_heights() {
				
				if ( !($blocks = BloxBlocksData::get_all_blocks()) )
					return false;

				$return = '';

				//Retrieve the blocks so we can check if the block type is fixed or fluid height
				$block_types = BloxBlocks::get_block_types();

				foreach ( $blocks as $block ) {

					/* Use legacy ID if present */
					$block['id'] = BloxBlocksData::get_legacy_id( $block );

					$selector = '#block-' . $block['id'];

					/* If the block is mirrored then change the selector */
						if ( $mirrored_block_id = BloxBlocksData::is_block_mirrored($block) )
							$selector = '#block-' . $mirrored_block_id . '.block-original-' . $block['id'];

					//If it's a fluid block (which blocks ARE by default), then we need to use min-height.  Otherwise, if it's fixed, we use height.
					if ( blox_get('fixed-height', blox_get($block['type'], $block_types), false) !== true )
						$return .= $selector . ' { min-height: ' . $block['dimensions']['height'] . 'px; }';
					else
						$return .= $selector . ' { height: ' . $block['dimensions']['height'] . 'px; }';

					$responsive_options = blox_get('responsive-options', $block['settings'], array());

					$options = self::get_repeater_options($responsive_options, 'blocks-breakpoint');

					if($options) {

						foreach ($options as $option) {
							
							/* Responsive CSS - some magic to make the columns work with the smartphone setting */
							$breakpoint = blox_fix_data_type(blox_get('blocks-breakpoint', $option, 'off'));

							if( !$breakpoint )
								continue;

							$max_width = blox_fix_data_type(blox_get('max-width', $option, ''));

							if($max_width && $breakpoint == 'custom')
								$breakpoint = $max_width;

							$disable_block_height = blox_fix_data_type(blox_get( 'disable-block-height', $option, false));
							$mobile_auto_center = blox_fix_data_type(blox_get( 'mobile-center-elements', $option, false));
							$breakpoint_min_max = blox_fix_data_type(blox_get( 'breakpoint-min-or-max', $option, 'max'));

							$fixed_height = blox_get('fixed-height', blox_get($block['type'], $block_types));

							/* Griddify Lists */
							$griddify_lists = blox_fix_data_type(blox_get( 'griddify-lists', $option, false));

							$hide_block = blox_fix_data_type(blox_get('hide-block', $option, false));

							/* Output Responsive CSS */
							$return .= '@media screen and ('. $breakpoint_min_max .'-width: ' . $breakpoint . ' ) { ';
							
								$return .= '#whitewrap ' . $selector . ' {';

									if ($hide_block)
										$return .= 'display: none!important;';
									if ($disable_block_height && $fixed_height !== true)
										$return .= 'min-height: inherit;';
									if ($disable_block_height && $fixed_height)
										$return .= 'height: auto;';
								
								$return .= '}';//close $selector

								if ($mobile_auto_center)
									$return .= '#whitewrap ' . $selector . ' * {
										text-align: center;
									}';

								

								if ( $griddify_lists ) {
									$return .= '#whitewrap ' . $selector . ' ul > li {
										float: left;
										margin: 0;
										width: 50%;
										font-size: 120%;
										-webkit-box-sizing: border-box;
										-moz-box-sizing: border-box;
										box-sizing: border-box;';

									if ($mobile_auto_center)
										$return .= 'text-align: center;';
									
									$return .= '}';

									$selector . ' ul li:nth-child(2n) {
										border-right: none;
									}';

								}

							$return .= '}';//close media query


						}

					}

				}

				return $return;

			}

	static function get_repeater_options($options, $default) {

		$has_options = false;

		foreach ( $options as $option )
			if ( $option[$default] ) {
				$has_options = true;
				break;
			}		

		if ( $has_options )
		  	return $options;

	}
	
	
	static function live_css() {
		
		if ( blox_get('visual-editor-open') )
			return null;
		
		return BloxSkinOption::get( 'live-css', false, null, false, false );
		
	}
	
}