<?php
/**
  * 3.7
  *
  * Add new custom MySQL table for blocks and transfer blocks in wp_options over to it
  */
add_action('padma_do_upgrade_37', 'padma_do_upgrade_37');
function padma_do_upgrade_37($current_step = false) {

	global $wpdb;

	$available_steps = array(
		'step_1',
		'step_2',
		'step_3',
		'step_4',
		'step_5',
		'finish'
	);

	/* If first run of upgrade, start on first step */
	if ( ! $current_step && ! get_option( 'pu_37_upgrade_current_step' ) ) {

		$current_step = $available_steps[0];

	/* If currently in middle of upgrade, set the current step to whatever the DB says */
	} else if ( ! $current_step && get_option( 'pu_37_upgrade_current_step' ) ) {

		$current_step = get_option( 'pu_37_upgrade_current_step' );

	}

	switch ( $current_step ) {

		case 'step_1':

			/* Create a backup of all Padma MySQL rows in wp_options */
			PadmaMaintenance::output_status( 'Backing up current Padma settings...' );

			$wp_options_backup = padma_upgrade_37_backup_wp_options();

			if ( ! $wp_options_backup ) {
				PadmaMaintenance::output_status( 'Padma Error: Unable to back up current Padma settings. Unable to proceed with upgrade. Please contact Padma support at support@padmaunlimited.com' );
				wp_die('<strong>Error: Unable to back up current Padma settings. Unable to proceed with upgrade. Please contact Padma support at support@padmaunlimited.com');
			}

			break;

		case 'step_2':

			/* Setup MySQL Tables */
			PadmaMaintenance::output_status( 'Setting up new data structure...' );
			padma_upgrade_37_setup_mysql();

			/* Fix template IDs and names */
			PadmaMaintenance::output_status( 'Fixing template names...' );
			padma_upgrade_37_fix_templates();

			/* Upgrade wrappers */
			PadmaMaintenance::output_status( 'Transferring wrappers to new data location...' );
			padma_upgrade_37_upgrade_wrappers();

			break;


		case 'step_3':

			/* Upgrade blocks and layout options */
			PadmaMaintenance::output_status( 'Transferring blocks to new data location...' );
			padma_upgrade_37_upgrade_blocks_and_layout_options();

			break;

		case 'step_4':

			/* Setup mirroring */
			PadmaMaintenance::output_status( 'Setting up mirroring of blocks and wrappers...' );
			padma_upgrade_37_setup_mirroring();

			/* Setup and copy options */
			PadmaMaintenance::output_status( 'Renaming old options...' );
			padma_upgrade_37_setup_options();


			break;

		case 'step_5':

			/* Fix design settings and instance IDs */
			PadmaMaintenance::output_status( 'Verifying Design Editor settings...' );
			padma_upgrade_37_fix_design_data();

			break;

		case 'finish':

			/* Rename and delete old options */
			PadmaMaintenance::output_status( 'Deleting old options...' );
			padma_upgrade_37_rename_and_delete_old_options();

			break;


	}

	/* Go to next step */
	$index_of_current_step = array_search($current_step, $available_steps);

	if ( isset($available_steps[$index_of_current_step + 1]) ) {

		$next_step = $available_steps[$index_of_current_step + 1];

		update_option( 'pu_37_upgrade_current_step', $next_step );

		padma_do_upgrade_37($next_step);

	}

}



