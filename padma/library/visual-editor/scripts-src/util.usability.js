define(['jquery', 'deps/mousetrap'], function($, mousetrap) {

	/* ANNOYANCE FIXER FUNCTIONS */
		prohibitVEClose = function () {	

			window.onbeforeunload = function(){
				return 'You have unsaved changes.  Are you sure you wish to leave the Visual Editor?';
			}
		
			allowVECloseSwitch = false;

		}


		allowVEClose = function() {

			window.onbeforeunload = function(){
				return null;
			}
		
			allowVECloseSwitch = true;

		}


		prohibitLiveCSSClose = function (w) {	

			w.onbeforeunload = function(){
				return 'You have unsaved changes.  Are you sure you wish to leave the Live CSS Editor?';
			}

		}


		allowLiveCSSClose = function(w) {

			w.onbeforeunload = function(){
				return null;
			}
		
		}


		disableBadKeys = function() {

			var disableBadKeysCallback = function(event) {
				
				//8 = Backspace
				//9 = Tab
				//13 = Enter
			
				var element = $(event.target); 

			
				if ( event.which === 8 && !element.is('input') && !element.is('textarea') && !element.hasClass('allow-backspace-key') && !element.parents('.wysiwyg-container').length ) {
					event.preventDefault();
					
					return false;
				}

				if ( event.which === 9 && !element.is('input') && !element.is('textarea') && !element.hasClass('allow-tab-key') && !element.parents('.wysiwyg-container').length ) {
					event.preventDefault();
					
					return false;
				}
			
				if ( event.which == 13 && !element.is('textarea') && !element.hasClass('allow-enter-key') && !element.parents('.wysiwyg-container').length ) {
					event.preventDefault();
					
					return false;
				}
				
				if( event.key == 'Delete' && !element.is('input') && !element.is('textarea') ){
					deleteSelectedBlock();
					return true;
				}
			
			}
		
			//Disable backspace for normal frame but still keep backspace functionality in inputs.  Also disable enter.
			$(document).bind('keypress', disableBadKeysCallback);
			$(document).bind('keydown', disableBadKeysCallback);
		
			//Disable backspace and enter for iframe
			$i('html').bind('keypress', disableBadKeysCallback);
			$i('html').bind('keydown', disableBadKeysCallback);
			
		}
	/* END ANNOYANCE FIXER FUNCTIONS */


	/* KEY SHORTCUTS */
		bindKeyShortcuts = function() {

			mousetrap.bindEventsTo($i('body').get(0));

			/* Close Tour */
				var keyBindingEscCloseTour = function(event) {

					if ( !$('.qtip-tour').is(':visible') )
						return;

					$(document.body).qtip('hide');

				}

				mousetrap.bind('esc', keyBindingEscCloseTour);
									
			/* Bindings with modifier */
				/* Save */
					mousetrap.bindGlobal(['ctrl+s', 'command+s'], function(event) {
						save();

						/* cancel browser default */
						return false; 
					});

				/* Panel Toggle */
					mousetrap.bind(['ctrl+p', 'command+p'], function(event) {
						togglePanel();

						/* cancel browser default */
						return false; 
					});

				/* Layout Selector Toggle */
					mousetrap.bind('ctrl+l', toggleLayoutSelector);

				/* Design Editor Stuff */
					if ( typeof designEditor != 'undefined' && typeof designEditor.processElementCopy == 'function' ) {

						/* Copy and Paste */
							mousetrap.bind(['ctrl+c', 'command+c'], designEditor.processElementCopy);
							mousetrap.bind(['ctrl+v', 'command+v'], designEditor.processElementPaste);

						/* Live CSS Toggle */
							mousetrap.bind('ctrl+e', function() { if ( !boxOpen('live-css') ) { $('#open-live-css').trigger('click'); } else { closeBox('live-css'); } });

						/* Inspector Toggle */
							mousetrap.bind('ctrl+i', toggleInspector);

						/* Design Editor Toggle */
							mousetrap.bind('tab', toggleDesignEditor);

					}
			/* End bindings with modifier */
			
		}
	/* END KEY SHORTCUTS */


	if ( Padma.touch ) {

		require(['deps/jquery.ui.touchpunch', 'deps/jquery.taphold'], function() {});

	}

});