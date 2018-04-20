<?php

class PadmaCompatibilityHeadway {
	
	
	public static function init() {

		if(!PadmaOption::get('headway-support'))
			return;
		
		self::load();

	}

	public static function load(){

		$GLOBALS['headway_default_element_data'] = $GLOBALS['padma_default_element_data'];
		
		PadmaCompatibilityHeadway::padma_define_headway_constants();

		Padma::load(array(
			'api/api-admin-meta-box',
			'api/api-box',
			'admin/admin-write' => true,
			'admin/admin-pages',
			'admin/api-admin-inputs'
		));

		require PADMA_LIBRARY_DIR . '/compatibility/headway/functions.php';	
		require PADMA_LIBRARY_DIR . '/compatibility/headway/abstract.php';	

		PadmaCompatibilityHeadway::padma_declare_headway_classes();
		Headway::init();

	}

	public static function padma_define_headway_constants(){

		define('HEADWAY_VERSION', 				"3.8.9");
		define('HEADWAY_DIR', 					PADMA_DIR);
		define('HEADWAY_LIBRARY_DIR', 			PADMA_LIBRARY_DIR);
		define('HEADWAY_SITE_URL', 				PADMA_SITE_URL);
		define('HEADWAY_DASHBOARD_URL', 		PADMA_DASHBOARD_URL);
		define('HEADWAY_EXTEND_URL', 			PADMA_EXTEND_URL);
		define('HEADWAY_DEFAULT_SKIN', 			PADMA_DEFAULT_SKIN);
		define('HEADWAY_CHILD_THEME_ACTIVE', 	PADMA_CHILD_THEME_ACTIVE);
		define('HEADWAY_CHILD_THEME_DIR', 		PADMA_CHILD_THEME_DIR);
		define('HEADWAY_UPLOADS_DIR', 			PADMA_UPLOADS_DIR);
		define('HEADWAY_CACHE_DIR', 			PADMA_CACHE_DIR);	

	}


	public static function padma_declare_headway_classes(){

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

		//debug($padmaClassArray);
		foreach ($padmaClassArray as $padmaClass => $methods) {

			$headwayClassName 	= str_replace('Padma', 'Headway', $padmaClass);
			$headwayTrait 		= $headwayClassName . 'Trait';
			

			$definition  = "trait $headwayTrait {";
			foreach ($methods as $key => $method) {

				// visibility
				$methodChecker 	= new ReflectionMethod($padmaClass,$method);
				$params 		= $methodChecker->getParameters();

				// is public/private/protected
				if($methodChecker->isPublic()){
					$definition .= 'public ';

				}elseif ($methodChecker->isPrivate()) {
					$definition .= 'private ';

				}elseif ($methodChecker->isProtected()) {
					$definition .= 'protected ';
				}
				
				// is static
				if($methodChecker->isStatic()){
					$definition .= 'static ';
				}

				$definition .= 'function ' . $method . '(';
				

				// Arguments
				$argumentsDefinition 	= '';
				$argumentsUsage 		= '';
				foreach ($params as $param) {

					if($param->isArray()){
						$argumentsDefinition .= 'array ';
					}

					if($param->isPassedByReference()){
						$argumentsDefinition .= '&';
					}

					$argumentsDefinition 	.= '$' . $param->getName();
					$argumentsUsage 		.= '$' . $param->getName();
					
					if($param->isOptional()){
						if($param->isDefaultValueAvailable()){
							$argumentsDefinition .= '= ' . var_export($param->getDefaultValue(),true);
						}else{
							$argumentsDefinition .= '= null';
						}
					}

					$argumentsDefinition 	.= ',';
					$argumentsUsage 		.= ',';
				}

				$argumentsDefinition 	= rtrim($argumentsDefinition,',');
				$argumentsUsage 		= rtrim($argumentsUsage,',');

				$definition .= $argumentsDefinition;
				$definition .= '){';
				$definition .= "parent::$method($argumentsUsage);";
				$definition .= '}';

			}


			$definition .= "}";
			$definition .= "class $headwayClassName extends $padmaClass {";
			$definition .= "use $headwayTrait;";
			$definition .= "}";

			
			try{
				eval($definition);						
			}catch(Exception $e){
				debug($e->getMessage());
			}

		}
	}	
}