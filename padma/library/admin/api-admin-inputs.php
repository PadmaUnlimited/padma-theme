<?php

class PadmaAdminInputs {	
	
	public static function generate($inputs, $table_class = 'form-table') {
		
		echo '<table class="' . $table_class . '">';
		
			$row_count = 0;
		
			foreach ( $inputs as $input ) {
				
				$row_count++;
				
				if ( !method_exists(__CLASS__, 'input_' . $input['type']) )
					continue;
					
				$tooltip = (isset($input['tooltip']) && $input['tooltip']) ? '<span class="label-tooltip" title="' . esc_attr($input['tooltip']) . '"></span>' : null;
				$description = (isset($input['description']) && $input['description']) ? '<p class="description">' . $input['description'] . '</p>' : null;
				$suffix = (isset($input['suffix']) && $input['suffix']) ? $input['suffix'] : null;
				
				$id = isset($input['id']) ? $input['id'] : null;
				
				$row_class = ( $row_count == count($inputs) ) ? ' class="no-bottom-border"' : null;

				if ( padma_get('value', $input) && !is_int(padma_get('value', $input)) )
					$input['value'] = stripslashes(esc_attr($input['value']));

				echo '<tr valign="top"' . $row_class . '>
					<th scope="row">
						<label for="' . $id . '">' . $input['label'] . $tooltip . '</label>
					</th>
					<td>';
					
					call_user_func(array(__CLASS__, 'input_' . $input['type']), $input);

				echo $suffix . $description;
				
				echo '</td>
					  </tr>';
				
			}
		
		echo '</table>';
		
	}
	
	
	public static function input_text($input) {
		
		$defaults = array(
			'size' => 'medium',
			'value' => null,
			'unit' => null,
			'tooltip' => null,
			'description' => null,
			'no-submit' => false,
			'masked' => false
		);
		
		$input = array_merge($defaults, $input);
		
		$name_attr = $input['no-submit'] ? null : ' name="padma-admin-input[' . $input['id'] . ']"';
		$type_attr = $input['masked'] ? 'password' : 'text';
		
		echo '<input type="' . $type_attr . '" class="' . $input['size'] . '-text" value="' . $input['value'] . '" id="' . $input['id'] . '"' . $name_attr . '> ' . $input['unit'];

	}
	
	
	public static function input_password($input) {
		
		$defaults = array(
			'size' => 'medium',
			'value' => null,
			'tooltip' => null,
			'description' => null,
			'no-submit' => false
		);
		
		$input = array_merge($defaults, $input);
		
		$name_attr = $input['no-submit'] ? null : ' name="padma-admin-input[' . $input['id'] . ']"';
		
		echo '<input type="password" class="' . $input['size'] . '-text" value="' . $input['value'] . '" id="' . $input['id'] . '"' . $name_attr . '>';

	}
	
	
	public static function input_paragraph($input) {
		
		$defaults = array(
			'cols' => 30,
			'rows' => 5,
			'value' => null,
			'allow-tabbing' => false,
			'no-submit' => false
		);
		
		$input = array_merge($defaults, $input);
		
		$allow_tabbing_class = $input['allow-tabbing'] ? 'class="allow-tabbing" ' : null;
		
		$name_attr = $input['no-submit'] ? null : ' name="padma-admin-input[' . $input['id'] . ']"';
		
		echo '<textarea ' . $allow_tabbing_class . 'cols="' . $input['cols'] . '" rows="' . $input['rows'] . '" id="' . $input['id'] . '"' . $name_attr . '>' . $input['value'] . '</textarea>';
		
	}
	
	
	public static function input_checkbox($input) {
		
		$defaults = array(
			'no-submit' => false
		);
		
		$input = array_merge($defaults, $input);
		
		echo '<fieldset>';
				
				//Initialize counter for adding <br />'s
				$checkbox_count = 0;
				
				echo '<legend class="screen-reader-text">
						<span>' . $input['label']. '</span>
					  </legend>';
				
				foreach($input['checkboxes'] as $checkbox) {
					
					$checkbox_count++;
					$checkbox_checked = ( isset($checkbox['checked']) && $checkbox['checked'] == true ) ? ' checked="checked"' : null;
					
					$name_attr = $input['no-submit'] ? null : ' name="padma-admin-input[' . $checkbox['id'] . ']"';
					
					echo '<label for="' . $checkbox['id'] . '">';
				
					//The hidden input is not needed if the no-submit attribute is set
					echo $input['no-submit'] ? null : '<input type="hidden" value="0"' . $name_attr . ' />';
							
					echo '<input type="checkbox" ' . $checkbox_checked . 'value="1" id="' . $checkbox['id'] . '"' . $name_attr . ' /> 
							' . $checkbox['label'] . 
						'</label>';
					
					//Add in <br /> if there are multiple checkboxes
					if ( count($input['checkboxes']) > 1 && $checkbox_count < count($input['checkboxes']) )
						echo '<br />';
					
				}
					
		echo '</fieldset>';
		
	}
	
	
	public static function input_checkboxes($input) {
		
		self::input_checkbox($input);
		
	}
	
	
	public static function input_select($input) {
		
		$defaults = array(
			'value' => null,
			'no-submit' => false,
			'multiple' => false
		);
		
		$input = array_merge($defaults, $input);	
		
		$name_multiple_array = $input['multiple'] ? '[]' : null;
		$multiple_attr = $input['multiple'] ? ' multiple="multiple" size="10"' : null;
		$name_attr = $input['no-submit'] ? null : ' name="padma-admin-input[' . $input['id'] . ']' . $name_multiple_array . '"';
		
		//If there are options in the multiselect and then the user wants to remove all of the selected options, it will not save.
		//This is the workaround.
		if ( !$input['no-submit'] && $input['multiple'] )
			echo '<input type="hidden" value="0" name="padma-admin-input[' . $input['id'] . ']" />';
					
		echo '<select class="postform" id="' . $input['id'] . '"' . $name_attr . $multiple_attr . '>';
				
				foreach($input['options'] as $value => $label) {
			
					if ( !$input['multiple'] )
						$selected = ( $value == $input['value'] ) ? ' selected="selected"' : null;
					else
						$selected = ( is_array($input['value']) && in_array($value, $input['value']) ) ? ' selected="selected"' : null;
			
					echo '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
			
				}
					
		echo '</select>';
		
	}
	
	
	public static function input_radio($input) {
		
		$defaults = array(
			'no-submit' => false
		);
		
		$input = array_merge($defaults, $input);
		
		$name_attr = $input['no-submit'] ? null : ' name="padma-admin-input[' . $input['id'] . ']"';
		
		echo '<fieldset>';

			//Initialize counter for adding <br />'s
			$radio_count = 0;
		
			echo '<legend class="screen-reader-text">
					<span>' . $input['label']. '</span>
				  </legend>';
		
			foreach($input['radios'] as $radio) {
			
				$radio_count++;
				$radio_checked = ( $radio['value'] === $input['value'] ) ? ' checked="checked"' : null;
			
				echo '
					<label for="' . $radio['value'] . '" class="radio-label">
						<input type="radio" ' . $radio_checked . 'value="' . $radio['value'] . '" id="' . $radio['value'] . '"' . $name_attr . ' /> 
						' . $radio['label'] . 
					'</label>';
			
			}
					
		echo '</fieldset>';
		
	}
		
}