function padma_upgrade_37_backup_wp_options() {

	global $wpdb;

	$wp_options_prefix = 'padma';

	$ignored_wp_option_value_1 = 'a:1:{s:7:"general";a:1:{s:6:"blocks";a:0:{}}}';
	$ignored_wp_option_value_2 = 'a:3:{s:7:"general";a:4:{s:8:"template";s:0:"";s:10:"hide-title";s:0:"";s:15:"alternate-title";s:0:"";s:9:"css-class";s:0:"";}s:14:"post-thumbnail";a:1:{s:8:"position";s:0:"";}s:3:"seo";a:9:{s:5:"title";s:0:"";s:11:"description";s:0:"";s:7:"noindex";s:5:"false";s:8:"nofollow";s:5:"false";s:9:"noarchive";s:5:"false";s:9:"nosnippet";s:5:"false";s:5:"noodp";s:5:"false";s:6:"noydir";s:5:"false";s:12:"redirect-301";s:0:"";}}';
	$ignored_wp_option_value_3 = 'a:2:{s:7:"general";a:4:{s:8:"template";s:0:"";s:10:"hide-title";s:0:"";s:15:"alternate-title";s:0:"";s:9:"css-class";s:0:"";}s:14:"post-thumbnail";a:1:{s:8:"position";s:0:"";}}';
	$ignored_wp_option_value_4 = 'a:2:{s:7:"general";a:4:{s:9:"css-class";s:0:"";s:8:"template";s:0:"";s:10:"hide-title";s:0:"";s:15:"alternate-title";s:0:"";}s:14:"post-thumbnail";a:1:{s:8:"position";s:4:"left";}}';
	$ignored_wp_option_value_5 = 'a:2:{s:7:"general";a:6:{s:8:"template";s:0:"";s:10:"hide-title";s:0:"";s:15:"alternate-title";s:0:"";s:9:"css-class";s:0:"";s:16:"page_title_alias";s:0:"";s:20:"page_sub_title_alias";s:0:"";}s:14:"post-thumbnail";a:1:{s:8:"position";s:0:"";}}';
	$ignored_wp_option_value_6 = 'a:2:{s:7:"general";a:4:{s:8:"template";s:0:"";s:10:"hide-title";s:0:"";s:15:"alternate-title";s:0:"";s:9:"css-class";s:0:"";}s:14:"post-thumbnail";a:1:{s:8:"position";s:4:"left";}}';

	$query = $wpdb->prepare("SELECT * FROM $wpdb->options WHERE option_name LIKE '%s' AND option_name NOT LIKE '%s' AND option_name != 'padma_option_group_block-actions-cache' AND option_value != '%s' AND option_value != '%s' AND option_value != '%s' AND option_value != '%s' AND option_value != '%s' AND option_value != '%s'", $wp_options_prefix . '%', 'padma_option_group_design-editor-group%',  $ignored_wp_option_value_1, $ignored_wp_option_value_2, $ignored_wp_option_value_3, $ignored_wp_option_value_4, $ignored_wp_option_value_5, $ignored_wp_option_value_6);

	$wp_options_backup_data = $wpdb->get_results($query);

	$file_content = '
<?php
/* Protect the backup contents */
die();

/* BACKUP CONTENTS

' . json_encode($wp_options_backup_data) . '

END BACKUP CONTENTS */
';

	/* Write the backup to a text file */
	$file_handle = @fopen(PADMA_UPLOADS_DIR . '/' . 'padma_37_upgrade_backup_' . mktime() . '.php', 'w');

	if ( !@fwrite($file_handle, $file_content) )
		return false;

	return true;

}


function padma_upgrade_37_setup_mysql() {

	Padma::mysql_drop_tables();
	Padma::mysql_dbdelta();

}


