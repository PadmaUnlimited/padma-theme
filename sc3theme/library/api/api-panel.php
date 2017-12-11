<?php
function blox_register_visual_editor_panel($class) {

	add_action('blox_visual_editor_display_init', create_function('', 'return blox_register_visual_editor_panel_callback(\'' . $class . '\');'), 999);
	
}


function blox_register_visual_editor_panel_callback($class) {

	if ( !class_exists($class) )
		return new WP_Error('panel_class_does_not_exist', __('Error: The panel class being registered does not exist.', 'blox'), $class);
	
	$panel = new $class();
	$panel->register();
	
	return true;
	
}


abstract class BloxVisualEditorPanelAPI {
	
	
	/**
	 *	Slug/ID of panel.  Will be used for HTML IDs and whatnot.
	 **/
	public $id;
	
	
	/**
	 * Name of panel.  This will be shown in the tabs.
	 **/
	public $name;
	
	
	/**
	 * Sub tabs.  This is not always used.
	 **/
	public $tabs;
	
	
	/**
	 * Inputs.  This is not always used.
	 **/
	public $inputs;
	
	
	/**
	 * Which mode to display the panel on.
	 **/
	public $mode;
	
	
	/**
	 * Which options group to save in by default
	 **/
	public $options_group = 'general';
	

	/**
	 * Will fire when the panel is opened via AJAX
	 **/
	public $open_js_callback = null;
	
	
	
	/**
	 * Register the panel.
	 * 
	 * @param string Name of panel to be displayed
	 * @param string ID of panel for HTML and options
	 **/
	public function register() {
		
		$mode = BloxVisualEditor::get_current_mode();

		/* Forward old Manage panels to Design */
		if ( $this->mode == 'manage' )
			$this->mode = 'design';
		
		if ( strtolower($this->mode) !== strtolower($mode) )
			return false;
		
		/* Since there is a message that's displayed if there is no panel content, we have to tell it not to display the message now that
		 * we're registering a panel */
		remove_action('blox_visual_editor_panel_top_tabs', array('BloxVisualEditorDisplay', 'add_default_panel_link'));
		remove_action('blox_visual_editor_content', array('BloxVisualEditorDisplay', 'add_default_panel'));
			
		add_action('blox_visual_editor_panel_top_tabs', array($this, 'panel_link'));
		add_action('blox_visual_editor_content', array($this, 'build_panel'));

		if ( method_exists($this, 'init') )
			$this->init();
					
	}
	
	
	public function modify_arguments($args) {
		
		//Allow developers to modify the properties of the class and use functions since doing a property 
		//outside of a function will not allow you to.
		
	}
	
	
	public function parse_function_args($array) {
		
		if ( !is_array($array) || count($array) === 0 )
			return $array;
			
		foreach ( $array as $key => $value ) {
			
			if ( !is_string($value) )
				continue;
			
			//Check if it's a function
			if ( preg_match("/^[a-z0-9_]*(\(\))$/", $value) ) {				
				$array[$key] = call_user_func(array($this, str_replace('()', '', $value)));
			} else {
				continue;
			}
			
		}
		
		return $array;
		
	}
	
	
	public function panel_link() {
		
		echo '<li><a href="#' . $this->id . '-tab">' . $this->name . '</a></li>';
		
	}
	
	
	public function build_panel($id) {
		
		$class = ($this->tabs) ? ' sub-tab' : null;
			
		echo '<div id="' . $this->id . '-tab" class="panel' . $class . '">';

			//Allow developers to modify the properties of the class and use functions since doing a property 
			//outside of a function will not allow you to.
			$this->modify_arguments(array());
		
			$this->panel_content();
		
		echo '</div>';

					
	}
	
	
	public function panel_content($args = false) {

		if ( $this->tabs && $this->inputs ) {
			
			echo '<ul class="sub-tabs" data-open-js-callback="' . esc_attr('(function(args){' . $this->open_js_callback . '})') . '">';
			
				foreach ($this->tabs as $id => $name) {
					
					echo '<li id="sub-tab-' . $id . '"><a href="#sub-tab-' . $id . '-content">' . $name . '</a></li>';
					
				}
			
			echo '</ul>';
			
			echo '<div class="sub-tabs-content-container" data-panel-args="' . esc_attr(json_encode($args)) . '">';
			
			foreach ($this->tabs as $id => $name) {
				
				echo '<div class="sub-tabs-content" id="sub-tab-' . $id . '-content">';
					
					//Display notice for tab if one exists.
					if ( isset($this->tab_notices[$id]) )
						echo '<div class="sub-tab-notice">' . $this->tab_notices[$id] . '</div>';
				
					$this->sub_tab_content($id, $name);
				
				echo '</div><!-- div#sub-tab-' . $id . '-content -->';
				
			}
			
			echo '</div><!-- .sub-tabs-content-container -->';
						
		}
		
	}
	
	
	public function sub_tab_content($id, $name = false) {
		
		$this->create_inputs($id);
					
	}


