define(['jquery'], function($) {

	Padma.contentEditorEditors 		= {};
	var contentEditorHelper 		= {

		init: function(){

		},

		showEditor: function(postId, blockId, changeCallback) {

			var blockContainer 	= $i('#block-' + blockId + ' div.block-content');
			var iframe 			= '<iframe src="/?padma-trigger=gutenberg-editor&action=edit&post=' + postId + '" frameborder="0" allowfullscreen="" width="100%" height="100%"></iframe>';
			
			blockContainer.css('height','100vh');
			blockContainer.empty().append(iframe);

		},

		bindEditor: function(id, mode, changeCallback) {

		}

	}

	contentEditorHelper.init();
	return contentEditorHelper;

});