function padma_upgrade_37_fix_templates() {

	global $wpdb;

	/* Shorten template IDs */
	$templates = PadmaOption::get_group('skins');

	/* If $templates turns up false then we need to repair it using the name of options */
	if ( !$templates || !is_array($templates) ) {

		$query_for_template_ids = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'padma_%skin=%'" );
		$templates_repaired = array();

		foreach ( $query_for_template_ids as $query_for_template_ids_obj ) {

			$option_name_fragments = explode('|_', str_replace('padma_|skin=', '', $query_for_template_ids_obj->option_name));
		    $template_id = $option_name_fragments[0];

			$templates_repaired[$template_id] = array(
				'name' => $template_id,
				'id' => $template_id
			);

		}

		$templates = $templates_repaired;

	}

	foreach ( $templates as $template_id => $template ) {

		/* Truncate the template ID to 12 characters due to varchar limit in wp_options */
		$new_template_id = substr(strtolower(str_replace(' ', '-', $template['id'])), 0, 12);
		$shortened_template_id = $new_template_id;

		$original_template_id = $template['id'];
		$original_template = $templates[ $original_template_id ];

		if ( $template['id'] == '' ) {
			$new_template_id = 'unnamed';

			$original_template = array(
				'name' => 'Unnamed'
			);
		}

		/* If the new template ID is the same as the current ID then don't do anything with this template */
		if ( $new_template_id == $original_template_id )
			continue;

		$template_unique_id_counter = 0;

		/* Check if template ID already exists.  If it does, change ID */
			while ( padma_get($new_template_id, $templates) || get_option('padma_|skin=' . $new_template_id . '|_option_group_general') ) {

				$template_unique_id_counter++;
				$new_template_id = $shortened_template_id . '-' . $template_unique_id_counter;

			}

		/* Update WP option names */
		$wpdb->query( "UPDATE IGNORE $wpdb->options SET option_name = replace(option_name, 'padma_|skin=$original_template_id|', 'padma_|skin=$new_template_id|') WHERE option_name LIKE 'padma_|skin=$original_template_id|%'" );

		/* If the current skin is the one with the name change then change that */
		if ( PadmaOption::get('current-skin', 'general', PADMA_DEFAULT_SKIN) == $original_template_id ) {
			PadmaOption::set('current-skin', $new_template_id);
			PadmaOption::$current_skin = $new_template_id;
		}

		$templates[$new_template_id] = $original_template;
		unset($templates[$original_template_id]);

		$templates[$new_template_id]['id'] = $new_template_id;

	}

	/* Save templates */
	PadmaOption::set_group('skins', $templates);

}


function padma_upgrade_37_upgrade_wrappers() {

	global $wpdb;

	/* Make sure wrappers table is empty in case this step was interrupted */
	$wpdb->query( "TRUNCATE TABLE $wpdb->pu_wrappers" );

	$upgraded_wrappers = array();

	$wrappers_by_template = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name LIKE 'padma%option_group_wrappers'");

	foreach ( $wrappers_by_template as $template_wrappers ) {

		if ( strpos($template_wrappers->option_name, 'padma_|skin=') !== 0 ) {
			$template = 'base';
		} else {
			$option_name_fragments = explode('|_', str_replace('padma_|skin=', '', $template_wrappers->option_name));
			$template = $option_name_fragments[0];

			if ( $template == '' ) {
				$template = 'unnamed';
			}
		}

		foreach ( padma_maybe_unserialize($template_wrappers->option_value) as $layout_id => $layout_wrappers ) {

			if ( !is_array($layout_wrappers) )
				continue;

			$layout_id = PadmaLayout::format_old_id($layout_id);

			foreach ( $layout_wrappers as $layout_wrapper_id => $layout_wrapper ) {

				$layout_wrapper['template'] = $template;
				$layout_wrapper['position'] = array_search($layout_wrapper_id, array_keys($layout_wrappers));
				$layout_wrapper['legacy_id'] = PadmaWrappers::format_wrapper_id($layout_wrapper_id);

				$layout_wrapper['settings'] = array(
					'fluid'                      => padma_get( 'fluid', $layout_wrapper ),
					'fluid-grid'                 => padma_get( 'fluid-grid', $layout_wrapper ),
					'columns'                    => padma_get( 'columns', $layout_wrapper ),
					'column-width'               => padma_get( 'column-width', $layout_wrapper ),
					'gutter-width'               => padma_get( 'gutter-width', $layout_wrapper ),
					'use-independent-grid'       => padma_get( 'use-independent-grid', $layout_wrapper ),
					'alias'                      => padma_get( 'alias', $layout_wrapper ),
					'css-classes'                => padma_get( 'css-classes', $layout_wrapper ),
					'responsive-wrapper-options' => padma_get( 'responsive-wrapper-options', $layout_wrapper, array() )
				);

				$new_wrapper = PadmaWrappersData::add_wrapper($layout_id, $layout_wrapper);

				if ( $new_wrapper && !is_wp_error($new_wrapper) ) {

					if ( !isset( $upgraded_wrappers[ $template ]) )
						$upgraded_wrappers[ $template ] = array();

					/* Gracefully handle duplicate IDs */
					if ( !isset($upgraded_wrappers[$template][PadmaWrappers::format_wrapper_id($layout_wrapper_id)]) ) {

						$upgraded_wrappers[$template][PadmaWrappers::format_wrapper_id($layout_wrapper_id)] = array(
							'id'             => $new_wrapper,
							'mirror-wrapper' => padma_get('mirror-wrapper', $layout_wrapper)
						);

					} else if ( isset($upgraded_wrappers[$template][PadmaWrappers::format_wrapper_id($layout_wrapper_id)]['id']) ) {

						$upgraded_wrappers[$template][PadmaWrappers::format_wrapper_id($layout_wrapper_id)] = array(
							$upgraded_wrappers[$template][PadmaWrappers::format_wrapper_id($layout_wrapper_id)],
							array(
								'id'             => $new_wrapper,
								'mirror-wrapper' => padma_get('mirror-wrapper', $layout_wrapper)
							)
						);

					} else {

						$upgraded_wrappers[$template][PadmaWrappers::format_wrapper_id($layout_wrapper_id)][] = array(
							'id'             => $new_wrapper,
							'mirror-wrapper' => padma_get('mirror-wrapper', $layout_wrapper)
						);

					}

				}

			}

		}

	}


	delete_option('pu_37_upgrade_wrappers');

	return add_option('pu_37_upgrade_wrappers', $upgraded_wrappers, false, 'no');

}


