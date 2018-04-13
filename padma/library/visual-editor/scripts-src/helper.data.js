define(['underscore'], function(_) {

	/* DATA HANDLING FUNCTIONS */
	dataHandleInput = function(input, value, additionalCallbackArgs) {

		var input = $(input);

		/* Make sure input exists */
		if ( !input.length )
			return false;

		/* Build variables */
		if ( typeof value == 'undefined' )
			var value = input.val();

		var optionID = input.attr('name').toLowerCase();
		var optionGroup = input.attr('data-group').toLowerCase();

		var callback = eval(input.attr('data-callback'));
		var dataHandlerOverrideCallback = eval(input.attr('data-data-handler-callback')) || null;

		/* Set up arguments */
		var panelArgs = input.parents('.sub-tabs-content-container').first().data('panel-args') || {};
		var callbackArgs = $.extend({}, {
			input: input,
			value: value
		}, panelArgs);

		/* Add in additionalCallbackArgs which is used for things like image uploader input */
		if ( typeof additionalCallbackArgs == 'object' )
			callbackArgs = $.extend({}, callbackArgs, additionalCallbackArgs);

		/* Allow saving */
		allowSaving();

		/* Handle repeater inputs */
		if ( !input.hasClass('repeater-group-input') && input.parents('.repeater-group').length ) {

			updateRepeaterValues(input.parents('.repeater'));

			if ( typeof callback == 'function' )
				callback(callbackArgs);

			return input.parents('.repeater-group');

		}

		/* If no save flag is present then stop here */
		if ( input.attr('data-no-save') ) {

			if ( typeof callback == 'function' )
				callback(callbackArgs);

			return input;

		}

		/* Route to the proper place to save the data */
		/* Data Handler Override */
		if ( typeof dataHandlerOverrideCallback == 'function' ) {

			dataHandlerOverrideCallback(callbackArgs);

			/* Block Option */
		} else if ( typeof panelArgs.block != 'undefined' && panelArgs.block ) {

			var blockID = panelArgs.blockID;

			dataSetBlockOption(blockID, optionID, value);
			refreshBlockContent(blockID, callback, callbackArgs);

			return input;

			/* Wrapper Option */
		} else if ( typeof panelArgs.wrapper != 'undefined' && panelArgs.wrapper ) {

			dataSetWrapperOption(panelArgs.wrapper.id, optionID, value);

			/* Regular Option */
		} else {

			dataSetOption(optionGroup, optionID, value);

		}

		/* Fire callback as long as it's not block setting (it would've returned above so this won't execute if it's a block setting... callback needs to fire after block content is loaded via AJAX) */
		if ( typeof callback == 'function' )
			callback(callbackArgs);

		/* Done */
		return input;

	}


	/* REGULAR OPTIONS DATA */
	dataSetOption = function(group, option, value) {

		dataPrepareOptionGroup(group);

		GLOBALunsavedValues['options'][group][option] = value;

		allowSaving();

		return GLOBALunsavedValues['options'][group];

	}


	dataPrepareOptionGroup = function(group) {

		if ( typeof GLOBALunsavedValues != 'object' )
			GLOBALunsavedValues = {};

		if ( typeof GLOBALunsavedValues['options'] != 'object' )
			GLOBALunsavedValues['options'] = {};

		if ( typeof GLOBALunsavedValues['options'][group] != 'object' )
			GLOBALunsavedValues['options'][group] = {};

		return GLOBALunsavedValues['options'][group];

	}
	/* END REGULAR OPTIONS DATA */


	/* BLOCK SAVING FUNCTIONS */
	dataSetBlockOption = function(blockID, option, value) {

		dataPrepareBlock(blockID);

		GLOBALunsavedValues['blocks'][blockID]['settings'][option] = value;

		allowSaving();

		return GLOBALunsavedValues['blocks'][blockID];

	}


	dataSetBlockPosition = function(blockID, position) {

		if ( typeof blockID === 'string' && blockID.indexOf('block-') !== -1 )
			var blockID = blockID.replace('block-', '');

		var position = position.left + ',' + position.top;

		dataPrepareBlock(blockID);

		GLOBALunsavedValues['blocks'][blockID]['position'] = position;

		allowSaving();

		return GLOBALunsavedValues['blocks'][blockID];

	}


	dataSetBlockDimensions = function(blockID, dimensions) {

		if ( typeof blockID === 'string' && blockID.indexOf('block-') !== -1 )
			var blockID = blockID.replace('block-', '');

		var dimensions = dimensions.width + ',' + dimensions.height;

		dataPrepareBlock(blockID);

		GLOBALunsavedValues['blocks'][blockID]['dimensions'] = dimensions;

		allowSaving();

		return GLOBALunsavedValues['blocks'][blockID];

	}


	dataSetBlockWrapper = function(blockID, newWrapperID) {

		dataPrepareBlock(blockID);

		GLOBALunsavedValues['blocks'][blockID]['wrapper'] = newWrapperID.toString().replace('wrapper-', '');

		allowSaving();

		return GLOBALunsavedValues['blocks'][blockID];

	}


	dataDeleteBlock = function(blockID) {

		if ( typeof blockID === 'string' && blockID.indexOf('block-') !== -1 )
			var blockID = blockID.replace('block-', '');

		dataPrepareBlock(blockID);

		GLOBALunsavedValues['blocks'][blockID]['delete'] = true;

		allowSaving();

		return GLOBALunsavedValues['blocks'][blockID];

	}


	dataAddBlock = function(block) {

		var blockID, blockType;

		if ( typeof block === 'string' && blockID.indexOf('block-') !== -1 ) {
			blockID = block.replace('block-', '');
		} else {
			blockID = getBlockID(block);
		}

		blockType = getBlockType(block);

		dataPrepareBlock(blockID);

		GLOBALunsavedValues['blocks'][blockID]['new'] = blockType;
		GLOBALunsavedValues['blocks'][blockID]['insert_id'] = block.data('desired-id');

		delete GLOBALunsavedValues['blocks'][blockID]['delete'];

		allowSaving();

		return GLOBALunsavedValues['blocks'][blockID];

	}


	dataPrepareBlock = function(blockID) {

		if ( typeof GLOBALunsavedValues != 'object' )
			GLOBALunsavedValues = {};

		if ( typeof GLOBALunsavedValues['blocks'] != 'object' )
			GLOBALunsavedValues['blocks'] = {};

		if ( typeof GLOBALunsavedValues['blocks'][blockID] != 'object' )
			GLOBALunsavedValues['blocks'][blockID] = {};

		if ( typeof GLOBALunsavedValues['blocks'][blockID]['settings'] != 'object' )
			GLOBALunsavedValues['blocks'][blockID]['settings'] = {};

		return GLOBALunsavedValues['blocks'][blockID];

	}
	/* END BLOCK HANDLING FUNCTIONS */


	/* WRAPPER DATA */
	dataSetWrapperOption = function(wrapperID, option, value) {

		wrapperID = String(wrapperID).replace('wrapper-', '');

		dataPrepareWrapper(wrapperID);

		GLOBALunsavedValues['wrappers'][wrapperID]['settings'][option] = value;

		allowSaving();

		return GLOBALunsavedValues['wrappers'][wrapperID];

	}


	dataAddWrapper = function(wrapper, settings, position) {

		wrapperID = String(wrapper.attr('id')).replace('wrapper-', '');

		dataPrepareWrapper(wrapperID);

		GLOBALunsavedValues['wrappers'][wrapperID] = {
			new: true,
			insert_id: wrapper.data('desired-id'),
			position: position,
			settings: jQuery.extend({}, {
				'columns': Padma.defaultGridColumnCount,
				'column-width': Padma.globalGridColumnWidth,
				'gutter-width': Padma.globalGridGutterWidth
			}, settings)
		};

		dataSortWrappers();

		allowSaving();

		return GLOBALunsavedValues['wrappers'][wrapperID];

	}


	dataDeleteWrapper = function(wrapperID) {

		wrapperID = String(wrapperID).replace('wrapper-', '');

		dataPrepareWrapper(wrapperID);

		GLOBALunsavedValues['wrappers'][wrapperID]['delete'] = true;

		allowSaving();

		return GLOBALunsavedValues['wrappers'][wrapperID];

	}


	dataSetWrapperWidth = function(wrapperID, fixedOrFluid) {

		var isFluid = fixedOrFluid == 'fluid';

		return dataSetWrapperOption(wrapperID, 'fluid', isFluid);

	}


	dataSetWrapperGridWidth = function(wrapperID, fixedOrFluid) {

		var isFluid = fixedOrFluid == 'fluid';

		return dataSetWrapperOption(wrapperID, 'fluid-grid', isFluid);

	}


	dataSortWrappers = function() {

		$i('.wrapper:visible').each(function() {

			var wrapperID = $(this).data('id');

			dataPrepareWrapper(wrapperID);

			GLOBALunsavedValues['wrappers'][wrapperID]['position'] = $i('.wrapper').index(this);

		});

	}


	dataPrepareWrapper = function(wrapperID) {

		wrapperID = String(wrapperID).replace('wrapper-', '');

		if ( typeof GLOBALunsavedValues != 'object' )
			GLOBALunsavedValues = {};

		if ( typeof GLOBALunsavedValues['wrappers'] == 'undefined' )
			GLOBALunsavedValues['wrappers'] = {};

		if ( typeof GLOBALunsavedValues['wrappers'][wrapperID] == 'undefined' )
			GLOBALunsavedValues['wrappers'][wrapperID] = {};

		if ( typeof GLOBALunsavedValues['wrappers'][wrapperID]['settings'] == 'undefined' )
			GLOBALunsavedValues['wrappers'][wrapperID]['settings'] = {};

	}
	/* END WRAPPER DATA */


	/* DESIGN EDITOR DATA */
	dataHandleDesignEditorInput = function(args) {

		var hiddenInput = $(args.hiddenInput);
		var value 		= args.value;

		if ( !hiddenInput.length )
			return false;

		/* If it's an uncustomized property and the user somehow tabs to the input, DO NOT send the stuff to the DB. */
		if ( hiddenInput.parents('li.uncustomized-property').length == 1 )
			return false;

		/* Get all vars */
		var element = hiddenInput.attr('element').toLowerCase();
		var property = hiddenInput.attr('property').toLowerCase();
		var selector = hiddenInput.attr('element_selector') || false;
		var specialElementType = hiddenInput.attr('special_element_type').toLowerCase() || false;
		var specialElementMeta = hiddenInput.attr('special_element_meta').toLowerCase() || false;

		/* Set the data for saving */
		args.unit = hiddenInput.siblings('.property-unit-select').find('select').val();

		dataSetDesignEditorProperty({
			element: element,
			property: property,
			value: value,
			specialElementType: specialElementType,
			specialElementMeta: specialElementMeta,
			unit: args.unit
		});

		/* Change null string to null */
		if ( value === 'null' || value == 'DELETE' )
			value = null;

		/* Update hidden input value */
		hiddenInput.val(value);

		/* Update yellow dots */
		/* Element selector node */
		$('#design-editor-element-selector-container').find('li#element-' + element)
			.addClass('customized-element')
			.attr('title', 'You have customized a property in this property group.');

		/* Customized parent */
		if ( $('#design-editor-main-elements').find('.ui-state-active').length && $('#design-editor-sub-elements').find('.ui-state-active').length )
			$('#design-editor-main-elements').find('.ui-state-active').addClass('has-customized-children');

		/* Property box */
		hiddenInput.parents('.design-editor-box').first()
			.addClass('design-editor-box-customized');

		hiddenInput.parents('.design-editor-box').first().find('.design-editor-box-title')
			.attr('title', 'You have customized a property in this property group.');

		/* Show the changes to the user */
		return dataDesignEditorPropertyFeedback({
			element: element,
			property: property,
			value: value,
			specialElementType: specialElementType,
			specialElementMeta: specialElementMeta,
			unit: args.unit
		});

	}

	dataDesignEditorPropertyFeedback = function(args) {

		/* Set up variables */
		var element 			= args.element.toLowerCase();
		var property 			= args.property.toLowerCase();
		var value 				= args.value;
		var specialElementType 	= args.specialElementType || false;
		var specialElementMeta 	= args.specialElementMeta || false;
		var selector;

		if ( value === 'null' || value == 'DELETE' ) {
			args.value = null;
			value = null;
		}

		/* Figure out the selector */
		if ( !specialElementType ) {

			selector = Padma.elements[element]['selector'];

		} else if ( specialElementType == 'layout' ) {

			var originalSelector = Padma.elements[element]['selector'].replace('body', '');

			if ( originalSelector.length ) {

				selector = 'body.layout-' + specialElementMeta.replace(/\|\|/g, '-') + ' ' + originalSelector;

			} else {

				selector = 'body.layout-' + specialElementMeta.replace(/\|\|/g, '-');

			}

		} else {

			selector = Padma.elements[element][specialElementType + 's'][specialElementMeta]['selector'];

		}

		/* Call developer-defined callback */
		if ( Padma.designEditorProperties.hasOwnProperty(property) ) {

			var callback = eval('(function(params){' + Padma.designEditorProperties[property]['js-callback'] + '})');

			args['selector'] = selector;
			args['element'] = $i(selector);

			if ( typeof args['unit'] == 'undefined' ) {
				args['unit'] = '';
			}

			callback(args);

		}

		/* If value is null, then it's an uncustomization. Remove CSS */
		if ( value == null && selector && property )
			stylesheet.delete_rule_property(selector, property);

		return selector;

	}

	dataSetDesignEditorProperty = function(args) {

		/* Set up variables */
		var element = args.element.toLowerCase();
		var property = args.property.toLowerCase();
		var value = args.value;
		var unit = args.unit;
		var specialElementType = args.specialElementType || false;
		var specialElementMeta = args.specialElementMeta || false;

		/* Add unit to value if it exists */
		if ( unit && unit.length && value != 'null' && value != 'DELETE' ) {
			value = value + unit;
		}

		/* Queue for saving */
		dataPrepareDesignEditor();

		if ( typeof GLOBALunsavedValues['design-editor'][element] != 'object' )
			GLOBALunsavedValues['design-editor'][element] = {};

		if ( specialElementType == false || specialElementMeta == false ) {

			if ( typeof GLOBALunsavedValues['design-editor'][element]['properties'] != 'object' )
				GLOBALunsavedValues['design-editor'][element]['properties'] = new Object();

			GLOBALunsavedValues['design-editor'][element]['properties'][property] = value;

			/* Change Padma.elementData as well that way other places can update before saving */
			if ( typeof Padma.elementData != 'object' )
				Padma.elementData = new Object();

			if ( typeof Padma.elementData[element] == 'undefined' )
				Padma.elementData[element] = {properties: {}};

			if ( typeof Padma.elementData[element]['properties'] == 'undefined' )
				Padma.elementData[element]['properties'] = {};

			Padma.elementData[element]['properties'][property] = value;

		} else {

			if ( typeof GLOBALunsavedValues['design-editor'][element]['special-element-' + specialElementType] != 'object' )
				GLOBALunsavedValues['design-editor'][element]['special-element-' + specialElementType] = new Object();

			if ( typeof GLOBALunsavedValues['design-editor'][element]['special-element-' + specialElementType][specialElementMeta] != 'object' )
				GLOBALunsavedValues['design-editor'][element]['special-element-' + specialElementType][specialElementMeta] = new Object();

			GLOBALunsavedValues['design-editor'][element]['special-element-' + specialElementType][specialElementMeta][property] = value;

			/* Change Padma.elementData as well that way other places can update before saving */
			if ( typeof Padma.elementData != 'object' )
				Padma.elementData = new Object();

			if ( typeof Padma.elementData[element] != 'object' )
				Padma.elementData[element] = new Object();

			if ( typeof Padma.elementData[element]['special-element-' + specialElementType] != 'object' )
				Padma.elementData[element]['special-element-' + specialElementType] = new Object();

			if ( !_.isObject(Padma.elementData[element]['special-element-' + specialElementType][specialElementMeta]) || _.isArray(Padma.elementData[element]['special-element-' + specialElementType][specialElementMeta]) )
				Padma.elementData[element]['special-element-' + specialElementType][specialElementMeta] = new Object();

			Padma.elementData[element]['special-element-' + specialElementType][specialElementMeta][property] = value;

		}

		/* Update the properties in the tree */
		if ( typeof designEditor != 'undefined' ) {

			var elementNode = $('ul#design-editor-element-selector li.element[data-element-id="' + element + '"]');

			if ( specialElementType == 'instance' ) {
				elementNode = elementNode.filter('[data-instance-id="' + specialElementMeta + '"]');
			} else if ( specialElementType == 'state' ) {
				elementNode = elementNode.filter('[data-state-id="' + specialElementMeta + '"]');
			}

			designEditor.showElementPropertiesThrottled(elementNode);

		}

		/* Allow saving */
		allowSaving();

		return true;

	}

	dataPrepareDesignEditor = function() {

		if ( typeof GLOBALunsavedValues != 'object' )
			GLOBALunsavedValues = {};

		if ( typeof GLOBALunsavedValues['design-editor'] != 'object' )
			GLOBALunsavedValues['design-editor'] = {};

		return GLOBALunsavedValues['design-editor'];

	}
	/* END DESIGN EDITOR DATA */
	/* END DATA HANDLING FUNCTIONS */

});