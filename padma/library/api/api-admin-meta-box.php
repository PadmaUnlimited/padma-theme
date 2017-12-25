<?php
/**
 * See https://core.trac.wordpress.org/ticket/26809
 */
add_action('edit_form_after_editor', 'padma_meta_padma_save_post_template_bypass');
function padma_meta_padma_save_post_template_bypass() {

	global $post;

	if ( 'page' == $post->post_type && !count(wp_get_theme()->get_page_templates()) ) {

		echo '
		<!--
		Added by Padma
		See: https://core.trac.wordpress.org/ticket/26809
		-->

		<input type="hidden" name="page_template" value="default" />

		';

	}

}


function padma_register_admin_meta_box($class) {

	add_action('init', create_function('', 'return padma_register_admin_meta_padma_callback(\'' . $class . '\');'), 15);
	
}


function padma_register_admin_meta_padma_callback($class) {
		
	if ( !class_exists($class) )
		return new WP_Error('meta_padma_class_does_not_exist', __('Error: The meta box class being registered does not exist.', 'padma'), $class);
	
	$meta_box = new $class();
	$meta_box->register();
	
	return true;
	
}


abstract class PadmaAdminMetaBoxAPI {
	
	/**
	 * Simple ID for the meta box.  Must not contain spaces.
	 **/
	protected $id;
	
	/**
	 * Name of the meta box.  This will show at the top of the box.
	 **/
	protected $name;
	
	/**
	 * Array containing all of the inputs
	 **/
	protected $inputs;
	
	/**
	 * Array (multiple post types) or string (one post type) defining which post types this meta box will be used with.
	 **/
	protected $post_types;
	
	/**
	 * Location of the meta box.  Generally 'advanced' or 'side'.
	 **/
	protected $context;
	
	/**
	 * Integer of where the meta box will be located vertically.
	 **/
	protected $priority;
	
	/**
	 * Can be used to show a simple notice box above the inputs.
	 **/
	protected $info;
	
