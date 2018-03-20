<?php
class PadmaDataSnapshots {


	public static function init() {


	}


	public static function list_snapshots() {

		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT id, timestamp, comments FROM $wpdb->pu_snapshots WHERE template = '%s' ORDER BY timestamp DESC", PadmaOption::$current_skin));

	}


	public static function save_snapshot($throttle = false) {

		global $wpdb;

		/* Only allow snapshots to be saved every 15 minutes if $throttle is true */
		if ( $throttle && $last_snapshot_timestamp = PadmaSkinOption::get('last-snapshot') ) {

			if ( time() < strtotime('+15 minutes', $last_snapshot_timestamp) )
				return false;

		}

		$wp_options_prefix = 'padma_|template=' . PadmaOption::$current_skin . '|_';

		$data_wp_options = padma_array_map_recursive( 'padma_maybe_unserialize', $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name LIKE '%s'", $wp_options_prefix . '%' ), ARRAY_A ));
		$data_wp_postmeta = padma_array_map_recursive( 'padma_maybe_unserialize', $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE '%s'", '_pu_|template=' . PadmaOption::$current_skin . '|_%' ), ARRAY_A ));
		$data_wp_pu_layout_meta = padma_array_map_recursive( 'padma_maybe_unserialize', $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->pu_layout_meta WHERE template = '%s'", PadmaOption::$current_skin ), ARRAY_A ));
		$data_pu_wrappers = padma_array_map_recursive( 'padma_maybe_unserialize', $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->pu_wrappers WHERE template = '%s'", PadmaOption::$current_skin ), ARRAY_A ));
		$data_pu_blocks = padma_array_map_recursive( 'padma_maybe_unserialize', $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->pu_blocks WHERE template = '%s'", PadmaOption::$current_skin ), ARRAY_A ));

		$insert_args = array(
			'template' => PadmaOption::$current_skin,
			'comments' => padma_post('snapshot_comments'),
			'timestamp' => current_time('mysql', 1), /* GMT */
			'data_wp_options' => padma_maybe_serialize( $data_wp_options ),
			'data_wp_postmeta' => padma_maybe_serialize( $data_wp_postmeta ),
			'data_pu_layout_meta' => padma_maybe_serialize( $data_wp_pu_layout_meta ),
			'data_pu_wrappers' => padma_maybe_serialize( $data_pu_wrappers ),
			'data_pu_blocks' => padma_maybe_serialize( $data_pu_blocks )
		);

		$snapshotquery = $wpdb->insert($wpdb->pu_snapshots, $insert_args);

		if ( is_wp_error($snapshotquery) ) {

			$output['errors'][] = $snapshotquery->get_error_code() . ($snapshotquery->get_error_message() ? ' - ' . $snapshotquery->get_error_code() : '');

		} else {

			PadmaSkinOption::set('last-snapshot', time());

			return array(
				'id' => $wpdb->insert_id,
				'comments' => $insert_args['comments'],
				'timestamp' => $insert_args['timestamp']
			);

		}

	}


	public static function rollback($rollback_id) {

		global $wpdb;

		if ( !$rollback = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->pu_snapshots WHERE id = %d AND template = '%s'", $rollback_id, PadmaOption::$current_skin), ARRAY_A) )
			return array('errors' => array('Snapshot does not exist.'));

		$rollback_process = self::process_rollback($rollback);

		return !is_wp_error($rollback_process) ? true : $rollback_process;

	}


		public static function process_rollback($data, $template = false) {

			global $wpdb;

			if ( !$template )
				$template = PadmaOption::$current_skin;

			$data_arrays = array(
				'data_wp_options',
				'data_wp_postmeta',
				'data_pu_layout_meta',
				'data_pu_wrappers',
				'data_pu_blocks'
			);

			/* Go through data and delete and insert */
			foreach ( $data_arrays as $data_array_id ) {

				$data_set = padma_maybe_unserialize(padma_get($data_array_id, $data));
				$table_name = str_replace(array('data_', 'wp_'), '', $data_array_id);
				$wpdb_table_name = $wpdb->{$table_name};

				/* Handle Padma tables */
				if ( strpos($table_name, 'pu_') === 0 ) {

					$delete_query = $wpdb->query($wpdb->prepare("DELETE FROM $wpdb_table_name WHERE template = '%s'", $template));

					foreach ( $data_set as $data_object ) {

						$insert_data = is_object($data_object) ? get_object_vars($data_object) : $data_object;
						$insert_data['template'] = $template;

						foreach ( $insert_data as $insert_data_key => $insert_data_value ) {

							if ( is_array($insert_data_value) ) {
								$insert_data[$insert_data_key] = padma_maybe_serialize($insert_data_value);
							}

						}

						$insert_query = $wpdb->insert($wpdb_table_name, $insert_data);

					}

				/* Handle WP options/postmeta */
				} else if ( $table_name == 'options' || $table_name == 'postmeta' ) {

					$prefix = $table_name == 'options' ? 'padma_|template=' . $template . '|_' : '_pu_|template=' . $template . '|_';
					$key_column = $table_name == 'options' ? 'option_name' : 'meta_key';

					$delete_query = $wpdb->query($wpdb->prepare("DELETE FROM $wpdb_table_name WHERE $key_column LIKE '%s'", $prefix . '%'));

					foreach ( $data_set as $data_object ) {

						$insert_data = is_object($data_object) ? get_object_vars($data_object) : $data_object;

						if ( isset($insert_data['option_id']) )
							unset($insert_data['option_id']);

						if ( isset($insert_data['meta_id']) )
							unset($insert_data['meta_id']);

						/* Build the key column ID with the correct template */
						$key_column_fragments = explode('|_', $insert_data[$key_column]);
						$key_column_id_without_prefix = $key_column_fragments[1];

						$insert_data[$key_column] = $prefix . $key_column_id_without_prefix;

						/* Fix wrapper IDs */
						if ( strpos($key_column_id_without_prefix, 'option_group_design') !== false ) {
							$insert_data['option_value'] = padma_preg_replace_json( "/-layout-[\w-]*/", '', $insert_data['option_value'] );
						}

						foreach ( $insert_data as $insert_data_key => $insert_data_value ) {

							if ( is_array($insert_data_value) ) {
								$insert_data[$insert_data_key] = padma_maybe_serialize($insert_data_value);
							}

						}

						$insert_query = $wpdb->insert($wpdb_table_name, $insert_data);

					}

				}

			}

			do_action('padma_snapshot_rollback');

			return $data;

		}


	public static function delete($id) {

		global $wpdb;

		return $wpdb->delete( $wpdb->pu_snapshots, array(
			'id' => $id,
			'template' => PadmaOption::$current_skin
		) );

	}


	public static function delete_by_template($template) {

		global $wpdb;

		return $wpdb->delete($wpdb->pu_snapshots, array(
			'template' => $template
		));

	}


	public static function get_table_info() {

		global $wpdb;

		$snapshots_info_query = $wpdb->get_row( $wpdb->prepare( "SHOW TABLE STATUS WHERE name = '%s'", $wpdb->pu_snapshots ), ARRAY_A );

		return array(
			'count' => $snapshots_info_query['Rows'],
			'size' => padma_human_bytes( $snapshots_info_query["Data_length"] + $snapshots_info_query["Index_length"] )
		);

	}


}