define(['jquery', 'deps/mousetrap', 'switch.mode'], function($, mousetrap, switchMode) {

	Padma.contentEditorEditors 		= {};
	var contentEditorHelper 		= {

		init: function(){

			/* Close all Editors when Visual Editor is closed */
			window.onunload = function() {

				$.each(Padma.contentEditorEditors, function(index, editor) {

					if ( typeof editor.window != 'undefined' && !editor.window.closed ) {
						editor.window.close();
					}

				});

			}

		},

		showEditor: function(id, blockId, changeCallback) {

			if ( typeof Padma.contentEditorEditors[id] != 'undefined' && !Padma.contentEditorEditors[id].window.closed ) {
				Padma.contentEditorEditors[id].window.focus();

				return Padma.contentEditorEditors[id];
			}

			var editorConfig = {
				width: 1200,
				height: 780
			};

			editorConfig.left = ( screen.width / 2 ) - (editorConfig.width / 2);
			editorConfig.top = ( screen.height / 2 ) - (editorConfig.height / 2);


			var postId = localStorage['visual-editor-block-post-data-' + blockId];

			Padma.contentEditorEditors[id] = {
				window: window.open(Padma.homeURL + '/?padma-trigger=content-editor&action=edit&post=' + postId , id, 'width=' + editorConfig.width + ',height=' + editorConfig.height + ',top=' + editorConfig.top + ',left=' + editorConfig.left, true)
			}

	
			Padma.contentEditorEditors[id].window.focus();
			contentEditorHelper.bindEditor(id, mode, changeCallback);


			return Padma.contentEditorEditors[id];

		},

		bindEditor: function(id, mode, changeCallback) {

			var window = Padma.contentEditorEditors[id].window;

			return $(window).bind('load', function() {

				/* Add keybindings */
				mousetrap.bindEventsTo(window.document);

				if (switchMode.mode() == "true") { // Is set on night
					themeSelected 	= 'night';
				}


				/* Init editor */
				//var editor = window.tinyMCE.editors[0];

				/*	Binde Save keys	*/
				/*
				window.addEventListener('keydown',function(event){
					if(String.fromCharCode(event.which).toLowerCase() == 's'){						
						return changeCallback(editor);	
					}
				})*/

				/* Bind the editor */				
				/*
				editor.on('Change',function(e){
					return changeCallback(editor);
				});
				*/
				

			});

		}

	}

	contentEditorHelper.init();
	return contentEditorHelper;

});