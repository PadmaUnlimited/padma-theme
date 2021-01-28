<?php
/**
 * Padma Unlimited Theme.
 *
 * @package padma
 */

/**
 * Block API main class.
 */
abstract class PadmaBlockAPI {


	public $id;
	public $name;
	public $block_type_url;
	public $block_type_path;
	public $block_type_icons;
	public $options_class 	= 'PadmaBlockOptionsAPI';		
	public $fixed_height 	= false;	
	public $html_tag 		= 'div';
	public $attributes 		= array();
	public $description 	= false;
	public $allow_titles 	= true;
	public $categories 		= array();
	public $inline_editable = array('block-title', 'block-subtitle');
	public $inline_editable_equivalences = array();



	/* System Properties (DO NOT USE OR TOUCH) */	
	protected $options;

	protected $show_content_in_grid = false;

	/**
	 * System Methods (DO NOT EXTEND OR MODIFY).
	 * return void
	 */
	public function register() {

		global $padma_block_types;

		// If the Padma blocks array doesn't exist, create it.
		if ( ! is_array( $padma_block_types ) ) {
			$padma_block_types = array();
		}

		/**
		 * Inline editable fields
		 * PadmaAudioBlock::inline_editable
		 * example 1:
		 * public $inline_editable = array('block-title', 'block-subtitle', 'prefix-text', 'separator');
		 * example 2:
		 * public $inline_editable = array('block-title', 'block-subtitle', array('title' => 'su-box-title'));
		 */
		$inline_editable_fields = array();

		if ( is_array( $this->inline_editable ) ) {
			foreach ( $this->inline_editable as $key => $editable_field_and_class ) {
				if ( is_array( $editable_field_and_class ) ) {
					foreach ( $editable_field_and_class as $field => $css_class ) {
						$inline_editable_fields[] = $css_class;
					}
				} else {
					$inline_editable_fields[] = $editable_field_and_class;
				}
			}
		}

		// Add block to array.  This array will be used for checking if certain blocks exist, the block type selector and so on.
		// Floating blocks are created on the fly, it change the id so DO NOT use css or js based on the id.
		$padma_block_types[ $this->id ] = array(
			'name'                         => $this->name,
			'url'                          => $this->block_type_url,
			'path'                         => $this->block_type_path,
			'icons'                        => $this->block_type_icons,
			'class'                        => get_class( $this ),
			'fixed-height'                 => $this->fixed_height,
			'html-tag'                     => $this->html_tag,
			'attributes'                   => $this->attributes,
			'show-content-in-grid'         => $this->show_content_in_grid,
			'allow-titles'                 => $this->allow_titles,
			'description'                  => $this->description,
			'categories'                   => $this->categories,
			'inline-editable'              => implode( ',', $inline_editable_fields ),
			'inline-editable-equivalences' => $this->inline_editable_equivalences,
		);

		// Add the element for the block itself.
		add_action( 'padma_register_elements', array( $this, 'setup_main_block_element' ) );

		// Run init method if it exists.
		if ( method_exists( $this, 'init' ) ) {
			$this->init();
		}

		// Run setup_elements if it exists.
		if ( method_exists( $this, 'setup_elements' ) ) {
			add_action( 'padma_register_elements', array( $this, 'setup_elements' ) );
		}

		// Setup hooks.
		if ( $this->allow_titles ) {
			add_action( 'padma_block_content_' . $this->id, array( $this, 'title_and_subtitle' ) );
		}

		add_action( 'padma_block_content_' . $this->id, array( $this, 'content' ) );

		add_action( 'padma_block_options_' . $this->id, array( $this, 'options_panel' ), 10, 2 );
	}


	public function setup_main_block_element() {

		PadmaElementAPI::register_element(array(
			'group' 		=> 'blocks',
			'id' 			=> 'block-' . $this->id,
			'name' 			=> $this->name,
			'selector' 		=> '.block-type-' . $this->id,
			'properties' 	=> array(
									'fonts',
									'background',
									'borders',
									'outlines',
									'padding',
									'margins',
									'corners',
									'box-shadow',
									'lists',
									'nudging',
									'overflow',
									'sizes',
									'animation',
									'transform',
									'transition',
									'advanced',
									'filter',
									'flexbox'
								),


			));

		if ( $this->allow_titles ) {

			$this->register_block_element(array(
				'id' => 'block-title',
				'name' => 'Block Title',
				'selector' => '.block-title'
			));

			$this->register_block_element(array(
				'id' => 'block-title-span',
				'name' => 'Block Title Inner',
				'parent' => 'block-title',
				'selector' => '.block-title span',
				'inspectable' => false
			));


			$this->register_block_element(array(
				'id' => 'block-title-link',
				'name' => 'Block Title Link',
				'parent' => 'block-title',
				'selector' => '.block-title a',
				'inspectable' => false
			));

			$this->register_block_element(array(
				'id' => 'block-subtitle',
				'name' => 'Block Subtitle',
				'selector' => '.block-subtitle'
			));

		}

	}


	public function options_panel($block, $layout) {

		if ( !class_exists($this->options_class) )
			return new WP_Error('block_options_class_does_not_exist', __('Error: The block options class being registered does not exist.', 'padma'), $this->options_class);

		//Initiate options class
		$options = new $this->options_class($this);
		$options->display($block, $layout);

	}


	/* Methods to extend (you can modify these!) */
	public function content($block) {

	}


