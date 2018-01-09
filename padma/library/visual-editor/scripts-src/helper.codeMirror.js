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

<<<<<<< HEAD
			console.log(this);

=======
>>>>>>> 1e3c286c749c627dcc161b5ceaef56ec040fdd07
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
<<<<<<< HEAD

			Padma.codeMirrorEditors[id].window.focus();
			codeMirrorHelper.bindEditor(id, mode, initialValue, changeCallback);

=======

			Padma.codeMirrorEditors[id].window.focus();
			aceHelper.bindEditor(id, mode, initialValue, changeCallback);

>>>>>>> 1e3c286c749c627dcc161b5ceaef56ec040fdd07
			return Padma.codeMirrorEditors[id];

		},

		bindEditor: function(id, mode, initialValue, changeCallback) {

			var window = Padma.codeMirrorEditors[id].window;

			return $(window).bind('load', function() {

				/* Add keybindings */
				mousetrap.bindEventsTo(window.document);

<<<<<<< HEAD

				//var ace = window.ace;

				/* Set paths */
				//var acePath = Padma.padmaURL + '/library/visual-editor/' + Padma.scriptFolder + '/deps/ace/';

				/*
=======
				var ace = window.ace;

				/* Set paths */
				var acePath = Padma.padmaURL + '/library/visual-editor/' + Padma.scriptFolder + '/deps/ace/';

>>>>>>> 1e3c286c749c627dcc161b5ceaef56ec040fdd07
				ace.config.set('basePath', acePath);
				ace.config.set('modePath', acePath);
				ace.config.set('workerPath', acePath);
				ace.config.set('themePath', acePath);
<<<<<<< HEAD
				*/

				/* Init editor */
				/*
				Padma.codeMirrorEditors[id].editor = ace.edit($(window.document).contents().find('#ace-editor').get(0));
				Padma.codeMirrorEditors[id].editorSession = Padma.codeMirrorEditors[id].editor.getSession();
				*/

				/* Set editor config */
				/*
=======

				/* Init editor */
				Padma.codeMirrorEditors[id].editor = ace.edit($(window.document).contents().find('#ace-editor').get(0));
				Padma.codeMirrorEditors[id].editorSession = Padma.codeMirrorEditors[id].editor.getSession();

				/* Set editor config */
>>>>>>> 1e3c286c749c627dcc161b5ceaef56ec040fdd07
				Padma.codeMirrorEditors[id].editor.setTheme('ace/theme/clouds');
				Padma.codeMirrorEditors[id].editorSession.setMode('ace/mode/' + mode);

				Padma.codeMirrorEditors[id].editor.setShowPrintMargin(false);

				Padma.codeMirrorEditors[id].editorSession.setUseWrapMode(true);
<<<<<<< HEAD
				*/
=======
>>>>>>> 1e3c286c749c627dcc161b5ceaef56ec040fdd07

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