function padma_upgrade_37_upgrade_blocks_and_layout_options() {

	global $wpdb;

	/* Make sure blocks and layout options tables are empty in case this step is interrupted */
	$wpdb->query( "TRUNCATE TABLE $wpdb->pu_blocks" );
	$wpdb->query( "TRUNCATE TABLE $wpdb->pu_layout_meta" );

	$upgraded_blocks = array();
	$upgraded_wrappers = get_option('pu_37_upgrade_wrappers');

	$all_layout_options = padma_upgrade_37_extract_layout_options();

	foreach ( $all_layout_options as $template => $template_layouts ) {

		if ( $template == '' ) {
			$template = 'unnamed';
		}

		foreach ( $template_layouts as $template_layout_id => $template_layout_options ) {

			/* Move blocks to pu_blocks table */
				foreach ( padma_get('blocks', padma_get('general', $template_layout_options), array()) as $block_id => $block ) {

					$block['template'] = $template;

					$original_wrapper_id = padma_get('wrapper', $block, 'default');

					$wrapper = padma_get( PadmaWrappers::format_wrapper_id( $original_wrapper_id ), padma_get($template, $upgraded_wrappers, array()) );

					if ( !$wrapper ) {

						$default_wrapper = PadmaWrappersData::add_wrapper(PadmaLayout::format_old_id( $template_layout_id ), array(
							'columns' => PadmaWrappers::$default_columns,
							'column-width' => PadmaWrappers::$default_column_width,
							'gutter-width' => PadmaWrappers::$default_gutter_width,
							'use-independent-grid' => false,
							'fluid' => false,
							'fluid-grid' => false,
							'position' => 1,
							'template' => $template
						));

						$upgraded_wrappers = get_option('pu_37_upgrade_wrappers', array());

						if ( empty($upgraded_wrappers[$template]) || !is_array($upgraded_wrappers[$template]) ) {
							$upgraded_wrappers[$template] = array();
						}

						$upgraded_wrappers[$template]['default'] = array(
							'id'             => $default_wrapper,
							'mirror-wrapper' => padma_get('mirror-wrapper', $default_wrapper)
						);

						update_option('pu_37_upgrade_wrappers', $upgraded_wrappers);

						$wrapper = $upgraded_wrappers[$template]['default'];

					}

					if ( !isset($wrapper['id']) )
						$wrapper = $wrapper[0];

					$block['wrapper'] = $wrapper['id'];
					$block['legacy_id'] = $block_id;

					$new_block_mirror_id = padma_get( 'mirror-block', padma_get( 'settings', $block ) );

					if ( isset($block['settings']) && isset($block['settings']['mirror-block']) ) {
						unset($block['settings']['mirror-block']);
					}

					$new_block = PadmaBlocksData::add_block( PadmaLayout::format_old_id( $template_layout_id ), $block );

					if ( $new_block && !is_wp_error($new_block) ) {

						if ( ! isset( $upgraded_blocks[ $template ] ) )
							$upgraded_blocks[ $template ] = array();

						/* Gracefully handle duplicate IDs */
						if ( ! isset( $upgraded_blocks[$template][$block_id] ) ) {

							$upgraded_blocks[$template][$block_id] = array(
								'id'           => $new_block,
								'type'         => $block['type'],
								'mirror-block' => $new_block_mirror_id
							);

						} else if ( isset( $upgraded_blocks[$template][$block_id]['id'] ) ) {

							$upgraded_blocks[$template][$block_id] = array(
								$upgraded_blocks[$template][$block_id],
								array(
									'id'           => $new_block,
									'type'         => $block['type'],
									'mirror-block' => $new_block_mirror_id
								)
							);

						} else {

							$upgraded_blocks[$template][$block_id][] = array(
								'id'           => $new_block,
								'type'         => $block['type'],
								'mirror-block' => $new_block_mirror_id
							);

						}

					}

				}

			/* Move layout meta from postmeta capable layouts to the wp_postmeta table */
			if ( is_array($template_layout_options) ) {

				foreach ( $template_layout_options as $template_layout_options_group => $template_layout_options_group_options ) {

					foreach ( $template_layout_options_group_options as $option => $option_value ) {

						if ( in_array($option, array('customized', 'blocks')) )
							continue;

						$global = $option == 'template' ? false : true;

						PadmaLayoutOption::set( PadmaLayout::format_old_id( $template_layout_id ), $option, $option_value, $global, $template_layout_options_group, $template );


					}

				}

			}

		}


	}


	delete_option('pu_37_upgrade_blocks');

	return add_option('pu_37_upgrade_blocks', $upgraded_blocks, false, 'no');

}


