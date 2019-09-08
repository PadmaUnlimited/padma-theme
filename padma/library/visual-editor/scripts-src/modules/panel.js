define(['jquery', 'jqueryUI', 'deps/jquery.cookie', 'util.tooltips', 'modules/panel.inputs'], function($, jQueryUI, jQueryCookie, tooltips, panelInputs) {

	selectTab = function(tab, context) {

		var tabs = context.find('.ui-tabs-nav');
		var tabLink = tabs.find('li[aria-controls="' + tab + '"] a').length ? tabs.find('li[aria-controls="' + tab + '"] a') : tabs.find('li[aria-controls="' + tab + '-content"] a');

		return tabLink.trigger('click');

	},

	addPanelTab = function(name, title, content, closable, closeOnLayoutSwitch, panelClass) {
		
		/* If the tab name already exists, don't try making it */
		if ( $('ul#panel-top li a[href="#' + name + '-tab"]').length !== 0 )
			return false;
		
		/* Set up default variables */
		if ( typeof closable == 'undefined' ) {
			var closable = false;
		}
		
		if ( typeof closeOnLayoutSwitch == 'undefined' ) {
			var closeOnLayoutSwitch = false;
		}
		
		if ( typeof panelClass == 'undefined' ) {
			var panelClass = false;
		}
		
		/* Add the tab */
		var tab = $('<li><a href="#' + name + '-tab">' + title + '</a></li>').appendTo('div#panel #panel-top');
		var panel = $('<div id="' + name + '-tab"></div>').appendTo('div#panel');
		var tabLink = tab.find('a');
		
		$('div#panel').tabs('refresh');
		$(tabLink).bind('click', showPanel);
		
		showPanel();

		/* Remove panel empty class from body so it will show again if it was empty before */
		$('body').removeClass('panel-empty');
		
		/* Add the panel class to the panel */
		panel.addClass('panel');

		
		/* If the content is static, just throw it in.  Otherwise get the content with AJAX */
		if ( typeof content == 'string' ) {
			
			panel.html(content);
			
		} else {
			
			var loadURL = content.url; 
			var loadData = content.data || false;
			
			var loadCallback = function() {
				
				if ( typeof content.callback == 'function' )
					content.callback.call();
								
			};
			
			createCog(panel, true);

			loadData.mode = Padma.mode;
						
			$('div#panel div#' +  name + '-tab').load(loadURL, loadData, loadCallback);
			
		}
		
		if ( panelClass )
			panel.addClass('panel-' + panelClass);

		/* Add delete to tab link if the tab is closable */
		if ( closable ) {
					
			tabLink.parent().append('<span class="close">X</span>');
			
		}
		
		/* If the panel is set to close on layout switch, add a class to the tab itself so we can target it down the road */
		tabLink.parent().addClass('tab-close-on-layout-switch');
				
		return tab;
		
	},


	removePanelTab = function(name) {

		var name = name.replace('-tab', '');
		
		/* If tab doesn't exist, don't try to delete any tabs */
		if ( $('#' + name + '-tab').length === 0 ) {
			return false;
		}

		$('#panel').find('#' + name + '-tab').remove();
		$('#panel-top').find('a[href="#' + name + '-tab"]').parent().remove();

		/* If panel is empty, add panel empty class to body so the entire panel is hidden */
		if ( !$('#panel-top').find('li').length )
			$('body').addClass('panel-empty');
		
		return $('div#panel').tabs('refresh');
		
	},


	removeLayoutSwitchPanels = function() {
		
		$('li.tab-close-on-layout-switch').each(function(){
			var id = $(this).find('a').attr('href').replace('#', '');
			
			removePanelTab(id);
		});
		
	},

	togglePanel = function() {

		if ( $('div#panel').hasClass('panel-hidden') )
			return showPanel();

		return hidePanel();

	},


	hidePanel = function() {
		
		//If the panel is already hidden, don't go through any trouble.
		if ( $('div#panel').hasClass('panel-hidden') )
			return false;
									
		var panelCSS = {bottom: -$('div#panel').height()};
		var iframeCSS = {bottom: $('ul#panel-top').outerHeight()};

			$('div#panel').css(panelCSS).addClass('panel-hidden');
			$('div#iframe-container').css(iframeCSS);

			setTimeout(repositionTooltips, 400);

		$('body').addClass('panel-hidden');

		/* Change arrow to pointing up arrow */
		$('ul#panel-top-right li#minimize span').text('^');
		
		/* De-select the selected block while the panel is hidden */
		if ( typeof $i == 'function' ) {
			$i('.block-selected').removeClass('block-selected block-hover');
		}

		$.cookie('hide-panel', true);
		
		return true;
		
	},


	showPanel = function() {
				
		//If the panel is already visible, don't go through any trouble.
		if ( !$('div#panel').hasClass('panel-hidden') )
			return false;

		var panelCSS = {bottom: 0};
		var iframeCSS = {bottom: $('div#panel').outerHeight()};
					
			$('div#panel').css(panelCSS).removeClass('panel-hidden');
			$('div#iframe-container').css(iframeCSS);

			setTimeout(repositionTooltips, 400);

		$('body').removeClass('panel-hidden');

		/* Change arrow to pointing down arrow */
		$('ul#panel-top-right li#minimize span').text('g');
		
		/* Re-select the the block if a block options panel tab is open. */
		if ( $('ul#panel-top > li.ui-state-active a').length )
			$i('#' + $('ul#panel-top > li.ui-state-active a').attr('href').replace('#', '').replace('-tab', '')).addClass('block-selected block-hover');
		
		$.cookie('hide-panel', false);
		
		return true;
		
	}


	var panel = {
		init: function() {

			panelInputs.delegate();
			panelInputs.bind();

		},

		getPanelMaxHeight: function() { 
			return $(window).height() - 275; 
		},

		resizePanel: function(panelHeight, resizingWindow) {

			var $panel = $('div#panel');
			var $panelTop = $panel.find('ul#panel-top');

			if ( typeof panelHeight == 'undefined' || panelHeight == false )
				panelHeight = $('div#panel').height();

			if ( panelHeight > panel.getPanelMaxHeight() )
				panelHeight = (panel.getPanelMaxHeight() > panelMinHeight) ? panel.getPanelMaxHeight() : panelMinHeight;

			if ( panelHeight < panelMinHeight )
				panelHeight = panelMinHeight;

			if ( typeof resizingWindow != 'undefined' && resizingWindow && panelHeight < panel.getPanelMaxHeight() )
				return;

			$panel.height(panelHeight);

			var iframeBottomPadding = $panel.hasClass('panel-hidden') ? $panelTop.outerHeight() : $panel.outerHeight();
			var layoutSelectorBottomPadding = $panel.hasClass('panel-hidden') ? $panelTop.outerHeight()  + $('div#layout-selector-tabs').height() : $panel.outerHeight() + $('div#layout-selector-tabs').height();

			$('div#iframe-container').css({bottom: iframeBottomPadding});

			if ( $panel.hasClass('panel-hidden') )
				$('div#panel').css({'bottom': -$('div#panel').height()});

			$.cookie('panel-height', $panel.height());

		}

	}

	/* If panel is empty then add body class */
		if ( !$('ul#panel-top').find('li').length )
			$('body').addClass('panel-empty');

	/* PANEL */
		/* Tab Functions */
		$('ul#panel-top').delegate('span.close', 'click', function(){
					
			var tab = $(this).siblings('a').attr('href').replace('#', '').replace('-tab', '');
					
			return removePanelTab(tab);
			
		});

		$('div#panel').tabs({
			tabTemplate: "<li><a href='#{href}'>#{label}</a></li>",
			add: function(event, ui, content) {

				$(ui.panel).append(content);

			},
			activate: function(event, ui) {

				var tabID = $(ui.newTab).children('a').attr('href').replace('#', '').replace('-tab', '');

				$i('.block-selected').removeClass('block-selected block-hover');

				if ( tabID.indexOf('block-') === 0 )
					$i('#' + tabID).addClass('block-selected block-hover');

			}
		});

		$('ul#panel-top li a').on('click', showPanel);

		$('div.sub-tab').tabs();

		/* PANEL RESIZING */
			var panelMinHeight = 120;

			/* Resize the panel according to the cookie right on VE load */
			$(document).ready(function() {

				if ( $.cookie('panel-height') )
					panel.resizePanel($.cookie('panel-height'));

			});

			/* Make the resizing handle actually work */
			$('div#panel').resizable({
				maxHeight: panel.getPanelMaxHeight(),
				minHeight: 120,
				handles: 'n',
				resize: function(event, ui) {

					$(this).css({
						width: '100%',
						position: 'fixed',
						bottom: 0,
						top: ''
					});

					/* Adjust Padding */
						$('div#iframe-container').css({bottom: $('div#panel').outerHeight()});

					/* Refresh iframe overlay size so it continues to cover iframe */
					showIframeOverlay();

				},
				start: function() {

					showIframeOverlay();

				},
				stop: function() {

					$.cookie('panel-height', $(this).height());

					hideIframeOverlay();

				},
			});

			/* The max height option on the resizable must be updated if the window is resized. */
			$(window).bind('resize', function(event) {

				/* For some reason jQuery UI resizable triggers window resize so only fire if window is truly the target. */
				if ( event.target != window )
					return;

				$('div#panel').resizable(
					'option', {
					maxHeight: panel.getPanelMaxHeight()
				});

				panel.resizePanel(false, true);

			});

		/* END PANEL RESIZING */

		/* PANEL TOGGLE */
			$('div#panel-top-container').bind('dblclick', function(event) {

				if ( event.target.id != 'panel-top-container' )
					return false;

				togglePanel();

			});

			$('ul#panel-top-right li#minimize').bind('click', function(event) {

				togglePanel();

				return false;

			});

			/* Check for cookie */
			if ( $.cookie('hide-panel') === 'true' ) {

				hidePanel(true);

			}
		/* END PANEL TOGGLE */
	/* END PANEL */

	return panel;

});