	/**
	 * Argument to be used in the post_type_supports() function to check against this meta box.
	 **/
	protected $post_type_supports_id;
	
	
	public function register() {
		
		//Check for at least a name and ID
		if ( !isset($this->id) )
			return false;
			
		if ( !isset($this->name) )
			return false;
		
		//Set up default variables
		$this->post_types 				= isset($this->post_types) 				? $this->post_types 			: array('post', 'page');
		$this->context 					= isset($this->context) 				? $this->context 				: 'advanced';
		$this->priority 				= isset($this->priority) 				? $this->priority 				: 'low';
		$this->post_type_supports_id 	= isset($this->post_type_supports_id) 	? $this->post_type_supports_id 	: 'padma-admin-meta-box-' . $this->id;
		
		//Add the prefix to the ID
		$this->id = 'padma-admin-meta-box-' . $this->id;
		
		//Change post types to array if it is a string
		if ( is_string($this->post_types) )
			$this->post_types = array($this->post_types);
		
		//Set up hooks
		add_action('admin_init', array($this, 'init'));
		add_action('save_post', array($this, 'save'), 10, 2);
		
	}
	
	
	public function init() {
		
		//Register the meta box on the specified post types
		foreach ($this->post_types as $post_type) {
			add_meta_box($this->id, $this->name, array($this, 'box'), $post_type, $this->context, $this->priority);
		}
		
		//Register the meta box on the post types that "support" the meta box
		foreach ((array)get_post_types(array('public' => true)) as $post_type) {
						
			if ( post_type_supports($post_type, $this->post_type_supports_id) )
				add_meta_box($this->id, $this->name, array($this, 'box'), $post_type, $this->context, $this->priority);
			
		}
		
	}
	
	
	public function box() {
		
		global $post;
		
		//Create the nonce
		echo '<input type="hidden" name="' . $this->id . '_nonce" id="' . $this->id . '_nonce" value="' . wp_create_nonce(md5($this->id)) . '" />';
		
		if ( method_exists($this, 'modify_arguments') )
			$this->modify_arguments($post);
		
		if ( isset($this->info) ) 
			echo '<div class="alert alert-yellow"><p>' . $this->info . '</p></div>';
		
		echo '<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table padma-admin-meta-box">';
		
			foreach($this->inputs as $name => $input) {
				
				//Change hyphens to underscores with the input types since methods/functions can't have hyphens
				$input['type'] = str_replace('-', '_', $input['type']);
		
				//The input type doesn't exist--go ahead and skip it
				if ( !method_exists($this, 'input_' . $input['type']) )
					continue;
		
				if ( !isset($input['group']) )
					$input['group'] = 'general';
				
				if ( !isset($input['default']) )
					$input['default'] = null;
					
				$input['attr-id'] = $this->id . '-' . $input['id'];
				$input['attr-name'] = $this->id . '[' . $input['group'] . '][' . $input['id'] . ']';

				$global = $input['id'] == 'template' ? false : true;
		
				$input['value'] = PadmaLayoutOption::get($post->ID, $input['id'], $input['default'], $global, $input['group']);

				if ( padma_get('name', $input) ) {

					echo '
						<tr class="label">
							<th valign="top" scope="row">
								<label for="' . $input['attr-id'] . '">' . $input['name'] . '</label>
							</th>
						</tr>
					';

				}
						
				call_user_func(array($this, 'input_' . $input['type']), $input);
				
				if ( isset($input['description']) ) 
					echo '<tr class="description"><td><p>' . $input['description'] . '</p></td></tr>';
			
			}
					
		echo '</table>';

	}
	
	
	public function save($post_ID) {

		/* Don't try saving meta if it's an autosave */
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return $post_ID;
								
		//Ruh-roh, bad nonce
		if ( !wp_verify_nonce(padma_post($this->id . '_nonce'), md5($this->id)) )
		  	return false;

		// If this is a revision, get real post ID
		if ( $parent_id = wp_is_post_revision( $post_ID ) )
			$post_ID = $parent_id;

		//If there are no options, then there's nothing to save
		if( !isset($_POST[$this->id]) || !is_array($_POST[$this->id]) ) 
			return false;		
		
		//Loop through and set the options
		foreach($_POST[$this->id] as $group => $inputs){	
			
			foreach ( $inputs as $input => $value ) {

				$global = $input == 'template' ? false : true;
				
				PadmaLayoutOption::set($post_ID, $input, $value, $global, $group);

				if ( $input == 'template' ) {
					PadmaLayout::clear_status_transient();
				}
				
			}
		
		}
				
	}
	
	
	protected function modify_arguments($post = false) {
		
		
		
	}
	
	
	protected function input_text($input) {
		
		echo '
			<tr>
				<td>
					<input type="text" value="' . esc_attr($input['value']) . '" id="' . $input['attr-id'] . '" name="' . $input['attr-name'] . '" />
				</td>
			</tr>
		';
			
	}
	
	
	protected function input_textarea($input) {
		
	 	echo '
	 		<tr>
				<td>
					<textarea rows="6" id="' . $input['attr-id'] . '" name="' . $input['attr-name'] . '">'. esc_textarea($input['value']) . '</textarea>
				</td>
			</tr>
		';

	}
	
	
	protected function input_checkbox($input) {
			
		$checked = ( $input['value'] == true && $input['value'] !== '0' ) ? ' checked' : null;	
				
		echo '
			<tr>
				<td colspan="2">
					<label class="selectit" for="' . $input['attr-id'] . '"> 
						<input type="hidden" id="' . $input['attr-id'] . '-hidden" value="false" name="' . $input['attr-name']. '" />
						<input type="checkbox" id="' . $input['attr-id'] . '" value="true" name="' . $input['attr-name']. '" class="check"' . $checked . ' /> ' . $input['name'] . '
					</label>
				</td>
			</tr>';
				
	}
	
	
	protected function input_select($input) {
		
		echo '
			<tr>
				<td>
					<select id="' . $input['attr-id'] . '" name="' . $input['attr-name'] . '">';

						if ( padma_get('blank-option', $input) )
							echo '<option value="">' . padma_get('blank-option', $input) . '</option>';

						foreach( $input['options'] as $value => $text ) {
							
							$selected = $input['value'] === $value ? ' selected' : null;
							
							echo '<option value="' . $value . '"' . $selected . '>' . $text . '</option>';
							
						}
		
		echo '		</select>
				</td>
			</tr>';
			
	}
	
	
	protected function input_radio($input) {
		
		echo '
			<tr>
				<td colspan="2">';

					$count = 0;
		
					$options = array_keys($input['options']);
		
					foreach($input['options'] as $value => $label) {
						
						$count++;
												
						$checked = ($input['value'] == $value ) ? ' checked="checked"' : null;
			
						echo '
							<input type="radio" id="' . $input['attr-id'] . '-' . $value . '" value="' . $value . '" name="' . $input['attr-name'] . '" class="check"' . $checked . ' />
							 
							<label class="selectit" for="' . $input['attr-id'] . '-' . $value  . '"> 
								' . $label . '
							</label>
						';
						
						if ( $count !== count($input['options']) )
							echo '<br />';
							
					}
					
		echo '			
				</td>
			</tr>
		';
		
	}
	
	
	protected function input_pages($input) {
		
		echo '
			<tr>
				<td>' . 
					wp_dropdown_pages(array(
						'selected' => $input['value'], 
						'name' => $input['attr-name'], 
						'id' => $input['attr-id'],
						'show_option_none' => '   ', 
						'sort_column'=> 'menu_order, post_title', 
						'echo' => false
					)) . '
				</td>
			</tr>
		';
								
	}
	

}