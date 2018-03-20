<?php
class PadmaBlocksData {

	
	public static function add_block($layout_id, $args) {

		global $wpdb;

		/* Validate input */
		if ( !$args || !is_array($args) )
			return false;
		
		if ( !padma_get('type', $args) )
			return new WP_Error('pu_add_block_missing_type');

		if ( !is_array($args['dimensions']) && !is_serialized($args['dimensions']) )
			return new WP_Error('pu_add_block_missing_dimensions');

		if ( !is_array($args['position']) && !is_serialized($args['position']) )
			return new WP_Error('pu_add_block_missing_position');

		/* Make sure the arrays are all unserialized */
		$args['position'] = padma_maybe_unserialize($args['position']);
		$args['dimensions'] = padma_maybe_unserialize($args['dimensions']);
		$args['settings'] = padma_maybe_unserialize($args['settings']);

		//Figure out mirror ID
		$mirror_id = padma_get('mirror-block', padma_get('settings', $args, array()));

		//Unset old mirror ID
		if ( isset($args['settings']['mirror-block']) )
			unset($args['settings']['mirror-block']);

		//Build insert args
		$random_prefix = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 2)), 0, 2);

		$insert_args = array(
			'id' => uniqid('b' . strtolower(substr($random_prefix, 0, 2))),
			'template' => padma_get('template', $args, PadmaOption::$current_skin),
			'layout' => $layout_id,
			'type' => $args['type'],
			'wrapper_id' => padma_get('wrapper', $args),
			'position' => padma_maybe_serialize($args['position']),
			'dimensions' => padma_maybe_serialize($args['dimensions']),
			'settings' => padma_maybe_serialize(padma_get('settings', $args, array())),
			'mirror_id' => $mirror_id
		);

		//Is a pre-defined ID required?
		if ( $insert_id = padma_get( 'insert_id', $args ) )
			$insert_args['id'] = $insert_id;

		if ( $legacy_id = padma_get( 'legacy_id', $args ) )
			$insert_args['legacy_id'] = $legacy_id;

		//Run the query
		$wpdb->insert($wpdb->pu_blocks, $insert_args);

		//All done. Spit back ID of newly created block.
		return $insert_args['id'];
		
	}
	
	
	public static function update_block($block_id, $args) {

		global $wpdb;

		$block_to_be_updated = self::get_block($block_id);

		/* Make sure block exists */
		if ( !$block_to_be_updated )
			return null;

		/* Map old args */
			if ( isset($args['wrapper']) ) {

				$args['wrapper_id'] = $args['wrapper'];
				unset($args['wrapper']);

			}

		/* Map mirror-block setting to mirror_id column */
			if ( isset($args['settings']) && isset($args['settings']['mirror-block']) ) {

				$args['mirror_id'] = $args['settings']['mirror-block'];
				unset($args['settings']['mirror-block']);

			}

		/* Handle template argument */
			$template = padma_get('template', $args, PadmaOption::$current_skin);

			if ( isset($args['template']) )
				unset($args['template']);

		/* Query */
		$query = $wpdb->update($wpdb->pu_blocks, array_map('padma_maybe_serialize', $args), array(
			'template' => $template,
			'id' => $block_id
		));

		return $query;
		
	}


	public static function delete_block($block_id) {

		global $wpdb;

		$block_to_be_deleted = self::get_block($block_id);

		/* Make sure block exists */
		if ( !$block_to_be_deleted )
			return null;

		/* Query for deletion */
		$query = $wpdb->delete( $wpdb->pu_blocks, array(
			'template' => PadmaOption::$current_skin,
			'id' => $block_id
		));

		/* Unmirror the blocks mirroring this block */
		$wpdb->update( $wpdb->pu_blocks, array(
			'mirror_id' => ''
		), array(
			'mirror_id' => $block_id
		));

		/* Get block type */
		$block_type = $block_to_be_deleted['type'];

		/* Remove design settings and instances for this block */
		self::delete_block_design_instances($block_id, $block_type);
			
		return $query;
		
	}


		public static function delete_block_design_instances($block_id, $block_type) {

			PadmaElementAPI::register_elements_hook();

			$block_element = PadmaElementAPI::get_element('block-' . $block_type);

			/* Start by queuing the instance of the block element */
				$instances_to_delete = array(
					'block-' . $block_type => $block_type . '-block-' . $block_id
				);
			
			/* Find all block children element instances and queue them to be deleted */
				foreach ( PadmaElementAPI::get_block_elements($block_type) as $element_id => $element_info )
					$instances_to_delete[$element_id] = $element_id . '-block-' . $block_id;

			/* Delete the instances now */
				$batch_data = array();

				foreach ( $instances_to_delete as $element_id => $instance_id ) {
					$batch_data[] = array(
						'element_id' => $element_id,
						'special_element_type' => 'instance',
						'special_element_meta' => $instance_id
					);
				}

				PadmaElementsData::batch_delete_special_element_properties($batch_data);

		}
	
	
	public static function delete_by_layout($layout_id) {

		global $wpdb;

		$layout_blocks = self::get_blocks_by_layout($layout_id);

		foreach ( $layout_blocks as $block_id => $options ) {

			//Delete design instances
			self::delete_block_design_instances($block_id, $options['type']);

			/* Unmirror the blocks mirroring this block */
			$wpdb->update( $wpdb->pu_blocks, array(
				'mirror_id' => ''
			), array(
				'mirror_id' => $block_id
			));
			
		}

		//Query to delete blocks
		$query = $wpdb->delete( $wpdb->pu_blocks, array(
			'template' => PadmaOption::$current_skin,
			'layout' => $layout_id
		));

		return $query;
		
	}


	public static function delete_by_wrapper($layout_id, $wrapper_id) {

		global $wpdb;

		$wrapper_blocks = self::get_blocks_by_layout($layout_id, $wrapper_id);

		foreach ( $wrapper_blocks as $block_id => $options ) {

			//Delete design instances
			self::delete_block_design_instances($block_id, $options['type']);

			/* Unmirror the blocks mirroring this block */
			$wpdb->update( $wpdb->pu_blocks, array(
				'mirror_id' => ''
			), array(
				'mirror_id' => $block_id
			));


		}

		//Query to delete blocks
		$query = $wpdb->delete( $wpdb->pu_blocks, array(
			'template' => PadmaOption::$current_skin,
			'wrapper_id' => $wrapper_id,
			'layout' => $layout_id
		));

		return $query;

	}


	public static function delete_by_template($template) {

		global $wpdb;

		return $wpdb->delete( $wpdb->pu_blocks, array(
			'template' => $template
		));

	}
	
	
	public static function get_block($block_id_or_obj, $use_mirrored = false) {

		global $wpdb;

		/* If a block array is supplied, make sure it is legitimate. */
		if ( is_array( $block_id_or_obj) ) {
			
			if ( !isset( $block_id_or_obj['id']) && !padma_get('new', $block_id_or_obj, false) )
				return null;

			$block = $block_id_or_obj;
				
		/* Fetch the block based off of ID */
		} elseif ( is_string( $block_id_or_obj) || is_numeric( $block_id_or_obj) ) {

			/* Build cache key */
			$cache_key = 'pu_block_' . $block_id_or_obj;

			/* Check cache */
			$block_from_cache = wp_cache_get($cache_key);

			if ( $block_from_cache === false ) {

				$block = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->pu_blocks WHERE template = '%s' AND id = '%s'", PadmaOption::$current_skin, $block_id_or_obj), ARRAY_A);

				if ( is_array($block) && !is_wp_error($block) ) {

					$block = array_map('padma_maybe_unserialize', $block);

				} else {

					/* If no block is found, try querying the legacy_id */
					$block_from_legacy_id = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->pu_blocks WHERE template = '%s' AND legacy_id = '%d'", PadmaOption::$current_skin, $block_id_or_obj ), ARRAY_A );

					if ( is_array($block_from_legacy_id) && ! is_wp_error( $block_from_legacy_id ) ) {
						$block = array_map( 'padma_maybe_unserialize', $block_from_legacy_id );
					} else {
						$block = null;
					}

				}

				wp_cache_set($cache_key, $block);

			} else {

				$block = $block_from_cache;

			}

		/* No valid argument provided. */	
		} else {
			
			return null;
			
		}
		
		/* Fetch the mirrored block if $use_mirrored is true */
		if ( $use_mirrored === true && $mirrored_block = self::get_block_mirror($block) )
			$block = $mirrored_block;
				
		return $block;
		
	}
	
	
	public static function get_blocks_by_layout($layout_id, $wrapper_id = false, $include_design_editor_instances = false) {

		global $wpdb;

		/* Build cache key */
		$cache_key = 'pu_blocks_by_layout_' . $layout_id;

		if ( $wrapper_id )
			$cache_key = $cache_key . '_wrapper_' . $wrapper_id;

		if ( $include_design_editor_instances )
			$cache_key = $cache_key . '_with_design';

		/* Check cache */
		$layout_blocks = wp_cache_get($cache_key);

		if ( $layout_blocks === false ) {

			/* Retrieve all blocks from layout */
				$query_string = $wpdb->prepare("SELECT * FROM $wpdb->pu_blocks WHERE layout = '%s' AND template = '%s'", $layout_id, PadmaOption::$current_skin);

				if ( $wrapper_id )
					$query_string .= " AND wrapper_id = '$wrapper_id'";

				$layout_blocks_query = $wpdb->get_results($query_string, ARRAY_A);

			/* Change results array into associative */
				$layout_blocks = array();

				foreach ( $layout_blocks_query as $layout_block ) {

					$layout_blocks[$layout_block['id']] = array_map('padma_maybe_unserialize', $layout_block);

					/* Add design editor instance in if set to do so */
					if ( $include_design_editor_instances ) {

						$layout_blocks[$layout_block['id']]['styling'] = self::get_block_styling($layout_block);

					}

				}

			wp_cache_set($cache_key, $layout_blocks);

		}
						
		return $layout_blocks;
				
	}


		public static function get_block_styling($block) {

			do_action('padma_before_get_block_styling');

			$block_element = PadmaElementAPI::get_element('block-' . $block['type']);

			/* Set up styling array */
			$styling = array();

			/* Get block instance styling */
				$block_instance_properties = PadmaElementsData::get_special_element_properties(array(
					'element' => 'block-' . $block['type'],
					'se_type' => 'instance', 
					'se_meta' => $block['type'] . '-block-' . $block['id']
				));

				if ( !empty($block_instance_properties) ) {

					$styling[$block['type'] . '-block-' . $block['id']] = array(
						'element' => 'block-' . $block['type'],
						'properties' => $block_instance_properties
					);

				}

			/* Get block children element instances (which could be a LOT) */
			foreach ( PadmaElementAPI::get_block_elements($block['type']) as $block_element_sub_element ) {

				/* Make sure that the element supports instances */
				if ( !padma_get('supports-instances', $block_element_sub_element) )
					continue;

				$sub_element_instance_id = $block_element_sub_element['id'] . '-block-' . $block['id'];

				$sub_element_instance_properties = PadmaElementsData::get_special_element_properties(array(
					'element' => $block_element_sub_element['id'], 
					'se_type' => 'instance', 
					'se_meta' => $sub_element_instance_id
				));

				/* Only add sub element instance if there are properties present */
				if ( !empty($sub_element_instance_properties) ) {

					$styling[$sub_element_instance_id] = array(
						'element' => $block_element_sub_element['id'],
						'properties' => $sub_element_instance_properties
					);

				}

				/* Instance states */
					if ( !empty($block_element_sub_element['states']) && is_array($block_element_sub_element['states']) ) {

						foreach ( $block_element_sub_element['states'] as $instance_state_id => $instance_state_info ) {

							$actual_instance_id = $block_element_sub_element['id'] . '-block-' . $block['id'] . '-state-' . $instance_state_id;
							$instance_state_properties = PadmaElementsData::get_special_element_properties(array(
								'element' => $block_element_sub_element['id'], 
								'se_type' => 'instance', 
								'se_meta' => $actual_instance_id
							));

							/* Only add instance state if there are properties present */
							if ( empty($instance_state_properties) )
								continue;

							$styling[$actual_instance_id] = array(
								'element' => $block_element_sub_element['id'],
								'properties' => $instance_state_properties 
							);

						}

					}
				/* End getting instance states */

			}

			return $styling;

		}


	public static function get_blocks_by_wrapper($layout_id, $wrapper_id) {

		$layout_blocks = self::get_blocks_by_layout($layout_id);
		$layout_wrappers = PadmaWrappersData::get_wrappers_by_layout($layout_id);
		$wrapper_blocks = array();

		foreach ( $layout_blocks as $block_id => $block ) {

			if ( padma_get('wrapper_id', $block, PadmaWrappers::$default_wrapper_id) === $wrapper_id )
				$wrapper_blocks[$block_id] = $block;

			/* If there's only one wrapper and the block does not have a proper ID or is default, move it to that wrapper */
			if ( count($layout_wrappers) === 1 && (padma_get('wrapper_id', $block) === null || padma_get('wrapper_id', $block) == 'wrapper-default') )
				$wrapper_blocks[$block_id] = $block;

		}

		return $wrapper_blocks;

	}
	
	
	public static function get_blocks_by_type($type) {

		global $wpdb;

		/* Build cache key */
		$cache_key = 'pu_blocks_by_type_' . $type;

		/* Check cache */
		$blocks_from_cache = wp_cache_get($cache_key);

		if ( $blocks_from_cache === false ) {

			$blocks_by_type_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->pu_blocks WHERE template = '%s' AND type = '%s'", PadmaOption::$current_skin, $type), ARRAY_A);

			/* Change results array into associative */
			$blocks_by_type = array();

			foreach ( $blocks_by_type_query as $block ) {

				$blocks_by_type[$block['id']] = array_map('padma_maybe_unserialize', $block);

			}

			wp_cache_set($cache_key, $blocks_by_type);

		} else {

			$blocks_by_type = $blocks_from_cache;

		}

		return $blocks_by_type;
		
	}
	
	
	public static function get_all_blocks() {

		global $wpdb;

		/* Build cache key */
		$cache_key = 'pu_all_blocks';

		/* Check cache */
		$blocks_from_cache = wp_cache_get($cache_key);

		if ( $blocks_from_cache === false ) {

			$blocks_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->pu_blocks WHERE template = '%s'", PadmaOption::$current_skin), ARRAY_A);

			/* Change results array into associative */
			$all_blocks = array();

			foreach ( $blocks_query as $block ) {

				$all_blocks[$block['id']] = array_map('padma_maybe_unserialize', $block);

			}

			wp_cache_set($cache_key, $all_blocks);

		} else {

			$all_blocks = $blocks_from_cache;

		}

		return $all_blocks;
		
	}


	public static function is_block_mirrored($block) {

		$block = self::get_block($block);
		$block_settings = padma_get( 'settings', $block, array() );

		/* Use mirror-block setting if present because it means the block is new or the setting has been updated and not saved yet */
		if ( isset( $block_settings['mirror-block'] ) )
			return $block_settings['mirror-block'];

		if ( $mirrored_block_id = padma_get('mirror_id', $block) )
			return $mirrored_block_id;

		return false;

	}


	public static function get_block_mirror($block) {

		$block = self::get_block($block);

		if ( $mirrored_block_id = self::is_block_mirrored($block) )
			return PadmaBlocksData::get_block($mirrored_block_id);

		return false;

	}


	public static function block_exists($id) {

		if ( self::get_block($id) )
			return true;

		return false;

	}


	public static function get_block_name($block) {
		
		$block = self::get_block($block);
	
		//Create the default name by using the block type and ID
		$default_name = PadmaBlocks::block_type_nice($block['type']);
		
		return padma_get('alias', padma_get('settings', $block, array()), $default_name);
		
	}
	
	
	public static function get_block_width($block) {
		
		$block = self::get_block($block);
			
		$block_grid_width = padma_get('width', $block['dimensions'], null);
		
		if ( $block_grid_width === null )
			return null;

		/* Fetch the wrapper that way we can get its Grid settings */
			$wrapper = PadmaWrappersData::get_wrapper(padma_get('wrapper_id', $block, 'wrapper-default'));

		$column_width = PadmaWrappers::get_column_width($wrapper);
		$gutter_width = PadmaWrappers::get_gutter_width($wrapper);
			
		return ( $block_grid_width * ($column_width + $gutter_width) ) - $gutter_width;
			
	}
	
	
	public static function get_block_height($block) {
		
		$block = self::get_block($block);
			
		$block_grid_height = padma_get('height', $block['dimensions'], null);
		
		if ( $block_grid_height === null )
			return null;
			
		return $block_grid_height;
		
	}
	

	public static function get_block_setting($block, $setting, $default = null) {
		
		$block = self::get_block($block);
			
		//No block, no settings
		if ( !$block )
			return $default;
			
		if ( !isset($block['settings'][$setting]) )
			return $default;
			
		return padma_fix_data_type($block['settings'][$setting]);
		
	}


	public static function get_legacy_id($block) {

		return is_numeric( padma_get( 'legacy_id', $block ) ) && padma_get( 'legacy_id', $block ) > 0  ? padma_get( 'legacy_id', $block ) : $block['id'];

	}

	
}