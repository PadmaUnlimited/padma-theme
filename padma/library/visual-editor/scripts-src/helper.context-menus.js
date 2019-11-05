/* CONTEXT MENU FUNCTIONALITY */
	setupContextMenu = function(args) {

		if ( typeof args != 'object' )
			return false;

		var args = $.extend(true, {}, { 
		   isIframeElement: true
		}, args);


		/* Unbind any existing of the same context menu */
		deactivateContextMenu(args.id);

		/* Bind the right click on the element(s) */
		var contextMenuOpenEvent = !Padma.touch ? 'contextmenu.contextMenu' + args.id : 'taphold.contextMenu' + args.id;		

		/* Get to binding! */
		if ( args.isIframeElement ) {

			$iDocument().on(contextMenuOpenEvent, args.elements, function(event, eventArgs) {
				event.data = eventArgs;
				contextMenuCreator(args, event, true);
			});

		} else {

			$(document).on(contextMenuOpenEvent, args.elements, function(event, eventArgs) {
				event.data = eventArgs;
				contextMenuCreator(args, event, false);
			});

		}

		/* Bind click on anything else to close */
		var clickToClose = function(event) {

			if ( (event.which !== 0 && event.which !== 1) || $(event.originalEvent.target).parents('#context-menu-' + args.id).length )
				return;

			var contextMenu = $('#context-menu-' + args.id);

			if ( typeof args.onHide == 'function' )
				args.onHide.apply(contextMenu);

			contextMenu.remove();

		}

		/* Bind mouseup to close context menu normally and tap for touch support */
		var contextMenuCloseEvent = !Padma.touch ? 'click' : 'touchstart';

		$('body').on(contextMenuCloseEvent + '.contextMenu' + args.id, clickToClose);
		$i('body').on(contextMenuCloseEvent + '.contextMenu' + args.id, clickToClose);
		/* End binding click on anything to close */

	}


	deactivateContextMenu = function(id) {

		$(document).off('.contextMenu' + id);
		$iDocument().off('.contextMenu' + id);

		$('body').off('.contextMenu' + id);
		$i('body').off('.contextMenu' + id);

		return true;

	}


	contextMenuCreator = function(args, event, iframe) {

		event.stopPropagation(); /* Keep other context menus from opening */

		if ( typeof args != 'object' )
			return false;

		/* Hide any other context menus */
		$('.context-menu').remove();

		/* Create context menu */
		var contextMenuTitle = typeof args.title == 'function' ? args.title.apply(undefined, [event]) : args.title;
		var contextMenu = $('<ul id="context-menu-' + args.id + '" class="context-menu"><h3>' + contextMenuTitle + '</h3></ul>');


		/* Trigger onShow callback */
		if ( typeof args.onShow == 'function' )
			args.onShow.apply(contextMenu, [event]);


		/* Fire contentsCallback to insert items */
		args.contentsCallback.apply(contextMenu, [event]);

		/* Bind click of items */
		var originalRightClickEvent = event;

		var contextMenuItemClick = function(event) {

			if ( typeof args.onItemClick == 'function' )
				args.onItemClick.apply(this, [contextMenu, originalRightClickEvent]);

			if ( typeof args.onHide == 'function' )
				args.onHide.apply(contextMenu);

			contextMenu.remove();

		};

		var contextMenuClickEvent = !Padma.touch ? 'click' : 'tap';
		contextMenu.delegate('span', contextMenuClickEvent, contextMenuItemClick);

		/* Context menu positioning */
		if ( typeof event.originalEvent != 'undefined' && typeof event.originalEvent.clientX != 'undefined' ) {

			var contextMenuX = event.originalEvent.clientX;			
			var contextMenuY = event.originalEvent.clientY + 40;

			if($('body').hasClass('panel-on-left')){
				contextMenuX += 290;
			}

		} else {

			var contextMenuX;
			var contextMenuY;

			if( event.type == 'taphold'){

				var contextMenuX = event.originalEvent.touches[0].clientX;
				var contextMenuY = event.originalEvent.touches[0].clientY + 40;

			}else{

				var contextMenuX = event.data.x;
				var contextMenuY = event.data.y + 40;

			}


		}

		if( ! $('#customize-preview').hasClass('preview-desktop')){
			contextMenuX += jQuery('iframe#content').offset().left;
		}


		contextMenu.css({
			left: contextMenuX,
			top: contextMenuY
		});

		/* Delegate hover event on context menu sub menus for the lovely window right bleeding */
			contextMenu.delegate('li:has(ul) span', 'hover', function() {

				var childMenu = $(this).siblings('ul');
				var childMenuOffset = childMenu.offset();

				if ( !childMenuOffset || ((childMenu.offset().left + childMenu.outerWidth()) < $('iframe.content').width()) )
					return;

				childMenu.css('right', childMenu.css('left'));
				childMenu.css('left', 'auto');			

				childMenu.css('width', '190px');			

				childMenu.css('zIndex', '999999');			

			});

		/* Add context menu to iframe */
			contextMenu.appendTo($('body'));

		
		/* Context Menu overflow */
			/* X overflow */
				if ( (contextMenuX + contextMenu.outerWidth()) > $(window).width() ) {

					var overflow = $(window).width() - (contextMenuX + contextMenu.outerWidth());
					contextMenu.css('left', contextMenuX + overflow - 20);

				}

			/* Y overflow */
				if ( (contextMenuY + contextMenu.outerHeight()) > $(window).height() ) {

					var overflow = $(window).height() - (contextMenuY + contextMenu.outerHeight());
					contextMenu.css('top', contextMenuY + overflow - 20);

				}
		/* End Context Menu Overflow */


		/* Prevent regular context menu from opening */
			if (event.cancelable) {
				event.preventDefault();
			}
			return false;

	}
/* END CONTEXT MENU FUNCTIONALITY */