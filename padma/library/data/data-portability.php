<?php
class PadmaDataPortability {


	public static function export_skin(array $info) {

		global $wpdb;

		do_action('padma_before_export_skin');

		$wp_options_prefix = 'padma_|template=' . PadmaOption::$current_skin . '|_';

		$skin = array(
			'pu-version' => PADMA_VERSION,
			'name' => padma_get('name', $info, 'Unnamed'),
			'author' => padma_get('author', $info),
			'image-url' => padma_get('image-url', $info),
			'version' => padma_get('version', $info),
			'data_wp_options' => $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->options WHERE option_name LIKE '%s'", $wp_options_prefix . '%'), ARRAY_A),
			'data_wp_postmeta' => $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE '%s'", '_pu_|template=' . PadmaOption::$current_skin . '|_%'), ARRAY_A),
			'data_pu_layout_meta' => $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->pu_layout_meta WHERE template = '%s'", PadmaOption::$current_skin), ARRAY_A),
			'data_pu_wrappers' => $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->pu_wrappers WHERE template = '%s'", PadmaOption::$current_skin), ARRAY_A),
			'data_pu_blocks' => $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->pu_blocks WHERE template = '%s'", PadmaOption::$current_skin), ARRAY_A)
		);

		/* Spit the file out */
		$filename = 'Padma Template - ' . padma_get('name', $info, 'Unnamed');

		if ( padma_get('version', $info) ) {
			$filename .= ' ' . padma_get('version', $info);
		}

		return self::to_json($filename, 'skin', $skin);

	}


	public static function install_skin(array $skin) {

		$skins = PadmaOption::get_group('skins');

		/* Remove image definitions */
			if ( isset($skin['image-definitions']) )
				unset($skin['image-definitions']);

		/* Skin ID ... Truncate the skin ID to 12 characters due to varchar limit in wp_options */
			$original_skin_id = substr(strtolower(str_replace(' ', '-', $skin['name'])), 0, 12);

			$skin_id = $original_skin_id;
			$skin_name = $skin['name'];

			$skin_unique_id_counter = 0;

		/* Check if skin already exists.  If it does, change ID and skin name */
			while ( PadmaOption::get($skin_id, 'skins') || get_option('padma_|template=' . $skin_id . '|_option_group_general') ) {

				$skin_unique_id_counter++;
				$skin_id = $original_skin_id . '-' . $skin_unique_id_counter;

				$skin_name = $skin['name'] . ' ' . $skin_unique_id_counter;

			}

		/* Send skin to DB */
			$skin['id'] = $skin_id;
			$skin['name'] = $skin_name;

			$skin_with_info_only = $skin;

			$data_to_remove_from_saved_skin = array(
				'data_wp_options',
				'data_wp_postmeta',
				'data_pu_layout_meta',
				'data_pu_wrappers',
				'data_pu_blocks',
				'templates',
				'layouts',
				'element-data'
			);

			foreach ( $data_to_remove_from_saved_skin as $key_to_remove ) {

				if ( !isset($skin_with_info_only[$key_to_remove]) )
					continue;

				unset($skin_with_info_only[$key_to_remove]);

			}

			PadmaOption::set($skin['id'], $skin_with_info_only, 'skins');

		/* Change current skin ID to the newly added skin so we can populate data */
			PadmaOption::$current_skin = $skin['id'];
			PadmaLayoutOption::$current_skin = $skin['id'];

		/* Process the install */
			if ( !padma_get('pu-version', $skin) || version_compare(padma_get('pu-version', $skin), '1.0', '<') ) {
				$skin = self::process_install_skin_pre37($skin);
			} else {
				$skin = self::process_install_skin($skin);
			}

		/* Change $current_skin back just to be safe */
			PadmaOption::$current_skin = PadmaTemplates::get_active_id();
			PadmaLayoutOption::$current_skin = PadmaTemplates::get_active_id();

		return $skin;

	}


