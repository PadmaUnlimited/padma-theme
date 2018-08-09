define(['deps/chosen.jquery'], function(chosen) {

	afterGridManagerLoad = function() {

		$('div#box-grid-manager div.box-content').tabs({active: 0});
		$('select#grid-manager-pages-to-clone').chosen();

	}

	bindGridManager = function () {


		/* Presets */
		var gridWizardPresets = {
			'right-sidebar': [
				{
					top: 0,
					left: 0,
					width: 24,
					height: 130,
					type: 'header'
				},

				{
					top: 140,
					left: 0,
					width: 24,
					height: 40,
					type: 'navigation'
				},

				{
					top: 190,
					left: 0,
					width: 18,
					height: 320,
					type: 'content'
				},

				{
					top: 190,
					left: 18,
					width: 6,
					height: 270,
					type: 'widget-area',
					mirroringOrigin: 'sidebar-1'
				},

				{
					top: 520,
					left: 0,
					width: 24,
					height: 70,
					type: 'footer'
				},
			],

			'left-sidebar': [
				{
					top: 0,
					left: 0,
					width: 24,
					height: 130,
					type: 'header'
				},

				{
					top: 140,
					left: 0,
					width: 24,
					height: 40,
					type: 'navigation'
				},

				{
					top: 190,
					left: 0,
					width: 6,
					height: 270,
					type: 'widget-area',
					mirroringOrigin: 'sidebar-1'
				},

				{
					top: 190,
					left: 6,
					width: 18,
					height: 320,
					type: 'content'
				},

				{
					top: 520,
					left: 0,
					width: 24,
					height: 70,
					type: 'footer'
				}
			],

			'two-right': [
				{
					top: 0,
					left: 0,
					width: 24,
					height: 130,
					type: 'header'
				},

				{
					top: 140,
					left: 0,
					width: 24,
					height: 40,
					type: 'navigation'
				},

				{
					top: 190,
					left: 0,
					width: 16,
					height: 320,
					type: 'content'
				},

				{
					top: 190,
					left: 16,
					width: 4,
					height: 270,
					type: 'widget-area',
					mirroringOrigin: 'sidebar-1'
				},

				{
					top: 190,
					left: 20,
					width: 4,
					height: 270,
					type: 'widget-area',
					mirroringOrigin: 'sidebar-2'
				},

				{
					top: 520,
					left: 0,
					width: 24,
					height: 70,
					type: 'footer'
				}
			],

			'two-both': [
				{
					top: 0,
					left: 0,
					width: 24,
					height: 130,
					type: 'header'
				},

				{
					top: 140,
					left: 0,
					width: 24,
					height: 40,
					type: 'navigation'
				},

				{
					top: 190,
					left: 0,
					width: 4,
					height: 270,
					type: 'widget-area',
					mirroringOrigin: 'sidebar-1'
				},

				{
					top: 190,
					left: 4,
					width: 16,
					height: 320,
					type: 'content'
				},

				{
					top: 190,
					left: 20,
					width: 4,
					height: 270,
					type: 'widget-area',
					mirroringOrigin: 'sidebar-2'
				},

				{
					top: 520,
					left: 0,
					width: 24,
					height: 70,
					type: 'footer'
				}
			],

			'all-content': [
				{
					top: 0,
					left: 0,
					width: 24,
					height: 130,
					type: 'header'
				},

				{
					top: 140,
					left: 0,
					width: 24,
					height: 40,
					type: 'navigation'
				},

				{
					top: 190,
					left: 0,
					width: 24,
					height: 320,
					type: 'content'
				},

				{
					top: 520,
					left: 0,
					width: 24,
					height: 70,
					type: 'footer'
				}
			]
		}


		$('div#boxes').delegate('div#box-grid-manager span.layout-preset', 'mousedown', function () {

			$('div#box-grid-manager span.layout-preset-selected').removeClass('layout-preset-selected');
			$(this).addClass('layout-preset-selected');

		});


		$('div#boxes').delegate('span#grid-manager-button-preset-next', 'click', function () {

			/* Populate the step 2 panel with the proper select boxes */
			var selectedPreset = $('div#box-grid-manager span.layout-preset-selected').attr('id').replace('layout-', '');

			switch (selectedPreset) {

				case 'right-sidebar':

					$('div#grid-manager-presets-mirroring-select-sidebar-1').show();
					$('div#grid-manager-presets-mirroring-select-sidebar-2').hide();

					$('div#grid-manager-presets-mirroring-select-sidebar-1 h5').text('Right Sidebar');

					break;


				case 'left-sidebar':

					$('div#grid-manager-presets-mirroring-select-sidebar-1').show();
					$('div#grid-manager-presets-mirroring-select-sidebar-2').hide();

					$('div#grid-manager-presets-mirroring-select-sidebar-1 h5').text('Left Sidebar');

					break;


				case 'two-right':

					$('div#grid-manager-presets-mirroring-select-sidebar-1').show();
					$('div#grid-manager-presets-mirroring-select-sidebar-2').show();

					$('div#grid-manager-presets-mirroring-select-sidebar-1 h5').text('Left Sidebar');
					$('div#grid-manager-presets-mirroring-select-sidebar-2 h5').text('Right Sidebar');

					break;


				case 'two-both':

					$('div#grid-manager-presets-mirroring-select-sidebar-1').show();
					$('div#grid-manager-presets-mirroring-select-sidebar-2').show();

					$('div#grid-manager-presets-mirroring-select-sidebar-1 h5').text('Left Sidebar');
					$('div#grid-manager-presets-mirroring-select-sidebar-2 h5').text('Right Sidebar');

					break;


				case 'all-content':

					$('div#grid-manager-presets-mirroring-select-sidebar-1').hide();
					$('div#grid-manager-presets-mirroring-select-sidebar-2').hide();

					break;

			}


			/* Change the buttons around */
			$(this).hide(); //Next button

			$('span#grid-manager-button-preset-previous').show();
			$('span#grid-manager-button-preset-use-preset').show();


			/* Change the content that's being displayed */
			$('div#grid-manager-presets-step-1').hide();
			$('div#grid-manager-presets-step-2').show();

		});


		$('div#boxes').delegate('span#grid-manager-button-preset-previous', 'click', function () {

			/* Change the buttons around */
			$(this).hide(); //Previous button
			$('span#grid-manager-button-preset-use-preset').hide();

			$('span#grid-manager-button-preset-next').show();


			/* Change the content that's being displayed */
			$('div#grid-manager-presets-step-2').hide();
			$('div#grid-manager-presets-step-1').show();

		});


		$('div#boxes').delegate('span#grid-manager-button-preset-use-preset', 'click', function () {

			var selectedPreset = $('div#box-grid-manager span.layout-preset-selected').attr('id').replace('layout-', '');

			//Delete any blocks that are on the grid already
			$i('.block').each(function () {

				deleteBlock(this);

			});

			//Put the new blocks on the layout
			$.each(gridWizardPresets[selectedPreset], function () {

				var addBlockArgs = this;
				;

				delete addBlockArgs.mirroringOrigin;

				/* Handle Mirroring */
				var mirroringOrigin 	= (typeof this.mirroringOrigin != 'undefined') ? this.mirroringOrigin : this.type;
				var mirroringSelectVal 	= $('div#grid-manager-presets-mirroring-select-' + mirroringOrigin + ' select').val();

				if (mirroringSelectVal !== '') {

					addBlockArgs.settings = {}
					addBlockArgs.settings['mirror-block'] = mirroringSelectVal;

				}

				/* Add the block to the grid */
				$i('.ui-padma-grid').first().data('ui-padmaGrid').addBlock(addBlockArgs);

			});

			return closeBox('grid-manager');

		});
		/* End Presets */


		/* Layout Cloning */
		$('div#boxes').delegate('span#grid-manager-button-clone-page', 'click', function () {

			var layoutToClone = $('select#grid-manager-pages-to-clone').val();

			if (layoutToClone === '' || !layoutToClone)
				return alert('Please select a page to clone.');

			if ($(this).hasClass('button-depressed'))
				return;

			$(this).text('Cloning...').addClass('button-depressed').css('cursor', 'default');

			var request = $.ajax(Padma.ajaxURL, {
				type: 'POST',
				async: true,
				data: {
					action: 'padma_visual_editor',
					method: 'get_layout_blocks_in_json',
					security: Padma.security,
					layout: layoutToClone
				},
				success: function (data, textStatus) {

					if (textStatus == false)
						return false;

					//Delete any wrappers and blocks that are on the grid already
					$i('.wrapper').each(function () {
						deleteWrapper(getWrapperID($(this)), true);
					});

					var wrappers = data.wrappers;
					var blocks = data.blocks;

					var wrapperIDTranslations = {};

					$.each(wrappers, function (id, settings) {

						/* Pluck wrapper styling out that way it doesn't get sent to the database */
						var wrapperStyling = settings['styling'];

						delete settings['styling'];
						var newWrapper = addWrapper('bottom', settings['settings'], true);

						/* Add old and new ID to wrapperIDTranslations that way blocks being added can be added to the correct wrapper */
						var newWrapperID = getWrapperID(newWrapper);
						wrapperIDTranslations[id.replace('wrapper-', '')] = newWrapperID;

						if (typeof settings['mirror_id'] != 'undefined') {
							updateWrapperMirrorStatus(newWrapperID, settings['mirror_id']);
							dataSetWrapperOption(newWrapperID, 'mirror-wrapper', settings['mirror_id']);
						}

						/* Add in styling */
						$.each(wrapperStyling, function (property, value) {

							dataSetDesignEditorProperty({
								element: "wrapper",
								property: property,
								value: (value !== null ? value.toString() : null),
								specialElementType: "instance",
								specialElementMeta: "wrapper-" + newWrapperID
							});

							/* If margin or padding, add it in now for visible feedback */
							if (property.indexOf('margin') === 0) {

								var whichMargin = property.replace('margin-', '').capitalize();
								newWrapper.css('margin' + whichMargin, value + 'px');

							} else if (property.indexOf('padding') === 0) {

								var whichPadding = property.replace('padding-', '').capitalize();
								newWrapper.css('padding' + whichPadding, value + 'px');

							}

						});

					});

					$.each(blocks, function () {

						var blockToMirror = this['mirror_id'] ? this['mirror_id'] : this.id;

						var addBlockArgs = {
							type: this.type,
							top: this.position.top,
							left: this.position.left,
							width: this.dimensions.width,
							height: this.dimensions.height,
							settings: $.extend({}, this.settings, {'mirror-block': blockToMirror})
						};

						var blockWrapper = (typeof this.wrapper_id != 'undefined' && this.wrapper_id) ? this.wrapper_id : 'default';

						/* If there's a wrapper ID translation, use it.  Otherwise we'll put the block in the last wrapper */
						if (typeof wrapperIDTranslations[blockWrapper.replace('wrapper-', '')] != 'undefined') {

							var destinationWrapperID = '#wrapper-' + wrapperIDTranslations[blockWrapper.replace('wrapper-', '')];
							var destinationWrapper = $i('.ui-padma-grid').filter(destinationWrapperID).first();

						} else {

							var destinationWrapper = $i('.ui-padma-grid').last();

						}

						/* Add block to wrapper */
						var newBlock = destinationWrapper.data('ui-padmaGrid').addBlock(addBlockArgs);
						var newBlockID = getBlockID(newBlock);
						var oldBlockID = this.id;

						/* Queue styling for saving */
						if (typeof this.styling != 'undefined' && this.styling) {

							$.each(this.styling, function (blockInstanceID, blockInstanceInfo) {

								/* Replace the block ID instance ID of the correct block ID */
								var blockInstanceID = blockInstanceID.replace('block-' + oldBlockID, 'block-' + newBlockID);

								$.each(blockInstanceInfo.properties, function (property, value) {

									dataSetDesignEditorProperty({
										group: "blocks",
										element: blockInstanceInfo.element,
										property: property,
										value: (value !== null ? value.toString() : null),
										specialElementType: "instance",
										specialElementMeta: blockInstanceID
									});

								});

							});

						}

					});

					return closeBox('grid-manager');

				}
			});

		});
		/* End Layout Cloning */


		/* Template Assigning */
		$('div#boxes').delegate('span#grid-manager-button-assign-template', 'click', function () {

			var templateToAssign = $('select#grid-manager-assign-template').val().replace('template-', '');

			if (templateToAssign === '')
				return alert('Please select a shared layout to assign.');

			//Do the AJAX request to assign the template
			$.post(Padma.ajaxURL, {
				action: 'padma_visual_editor',
				method: 'assign_template',
				security: Padma.security,
				template: templateToAssign,
				layout: Padma.viewModels.layoutSelector.currentLayout()
			}, function (response) {

				if (typeof response === 'undefined' || response == 'failure') {
					showErrorNotification({
						id: 'error-could-not-assign-template',
						message: 'Error: Could not assign shared layout.'
					});

					return false;
				}

				$('div#layout-selector li.layout-selected').removeClass('layout-item-customized');
				$('div#layout-selector li.layout-selected').addClass('layout-item-template-used');

				$('div#layout-selector li.layout-selected span.status-template').text(response);

				//Reload iframe

				showIframeLoadingOverlay();

				//Change title to loading
				changeTitle('Visual Editor: Assigning Shared Layout');
				startTitleActivityIndicator();

				Padma.viewModels.layoutSelector.currentLayoutTemplate('template-' + templateToAssign);
				Padma.viewModels.layoutSelector.currentLayoutTemplateName($('span.layout[data-layout-id="template-' + templateToAssign + '"]').find('.template-name').text());

				//Reload iframe and new layout
				padmaIframeLoadNotification = 'Shared Layout assigned successfully!';

				loadIframe(Padma.instance.iframeCallback);

				//End reload iframe

			});

			return closeBox('grid-manager');

		});
		/* End Template Assigning */


		/* Empty Grid */
		$('div#boxes').delegate('span.grid-manager-use-empty-grid', 'click', function () {

			//Empty the grid out
			$i('.block').each(function () {

				deleteBlock(this);

			});

			closeBox('grid-manager');

		});
		/* End Empty Grid */


		/* Layout Import/Export */
		/* Layout Import */
		initiateLayoutImport = function (input) {

			var layoutChooser = input;

			if (!layoutChooser.val())
				return alert('You must select a Padma layout file before importing.');

			var layoutFile = layoutChooser.get(0).files[0];

			if (layoutFile && typeof layoutFile.name != 'undefined' && typeof layoutFile.type != 'undefined') {

				var layoutReader = new FileReader();

				layoutReader.onload = function (e) {

					var contents = e.target.result;
					var layout = JSON.parse(contents);

					/* Check to be sure that the JSON file is a layout */
					if (layout['data-type'] != 'layout')
						return alert('Cannot load layout file.  Please insure that the selected file is a valid Padma layout export.');

					if (typeof layout['image-definitions'] != 'undefined' && Object.keys(layout['image-definitions']).length) {

						showNotification({
							id: 'importing-images',
							message: 'Currently importing images.',
							closeTimer: 10000
						});

						$.post(Padma.ajaxURL, {
							action: 'padma_visual_editor',
							method: 'import_images',
							security: Padma.security,
							importFile: layout
						}, function (response) {

							var layout = response;

							/* If there's an error when sideloading images, then hault import. */
							if (typeof layout['error'] != 'undefined')
								return alert('Error while importing images for layout: ' + layout['error']);

							importLayout(layout);

						});

					} else {

						importLayout(layout);

					}

				}

				layoutReader.readAsText(layoutFile);

			} else {

				alert('Cannot load layout file.  Please insure that the selected file is a valid Padma layout export.');

			}

		}


		importLayout = function (layout) {

			/* Delete any blocks and wrappers already on the layout */
			$i('.wrapper').each(function () {
				deleteWrapper(getWrapperID(this), true);
			});

			var blocks = layout['blocks'];
			var wrappers = layout['wrappers'];

			var wrapperIDTranslations = {};

			$.each(wrappers, function (id, settings) {

				/* Pluck wrapper styling out that way it doesn't get sent to the database */
				var wrapperStyling = settings['styling'] || {};

				delete settings['styling'];
				var newWrapper = addWrapper('bottom', settings['settings'], true);

				/* Add old and new ID to wrapperIDTranslations that way blocks being added can be added to the correct wrapper */
				var newWrapperID = getWrapperID(newWrapper);
				wrapperIDTranslations[id.replace('wrapper-', '')] = newWrapperID;

				if (typeof settings['mirror_id'] != 'undefined') {
					updateWrapperMirrorStatus(newWrapperID, settings['mirror_id']);
					dataSetWrapperOption(newWrapperID, 'mirror-wrapper', settings['mirror_id']);
				}

				/* Add in styling */
				$.each(wrapperStyling, function (property, value) {

					dataSetDesignEditorProperty({
						element: "wrapper",
						property: property,
						value: (value !== null ? value.toString() : null),
						specialElementType: "instance",
						specialElementMeta: "wrapper-" + newWrapperID
					});

					/* If margin or padding, add it in now for visible feedback */
					if (property.indexOf('margin') === 0) {

						var whichMargin = property.replace('margin-', '').capitalize();
						newWrapper.css('margin' + whichMargin, value + 'px');

					} else if (property.indexOf('padding') === 0) {

						var whichPadding = property.replace('padding-', '').capitalize();
						newWrapper.css('padding' + whichPadding, value + 'px');

					}

				});

			});

			$.each(blocks, function () {

				var addBlockArgs = {
					type: this.type,
					top: this.position.top,
					left: this.position.left,
					width: this.dimensions.width,
					height: this.dimensions.height,
					settings: $.extend({}, this.settings, {'mirror-block': this['mirror_id']})
				};

				var blockWrapper = (typeof this.wrapper_id != 'undefined' && this.wrapper_id) ? this.wrapper_id : 'default';

				/* If there's a wrapper ID translation, use it.  Otherwise we'll put the block in the last wrapper */
				if (typeof wrapperIDTranslations[blockWrapper.replace('wrapper-', '')] != 'undefined') {

					var destinationWrapperID = '#wrapper-' + wrapperIDTranslations[blockWrapper.replace('wrapper-', '')];
					var destinationWrapper = $i('.ui-padma-grid').filter(destinationWrapperID).first();

				} else {

					var destinationWrapper = $i('.ui-padma-grid').last();

				}

				/* Add block to wrapper */
				var newBlock = destinationWrapper.data('ui-padmaGrid').addBlock(addBlockArgs);
				var newBlockID = getBlockID(newBlock);
				var oldBlockID = this.id;

				/* Queue styling for saving */
				if (typeof this.styling != 'undefined' && this.styling) {

					$.each(this.styling, function (blockInstanceID, blockInstanceInfo) {

						/* Replace the block ID instance ID of the correct block ID */
						var blockInstanceID = blockInstanceID.replace('block-' + oldBlockID, 'block-' + newBlockID);

						$.each(blockInstanceInfo.properties, function (property, value) {

							dataSetDesignEditorProperty({
								group: "blocks",
								element: blockInstanceInfo.element,
								property: property,
								value: (value !== null ? value.toString() : null),
								specialElementType: "instance",
								specialElementMeta: blockInstanceID
							});

						});

					});

				}

			});

			/* Finish Up */
			showNotification({
				id: 'layout-successfully-imported',
				message: 'Layout successfully imported.<br /><br />Remember to save if you wish to keep the layout.',
				closeTimer: false,
				closable: true,
				success: true
			});

			closeBox('grid-manager');

			allowSaving();

			return true;

		}


		$('div#boxes').delegate('#grid-manager-import-select-file', 'click', function () {

			$(this).siblings('input[type="file"]').trigger('click');

		});


		$('div#boxes').delegate('#grid-manager-import input[type="file"]', 'change', function (event) {

			if (event.target.files[0].name.split('.').slice(-1)[0] != 'json') {

				$(this).val(null);
				return alert('Invalid layout file.  Please be sure that the layout is a valid JSON formatted file.');

			}

			initiateLayoutImport($(this));

		});

		/* Layout Export */
		$('div#boxes').delegate('#grid-manager-export-download-file', 'click', function () {

			var params = {
				'action': 'padma_visual_editor',
				'security': Padma.security,
				'method': 'export_layout',
				'layout': Padma.viewModels.layoutSelector.currentLayout()
			}

			var exportURL = Padma.ajaxURL + '?' + $.param(params);

			return window.open(exportURL);

			closeBox('grid-manager');

		});
		/* End Import/Export */

	}

});