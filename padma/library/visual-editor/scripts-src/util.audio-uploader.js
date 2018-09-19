define(['jquery', 'underscore'], function($, _) {

	openAudioUploader = function(callback) {

		if ( !boxExists('input-audio') ) {

			if ( isNaN(Padma.viewModels.layoutSelector.currentLayout()) )
				iframePostID = 0;

			var settings = {
				id: 'input-audio',
				title: 'Select an audio',
				description: 'Upload or select an audio',
				src: Padma.homeURL + '/?padma-trigger=media-uploader',
				load: function() {
					initiateAudioUploader(callback);
				},
				width: $(window).width() - 200,
				height: $(window).height() - 200,
				center: true,
				draggable: false,
				deleteWhenClosed: true,
				blackOverlay: true
			};

			var box = createBox(settings);

			$('#box-input-audio').css({
				width: 'auto',
				height: 'auto',
				top: '70px',
				left: '70px',
				right: '70px',
				bottom: '70px',
				margin: 0
			});

		}

		openBox('input-audio');

	}

	initiateAudioUploader = function(callback) {

		/* Check if iframe body has iframe-loaded class which is added via inline script in the footer of the iframe */
		if (
			!$('#box-input-audio iframe').length
			|| typeof $('#box-input-audio iframe')[0].contentWindow.wp == 'undefined'
			|| typeof $('#box-input-audio iframe')[0].contentWindow.wp.media == 'undefined'
			|| typeof $('#box-input-audio iframe')[0].contentWindow.wp.media() == 'undefined'
		) {

			return setTimeout(function() {
				initiateAudioUploader(callback);
			}, 100);

		}

		wpMedia = $('#box-input-audio iframe')[0].contentWindow.wp.media;
		var currentLayoutFragments = Padma.viewModels.layoutSelector.currentLayout().split('-');

		/* If the current layout is a WordPress "post" then associate all attachments uploaded with the post */
		if (_.first(currentLayoutFragments) == 'single' && !isNaN(_.last(currentLayoutFragments)) ) {
			wpMedia.model.settings.post.id = _.last(currentLayoutFragments);
		}

		wpMedia.frames = {
			file_frame: wpMedia({
				title: '',
				button: {
					text: 'Use Audio'
				},
				multiple: false
			})
		};

		wpMedia.frames.file_frame.on( 'select', function() {
			// We set multiple to false so only get one audio from the uploader
			attachment = wpMedia.frames.file_frame.state().get('selection').first().toJSON();

			if ( typeof url == 'undefined' )
				var url = attachment.url;

			var filename = url.split('/')[url.split('/').length-1];

			callback(url, filename);

			parent.closeBox('input-audio', true);
		});

		wpMedia.frames.file_frame.on('escape', function() {
			parent.closeBox('input-audio', true);
		});

		return wpMedia.frames.file_frame.open();

	}

});