<?php

class PadmaThemeUpdater{

	public static function init() {

		if(!class_exists('Puc_v4_Factory')){			
			if ( PADMA_CHILD_THEME_ACTIVE === true ){
				require_once get_stylesheet_directory() . '/library/common/lib/plugin-update-checker/plugin-update-checker.php';
			}else{
				require_once get_template_directory() . '/library/common/lib/plugin-update-checker/plugin-update-checker.php';
			}
		}

		$updateChecker = Puc_v4_Factory::buildUpdateChecker(
			'http://cdn.padmaunlimited.com/software/?action=get_metadata&slug=padma',
			__FILE__,
			'padma'
		);

		debug($updateChecker);
		
	}

}