		public function create_inputs($tab) {

			if ( isset($this->inputs[$tab]) && is_array($this->inputs[$tab]) ) {

				foreach ( $this->inputs[$tab] as $name => $input )
					$this->render_input($input);

			}

		}
	
	
	public function render_input($input) {
						
		//Fill defaults
		$defaults = array(
			'tooltip' => false,
			'default' => false,
			'callback' => null
		);
		
		//Merge defaults
		$input = array_merge($defaults, $input);
		
		//Fix up inputs
		$input = $this->parse_function_args($input);

		if ( !isset($input['name']) || !isset($input['type']) )
			return;
		
		/* Set up main input variables */
			$input['name'] = strtolower($input['name']);
			$input['group'] = ( isset($input['group']) ) ? $input['group'] : $this->options_group;
			$input['tooltip'] = (isset($input['tooltip']) && $input['tooltip'] != false) ? $input['tooltip'] : false;

		/* Populate the value */
			$input['default'] = ( isset($input['default']) ) ? $input['default'] : null;

			if ( isset($this->wrapper) && $this->wrapper && !isset($input['value']) )
				$input['value'] = BloxWrappersData::get_wrapper_setting($this->wrapper, $input['name'], $input['default']);
			else if ( isset($this->block) && $this->block && !isset($input['value']) )
				$input['value'] = BloxBlocksData::get_block_setting($this->block, $input['name'], $input['default']);
			else if ( !isset($input['value']) && blox_get('template-option', $input, true) )
				$input['value'] = BloxSkinOption::get($input['name'], $input['group'], $input['default']);
			else if ( !isset($input['value']) )
				$input['value'] = BloxOption::get($input['name'], $input['group'], $input['default']);

		/* Setup Attributes */
			$attributes_array = array(
				'id' => (isset($this->block) && $this->block) ? 'input-' . $this->block['id'] . '-' . $input['name'] : 'input-' . $input['group'] . '-' . $input['name'],
				'name' => $input['name'],
				'data-group' => $input['group']
			);
									
			/* Set up the callback attribute */
				$attributes_array['data-callback'] = esc_attr('(function(args){var input=args.input;var value=args.value;var block=args.block || null;' . $input['callback'] . '})');

			/* Set up data handler override if it's used */
				if ( blox_get('data-handler-callback', $input) )
					$attributes_array['data-data-handler-callback'] = esc_attr('(function(args){' . $input['data-handler-callback'] . '})');
				
			/* Set up toggle attribute */
				if ( blox_get('toggle', $input) )
					$attributes_array['data-toggle'] = esc_attr(json_encode($input['toggle']));
				
			/* No save attribute */
				if ( blox_get('no-save', $input, false) )
					$attributes_array['data-no-save'] = 'true';

			/* Turn attributes array into a string for HTML */
				$input['attributes'] = '';

				foreach ( $attributes_array as $attribute => $attribute_value )
					$input['attributes'] .= $attribute . '="' . $attribute_value . '" ';

				$input['attributes'] = trim($input['attributes']);

		/* If it's a repeater then handle it before it's handled as an input */
			if ( $input['type'] == 'repeater' )
				return $this->repeater($input);

		/* Handle regular input */				
			if ( method_exists($this, 'input_' . str_replace('-', '_', $input['type'])) ) {

				/* Handle all types except for raw HTML input */
				if ( $input['type'] != 'raw-html' ) {

					echo '<div class="input input-' . $input['type'] . '" id="input-' . $input['name'] . '">';
										
						if ( $input['tooltip'] )
							echo '<div class="tooltip-button" title="' . esc_attr($input['tooltip']) . '"></div>';
					
						call_user_func(array($this, 'input_' . str_replace('-', '_', $input['type'])), $input);

					echo '</div><!-- #input-' . $input['name'] . ' -->';

				} else {

					call_user_func(array($this, 'input_' . str_replace('-', '_', $input['type'])), $input);

				}
				
			}
		/* End regular input handling */
		
	}


