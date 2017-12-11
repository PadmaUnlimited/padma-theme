<?php
function blox_register_visual_editor_box($class) {

	add_action('blox_visual_editor_display_init', create_function('', 'return blox_register_visual_editor_box_callback(\'' . $class . '\');'));
	
}


function blox_register_visual_editor_box_callback($class) {
		
	if ( !class_exists($class) )
		return new WP_Error('box_class_does_not_exist', __('Error: The box class being registered does not exist.', 'blox'), $class);
	
	$box = new $class();
	$box->register();
	
	return true;
	
}


abstract class BloxVisualEditorBoxAPI {
	
	
	/**
	 *	Slug/ID of panel.  Will be used for HTML IDs and whatnot.
	 **/
	protected $id;
	
	
	/**
	 * Name of panel.  This will be shown in the title.
	 **/
	protected $title;
	
	protected $description = false;
	
	
	/**
	 * Which mode to put the panel on.
	 **/
	protected $mode = 'all';
		
	protected $width = 500;
		
	protected $height = 300;
	
	protected $center = true;
	
	protected $closable = true;
	
	protected $draggable = true;
	
	protected $resizable = false;
	
	protected $min_width = 500;
	
	protected $min_height = 300;
	
	protected $black_overlay = false;
	
	protected $black_overlay_opacity = 0.6;
	
	protected $black_overlay_iframe = false;
	
	protected $load_with_ajax = false;
	
	protected $load_with_ajax_callback = false;
			
	
	/**
	 * Register the panel.
	 * 
	 * @param string Name of panel to be displayed
	 * @param string ID of panel for HTML and options
	 **/
	public function register() {
		
		global $blox_registered_ve_boxes;
		
		if ( !isset($blox_registered_ve_boxes) )
			$blox_registered_ve_boxes = array();
		
		if ( in_array($this->id, $blox_registered_ve_boxes) )
			return false;
		
		$mode = BloxVisualEditor::get_current_mode();
				
		if ( $this->mode === 'all' || strtolower($this->mode) === strtolower($mode) )
			add_action('blox_visual_editor_boxes', array($this, 'build_box'));		
				
		if ( $this->load_with_ajax )
			add_action('blox_visual_editor_ajax_box_content_' . $this->id, array($this, 'content'));
		
		$blox_registered_ve_boxes[] = $this->id;
				
	}
	

	public function build_box() {
		
		$attributes = array(
			'class' => 'box',
			'id' => 'box-' . $this->id,
			'width' => $this->width,
			'height' => $this->height,
			'center' => $this->center,
			'closable' => $this->closable,
			'draggable' => $this->draggable,
			'resizable' => $this->resizable,
			'min_width' => $this->min_width,
			'min_height' => $this->min_height,
			'black_overlay' => $this->black_overlay,
			'black_overlay_opacity' => $this->black_overlay_opacity,
			'black_overlay_iframe' => $this->black_overlay_iframe,
			'load_with_ajax' => $this->load_with_ajax,
			'load_with_ajax_callback' => esc_attr('(function(args){' . $this->load_with_ajax_callback . '})')
		);
		
		$attributes_string = '';
		
		foreach ( $attributes as $attribute => $value ) 
			$attributes_string .= $attribute . '="' . $value . '" ';
		
		echo '<div ' . trim($attributes_string) . '>';
		
			echo '<div class="box-top">';
			
				echo '<strong>' . $this->title . '</strong>';
				
				if ( $this->description ) {
					
					echo '<span>' . $this->description . '</span>';
					
				}
			
			echo '</div>';
			
			echo '<div class="box-content">';
				
				if ( !$this->load_with_ajax )
					$this->content();
				
			echo '</div>';

		echo '</div><!-- div#box-' . $this->id . ' -->';
		
	}
	
	
	public function content() {
		
	}
		
	
}