define(['jquery', 'deps/mousetrap'], function($, mousetrap) {

	Padma.aceEditors = {};

	var aceHelper = {
		init: function() {

			/* Close all Ace Editors when Visual Editor is closed */
			window.onunload = function() {

				$.each(Padma.aceEditors, function(index, aceEditor) {

					if ( typeof aceEditor.window != 'undefined' && !aceEditor.window.closed ) {
						aceEditor.window.close();
					}

				});

			}

		},

		showEditor: function(id, mode, initialValue, changeCallback) {

			if ( typeof Padma.aceEditors[id] != 'undefined' && !Padma.aceEditors[id].window.closed ) {
				Padma.aceEditors[id].window.focus();

				return Padma.aceEditors[id];
			}

			var editorConfig = {
				width: 750,
				height: 550
			};

			editorConfig.left = ( screen.width / 2 ) - (editorConfig.width / 2);
			editorConfig.top = ( screen.height / 2 ) - (editorConfig.height / 2);

			Padma.aceEditors[id] = {
				window: window.open(Padma.homeURL + '/?Padma-trigger=ace-editor&mode=' + mode, id, 'width=' + editorConfig.width + ',height=' + editorConfig.height + ',top=' + editorConfig.top + ',left=' + editorConfig.left, true)
			}

			Padma.aceEditors[id].window.focus();
			aceHelper.bindEditor(id, mode, initialValue, changeCallback);

			return Padma.aceEditors[id];

		},

		bindEditor: function(id, mode, initialValue, changeCallback) {

			var window = Padma.aceEditors[id].window;

			return $(window).bind('load', function() {

				/* Add keybindings */
				mousetrap.bindEventsTo(window.document);

				var ace = window.ace;

				/* Set paths */
				var acePath = Padma.PadmaURL + '/library/visual-editor/' + Padma.scriptFolder + '/deps/ace/';

				ace.config.set('basePath', acePath);
				ace.config.set('modePath', acePath);
				ace.config.set('workerPath', acePath);
				ace.config.set('themePath', acePath);

				/* Init editor */
				Padma.aceEditors[id].editor = ace.edit($(window.document).contents().find('#ace-editor').get(0));
				Padma.aceEditors[id].editorSession = Padma.aceEditors[id].editor.getSession();

				/* Set editor config */
				Padma.aceEditors[id].editor.setTheme('ace/theme/clouds');
				Padma.aceEditors[id].editorSession.setMode('ace/mode/' + mode);

				Padma.aceEditors[id].editor.setShowPrintMargin(false);

				Padma.aceEditors[id].editorSession.setUseWrapMode(true);

				/* Populate the editor */
				Padma.aceEditors[id].editor.setValue(initialValue);

				/* Focus editor */
				Padma.aceEditors[id].editor.gotoLine(0);
				Padma.aceEditors[id].editor.focus();

				/* Bind the editor */
				Padma.aceEditors[id].editorSession.on('change', function(e) {
					return changeCallback(Padma.aceEditors[id].editor);
				});

			});

		}

	}

	aceHelper.init();

	return aceHelper;

});
