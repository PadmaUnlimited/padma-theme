define(['jquery', 'underscore'], function($, _) {

	openJsonUploader = function(callback) {

		if ( !boxExists('input-json') ) {

			if ( isNaN(Padma.viewModels.layoutSelector.currentLayout()) )
				iframePostID = 0;

			var settings = {
				id: 'input-json',
				title: 'Select an json',
				description: 'Upload or select an json',
				src: Padma.homeURL + '/?padma-trigger=media-uploader',
				load: function() {
					initiateJSONUploader(callback);
				},
				width: $(window).width() - 200,
				height: $(window).height() - 200,
				center: true,
				draggable: false,
				deleteWhenClosed: true,
				blackOverlay: true
			};

			var box = createBox(settings);

			$('#box-input-json').css({
				width: 'auto',
				height: 'auto',
				top: '70px',
				left: '70px',
				right: '70px',
				bottom: '70px',
				margin: 0
			});

		}

		openBox('input-json');

	}

	initiateJSONUploader = function(callback) {

		/* Check if iframe body has iframe-loaded class which is added via inline script in the footer of the iframe */
		if (
			!$('#box-input-json iframe').length
			|| typeof $('#box-input-json iframe')[0].contentWindow.wp == 'undefined'
			|| typeof $('#box-input-json iframe')[0].contentWindow.wp.media == 'undefined'
			|| typeof $('#box-input-json iframe')[0].contentWindow.wp.media() == 'undefined'
		) {

			return setTimeout(function() {
				initiateJSONUploader(callback);
			}, 100);

		}

		wpMedia = $('#box-input-json iframe')[0].contentWindow.wp.media;
		var currentLayoutFragments = Padma.viewModels.layoutSelector.currentLayout().split('-');

		/* If the current layout is a WordPress "post" then associate all attachments uploaded with the post */
		if (_.first(currentLayoutFragments) == 'single' && !isNaN(_.last(currentLayoutFragments)) ) {
			wpMedia.model.settings.post.id = _.last(currentLayoutFragments);
		}

		wpMedia.frames = {
			file_frame: wpMedia({
				title: '',
				button: {
					text: 'Use Json'
				},
				multiple: false
			})
		};

		wpMedia.frames.file_frame.on( 'select', function() {
			// We set multiple to false so only get one json from the uploader
			attachment = wpMedia.frames.file_frame.state().get('selection').first().toJSON();

			if ( typeof url == 'undefined' )
				var url = attachment.url;

			var filename = url.split('/')[url.split('/').length-1];

			callback(url, filename);

			parent.closeBox('input-json', true);
		});

		wpMedia.frames.file_frame.on('escape', function() {
			parent.closeBox('input-json', true);
		});

		return wpMedia.frames.file_frame.open();

	}

});