define(['jquery', 'util.loader', 'knockout', 'deps/json2'], function($, loader, ko) {

	save = function() {
		
		/* If saving isn't allowed, don't try to save. */
		if ( typeof isSavingAllowed === 'undefined' || isSavingAllowed === false ) {
			return false;
		}
		
		/* If currently saving, do not do it again. */
		if ( typeof currentlySaving !== 'undefined' && currentlySaving === true ) {
			return false;
		}
	
		currentlySaving = true;		
		savedTitle 		= $('title').text();
		saveButton 		= $('span#save-button');
	
		saveButton
			.text('Saving...')
			.addClass('active')
			.css('cursor', 'wait');
		
		/* Change the title */
		changeTitle('Visual Editor: Saving');
		startTitleActivityIndicator();


		/* Do the stuff */
		$.post(Padma.ajaxURL, {
			security: Padma.security,
			action: 'padma_visual_editor',
			method: 'save_options',
			options: JSON.stringify(GLOBALunsavedValues),
			layout: Padma.viewModels.layoutSelector.currentLayout(),
			mode: Padma.mode
		}, function(response) {
			
			delete currentlySaving;

			/* If the AJAX response is '0' then show a log in alert */
			if ( response === '0' ) {
								
				saveButton.stop(true);
			
				saveButton.text('Save');
				saveButton.removeClass('active');

				saveButton.css('cursor', 'pointer');
							
				return showErrorNotification({
					id: 'error-wordpress-authentication',
					message: '<strong>Notice!</strong><br /><br />Your WordPress authentication has expired and you must log in before you can save.<br /><br /><a href="' + Padma.adminURL + '" target="_blank">Click Here to log in</a>, then switch back to the window/tab the Visual Editor is in.',
					closeTimer: false,
					closable: true
				});
				
				/* If it's not a successful save, revert the save button to normal and display an alert. */
			} else if ( typeof response.errors !== 'undefined' || (typeof response != 'object' && response != 'success') ) {
								
				saveButton.stop(true);
			
				saveButton.text('Save');
				saveButton.removeClass('active');

				saveButton.css('cursor', 'pointer');

				var errorMessage = 'There was an error while saving.  Please try again';

				if ( typeof response.errors != 'undefined' ) {

					errorMessage += '<br /><ul>';

					$.each(response.errors, function(errorIndex, errorValue) {
						errorMessage += '<li>' + errorValue + '</li>';
					});

					errorMessage += '</ul>';

				}
							
				return showErrorNotification({
					id: 'error-invalid-save-response',
					message: errorMessage,
					closeTimer: false,
					closable: true
				});

			/* Successful Save */
			} else {

				/* Hide any previous save errors */
					hideNotification('error-wordpress-authentication');
					hideNotification('error-invalid-save-response');
					
				saveButton.animate({boxShadow: '0 0 0 #7dd1e2'}, 350);
				
				setTimeout(function() {

					saveButton.css('boxShadow', '');
					saveButton.stop(true);

					saveButton.text('Save');
					saveButton.removeClass('active');

					saveButton.css('cursor', 'pointer');

					/* Replace temporary IDs on new blocks with the new ID */
						if ( typeof response['block-id-mapping'] !== 'undefined' ) {

							$.each(response['block-id-mapping'], function(tempID, id) {

								var block = $i('.block[data-temp-id="' + tempID + '"]');

								block.attr('id', 'block-' + id)
									.data('id', id)
									.attr('data-id', id)
									.removeAttr('data-temp-id')
									.removeAttr('data-desired-id')
									.removeData('duplicateOf')
									.removeData('temp-id')
									.removeData('desired-id');

								updateBlockContentCover(block);

								/* Reload options with proper ID */
								if ( $('#block-' + tempID + '-tab').length ) {

									var currentSubTab = $('#block-' + tempID + '-tab').find('.sub-tabs .ui-tabs-active').attr('aria-controls');

									removePanelTab('block-' + tempID);
									openBlockOptions(block, currentSubTab)

								}

							});

						}

					/* Replace temporary IDs on new wrapper with the new ID */
						if ( typeof response['wrapper-id-mapping'] !== 'undefined' ) {

							$.each(response['wrapper-id-mapping'], function(tempID, id) {

								var wrapper = $i('.wrapper[data-temp-id="' + tempID + '"]');

								wrapper.attr('id', 'wrapper-' + id)
									.data('id', id)
									.attr('data-id', id)
									.removeData('temp-id')
									.removeData('desired-id');

								/* Reload options with proper ID */
								if ( $('#wrapper-' + tempID + '-tab').length ) {

									removePanelTab('wrapper-' + tempID);
									openWrapperOptions(id);

								}

							});

						}

					/* Clear out hidden inputs */
					clearUnsavedValues();

					/* Output information about snapshot */
						if ( typeof response['snapshot'] !== 'undefined' && typeof response['snapshot'].timestamp !== 'undefined' ) {

							showNotification({
								id: 'snapshot-saved',
								message: 'Snapshot automatically saved.',
								success: true
							});

							Padma.viewModels.snapshots.snapshots.unshift({
								id: response['snapshot'].id,
								timestamp: response['snapshot'].timestamp,
								comments: response['snapshot'].comments
							});

						}

					/* Set the current layout to customized after save */
					if ( $('li.layout-selected').length ) {

						ko.dataFor($('li.layout-selected').get(0)).customized(true);

					}

					/* Fade back to inactive save button. */
					disallowSaving();				

					/* Reset the title and show the saving complete notification */
					setTimeout(function() {

						stopTitleActivityIndicator();
						changeTitle(savedTitle);

						showNotification({
							id: 'saving-complete',
							message: 'Saving Complete!',
							closeTimer: 3500,
							success: true
						});

					}, 150);

				}, 350);

				allowVEClose(); //Do this here in case we have some speedy folks who want to close VE ultra-early after a save.
				
			}

		});
	
	}

	clearUnsavedValues = function() {

		delete GLOBALunsavedValues;
		
	}

	allowSaving = function() {
						
		/* If it's the layout mode and there no blocks on the page, then do not allow saving.  Also do not allow saving if there are overlapping blocks */
			if ( (Padma.mode == 'grid' && $i('.block').length === 0) || (typeof Padma.overlappingBlocks != 'undefined' && Padma.overlappingBlocks) )
				return disallowSaving();

		/* If saving is already allowed, don't do anything else	*/
			if ( typeof isSavingAllowed !== 'undefined' && isSavingAllowed === true )
				return;
				
		$('body').addClass('allow-saving');
		isSavingAllowed = true;
		
		/* Set reminder when trying to leave that there are changes. */
		prohibitVEClose();
		
		return true;
		
	}
	
	disallowSaving = function() {
		
		isSavingAllowed = false;

		$('body').removeClass('allow-saving');

		/* User can safely leave VE now--changes are saved.  As long as there are no overlapping blocks */
		if ( typeof Padma.overlappingBlocks == 'undefined' || !Padma.overlappingBlocks )
			allowVEClose();

		return true;
		
	}

});