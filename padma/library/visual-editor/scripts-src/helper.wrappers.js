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
				action: 'Padma_visual_editor',
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
			wrapper.PadmaGrid('disable');

			/* Hide wrapper options */
			if ( typeof input != 'undefined' )
				input.parents('.panel').find('ul.sub-tabs li:not(#sub-tab-config)').hide();

			/* Wrapper is not mirrored, remove class and set data-mirrored-wrapper to null */
		} else {

			wrapper.removeClass('wrapper-mirrored');
			wrapper.PadmaGrid('enable');

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
		wrapper.data('ui-PadmaGrid').updateGridContainerHeight();
		wrapper.data('ui-PadmaGrid').resetGridCalculations();
		wrapper.data('ui-PadmaGrid').alignAllBlocksWithGuides();

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


});