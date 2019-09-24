<?php
class PadmaPropertyInputs {
	
	
	public static function display($element, $special_element_type = false, $special_element_meta = false, $data, $data_without_defaults) {
						
		if ( !is_array($element) || empty($element['properties']) )
			return null;

		$default_box_args = array(
			'group' => false,
			'element' => $element,
			'special_element_type' => $special_element_type,
			'special_element_meta' => $special_element_meta,
			'selective_properties' => false,
			'property_values' => $data,
			'property_values_excluding_defaults' => $data_without_defaults
		);
				
		/*  Format $element['properties'] into an easier array to work with and then make it alphabetical */
			$property_groups = array();

			foreach ( $element['properties'] as $key => $value ) {

				if ( is_numeric($key) ) {
					$property_groups[$value] = false;
				} else {
					$property_groups[$key] = $value;
				}

			}

		/* Include nudging and margin for all elements except for Body */
			if ( !(!empty($element['disallow-nudging']) && padma_fix_data_type($element['disallow-nudging']) === true) && !in_array('nudging', $element['properties']) ) {

				$property_groups['nudging'] = false;
				$property_groups['margins'] = false;

			}

		/* Change 'rounded-corners' to 'corners' if it exists */
			if ( isset($property_groups['rounded-corners']) ) {

				$property_groups['corners'] = $property_groups['rounded-corners'];
				unset($property_groups['rounded-corners']);

			}

		/* Sort property groups */
			ksort($property_groups);
			
		/* Display the property groups registered to the element.  */			
			$property_group_row_i = 0;

			foreach ( $property_groups as $group => $selective_properties ) {

				/* Possibly open property group row */
					if ( $property_group_row_i % 3 === 0 ) {
						echo '<div class="property-group-row">';
						$property_group_row_open = true;
					}

					$property_group_row_i++;

				/* Display the box */
					self::box(array_merge($default_box_args, array(
						'group' => $group,
						'selective_properties' => $selective_properties
					)));

				/* Close property group row if necessary */
					if ( $property_group_row_i % 3 === 0 || ($property_group_row_open && key(array_slice($property_groups, -1, 1, true)) == $group ) ) {
						echo '</div><!-- .property-group-row -->';
						unset($property_group_row_open);
					}
							
			} 	
		
	}
	
	
	public static function box($args) {
		
		$defaults = array(
			'group' => null,
			'element' => null,
			'special_element_type' => false,
			'special_element_meta' => false,
			'selective_properties' => false,
			'property_values' => false,
			'property_values_excluding_defaults' => false,
			'unsaved_values' => false
		);
		
		$args = array_merge($defaults, $args);
		$args['group_nice'] = ucwords(str_replace('-', ' ', $args['group']));

		//If the group doesn't exist, don't attempt to display it
		if ( !($properties = PadmaElementProperties::get_properties_by_group($args['group_nice'])) )
			return false;
			
		$args['selector'] = isset($args['element']['selector']) ? $args['element']['selector'] : null;

		/* Custom behaviors for special element types */
			switch ( $args['special_element_type'] ) {
				
				case 'instance':

					$instances = padma_get('instances', $args['element']);
					$instance = $instances[$args['special_element_meta']];
				
					$args['selector'] = $instance['selector'];

				break;
				
				case 'state':

					$states = padma_get('states', $args['element']);
					$state = $states[$args['special_element_meta']];
				
					$args['selector'] = $state['selector'];

				break;


				case 'layout':

					if ( isset($args['element']['selector']) && isset($args['special_element_meta']) ) {

						$args['selector'] = 'body.layout-using-' . $args['special_element_meta'] . ' ' . $args['element']['selector'];

						if ( $args['element']['selector'] == 'body' )
							$args['selector'] = str_replace(' body', '', $args['selector']);

					}

				break;
				
			} 

		/* Set customized box class flag */
			$customized_box_class = '';
			$property_box_title = '';

			foreach ( $args['property_values_excluding_defaults'] as $property_id => $property_value ) {

				if ( !isset($properties[$property_id]) )
					continue;

				$customized_box_class = ' design-editor-box-customized';
				$property_box_title = ' title="' . __('You have customized a property in this property group.', 'padma') . '"';

				break;

			}

		/* Create the box */
			echo '<div class="design-editor-box design-editor-box-' . $args['group'] . $customized_box_class . '">';
				echo '<span class="design-editor-box-title"' . $property_box_title . '><span>' . $args['group_nice'] . '</span></span>';
					
				echo '<ul class="design-editor-box-content">';
					
					foreach ( $properties as $property_id => $property_options ) {


						//If the $selective_properties variable is set, then make sure we're only showing those properties.
						if ( is_array($args['selective_properties']) )
							if ( !in_array($property_id, $args['selective_properties']) )
								continue;

						if ( !padma_get('display', $property_options, true) )
							continue;

						if ( $property_options['type'] != 'box-model' ) {

							self::build_property_input($property_id, $property_options, $args);

							continue;

						}

						/**
						 *
						 * Margin auto support
						 *
						 */						
						if($args['group']=='margins'){							
							if( isset($args['property_values']['margin-top']) && $args['property_values']['margin-top'] == 'auto'){
								unset($args['property_values']['margin-top']);
								$args['property_values']['margin-top-auto'] = 'auto';
							}
							if($args['property_values']['margin-right'] == 'auto'){
								unset($args['property_values']['margin-right']);
								$args['property_values']['margin-right-auto'] = 'auto';
							}
							if($args['property_values']['margin-bottom'] == 'auto'){
								unset($args['property_values']['margin-bottom']);
								$args['property_values']['margin-bottom-auto'] = 'auto';
							}
							if($args['property_values']['margin-left'] == 'auto'){
								unset($args['property_values']['margin-left']);
								$args['property_values']['margin-left-auto'] = 'auto';
							}
						}

						/* Handle box model inputs differently from the rest of the property inputs */							
							echo '<div class="box-model-inputs-container">';

								if ( padma_get('name', $property_options) )
									echo '<strong class="box-model-inputs-heading">' . $property_options['name'] . '</strong>';

								echo '<div class="box-model-inputs box-model-inputs-position-' . padma_get('position', $property_options, 'sides') . '">';

									foreach ( $property_options['box-model-inputs'] as $box_modal_input_id ) {

										/* Do not show wrapper-left and wrapper-right if it's a block or wrapper */										
											if ( ((strpos($args['element']['selector'], '.block-type') === 0 && strpos($args['element']['selector'], ' ') === false) || $args['element']['id'] == 'wrapper') && in_array($box_modal_input_id, array('margin-left', 'margin-right')) )
												continue;

										$box_modal_input_options['lockable'] = true;
										self::build_property_input($box_modal_input_id, $properties[$box_modal_input_id], $args);

									}

									echo '<span class="design-editor-lock-sides" data-locked="false"></span>';

								echo '</div><!-- .box-model-inputs -->';

							echo '</div><!-- .box-model-inputs-container -->';
						/* End box model input handling */

					}
					
				echo '</ul><!-- .design-editor-box-content -->';
			
			echo '</div><!-- .design-editor-box -->';
		/* End box creation */
		
	}