	public function repeater($input) {

		$repeater_sortable_class = blox_get('sortable', $input) ? ' repeater-sortable' : null;

		echo '<div class="repeater' . $repeater_sortable_class . '" data-repeater-limit="' . blox_get('limit', $input, '0') . '">';

			if ( $repeater_label = blox_get('label', $input) ) {

				$this->render_input(array(
					'name' => 'repeater-' . $input['name'] . '-heading',
					'type' => 'heading',
					'label' => $repeater_label,
					'tooltip' => blox_get('tooltip', $input)
				));

			}

			/* If the value is non-existent then show an empty group. */
				if ( !isset($input['value']) || !is_array($input['value']) || empty($input['value']) ) {

					$this->repeater_group(array_merge($input, array(
						'single' => true
					)));

			/* Values are valid, loop them. */
				} else {

					foreach ( $input['value'] as $group_index => $value_group ) {

						if ( count($input['value']) === 1 ) {
							$input['single'] = true;
						}

						$this->repeater_group( $input, $group_index );

					}

				}

			/* Add the template repeater group that's cloned when a new group is added */
				$this->repeater_group(array_merge($input, array(
					'template' => true,
					'value' => null
				)));

			/* Hidden Input */
				echo '<input ' . $input['attributes'] . ' type="hidden" value="" class="repeater-group-input" />';
 				
		echo '</div><!-- .repeater -->';

	}
	

		public function repeater_group($input, $group_index = null) {

			$classes = array('repeater-group');

			if ( blox_get('template', $input) )
				$classes[] = 'repeater-group-template';

			if ( blox_get('single', $input) )
				$classes[] = 'repeater-group-single';

			echo '<div class="' . implode(' ', $classes) . '">';

				if ( blox_get('sortable', $input) )
					echo '<span class="sortable-handle"><span></span><span></span><span></span></span>';

				foreach ( $input['inputs'] as $index => $input_options ) {

					$input_value = blox_get($input_options['name'], $input['value'][$group_index], blox_get('default', $input_options));

					$this->render_input(array_merge($input_options, array(
						'value' => $input_value
					)));

				}

				echo '<div class="repeater-group-buttons">';
					echo '<span class="remove-group"></span>';
					echo '<span class="add-group"></span>';
				echo '</div><!-- .repeater-group-buttons -->';

			echo '</div><!-- .repeater-group -->';	

		}
	

	public function input_checkbox($input) {
		
		$checked_attribute = ( (bool)blox_fix_data_type($input['value']) === true ) ? ' checked="checked"' : null;

		echo '
			<div class="input-left">
				<label>
					<input ' . $input['attributes'] . ' type="checkbox" value="true"' . $checked_attribute . ' />
					' . $input['label'] . '
				</label>
			</div>
		';
		
	}
	
	
	public function input_text($input) {
	
		$readonly = ( isset($input['readonly']) && $input['readonly'] === true )  ? ' disabled' : null;
		
		echo '
			<div class="input-left">
				<label>' . $input['label'] . '</label>
			</div>
			
			<div class="input-right">
				<input type="text" ' . $input['attributes'] . ' placeholder="' . stripslashes(esc_attr(blox_get('placeholder', $input))) . '" value="' . stripslashes(esc_attr($input['value'])) . '" class="text"' . $readonly . ' />';
				
			if ( isset($input['suffix']) ) echo '<span class="suffix">' . $input['suffix'] . '</span>';

		echo '
			</div>
		';

	}
	
	
	public function input_textarea($input) {

		echo '
			<div class="input-left">
				<label>' . $input['label'] . '</label>
			</div>
			
			<div class="input-right">
				<span class="textarea-open pencil-icon tooltip" title="View Textarea"></span>
				<div class="textarea-container">
					<textarea ' . $input['attributes'] . '>' . stripslashes(esc_textarea($input['value'])) . '</textarea>
				</div>
			</div>
		';
		
	}


