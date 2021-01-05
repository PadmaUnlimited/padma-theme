<?php
class PadmaLayoutRenderer {


	private $id;
	private $blocks;
	private $wrappers;


	public function __construct() {

		$this->id 		= PadmaLayout::get_current_in_use();
		$this->blocks 	= PadmaBlocksData::get_blocks_by_layout($this->id);		
		$this->wrappers = PadmaWrappersData::get_wrappers_by_layout($this->id);

	}


	public function display() {

		if ( !$this->blocks )
			return $this->display_no_blocks_message();



		/*	Add Animation rules to layout?	*/		
		$animation_rules = array();

		foreach ( $this->wrappers as $wrapper_id => $wrapper ) {

			$wrapper_id_for_blocks 	= $wrapper_id;
			$wrapper_settings 		= padma_get('settings', $wrapper, array());

			/* Check if mirroring.  If mirroring, change wrapper ID to the wrapper being mirrored and preserve original ID for a later class */
			if ( $wrapper_being_mirrored = PadmaWrappersData::get_wrapper_mirror($wrapper) ) {

				$mirrored_wrapper_id 	= $wrapper_being_mirrored['id'];
				$wrapper_id_for_blocks 	= $mirrored_wrapper_id;

				foreach ( PadmaBlocksData::get_blocks_by_wrapper($wrapper_being_mirrored['layout'], $mirrored_wrapper_id) as $block_from_mirrored_wrapper ){
					$this->blocks[$block_from_mirrored_wrapper['id']] = $block_from_mirrored_wrapper;
				}

			}

			/* Grab blocks belonging to this wrapper */
			$wrapper_blocks = array();

			foreach ( $this->blocks as $block_id => $block ) {

				if ( padma_get('wrapper_id', $block, PadmaWrappers::$default_wrapper_id) == $wrapper_id_for_blocks )
					$wrapper_blocks[$block_id] = $block;

				/* If there's only one wrapper and the block does not have a proper ID or is default, move it to that wrapper */
				if ( count($this->wrappers) === 1 && (padma_get('wrapper_id', $block) === null || padma_get('wrapper_id', $block) == 'wrapper-default' || !isset($this->wrappers[padma_get('wrapper_id', $block)])) )
					$wrapper_blocks[$block_id] = $block;


				// Add animation rules ?
				if( !empty($block['settings']['animation-rules']) ){					
					foreach ($block['settings']['animation-rules'] as $key => $value) {
						$animation_rules[$key] = $value;
					}					
				}

			}

			/* Setup wrapper classes */
			$wrapper_id    			= PadmaWrappersData::get_legacy_id( $wrapper );
			$wrapper['original-id'] = $wrapper['id'];
			$wrapper['id'] 			= PadmaWrappersData::get_legacy_id( $wrapper );


			$wrapper_columns 		= PadmaWrappers::get_columns($wrapper);
			$wrapper_column_width 	= PadmaWrappers::get_column_width($wrapper);
			$wrapper_gutter_width 	= PadmaWrappers::get_gutter_width($wrapper);

			if( PadmaWrappers::is_independent_grid($wrapper) ){
				$wrapper_grid_system = PadmaWrappers::get_grid_system($wrapper);	
			}else{
				$wrapper_grid_system = PadmaSkinOption::get('grid-system', false, 'css-grid');
			}

			$wrapper_classes 		= array('wrapper');

			$wrapper_classes[] 		= PadmaWrappers::is_independent_grid($wrapper) ? 'independent-grid' : null;
			$wrapper_classes[] 		= PadmaWrappers::is_fluid($wrapper) ? 'wrapper-fluid' : 'wrapper-fixed';
			$wrapper_classes[] 		= PadmaWrappers::is_grid_fluid($wrapper) ? 'wrapper-fluid-grid' : 'wrapper-fixed-grid';
			$wrapper_classes[] 		= 'grid-' . (PadmaWrappers::is_grid_fluid($wrapper) || PadmaResponsiveGrid::is_enabled() ? 'fluid' : 'fixed') . '-' . $wrapper_columns . '-' . $wrapper_column_width . '-' . $wrapper_gutter_width;

			$wrapper_classes[] 		= PadmaResponsiveGrid::is_active() ? 'responsive-grid' : null;
			$wrapper_classes[] 		= $wrapper_being_mirrored ? 'wrapper-mirroring-' . PadmaWrappersData::get_legacy_id($wrapper_being_mirrored) : null;

			$last_wrapper_id 		= array_slice(array_keys($this->wrappers), -1, 1);
			$last_wrapper_id 		= $last_wrapper_id[0];

			$first_wrapper_id 		= array_keys($this->wrappers);
			$first_wrapper_id 		= $first_wrapper_id[0];

			if ( $last_wrapper_id == $wrapper['original-id'] )
				$wrapper_classes[] = 'wrapper-last';
			else if ( $first_wrapper_id == $wrapper['original-id'] )
				$wrapper_classes[] = 'wrapper-first';

			/* Custom wrapper classes */
			$custom_css_classes 	= str_replace('  ', ' ', str_replace(',', ' ', esc_attr(strip_tags(padma_get('css-classes', $wrapper_settings, '')))));
			$wrapper_classes 		= array_merge($wrapper_classes, explode(' ', $custom_css_classes));

			/* Visual Editor Attributes */
			$wrapper_visual_editor_attributes = '';

			if ( PadmaRoute::is_visual_editor_iframe() ) {
				$wrapper_visual_editor_attributes = ' data-id="' . $wrapper['original-id'] . '" data-custom-classes="' .  trim($custom_css_classes) . '"';
			}

			/* Display the wrapper */	
			do_action('padma_before_wrapper');

			if( $wrapper_grid_system == 'css-grid' ){
				$wrapper_classes[] = 'css-grid';
			}

			$wrapper_classes = implode(' ', array_unique(array_filter($wrapper_classes)));
			$wrapper_data_alias = esc_attr( padma_get( 'alias', padma_get( 'settings', $wrapper, array() )) );

			echo '<div id="wrapper-' . $wrapper_id . '" class="' . $wrapper_classes . '" data-alias="' . $wrapper_data_alias . '"' . $wrapper_visual_editor_attributes . '>';

				do_action('padma_wrapper_open');

						$wrapper = new PadmaGridRenderer($wrapper_blocks, $wrapper_settings);
						if( 'css-grid' === $wrapper_grid_system ) {
							$wrapper->render_grid_css();
						}else{
							$wrapper->render_grid();
						}

					do_action('padma_wrapper_close');

				echo '</div>';

				do_action('padma_after_wrapper');
			/* End displaying wrapper */

		}



		if( !empty( $animation_rules ) ){
					
			wp_enqueue_script( 'padma-animation-rules', padma_url() . '/library/media/js/animation-rules.js', array( 'jquery' ) );
			wp_localize_script( 'padma-animation-rules', 'PadmaAnimationRulesSelectors', $animation_rules );

		}

	}


	private function display_no_blocks_message() {

		echo '<div class="wrapper wrapper-no-blocks wrapper-fixed" id="wrapper-default">';

			echo '<div class="block-type-content">';

				echo '<div class="entry-content">';

					echo '<h1 class="entry-title">' . __('No Content to Display', 'padma') . '</h1>';

					$visual_editor_url = add_query_arg(array('visual-editor' => 'true', 'visual-editor-mode' => 'grid', 've-layout' => urlencode(PadmaLayout::get_current())), home_url());

					if ( PadmaCapabilities::can_user_visually_edit() ) {

						echo sprintf(__('<p>There are no blocks to display.  Add some by going to the <a href="%s">Padma Grid</a>!</p>', 'padma'), $visual_editor_url);

					} else {

						echo sprintf(__('<p>There is no content to display.  Please notify the site administrator or <a href="%s">login</a>.</p>', 'padma'), $visual_editor_url);

					}

				echo '</div>';

			echo '</div>';

		echo '</div>';

		return false;

	}


}