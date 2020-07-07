<?php

class PadmaCompatibilityBlox {


	public static function init() {

		if(!PadmaOption::get('bloxtheme-support'))
			return;

		self::load();

	}

	public static function load(){

		$GLOBALS['blox_default_element_data'] = $GLOBALS['padma_default_element_data'];

		PadmaCompatibilityBlox::padma_define_bloxtheme_constants();

		Padma::load(array(
			'abstract/api-admin-meta-box',
			'abstract/api-box',
			'admin/admin-write' => true,
			'admin/admin-pages',
			'admin/api-admin-inputs'
		));

		require PADMA_LIBRARY_DIR . '/compatibility/blox/functions.php';	
		require PADMA_LIBRARY_DIR . '/compatibility/blox/abstract.php';	

		add_action('after_setup_theme',function(){

			PadmaCompatibilityBlox::padma_declare_bloxtheme_classes();
			Blox::init();

		});

	}

	public static function padma_define_bloxtheme_constants(){

		define('BLOX_VERSION', 				"1.0.6");
		define('BLOX_DIR', 					PADMA_DIR);
		define('BLOX_LIBRARY_DIR', 			PADMA_LIBRARY_DIR);
		define('BLOX_SITE_URL', 			PADMA_SITE_URL);
		define('BLOX_DASHBOARD_URL', 		PADMA_DASHBOARD_URL);
		define('BLOX_EXTEND_URL', 			PADMA_EXTEND_URL);
		define('BLOX_DEFAULT_SKIN', 		PADMA_DEFAULT_SKIN);
		define('BLOX_CHILD_THEME_ACTIVE', 	PADMA_CHILD_THEME_ACTIVE);
		define('BLOX_CHILD_THEME_DIR', 		PADMA_CHILD_THEME_DIR);
		define('BLOX_UPLOADS_DIR', 			PADMA_UPLOADS_DIR);
		define('BLOX_CACHE_DIR', 			PADMA_CACHE_DIR);	

	}


	public static function padma_declare_bloxtheme_classes(){

		$padmaClassArray = array();

		foreach (get_declared_classes() as $key => $padmaClass) {

			if (strpos($padmaClass, 'Padma') !== false) {

				if(
					$padmaClass == 'PadmaUpdater' ||
					$padmaClass == 'PadmaLifeSaver' ||
					$padmaClass == 'PadmaLifeSaver\helpers\Plugin' ||
					$padmaClass == 'PadmaLifeSaver\helpers\json' ||
					$padmaClass == 'PadmaAdminMetaBoxAPI' ||
					$padmaClass == 'PadmaBlockAPI' ||
					$padmaClass == 'PadmaVisualEditorBoxAPI' ||
					$padmaClass == 'PadmaVisualEditorPanelAPI' 
					)
					continue;

				$padmaClassArray[$padmaClass] = get_class_methods($padmaClass);

			}
		}

		foreach ($padmaClassArray as $padmaClass => $methods) {

			$bloxClassName 	= str_replace('Padma', 'Blox', $padmaClass);
			if ( ! class_exists( $bloxClassName ) ){
				$status = class_alias($padmaClass, $bloxClassName);
				if (!$status) {
					error_log('Can\'t create class: ' . $bloxClassName);
				}
			}
		}
	}
}