<?php
class BloxLayoutRenderer {


	private $id;


	private $blocks;


	private $wrappers;


	public function __construct() {

		$this->id = BloxLayout::get_current_in_use();
		$this->blocks = BloxBlocksData::get_blocks_by_layout($this->id);
		
		$this->wrappers = BloxWrappersData::get_wrappers_by_layout($this->id);

	}


	public function display() {

		if ( !$this->blocks )
			return $this->display_no_blocks_message();

		foreach ( $this->wrappers as $wrapper_id => $wrapper ) {

			$wrapper_id_for_blocks = $wrapper_id;
			$wrapper_settings = blox_get('settings', $wrapper, array());

			/* Check if mirroring.  If mirroring, change wrapper ID to the wrapper being mirrored and preserve original ID for a later class */
				if ( $wrapper_being_mirrored = BloxWrappersData::get_wrapper_mirror($wrapper) ) {

					$mirrored_wrapper_id = $wrapper_being_mirrored['id'];
					$wrapper_id_for_blocks = $mirrored_wrapper_id;

					foreach ( BloxBlocksData::get_blocks_by_wrapper($wrapper_being_mirrored['layout'], $mirrored_wrapper_id) as $block_from_mirrored_wrapper )
						$this->blocks[$block_from_mirrored_wrapper['id']] = $block_from_mirrored_wrapper;

				}

			/* Grab blocks belonging to this wrapper */
				$wrapper_blocks = array();

				foreach ( $this->blocks as $block_id => $block ) {

					if ( blox_get('wrapper_id', $block, BloxWrappers::$default_wrapper_id) == $wrapper_id_for_blocks )
						$wrapper_blocks[$block_id] = $block;

					/* If there's only one wrapper and the block does not have a proper ID or is default, move it to that wrapper */
					if ( count($this->wrappers) === 1 && (blox_get('wrapper_id', $block) === null || blox_get('wrapper_id', $block) == 'wrapper-default' || !isset($this->wrappers[blox_get('wrapper_id', $block)])) )
						$wrapper_blocks[$block_id] = $block;

				}

			/* Setup wrapper classes */
				$wrapper_id    = BloxWrappersData::get_legacy_id( $wrapper );
				$wrapper['original-id'] = $wrapper['id'];
				$wrapper['id'] = BloxWrappersData::get_legacy_id( $wrapper );


				$wrapper_columns = BloxWrappers::get_columns($wrapper);
				$wrapper_column_width = BloxWrappers::get_column_width($wrapper);
				$wrapper_gutter_width = BloxWrappers::get_gutter_width($wrapper);

				$wrapper_classes = array('wrapper');

				$wrapper_classes[] = BloxWrappers::is_independent_grid($wrapper) ? 'independent-grid' : null;
				$wrapper_classes[] = BloxWrappers::is_fluid($wrapper) ? 'wrapper-fluid' : 'wrapper-fixed';
				$wrapper_classes[] = BloxWrappers::is_grid_fluid($wrapper) ? 'wrapper-fluid-grid' : 'wrapper-fixed-grid';
				$wrapper_classes[] = 'grid-' . (BloxWrappers::is_grid_fluid($wrapper) || BloxResponsiveGrid::is_enabled() ? 'fluid' : 'fixed') . '-' . $wrapper_columns . '-' . $wrapper_column_width . '-' . $wrapper_gutter_width;

				$wrapper_classes[] = BloxResponsiveGrid::is_active() ? 'responsive-grid' : null;
				$wrapper_classes[] = $wrapper_being_mirrored ? 'wrapper-mirroring-' . BloxWrappersData::get_legacy_id($wrapper_being_mirrored) : null;

				$last_wrapper_id = array_slice(array_keys($this->wrappers), -1, 1);
				$last_wrapper_id = $last_wrapper_id[0];

				$first_wrapper_id = array_keys($this->wrappers);
				$first_wrapper_id = $first_wrapper_id[0];

				if ( $last_wrapper_id == $wrapper['original-id'] )
					$wrapper_classes[] = 'wrapper-last';
				else if ( $first_wrapper_id == $wrapper['original-id'] )
					$wrapper_classes[] = 'wrapper-first';

				/* Custom wrapper classes */
				$custom_css_classes = str_replace('  ', ' ', str_replace(',', ' ', esc_attr(strip_tags(blox_get('css-classes', $wrapper_settings, '')))));
				$wrapper_classes = array_merge($wrapper_classes, explode(' ', $custom_css_classes));

				/* Visual Editor Attributes */
				$wrapper_visual_editor_attributes = '';

				if ( BloxRoute::is_visual_editor_iframe() ) {
					$wrapper_visual_editor_attributes = ' data-id="' . $wrapper['original-id'] . '" data-custom-classes="' .  trim($custom_css_classes) . '"';
				}

			/* Display the wrapper */	
				do_action('blox_before_wrapper');
			
				echo '<div id="wrapper-' . $wrapper_id . '" class="' . implode(' ', array_unique(array_filter($wrapper_classes))) . '" data-alias="' . esc_attr( blox_get( 'alias', blox_get( 'settings', $wrapper, array() )) ) . '"' . $wrapper_visual_editor_attributes . '>' . "\n\n";
				
					do_action('blox_wrapper_open');

						$wrapper = new BloxGridRenderer($wrapper_blocks, $wrapper_settings);
						$wrapper->render_grid();
					
					do_action('blox_wrapper_close');
				
				echo '</div>' . "\n\n";
				
				do_action('blox_after_wrapper');
			/* End displaying wrapper */

		}

	}


	private function display_no_blocks_message() {
		
		echo '<div class="wrapper wrapper-no-blocks wrapper-fixed" id="wrapper-default">' . "\n\n";
			
			echo '<div class="block-type-content">';
		
				echo '<div class="entry-content">';
			
					echo '<h1 class="entry-title">' . __('No Content to Display', 'blox') . '</h1>';
		
					$visual_editor_url = add_query_arg(array('visual-editor' => 'true', 'visual-editor-mode' => 'grid', 've-layout' => urlencode(BloxLayout::get_current())), home_url());
					
					if ( BloxCapabilities::can_user_visually_edit() ) {
			
						echo sprintf(__('<p>There are no blocks to display.  Add some by going to the <a href="%s">Blox Grid</a>!</p>', 'blox'), $visual_editor_url);
			
					} else {
													
						echo sprintf(__('<p>There is no content to display.  Please notify the site administrator or <a href="%s">login</a>.</p>', 'blox'), $visual_editor_url);
										
					}
			
				echo '</div>';
			
			echo '</div>';
				
		echo '</div>';
		
		return false;
		
	}


}