	public function title_and_subtitle($block) {

		if ( !$this->allow_titles )
			return;

		/* Output Block Titles */
		if ( padma_get('original', $block) )
			$block = padma_get('original', $block);

		$block_settings          = padma_get( 'settings', $block );		
		$block_title             = padma_get( 'block-title', padma_get( 'settings', $block, array() ) );
		$block_title_tag         = padma_get( 'block-title-tag', padma_get( 'settings', $block, array() ) );
		$block_subtitle          = padma_get( 'block-subtitle', padma_get( 'settings', $block, array() ) );
		$block_subtitle_tag      = padma_get( 'block-subtitle-tag', padma_get( 'settings', $block, array() ) );
		$block_title_link_url    = padma_get( 'block-title-link-url', padma_get( 'settings', $block, array() ) );
		$block_title_link_target = padma_get( 'block-title-link-target', $block_settings, false, true ) ? $target = ' target="_blank"' : '';
		$block_title_link_rel 	 = padma_get( 'block-title-link-rel', $block_settings, 'noreferrer' );

		if ( $block_title || $block_subtitle ) {

			/* Open hgroup if necessary */
			if ( $block_title && $block_subtitle ) {
				echo '<hgroup>';
			}

			/* Title */
			if ( $block_title ) {
				if ( padma_get( 'block-title-link-check', $block_settings, false ) ) {
					if($block_title_tag == ''){
						echo '<h1 class="block-title"><a href="' . padma_fix_data_type( $block_title_link_url ) . '"' . $block_title_link_target . ' rel="'.$block_title_link_rel.'"><span>' . padma_fix_data_type( $block_title ) . '</span></a></h1>';
					}else{
						echo '<'.$block_title_tag.' class="block-title"><a href="' . padma_fix_data_type( $block_title_link_url ) . '"' . $block_title_link_target . ' rel="'.$block_title_link_rel.'"><span>' . padma_fix_data_type( $block_title ) . '</span></a></'.$block_title_tag.'>';
					}
				} else {
					if($block_title_tag == ''){
						echo '<h1 class="block-title"><span>' . padma_fix_data_type( $block_title ) . '</span></h1>';
					}else{
						echo '<'.$block_title_tag.' class="block-title"><span>' . padma_fix_data_type( $block_title ) . '</span></'.$block_title_tag.'>';
					}
				}
			}

			/* Subtitle */
			if ( $block_subtitle ) {
				if($block_subtitle_tag == ''){
					echo '<h2 class="block-subtitle">' . padma_fix_data_type( $block_subtitle ) . '</h2>';
				}else{
					echo '<'.$block_subtitle_tag.' class="block-subtitle">' . padma_fix_data_type( $block_subtitle ) . '</'.$block_subtitle_tag.'>';				
				}
			}

			/* Close hgroup */
			if ( $block_title && $block_subtitle ) {
				echo '</hgroup>';
			}

		}

	}


	/**
	 * The following are commented out so they are not detected 
	 * 
	 *  public static function init_action($block_id, $block) {
	 *  
	 *  }
	 * 
	 * 
	 *  public static function enqueue_action($block_id, $block, $original_block = null) {
	 *  
	 *  }
	 *
	 *
	 *  public static function dynamic_css($block_id, $block, $original_block = null) {
	 *  
	 *  }
 	 *
     *
	 *  public static function dynamic_js($block_id, $block, $original_block = null) {
	 *  
	 *  }
	 * 
	 **/


	/* Methods to use, but not modify! */
	public function register_block_element($args) {

		/* Add the selector prefix to the selector and even handle commas */
		$selector_prefix = '.block-type-' . $this->id . ' ';

		$selector_array = explode(',', $args['selector']);

		foreach ( $selector_array as $selector_index => $selector ) {

			if ( strpos(trim($selector_array[$selector_index]), '.block-type-') === 0 )
				continue;


			// Allow selector outsite the block			
			if( strlen($selector) > 0 && $selector[0] === '\\' ){			
				$selector = str_replace('\\', '', $selector);				
				$selector_array[$selector_index] = trim($selector);
				$args['tooltip'] = 'Warning: Apply style to this element affects all the instances';
			}else{
				$selector_array[$selector_index] = $selector_prefix . trim($selector);
			}

		}

		$modified_selector = implode(',', $selector_array);	
		/* End Selector Modification */

		if(empty($args['id']))
			$args['id'] = '';

		$defaults = array(
			'group' 	=> 'blocks',
			'parent' 	=> 'block-' . $this->id,
			'id' 		=> 'block-' . $this->id . '-' . $args['id'],
			'name' 		=> $args['name'],
			'selector'	=> $modified_selector
		);


		//Unset the following so they don't overwrite the defaults
		unset($args['id']);
		unset($args['name']);
		unset($args['selector']);

		//If the parent isn't the default then put on block type prefix
		if ( !empty($args['parent']) && $args['parent'] != 'block-' . $this->id )
			$args['parent'] = 'block-' . $this->id . '-' . $args['parent'];

		$element = array_merge($defaults, $args);

		//Go through states and add the selector prefix to each state
		if ( isset($element['states']) && is_array($element['states']) ) {

			foreach ( $element['states'] as $state_name => $state_selector ) {

				$state_selector_array = explode(',', $state_selector);

				foreach ( $state_selector_array as $selector_index => $selector ) {

					if ( strpos(trim($state_selector_array[$selector_index]), '.block-type-') === 0 )
						continue;

					// Allow selector outsite the block
					if( $selector[0] === '\\' ){			
						$selector = str_replace('\\', '', $selector);						
						$state_selector_array[$selector_index] = trim($selector);
					}else{
						$state_selector_array[$selector_index] = $selector_prefix . trim($selector);
					}

					

				}

				$element['states'][$state_name] = trim(implode(',', $state_selector_array));

			}

		}


		return PadmaElementAPI::register_element($element);

	}


	public static function get_setting($block, $setting, $default = null) {

		return PadmaBlocksData::get_block_setting($block, $setting, $default);

	}

}