function padma_upgrade_37_extract_layout_options() {

	global $wpdb;

	$all_layout_options = array();

	$post_query = $wpdb->get_col( "SELECT ID FROM $wpdb->posts" );
	$revisions_query = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_status = 'inherit' AND post_type != 'attachment'" );

	/* Build layout options catalog */
		foreach ( $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'padma_%'" ) as $option_obj ) {

			if ( $option_obj->option_name == 'padma_layout_options_catalog' || (strpos( $option_obj->option_name, 'padma_') === 0 && substr( $option_obj->option_name, -8) == '_preview') )
				continue;

			if ( strpos( $option_obj->option_name, 'padma_') !== 0 || strpos( $option_obj->option_name, 'layout_options') === false )
				continue;

			/* Check against bad option values */
			$bad_option_values = array(
				'a:1:{s:7:"general";a:1:{s:6:"blocks";a:0:{}}}',
				'a:3:{s:7:"general";a:4:{s:8:"template";s:0:"";s:10:"hide-title";s:0:"";s:15:"alternate-title";s:0:"";s:9:"css-class";s:0:"";}s:14:"post-thumbnail";a:1:{s:8:"position";s:0:"";}s:3:"seo";a:9:{s:5:"title";s:0:"";s:11:"description";s:0:"";s:7:"noindex";s:5:"false";s:8:"nofollow";s:5:"false";s:9:"noarchive";s:5:"false";s:9:"nosnippet";s:5:"false";s:5:"noodp";s:5:"false";s:6:"noydir";s:5:"false";s:12:"redirect-301";s:0:"";}}',
				'a:2:{s:7:"general";a:4:{s:8:"template";s:0:"";s:10:"hide-title";s:0:"";s:15:"alternate-title";s:0:"";s:9:"css-class";s:0:"";}s:14:"post-thumbnail";a:1:{s:8:"position";s:0:"";}}',
				'a:2:{s:7:"general";a:4:{s:9:"css-class";s:0:"";s:8:"template";s:0:"";s:10:"hide-title";s:0:"";s:15:"alternate-title";s:0:"";}s:14:"post-thumbnail";a:1:{s:8:"position";s:4:"left";}}',
				'a:2:{s:7:"general";a:6:{s:8:"template";s:0:"";s:10:"hide-title";s:0:"";s:15:"alternate-title";s:0:"";s:9:"css-class";s:0:"";s:16:"page_title_alias";s:0:"";s:20:"page_sub_title_alias";s:0:"";}s:14:"post-thumbnail";a:1:{s:8:"position";s:0:"";}}',
				'a:2:{s:7:"general";a:4:{s:8:"template";s:0:"";s:10:"hide-title";s:0:"";s:15:"alternate-title";s:0:"";s:9:"css-class";s:0:"";}s:14:"post-thumbnail";a:1:{s:8:"position";s:4:"left";}}'
			);

			if ( in_array($option_obj->option_value, $bad_option_values) )
				continue;

			$option = $option_obj->option_name;
			$option_value = $option_obj->option_value;

			/* Figure out template ID and layout */
				if ( strpos($option, 'padma_|skin=') !== 0 ) {

					$template = 'base';
					$layout = str_replace('padma_layout_options_', '', $option);

				} else {

					$option_name_fragments = explode('|_', str_replace('padma_|skin=', '', $option));

					$template = $option_name_fragments[0];
					$layout = str_replace('layout_options_', '', $option_name_fragments[1]);

				}

				/* If the layout ID is template then change the underscore to a hyphen */
				if ( strpos($layout, 'template_') === 0 )
					$layout = str_replace('template_', 'template-', $layout);

			/* If the layout is numeric, then check if the post even exists and isn't a revision.  If it does not exist or is a revision, delete it! */
				if ( is_numeric($layout) ) {

					/* If the post row is false (doesn't exist) don't process layout */
					if ( !in_array($layout, $post_query) ) {
						continue;
					}

					/* If the post row is post revision then don't process it */
					if ( in_array( $layout, $revisions_query ) ) {
						continue;
					}


				}

			/* Add to return array */
				if ( !isset($all_layout_options[$template]) )
					$all_layout_options[$template] = array();

				$all_layout_options[$template][$layout] = padma_maybe_unserialize($option_value);

		}

	return $all_layout_options;

}


