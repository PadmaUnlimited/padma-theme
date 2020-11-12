define(['modules/panel.inputs'], function(panelInputs) {

	/* WRAPPER FUNCTIONS */
	getWrapperID = function(element) {

		if ( !$(element).length || !$(element).data('id') ) {
			return null;
		}

		return $(element).data('id').toString().replace('wrapper-', '');

	}

	openWrapperOptions = function(wrapperID) {

		var wrapperID = 'wrapper-' + wrapperID;

		var readyTabs = function() {

			var tab = $('div#' + wrapperID + '-tab');

			/* Ready tab, sliders, and inputs */
			tab.tabs();
			panelInputs.bind('div#' + wrapperID + '-tab');

			/* Show and hide elements based on toggle options */
			handleInputTogglesInContainer(tab.find('div.sub-tabs-content'));

			/* Refresh tooltips */
			setupTooltips();

			/* Update the Grid Width Input */
			updateGridWidthInput($('div#' + wrapperID + '-tab'));

			/* If it's a mirrored wrapper, then hide the other tabs */
			if ( $('div#' + wrapperID + '-tab').find('select[name="mirror-wrapper"]').val() ) {

				$('div#' + wrapperID + '-tab ul.sub-tabs li:not(#sub-tab-config)').hide();
				selectTab('sub-tab-config', $('div#' + wrapperID + '-tab'));

			}

		}

		var wrapperIDForTab = wrapperID.replace('wrapper-', '');

		var wrapperTabName = 'Wrapper';
		
		if ( typeof $i('#' + wrapperID).data('alias') != 'undefined' && $i('#' + wrapperID).data('alias') )
			wrapperTabName += ' (' + $i('#' + wrapperID).data('alias') + ')';

		addPanelTab(wrapperID, wrapperTabName, {
			url: Padma.ajaxURL,
			data: {
				security: Padma.security,
				action: 'padma_visual_editor',
				method: 'load_wrapper_options',
				wrapper_id: wrapperID.replace('wrapper-', ''),
				unsaved_wrapper_options: getUnsavedWrapperOptionValues(wrapperID.replace('wrapper-', '')),
				layout: Padma.viewModels.layoutSelector.currentLayout()
			},
			callback: readyTabs}, true, true, 'wrapper-options');

		$('div#panel').tabs('option', 'active', $('#panel-top').children('li[role="tab"]').index($('[aria-controls="' + wrapperID + '-tab"]')));


	}


	getUnsavedWrapperOptionValues = function(wrapperID) {

		if (
			typeof GLOBALunsavedValues == 'object' &&
				typeof GLOBALunsavedValues['wrappers'] == 'object' &&
				typeof GLOBALunsavedValues['wrappers'][wrapperID] == 'object'
			)
			var unsavedWrapperSettings = GLOBALunsavedValues['wrappers'][wrapperID];

		return (typeof unsavedWrapperSettings == 'object' && Object.keys(unsavedWrapperSettings).length > 0) ? unsavedWrapperSettings : null;

	}


	getWrapperMirror = function(wrapperID) {

		return $i('.wrapper[data-id="' + wrapperID.replace('wrapper-', '') + '"]').data('mirror-wrapper');

	}


	updateWrapperMirrorStatus = function(wrapperID, mirroredWrapperID, input) {

		if ( input ) {
			var selectOptionText = input.find('option:selected').text();
		}

		var wrapperID = wrapperID.replace('wrapper-', '');
		var wrapper = $i('.wrapper[data-id="' + wrapperID + '"]');

		/* Update data-mirrored-wrapper and toggle the wrapper-mirrored class */
		if ( mirroredWrapperID ) {

			var mirroredWrapperID = mirroredWrapperID.replace('wrapper-', '');

			wrapper.addClass('wrapper-mirrored');
			wrapper.padmaGrid('disable');

			/* Hide wrapper options */
			if ( typeof input != 'undefined' )
				input.parents('.panel').find('ul.sub-tabs li:not(#sub-tab-config)').hide();

			/* Wrapper is not mirrored, remove class and set data-mirrored-wrapper to null */
		} else {

			wrapper.removeClass('wrapper-mirrored');
			wrapper.padmaGrid('enable');

			/* Show wrapper options */
			if ( typeof input != 'undefined' )
				input.parents('.panel').find('ul.sub-tabs li:not(#sub-tab-config)').show();

		}

		if ( input ) {
			wrapper.find('.wrapper-mirror-notice p').text('This wrapper is mirroring the blocks from "' + selectOptionText + '"');
		} else {
			populateWrapperMirrorNotice($i('.wrapper[data-id="' + wrapperID + '"]'));
		}

		/* Recalculate wrapper height */
		wrapper.data('ui-padmaGrid').updateGridContainerHeight();
		wrapper.data('ui-padmaGrid').resetGridCalculations();
		wrapper.data('ui-padmaGrid').alignAllBlocksWithGuides();

	}


	updateWrapperCustomClasses = function(wrapperID, value) {

		if ( Padma.mode != 'design' )
			return false;

		var wrapper = $i('.wrapper[data-id="' + wrapperID + '"]');

		if ( !wrapper.length ) {
			return false;
		}

		/* Remove existing custom classes on wrapper */
		wrapper.removeClass(wrapper.data('custom-classes'));

		/* Add new custom classes */
		wrapper.data('custom-classes', value);
		wrapper.addClass(value);

		return wrapper;

	}

	exportWrapperSettingsButtonCallback = function(args) {

		var params = {
			'security': Padma.security,
			'action': 'padma_visual_editor',
			'method': 'export_wrapper_settings',
			'wrapper-id': args.wrapper.id
		}

		var exportURL = Padma.ajaxURL + '?' + $.param(params);

		return window.open(exportURL);

	}

	initiateWrapperSettingsImport = function(args) {

		var input = args.input;
		var wrapperID = args.wrapper.id;
		var fileInput = $(input).parents('.ui-tabs-panel').first().find('input[name="wrapper-import-settings-file"]');

		//var importOptions = puBoolean($(input).parents('.ui-tabs-panel').first().find('input[name="wrapper-import-settings-include-options"]').val());
		//var importDesign = puBoolean($(input).parents('.ui-tabs-panel').first().find('input[name="wrapper-import-settings-include-design"]').val());

		if ( !fileInput.val() )
			return alert('You must select a wrapper settings export file before importing.');

		//if ( !importOptions )
			//return alert('You must import at least the options when importing wrapper settings.');

		var wrapperSettingsFile = fileInput.get(0).files[0];

		if ( wrapperSettingsFile && typeof wrapperSettingsFile.name != 'undefined' && typeof wrapperSettingsFile.type != 'undefined' ) {

			var wrapperSettingsReader = new FileReader();

			wrapperSettingsReader.onload = function(e) { 

				var contents = e.target.result;
				var wrapperSettingsImportArray = JSON.parse(contents);


				/* Check to be sure that the JSON file is a block settings export file */
					if ( wrapperSettingsImportArray['data-type'] != 'wrapper-settings' )
						return alert('Cannot load wrapper settings.  Please insure that the wrapper settings are a proper Padma wrapper settings export.');

				/* Handle the fun stuff */
					if ( typeof wrapperSettingsImportArray['image-definitions'] != 'undefined' && Object.keys(wrapperSettingsImportArray['image-definitions']).length ) {

						showNotification({
							id: 'importing-images',
							message: 'Currently importing images.',
							closeTimer: 10000
						});

						$.post(Padma.ajaxURL, {
							security: Padma.security,
							action: 'padma_visual_editor',
							method: 'import_images',
							importFile: wrapperSettingsImportArray

						}, function(response) {

								
							var wrapperSettings = response;
	

							/* If there's an error when sideloading images, then hault import. */
							if ( typeof blockSettings['error'] != 'undefined' )
								return alert('Error while importing images for wrapper: ' + wrapperSettings['error']);
								
							importWrapperSettingsAJAXCallback(wrapperID, wrapperSettings);

						});

					} else {

						importWrapperSettingsAJAXCallback(wrapperID, wrapperSettingsImportArray);

					}

			}; /* end wrapperSettingsReader.onload */

			wrapperSettingsReader.readAsText(wrapperSettingsFile);

		} else {

			alert('Cannot load wrapper settings.  Please insure that the wrapper settings are a proper Padma wrapper settings export.');

		}

	}


		importWrapperSettingsAJAXCallback = function(wrapperID, wrapperSettings) {

			/* Import wrapper settings */
				importWrapperSettings(wrapperSettings, wrapperID);

			/* Reload wrapper settings */
				removePanelTab('wrapper-' + wrapperID);
				openWrapperOptions(wrapperID);

			/* All done, allow saving */
				allowSaving();

		}

		importWrapperSettings = function(importWrapperSettings, wrapperID) {

			wrapper = $('#wrapper-' + wrapperID)

			/* Send the wrapper settings data to the unsaved data */
				dataPrepareWrapper(wrapperID);

				GLOBALunsavedValues['wrappers'][wrapperID]['settings'] = importWrapperSettings['settings'];
	

			/* Show notification */
				showNotification({
					id: 'wrapper-settings-imported-' + wrapperID,
					message: 'Wrapper settings successfully imported. Please save and refresh.',
					closeTimer: 6000,
					success: true
				});

		}


});