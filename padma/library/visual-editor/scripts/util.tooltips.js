define(['jquery', 'jbox' ], function($) {

	setupTooltips = function(location) {
	
		if ( typeof location === 'undefined' )
			location = false;
			
		if ( Padma.disableTooltips == 1 || Padma.touch ) {
			
			$('div.tooltip-button').hide();
			$('*').removeAttr('title');
			
			return false;
			
		}


		var tooltipOptions = {
			theme: 'TooltipDark',
			addClass: 'jbox-padma',
			position: {
				viewport: $(window),
				x: 'left',
				y: 'center'
			},
			trigger: 'mouseenter',
			outside: 'xy',
			offset: { 
				x: -10, 
				y: 0
			},
			maxWidth: 800,
			reposition: true,
			repositionOnOpen: true,
			repositionOnContent: true,
		}
		
		if ( location == 'iframe' ) {
			
			tooltipOptions.position.container = $i('body');
			tooltipOptions.position.viewport = $i('#padma-tooltip-container');
						
			var tooltipElement = $i;
			
		} else {
			
			var tooltipElement = $;
			
		}

		// Tooltips for panel
		if(location === false){
			tooltipOptions.position.viewport = '';
			tooltipElement('.sub-tabs-content .tooltip-button').jBox('Tooltip', tooltipOptions);

		}

		tooltipElement('div.tooltip-button:not([data-hasqtip]), .tooltip:not([data-hasqtip])').jBox('Tooltip', tooltipOptions);
		
		tooltipElement('.tooltip-bottom-right:not([data-hasqtip])').jBox('Tooltip', $.extend( true, {}, tooltipOptions, {
			position: {
				my: 'bottom right',
				at: 'top center'
			}
		}));

		tooltipElement('.tooltip-top-right:not([data-hasqtip])').jBox('Tooltip', $.extend(true, {}, tooltipOptions, {
			position: {
				my: 'top right',
				at: 'bottom center'
			}
		}));
		
		tooltipElement('.tooltip-top-left:not([data-hasqtip])').jBox('Tooltip', $.extend(true, {}, tooltipOptions, { 
		   position: {
				my: 'top left',
				at: 'bottom center'
		   },
		   show: {
		   		delay: 750
		   }
		}));
		
		tooltipElement('.tooltip-left:not([data-hasqtip])').jBox('Tooltip', $.extend(true, {}, tooltipOptions, { 
		   position: {
				my: 'left center',
				at: 'right center'
		   }
		}));
		
		tooltipElement('.tooltip-right:not([data-hasqtip])').jBox('Tooltip', $.extend(true, {}, tooltipOptions, { 
		   position: {
				my: 'right center',
				at: 'left center'
		   }
		}));

		tooltipElement('.tooltip-top:not([data-hasqtip])').jBox('Tooltip', $.extend(true, {}, tooltipOptions, { 
		   position: {
				my: 'top center',
				at: 'bottom center'
		   }
		}));
		
		/*
		var iframeScrollTooltipReposition = function() {
			

			// Flood Control
			if ( $i('.qtip:visible').length === 0 || typeof iframeScrollTooltipRepositionFloodTimeout != 'undefined' )
				return;
			
			iframeScrollTooltipRepositionFloodTimeout = setTimeout(function() {
				
				$i('.qtip:visible').qtip('reposition');
				
				delete iframeScrollTooltipRepositionFloodTimeout;
				
			}, 400);

						
		}

		Padma.iframe.contents().unbind('scroll', iframeScrollTooltipReposition);		
		Padma.iframe.contents().bind('scroll', iframeScrollTooltipReposition);
		*/
		
	}
	
	/*	
	repositionTooltips = function() {

		if ( $i('.qtip:visible').length > 0 ) {
			//$i('.qtip:visible').qtip('reposition');
		}
		
	}*/

});