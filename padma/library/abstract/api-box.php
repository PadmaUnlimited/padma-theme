<?php

abstract class PadmaVisualEditorBoxAPI {
	
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
	
	protected $resizable = true;
	
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
		
		global $padma_registered_ve_boxes;
		
		if ( !isset($padma_registered_ve_boxes) )
			$padma_registered_ve_boxes = array();
		
		if ( in_array($this->id, $padma_registered_ve_boxes) )
			return false;
		
		$mode = PadmaVisualEditor::get_current_mode();
				
		if ( $this->mode === 'all' || strtolower($this->mode) === strtolower($mode) )
			add_action('padma_visual_editor_boxes', array($this, 'build_box'));		
				
		if ( $this->load_with_ajax )
			add_action('padma_visual_editor_ajax_box_content_' . $this->id, array($this, 'content'));
		
		$padma_registered_ve_boxes[] = $this->id;
				
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