		public static function process_install_skin_pre37(array $skin) {

			/* Set up skin options that way when it's activated it looks right */
				/* Install templates */
				if ( $skin_templates = padma_get('templates', $skin) )
					PadmaSkinOption::set_group('templates', $skin_templates);

					/* Assign templates */
						if ( !empty($skin['templates']['assignments']) ) {

							foreach ( $skin['templates']['assignments'] as $layout_id => $template_id ) {

								/* Change layout ID separators */
								if ( strpos($layout_id, 'template-') !== 0 )
									$layout_id = str_replace('-', PadmaLayout::$sep, $layout_id);

								PadmaLayoutOption::set($layout_id, 'template', $template_id);

							}

						}

				/* Install layouts (blocks, wrappers, and flags */
					$wrapper_id_mapping = array();
					$block_id_mapping = array();

					foreach ( $skin['layouts'] as $layout_id => $layout_data ) {

						/* Change layout ID separators */
							if ( strpos($layout_id, 'template-') !== 0 )
								$layout_id = str_replace('-', PadmaLayout::$sep, $layout_id);

						/* Install Wrappers */
							foreach ( $layout_data['wrappers'] as $wrapper_id => $wrapper_data ) {

								$wrapper_data['position'] = array_search($wrapper_id, array_keys($layout_data['wrappers']));

								$wrapper_data['settings'] = array(
									'fluid' => padma_get('fluid', $wrapper_data),
									'fluid-grid' => padma_get('fluid-grid', $wrapper_data),
									'columns' => padma_get('columns', $wrapper_data),
									'column-width' => padma_get('column-width', $wrapper_data),
									'gutter-width' => padma_get('gutter-width', $wrapper_data),
									'use-independent-grid' => padma_get('use-independent-grid', $wrapper_data)
								);

								$new_wrapper = PadmaWrappersData::add_wrapper($layout_id, $wrapper_data);

								if ( $new_wrapper && !is_wp_error($new_wrapper)  ) {
									$wrapper_id_mapping[PadmaWrappers::format_wrapper_id($wrapper_id)] = $new_wrapper;
								}

							}

						/* Install Blocks */
							foreach ( $layout_data['blocks'] as $block_id => $block_data ) {

								$block_data['wrapper'] = padma_get(PadmaWrappers::format_wrapper_id(padma_get('wrapper', $block_data)), $wrapper_id_mapping);

								$new_block = PadmaBlocksData::add_block($layout_id, $block_data);

								if ( $new_block && !is_wp_error($new_block) ) {
									$block_id_mapping[$block_id] = $new_block;
								}

							}

					}

				/* Setup mirroring */
					foreach ( $skin['layouts'] as $layout_id => $layout_data ) {

						/* Change layout ID separators */
						if (strpos($layout_id, 'template-') !== 0)
							$layout_id = str_replace('-', PadmaLayout::$sep, $layout_id);

						foreach ($layout_data['wrappers'] as $wrapper_id => $wrapper_data) {

							$wrapper_to_update = $wrapper_id_mapping[PadmaWrappers::format_wrapper_id($wrapper_id)];

							if (!$mirror_id = padma_get('mirror-wrapper', $wrapper_data))
								continue;

							$mirror_id = padma_get(PadmaWrappers::format_wrapper_id($mirror_id), $wrapper_id_mapping);

							PadmaWrappersData::update_wrapper($wrapper_to_update, array(
								'mirror_id' => $mirror_id
							));

						}

						foreach ( $layout_data['blocks'] as $block_id => $block_data ) {

							if ( !isset($block_id_mapping[$block_id]) )
								continue;

							$block_to_update = $block_id_mapping[$block_id];

							if ( !$mirror_id = padma_get('mirror-block', padma_get('settings', $block_data, array())) )
								continue;

							$mirror_id = padma_get($mirror_id, $block_id_mapping);

							PadmaBlocksData::update_block($block_to_update, array(
								'mirror_id' => $mirror_id
							));

						}

					}

			/* Install design data */
				/* Sort the block and wrapper mappings by descending number that way when we do a simple recursive find and replace the small block IDs won't mess up the larger block IDs.
				   Example: Replacing block-1 before block-11 is replaced would be bad news */
				krsort($block_id_mapping);
				krsort($wrapper_id_mapping);

				foreach ( $block_id_mapping as $old_block_id => $new_block_id ) {
					$skin['element-data'] = padma_str_replace_json('block-' . $old_block_id, 'block-' . $new_block_id, $skin['element-data']);
				}

				foreach ( $wrapper_id_mapping as $old_wrapper_id => $new_wrapper_id ) {
					$skin['element-data'] = padma_str_replace_json('wrapper-' . $old_wrapper_id, 'wrapper-' . $new_wrapper_id, $skin['element-data']);
				}

				$skin['element-data'] = padma_preg_replace_json( "/-layout-[\w-]*/", '', $skin['element-data'] );

				PadmaSkinOption::set('properties', $skin['element-data'], 'design');
				PadmaSkinOption::set('live-css', stripslashes($skin['live-css']));

			/* Set merge flag that way the next time they save it won't screw up the styling */
				PadmaSkinOption::set('merged-default-design-data-core', true, 'general');

			/* Set wrapper defaults */
				if ( !empty($skin['wrapper-defaults']) && is_array($skin['wrapper-defaults']) ) {

					PadmaSkinOption::set('columns', padma_get('columns', $skin['wrapper-defaults'], PadmaWrappers::$default_columns));
					PadmaSkinOption::set('columns-width', padma_get('columns', $skin['wrapper-defaults'], PadmaWrappers::$default_columns));
					PadmaSkinOption::set('gutter-width', padma_get('columns', $skin['wrapper-defaults'], PadmaWrappers::$default_columns));

				}

			return $skin;

		}



		public static function process_install_skin(array $skin) {

			return PadmaDataSnapshots::process_rollback($skin, $skin['id']);

		}


