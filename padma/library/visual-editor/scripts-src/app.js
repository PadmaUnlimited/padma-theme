require.config({
	paths: {
		knockout: 'deps/knockout',

		underscore: 'deps/underscore',

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

	/* Parse the JSON in the Blox l10n array */
	Blox = Padma.clone();
	Blox.blockTypeURLs = $.parseJSON(Blox.blockTypeURLs.replace(/&quot;/g, '"'));
	Blox.allBlockTypes = $.parseJSON(Blox.allBlockTypes.replace(/&quot;/g, '"'));
	Blox.ranTour = $.parseJSON(Blox.ranTour.replace(/&quot;/g, '"'));

	Blox.designEditorProperties = $.parseJSON(Blox.designEditorProperties.replace(/&quot;/g, '"'));

	Blox.layouts = $.parseJSON(Blox.layouts.replace(/&quot;/g, '"'));

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

		if ( Blox.ranTour[Blox.mode] == false && Blox.ranTour.legacy == false ) {
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

	/* Load in the appropriate modules depending on the mode */
	switch ( Blox.mode ) {

		case 'grid':

			require(['modules/grid/mode-grid', 'modules/iframe', 'modules/layout-selector'], function(modeGrid) {
				Blox.instance = modeGrid;

				modeGrid.init();
				waitForIframeLoad(modeGrid.iframeCallback);
			});

		break;

		case 'design':

			require(['modules/design/mode-design', 'modules/iframe', 'modules/layout-selector'], function(modeDesign) {
				Blox.instance = modeDesign;

				modeDesign.init();
				waitForIframeLoad(modeDesign.iframeCallback);
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