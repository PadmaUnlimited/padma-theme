define(['jquery', 'underscore'], function($, _) {

	openVideoUploader = function(callback) {

		if ( !boxExists('input-video') ) {

			if ( isNaN(Padma.viewModels.layoutSelector.currentLayout()) )
				iframePostID = 0;

			var settings = {
				id: 'input-video',
				title: 'Select an video',
				description: 'Upload or select an video',
				src: Padma.homeURL + '/?padma-trigger=media-uploader',
				load: function() {
					initiateVideoUploader(callback);
				},
				width: $(window).width() - 200,
				height: $(window).height() - 200,
				center: true,
				draggable: false,
				deleteWhenClosed: true,
				blackOverlay: true
			};

			var box = createBox(settings);

			$('#box-input-video').css({
				width: 'auto',
				height: 'auto',
				top: '70px',
				left: '70px',
				right: '70px',
				bottom: '70px',
				margin: 0
			});

		}

		openBox('input-video');

	}

	initiateVideoUploader = function(callback) {

		/* Check if iframe body has iframe-loaded class which is added via inline script in the footer of the iframe */
		if (
			!$('#box-input-video iframe').length
			|| typeof $('#box-input-video iframe')[0].contentWindow.wp == 'undefined'
			|| typeof $('#box-input-video iframe')[0].contentWindow.wp.media == 'undefined'
			|| typeof $('#box-input-video iframe')[0].contentWindow.wp.media() == 'undefined'
		) {

			return setTimeout(function() {
				initiateVideoUploader(callback);
			}, 100);

		}

		wpMedia = $('#box-input-video iframe')[0].contentWindow.wp.media;
		var currentLayoutFragments = Padma.viewModels.layoutSelector.currentLayout().split('-');

		/* If the current layout is a WordPress "post" then associate all attachments uploaded with the post */
		if (_.first(currentLayoutFragments) == 'single' && !isNaN(_.last(currentLayoutFragments)) ) {
			wpMedia.model.settings.post.id = _.last(currentLayoutFragments);
		}

		wpMedia.frames = {
			file_frame: wpMedia({
				title: '',
				button: {
					text: 'Use Video'
				},
				multiple: false
			})
		};

		wpMedia.frames.file_frame.on( 'select', function() {
			// We set multiple to false so only get one video from the uploader
			attachment = wpMedia.frames.file_frame.state().get('selection').first().toJSON();

			if ( typeof url == 'undefined' )
				var url = attachment.url;

			var filename = url.split('/')[url.split('/').length-1];

			callback(url, filename);

			parent.closeBox('input-video', true);
		});

		wpMedia.frames.file_frame.on('escape', function() {
			parent.closeBox('input-video', true);
		});

		return wpMedia.frames.file_frame.open();

	}

});