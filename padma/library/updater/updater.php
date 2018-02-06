<?php

class PadmaUpdater{

	public static function init() {

		if(!class_exists('Puc_v4_Factory')){			
			if ( PADMA_CHILD_THEME_ACTIVE === true ){
				require_once get_stylesheet_directory() . '/library/common/lib/update-checker/plugin-update-checker.php';
			}else{
				require_once get_template_directory() . '/library/common/lib/update-checker/plugin-update-checker.php';
			}
		}

		$updateChecker = Puc_v4_Factory::buildUpdateChecker(
			PADMA_CDN_URL . 'software/?action=get_metadata&slug=padma',
			PADMA_DIR,
			'padma'
		);
	}

}