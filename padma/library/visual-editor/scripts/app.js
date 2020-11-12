require.config({
	paths: {
		knockout: 'deps/knockout',
		underscore: 'deps/underscore',
		jquery: 'jquery.min',

		/* jQuery Plugins */
		jqueryUI: 'deps/jquery.ui',
		qtip: 'deps/jquery.qtip'
	},
	shim: {
	    underscore: {
	      exports: '_'
		}
	}
});

require(['jquery', 'util.loader'], function($) {

	/* Start loading indidcator */
	startTitleActivityIndicator();
	//iframe.showIframeLoadingOverlay();

	/* Parse the JSON in the Padma l10n array */	
	Padma.blockTypeURLs 			= $.parseJSON(Padma.blockTypeURLs.replace(/&quot;/g, '"'));
	Padma.allBlockTypes 			= $.parseJSON(Padma.allBlockTypes.replace(/&quot;/g, '"'));
	Padma.ranTour 					= $.parseJSON(Padma.ranTour.replace(/&quot;/g, '"'));
	Padma.designEditorProperties 	= $.parseJSON(Padma.designEditorProperties.replace(/&quot;/g, '"'));
	Padma.layouts 					= $.parseJSON(Padma.layouts.replace(/&quot;/g, '"'));

	/* Setup modules */
	require(['modules/layout-selector'], function(layoutSelector) {
		layoutSelector.init();
	});

	require(['modules/panel', 'modules/iframe'], function(panel, iframe) {
		panel.init();
		iframe.init();
	});

	require(['modules/menu'], function(menu) {
		menu.init();
	});

	require(['modules/snapshots'], function(snapshots) {
		snapshots.init();
	});

	/* Init tour */
	require(['util.tour'], function (tour) {

		if ( Padma.ranTour[Padma.mode] == false && Padma.ranTour.legacy == false ) {
			tour.start();
		}

	});

	/**
	 *
	 * Load mode switcher
	 *
	 */
	require(['switch.mode'], function(switchMode) {
		switchMode.init();
	});


	/* Load helpers all at once since they're used everywhere */
	require(['helper.data', 'helper.blocks', 'helper.wrappers', 'helper.context-menus', 'helper.notifications', 'helper.boxes', 'helper.history'], function(data, blocks, wrappers, contextMenus, notifications, boxes, history) {
		history.init();
	});


	/**
	 *
	 * Offline check
	 *
	 */
	require(['util.offline'], function(offline) {
		offline.init();
	});
	
	

	/* Load in the appropriate modules depending on the mode */
	switch ( Padma.mode ) {

		case 'grid':

			require(['modules/grid/mode-grid', 'modules/iframe', 'modules/layout-selector'], function(modeGrid) {
				
				Padma.instance = modeGrid;

				modeGrid.init();
				waitForIframeLoad(modeGrid.iframeCallback);
			});

		break;

		case 'design':

			require(['modules/design/mode-design', 'util.preview', 'modules/iframe', 'modules/layout-selector'], function(modeDesign, devicePreview) {
			//require(['modules/design/mode-design', 'util.preview', 'modules/content-selector', 'modules/iframe', 'modules/layout-selector'], function(modeDesign, devicePreview, contentSelector) {
				
				/**
				 *
				 * Load Devices Preview 
				 *
				 */
				devicePreview.init();


				Padma.instance = modeDesign;

				modeDesign.init();
				waitForIframeLoad(modeDesign.iframeCallback);

				/*
					- Disable until future release
				
				contentSelector.init();

				if(Padma.viewModels.layoutSelector.currentLayout().search('template') != -1){
					$('#content-selector-select').show();
				}else{
					$('#content-selector-select').hide();
				}
				*/
			});

		break;

	}

	/* After everything is loaded show the Visual Editor */
	$(document).ready(function() {

		$('body').addClass('show-ve');

	});

	$(window).bind('load', function() {

		/* Remove VE loader overlay after we know page has loaded */
		setTimeout(function () {
			$('div#ve-loading-overlay').remove();
		}, 1000);

	});


});