function padma_upgrade_37_setup_mirroring() {

	$upgraded_blocks = get_option('pu_37_upgrade_blocks');
	$upgraded_wrappers = get_option('pu_37_upgrade_wrappers');

	foreach ( $upgraded_blocks as $template => $template_blocks ) {

		foreach ( $template_blocks as $old_block_id => $new_blocks_info ) {

			if ( isset($new_blocks_info['id']) ) {
				$new_blocks_info = array($new_blocks_info);
			}

			foreach ( $new_blocks_info as $new_block_info ) {

				if ( ! $mirror_block = padma_get( padma_get( 'mirror-block', $new_block_info ), $template_blocks ) )
					continue;

				$mirror_id = padma_get( 'id', $mirror_block );

				if ( $mirrored_block_mirror_id = padma_get( 'mirror-block', $mirror_block ) ) {

					if ( $mirrored_block_mirror = padma_get( $mirrored_block_mirror_id, $template_blocks ) ) {

						if ( padma_get( 'type', $new_block_info ) == padma_get( 'type', $mirrored_block_mirror ) ) {
							continue;
						}

					}

				}

				if ( padma_get( 'type', $new_block_info ) != padma_get( 'type', $mirror_block ) ) {
					continue;
				}

				if ( padma_get( 'id', $new_block_info ) == padma_get( 'id', $mirror_block ) ) {
					continue;
				}

				PadmaBlocksData::update_block( $new_block_info['id'], array(
					'mirror_id' => $mirror_id,
					'template'  => $template
				) );

			}

		}

	}


	foreach ( $upgraded_wrappers as $template => $template_wrappers ) {

		foreach ( $template_wrappers as $old_wrapper_id => $new_wrappers_info ) {

			if ( isset($new_wrappers_info['id']) ) {
				$new_wrappers_info = array($new_wrappers_info);
			}

			foreach ( $new_wrappers_info as $new_wrapper_info ) {

				if ( ! $mirror_wrapper = padma_get( PadmaWrappers::format_wrapper_id(padma_get( 'mirror-wrapper', $new_wrapper_info )), $template_wrappers ) )
					continue;

				$mirror_id = padma_get( 'id', $mirror_wrapper );

				PadmaWrappersData::update_wrapper( $new_wrapper_info['id'], array(
					'mirror_id' => $mirror_id,
					'template'  => $template
				) );

			}

		}

	}

}