		public static function build_property_input($property_id, $property_options, $element_args) {

			//Make sure the input type for the property really exists
			if ( !is_callable(array(__CLASS__, 'input_' . str_replace('-', '_', $property_options['type']))) )
				return false;
			
			/* Get the value of the property */
				$original_property_value = padma_fix_data_type(padma_get($property_id, $element_args['property_values']));

				if ( ($original_property_value || $original_property_value === 0) && strtolower($original_property_value) !== 'delete' ) {
					
					$property_options['value'] 		= $element_args['property_values'][$property_id];
					$property_options['customized'] = true;
					

				//Fall back to default
				} else {
																	
					$property_default = isset($property_options['default']) ? $property_options['default'] : null;

					$property_options['value'] 		= $property_default;
					$property_options['customized'] = false;
																		
				}	

			/* Set up elements and attributes */
				$uncustomize_button = $element_args['special_element_type'] != 'default' ? '<span class="uncustomize-property tooltip" title="Delete this customization."></span>' : null;
				$customize_button = $element_args['special_element_type'] != 'default' ? '<div class="customize-property"><span class="tooltip" title="Click to change the value for this property.  If left uncustomized, the property will automatically inherit to the default set for this element type in the defaults tab or the parent element if editing a state, instance, or layout-specific element.">Customize</span></div>' : null;


				$hidden_input_attributes_array = array(
					'type' 					=> 'hidden',
					'class' 				=> 'property-hidden-input',
					'value' 				=> $property_options['value'],
					'element' 				=> $element_args['element']['id'],
					'property' 				=> $property_id,
					'special_element_type' 	=> $element_args['special_element_type'],
					'special_element_meta' 	=> $element_args['special_element_meta'],
					'element_selector' 		=> esc_attr(stripslashes($element_args['selector'])),
					'callback' 				=> $callbackJS = esc_attr('(function(params){' . $property_options['js-callback'] . '})')
				);
									

				/* Turn attributes array into a string for HTML */
					$hidden_input_attributes = '';

					foreach ( $hidden_input_attributes_array as $attribute => $attribute_value )
						$hidden_input_attributes .= $attribute . '="' . $attribute_value . '" ';

					$hidden_input_attributes = trim($hidden_input_attributes);
							
			/* Set up attributes */
				$property_title = '';
				$property_classes = array(
					'design-editor-property-' . $property_id
				);;

				if ( $property_options['customized'] ) {

					$property_classes[] = 'customized-property-by-user';
					$property_title = ' title="' . __('You have customized this property.', 'padma') . '"';

				} else if ( $element_args['special_element_type'] !== 'default' ) {

					$property_classes[] = 'uncustomized-property';

				}

			/* add a locked class if it's a lockable element only */
				if ( padma_get('lockable', $property_options) )
					$property_classes[] = 'lockable-property';

			/* set the customizable defaults and class */
			if ( is_array( padma_get( 'unit', $property_options ) ) ) {

				$unit_defaults = array(
					'default' => 'px',
					'options' => array(
						'Absolute Lengths' => array(
							'cm'	=> 'cm',
							'mm'	=> 'mm',
							'in'	=> 'in',
							'px'	=> 'px',
							'pt'	=> 'pt',
							'pc'	=> 'pc',
						),
						'Relative Lengths' => array(
							'em'	=> 'em',
							'ex'	=> 'ex',
							'rem'	=> 'rem',
							'vw'	=> 'vw',
							'vh'	=> 'vh',
							'vmin'	=> 'vmin',
							'vmax'	=> 'vmax',
							'%'		=> '%',
						),
					)
				);

				$property_options['unit'] = array_merge( $unit_defaults, $property_options['unit'] );

				/* add a unit class if necessary */
				$property_classes[] = 'customizable-unit';

			}

			echo '<li data-property-id="' . $property_id . '" class="' . implode(' ', array_filter($property_classes)) . '"' . $property_title . '>';
			
				echo '<strong><span class="property-label">' . $property_options['name'] . '</span>' . (!padma_get('lockable', $property_options) ? $uncustomize_button : null) . '</strong>';
				echo '<div class="property-' . $property_options['type'] . ' property">';
												
					echo (padma_get('lockable', $property_options)) ? $uncustomize_button : null; /* Uncustomize button needs to be in different location for box model input s*/




					//Effects
					if($property_id === 'effect' && isset($property_options['complex-options'])){
						
						$property_options['options'] = call_user_func($property_options['complex-options'], $element_args);

						call_user_func(
							array(__CLASS__, 'input_' . str_replace('-', '_', $property_options['type'])), 
							$property_options, 
							$property_id
						);

					}else{
						
						call_user_func(array(__CLASS__, 'input_' . str_replace('-', '_', $property_options['type'])), $property_options, $property_id);
					}


					if ( is_array( padma_get( 'unit', $property_options ) ) ) {
					
						$unit_value = trim(str_replace( array_merge( range( 0, 9 ), array( '.', '-' ) ), '', $property_options['value'] ));

						self::input_select(array(
							'options' 		=> $property_options['unit']['options'],
							'value' 		=> $unit_value ? $unit_value : $property_options['unit']['default'],
							'unit-select' 	=> true
						));
					}
					

				echo '<input ' . $hidden_input_attributes . ' />';
					
				echo '</div>';
				
				echo $customize_button; 
				
			echo '</li>';
			

		}
	
	
	public static function input_integer($options, $id) {
		
		$unit = is_string(padma_get('unit', $options)) ? '<span class="unit">' . padma_get('unit', $options) . '</span>' : null;

		/* Remove unit from value */
		if ( is_array( padma_get( 'unit', $options ) ) ) {

			$value_unit = trim( str_replace( array_merge( range( 0, 9 ), array( '.', '-' ) ), '', $options['value'] ) );
			$options['value'] = str_replace( $value_unit, '', $options['value']);

		}


		echo '<input type="number" value="' . $options['value'] . '" step="' . padma_get('step', $options, 1) . '"  />' . $unit;
						
	}
	
	
	public static function input_color($options, $id) {
				
		echo '
		<div class="colorpicker-box-container">
			<div class="colorpicker-box-transparency"></div>
			<div class="colorpicker-box" style="background-color:' . padma_format_color($options['value']) . ';"></div>
		</div><!-- .colorpicker-box-container -->
		';
		
	}
	
	
	public static function input_select($options, $id = null) {

		$unit_select_class = padma_get('unit-select', $options) ? ' property-unit-select' : '';
		
		echo '<div class="select-container' . $unit_select_class . '"><select>';
						
			//If 'options' is a function, then call it and replace $options['options']
			if ( is_string($options['options']) && strpos($options['options'], '()') !== false ) {
				
				$sanitized_function = str_replace('()', '', $options['options']);
				
				//If is a method rather than function, the method must be declared as static otherwise it'll return false on PHP 5.2
				if ( !is_callable($sanitized_function) ) {
					echo '</select></div><!-- .select-container -->';
					return;
				}
				
				$options['options'] = call_user_func($sanitized_function);
				
			}
			
			if ( is_array($options['options']) ) {
				
				foreach ( $options['options'] as $value => $content ) {
					
					//If it's an optgroup, handle it.
					if ( is_array($content) ) {
						
						echo '<optgroup label="' . $value . '">';
						
						foreach ( $content as $value => $text ) {
				
							//If the current option is the value in the DB, then mark it as selected
							$selected_option = ( $value == $options['value'] ) ? ' selected="selected"' : null;

							echo '<option value="' . $value . '"' . $selected_option . '>' . $text . '</option>';
							
						} 
						
						echo '</optgroup>';
						
					//Otherwise it's just a normal option
					} else {
						
						//If the current option is the value in the DB, then mark it as selected
						$selected_option = ( $value == $options['value'] ) ? ' selected="selected"' : null;

						echo '<option value="' . $value . '"' . $selected_option . '>' . $content . '</option>';
						
					}
					
				}
				
			}	
				
			
		echo '</select></div><!-- .select-container -->';
		
	}
	
	
	public static function input_image($options, $id) {
		
		$src_visibility = ( is_string($options['value']) && strlen($options['value']) > 0 && $options['value'] != 'none' ) ? '' : ' style="display:none;"';

		$filename_parts = explode('/', $options['value']);
		$filename = end($filename_parts);
		
		echo '
			<span class="button">Choose</span>
			
			<div class="image-input-controls-container"' . $src_visibility . '>
				<span class="src">' . $filename . '</span>
				<span class="delete-image">Delete</span>
			</div>
		';
				
	}
	
	
	public static function input_checkbox($options, $id) {
		
	}
	
	
	public static function input_font_family_select($options, $id) {

		/* Output input */
			$font_fragments = explode('|', $options['value']);



			/* Web Font */
			if ( count($font_fragments) >= 2 ){

				if (\strpos($font_fragments[1], ':') !== false) {
					$parts = explode(':', $font_fragments[1]);
					$font_stack = $parts[0];
					$font_name = $parts[0];
				}else{
					$font_stack = $font_fragments[1];
					$font_name = $font_fragments[1];
				}

				$webfont_class = ' font-name-webfont';

			}else{
				/* Traditional Font */
				$font_stack = PadmaFonts::get_stack($options['value']);
				$font_name = ucwords($options['value']);

				$webfont_class = null;

			}



			echo '<span class="font-name' . $webfont_class . '" style="font-family: ' . $font_stack . ';" data-webfont-value="' .  $options['value'] . '">' . $font_name . '</span>';

			echo '<span class="open-font-browser pencil-icon"></span>';

		/* Font Browser */
			echo '<div class="font-browser">';
					
					echo '<ul class="tabs">';
						do_action('padma_fonts_browser_tabs');
					echo '</ul>';

					do_action('padma_fonts_browser_content');

			echo '</div><!-- .font-browser -->';
				
	}
}