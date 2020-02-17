define(['jquery', 'deps/itstylesheet', 'util.saving', 'util.usability', 'util.tooltips'], function($, itstylesheet, saving) {

	$i = function(element) {

		if ( typeof Padma.iframe == 'undefined' || typeof Padma.iframe.contents() == 'undefined' )
			return $();

		return Padma.iframe.contents().find(element);

	}

	$iDocument = function() {

		return $(Padma.iframe.contents());

	}


	loadIframe = function(callback, url) {

		if ( typeof url == 'undefined' || !url)
			var url = Padma.homeURL;

		/* Choose contents iframe or preview iframe depending on argument */
			var iframe = Padma.iframe;

		/* Make the title talk */
		startTitleActivityIndicator();
		showIframeLoadingOverlay();

		/* Close Grid Manager */
		closeBox('grid-manager');

		/* Build the URL */
			iframeURL = url;
			iframeURL = updateQueryStringParameter(iframeURL, 've-iframe', 'true');
			iframeURL = updateQueryStringParameter(iframeURL, 've-layout', encodeURIComponent(Padma.viewModels.layoutSelector.currentLayout()));
            iframeURL = updateQueryStringParameter(iframeURL, 've-layout-customized', Padma.viewModels.layoutSelector.currentLayoutCustomized());
            iframeURL = updateQueryStringParameter(iframeURL, 've-iframe-mode', Padma.mode);
			iframeURL = updateQueryStringParameter(iframeURL, 'rand', Math.floor(Math.random() * 100000001));

		/* Clear out existing iframe contents */
			if ( iframe.contents().find('.ui-padma-grid').length && typeof iframe.contents().find('.ui-padma-grid').padmaGrid != 'undefined' ) {
				iframe.contents().find('.ui-padma-grid').padmaGrid('destroy');
			}

			iframe.contents().find('*')
				.unbind()
				.remove();

		iframe[0].src = iframeURL;
		waitForIframeLoad(callback, iframe);

	}


	waitForIframeLoad = function(callback, iframeEl) {
		
		if ( typeof iframeEl == 'undefined' || !iframeEl )
			var iframeEl = Padma.iframe;

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

				Padma.iframe = $('iframe#content');

				iframe.bindFocusBlur();

			});

		},

		bindFocusBlur: function() {

			Padma.iframe.on('mouseleave', function() {
				$(this).trigger('blur');

				/* Hide any tooltips */
				$i('[data-hasqtip]').qtip('disable', true);
			});

			Padma.iframe.on('mouseenter mousedown', function() {
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
			$('body').triggerHandler('padmaIframeLoad');

			return true;

		},

		defaultLoadCallback: function() {

			stopTitleActivityIndicator();

			changeTitle('Visual Editor: ' + Padma.viewModels.layoutSelector.currentLayoutName());
			$('span#current-layout').text(Padma.viewModels.layoutSelector.currentLayoutName());

			/* Set up tooltips */
			setupTooltips();
			setupTooltips('iframe');
			/* End Tooltips */

			/* Stylesheets for more accurate live designing */
				/* Main Padma stylesheet, used primarily by design editor */
				stylesheet = new ITStylesheet({document: Padma.iframe.contents()[0], href: Padma.homeURL + '/?padma-trigger=compiler&file=general-design-editor'}, 'find');

				/* Catch-all adhoc stylesheet used for overriding */
				css = new ITStylesheet({document: Padma.iframe.contents()[0]}, 'load');
			/* End stylesheets */

			/* Hide iframe overlay if it exists */
				hideIframeOverlay(false);

			$('#iframe-notice').remove();

			/* Add the template notice if it's layout mode and a template is active */
				if ( Padma.viewModels.layoutSelector.currentLayoutTemplate() && Padma.mode == 'grid' ) {

					showIframeOverlay();

					var $iframeNotice = $('<div id="iframe-notice">' +
						'<div>' +
							'<h1>This layout currently has a Shared Layout assigned to it.</h1>' +
							'<h3>The shared layout assigned is <strong>' + Padma.viewModels.layoutSelector.currentLayoutTemplateName() + '</strong></h3>' +
							'<p><span class="button button-blue" id="iframe-notice-switch-to-shared-layout">Switch To Shared Layout</span><span class="button button-blue" id="iframe-notice-unassign-shared-layout">Unassign Shared Layout</span></p>' +
						'</div>' +
					'</div>');

					$iframeNotice.appendTo($('#iframe-container'));

					$iframeNotice.on('click', '#iframe-notice-unassign-shared-layout', function () {

						return unassignSharedLayout(Padma.viewModels.layoutSelector.currentLayout(), false, Padma.viewModels.layoutSelector.currentLayoutName());

					});

					$iframeNotice.on('click', '#iframe-notice-switch-to-shared-layout', function () {

						switchToLayout('template-' + Padma.viewModels.layoutSelector.currentLayoutTemplate().replace('template-', ''), true, Padma.viewModels.layoutSelector.currentLayoutTemplateName());

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
			if ( Padma.touch )
				Padma.iframe.contents().find('body').css('-webkit-touch-callout', 'none');

			Padma.iframe.contents().find('body').delegate('a, input[type="submit"], button, span', 'click', function(event) {

				console.log($(this))
				if ( $(this).hasClass('allow-click') )
					return;

				event.preventDefault();
				
				return false;
				
			});
			
			/* Show the load message */
			if ( typeof padmaIframeLoadNotification !== 'undefined' ) {
				showNotification({
					id: 'iframe-load-notification',
					message: padmaIframeLoadNotification,
					overwriteExisting: true
				});
				
				delete padmaIframeLoadNotification;
			}
			
			/* Remove the tabs that are set to close on layout switch */
			removeLayoutSwitchPanels();
			
			/* Show the grid wizard if the current layout isn't customized and not using a tmeplate */
			var layoutNode = $('div#layout-selector span.layout[data-layout-id="' + Padma.viewModels.layoutSelector.currentLayout() + '"]');
			var layoutLi = layoutNode.parent();

			if ( 
				!$i('.block').length
				&& !(Padma.viewModels.layoutSelector.currentLayoutCustomized() && Padma.viewModels.layoutSelector.currentLayout().indexOf('template-') !== 0)
				&& !Padma.viewModels.layoutSelector.currentLayoutTemplate()
				&& Padma.mode == 'grid'
				&& Padma.viewModels.layoutSelector.currentLayoutInUse() != Padma.viewModels.layoutSelector.currentLayout()
				&& Padma.viewModels.layoutSelector.currentLayout().indexOf('template-') === -1
			) {
			
				hidePanel();

				showIframeOverlay();

				var $iframeNotice = $('<div id="iframe-notice">' +
					'<div>' +
					'<h1>This layout is inheriting from another layout.</h1>' +
					'<h3>The inherited layout is <strong>' + Padma.viewModels.layoutSelector.currentLayoutInUseName() + '</strong></h3>' +
					'<p><span class="button button-blue" id="iframe-notice-customize-current">Customize Current Layout</span><span class="button button-blue" id="iframe-notice-switch-to-inherited">Switch To Inherited Layout</span></p>' +
				'	</div>' +
				'</div>');

				$iframeNotice.appendTo('#iframe-container');

				$iframeNotice.on('click', '#iframe-notice-customize-current', function() {

					$('#iframe-notice').remove();

					hideIframeOverlay();
					if(typeof openBox !== 'undefined'){
						openBox('grid-manager');
					}

				});

				$iframeNotice.on('click', '#iframe-notice-switch-to-inherited', function () {

					switchToLayout(Padma.viewModels.layoutSelector.currentLayoutInUse(), true, Padma.viewModels.layoutSelector.currentLayoutInUseName());

				});

			} else if ( Padma.viewModels.layoutSelector.currentLayoutCustomized() || Padma.viewModels.layoutSelector.currentLayoutTemplate() ) {

				if(typeof closeBox !== 'undefined'){
					closeBox('grid-manager');					
				}
				
			} else {

				if(typeof openBox !== 'undefined'){
					openBox('grid-manager');
				}

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