	public function input_code($input) {

			echo '
				<div class="input-left">
					<label>' . $input['label'] . '</label>
				</div>

				<div class="input-right">
					<span class="code-editor-open pencil-icon tooltip" title="View Code Editor" data-editor-mode="' . blox_get('mode', $input, 'php') . '"></span>
					<textarea ' . $input['attributes'] . '>' . stripslashes(esc_textarea($input['value'])) . '</textarea>
				</div>
			';

		}


	public function input_wysiwyg($input) {
               
		echo '
			<div class="input-left">
				<label>' . $input['label'] . '</label>
			</div>
			
			<div class="input-right">
				<span class="wysiwyg-open pencil-icon tooltip" title="View Editor"></span>
				<div class="wysiwyg-container">
					<textarea ' . $input['attributes'] . '>' . stripslashes(esc_textarea($input['value'])) . '</textarea>
				</div>
			</div>
		';
		
	}
	

	public function input_integer($input) {

		$readonly = ( isset($input['readonly']) && $input['readonly'] === true )  ? ' disabled' : null;
		
		echo '<div class="input-left">
				<label>' . $input['label'] . '</label>
			</div>
			
			<div class="input-right">
				<input type="text" ' . $input['attributes'] . ' value="' . (int)$input['value'] . '" class="text"'. $readonly .' />';
				
			if ( isset($input['unit']) ) echo '<span class="suffix">' . $input['unit'] . '</span>';
			
		echo '
			</div>
		';
						
	}
	
	
	public function input_select($input) {

		echo '
			<div class="input-left">
				<label>' . $input['label'] . '</label>
			</div>
		';
		
		$chosen_class = ( blox_get('chosen', $input) ) ? ' select-chosen' : '';

		echo '<div class="input-right' . $chosen_class . '">';
			
			echo '<div class="select-container"><select ' . $input['attributes'] . '>';

			foreach( $input['options'] as $value => $text ) {

				if ( is_array($text) ) {

					echo '<optgroup label="' . $value . '">';

					foreach ( $text as $optgrop_option_value => $optgrop_option_text ) {

						self::input_select_output_option( $optgrop_option_value, $optgrop_option_text, $input['value'] );

					}

					echo '</optgroup>';

				} else {

					self::input_select_output_option($value, $text, $input['value']);

				}
				
			}

			echo '</select></div><!-- .select-container -->';

		echo '</div>';
										
	}


