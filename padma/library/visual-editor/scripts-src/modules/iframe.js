define(['jquery', 'deps/itstylesheet', 'util.saving', 'util.usability', 'util.tooltips'], function($, itstylesheet, saving) {

	$i = function(element) {

		if ( typeof Blox.iframe == 'undefined' || typeof Blox.iframe.contents() == 'undefined' )
			return $();

		return Blox.iframe.contents().find(element);

	}

	$iDocument = function() {

		return $(Blox.iframe.contents());

	}


	loadIframe = function(callback, url) {

		if ( typeof url == 'undefined' || !url)
			var url = Blox.homeURL;

		/* Choose contents iframe or preview iframe depending on argument */
			var iframe = Blox.iframe;

		/* Make the title talk */
		startTitleActivityIndicator();
		showIframeLoadingOverlay();

		/* Close Grid Wizard */
		closeBox('grid-wizard');

		/* Build the URL */
			iframeURL = url;
			iframeURL = updateQueryStringParameter(iframeURL, 've-iframe', 'true');
			iframeURL = updateQueryStringParameter(iframeURL, 've-layout', encodeURIComponent(Blox.viewModels.layoutSelector.currentLayout()));
            iframeURL = updateQueryStringParameter(iframeURL, 've-layout-customized', Blox.viewModels.layoutSelector.currentLayoutCustomized());
            iframeURL = updateQueryStringParameter(iframeURL, 've-iframe-mode', Blox.mode);
			iframeURL = updateQueryStringParameter(iframeURL, 'rand', Math.floor(Math.random() * 100000001));

		/* Clear out existing iframe contents */
			if ( iframe.contents().find('.ui-blox-grid').length && typeof iframe.contents().find('.ui-blox-grid').bloxGrid != 'undefined' ) {
				iframe.contents().find('.ui-blox-grid').bloxGrid('destroy');
			}

			iframe.contents().find('*')
				.unbind()
				.remove();

		iframe[0].src = iframeURL;
		waitForIframeLoad(callback, iframe);

	}


	waitForIframeLoad = function(callback, iframeEl) {

		if ( typeof iframeEl == 'undefined' || !iframeEl )
			var iframeEl = Blox.iframe;

		/* Setup timeout */
			if ( typeof iframeTimeout == 'undefined' )
				iframeTimeout = setTimeout(iframe.loadTimeout, 40000);

		/* Check if iframe body has iframe-loaded class which is added via inline script in the footer of the iframe */
			if ( typeof iframeEl == 'undefined' || iframeEl.contents().find('body.iframe-loaded').length != 1 ) {

				return setTimeout(function() {
					waitForIframeLoad(callback, iframeEl);
				}, 100);

			}

		/* Cancel out timeout callback */
			clearTimeout(iframeTimeout);

		return iframe.loadCallback(callback);

	}


	showIframeOverlay = function() {
		
		var overlay = $('div#iframe-overlay');		
		overlay.show();
		
	}
	

	hideIframeOverlay = function(delay) {

		if ( typeof delay != 'undefined' && delay == false )
			return $('div#iframe-overlay').hide();
		
		/* Add a timeout for intense draggers */
		setTimeout(function(){
			$('div#iframe-overlay').hide();
		}, 250);
		
	}


	showIframeLoadingOverlay = function() {

		/* Restrict scrolling */
		$('div#iframe-container').css('overflow', 'hidden');

		/* Position loading overlay */
		$('div#iframe-loading-overlay').css({
			top: $('div#iframe-container').scrollTop()
		});

		/* Only show if not already visible */
		if ( !$('div#iframe-loading-overlay').is(':visible') ) {
			createCog($('div#iframe-loading-overlay'), true);
			$('div#iframe-loading-overlay').show();
		}
		
		return $('div#iframe-loading-overlay');

	},


	hideIframeLoadingOverlay = function() {

		$('div#iframe-container').css('overflow', 'auto');
		$('div#iframe-loading-overlay').hide().html('');

	}


	var iframe = {
		init: function() {

			$(document).ready(function() {

				Blox.iframe = $('iframe#content');

				iframe.bindFocusBlur();

			});

		},

		bindFocusBlur: function() {

			Blox.iframe.on('mouseleave', function() {
				$(this).trigger('blur');

				/* Hide any tooltips */
				$i('[data-hasqtip]').qtip('disable', true);
			});

			Blox.iframe.on('mouseenter mousedown', function() {
				//If there is another textarea/input that's focused, don't focus the iframe.
				if ( $('textarea:focus, input:focus').length === 1 )
					return;

				$i('[data-hasqtip]').qtip('enable');
				$(this).trigger('focus');
			});

		},

		loadCallback: function(callback) {

			clearUnsavedValues();
						
			/* Fire callback if it exists */
			if ( typeof callback === 'function' )
				callback();
			
			iframe.defaultLoadCallback();

			iframe.stopFirefoxLoadingIndicator();

			/* Fire callback! */
			$('body').triggerHandler('bloxIframeLoad');

			return true;

		},

		defaultLoadCallback: function() {

			stopTitleActivityIndicator();

			changeTitle('Visual Editor: ' + Blox.viewModels.layoutSelector.currentLayoutName());
			$('span#current-layout').text(Blox.viewModels.layoutSelector.currentLayoutName());

			/* Set up tooltips */
			setupTooltips();
			setupTooltips('iframe');
			/* End Tooltips */

			/* Stylesheets for more accurate live designing */
				/* Main Blox stylesheet, used primarily by design editor */
				stylesheet = new ITStylesheet({document: Blox.iframe.contents()[0], href: Blox.homeURL + '/?blox-trigger=compiler&file=general-design-editor'}, 'find');

				/* Catch-all adhoc stylesheet used for overriding */
				css = new ITStylesheet({document: Blox.iframe.contents()[0]}, 'load');
			/* End stylesheets */

			/* Hide iframe overlay if it exists */
				hideIframeOverlay(false);

			$('#iframe-notice').remove();

			/* Add the template notice if it's layout mode and a template is active */
				if ( Blox.viewModels.layoutSelector.currentLayoutTemplate() && Blox.mode == 'grid' ) {

					showIframeOverlay();

					var $iframeNotice = $('<div id="iframe-notice">' +
						'<div>' +
							'<h1>This layout currently has a Shared Layout assigned to it.</h1>' +
							'<h3>The shared layout assigned is <strong>' + Blox.viewModels.layoutSelector.currentLayoutTemplateName() + '</strong></h3>' +
							'<p><span class="button button-blue" id="iframe-notice-switch-to-shared-layout">Switch To Shared Layout</span><span class="button button-blue" id="iframe-notice-unassign-shared-layout">Unassign Shared Layout</span></p>' +
						'</div>' +
					'</div>');

					$iframeNotice.appendTo($('#iframe-container'));

					$iframeNotice.on('click', '#iframe-notice-unassign-shared-layout', function () {

						return unassignSharedLayout(Blox.viewModels.layoutSelector.currentLayout(), false, Blox.viewModels.layoutSelector.currentLayoutName());

					});

					$iframeNotice.on('click', '#iframe-notice-switch-to-shared-layout', function () {

						switchToLayout('template-' + Blox.viewModels.layoutSelector.currentLayoutTemplate().replace('template-', ''), true, Blox.viewModels.layoutSelector.currentLayoutTemplateName());

					});


				}
			/* Disallow certain keys so user doesn't accidentally leave the VE */
			disableBadKeys();
			
			/* Bind visual editor key shortcuts */
			bindKeyShortcuts();

			/* Funnel any keydown, keypress, keyup events to the parent window */
				$i('html, body').bind('keydown', function(event) {
					$(document).trigger(event);
					event.stopPropagation();
				});

				$i('html, body').bind('keypress', function(event) {
					$(document).trigger(event);
					event.stopPropagation();
				});

				$i('html, body').bind('keyup', function(event) {
					$(document).trigger(event);
					event.stopPropagation();
				});

			/* Deactivate all links and buttons */
			if ( Blox.touch )
				Blox.iframe.contents().find('body').css('-webkit-touch-callout', 'none');

			Blox.iframe.contents().find('body').delegate('a, input[type="submit"], button', 'click', function(event) {

				if ( $(this).hasClass('allow-click') )
					return;

				event.preventDefault();
				
				return false;
				
			});
			
			/* Show the load message */
			if ( typeof bloxIframeLoadNotification !== 'undefined' ) {
				showNotification({
					id: 'iframe-load-notification',
					message: bloxIframeLoadNotification,
					overwriteExisting: true
				});
				
				delete bloxIframeLoadNotification;
			}
			
			/* Remove the tabs that are set to close on layout switch */
			removeLayoutSwitchPanels();
			
			/* Show the grid wizard if the current layout isn't customized and not using a tmeplate */
			var layoutNode = $('div#layout-selector span.layout[data-layout-id="' + Blox.viewModels.layoutSelector.currentLayout() + '"]');
			var layoutLi = layoutNode.parent();

			if ( 
				!$i('.block').length
				&& !(Blox.viewModels.layoutSelector.currentLayoutCustomized() && Blox.viewModels.layoutSelector.currentLayout().indexOf('template-') !== 0)
				&& !Blox.viewModels.layoutSelector.currentLayoutTemplate()
				&& Blox.mode == 'grid'
				&& Blox.viewModels.layoutSelector.currentLayoutInUse() != Blox.viewModels.layoutSelector.currentLayout()
				&& Blox.viewModels.layoutSelector.currentLayout().indexOf('template-') === -1
			) {
			
				hidePanel();

				showIframeOverlay();

				var $iframeNotice = $('<div id="iframe-notice">' +
					'<div>' +
					'<h1>This layout is inheriting from another layout.</h1>' +
					'<h3>The inherited layout is <strong>' + Blox.viewModels.layoutSelector.currentLayoutInUseName() + '</strong></h3>' +
					'<p><span class="button button-blue" id="iframe-notice-customize-current">Customize Current Layout</span><span class="button button-blue" id="iframe-notice-switch-to-inherited">Switch To Inherited Layout</span></p>' +
				'	</div>' +
				'</div>');

				$iframeNotice.appendTo('#iframe-container');

				$iframeNotice.on('click', '#iframe-notice-customize-current', function() {

					$('#iframe-notice').remove();

					hideIframeOverlay();
					openBox('grid-wizard');

				});

				$iframeNotice.on('click', '#iframe-notice-switch-to-inherited', function () {

					switchToLayout(Blox.viewModels.layoutSelector.currentLayoutInUse(), true, Blox.viewModels.layoutSelector.currentLayoutInUseName());

				});

			} else if ( Blox.viewModels.layoutSelector.currentLayoutCustomized() || Blox.viewModels.layoutSelector.currentLayoutTemplate() ) {

				closeBox('grid-wizard');
				
			} else {

				openBox('grid-wizard');

			}

			/* Clear out and disable iframe loading indicator */
			hideIframeLoadingOverlay();

		},

		loadTimeout: function() {

			iframeTimeout = true;	
			
			stopTitleActivityIndicator();

			changeTitle('Visual Editor: Error!');	

			/* Hide all controls */
			$('#iframe-container, #menu, #panel, #layout-selector-offset').hide();			
									
			alert("ERROR: There was a problem while loading the visual editor.\n\nYour browser will automatically refresh to attempt loading again.");

			document.location.reload(true);

		},

		stopFirefoxLoadingIndicator: function() {

			//http://www.shanison.com/2010/05/10/stop-the-browser-%E2%80%9Cthrobber-of-doom%E2%80%9D-while-loading-comet-forever-iframe/
			if ( /Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent) ) {
				
				var fake_iframe;

				if ( fake_iframe == null ){
					fake_iframe = document.createElement('iframe');
					fake_iframe.style.display = 'none';
				}

				document.body.appendChild(fake_iframe);
				document.body.removeChild(fake_iframe);
				
			}

		}

	}

	return iframe;

});