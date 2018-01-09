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

		showEditor: function(){

		},

		bindEditor: function(){

		},

	}

	codeMirrorHelper.init();
	return codeMirrorHelper;

});