	private static function input_select_output_option($value, $text, $input_value) {

		$selected = ( $input_value == $value ) ? ' selected' : null;

		echo '<option value="' . $value . '"' . $selected . '>' . $text . '</option>';

	}
	
	
	public function input_multi_select($input) {
				
		echo '
			<div class="input-left">
				<label>' . $input['label'] . '</label>
			</div>
		';				
				
		echo '<div class="input-right">';
	
			echo '<span class="multi-select-open pencil-icon tooltip" title="View Options"></span>';
			echo '<div class="multi-select-container">';
						
				echo '<select ' . $input['attributes'] . ' multiple="multiple" class="tooltip" title="Hold Ctrl (Windows) or Command (Mac) to select multiple options.">';

				foreach ( $input['options'] as $value => $text ) {
					
					$selected = ( is_array($input['value']) && in_array($value, $input['value']) ) ? ' selected' : null;
		
					echo '<option title="' . $text . '" value="' . $value . '"' . $selected . '>' . $text . '</option>';
					
				}

				echo '</select>';
			
			echo '</div><!-- .multi-select-container -->';
	
		echo '</div>';
										
	}

	
	public function input_colorpicker($input) {
		
		$input['value'] = blox_format_color($input['value']);
		
		echo '
			<div class="input-left">
				<label>' . $input['label'] . '</label>
			</div>
			
			<div class="input-right">
				<div class="colorpicker-box-container">
					<div class="colorpicker-box-transparency"></div>
					<div class="colorpicker-box" style="background-color:' . $input['value'] . ';"></div>
				</div><!-- .colorpicker-box-container -->

				<input ' . $input['attributes'] . ' type="hidden" value="' . $input['value'] . '" />
			</div>
		';
		
	}
	
	
	public function input_image($input) {
		
		$src_visibility = ( $input['value'] !== null && is_string($input['value']) ) ? '' : ' style="display:none;"';
		
		echo '<div class="input-left"><label>' . $input['label'] . '</label></div><!-- .input-left -->';

		$filepath = explode('/', $input['value']);
		$filename = end($filepath);
				
		echo '<div class="input-right"><span class="src"' . $src_visibility . '>' . $filename . '</span>
		<span class="delete-image"' . $src_visibility . '>Delete</span>';
						
		echo '<span class="button">Choose Image</span>
			<input ' . $input['attributes'] . ' type="hidden" value="' . $input['value'] . '" /></div><!-- .input-right -->';
		
	}


	public function input_slider($input) {
				
		$input['slider-interval'] = (isset($input['slider-interval'])) ? $input['slider-interval'] : 1;
			
		echo '<div class="input-left">
				<label>' . $input['label'] . '</label>
			</div><!-- .input-left -->
	
			<div class="input-right">
				<div class="input-slider-bar" slider_min="' . $input['slider-min'] . '" slider_max="' . $input['slider-max'] . '" slider_interval="' . $input['slider-interval'] . '"></div><!-- .input-slider-bar -->
			
				<div class="input-slider-bar-text">

					<input type="number" value="' . $input['value'] . '" ' . $input['attributes'] . ' class="input-slider-bar-input" min="' . $input['slider-min'] . '" max="' . $input['slider-max'] . '" step="' . $input['slider-interval'] . '" pattern="\d*" />';
	
		if ( isset($input['unit']) && $input['unit'] !== false ) echo '<span class="slider-unit">' . $input['unit'] . '</span>';
		
		echo '</div><!-- .input-slider-bar-text -->';
		echo '</div><!-- .input-right -->';

		echo '<input type="hidden" value="' . $input['value'] . '" ' . $input['attributes'] . ' class="input-slider-bar-hidden" />';

	}


	public function input_heading($input) {

		echo '
			<h3 class="options-heading">' . $input['label'] . '</h3>
		';
		
	}


	public function input_notice($input) {

		echo '<div class="sub-tab-notice">' . $input['notice'] . '</div>';

	}


	public function input_raw_html($input) {

		echo $input['html'];

	}


	public function input_button($input) {
							
		if ( isset($input['label']) && !empty($input['label']) ) {

			echo '<div class="input-left">
					<label>' . $input['label'] . '</label>
				</div><!-- .input-left -->';

		}
	
		echo '<div class="input-right">
				<span class="button" ' . $input['attributes'] . '>' . $input['button-label'] . '</span>
			</div><!-- .input-right -->';

	}


	public function input_import_file($input) {
							
		if ( isset($input['label']) && !empty($input['label']) ) {

			echo '<div class="input-left">
					<label>' . $input['label'] . '</label>
				</div><!-- .input-left -->';

		}

		echo '<div class="input-right">';
								
			echo '<span class="button">' . $input['button-label'] . '</span>';
			echo '<input type="file" ' . $input['attributes'] . ' />';

			$src_visibility = ( $input['value'] !== null && is_string($input['value']) ) ? '' : ' style="display:none;"';

			$file_fragments = explode( '/', $input['value'] );
			$filename = end($file_fragments);

			echo '<span class="src"' . $src_visibility . '>' . $filename . '</span>
			<span class="delete-file"' . $src_visibility . '>Delete</span>';

		echo '</div><!-- .input-right -->';

	}


}
