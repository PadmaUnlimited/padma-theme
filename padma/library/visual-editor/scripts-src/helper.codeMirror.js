define(['jquery', 'deps/mousetrap', 'switch.mode'], function($, mousetrap, switchMode) {

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


				var themeSelected 	= 'cm-s-default';
				if (switchMode.mode() == "true") { // Is set on night
					themeSelected 	= 'night';
				}

				if(mode == 'html'){
					mode = 'htmlmixed';
				}

				/* Init editor */
				var editor = window.CodeMirror.fromTextArea(window.document.getElementById("code"), {
		    		indentUnit: 		4,
		    		indentWithTabs: 	true,
		    		inputStyle: 		'contenteditable',
					lineNumbers: 		true,
		    		lineWrapping: 		true,
					styleActiveLine: 	true,
					continueComments: 	true,
		    		mode:  				mode,
					extraKeys: {				
						"Ctrl-Space": "autocomplete",
						'Ctrl-/'    : 'toggleComment',
						'Cmd-/'     : 'toggleComment',
						'Alt-F'     : 'findPersistent',
						'Ctrl-F'    : 'findPersistent',
						'Cmd-F'     : 'findPersistent',
					},
					direction: 			'ltr',
		    		autoCloseBrackets: 	true,
		    		autoCloseTags: 		true,
		    		matchTags: {
		    			bothTags: 		true
		    		},
		    		gutters: 			["CodeMirror-lint-markers"],
    				lint: 				true
				});
							
				/* Populate the editor */
				editor.setValue(initialValue);

				/* Focus editor */
				editor.focus();
				window.document.getElementById("ve-loading-overlay").classList.add('hidden');

				/* Bind the editor */
				editor.on('change',function(e){					
					// prevent close without save
					prohibitLiveCSSClose(window);
					return changeCallback(editor,window);
				});

			});

		}

	}

	codeMirrorHelper.init();
	return codeMirrorHelper;

});