function padma_upgrade_37_setup_options() {

	global $wpdb;

	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'pu_|template=%'" );

	update_option( 'padma_|skin=base|_option_group_design', get_option( 'padma_option_group_design', array() ) );
	update_option( 'padma_|skin=base|_option_group_general', get_option( 'padma_option_group_general', array() ) );
	update_option( 'padma_|skin=base|_option_group_templates', get_option( 'padma_option_group_templates', array() ) );

	wp_cache_flush();

	/* Pull settings from padma_option_group_general and padma_|skin=base|_option_group_general */
	$option_group_general = get_option('padma_option_group_general');

	foreach ( $option_group_general as $option => $option_value ) {

		$options_to_remove = array(
			'cache',
			'colorpicker-swatches',
			'webfont-cache'
		);

		if ( in_array($option, $options_to_remove) || strpos( $option, 'merged-default-design-data-' ) === 0 ) {
			unset( $option_group_general[ $option ] );
		}

	}

	update_option('padma_option_group_general', $option_group_general);

}


function padma_upgrade_37_fix_design_data() {

	$upgraded_blocks = get_option('pu_37_upgrade_blocks');
	$upgraded_wrappers = get_option('pu_37_upgrade_wrappers');

	/* Sort the block and wrapper mappings by descending number that way when we do a simple recursive find and replace the small block IDs won't mess up the larger block IDs.
	Example: Replacing block-1 before block-11 is replaced would be bad news */
	/* Pull settings from padma_option_group_general and padma_|skin=base|_option_group_general */
	foreach ( $upgraded_blocks as $template => $template_blocks ) {

		$template_design_settings = get_option( 'padma_|skin=' . $template . '|_option_group_design', array() );

		if ( !is_array($template_design_settings) )
			continue;

		$template_design_settings_json = json_encode($template_design_settings);

		krsort( $template_blocks );

		foreach ( $template_blocks as $old_block_id => $new_block_info ) {

			if ( !isset($new_block_info['id']) ) {
				$new_block_info = $new_block_info[0];
			}

			$template_design_settings_json = str_replace( 'block-' . $old_block_id, 'block-' . $new_block_info['id'], $template_design_settings_json );

		}

		update_option( 'padma_|skin=' . $template . '|_option_group_design', json_decode($template_design_settings_json, true) );

	}


	foreach ( $upgraded_wrappers as $template => $template_wrappers ) {

		$template_design_settings = get_option( 'padma_|skin=' . $template . '|_option_group_design', array() );

		if ( !is_array($template_design_settings) )
			continue;

		$template_design_settings_json = json_encode($template_design_settings);

		krsort( $template_wrappers );

		foreach ( $template_wrappers as $old_wrapper_id => $new_wrapper_info ) {

			if ( !isset($new_wrapper_info['id']) ) {
				$new_wrapper_info = $new_wrapper_info[0];
			}

			$template_design_settings_json = str_replace( 'wrapper-' . $old_wrapper_id, 'wrapper-' . $new_wrapper_info['id'], $template_design_settings_json );

		}

		update_option( 'padma_|skin=' . $template . '|_option_group_design', json_decode($template_design_settings_json, true) );

	}

}


function padma_upgrade_37_rename_and_delete_old_options() {

	global $wpdb;

	/* Change option names */
	$wpdb->query( "UPDATE IGNORE $wpdb->options SET option_name = replace(option_name, 'padma_|skin=', 'pu_|template=') WHERE option_name LIKE 'padma_|skin=%'" );

	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'padma_%layout_options_%'" );

	/* Delete old options */
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name = 'padma_option_group_design'" );
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name = 'padma_option_group_templates'" );

	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'padma%option_group_wrappers'" );
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'padma%option_group_blocks'" );
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'padma%option_group_block-actions'" );

}