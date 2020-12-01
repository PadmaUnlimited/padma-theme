define(['modules/iframe', 'deps/jquery.pep'], function(iframe) {

	/* BOX FUNCTIONS */
		createBox = function(args) {
			var settings = {};
			
			var defaults = {
				id: null,
				title: null,
				description: null,
				content: null,
				src: null,
				load: null,
				width: 500,
				height: 300,
				center: true,
				closable: true,
				resizable: false,
				draggable: true,
				deleteWhenClosed: false,
				blackOverlay: false,
				blackOverlayOpacity: .6,
				blackOverlayIframe: false
			};
			
			$.extend(settings, defaults, args);
					
			/* Create box */
				var box = $('<div class="box" id="box-' + settings.id + '"><div class="box-top"></div><div class="box-content-bg"><div class="box-content"></div></div></div>');
				
				box.attr('black_overlay', settings.blackOverlay);
				box.attr('black_overlay_opacity', settings.blackOverlayOpacity);
				box.attr('black_overlay_iframe', settings.blackOverlayIframe);
				box.attr('load_with_ajax', false);
					
			/* Move box into document */
				box.appendTo('div#boxes');
						
			/* Inject everything */
				/* If regular content and not iframe, just put it in */
				if ( typeof settings.src !== 'string' ) {
									
					box.find('.box-content').html(settings.content);
				
				/* Else use iframe */	
				} else {

					box.find('.box-content').addClass('box-content-with-iframe');
					box.find('.box-content').html('<iframe src="' + settings.src + '"></iframe>');
									
					if ( typeof settings.load === 'function' ) {
						
						box.find('.box-content iframe').bind('load', settings.load);
						
					}
					
				}
			
				box.find('.box-top').append('<strong>' + settings.title + '</strong>');
				
				if ( typeof settings.description === 'string' ) {
					box.find('.box-top').append('<span>' + settings.description + '</span>');
				}
			
			/* Setup box */
				setupBox(settings.id, settings);
						
			return box;
		}
		
		
		setupBox = function(id, args) {
			
			var settings = {};
			
			var defaults = {
				width: 600,
				height: 300,
				center: true,
				closable: true,
				deleteWhenClosed: false,
				draggable: false,
				resizable: false
			};
					
			$.extend(settings, defaults, args);		
					
			var box = $('div#box-' + id);
					
			/* Handle draggable */
			if ( settings.draggable && typeof box != 'undefined') {
				
				box.draggable({
					handle: box.find('.box-top'),
					start: showIframeOverlay,
					stop: hideIframeOverlay,
					shouldEase: false
				});

				box.find('.box-top').css('cursor', 'move');
				
			}
			
			/* Make box closable */
			if ( settings.closable ) {
				
				/* If close button doesn't exist, create it. */
				box.find('.box-top').append('<span class="box-close">X</span>');
				
				box.find('.box-close').bind('click', function(){
					closeBox(id, settings.deleteWhenClosed);
				});
				
			}
			
			/* Make box resizable */			
			if ( settings.resizable ) {
				
				/* If close button doesn't exist, create it. */				
				box.resizable({
					start: showIframeOverlay,
					stop: hideIframeOverlay,
					handles: 'n, e, s, w, ne, se, sw, nw',
					minWidth: settings.minWidth,
					minHeight: settings.minHeight
				});
				
			}
			
			/* Set box dimensions */
			box.css({
				width: settings.width,
				height: settings.height
			});

			/* Center Box */
			if ( settings.center ) {
				
				var marginLeft = -(box.width() / 2);
				var marginTop = -(box.height() / 2);
				
				box.css({
					top: '50%',
					left: '50%',
					marginLeft: marginLeft,
					marginTop: marginTop,
				});
				
			}
			
		}
		
		
		setupStaticBoxes = function() {
			$('div.box').each(function () {

				/* Fetch settings */
				var draggable = puBoolean($(this).attr('draggable'));
				var closable = puBoolean($(this).attr('closable'));
				var resizable = puBoolean($(this).attr('resizable'));
				var center = puBoolean($(this).attr('center'));
				var width = $(this).attr('width');
				var height = $(this).attr('height');
				var minWidth = $(this).attr('min_width');
				var minHeight = $(this).attr('min_height');

				var id = $(this).attr('id').replace('box-', '');

				setupBox(id, {
					draggable: draggable,
					closable: closable,
					resizable: resizable,
					center: center,
					width: width,
					height: height,
					minWidth: minWidth,
					minHeight: minHeight
				});

				/* Remove settings attributes */
				$(this).attr('draggable', null);
				$(this).attr('closable', null);
				$(this).attr('resizable', null);
				$(this).attr('center', null);
				$(this).attr('width', null);
				$(this).attr('height', null);
				$(this).attr('min_width', null);
				$(this).attr('min_height', null);

			});
		}
		
		
		openBox = function(id) {
			
			var id = id.replace('box-', '');
			var box = $('div#box-' + id);
			
			if ( box.length === 0 )
				return false;
			
			var blackOverlay = puBoolean(box.attr('black_overlay'));
			var blackOverlayOpacity = box.attr('black_overlay_opacity');
			var blackOverlayIframe = puBoolean(box.attr('black_overlay_iframe'));
			var loadWithAjax = puBoolean(box.attr('load_with_ajax'));
			
			if ( blackOverlay && !boxOpen(id) ) {

				var overlay = $('<div class="black-overlay"></div>')
					.hide()
					.attr('id', 'black-overlay-box-' + id)
					.appendTo('#boxes');

				if ( blackOverlayIframe === true )
					overlay.css('zIndex', 4);

				if ( !isNaN(blackOverlayOpacity) )
					overlay.css('background', 'rgba(0, 0, 0, ' + blackOverlayOpacity + ')');

				overlay.show();

			}
				
			if ( loadWithAjax && !box.data('currently-ajax-loading') ) {

				/* Remove all data such as jQuery UI widgets.  jQuery UI upgrade to 1.10 required this */
				box.find('*').removeData();
				box.find('.box-content *').remove();
				
				/* Add the loading cog */
				createCog(box.find('.box-content'), true);

				/* Add loading flag */
				box.data('currently-ajax-loading', true);
							
				box.find('.box-content').load(Padma.ajaxURL, {
					security: Padma.security,
					action: 'padma_visual_editor',
					method: 'load_box_ajax_content',
					box_id: id,
					layout: Padma.viewModels.layoutSelector.currentLayout()
				}, function() {
										
					var loadWithAjaxCallback = eval(box.attr('load_with_ajax_callback'));
									
					loadWithAjaxCallback.call();

					/* Remove loading flag */
					box.removeData('currently-ajax-loading');

				});
				
			}
				
			return box.show();
			
		}
		
		
		closeBox = function(id, deleteWhenClosed) {
			
			var id = id.replace('box-', '');
			var box = $('div#box-' + id);
			
			box.hide();

			if ( typeof deleteWhenClosed != 'undefined' && deleteWhenClosed == true )
				box.remove();
						
			$('div#black-overlay-box-' + id).remove();
			
			return true;
			
		}
		
		
		boxOpen = function(id) {
			
			return $('div#box-' + id).is(':visible');
			
		}
		
		
		boxExists = function(id) {
			
			if ( $('div#box-' + id).length === 1 ) {
				
				return true;
				
			} else {
				
				return false;
				
			}
			
		}


		toggleBox = function(id) {

			if ( !boxOpen(id) ) {
								
				openBox(id);
				
			} else {
								
				closeBox(id);
				
			}

		}
	/* END BOX FUNCTIONS */

	/* BOXES */
		setupStaticBoxes();

		/* Make clicking box overlay close visible box. */
		$('#boxes').on('click', 'div.black-overlay', function(){

			var id = $(this).attr('id').replace('black-overlay-', '');

			if ( $('#' + id).length === 0 )
				return;

			if ( $('.qtip-tour').is(':visible') )
				return;

			closeBox(id);

		});
	/* END BOXES */

});