	public static function export_block_settings($block_id) {

		/* Set up variables */
			$block = PadmaBlocksData::get_block($block_id);

		/* Check if block exists */
			if ( !$block )
				die('Error: Could not export block settings.');

		/* Spit the file out */
			return self::to_json('Block Settings - ' . PadmaBlocksData::get_block_name($block), 'block-settings', array(
				'id' => $block_id,
				'type' => $block['type'],
				'settings' => $block['settings'],
				'styling' => PadmaBlocksData::get_block_styling($block)
			));

	}


	public static function export_layout($layout_id) {

		/* Set up variables */
			if ( !$layout_name = PadmaLayout::get_name($layout_id) )
				die('Error: Invalid layout.');

			$layout = array(
				'name' => $layout_name,
				'blocks' => PadmaBlocksData::get_blocks_by_layout($layout_id, false, true),
				'wrappers' => PadmaWrappersData::get_wrappers_by_layout($layout_id, true)
			);

		/* Spit the file out */
		return self::to_json('Padma Layout - ' . $layout_name, 'layout', $layout);

	}


	/**
	 * Convert array to JSON file and force download.
	 *
	 * Images will be converted to base64 via PadmaDataPortability::encode_images()
	 **/
	public static function to_json($filename, $data_type = null, $array) {

		if ( !$array['data-type'] = $data_type )
			die('Missing data type for PadmaDataPortability::to_json()');

		$array['image-definitions'] = self::encode_images($array);

		header('Content-Disposition: attachment; filename="' . $filename . '.json"');
		header('Content-Type: application/json');
		header('Pragma: no-cache');

		echo json_encode($array);

		return $filename;

	}


		/**
		 * Convert all images to base64.
		 *
		 * This method is recursive.
		 **/
		public static function encode_images(&$array, $images = null) {

			if ( !$images )
				$images = array();

			foreach ( $array as $key => $value ) {

				$is_serialized = is_serialized($value);

				if ( is_array($value) || $is_serialized ) {

					if ( $is_serialized ) {

						$value = padma_maybe_unserialize($value);

						if ( !is_array($value) ) {
							continue;
						}

						$array[$key] = $value;

					}

					$images = array_merge($images, self::encode_images($array[$key], $images));

					continue;

				} else if ( is_string($value) ) {

					$image_matches = array();

					/* PREG_SET_ORDER makes the $image_matches array make more sense */
					preg_match_all('/([a-z\-_0-9\/\:\.]*\.(jpg|jpeg|png|gif))/i', $value, $image_matches, PREG_SET_ORDER);

					/* Go through each image in the string and download it then base64 encode it and replace the URL with variable */
					foreach ( $image_matches as $image_match ) {

						if ( !count($image_match) )
							continue;

						$image_request = wp_remote_get($image_match[0]);

						if ( $image_request && $image_contents = wp_remote_retrieve_body($image_request) ) {

							$image = array(
								'base64_contents' => base64_encode($image_contents),
								'mime_type' => $image_request['headers']['content-type']
							);

							/* Add base64 encoded image to image definitions. */
								/* Make sure that the image isn't already in the definitions.  If it is, $possible_duplicate will be the key/ID to the image */
								if ( !$possible_duplicate = array_search($image, $images) )
									$images['%%IMAGE_REPLACEMENT_' . (count($images) + 1) . '%%'] = $image;

							/* Replace the URL with variable that way it can be replaced with uploaded image on import.  If $possible_duplicate isn't null/false, then use it! */
								$variable = $possible_duplicate ? $possible_duplicate : '%%IMAGE_REPLACEMENT_' . (count($images)) . '%%';
								$array[$key] = str_replace($image_match[0], $variable, $array[$key]);

						}

					}

				}

			}

			return $images;

		}


	/**
	 * Convert base64 encoded image into a file and move it to proper WP uploads directory.
	 **/
	public static function decode_image_to_uploads($base64_string) {

		/* Make sure user has permissions to edit in the Visual Editor */
			if ( !PadmaCapabilities::can_user_visually_edit() )
				return;

		/* Create a temporary file and decode the base64 encoded image into it */
			$temporary_file = wp_tempnam();
			file_put_contents($temporary_file, base64_decode($base64_string));

		/* Use wp_check_filetype_and_ext() to figure out the real mimetype of the image.  Provide a bogus extension and then we'll use the 'proper_filename' later. */
			$filename = 'padma-imported-image.jpg';
			$file_information = wp_check_filetype_and_ext($temporary_file, $filename);

		/* Construct $file array which is similar to a PHP $_FILES array.  This array must be a variable since wp_handle_sideload() requires a variable reference argument. */
			if ( padma_get('proper_filename', $file_information) )
				$filename = $file_information['proper_filename'];

			$file = array(
				'name' => $filename,
				'tmp_name' => $temporary_file
			);

		/* Let WordPress move the image and spit out the file path, URL, etc.  Set test_form to false that way it doesn't verify $_POST['action'] */
			$upload = wp_handle_sideload($file, array('test_form' => false));

			/* If there's an error, be sure to unlink/delete the temporary file in case wp_handle_sideload() doesn't. */
			if ( isset($upload['error']) )
				@unlink($temporary_file);

			return $upload;

	}


}