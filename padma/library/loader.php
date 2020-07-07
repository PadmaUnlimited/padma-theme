<?php

namespace Padma;

global $padma_registry;

$padma_registry = array(

	// Abstract.
	'Padma\PadmaAdminMetaBoxAPI'            => 'abstract/class-api-admin-meta-box',
	'Padma\PadmaNotice'                     => 'abstract/notice',
	'Padma\PadmaBlockAPI'                   => 'abstract/api-block',
	'Padma\PadmaVisualEditorBoxAPI'         => 'abstract/api-box',
	'Padma\PadmaVisualEditorPanelAPI'       => 'abstract/api-panel',
	'Padma\PadmaWebFontProvider'            => 'abstract/web-fonts-api',

	// Admin.
	'Padma\PadmaAdmin'                      => 'admin/admin',
	'Padma\PadmaAdminBar'                   => 'admin/admin-bar',
	'Padma\PadmaMetaBoxTemplate'            => 'admin/admin-meta-boxes',
	'Padma\PadmaMetaBoxTitleControl'        => 'admin/admin-meta-boxes',
	'Padma\PadmaMetaBoxDisplay'             => 'admin/admin-meta-boxes',
	'Padma\PadmaMetaBoxPostThumbnail'       => 'admin/admin-meta-boxes',
	'Padma\PadmaMetaBoxSEO'                 => 'admin/admin-meta-boxes',
	'Padma\PadmaAdminPages'                 => 'admin/admin-pages',
	'Padma\PadmaAdminWrite'                 => 'admin/admin-write',
	'Padma\PadmaAdminInputs'                => 'admin/api-admin-inputs',

	// API.
	'Padma\PadmaBlockOptionsAPI'            => 'api/api-block-options',
	'Padma\PadmaChildThemeAPI'              => 'api/api-child-theme',
	'Padma\PadmaElementAPI'                 => 'api/api-element',

	// Blocks.
	'Padma\PadmaBlocks'                     => 'blocks/blocks',

	// Common.
	'Padma\Padma'                           => 'common/application',
	'Padma\PadmaBlocksAnywhere'             => 'common/blocks-anywhere',
	'Padma\PadmaCapabilities'               => 'common/capabilities',
	'Padma\PadmaCompiler'                   => 'common/compiler',
	'Padma\PadmaFeed'                       => 'common/feed',
	'Padma\PadmaCapabilities'               => 'common/capabilities',
	'Padma\PadmaGutenbergBlocks'            => 'common/gutenberg-blocks',
	'Padma\PadmaHttp2ServerPush'            => 'common/http2-server-push',
	'Padma\PadmaImageResize'                => 'common/image-resizer',
	'Padma\PadmaLayout'                     => 'common/layout',
	'Padma\PadmaNotices'                    => 'common/notices',
	'Padma\PadmaPlugins'                    => 'common/plugins',
	'Padma\PadmaQuery'                      => 'common/query',
	'Padma\PadmaResponsiveGrid'             => 'common/responsive-grid',
	'Padma\PadmaRoute'                      => 'common/route',
	'Padma\PadmaSchema'                     => 'common/schema',
	'Padma\PadmaSeo'                        => 'common/seo',
	'Padma\PadmaSettings'                   => 'common/settings',
	'Padma\PadmaSocialOptimization'         => 'common/social-optimization',
	'Padma\PadmaTemplates'                  => 'common/templates',
	'Padma\PadmaMobileDetect'               => 'common/mobile-detect',

	// Compatibility.
	'Padma\PadmaCompatibilityAmember'       => 'compatibility/amember/compatibility-amember',
	'Padma\PadmaCompatibilityDiviBuilder'   => 'compatibility/divi-builder/compatibility-divi-builder',
	'Padma\PadmaCompatibilityHeadway'       => 'compatibility/headway/compatibility-headway',
	'Padma\HeadwayAdminMetaBoxAPI'                => 'compatibility/headway/abstract',
	'Padma\HeadwayBlockAPI'                       => 'compatibility/headway/abstract',
	'Padma\HeadwayVisualEditorPanelAPI'           => 'compatibility/headway/abstract',
	'Padma\PadmaCompatibilityWooCommerce'   => 'compatibility/woocommerce/compatibility-woocommerce',
	'Padma\PadmaCompatibilityWpml'          => 'compatibility/wpml/compatibility-wpml',

	// Data.
	'Padma\PadmaBlocksData'                 => 'data/data-blocks',
	'Padma\PadmaElementsData'               => 'data/data-elements',
	'Padma\PadmaLayoutOption'               => 'data/data-layout-options',
	'Padma\PadmaOption'                     => 'data/data-options',
	'Padma\PadmaDataPortability'            => 'data/data-portability',
	'Padma\PadmaSkinOption'                 => 'data/data-skin-options',
	'Padma\PadmaDataSnapshots'              => 'data/data-snapshots',
	'Padma\PadmaWrappersData'               => 'data/data-wrappers',

	// Display.
	'Padma\PadmaDisplay'                    => 'display/display',
	'Padma\PadmaGridRenderer'               => 'display/grid-renderer',
	'Padma\PadmaHead'                       => 'display/head',
	'Padma\PadmaLayoutRenderer'             => 'display/layout-renderer',

	// Elements.
	'Padma\PadmaElements'                   => 'elements/elements',
	'Padma\PadmaJSProperties'               => 'elements/js-properties',
	'Padma\PadmaElementProperties'          => 'elements/properties',
	'Padma\PadmaElementProperties'          => 'elements/properties',

	// Fonts.
	'Padma\PadmaGoogleFonts'                => 'fonts/google-fonts',
	'Padma\PadmaTraditionalFonts'           => 'fonts/traditional-fonts',
	'Padma\PadmaFonts'                      => 'fonts/traditional-fonts',
	'Padma\PadmaWebFontsLoader'             => 'fonts/web-fonts-loader',

	// Maintenance.
	'Padma\PadmaMaintenance'                => 'maintenance/upgrades',

	// Media.
	'Padma\PadmaResponsiveGridDynamicMedia' => 'media/dynamic/responsive-grid',

	// Visual Editor.
	'Padma\PadmaVisualEditorDisplay'        => 'visual-editor/display',
	'Padma\PadmaIframeDummyContent'         => 'visual-editor/dummy-content',
	'Padma\PadmaVisualEditorIframeGrid'     => 'visual-editor/iframe-grid',
	'Padma\PadmaLayoutSelector'             => 'visual-editor/layout-selector',
	'Padma\PadmaVisualEditorPreview'        => 'visual-editor/preview',
	'Padma\PadmaVisualEditorAJAX'           => 'visual-editor/visual-editor-ajax',
	'Padma\PadmaVisualEditor'               => 'visual-editor/visual-editor',
	'Padma\PadmaGridManagerBox'             => 'visual-editor/boxes/grid-manager',
	'Padma\PadmaSnapshotsBox'               => 'visual-editor/boxes/snapshots',
	'Padma\PadmaPropertyInputs'             => 'visual-editor/panels/design/property-inputs',
	'Padma\PadmaSidePanelDesignEditor'      => 'visual-editor/panels/design/side-panel-design-editor',
	'Padma\GridSetupPanel'                  => 'visual-editor/panels/grid/setup',

	// Widgets.
	'Padma\PadmaWidgets'                    => 'widgets/widgets',

	// Wrappers.
	'Padma\PadmaWrapperOptions'             => 'wrappers/wrapper-options',
	'Padma\PadmaWrappers'                   => 'wrappers/wrappers',

);

$padma_registry = apply_filters( 'padma_class_registry', $padma_registry );

spl_autoload_register(
	function ( $class ) {

		$namespace = 'Padma';

		if ( strpos( $class, $namespace ) !== 0 ) {
			return;
		}

		if ( strpos( $class, 'Padma' ) === 0 ) {

			global $padma_registry;
			$file = '';

			if ( isset( $padma_registry[ $class ] ) ) {
				$file = $padma_registry[ $class ];
			}

			if ( ! is_file( $file ) ) {
				$file = dirname( __FILE__ ) . '/' . $file . '.php';
			}

			if ( file_exists( $file ) ) {
				include_once $file;
			}
		}
	}
);
