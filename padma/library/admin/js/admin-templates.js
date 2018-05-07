jQuery(document).ready(function($) {

	showNotification = function(args) {

		var notification = $('<div id="padma-notification-' + args.id + '" class="updated below-h2"><p>' + args.message + '</p></div>');

		notification.appendTo('#padma-admin-notifications');

		if ( typeof args.closeTimer != 'undefined' && args.closeTimer ) {

			setTimeout(function() {
				notification.fadeOut(1000, function() {
					$(this).remove();
				});
			}, args.closeTimer);

		}

		return notification;

	}

	showErrorNotification = function(args) {

		var notification = $('<div id="padma-notification-' + args.id + '" class="error below-h2"><p>' + args.message + '</p></div>');

		notification.appendTo('#padma-admin-notifications');

		return notification;

	}

	updateNotification = function(id, message) {

		return $('#padma-notification-' + id).children('p').html(message);

	}

	hideNotification = function(id) {

		$('#padma-notification-' + id).fadeOut(500);

	}


	var templates = {
		init: function() {

			templates.bind();
			templates.setupViewModel();

		},

		setupViewModel: function() {

			Padma.viewModels.templates = {
				templates: ko.observableArray(Padma.templates),
				active: ko.observable(Padma.templateActive),
				activateSkin: function() {

					var skin = this;

					/* Don't try to activate if it's already activated */
					if ( skin.id == Padma.viewModels.templates.active().id )
						return;

					/* Send AJAX Request to switch skins */
						$.post(Padma.ajaxURL, {
							security: Padma.security,
							action: 'padma_visual_editor',
							method: 'switch_skin',
							skin: skin.id
						}, function(response) {

							/* Set this skin as the activated skin */
							Padma.viewModels.templates.active(skin);

							showNotification({
								id: 'skin-switched',
								message: skin.name + ' activated.',
								closeTimer: 5000,
								success: true
							});

						});

				},
				deleteSkin: function() {

					var skin = this;

					if ( !confirm('Are you sure you want to delete this template?  All design settings, blocks, and layout settings for this template will be deleted.') )
						return;

					/* Send AJAX Request to switch skins */
						$.post(Padma.ajaxURL, {
							security: Padma.security,
							action: 'padma_visual_editor',
							method: 'delete_skin',
							skin: skin.id
						}, function(response) {

							if ( response != 'success' ) {

								return showErrorNotification({
									id: 'unable-to-delete-skin',
									message: 'Unable to delete template.',
								});

							} else {

								showNotification({
									id: 'skin-deleted',
									message: skin.name + ' deleted.',
									closeTimer: 5000,
									success: true
								});

							}

							Padma.viewModels.templates.templates.remove(skin);

						});

				}
			}

			ko.applyBindings(Padma.viewModels.templates, $('.padma-templates').get(0));

		},

		bind: function() {

			/* Skin Upload button */
			$('#install-template').on('click', function() {

				if ( $(this).is('[disabled]') )
					return;

				$('#upload-skin input[type="file"]').first().trigger('click');

			});


			/*		Install padma services cloud template		*/
			$('.install-cloud-template').on('click',function(){

				var id 		= this.id.split('-')[1];
				var token 	= $(this).data('token');

				$.post(Padma.apiURL + 'template/get-data', {
					token: 		token,
					id: 		id,
					user_agent: 'padma',					
				}, function(response) {

					try {
						var skin = JSON.parse(response.skin);

						if ( skin && typeof skin.name != 'undefined' && typeof skin['data-type'] != 'undefined' ) {

							/* Check to be sure that the JSON file is a layout */
							if ( skin['data-type'] != 'skin' ){
								return alert('Cannot load template.  Please insure that the file is a valid Padma Template.');
							}

							/* Deactivate install template button */
							$('#install-template').attr('disabled', 'true');

							showNotification({
								id: 'installing-skin',
								message: 'Installing Template: ' + skin['name'],
								closeTimer: false,
								closable: false
							});

							Padma.viewModels.templates.templates.push({
								description: null,
								name: 'Installing ' + skin['name'] + '...',
								installing: true,
								id: null,
								author: null,
								active: false,
								version: null
							});

							installSkin(skin);

						}

					} catch ( e ) {

						return alert('Cannot load template.  Please insure that the file is a valid Padma Template.');

					}

				});

			});

			$('#upload-skin input[type="file"]').on('change', function(event) {

				var skinFile = $(this).get(0).files[0];

				if ( skinFile && typeof skinFile.name != 'undefined' && typeof skinFile.type != 'undefined' ) {

					var skinReader = new FileReader();

					skinReader.onload = function(e) {

						var skinJSON = e.target.result;

						try {

							var skin = JSON.parse(skinJSON);

							/* Check to be sure that the JSON file is a layout */
							if ( skin['data-type'] != 'skin' )
								return alert('Cannot load template.  Please insure that the file is a valid Padma Template.');

							/* Deactivate install template button */
							$('#install-template').attr('disabled', 'true');

							showNotification({
								id: 'installing-skin',
								message: 'Installing Template: ' + skin['name'],
								closeTimer: false,
								closable: false
							});

							Padma.viewModels.templates.templates.push({
								description: null,
								name: 'Installing ' + skin['name'] + '...',
								installing: true,
								id: null,
								author: null,
								active: false,
								version: null
							});

							installSkin(skin);

						} catch ( e ) {

							return alert('Cannot load template.  Please insure that the file is a valid Padma Template.');

						}

					}

					$('#upload-skin input[type="file"]').val('');

					skinReader.readAsText(skinFile);

				} else {

					alert('Cannot load template.  Please insure that the file is a valid Padma Template.');

				}

			});


				installSkin = function(skin) {


					if ( typeof skin['image-definitions'] == 'object' && Object.keys(skin['image-definitions']).length ) {

						var numberOfImages = Object.keys(skin['image-definitions']).length;
						var importedImages = {};

						showNotification({
							id: 'skin-importing-images',
							message: 'Importing Images...',
							closeTimer: false,
							closable: false
						});

						var importSkinImage = function(imageID) {

							/* Update notification for image import */
								var imageIDInt = parseInt(imageID.replace('%%', '').replace('IMAGE_REPLACEMENT_', ''));

								updateNotification('skin-importing-images', 'Importing Image (' + imageIDInt + '/' + numberOfImages + ')');

							/* Do the AJAX request to upload the image */
								var imageImportXhr = $.post(Padma.ajaxURL, {
									security: Padma.security,
									action: 'padma_visual_editor',
									method: 'import_image',
									imageID: imageID,
									imageContents: skin['image-definitions'][imageID]
								}, null, 'json')
									.always(function(response) {

										/* Update notification */

										/* Check if error.  If so, fire notification */
											if ( typeof response['url'] == 'undefined' ) {
												var response = 'ERROR';

												showNotification({
													id: 'skin-importing-images-error-' + imageIDInt,
													message: 'Error Importing Image #' + imageIDInt,
													closeTimer: 10000,
													closable: true,
													error: true
												});
											}

										/* Store uploaded image URL */
											importedImages[imageID] = response;

										/* Check if there are more images to upload.  If so, upload them. */
											var nextImageID = '%%IMAGE_REPLACEMENT_' + (parseInt(imageID.replace('%%', '').replace('IMAGE_REPLACEMENT_', '')) + 1) + '%%';

											if ( typeof skin['image-definitions'][nextImageID] != 'undefined' ) {

												importSkinImage(nextImageID);

										/* If not, finalize skin installation */
											} else {

												/* Hide notification since images are uploaded is complete */
												hideNotification('skin-importing-images');

												/* Finalize */
												skin['imported-images'] = importedImages;

												finalizeSkinInstallation(skin);

											}

									});
							/* End doing AJAX request to upload image */

						}

						importSkinImage('%%IMAGE_REPLACEMENT_1%%');

					} else {

						finalizeSkinInstallation(skin);

					}

				}


					finalizeSkinInstallation = function(skin) {

						/* Remove image definitions from skin array since they've already been imported */
						if ( typeof skin['image-definitions'] != 'undefined' )
							delete skin['image-definitions'];

						/* Do AJAX request to install skin */
						return $.post(Padma.ajaxURL, {
							security: Padma.security,
							action: 'padma_visual_editor',
							method: 'install_skin',
							skin: JSON.stringify(skin)
						}).done(function(data) {

							var skin = data;

							if ( typeof skin['error'] !== 'undefined' || typeof skin['name'] == 'undefined' ) {

								if ( typeof skin['error'] == 'undefined' )
									skin['error'] = 'Could not install template.';

								Padma.viewModels.templates.templates.pop();
								$('#install-template').removeAttr('disabled');

								return showNotification({
									id: 'skin-not-installed',
									message: 'Error: ' + skin['error'],
									closable: true,
									closeTimer: false,
									error: true
								});

							}

							hideNotification('installing-skin');

							showNotification({
								id: 'skin-installed',
								message: skin['name'] + ' successfully installed.',
								closeTimer: 5000,
								success: true
							});

							/* Pop off the last skin which is going to be the loader */
							Padma.viewModels.templates.templates.pop();
							Padma.viewModels.templates.templates.push($.extend({}, {description: null}, skin));

							/* Reactive install template button */
							$('#install-template').removeAttr('disabled');

						}).fail(function(data) {

							showNotification({
								id: 'skin-not-installed',
								message: 'Error: Could not install template.',
								closable: true,
								closeTimer: false,
								error: true
							});

						});

					}

		/* Skin Export */
			$('#export-template-submit').on('click', function(event) {

				event.preventDefault();

				var params = {
					'security': Padma.security,
					'action': 'padma_visual_editor',
					'method': 'export_skin',
					'skin-info': $('#export-template-form').serialize()
				}


				var exportURL = Padma.ajaxURL + '?' + $.param(params);
				return window.open(exportURL);


			});

		/* Skin Save on Cloud */
			$('#save-template-on-cloud-submit').on('click', function(event) {

				event.preventDefault();

				var params = {
					'security': Padma.security,
					'action': 'padma_visual_editor',
					'method': 'save_skin_on_cloud',
					'skin-info': $('#save-template-on-cloud-form').serialize()
				}

				$('#TB_window .tb-close-icon').click();


				/* Do AJAX request to save skin on cloud */
				return $.post(Padma.ajaxURL, params).done(function(data) {

					if ( typeof data['ok'] !== 'undefined') {
						return showNotification({
							id: 'skin-saved',
							message: 'Template successfully saved.',
							closeTimer: 5000,
							success: true
						});						
					}
					
					if ( typeof data['error'] !== 'undefined' || typeof data['name'] == 'undefined' ) {

						if ( typeof data['error'] == 'undefined' ){
							data['error'] = 'Could not save template.';
						}

						return showNotification({
							id: 'skin-not-saved',
							message: 'Error: ' + data['error'],
							closable: true,
							closeTimer: false,
							error: true
						});

					}

						
				});

			});

			/* Export Template Image */
			var BTTemplateExportImageFrame;

			$('#template-export-image-button').on('click', function (event) {

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if (BTTemplateExportImageFrame) {
					BTTemplateExportImageFrame.open();
					return;
				}

				// Create the media frame.
				BTTemplateExportImageFrame = wp.media.frames.file_frame = wp.media({
					title: 'Select Image for Template',
					button: {
						text: 'Select Image',
					},
					multiple: false
				});

				// When an image is selected, run a callback.
				BTTemplateExportImageFrame.on('select', function () {
					attachment = BTTemplateExportImageFrame.state().get('selection').first().toJSON();

					$('input#template-export-image').val(attachment.url);

					$('img#template-export-image-preview')
						.attr('src', attachment.url)
						.show();

				});

				BTTemplateExportImageFrame.open();
			});



			/* Save on cloud Template Image */
			var BTTemplateExportImageFrame;

			$('#template-save-on-cloud-image-button ').on('click', function (event) {

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if (BTTemplateExportImageFrame) {
					BTTemplateExportImageFrame.open();
					return;
				}

				// Create the media frame.
				BTTemplateExportImageFrame = wp.media.frames.file_frame = wp.media({
					title: 'Select Image for Template',
					button: {
						text: 'Select Image',
					},
					multiple: false
				});

				// When an image is selected, run a callback.
				BTTemplateExportImageFrame.on('select', function () {
					attachment = BTTemplateExportImageFrame.state().get('selection').first().toJSON();

					$('input#template-save-on-cloud-image').val(attachment.url);

					$('img#template-save-on-cloud-image-preview')
						.attr('src', attachment.url)
						.show();

				});

				BTTemplateExportImageFrame.open();
			});


		/* Add Blank Skin */
			$('#add-blank-template').on('click', function() {

				var skinName = window.prompt('Please enter a name for the new template:' , 'Template Name');

				if ( !skinName || $('#notification-adding-blank-skin').length )
					return;

				/* Perform AJAX request to create the skin and get the ID and name */
					$.post(Padma.ajaxURL, {
						security: Padma.security,
						action: 'padma_visual_editor',
						method: 'add_blank_skin',
						skinName: skinName
					}, function(response) {

						var skinID = response['id'];
						var skinName = response['name'];

						showNotification({
							id: 'added-blank-skin',
							message: skinName + ' successfully added.',
							closeTimer: 5000,
							success: true
						});

						Padma.viewModels.templates.templates.push({
							id: skinID,
							name: skinName,
							version: null,
							author: null,
							description: null
						});

					}, 'json');

			});

		}
	}

	templates.init();

});