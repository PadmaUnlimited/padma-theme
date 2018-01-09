define(['jquery', 'deps/mousetrap'], function($, mousetrap) {

	Padma.codeMirrorEditors 	= {};
	var codeMirrorHelper 		= {

		init: function(){

			/* Close all Editors when Visual Editor is closed */
			window.onunload = function() {

				$.each(Padma.codeMirrorEditors, function(index, editor) {

					if ( typeof editor.window != 'undefined' && !editor.window.closed ) {
						editor.window.close();
					}

				});

			}

		},

		showEditor: function(id, mode, initialValue, changeCallback) {

			if ( typeof Padma.codeMirrorEditors[id] != 'undefined' && !Padma.codeMirrorEditors[id].window.closed ) {
				Padma.codeMirrorEditors[id].window.focus();

				return Padma.codeMirrorEditors[id];
			}

			var editorConfig = {
				width: 750,
				height: 550
			};

			editorConfig.left = ( screen.width / 2 ) - (editorConfig.width / 2);
			editorConfig.top = ( screen.height / 2 ) - (editorConfig.height / 2);

			Padma.codeMirrorEditors[id] = {
				window: window.open(Padma.homeURL + '/?padma-trigger=code-mirror&mode=' + mode, id, 'width=' + editorConfig.width + ',height=' + editorConfig.height + ',top=' + editorConfig.top + ',left=' + editorConfig.left, true)
			}

			Padma.codeMirrorEditors[id].window.focus();
			codeMirrorHelper.bindEditor(id, mode, initialValue, changeCallback);


			return Padma.codeMirrorEditors[id];

		},

		bindEditor: function(id, mode, initialValue, changeCallback) {

			var window = Padma.codeMirrorEditors[id].window;

			return $(window).bind('load', function() {

				/* Add keybindings */
				mousetrap.bindEventsTo(window.document);


				/* Init editor */
				/*
				Padma.codeMirrorEditors[id].editor = ace.edit($(window.document).contents().find('#ace-editor').get(0));
				Padma.codeMirrorEditors[id].editorSession = Padma.codeMirrorEditors[id].editor.getSession();
				*/


				/* Populate the editor */
				Padma.codeMirrorEditors[id].editor.setValue(initialValue);

				/* Focus editor */
				Padma.codeMirrorEditors[id].editor.gotoLine(0);
				Padma.codeMirrorEditors[id].editor.focus();

				/* Bind the editor */
				Padma.codeMirrorEditors[id].editorSession.on('change', function(e) {
					return changeCallback(Padma.codeMirrorEditors[id].editor);
				});

			});

		}

	}

	codeMirrorHelper.init();
	return codeMirrorHelper;

});