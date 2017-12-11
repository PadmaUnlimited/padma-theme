define(['jquery', 'deps/mousetrap'], function($, mousetrap) {

	Blox.aceEditors = {};

	var aceHelper = {
		init: function() {

			/* Close all Ace Editors when Visual Editor is closed */
			window.onunload = function() {

				$.each(Blox.aceEditors, function(index, aceEditor) {

					if ( typeof aceEditor.window != 'undefined' && !aceEditor.window.closed ) {
						aceEditor.window.close();
					}

				});

			}

		},

		showEditor: function(id, mode, initialValue, changeCallback) {

			if ( typeof Blox.aceEditors[id] != 'undefined' && !Blox.aceEditors[id].window.closed ) {
				Blox.aceEditors[id].window.focus();

				return Blox.aceEditors[id];
			}

			var editorConfig = {
				width: 750,
				height: 550
			};

			editorConfig.left = ( screen.width / 2 ) - (editorConfig.width / 2);
			editorConfig.top = ( screen.height / 2 ) - (editorConfig.height / 2);

			Blox.aceEditors[id] = {
				window: window.open(Blox.homeURL + '/?blox-trigger=ace-editor&mode=' + mode, id, 'width=' + editorConfig.width + ',height=' + editorConfig.height + ',top=' + editorConfig.top + ',left=' + editorConfig.left, true)
			}

			Blox.aceEditors[id].window.focus();
			aceHelper.bindEditor(id, mode, initialValue, changeCallback);

			return Blox.aceEditors[id];

		},

		bindEditor: function(id, mode, initialValue, changeCallback) {

			var window = Blox.aceEditors[id].window;

			return $(window).bind('load', function() {

				/* Add keybindings */
				mousetrap.bindEventsTo(window.document);

				var ace = window.ace;

				/* Set paths */
				var acePath = Blox.bloxURL + '/library/visual-editor/' + Blox.scriptFolder + '/deps/ace/';

				ace.config.set('basePath', acePath);
				ace.config.set('modePath', acePath);
				ace.config.set('workerPath', acePath);
				ace.config.set('themePath', acePath);

				/* Init editor */
				Blox.aceEditors[id].editor = ace.edit($(window.document).contents().find('#ace-editor').get(0));
				Blox.aceEditors[id].editorSession = Blox.aceEditors[id].editor.getSession();

				/* Set editor config */
				Blox.aceEditors[id].editor.setTheme('ace/theme/clouds');
				Blox.aceEditors[id].editorSession.setMode('ace/mode/' + mode);

				Blox.aceEditors[id].editor.setShowPrintMargin(false);

				Blox.aceEditors[id].editorSession.setUseWrapMode(true);

				/* Populate the editor */
				Blox.aceEditors[id].editor.setValue(initialValue);

				/* Focus editor */
				Blox.aceEditors[id].editor.gotoLine(0);
				Blox.aceEditors[id].editor.focus();

				/* Bind the editor */
				Blox.aceEditors[id].editorSession.on('change', function(e) {
					return changeCallback(Blox.aceEditors[id].editor);
				});

			});

		}

	}

	aceHelper.init();

	return aceHelper;

});
