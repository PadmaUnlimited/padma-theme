<?php
class PadmaTemplates {


	public static function get($id) {

		$all_skins = self::get_all(true);

		return padma_get($id, $all_skins);

	}


	public static function get_all($associative = false, $info_only = true) {

		$skins = array(
			array(
				'id' => 'base',
				'name' => 'Base',
				'author' => 'Padma Theme',
				'version' => PADMA_VERSION,
				'image-url' => padma_url() . '/screenshot.png',
				'description' => null
			)
		);

		$installed_skins = PadmaOption::get_group('skins');

		if ( !$installed_skins ) {
			$installed_skins = array();
		}
				
		if ( !empty($installed_skins) && is_array($installed_skins) ) {

			foreach ( $installed_skins as $installed_skin_id => $installed_skin ) {

				$skin = array_merge(array(
					'id' => $installed_skin_id, 
					'description' => null,
					'version' => null,
					'author' => null
				), $installed_skin);

				if ( $info_only ) {

					foreach ( $skin as $skin_key => $skin_value ) {

						$keys_allowed = array(
							'id',
							'name',
							'author',
							'version',
							'image-url',
							'description'
						);

						if ( !in_array($skin_key, $keys_allowed) ) {
							unset($skin[$skin_key]);
						}

					}

				}

				$skins[] = $skin;

			}

		}

		/* Resize all images */
		foreach ( $skins as $skin_index => $skin ) {

			$skins[$skin_index]['image-url'] = padma_resize_image(padma_get('image-url', $skin), 400, 350);

			if ( !$skins[$skin_index]['image-url'] || is_wp_error($skins[$skin_index]['image-url']) )
				$skins[$skin_index]['image-url'] = padma_get('image-url', $skin);

		}

		
		if ( $associative ) {

			$associative_skins = array();

			foreach ( $skins as $skin )
				$associative_skins[$skin['id']] = $skin;

			return $associative_skins;

		}

		return $skins;

	}


	public static function get_active() {

		$current_skin_id = PadmaOption::get('current-skin', 'general', PADMA_DEFAULT_SKIN);
		$all_skins = self::get_all(true);

		return padma_get($current_skin_id, $all_skins, padma_get(PADMA_DEFAULT_SKIN, $all_skins));

	}


	public static function get_active_id() {

		$active_skin = self::get_active();

		return $active_skin['id'];

	}


	public static function add() {



	}


	public static function delete() {



	}


}