define(['jquery', 'underscore', 'helper.contentEditor', 'deps/interact', 'deps/colorpicker', 'helper.blocks', 'modules/grid/wrappers' ], function($, _, contentEditor, interact) {

	/* DESIGN EDITOR ELEMENT LOADING */
		designEditorRequestElements = function(forceReload) {

			if ( Padma.elementsRequest && (!forceReload || typeof forceReload == 'undefined') )
				return Padma.elementsRequest;

			/* Get the elements and set up bindings */
			Padma.elementsRequest = $.post(Padma.ajaxURL, {
				security: Padma.security,
				action: 'padma_visual_editor',
				method: 'get_design_editor_elements',
				layout: Padma.viewModels.layoutSelector.currentLayout()
			}, function(elements) {

				Padma.elementGroups = $.extend({}, elements.groups);

				delete elements.groups; 

				Padma.elements = elements;

			}, 'json');

			return Padma.elementsRequest;

		}

		designEditorRequestElementData = function(forceReload) {

			if ( Padma.elementDataRequest && (!forceReload || typeof forceReload == 'undefined') )
				return Padma.elementDataRequest;

			/* Get the elements and set up bindings */
			Padma.elementDataRequest = $.post(Padma.ajaxURL, {
				security: Padma.security,
				action: 'padma_visual_editor',
				method: 'get_design_editor_element_data',
				layout: Padma.viewModels.layoutSelector.currentLayout()
			}, function(elementData) {

				Padma.elementData = elementData;

			}, 'json');

			return Padma.elementDataRequest;

		}
	/* END DESIGN EDITOR ELEMENT LOADING */


	/* DESIGN EDITOR TABS */
		designEditorTabEditor = function() {

			var self = this;
			
			this._init = function() {

				createCog($('#side-panel'), true, true);

				$.when(designEditorRequestElements(), designEditorRequestElementData())
					.then(this.setupElementSelector);

				this.bindElementSelector();

				this.setupTabs();

				this.setupBoxes();
				this.bindDesignEditorInfo();
			
			}

			this.setupTabs = function() {

				$('#side-panel-top').tabs();

			}

			this.setupBoxes = function() {
									
				designEditorBindPropertyBoxToggle();
			
			}
		
			this.setupElementSelector = function() {

                /* Show tabs */
                $('#side-panel').addClass('side-panel-loaded');
                $('#side-panel').find('> .cog-container').remove();

				/* Load in elements */
					$('#design-editor-element-selector').empty();

					$.each(Padma.elementGroups, function(groupID, groupInfo) {

						var groupNode = $('<li id="element-group-' + groupID + '" class="element-group has-children">\
											<span class="element-group-name">' + groupInfo.name + '</span>\
											<span class="element-expander"></span>\
											<ul class="group-elements"></ul>\
										</li>');

						if ( typeof groupInfo.description != 'undefined' && groupInfo.description ) {
							groupNode.find('.element-group-name').after('<small class="description">' + groupInfo.description + '</small>');
						}

						groupNode.appendTo('#design-editor-element-selector');

					});

					$.each(Padma.elements, designEditor.addElementToSelector);

					/* Move each element to its appropriate parent */
						$('#design-editor-element-selector li.element').each(function() {

							var parentID = $(this).data('parent');

							if ( !parentID )
								return;

							var parentElement = $('#design-editor-element-selector li#element-' + parentID);

							/* Set parent element up to handle children */
								if ( !parentElement.hasClass('has-children') ) {

									parentElement.addClass('has-children');

									parentElement.prepend('<span class="element-expander"></span>');
									parentElement.append('<ul class="children-elements"></ul>');

								}

							/* Move element to its parent */
								$(this).appendTo(parentElement.find('> ul.children-elements'));

							/* If this element is customized then put the customized children flag on its parent */
								if ( $(this).hasClass('customized-element') )
									parentElement.addClass('had-customized-children');


						});
					/* End moving each element to its parent */

				/* Add instances and states */
					$('#design-editor-element-selector li.element').each(function() {
						designEditor.addElementStates($(this));
						designEditor.addElementInstances($(this));
					});
				/* End loading in elements */

				/* Show only layout elements */
				designEditor.showOnlyLayoutElements();

			}

				this.showAllElements = function(event) {

					$('#design-editor-element-selector-container').removeClass('show-only-layout-elements');

				}


				this.showOnlyLayoutElements = function() {

					$('#design-editor-element-selector-container').addClass('show-only-layout-elements');

					var callback = function($el) {

						if ( $el.hasClass('element-instances-container') ) {
							return $el.find('> ul > li.element').each(function () {
								callback($(this));
							});
						} else if ( !$el.data('selector') ) {
							return;
						}

						if ( $i($el.data('selector')).length ) {

							$el.addClass('element-in-layout');

							$el.find('> ul > li.element').each(function() {
								callback($(this));
							});

							/* Show all states of element */
							$el.find('> ul > li.element-states-container > ul.children-elements > li.element-state').addClass('element-in-layout');

						} else {

							$el.removeClass('element-in-layout');

							/* Hode all states of element */
							$el.find('> ul > li.element-states-container > ul.children-elements > li.element-state').removeClass('element-in-layout');

						}

					};

					$('#design-editor-element-selector > .element-group > ul.group-elements > li.element').each(function() {
						callback($(this));
					});

				}


				this.bindElementSelector = function() {

					var $elementSelector 	= $('ul#design-editor-element-selector');
					var $deStyles 			= $('ul#design-editor-styles');

					/* Bind the element clicks */
						$elementSelector.on('click', designEditor.processNoElementClick);
						$elementSelector.on('mouseenter', 'li span.element-name', designEditor.processElementMouseEnter);
						$elementSelector.on('mouseleave', 'li span.element-name', designEditor.processElementMouseLeave);
						$elementSelector.on('click', 'li span.element-name', designEditor.processElementClick);
						$elementSelector.on('click', 'li span.element-expander', designEditor.processElementExpanderClick);
					/* End binding elements */

					/* Element Name Buttons */
						$elementSelector.on('click', 'li span.element-name span.element-name-button-layout-specific', designEditor.processElementNameLayoutSpecific);
						$elementSelector.on('click', 'li span.element-name span.element-name-button-live-css', designEditor.processElementNameLiveCSS);
					/* End element name buttons */

					/* Bind property clicks */
						$deStyles.on('click', 'li.property .property-value', designEditor.processPropertyValueClick);
						$deStyles.on('click', 'li.property .property-delete', designEditor.processPropertyDeleteClick);
					/* End binding property clicks */

					/* Bind Toggle */
						$('#side-panel-collapse-arrow').on('click', toggleDesignEditor);

					/* Bind Filters */
						$('#element-selector-show-current-layout-elements').on('click', designEditor.showOnlyLayoutElements);
						$('#element-selector-show-all-elements').on('click', designEditor.showAllElements);
					/* End binding filters */
					
				}


				this.addElementToSelector = function(elementID, elementSettings) {

					var elementDescription = elementSettings['description'] ? '<small class="description">' + elementSettings['description'] + '</small>' : '';
					var elementNode = $('<li id="element-' + elementID + '" data-element-id="' + elementID + '" class="element" data-selector="' + elementSettings['selector'] + '">\
												<span class="element-name">' + elementSettings['name'] + '</span>\
												' + elementDescription + '\
											</li>');
					
					/* Set Data */
						elementNode.data({
							group: elementSettings['group'], 
							parent: elementSettings['parent'],
							selector: elementSettings['selector'],
							id: elementID
						});

					/* Customized flag */
						if ( elementSettings['customized'] ) {
							elementNode.addClass('customized-element');
						}

					/* Append element to either group or parent */
						elementNode.appendTo($('li#element-group-' + elementSettings['group'] + ' ul.group-elements'));

				}


				this.addElementInstances = function(elementNode) {

					var elementID = elementNode.data('element-id');
					var instances = designEditorGetElementObject(elementID, false).instances;

					if ( _.isEmpty(instances) )
						return false;

					/* Sort instances alphabetically */
						var instancesArray = $.map(instances, function(k, v) {
	    					return [k];
						});

						instancesArray.sort(function(a, b){
						    if(a.name < b.name) return -1;
						    if(a.name > b.name) return 1;
						    return 0;
						});

					/* Add instances */
						if ( !elementNode.hasClass('has-children') ) {

							elementNode.addClass('has-children');
							elementNode.prepend('<span class="element-expander"></span>');

							elementNode.append('<ul class="children-elements"></ul>');

						}
					
						elementNode.children('.children-elements').prepend('\
							<li id="element-' + elementID + '-instances" class="element element-instances-container has-children">\
								<span class="element-expander"></span>\
								<span class="element-name">Instances</span>\
								<ul class="children-elements"></ul>\
							</li>\
						');

						elementNode.addClass('instances-visible');

						$.each(instancesArray, function(key, instance) {

							/* Build instance name */
								var id = instance.id;
								var instanceName = instance.name;

								if ( typeof instance['state-of'] != 'undefined' && instance['state-of'] )
									instanceName = '  -- ' + instance['state-name'];

							/* Add instance to tree */
								var instanceNode = $('<li id="element-instance-' + id + '" data-element-id="' + elementID + '" data-instance-id="' + id + '" data-selector="' + instance['selector'] + '" class="element element-instance">\
										<span class="element-name">' + instanceName + '</span>\
										<small class="description">' + instance['layout-name'] + '</small>\
									</li>')
										.appendTo(elementNode.find('> ul.children-elements > li.element-instances-container > ul.children-elements'));

								/* Add instance data to node */
								instanceNode.data({
									selector: instance['selector']
								});

							/* Instance Customized Class */
								if ( typeof instance['customized'] != 'undefined' && instance['customized'] )
									instanceNode.addClass('customized-element');

						});
					/* End add instances loop */

				}


				this.addElementStates = function(elementNode) {

					var elementID = elementNode.data('element-id');
					var states = designEditorGetElementObject(elementID).states;

					if ( _.isEmpty(states) )
						return false;

					/* Add states */
						if ( !elementNode.hasClass('has-children') ) {

							elementNode.addClass('has-children');
							elementNode.prepend('<span class="element-expander"></span>');

							elementNode.append('<ul class="children-elements"></ul>');

						}
					
						elementNode.children('.children-elements').prepend('\
							<li id="element-' + elementID + '-states" class="element element-states-container has-children">\
								<span class="element-expander"></span>\
								<span class="element-name">States</span>\
								<ul class="children-elements"></ul>\
							</li>\
						');

						elementNode.addClass('states-visible');

						$.each(states, function(stateID, stateInfo) {

							/* Add instance to tree */
								var stateNode = $('<li id="element-state-' + stateID + '-for-' + elementID + '" data-element-id="' + elementID + '" data-state-id="' + stateID + '" data-selector="' + stateInfo.selector + '" class="element element-state">\
										<span class="element-name">' + stateInfo.name + '</span>\
									</li>')
										.appendTo(elementNode.find('> ul.children-elements > li.element-states-container > ul.children-elements'));

								/* Add instance data to node */
								stateNode.data({
									selector: stateInfo.selector
								});

						});
					/* End add states loop */

				}

				/* Element Property Handling */
					this.showElementProperties = function(elementNode) {

						if ( typeof elementNode != 'object' )
							var elementNode = $(this);

						var elementID = elementNode.data('element-id');

						/* Remove existing properties */
						$('ul#design-editor-styles').empty().show();

						/* Hide the you have nothing selected message */
						$('.design-editor-styles-message').hide();

						/* Insure that the element is customized and that there is data for it. */
						if ( _.isEmpty(Padma.elementData[elementID]) ) {

							$('ul#design-editor-styles').empty();

							$('#design-editor-styles-no-styles').show();
							$('#design-editor-styles-nothing-selected').hide();

							return;

						}

						$('ul#design-editor-styles').prepend('<h2>' + elementNode.children('.element-name').first().text() + '</h2>');

						var elementProperties = Padma.elementData[elementID];

						/* Create a node that will hold the properties.  We'll put it into place later that way we can insure that there are properties in it */
							var elementPropertiesLI = $('<li class="properties"><ul></ul></li>');
							var elementPropertiesUL = $('ul#design-editor-styles');

						/* Handle regular element properties */
							if ( !_.isEmpty(Padma.elementData[elementID]['properties']) ) {

								$.each(Padma.elementData[elementID]['properties'], function(property, propertyValue) {
									self.addElementProperty(elementPropertiesUL, property, propertyValue);
								});

							}

						/* Handle special element properties */
							var specialElementTypes = ['instance', 'state', 'layout'];

							$.each(specialElementTypes, function(index, specialElementType) {

								if ( !_.isEmpty(Padma.elementData[elementID]['special-element-' + specialElementType]) ) {

									$.each(Padma.elementData[elementID]['special-element-' + specialElementType], function(specialElementID, specialElementProperties) {

										/* Build special element name */
											if ( specialElementType == 'layout' ) {

												var specialElementName = $('#layout-selector span[data-layout-id="' + specialElementID + '"] strong').text();
												var specialElementLayoutName = '';

											} else if ( !_.isUndefined(designEditorGetElementObject(elementID, false)[specialElementType + 's'][specialElementID]) ) {

												var specialElementName = designEditorGetElementObject(elementID, false)[specialElementType + 's'][specialElementID].name;

												if ( typeof designEditorGetElementObject(elementID, false)[specialElementType + 's'][specialElementID]['layout-name'] != 'undefined' ) {
													var specialElementLayoutName = ' &ndash; Layout: ' + designEditorGetElementObject(elementID, false)[specialElementType + 's'][specialElementID]['layout-name'];
												} else {
													var specialElementLayoutName = '';
												}

											} else {

												return;

											}

										/* Add special element properties */
										$.each(specialElementProperties, function(property, propertyValue) {
											self.addElementProperty(elementPropertiesUL, property, propertyValue, {
												type: specialElementType, 
												id: specialElementID,
												name: specialElementName,
												layoutName: specialElementLayoutName
											});
										});

									});

								}

							});
						/* End special element handling */

						setupTooltips();
					
						return elementPropertiesLI;

					}

						this.showElementPropertiesThrottled = _.throttle(this.showElementProperties, 300);

					this.addElementProperty = function(context, property, propertyValue, specialElement) {

						/* Do not display deleted properties */
						if ( propertyValue == 'DELETE' )
							return false;

						if ( typeof Padma.designEditorProperties[property] == 'undefined' )
							return false;

						var formattedPropertyValue;
						var propertyObject = Padma.designEditorProperties[property];
						var propertyGroupID = propertyObject.group.toLowerCase().replace(' ', '-');

						/* Add in group separators for the properties */
							if ( !context.find('.property-value-group-name[data-property-group-id="' + propertyGroupID + '"]').length ) {
								context.append('<li class="property-value-group-name" data-property-group-id="' + propertyGroupID + '">' + propertyObject.group + '</strong></li>');
								context.append('<ul data-property-group-id="' + propertyGroupID + '"></ul>');
							}

						/* Format the property value */
							/* Uncustomizations */
							if ( _.isEmpty(propertyValue) ) {

								formattedPropertyValue = 'Inheriting';

							/* Colors */
							} else if ( propertyObject['type'] == 'color' ) {

								var colorValue = propertyValue.replace('#', '');

								if ( colorValue.length == 6 )
									colorValue = '#' + colorValue;

								formattedPropertyValue = '\
									<div class="colorpicker-box-container">\
										<div style="background-color: ' + colorValue + '" class="colorpicker-box"></div>\
									</div>';

								if ( propertyValue.replace('#', '').toUpperCase().length == 6 )
									formattedPropertyValue += '<span style="font-family:monospace;">#' + propertyValue.replace('#', '').toUpperCase() + '</span>';

							/* Colors */
							} else if ( propertyObject['type'] == 'image' ) {

								formattedPropertyValue = '\
									<span class="tooltip tooltip-left" title="&lt;img src=\'' + propertyValue + '\' style=\'max-width:300px;height:auto;\' /&gt;" >Preview</span>\
								';

							/* Integers with units */
							} else if ( propertyObject['unit'] && propertyValue ) {

								if ( typeof propertyObject['unit'] != 'object' ) {

									var unitString = propertyObject['unit'];

								} else {

									var unitString = propertyValue.replace(/^[+-]?\d+(\.\d+)?/g, '');

									if ( !unitString ) {

										if ( !propertyObject['unit']['default'] ) {
											propertyObject['unit']['default'] = 'px';
										}

										unitString = propertyObject['unit']['default'];

									}

									propertyValue = propertyValue.replace(unitString, '');

								}

								formattedPropertyValue = propertyValue + '<span class="unit">' + unitString + '</span>';

							/* Font families */
							} else if ( propertyObject['type'] == 'font-family-select' ) {

								var fontFragments 	= propertyValue.split('|');
								var isWebFont 		= (_.first(fontFragments) == 'google') ? true : false;

								if ( isWebFont ) {
									var fontName = fontFragments[1];
									webFontQuickLoad(propertyValue);
								} else {
									var fontName = propertyValue;
								}

								formattedPropertyValue = '<span style="font-family:' + fontName + ';">' + fontName.capitalize() + '</span>';

							/* Everything else */
							} else if ( _.isString(propertyValue) ) {

								formattedPropertyValue = propertyValue.capitalize();

							}

						/* Add the property value to the UL in the appropriate position */
							/* If it's a regular element handle it like normal */
								if ( typeof specialElement == 'undefined' ) {

									var propertyNode = $('\
										<li class="property" data-property-id="' + property + '" data-property-group-id="' + propertyGroupID + '">\
											<strong title="' + propertyObject['name'] + '">' + propertyObject['name'] + '</strong> \
											<span class="property-delete"></span>\
											<span class="property-value" title="Click to Edit">' + formattedPropertyValue + '</span>\
										</li>\
									');

									propertyNode.appendTo(context.find('ul[data-property-group-id="' + propertyGroupID + '"]'));

							/* Handle special element property values */	
								} else {

									/* Insure that the property name exists... In some cases it may not if the only customizations are special elements */
										if ( !context.find('[data-property-id="' + property + '"]:not([data-special-element-type])').length ) {

											context.find('ul[data-property-group-id="' + propertyGroupID + '"]').append('\
												<li class="property" data-property-id="' + property + '" data-property-group-id="' + propertyGroupID + '">\
													<strong>' + propertyObject['name'] + '</strong> \
												</li>\
											');

										}

									var propertyNode = $('\
										<li class="property" data-property-id="' + property + '" data-property-group-id="' + propertyGroupID + '" data-special-element-type="' + specialElement.type + '" data-special-element-id="' + specialElement.id + '">\
											<strong title="' + specialElement.name.split(' &ndash; ')[0] + specialElement.layoutName + '">' + specialElement.name.split(' &ndash; ')[0] + '</strong> \
											<span class="property-delete"></span>\
											<span class="property-value" title="Click to Edit">' + formattedPropertyValue + '</span>\
										</li>\
									');

									/* Add class to the regular element property to say that there are special elements that way we can adjust the border */
										context.find('[data-property-id="' + property + '"]').not('[data-special-element-type]').last().addClass('has-special-element-properties');

									propertyNode.insertAfter(context.find('[data-property-id="' + property + '"]').last());

								}
						/* End adding property values */

					}

					this.processPropertyValueClick = function() {

						var elementNode = $('ul#design-editor-element-selector li.ui-state-active').first();
						var elementID = elementNode.data('element-id');

						var propertyNode = $(this).parents('li.property').first();
						var specialElementType = propertyNode.data('special-element-type');
						var specialElementID = propertyNode.data('special-element-id');
						
						/* Open element */
							if ( _.isEmpty(specialElementType) ) {
								return elementNode.children('span.element-name').trigger('click', [propertyNode.data('property-group-id')]);
							} else {
								return designEditor.selectSpecialElement(elementID, specialElementType, specialElementID, propertyNode.data('property-group-id'));
							}

					}

					this.processPropertyDeleteClick = function() {

						var elementNode = $('ul#design-editor-element-selector li.ui-state-active').first();
						var elementObj = designEditorGetElementObject(elementNode.data('element-id'), false);

						var propertyNode = $(this).parents('li.property').first();
						var propertyID = propertyNode.data('property-id');

						var specialElementType = propertyNode.data('special-element-type');
						var specialElementID = propertyNode.data('special-element-id');

						/* Remove styling */
							if ( _.isEmpty(specialElementType) ) {

								var selector = elementObj.selector;

							} else if ( specialElementType == 'layout' ) {

								var selector = ('body.layout-using-' + specialElementID + ' ' + elementObj.selector).replace(' body', '');

							} else {

								var selector = elementObj[specialElementType + 's'][specialElementID].selector;


							}

							stylesheet.delete_rule_property(selector, propertyID);

						/* Remove property node */
						propertyNode.remove();

						/* If properties are empty for an element then delete the properties */
							if ( !elementNode.find('> .children-elements > .properties ul li').length ) {

								elementNode.find('> .children-elements > .properties').remove();
								elementNode.removeClass('properties-visible');

								/* If there are no children elements then delete that as well */
								if ( !elementNode.find('> .children-elements li').length ) {

									elementNode.find('> .children-elements, > .element-expander').remove();

									elementNode
										.removeClass('has-children')
										.removeClass('children-visible');

								}

							}

						/* Queue it for saving */
							dataSetDesignEditorProperty({
								element: elementObj.id, 
								property: propertyID, 
								value: 'DELETE', 
								specialElementType: specialElementType, 
								specialElementMeta: specialElementID
							});

					}
				/* End Element Property Handling */

			this.processNoElementClick = function(event) {

				/* If clicking an element, don't fire this */
				if ( $(event.target).is('span') )
					return;

				$('body').removeClass('design-editor-element-selected');

				$('ul#design-editor-element-selector').find('.ui-state-active')
					.addClass('element-just-selected')
					.removeClass('ui-state-active');

				removeInspectorVisibleBoxModal();
				setSelectedElement({});

				$i('.inspector-element-selected').removeClass('inspector-element-selected');

				/* Reset the styles tab */
				$('#design-editor-styles-no-styles').hide();
				$('#design-editor-styles-nothing-selected').show();
				$('ul#design-editor-styles').empty().hide();

			}

			this.processElementExpanderClick = function(event) {
				$(this).parent().toggleClass('children-visible');
			}

			this.processElementMouseEnter = function(event) {

				var link = $(this).parent();

				if ( link.hasClass('element-group') || link.hasClass('element-instances-container') || link.hasClass('element-states-container') )
					return;

				var elementID = link.data('element-id');
				var elementObject = designEditorGetElementObject(elementID, false);

				/* Remove .element-just-selected */
					$('.element-just-selected').removeClass('element-just-selected');

				/* Highlight [special] element in iframe */
					$i('.inspector-element-hover').removeClass('inspector-element-hover');
					$i(link.data('selector')).addClass('inspector-element-hover');

				/* Add buttons on demand that way DOM doesn't get super clogged */
					if ( !link.hasClass('element-name-has-buttons') ) {

						var elementName = link.children('.element-name');
						
						/* Instances, States, and Layout-specific button should not show on instances or states */
						if ( !link.hasClass('element-instance') && !link.hasClass('element-state') ) {

							/* Layout-specific */
								elementName.append('<span class="element-name-button element-name-button-layout-specific tooltip" title="Edit Element Only On This Layout"></span>');

						}

						/* Live CSS */
						elementName.append('<span class="element-name-button element-name-button-live-css tooltip" title="Edit in Live CSS"></span>');

						elementName.find('.tooltip').qtip({
							style: {
								classes: 'qtip-padma qtip-padma-element-selector',
								tip: false
							},
							position: {
								my: 'bottom left',
								at: 'top center',
								viewport: $(window),
								adjust: {
									y: -5,
									method: 'flipinvert'
								}
							},
						});

						link.addClass('element-name-has-buttons');

					}

			}

				this.processElementMouseLeave = function(event) {

					var link = $(this).parent();

					if ( link.hasClass('element-group') || link.hasClass('ui-state-active') || link.hasClass('element-instances-container') )
						return;

					var element = link.data('element-id');
					var elementObject = designEditorGetElementObject(element);

					/* Unhighlight element in iframe */
						$i(link.data('selector')).removeClass('inspector-element-hover');

					/* Remove buttons */
						link.children('.element-name').find('.element-name-button').each(function() {
							$(this).qtip('api').destroy(true);
							$(this).remove();
						});

						link.removeClass('element-name-has-buttons');

				}

			this.processElementClick = function(event, propertyGroup) {

				var link = $(this).parent();

				/* Do not fire element click event if an element name button is clicked */
				if ( $(event.target).hasClass('element-name-button') || link.hasClass('element-instances-container') )
					return;

				if ( link.hasClass('element-group') )
					return;

				if ( link.hasClass('element-instance') )
					return designEditor.selectSpecialElement(link.data('element-id'), 'instance', link.data('instance-id'), propertyGroup);

				if ( link.hasClass('element-state') )
					return designEditor.selectSpecialElement(link.data('element-id'), 'state', link.data('state-id'), propertyGroup);
			
				/* Set up variables */
				var elementName = getElementNodeName(link); /* Element Name */
				var element = link.data('element-id');
				var elementObject = designEditorGetElementObject(element, false);

				/* Add class to body to insure side panel is split and property inputs are showing */
					$('body').addClass('design-editor-element-selected');

				/* If element has children, then expand it */
					if ( link.hasClass('has-children') )
						link.addClass('children-visible');

				/* Expand elements parents */
					$(this).parents('li.has-children').addClass('children-visible');

				/* Scroll to element in element selector */
					$('div#design-editor-element-selector-container').animate({scrollTop: link.offset().top - (150 - $('div#design-editor-element-selector-container').scrollTop())}, 300);

				/* Reset regular element/element for layout buttons */
					$('div.design-editor-info span.customize-for-regular-element').hide();
					$('div.design-editor-info span.customize-element-for-layout').show();

				/* Highlight element in iframe */
					inspectorSelectElement(elementObject.selector);

				/* Update DE info text */
					setSelectedElement({
						id: element,
						name: elementName,
						object: elementObject
					});

				/* LOAD INPUTS */
					designEditorShowCog();

					$.when(
						designEditor.loadElementInputs(element)
					).then(function() {
						designEditorShowContent(propertyGroup);
					});
				/* END LOAD INPUTS */

				$('ul#design-editor-element-selector').find('.ui-state-active').removeClass('ui-state-active');
				link.addClass('ui-state-active');

				/* Load properties */
				designEditor.showElementProperties(link);

			}

				this.processElementNameLayoutSpecific = function() {

					var elementNode = $(this).parents('li.element').first();
					var elementID = elementNode.data('element-id');

					designEditor.selectSpecialElement(elementID, 'layout', Padma.viewModels.layoutSelector.currentLayout());

				}

				this.processElementNameLiveCSS = function() {

					var elementNode 	= $(this).parents('li.element').first();
					var elementSelector = elementNode.data('selector');					
					var liveCSSValue 	= ( typeof Padma.codeMirrorEditors['live-css'] == 'undefined' || !Padma.codeMirrorEditors['live-css'] ) ? $('textarea#live-css-content').val() : atob(localStorage['padma-visual-editor-live-css-content']);
					var linesBefore 	= liveCSSValue ? "\n\n" : '';
					$('textarea#live-css-content').val(liveCSSValue + linesBefore + elementSelector + " {\n\n}");

					/* Open CSS Editor */
					$('#open-live-css').trigger('click');

					/* Move the cursor to the new selector */
					if ( typeof Padma.codeMirrorEditors['live-css'] != 'undefined' ) {

						$(Padma.codeMirrorEditors["live-css"].window.document.getElementById("code")).val($('textarea#live-css-content').val());						
                        $(Padma.codeMirrorEditors["live-css"].window.document.getElementById("code")).focus();

					}

				}

			this.processElementCopy = function(event) {

				var currentElement = getSelectedElement();

				if ( !currentElement )
					return;

				var currentElementName = (typeof currentElement.specialElementName != 'undefined') ? currentElement.specialElementName : currentElement.name;
				
				/* Get data */
					if ( !_.isEmpty(Padma.elementData[currentElement.id]) ) {

						/* If it's a special element then we need to pull properties from there */
							if ( currentElement.specialElementType && !_.isEmpty(Padma.elementData[currentElement.id]['special-element-' + currentElement.specialElementType]) ) {

								if ( !_.isEmpty(Padma.elementData[currentElement.id]['special-element-' + currentElement.specialElementType][currentElement.specialElementID]) )
									var elementData = Padma.elementData[currentElement.id]['special-element-' + currentElement.specialElementType][currentElement.specialElementID];

						/* Otherwise looks in 'properties' for the regular elements */
							} else if ( !_.isEmpty(Padma.elementData[currentElement.id]['properties']) ) {

								var elementData = Padma.elementData[currentElement.id]['properties'];

							}

					}

					if ( typeof elementData == 'undefined' || _.isEmpty(elementData) )
						return false;

				showNotification({
					id: 'copied-design-properties',
					message: 'Copied properties from <strong>' + currentElementName + '</strong>',
					closeTimer: 2000,
					overwriteExisting: true
				});

				Padma.designEditorClipboard = $.extend({}, elementData);

			}

			this.processElementPaste = function(event) {

				var currentElement = getSelectedElement();

				if ( !currentElement || typeof Padma.designEditorClipboard == 'undefined' )
					return;

				var currentElementName = (typeof currentElement.specialElementName != 'undefined') ? currentElement.specialElementName : currentElement.name;

				/* Pull existing data that way we can modify it */
						if ( !_.isEmpty(Padma.elementData[currentElement.id]) ) {

							if ( currentElement.specialElementType && !_.isEmpty(Padma.elementData[currentElement.id]['special-element-' + currentElement.specialElementType]) ) {

								if ( !_.isEmpty(Padma.elementData[currentElement.id]['special-element-' + currentElement.specialElementType][currentElement.specialElementID]) )
									var elementData = Padma.elementData[currentElement.id]['special-element-' + currentElement.specialElementType][currentElement.specialElementID];

							} else if ( !_.isEmpty(Padma.elementData[currentElement.id]['properties']) ) {

								var elementData = Padma.elementData[currentElement.id]['properties'];

							}

						}

						if ( typeof elementData == 'undefined' )
							var elementData = {};

				/* Go through existing data and set EVERYTHING to delete first */
					$.each(elementData, function(property, value) {
						
						dataSetDesignEditorProperty({
							element: currentElement.id,
							property: property,
							value: 'DELETE',
							specialElementType: currentElement.specialElementType,
							specialElementMeta: currentElement.specialElementID
						});

					});

				/* Merge in the pasted data */
					$.each(Padma.designEditorClipboard, function(property, value) {

						if ( typeof Padma.designEditorProperties[property]['unit'] != 'undefined' && !isNaN(value) ) {

							if ( typeof Padma.designEditorProperties[property]['unit'] == 'string' ) {

								value = value + Padma.designEditorProperties[property]['unit'];

							} else if ( typeof Padma.designEditorProperties[property]['unit']['default'] != 'undefined' ) {

								value = value + Padma.designEditorProperties[property]['unit']['default'];

							}

						}

						dataSetDesignEditorProperty({
							element: currentElement.id,
							property: property,
							value: value,
							specialElementType: currentElement.specialElementType,
							specialElementMeta: currentElement.specialElementID
						});

					});

				/* Now loop through the element data and update it in the iframe */
					if ( currentElement.specialElementType ) {
						var elementData = Padma.elementData[currentElement.id]['special-element-' + currentElement.specialElementType][currentElement.specialElementID];
					} else {
						var elementData = Padma.elementData[currentElement.id]['properties'];
					}

					$.each(elementData, function(property, value) {

						dataDesignEditorPropertyFeedback({
							element: currentElement.id,
							property: property,
							value: value,
							specialElementType: currentElement.specialElementType,
							specialElementMeta: currentElement.specialElementID
						});

					});

				showNotification({
					id: 'copied-design-properties',
					message: 'Pasted properties onto <strong>' + currentElementName + '</strong>',
					closeTimer: 2000,
					overwriteExisting: true,
					success: true
				});


			}

			this.loadElementInputs = function(element, specialElementInfo) {

				var ajaxArgs = {
					security: Padma.security,
					action: 'padma_visual_editor',
					method: 'get_element_inputs',
					unsavedValues: designEditorGetUnsavedValues(element),
					element: designEditorGetElementObject(element)
				};

				if ( typeof specialElementInfo == 'object' ) {

					var specialElementType = Object.keys(specialElementInfo)[0];
					var specialElementMeta = specialElementInfo[specialElementType];

					ajaxArgs['specialElementType'] = specialElementType;
					ajaxArgs['specialElementMeta'] = specialElementMeta;
					ajaxArgs['unsavedValues'] = designEditorGetUnsavedValues(element, specialElementType, specialElementMeta);

					/* If special element type is instance we need to be sure that the instance is included in the element payload that way the instance info can be pulled when displaying the property inputs */
					if ( specialElementType == 'instance' )
						ajaxArgs['element'] = designEditorGetElementObject(element, true, specialElementMeta)

				}

				return $.post(Padma.ajaxURL, ajaxArgs).done(function(inputs) {
				
					var options = $('div.design-editor-options');

					options.html(inputs);

					/* Set the flags */
					$('div.design-editor-options').data({
						'element': element, 
						'specialElementType': false, 
						'specialElementMeta': false
					});

					/* Load web fonts */
					$('div.design-editor-options').find('.property-font-family-select').each(function() {
						webFontQuickLoad($(this).find('span.font-name').data('webfont-value'));
					});

					/* Focus the iframe to allow immediate nudging control */
					Padma.iframe.focus();

				});

			}


			this.bindDesignEditorInfo = function() {
					
				/* Customize for layout button */
				$('span.customize-element-for-layout').bind('click', function() {

					var currentElement = designEditor.getCurrentElement();
					var currentElementID = currentElement.data('element-id');

					designEditor.selectSpecialElement(currentElementID, 'layout', Padma.viewModels.layoutSelector.currentLayout());

				});
			
				/* Customize for regular element button */
				$('span.customize-for-regular-element').bind('click', function() {
					designEditor.getCurrentElement().find('> span.element-name').trigger('click');
				});

			}

				this.selectSpecialElement = function(elementID, specialElementType, specialElementID, propertyGroup) {

					var elementNode = $('ul#design-editor-element-selector li.element-' + specialElementType)
						.filter('[data-' + specialElementType + '-id="' + specialElementID + '"]')
						.filter('[data-element-id="' + elementID + '"]');

					/* If the special element node isn't present then we'll use the regular element node */
					if ( !elementNode.length )
						elementNode = $('ul#design-editor-element-selector li.element[data-element-id="' + elementID + '"]').first();

					var elementObject = designEditorGetElementObject(elementID, false);
					var specialElementName = (specialElementType != 'layout') ? elementObject[specialElementType + 's'][specialElementID].name : Padma.viewModels.layoutSelector.currentLayoutName();
					var elementSelector = (specialElementType != 'layout') ? elementObject[specialElementType + 's'][specialElementID].selector : elementObject.selector;

					/* Add class to body to insure side panel is split and property inputs are showing */
						$('body').addClass('design-editor-element-selected');					

					/* Expand elements parents */
						elementNode.parents('li').addClass('children-visible');

					/* Scroll to element in element selector */
						$('div#design-editor-element-selector-container').animate({scrollTop: elementNode.offset().top - ($('div#design-editor-element-selector-container').height()/1.5 - $('div#design-editor-element-selector-container').scrollTop())}, 300);

					/* Load styles */
						designEditor.showElementProperties(elementNode);

					/* Reset regular element/element for layout buttons */
						if ( specialElementType == 'layout' ) {

							$('div.design-editor-info span.customize-for-regular-element').show();
							$('div.design-editor-info span.customize-element-for-layout').hide();

						} else {

							$('div.design-editor-info span.customize-for-regular-element').hide();
							$('div.design-editor-info span.customize-element-for-layout').hide();

						}

					/* Highlight element in iframe */
						inspectorSelectElement(elementSelector);

					/* Args for DE info and inputs */
						var argsForConditionals = {};
						var argsForInputs = {};

						argsForConditionals[specialElementType] = specialElementName;
						argsForInputs[specialElementType] = specialElementID;

					/* Update DE info */
						setSelectedElement({
							id: elementID,
							selector: elementSelector,
							name: elementObject.name,
							specialElementType: specialElementType,
							specialElementID: specialElementID,
							specialElementName: specialElementName,
							object: elementObject
						});

					/* LOAD INPUTS */
						if ( typeof loadInputs == 'undefined' || loadInputs ) {

							designEditorShowCog();

							$.when(
								designEditor.loadElementInputs(elementID, argsForInputs)
							).then(function() {
								designEditorShowContent(propertyGroup);
							});

						}
					/* END LOAD INPUTS */

					$('ul#design-editor-element-selector').find('.ui-state-active').removeClass('ui-state-active');
					elementNode.addClass('ui-state-active');

				}

			this.getCurrentElement = function() {
			
				return $('ul#design-editor-element-selector li.ui-state-active');
			
			}
		
			this.switchLayout = function() {

				/* Make sure this doesn't fire on initial load */
				if ( typeof Padma.switchedToLayout == 'undefined' || !Padma.switchedToLayout )
					return;
			
				$.when(designEditorRequestElements(true)).then(function() {
					designEditor.setupElementSelector.apply(designEditor);

					$('div.design-editor-options').hide();
				});
			
			}
			
		}
	/* END DESIGN EDITOR TABS */


	/* DESIGN EDITOR CONTAINER */
		toggleDesignEditor = function() {

			if ( $('body').hasClass('side-panel-hidden') )
				return showDesignEditor();

			return hideDesignEditor();

		}
		
		
		hideDesignEditor = function() {
			
			//If the panel is already hidden, don't go through any trouble.
			if ( $('body').hasClass('side-panel-hidden') )
				return false;
										
			$('body').addClass('side-panel-hidden');

			setTimeout(repositionTooltips, 400);

			/* Change arrow to pointing left arrow */
			$('#design-editor-toggle span').text('eee');

			$.cookie('hide-design-editor', true);
			
			return true;
			
		}
		
		
		showDesignEditor = function() {
					
			//If the panel is already visible, don't go through any trouble.
			if ( !$('body').hasClass('side-panel-hidden') )
				return false;
					
			$('body').removeClass('side-panel-hidden');

			setTimeout(repositionTooltips, 400);

			/* Change arrow to pointing right arrow */
			$('#design-editor-toggle span').text('iii');
			
			$.cookie('hide-design-editor', false);
			
			return true;
			
		}

	/* END DESIGN EDITOR CONTAINER */


	/* CONTENT TOGGLING */
		designEditorShowCog = function() {

			$('div#side-panel').addClass('properties-loading');

			$('div.design-editor-info').hide();
			$('div.design-editor-options').hide();

			createCog($('div#side-panel-bottom'), true, true);
			
		}

		designEditorShowContent = function(propertyGroup) {

			/* Show info/options and hide cog/instructions */
			$('div#side-panel-bottom')
				.find('.cog-container').remove();
				
			$('div.design-editor-info').show();
			$('div.design-editor-options').show();

			$('div#side-panel').removeClass('properties-loading');

			/* If propertyGroup is present then automatically open that property group */
				if ( !_.isEmpty(propertyGroup) )
					$('.design-editor-box-' + propertyGroup).find('.design-editor-box-title').trigger('click');
			
			/* Refresh Tooltips */
			setupTooltips();
		
		}
	/* END CONTENT TOGGLING */


	/* DESIGN EDITOR OPTIONS/INPUTS */
		designEditorGetElementObject = function(element, excludeInstances, instanceToKeep) {

			var elementNode = $('ul#design-editor-element-selector').find('#element-' + element);
			var elementGroup = elementNode.data('group');
			var elementParent = elementNode.data('parent');

			if ( typeof excludeInstances == 'undefined' )
				var excludeInstances = true;

			var element = jQuery.extend(true, {}, Padma.elements[element]);

			/* Delete instances if set to do so */
			if ( excludeInstances ) {

				if ( typeof instanceToKeep != 'undefined' && instanceToKeep ) {

					$.each(element.instances, function(instanceID, instanceOptions) {

						if ( instanceID != instanceToKeep )
							delete element.instances[instanceID];

					});

				} else {

					delete element.instances;

				}

			}
			
			return element;

		}

		designEditorGetUnsavedValues = function(element, specialElementType, specialElementMeta) {
			
			if ( typeof specialElementType == 'undefined' )
				var specialElementType = false;
			
			if ( typeof specialElementMeta == 'undefined' )
				var specialElementMeta = false;

			if ( 
				typeof GLOBALunsavedValues == 'undefined' ||
				typeof GLOBALunsavedValues['design-editor'] == 'undefined' || 
				typeof GLOBALunsavedValues['design-editor'][element] == 'undefined'
			)
				return null;
			
			if ( !specialElementType || !specialElementMeta ) {

				if ( typeof GLOBALunsavedValues['design-editor'][element]['properties'] == 'undefined' )
					return null;

				var properties = GLOBALunsavedValues['design-editor'][element]['properties'];

			} else {

				if ( typeof GLOBALunsavedValues['design-editor'][element]['special-element-' + specialElementType] == 'undefined' )
					return null;

				if ( typeof GLOBALunsavedValues['design-editor'][element]['special-element-' + specialElementType][specialElementMeta] == 'undefined' )
					return null;

				var properties = GLOBALunsavedValues['design-editor'][element]['special-element-' + specialElementType][specialElementMeta];

			}			
			return !_.isEmpty(properties) ? properties : null;
			
		}

		designEditorBindPropertyBoxToggle = function() {
			
			$('div.design-editor-options').delegate('span.design-editor-box-title', 'click', function() {

				var box = $(this).parents('div.design-editor-box');

				/* Check if box was already open */
				var boxOpen = box.hasClass('design-editor-box-open');

				/* Close all other boxes */
				$('div.design-editor-options div.design-editor-box-open').removeClass('design-editor-box-open');

				/* If selected box does not have open class, then add it.  Otherwise the above line will close all boxes including it so it will act as a toggle */
					if ( !box.hasClass('design-editor-box-open') && !boxOpen )
						box.addClass('design-editor-box-open');

			});

		}

		designEditorBindPropertyInputs = function() {

			/* Customize Buttons */
			$('.design-editor-options-container').delegate('div.customize-property', 'click', function() {

				var property = $(this).parents('li').first();

				if ( property.hasClass('lockable-property') && property.parents('.box-model-inputs').hasClass('box-model-inputs-locked') )
				    var property = $(this).parents('.box-model-inputs').find('> li.lockable-property');

				property.each(function() {

					$(this).find('.customize-property').fadeOut(150);
			    	$(this).removeClass('uncustomized-property');
			    	$(this).addClass('customized-property-by-user');
			    	$(this).attr('title', 'You have customized this property.');

			    	var hidden = $(this).find('input.property-hidden-input');

			    	/* When clicking on Customize on a property that uses a select, sometimes the first option in the select is what you want.  
			    	This will fill the hidden input with it */
			    	var siblingInput = hidden.parent().find('select, input:not(.property-hidden-input)').first();

			    	if ( !hidden.val() && siblingInput.length )
			    		hidden.val(siblingInput.val());

			    	dataHandleDesignEditorInput({hiddenInput: hidden, value: hidden.val()});

			    });
							
			});
			
			/* Uncustomize Button */
			$('.design-editor-options-container').delegate('span.uncustomize-property', 'click', function() {
				
				if ( !confirm('Are you sure you wish to delete this customization?') )
					return false;

				var property = $(this).parents('li').first();

				

				if ( property.hasClass('lockable-property') && property.parents('.box-model-inputs').hasClass('box-model-inputs-locked') )
				    var property = $(this).parents('.box-model-inputs').find('> li.lockable-property');

				property.each(function() {

					var hidden = $(this).find('input.property-hidden-input');


					if(property.data('propertyId') == 'effect'){
						var selector = hidden.attr('element_selector').replace(/\s/g, "-").replace(/#/g, "");

						console.log(selector);
							
						console.log($('textarea#effect-' + selector));
						if($('textarea' + selector).length>0){
							console.log("remove textarea");
						}

						console.log($i('style#effect-' + selector));
						if($i('style' + selector).length>0){
							console.log("remove textarea");
						}
							
					}

			    	$(this).find('div.customize-property').fadeIn(150);
			
					dataHandleDesignEditorInput({hiddenInput: hidden, value: 'DELETE', unit: ''});

					$(this).addClass('uncustomized-property', 150);
					$(this).removeClass('customized-property-by-user');
					$(this).attr('title', 'You have set this property to inherit.');
					
			    });
											
			});

			/* Fonts */
			$('.design-editor-options-container').delegate('.design-editor-property-font-family span.open-font-browser', 'click', function() {
				/* Using anonymous function because fontBrowserOpen won't be defined yet since it's loaded via $.getScript() */
				if ( typeof fontBrowserOpen == 'function' )
					fontBrowserOpen.apply(this);
			});

			/* Lock Sides */
			$('.design-editor-options-container').delegate('span.design-editor-lock-sides', 'click', function() {

			    if ( $(this).parent().hasClass('box-model-inputs-locked') ) {

			        $(this)
			    		.attr('data-locked', false)
			    		.attr('title', 'Unlock sides')
			    		.parent().removeClass('box-model-inputs-locked');

			    } else {

			        $(this)
			    		.attr('data-locked', true)
			    		.attr('title', 'Lock sides')
			    		.parent().addClass('box-model-inputs-locked');

			    }

			});

			$('.design-editor-options-container').delegate('.box-model-inputs-locked li.lockable-property input[type="number"]', 'keyup blur change', function(event, preventRecurse) {

				if ( typeof preventRecurse != 'undefined' && preventRecurse )
					return;

		    	$(this).parents('.box-model-inputs-locked').find('.lockable-property')
		    		.removeClass('uncustomized-property');

			    $(this).parents('.box-model-inputs-locked').find('li.lockable-property input[type="number"]')
					.not($(this))
			    	.val($(this).val())
			    	.trigger('change', [true]);

			});

			$('.design-editor-options-container').delegate('.box-model-inputs-locked li.lockable-property select', 'change', function (event, preventRecurse) {

				if ( typeof preventRecurse != 'undefined' && preventRecurse )
					return;

				$(this).parents('.box-model-inputs-locked').find('.lockable-property')
					.removeClass('uncustomized-property');

				$(this).parents('.box-model-inputs-locked').find('li.lockable-property select')
					.not($(this))
					.val($(this).val())
					.trigger('change', [true]);

			});
			
			/* Select */
			$('.design-editor-options-container').delegate('div.property-select select', 'change', designEditorInputSelect);
			
			/* Integer */
			$('.design-editor-options-container').delegate('div.property-integer input', 'focus', designEditorInputIntegerFocus);
			
			$('.design-editor-options-container').delegate('div.property-integer input', 'keyup blur change', designEditorInputIntegerChange);
			$('.design-editor-options-container').delegate('div.property-integer .property-unit-select select', 'change', designEditorInputIntegerUnitChange);

			/* Image Uploaders */
			$('.design-editor-options-container').delegate('div.property-image span.button', 'click', designEditorInputImageUpload);

			$('.design-editor-options-container').delegate('div.property-image span.delete-image', 'click', designEditorInputImageUploadDelete);

			/* Color Inputs */
			$('.design-editor-options-container').delegate('div.property-color div.colorpicker-box', 'click', designEditorInputColor);

		}
	/* END DESIGN EDITOR INPUTS */


	/* INPUT FUNCTIONALITY */
		/* Select */
		designEditorInputSelect = function(event) {
			
			var hidden = $(this).parent().siblings('input.property-hidden-input');
			
			dataHandleDesignEditorInput({hiddenInput: hidden, value: $(this).val()});
			
		}


		/* Integer */
		designEditorInputIntegerFocus = function(event) {

			if ( typeof originalValues !== 'undefined' ) {
				delete originalValues;
			}
			
			originalValues = new Object;
			
			var hidden = $(this).siblings('input.property-hidden-input');
			var id = hidden.attr('selector') + '-' + hidden.attr('property');
			
			originalValues[id] = $(this).val();
			
		}

		designEditorInputIntegerUnitChange = function (event) {

			$(this).parents('.property-integer').find('input[type="number"]').trigger('change');

		}
		
		designEditorInputIntegerChange = function(event) {

			var hidden = $(this).siblings('input.property-hidden-input');
			var value = $(this).val();

			if ( event.type == 'keyup' && value == '-' )
				return;
			
			/* Validate the value and make sure it's a number */
			if ( isNaN(value) ) {
				
				/* Take the nasties out to make sure it's a number */
				value = value.replace(/[^0-9]*/ig, '');
				
				/* If the value is an empty string, then revert back to the original value */
				if ( value === '' ) {
					
					var id = hidden.attr('selector') + '-' + hidden.attr('property');
					var value = originalValues[id];
											
				}
				
				/* Set the value of the input to the sanitized value */
				$(this).val(value);
				
			}
			
			/* Remove leading zeroes */
			if ( value.length > 1 && value[0] == 0 ) {
				
				value = value.replace(/^[0]+/g, '');
				
				/* Set the value of the input to the sanitized value */
				$(this).val(value);
				
			}

			dataHandleDesignEditorInput({hiddenInput: hidden, value: $(this).val()});
			
		}

		/* Image Uploaders */
		designEditorInputImageUpload = function(event) {
			
			var self = this;
			
			openImageUploader(function(url, filename) {
				
				var hidden = $(self).siblings('input');

				$(self).siblings('.image-input-controls-container').find('span.src').text(filename);
				$(self).siblings('.image-input-controls-container').show();

				dataHandleDesignEditorInput({hiddenInput: hidden, value: url});
				
			});
			
		}
		
		designEditorInputImageUploadDelete = function(event) {
			
			if ( !confirm('Are you sure you wish to remove this image?') ) {
				return false;
			}

			$(this).parent('.image-input-controls-container').hide();
			$(this).hide();
			
			var hidden = $(this).parent().siblings('input');

			dataHandleDesignEditorInput({hiddenInput: hidden, value: 'none'});
			
		}
		
		/* Color Inputs */
		designEditorInputColor = function(event) {
			
			/* Keep the design editor options container from scrolling */
			$('div.design-editor-options-container').css('overflow-y', 'hidden');

			/* Set up variables */
			var input = $(this).parent().siblings('input');
			var inputVal = input.val();

			if ( inputVal == 'transparent' )
				inputVal = '00FFFFFF';

			var colorpickerHandleVal = function(color, inst) {

				var colorValue = '#' + color.hex;

				/* If alpha ISN'T 100% then use RGBa */
				if ( color.a != 100 )
					var colorValue = color.rgba;

				dataHandleDesignEditorInput({hiddenInput: input, value: colorValue});			

			}

			$(this).colorpicker({
				realtime: true,
				alpha: true,
				alphaHex: true,
				allowNull: false,
				showAnim: false,
				swatches: (typeof Padma.colorpickerSwatches == 'object' && Padma.colorpickerSwatches.length) ? Padma.colorpickerSwatches : true,
				color: inputVal,
				beforeShow: function(input, inst) {

					/* Add iframe overlay */
					showIframeOverlay();

				},
				onClose: function(color, inst) {

					colorpickerHandleVal(color, inst);

					/* Hide iframe overlay */
					hideIframeOverlay();

					/* Allow design editor options container to scroll again */
					$('div.design-editor-options-container').css('overflow-y', 'auto');

				},
				onSelect: function(color, inst) {

					colorpickerHandleVal(color, inst);

				},
				onAddSwatch: function(color, swatches) {

					dataSetOption('general', 'colorpicker-swatches', swatches);

				},
				onDeleteSwatch: function(color, swatches) {

					dataSetOption('general', 'colorpicker-swatches', swatches);

				}
			});

			$.colorpicker._showColorpicker($(this));

			setupTooltips();
			
		}
	/* END INPUT FUNCTIONALITY */


	/* COMPLEX JS CALLBACKS */
		propertyInputCallbackFontFamily = function(params) {

			var selector = params.selector;
			var value = params.value;
			var element = params.element;
			var cssValue = params.stack ? params.stack : params.value;

			/* Uncustomization */
				if ( !value ) {

					stylesheet.delete_rule_property(selector, 'font-family');
					return;

				}

			/* Non web fonts */
				if ( !value.match(/\|/g) ) {

					stylesheet.update_rule(selector, {"font-family": cssValue});
					return;

				}

			/* Web Fonts */
				var fontFragments = value.split('|');
				var args = {};

				/* Handle variants */
				var variants = '';

				if ( typeof fontFragments[2] != 'undefined' && fontFragments[2] )
					variants = ':' + fontFragments[2];

				args[fontFragments[0]] = {
					families: [fontFragments[1] + variants]
				};

				var cssValue = fontFragments[1];

				stylesheet.update_rule(selector, {"font-family": cssValue});

				if ( typeof $('iframe#content').get(0).contentWindow.WebFont == 'object' )
					$('iframe#content').get(0).contentWindow.WebFont.load(args);
			/* End Web Font handling */
			
		}

		propertyInputCallbackBackgroundImage = function(params) {

			var selector = params.selector;
			var value = params.value;
			var element = params.element;
			
			if ( value != 'none' ) {
				stylesheet.update_rule(selector, {"background-image": 'url(' + value + ')'});
			} else if ( value == 'none' ) {
				stylesheet.update_rule(selector, {"background-image": 'none'});
			}
			
		}


		propertyInputCallbackAnimation = function(params) {

			var animation 	= params.value;
			var selector 	= params.selector;

			stylesheet.update_rule(selector, {
					'animation-name': animation,
					'animation-duration': '1s',
					'animation-fill-mode': 'both'
				});

		}


		propertyInputCallbackTransform = function(params) {

			

			var angle 		= 45;
			var keyAngle 	= 'propertyInputCallbackTransformAngle-' + params.selector.replace(' ','-');
			var keyType 	= 'propertyInputCallbackTransformType-' + params.selector.replace(' ','-');

			if(localStorage[keyAngle] !== undefined && localStorage[keyAngle] !== 'null'){
				angle = localStorage[keyAngle];
			}

			
			stylesheet.update_rule(params.selector, {
				'transform': params.value + '('+angle+'deg)'
			});

			localStorage[keyType] = params.value;
			
		}

		propertyInputCallbackTransformAngle = function(params) {

			var keyAngle 	= 'propertyInputCallbackTransformAngle-' + params.selector.replace(' ','-');
			var keyType 	= 'propertyInputCallbackTransformType-' + params.selector.replace(' ','-');
			localStorage[keyAngle] = params.value;

			if(localStorage[keyType] !== undefined && localStorage[keyType] !== 'null'){
				
				var unit = 'deg';

				if(localStorage[keyType] == 'scale' || localStorage[keyType] == 'scaleX' || localStorage[keyType] == 'scaleY'){
					unit = '';
				}else if(localStorage[keyType] == 'translate' || localStorage[keyType] == 'translateX' || localStorage[keyType] == 'translateY'){
					unit = 'px';
				}

				stylesheet.update_rule(params.selector, {
					'transform': localStorage[keyType] + '('+params.value+unit+')'
				});
			}

		}

		propertyInputCallbackEffects = function(params) {

			console.log(params);
			
			var selector 	= params.selector.replace(/\s/g, "-").replace(/#/g, ""); 

			var tagName 	= params.element[0].tagName;

			return $.post(Padma.ajaxURL, {
						security: Padma.security,
						action: 'padma_visual_editor',
						method: 'get_effect_content',
						effect: params.value,
						selector: selector,
						tagName: tagName,

					}, function(response) {

						$('body').append('<textarea id="effect-'+selector+'" name="effect-'+selector+'" data-group="general" style="display:none;">'+response+'</textarea>');
						$i('head').append('<style id="style#effect-'+selector + '>' + response + '</style>');
							
					});

		}


		propertyInputCallbackFontStyling = function(params) {

			var selector = params.selector;
			var value 	= params.value;
			var element = params.element;
			
			if ( value === 'normal' ) {
				
				stylesheet.update_rule(selector, {
					'font-style': 'normal',
					'font-weight': 'normal'
				});
				
			} else if ( value === 'bold' ) {
				
				stylesheet.update_rule(selector, {
					'font-style': 'normal',
					'font-weight': 'bold'
				});

			} else if ( value === 'light' ) {
				
				stylesheet.update_rule(selector, {
					'font-style': 'normal',
					'font-weight': 'lighter'
				});
				
			} else if ( value === 'italic' ) {
				
				stylesheet.update_rule(selector, {
					'font-style': 'italic',
					'font-weight': 'normal'
				});
				
			} else if ( value === 'bold-italic' ) {
				
				stylesheet.update_rule(selector, {
					'font-style': 'italic',
					'font-weight': 'bold'
				});
				
			} else if ( value === null ) {

				stylesheet.delete_rule_property(selector, 'font-style');
				stylesheet.delete_rule_property(selector, 'font-weight');

			}
			
		}

		propertyInputCallbackCapitalization = function(params) {

			var selector = params.selector;
			var value = params.value;
			var element = params.element;
			
			if ( value === 'none' || value == null ) {
				
				stylesheet.update_rule(selector, {
					'text-transform': 'none',
					'font-variant': 'normal'
				});
				
			} else if ( value === 'small-caps' ) {
				
				stylesheet.update_rule(selector, {
					'text-transform': 'none',
					'font-variant': 'small-caps'
				});
				
			} else {
				
				stylesheet.update_rule(selector, {
					'text-transform': value,
					'font-variant': 'normal'
				});
				
			}
			
		}

		propertyInputCallbackShadow = function(params) {
		
			var selector = params.selector;
			var value = params.value;
			var element = params.element;
			var property = params.property;

			var shadowType = ( property.indexOf('box-shadow') === 0 ) ? 'box-shadow' : 'text-shadow';
												
			var currentShadow = $i(selector).css(shadowType) || false;
									
			//If the current shadow isn't set, then create an empty template to work off of.
			if ( currentShadow == false || currentShadow == 'none' )
				currentShadow = 'rgba(0, 0, 0, 0) 0 0 0';
			
			//Remove all spaces inside rgba, rgb, and hsb colors and also remove all px
			var shadowFragments = currentShadow.replace(/, /g, ',').replace(/px/g, '').split(' ');
			
			var shadowColor = $('li[data-property-id="' + shadowType + '-color' + '"] input').val() || shadowFragments[0];
			var shadowHOffset = $('li[data-property-id="' + shadowType + '-horizontal-offset' + '"] input').val() || shadowFragments[1];
			var shadowVOffset = $('li[data-property-id="' + shadowType + '-vertical-offset' + '"] input').val() || shadowFragments[2];
			var shadowBlur = $('li[data-property-id="' + shadowType + '-blur' + '"] input').val() || shadowFragments[3];
			var shadowInset = $('li[data-property-id="' + shadowType + '-position' + '"] input').val() || shadowFragments[4];
			
			switch ( property ) {
				
				case shadowType + '-horizontal-offset':
					shadowHOffset = value || 0;
				break;
				
				case shadowType + '-vertical-offset':
					shadowVOffset = value || 0;
				break;
				
				case shadowType + '-blur':
					shadowBlur = value || 0;
				break;
				
				case shadowType + '-inset':
					shadowInset = value;
				break;
				
				case shadowType + '-color':
					shadowColor = value;
				break;
				
			}

			if ( !shadowColor )
				return stylesheet.delete_rule_property(selector, shadowType);
			
			/* Handle inset */
			if ( shadowInset == 'inset' ) {
				shadowInset = ' inset';
			} else {
				shadowInset = '';
			}

			var shadow = shadowColor + ' ' + shadowHOffset + 'px ' + shadowVOffset + 'px ' + shadowBlur + 'px' + shadowInset;

			var properties = {};
			
			//Use this syntax so the shadow type can feed from variable.
			properties[shadowType] = shadow;

			stylesheet.update_rule(selector, properties);

			updateInspectorVisibleBoxModal();
			
		}
	/* END COMPLEX JS CALLBACKS */


	/* INSPECTOR */
		/* INSPECTOR INIT */
			addInspector = function(refresh) {

				if ( typeof Padma.elements == 'undefined' )
					return $.when(designEditorRequestElements()).then(addInspector);

				$.each(Padma.elements, function(elementID, elementSettings) {

					if ( !elementSettings['inspectable'] )
						return;

					addInspectorProcessElement(elementSettings);

				});

				/* Build element hover tooltip */
				if ( typeof refresh == 'undefined' || refresh !== true ) {

					$i('body').qtip({
						id: '',
						style: {
							classes: 'qtip-padma qtip-inspector-tooltip'
						},
						position: {
							target: [-9999, -9999],
							my: 'bottom left',
							at: 'top right',
							container: $i('body'),
							viewport: $i('#padma-tooltip-container'),
							effect: false,
							adjust: {
								x: 0,
								y: 0,
								method: 'flipinvert'
							}
						},
						content: {
							text: 'Hover over an element.'
						},
						show: {
							event: false,
							ready: true
						},
						hide: false,
						events: {
							render: function(event, api) {
								
								delete inspectorElement;
								delete inspectorTooltip;
								delete inspectorElementOptions;

								inspectorTooltip = api;

								if ( !$('#toggle-inspector').hasClass('inspector-disabled') ) {
									enableInspector();
								} else {
									disableInspector();
								}

							}
						}
					});

				}

			}

				refreshInspector = function() {
					return addInspector(true);
				}

				addInspectorProcessElement = function(value) {

					if ( value['group'] == 'default-elements' )
						return;

					if ( !$i(value['selector']).length )
						return;


					if ( value['selector'].indexOf(':') == -1 ) {

						$i(value['selector']).data({
							inspectorElementOptions: value
						})

						$i(value['selector']).addClass('inspector-element');

					}

					/* Instances */
					$.each(value['instances'], function(instanceID, instanceValue) {

						/* Do not add elements with pseudo selectors to the inspector */
						if ( instanceValue['selector'].indexOf(':') != -1 )
							return;

						if ( !$i(instanceValue['selector']).length )
							return;

						/* Simply change selector, add ID and name for instances */
						var instanceOptions = jQuery.extend(true, {}, value);
						instanceOptions['parentName'] = value['name'];
						instanceOptions['instance'] = instanceValue['id'];
						instanceOptions['name'] = instanceValue['name'];
						instanceOptions['selector'] = instanceValue['selector'];
						instanceOptions['instances'] = {};

						/* Filter instances to only be states of this instance */
						$.each(value['instances'], function(index, instance) {

							if ( instance['state-of'] == instanceID )
								instanceOptions['instances'][index] = instance;

						});

						/* Split the selector that way we can filter out :hover and :active */
						$.each(instanceOptions['selector'].split(','), function(index, selector) {

							/* Do not add elements with pseudo selectors to the inspector */
							if ( selector.indexOf(':') != -1 )
								return;

							$i(selector).data({
								inspectorElementOptions: instanceOptions
							});

							$i(selector).addClass('inspector-element');

						});

					});

				}

			enableInspector = function() {

				if ( Padma.mode != 'design' || !Padma.designEditorSupport )
					return false;

				Padma.inspectorDisabled = false;
				Padma.disableBlockDimensions = true;

				$i('body').addClass('disable-block-hover').removeClass('inspector-disabled');

				$i('.block[data-hasqtip]').each(function() {
					var api = $(this).qtip('api');
					api.destroy();
				});

				inspectorTooltip.show();

				var inspectorMouseMoveEvent = !Padma.touch ? 'mousemove' : 'tap';
				$i('html').bind(inspectorMouseMoveEvent, inspectorMouseMove);

				setupInspectorContextMenu();

				deactivateContextMenu('block');
				deactivateContextMenu('wrapper');

				/* For some reason the iframe doesn't always focus correctly so both of these bindings are needed */
				Padma.iframe.contents().find('body').bind('keydown', inspectorNudging);
				Padma.iframe.bind('keydown', inspectorNudging);

				/* Focus iframe on mouseover */
				Padma.iframe.bind('mouseover', function() {
					Padma.iframe.focus();
				});

				showNotification({
					id: 'inspector',
					message: '<strong>Right-click</strong> highlighted elements to style them.<br /><br />Once an element is selected, you may nudge it using your arrow keys.<br /><br />The faded orange and purple are the margins and padding.  These colors are only visible when the inspector is active.',
					closeConfirmMessage: 'Please be sure you understand how the Design Editor inspector works before hiding this message.',
					closeTimer: false,
					closable: true,
					doNotShowAgain: true
				});

				updateInspectorVisibleBoxModal();

				$('#toggle-inspector').removeClass('inspector-disabled');

			}

			disableInspector = function() {

				if ( Padma.mode != 'design' || !Padma.designEditorSupport )
					return false;

				Padma.inspectorDisabled = true;

				delete Padma.disableBlockDimensions;
				delete inspectorElement;

				$i('.inspector-element-hover').removeClass('inspector-element-hover');
				$i('body').removeClass('disable-block-hover').addClass('inspector-disabled'); 
				$i('.block').qtip('enable');

				$(inspectorTooltip.elements.tooltip).hide();
				hideNotification('inspector');

				$i('html').unbind('mousemove', inspectorMouseMove);

				deactivateContextMenu('inspector');

				setupBlockContextMenu();
				setupWrapperContextMenu();

				Padma.iframe.contents().find('body').unbind('keydown', inspectorNudging);
				Padma.iframe.unbind('keydown', inspectorNudging);

				removeInspectorVisibleBoxModal();

				$('#toggle-inspector').addClass('inspector-disabled');

			}

			toggleInspector = function() {

				if ( Padma.mode != 'design' || !Padma.designEditorSupport )
					return false;

				if ( $('#toggle-inspector').hasClass('inspector-disabled') )
					return enableInspector();

				disableInspector();

			}
		/* END INSPECTOR INIT */
		
		/* INSPECTOR ELEMENT HIGHLIGHTING */
			inspectorSelectElement = function(selector) {

				/* Unhighlight previous elements */
				$i('.inspector-element-selected').each(function() {

					$(this).removeClass('inspector-element-selected');

					removeInspectorVisibleBoxModal($(this));

				});

				/* Mark the new selected elements */
				$i(selector).addClass('inspector-element-selected');

				updateInspectorVisibleBoxModal();
				
			}
		/* END INSPECTOR ELEMENT HIGHLIGHTING */

		/* INSPECTOR BOX MODAL HIGHLIGHTING */
			removeInspectorVisibleBoxModal = function(selector) {

				if ( typeof selector == 'undefined' )
					var selector = $i('.inspector-element-selected');

				if ( !$(selector).data('previousBoxShadow') )
					return false;

				$(selector).data('previousBoxShadow', null);

				/* Clear style attribute box shadow and rely on previous CSS */
				return $(selector).css('boxShadow', '');

			}

			updateInspectorVisibleBoxModal = function() {

				if ( typeof Padma.inspectorDisabled != 'undefined' && Padma.inspectorDisabled )
					return;

				/* Show padding/margin with box shadow */
				$i('.inspector-element-selected').each(function() {

					/* Remove any previous margin/padding shadows */
					removeInspectorVisibleBoxModal($(this));

					var self = this;
					var previousBoxShadow = $(this).css('box-shadow');
					var boxShadow = previousBoxShadow != 'none' ? previousBoxShadow.split(',') : [];

					$(this).data('previousBoxShadow', previousBoxShadow);

					$.each([
						'paddingTop',
						'paddingRight',
						'paddingBottom',
						'paddingLeft',
						'marginTop',
						'marginRight',
						'marginBottom',
						'marginLeft'
					], function(index, cssProperty) {

						var cssValueUnit = $(self).css(cssProperty).replace(/^[+-]?\d+(\.\d+)?/g, '');
						var cssValue = $(self).css(cssProperty).replace(cssValueUnit, '');

						if ( cssValue == 'auto' )
							return;

						var color = cssProperty.indexOf('padding') !== -1 ? 'rgba(0, 0, 255, .15)' : 'rgba(255, 127, 0, .15)';
						var negative = '';
						var inset = '';

						if ( 
							cssProperty == 'paddingRight' ||
							cssProperty == 'paddingBottom' ||
							cssProperty == 'marginLeft' ||
							cssProperty == 'marginTop'
						) 
							negative = '-';

						var value = negative + cssValue + cssValueUnit;

						if ( cssProperty.toLowerCase().indexOf('left') !== -1 || cssProperty.toLowerCase().indexOf('right') !== -1 )
							var xyValue = value + ' 0';
						else 
							var xyValue = '0 ' + value;

						if ( cssProperty.indexOf('padding') !== -1 )
							inset = 'inset ';

						boxShadow.push(inset + xyValue + ' 0 0 ' + color);

					});

					$(this).css({
						boxShadow: boxShadow.join(',')
					});

				});

			}
		/* END INSPECTOR BOX MODAL HIGHLIGHTING */

		/* INSPECTOR TOOLTIP */
			inspectorMouseMove = function(event) {

				if ( Padma.inspectorDisabled )
					return;

				var targetInspectorElement = $(event.target);

				if ( !targetInspectorElement.hasClass('inspector-element') )
					targetInspectorElement = targetInspectorElement.parents('.inspector-element').first();

				/* Only change tooltip content if the hovered element isn't the existing inspector element */
				if ( typeof inspectorElement == 'undefined' || !targetInspectorElement.is(inspectorElement) ) {

					inspectorElement = $(event.target);

					if ( !inspectorElement.hasClass('inspector-element') )
						inspectorElement = inspectorElement.parents('.inspector-element').first();

					var inspectorElementOptions = inspectorElement.data('inspectorElementOptions');

					if ( typeof inspectorElementOptions == 'object' ) {

						$i('.inspector-element-hover').removeClass('inspector-element-hover');
						$i(inspectorElementOptions['selector']).addClass('inspector-element-hover');

						/* Build tooltip text */
							var elementSelectorNode = $('#design-editor-element-selector').find('li#element-' + inspectorElementOptions.id);

						var elementName = elementSelectorNode.children('.element-name').text();

						var tooltipText = '<span class="inspector-tooltip-element-path">';
						var tooltipElementPath = [];

						elementSelectorNode.parents('li').reverse().each(function() {

							tooltipElementPath.push($(this).children('.element-group-name, .element-name').first().text());

						});

						/* Add info to tooltip if hovered element is an instance */
							var insideInstanceText = '';

							if ( typeof inspectorElementOptions.instance != 'undefined' ) {

								if ( inspectorElementOptions.name.indexOf(' &ndash; ') !== -1 ) {

									insideInstanceText = '<span class="inspector-tooltip-instance">Inside <strong>' + inspectorElementOptions.name.split(' &ndash; ')[0] + '</strong></span>';

								} else {

									elementName = inspectorElementOptions.name;

								}

							}

						/* Trim down the path and omit ancestors to save space if necessary */
						if ( tooltipElementPath.join(' > ').length + elementName.length > 40 ) {

							while ( tooltipElementPath.join(' > ').length + elementName.length > 40 && elementName.length < 40 ) {

								tooltipElementPath.shift();

								tooltipElementPathStr = '<span class="ellipsis">...</span> ' + tooltipElementPath.join(' &rsaquo; ');

							}

						} else {

							tooltipElementPathStr = tooltipElementPath.join(' &rsaquo; ');

						}

						tooltipText += tooltipElementPathStr;
						tooltipText += ' &rsaquo; <strong>' + elementName + '</strong></span>';
						tooltipText += insideInstanceText;

						tooltipText += '<small class="right-click-message">Right-click to style</small>';


						inspectorTooltip.set('content.text', tooltipText);

					}

				}

				inspectorTooltip.show();
				inspectorTooltip.set('position.target', 'mouse');

			}
		/* END INSPECTOR TOOLTIP */

		/* INSPECTOR CONTEXT MENU */
			setupInspectorContextMenu = function() {

				
				return setupContextMenu({					
					id: 'inspector',
					elements: 'body',
					title: function(event) {
						return inspectorElement.data('inspectorElementOptions').name;
					},
					onShow: inspectorContextMenuOnShow,
					onHide: function() {

						/* Reactivate inspector tooltip */
						inspectorTooltip.show();
						Padma.inspectorDisabled = false;

					},
					onItemClick: inspectorContextMenuItemClick,
					contentsCallback: inspectorContextMenuContents
				});

			}

			inspectorContextMenuOnShow = function(event) {

				/* Add element options object to the context menu */
					$(this).data('element-options', inspectorElement.data('inspectorElementOptions'));

				/* Disable inspector tooltip */
					$(inspectorTooltip.elements.tooltip).hide();
					Padma.inspectorDisabled = true;

			}

			inspectorContextMenuItemClick = function(contextMenu, originalRightClickEvent) {

				if ( $(this).hasClass('group-title') && !$(this).hasClass('group-title-clickable') )
					return;

				/* Block Options Click */
				if ( $(this).parents('li').first().hasClass('inspector-context-menu-block-options') ) {

					openBlockOptions(getBlock($(inspectorElement)));

				/* Edit content */
				} else if ( $(this).parents('li').first().hasClass('inspector-context-menu-edit-content') ){

					var blockID 	= getBlock($(inspectorElement))[0].dataset.id;
					var blockType 	= getBlock($(inspectorElement))[0].dataset.type;


					if(blockType == 'content'){
						
						var elementId = $(inspectorElement).closest('article').attr('id');
						if(elementId !== undefined){
							postId = elementId.split('-')[1];
							localStorage['visual-editor-block-post-data-' + blockID + '-0'] = postId;							
						}

						contentEditor.showEditor('content-editor', blockID, function(editor) {
							refreshInspector();
						});
			

					}else{

						showNotification({
							id: 'no-supported',
							message: 'Content editor currently supports post and pages only.',
							closeTimer: 3000
						});
						
					}

				} else {


					var inspectorElementOptions = contextMenu.data('element-options');
					var instanceID 				= $(this).parents('li').first().data('instance-id');
					var stateID 				= $(this).parents('li').first().data('state-id');

					/* Reactivate inspector tooltip */
					inspectorTooltip.show();
					Padma.inspectorDisabled = false;

					/* Remove the highlight on the previously selected elements */
					$('#design-editor-element-selector-container .ui-state-active').removeClass('ui-state-active');

					/* Instances */
					if ( typeof instanceID != 'undefined' ) {

						designEditor.selectSpecialElement(inspectorElementOptions['id'], 'instance', instanceID);

					}

					/* States */
					else if ( typeof stateID != 'undefined' ) {

						designEditor.selectSpecialElement(inspectorElementOptions['id'], 'state', stateID);

					}

					/* Handle Top Level Elements */
					else if ( !$(this).parents('li').first().hasClass('inspector-context-menu-parent') ) {

						$('ul#design-editor-element-selector li#element-' + inspectorElementOptions['id']).find('> span').trigger('click');

					}

					/* Layout-specific customizations */
					if ( $(this).parents('li').first().hasClass('inspector-context-menu-edit-for-layout') ) {

						designEditor.selectSpecialElement(inspectorElementOptions['id'], 'layout', Padma.viewModels.layoutSelector.currentLayout());

					}

				}

			}

			inspectorContextMenuContents = function(event) {

				var contextMenu = $(this);
				var inspectorElementOptions = contextMenu.data('element-options');

				/* Set instance variable */
				var isInstance = (typeof inspectorElementOptions.instance != 'undefined' && inspectorElementOptions.instance);

				/* Add options to context menu */
					/* Regular Element Group */
						var regularElementGroup = contextMenu;

						if ( isInstance ) {

							// Edit This Instance
							contextMenu.append('<li class="inspector-context-menu-edit-instance" data-instance-id="' + inspectorElementOptions.instance + '"><span>Edit This Instance</span></li>');

							// Transform options
							//contextMenu.append('<li class="inspector-context-menu-edit-transform"><span class="group-title group-title-clickable">Transform</span><ul><li class="transform-rotate"><span>Rotate</span></li></ul></li>');

							/* 
								Edit content option
							*/
							contextMenu.append('<li class="inspector-context-menu-edit-content" data-instance-id="' + inspectorElementOptions.instance + '"><span>Edit Content</span></li>');
							

							var regularElementGroup = $('<li class="inspector-context-menu-edit-normal"><span class="group-title group-title-clickable">Edit Regular Element<small>' + inspectorElementOptions.parentName + '</small></span><ul></ul></li>').appendTo(contextMenu);
							regularElementGroup = regularElementGroup.find('ul').first();

						/* Regular Element */
						} else {

							regularElementGroup.append('<li class="inspector-context-menu-edit-normal"><span>Edit</span></li>');

						}

							regularElementGroup.append('<li class="inspector-context-menu-edit-for-layout"><span>Edit For This Layout</span></li>');

						/* Regular Element States */
							if ( !_.isEmpty(inspectorElementOptions.states) ) {

								var statesMenu = $('<li class="inspector-context-menu-states"><span class="group-title">States</span><ul></ul></li>').appendTo(regularElementGroup);

								$.each(inspectorElementOptions.states, function(stateID, stateInfo) {
									statesMenu.find('ul').append('<li data-state-id="' + stateID + '"><span>Edit ' + stateInfo.name + '</span></li>');
								});

							}
					/* End Regular Element */

					/* Instances */
						if ( !_.isEmpty(inspectorElementOptions.instances) ) {

							if ( typeof inspectorElementOptions.instance == 'undefined' || !inspectorElementOptions.instance ) {
								var instancesMenu = $('<li class="inspector-context-menu-instances"><span class="group-title">Instances</span><ul></ul></li>').appendTo(contextMenu);
							} else {
								var instancesMenu = false;
							}

							$.each(inspectorElementOptions.instances, function(instanceID, instance) {

								/* Handle instance states that will be in the actual instances menu */
									if ( instance['state-of'] == inspectorElementOptions.instance ) {

										if ( !contextMenu.find('> li.inspector-context-menu-instance-states').length )
											$('<li class="inspector-context-menu-instance-states"><span class="group-title">Instance States</span><ul></ul></li>')
												.insertAfter(contextMenu.find('li.inspector-context-menu-edit-instance'));

										contextMenu.find('> li.inspector-context-menu-instance-states ul').append('<li data-instance-id="' + instanceID + '"><span>Edit ' + instance['state-name'] + '</span></li>');

									}

							});

							/* If the instances menu is empty somehow (one instance and that instance is selected), then delete it */
							if ( instancesMenu && !instancesMenu.find('ul li').length )
								instancesMenu.remove();

						}

					/* Parent Elements */
						if ( inspectorElement.parents('.inspector-element').length ) {

							var parentsMenu = $('<li class="inspector-context-menu-parents"><span class="group-title">Parents</span><ul></ul></li>').appendTo(contextMenu);

							inspectorElement.parents('.inspector-element').each(function() {

								var parentElement = $(this);

								$('<li class="inspector-context-menu-parent" data-parent-id="' + $(this).data('inspectorElementOptions').id + '"><span>' + $(this).data('inspectorElementOptions').name + '</span></li>')
									.appendTo(parentsMenu.find('ul'))
									.bind('click', function() {

										inspectorElement = parentElement;

										var x = typeof event.data != 'undefined' ? event.data.x : event.originalEvent.clientX;
										var y = typeof event.data != 'undefined' ? event.data.y : event.originalEvent.clientY;

										parentElement.trigger('contextmenu', {
											x: x,
											y: y
										});

									});

							});

						}

					/* Block Options */
						if ( getBlock(inspectorElement) ) {

							var block = getBlock(inspectorElement);
							var blockID = getBlockID(block);
							var blockType = getBlockTypeNice(getBlockType(block));

							var blockOptionsNode = $('<li class="inspector-context-menu-block-options"><span>Open Block Options</span></li>').appendTo(contextMenu);

						}
					/* End block options */

			}
		/* END INSPECTOR CONTEXT MENU */

		/* INSPECTOR NUDGING */	
			inspectorNudging = function(event) {

				var key = event.keyCode;

				if ( key < 37 || key > 40 || !$i('.inspector-element-selected').length || $i('.inspector-element-selected').is('body') )
					return;

				var interval = event.shiftKey ? 5 : 1;

				/* Get the selector that way the stylesheet object can be used */
				var methodInput = $('.design-editor-box-nudging .design-editor-property-position select', '.design-editor-options-container');
				var methodInputHidden = methodInput.parents('.design-editor-property-position').find('input[type="hidden"]');
				
				var selector = methodInputHidden.attr('element_selector');

				/* Set the 3 nudging properties to customized */
				$('.design-editor-box-nudging .uncustomized-property .customize-property span', '.design-editor-options-container').trigger('click');

				/* Set the nudging method to whatever the position property is of the element as long as it's not static */
				if ( $i('.inspector-element-selected').css('position') != 'static' ) {

					var positionMethod = $i('.inspector-element-selected').css('position');

					$i('.inspector-element-selected').css({
						position: positionMethod	
					});

					methodInput.val(positionMethod).trigger('change');

				} else {

					var positionMethod = 'relative';

					$i('.inspector-element-selected').css({
						position: positionMethod	
					});

					methodInput.val(positionMethod).trigger('change');

				}

				switch ( key ) {

					/* Left */
					case 37:

						var previousLeft = parseInt($i('.inspector-element-selected').css('left'));

						if ( isNaN(previousLeft) )
							var previousLeft = 0;

						stylesheet.update_rule(selector, {"left": (previousLeft - interval) + 'px'});

						var currentLeft = $i('.inspector-element-selected').css('left').replace('px', '');
						$('.design-editor-box-nudging .design-editor-property-left input[type="number"]', '.design-editor-options-container').val(currentLeft).trigger('change');

					break;

					/* Up */
					case 38:

						var previousTop = parseInt($i('.inspector-element-selected').css('top'));

						if ( isNaN(previousTop) )
							previousTop = 0;

						stylesheet.update_rule(selector, {"top": (previousTop - interval) + 'px'});

						var currentTop = $i('.inspector-element-selected').css('top').replace('px', '');
						$('.design-editor-box-nudging .design-editor-property-top input[type="number"]', '.design-editor-options-container').val(currentTop).trigger('change');

					break;

					/* Right */
					case 39:

						var previousLeft = parseInt($i('.inspector-element-selected').css('left'));

						if ( isNaN(previousLeft) )
							var previousLeft = 0;

						stylesheet.update_rule(selector, {"left": (previousLeft + interval) + 'px'});

						var currentLeft = $i('.inspector-element-selected').css('left').replace('px', '');
						$('.design-editor-box-nudging .design-editor-property-left input[type="number"]', '.design-editor-options-container').val(currentLeft).trigger('change');

					break;

					/* Down */
					case 40:

						var previousTop = parseInt($i('.inspector-element-selected').css('top'));

						if ( isNaN(previousTop) )
							previousTop = 0;

						stylesheet.update_rule(selector, {"top": (previousTop + interval) + 'px'});

						var currentTop = $i('.inspector-element-selected').css('top').replace('px', '');
						$('.design-editor-box-nudging .design-editor-property-top input[type="number"]', '.design-editor-options-container').val(currentTop).trigger('change');

					break;

				}

				/* Prevent scrolling */
				event.preventDefault();
				return false;
			}

		/* END INSPECTOR NUDGING */

		/*	TRANSFORM CONTEXT MENU		*/
			transformFromContextMenu = function(event){
				event.preventDefault();
			}
		/*	END TRANSFORM CONTEXT MENU	*/


	/* END INSPECTOR */


	/* ELEMENT INFO */
		getElementNodeName = function(node) {

			var clonedNode = node.clone();

			return clonedNode.find('> span').children().remove().end().text();

		}


		getSelectedElement = function() {

			if ( typeof Padma.designEditorCurrentElement != 'undefined' )
				return Padma.designEditorCurrentElement;

			return null;

		}

		setSelectedElement = function(element) {

			Padma.designEditorCurrentElement = element;

			if ( _.isEmpty(element) )
				return;

			var details = {};

			if ( typeof element.specialElementType != 'undefined' )
				details[element.specialElementType] = element.specialElementName;

			setSelectedElementDetails(details, element.object);

		}

		setSelectedElementDetails = function(details, element) {

			var details = $.extend({}, {
				instance: element.name,
				layout: 'all layouts',
				state: 'all states'
			}, details);

			var deSelectionDetails = $('span.design-editor-selection-details');

			deSelectionDetails.find('.design-editor-selected-element').html(details.instance);
			deSelectionDetails.find('strong.design-editor-selection-details-layout').html(details.layout);

			if ( typeof element.states == 'undefined' || _.isEmpty(element.states) ) {

				deSelectionDetails.find('span.design-editor-selection-details-state-container').hide();

			} else {

				deSelectionDetails.find('span.design-editor-selection-details-state-container').show();


				if ( details.state == 'all states' ) {
					deSelectionDetails.find('.design-editor-selection-details-state-before').text('and');
				} else {
					deSelectionDetails.find('.design-editor-selection-details-state-before').text('when');
				}

				if ( details.state == 'Hover' )
					details.state = 'hovered';

				deSelectionDetails.find('strong.design-editor-selection-details-state').html(details.state.toLowerCase());

			}

			/* Update height of DE info */
			var deInfoHeight = $('.design-editor-info').outerHeight();

			$('.design-editor-info').css('marginTop', '-' + deInfoHeight + 'px');
			$('#side-panel-bottom').css('paddingTop', deInfoHeight + 'px');

			return $('span.design-editor-selection-details');

		}

		sanitizeElementName = function(elementName) {

			return $.trim(elementName.escapeHTML());

		}
	/* END ELEMENT INFO */


	var modeDesign = {

		init: function() {

			designEditor = new designEditorTabEditor();
			designEditor._init();

			modeDesign.bind();

			designEditorBindPropertyInputs();

			/* Load scripts */
				try {

					/* Load editor.fonts.js */
					$.getScript(Padma.padmaURL + '/library/visual-editor/' + Padma.scriptFolder + '/util.fonts-browser.js');

					/* Load Google API */
					$.getScript('//ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js');

				} catch (exception) {}

			/* Hide DE if cookie is set to do so */
				if ( $.cookie('hide-design-editor') === 'true' )
					hideDesignEditor();




			/*
				Change side of panel
			*/
			$(document).on('click','#change-side-of-panel',function(){


				$('body').toggleClass('panel-on-left');

				if($('body').hasClass('panel-on-left')){
					$.cookie('panel-on-left', true);
				}else{
					$.cookie('panel-on-left', false);
				}
			});
			if ( $.cookie('panel-on-left') === 'true' ){
				$('body').addClass('panel-on-left');
			}	



			/*
				Options filter Reset function
			*/
			var optionsFilterReset = function(){
				$('.design-editor-options li').show();
				$('#options-filter').val('');
				$('.design-editor-box').show();
				
				$('.design-editor-options li').removeClass('hidden');
				$('.design-editor-box').removeClass('filtered');
				$('.design-editor-box').removeClass('design-editor-box-open');
				$('.design-editor-box-content').removeClass('filtered');
			}

			/*
				Options filter
			*/
			$(document).on('keyup','#options-filter',function(){

				var string  = $(this).val();				
				var options = $('ul.design-editor-box-content > li, div.box-model-inputs li');

				if(string.length == 0){
					optionsFilterReset();
					return;
				}
				
				options.each(function(index){
					
					var property_id = $(this).data('property-id').toString();
					var target 		= $(this);					


					if( property_id == undefined ){
						//console.log(options[index]);

					}else{

						
						if ( property_id.indexOf(string) !== -1) {

							target.parent().addClass('filtered');
							target.closest('.design-editor-box').addClass('filtered');
							target.closest('.design-editor-box').addClass('design-editor-box-open');
							target.removeClass('hidden');
							target.show();

						}else{
							target.addClass('hidden');
							target.hide();
						}
					}

				});

				$('.design-editor-box').each(function(){

					var ttlOptions 		= $(this).find('ul.design-editor-box-content > li, div.box-model-inputs li').length;
					var ttlOptionsHiden = $(this).find('ul.design-editor-box-content > li.hidden, div.box-model-inputs li.hidden').length;
					
					if(ttlOptions == ttlOptionsHiden){						
						$(this).hide();
					}else{
						$(this).show();
					}
				});

			
			});

			/*
				Options filter Reset
			*/
			$(document).on('click','.options-filter-reset',function(){
				optionsFilterReset();
				
			});

			/*
				options-filter-only-modified
			*/
			$(document).on('click','.options-filter-only-modified',function(){

				if($('.options-filter-only-modified input').is(':checked')){

					var targetToHide = $('.design-editor-options li:not(.customized-property-by-user)');					
					targetToHide.each(function(){
						$(this).addClass('hidden');
						$(this).hide();
					});

					var targetToShow = $('.design-editor-options li.customized-property-by-user');					
					targetToShow.each(function(){
						$(this).parent().addClass('filtered');
						$(this).parent().parent().addClass('filtered');
						$(this).parent().parent().addClass('design-editor-box-open');
						$(this).removeClass('hidden');
						$(this).show();
					});

					$('.design-editor-box').each(function(){

						var ttlOptions 		= $(this).find('.design-editor-box-content > li').length;
						var ttlOptionsHiden = $(this).find('.design-editor-box-content > li.hidden').length;
						
						if(ttlOptions == ttlOptionsHiden){						
							$(this).hide();
						}else{
							$(this).show();
						}
					});

				}else{

					optionsFilterReset();

				}
			});

				


		},

		bind: function() {

			$('#toggle-inspector').bind('click', toggleInspector);

		},

		iframeCallback: function() {

			bindBlockDimensionsTooltip();
			addInspector();

			/* Reset editor for layout switch */
			designEditor.switchLayout();

		}
	}

	return modeDesign;

});