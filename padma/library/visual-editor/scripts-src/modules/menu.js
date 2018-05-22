define(['jquery', 'util.tour', 'helper.codeMirror', 'deps/url'], function($, tour, codeMirrorHelper, url) {

	var menu = {
		init: function() {
			menu.bind();
		},

		bind: function() {

			/* MODE SWITCHING */
				$('ul#modes li').on('click', function(){
					$(this).siblings('li').removeClass('active');
					$(this).addClass('active');
				});

				$('ul#modes li a').bind('click', function(){

					var modeURL 				= new Url($(this).attr('href'));
					modeURL.query['ve-layout'] 	= Padma.viewModels.layoutSelector.currentLayout();

					$(this).attr('href', modeURL.toString());

				});
			/* END MODE SWITCHING */

			/* VIEW SITE BUTTON */
				$('#menu-link-view-site a').bind('click', function(){

					var siteURL 					= new Url(Padma.homeURL);
					siteURL.query['padma-trigger'] 	= 'layout-redirect';
					siteURL.query['layout'] 		= Padma.viewModels.layoutSelector.currentLayout();

					$(this).attr('href', siteURL.toString());


				});
			/* END VIEW SITE BUTTON */

			/* SAVE BUTTON */
				$('span#save-button').click(function() {

					save();

					return false;

				});
			/* END SAVE BUTTON */

			/* SNAPSHOTS */
				$('#snapshots-button').bind('click', function(){

					openBox('snapshots');

				});
			/* END SNAPSHOTS */

			/* TOOLS */
				$('#tools-tour').bind('click', tour.start);

				$('#tools-grid-manager').bind('click', function(){

					hidePanel();

					openBox('grid-manager');

				});

				$('#open-live-css').bind('click', function() {

					codeMirrorHelper.showEditor('live-css', 'css', $('textarea#live-css').val(), function(editor) {

						var value 		= editor.getValue();
						var textarea 	= $('textarea#live-css');
						textarea.val(value);
						dataHandleInput(textarea);
						$i('style#live-css-holder').html(value);
						allowSaving();
						
					});

				});

				$('#tools-clear-cache').bind('click', function(){

					/* Set up parameters */
					var parameters = {
						security: Padma.security,
						action: 'padma_visual_editor',
						method: 'clear_cache'
					};

					/* Do the stuff */
					$.post(Padma.ajaxURL, parameters, function(response){

						if ( response === 'success' ) {

							showNotification({
								id: 'cache-cleared',
								message: 'The cache was successfully cleared!',
								success: true
							});

						} else {

							showErrorNotification({
								id: 'error-could-not-clear-cache',
								message: 'Error: Could not clear cache.'
							});

						}

					});

				});
			/* END TOOLS */

		}

	}

	return menu;
	

});