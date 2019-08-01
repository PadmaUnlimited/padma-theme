<?php

global $registry;

$registry = array(

	// Abstract
	'PadmaAdminMetaBoxAPI' => 'abstract/api-admin-meta-box',
	'PadmaNotice'	=> 'abstract/notice',
	'PadmaBlockAPI'	=> 'abstract/api-block',
	'PadmaVisualEditorBoxAPI' => 'abstract/api-box',
	'PadmaVisualEditorPanelAPI' => 'abstract/api-panel',
	'PadmaWebFontProvider' => 'abstract/web-fonts-api',

	// Admin
	'PadmaAdmin'	=> 'admin/admin',
	'PadmaAdminBar'	=> 'admin/admin-bar',
	'PadmaMetaBoxTemplate'	=> 'admin/admin-meta-boxes',
	'PadmaMetaBoxTitleControl'	=> 'admin/admin-meta-boxes',
	'PadmaMetaBoxDisplay'	=> 'admin/admin-meta-boxes',
	'PadmaMetaBoxPostThumbnail'	=> 'admin/admin-meta-boxes',
	'PadmaMetaBoxSEO'	=> 'admin/admin-meta-boxes',
	'PadmaAdminPages'	=> 'admin/admin-pages',
	'PadmaAdminWrite'	=> 'admin/admin-write',
	'PadmaAdminInputs' => 'admin/api-admin-inputs',

	// API	
	'PadmaPadmaBlockOptionsAPI' => 'api/api-block',
	'PadmaChildThemeAPI' => 'api/api-child-theme',
	'PadmaElementAPI' => 'api/api-element',

	// Blocks
	'PadmaBlocks'	=> 'blocks/blocks',

	// Common
	'Padma'	=> 'common/application',
	'PadmaBlocksAnywhere'	=> 'common/blocks-anywhere',
	'PadmaCapabilities'	=> 'common/capabilities',
	'PadmaCompiler'	=> 'common/compiler',
	'PadmaFeed'	=> 'common/feed',
	'PadmaCapabilities'	=> 'common/capabilities',
	'PadmaGutenbergBlocks'	=> 'common/gutenberg-blocks',
	'PadmaHttp2ServerPush'	=> 'common/http2-server-push',
	'PadmaImageResize'	=> 'common/image-resizer',
	'PadmaLayout'	=> 'common/layout',
	'PadmaNotices'	=> 'common/notices',
	'PadmaPlugins'	=> 'common/plugins',
	'PadmaQuery'	=> 'common/query',
	'PadmaResponsiveGrid' => 'common/responsive-grid',
	'PadmaRoute' => 'common/route',
	'PadmaSchema' => 'common/schema',
	'PadmaSeo' => 'common/seo',
	'PadmaSettings' => 'common/settings',
	'PadmaSocialOptimization' => 'common/social-optimization',
	'PadmaTemplates' => 'common/templates',

	// Compatibility
	'PadmaCompatibilityAmember'	=> 'compatibility/amember/compatibility-amember',
	'PadmaCompatibilityDiviBuilder'	=> 'compatibility/divi-builder/compatibility-divi-builder',
	'PadmaCompatibilityHeadway'	=> 'compatibility/headway/compatibility-headway',
	'HeadwayAdminMetaBoxAPI'	=> 'compatibility/headway/abstract',
	'HeadwayBlockAPI'	=> 'compatibility/headway/abstract',
	'HeadwayVisualEditorPanelAPI'	=> 'compatibility/headway/abstract',
	'PadmaCompatibilityWooCommerce'	=> 'compatibility/woocommerce/compatibility-woocommerce',
	'PadmaCompatibilityWpml'	=> 'compatibility/wpml/compatibility-wpml',

	// Data
	'PadmaBlocksData'	=>	'data/data-blocks',
	'PadmaElementsData'	=>	'data/data-elements',
	'PadmaLayoutOption'	=>	'data/data-layout-options',
	'PadmaOption'	=>	'data/data-options',
	'PadmaDataPortability'	=>	'data/data-portability',
	'PadmaSkinOption'	=>	'data/data-skin-options',
	'PadmaDataSnapshots'	=>	'data/data-snapshots',
	'PadmaWrappersData'	=>	'data/data-wrappers',

	// Display
	'PadmaDisplay'	=> 'display/display',
	'PadmaGridRenderer'	=> 'display/grid-renderer',
	'PadmaHead'	=> 'display/head',
	'PadmaLayoutRenderer'	=> 'display/layout-renderer',

	//Elements
	'PadmaElements'	=> 'elements/elements',
	'PadmaJSProperties'	=> 'elements/js-properties',
	'PadmaElementProperties'	=> 'elements/properties',
	'PadmaElementProperties'	=> 'elements/properties',

	// Fonts
	'PadmaGoogleFonts'	=>	'fonts/google-fonts',
	'PadmaTraditionalFonts'	=>	'fonts/traditional-fonts',
	'PadmaFonts'	=>	'fonts/traditional-fonts',
	'PadmaWebFontsLoader'	=>	'fonts/web-fonts-loader',

	// Maintenance
	'PadmaMaintenance'	=>	'maintenance/upgrades',
	
	// Media
	'PadmaResponsiveGridDynamicMedia'	=>	'media/dynamic/responsive-grid',

	// Visual Editor
	'PadmaVisualEditorDisplay'	=>	'visual-editor/display',
	'PadmaIframeDummyContent'	=>	'visual-editor/dummy-content',
	'PadmaVisualEditorIframeGrid'	=>	'visual-editor/iframe-grid',
	'PadmaLayoutSelector'	=>	'visual-editor/layout-selector',
	'PadmaLayoutSelector'	=>	'visual-editor/layout-selector',
	'PadmaVisualEditorPreview'	=>	'visual-editor/preview',
	'PadmaVisualEditorAJAX'	=>	'visual-editor/visual-editor-ajax',
	'PadmaVisualEditor'	=>	'visual-editor/visual-editor',	
	'PadmaGridManagerBox'	=>	'visual-editor/boxes/grid-manager',
	'PadmaSnapshotsBox'	=>	'visual-editor/boxes/snapshots',
	'PadmaPropertyInputs'	=>	'visual-editor/panels/design/property-inputs',
	'PadmaSidePanelDesignEditor'	=>	'visual-editor/panels/design/side-panel-design-editor',
	'GridSetupPanel'	=>	'visual-editor/panels/grid/setup',

	// Widgets
	'PadmaWidgets'	=>	'widgets/widgets',

	// Wrappers
	'PadmaWrapperOptions'	=>	'wrappers/wrapper-options',
	'PadmaWrappers'	=>	'wrappers/wrappers',

);

$registry = apply_filters('padma_class_registry', $registry);


spl_autoload_register(function ($class) {

	global $registry;
	$file = '';

	if ( isset($registry[$class]) )
		$file = $registry[$class];

	if(!is_file($file))
		$file = dirname( __FILE__ ) . '/' . $file . '.php';
	
	if ( file_exists( $file ) && strpos($class, 'Padma') == 0 )
		include $file;

});