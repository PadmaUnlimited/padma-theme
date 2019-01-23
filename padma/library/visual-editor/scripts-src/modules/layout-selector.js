define(['jquery', 'knockout', 'underscore', 'jqueryUI'], function($, ko, _) {

	showLayoutSelector = function() {

		$('div#layout-selector-select')
			.addClass('layout-selector-visible');

		/* Move layout selector into correct position below the layout selector select */
		$('div#layout-selector').css({
			left: $('div#layout-selector-select-content').offset().left
		});

		$(document).bind('mousedown', hideLayoutSelector);
		Padma.iframe.contents().bind('mousedown', hideLayoutSelector);

		return $('div#layout-selector-select');

	}

	hideLayoutSelector = function(event) {

		if ( event && ($(event.target).is('#layout-selector-select') || $(event.target).parents('#layout-selector-select').length === 1 ))
			return;

		$('div#layout-selector-select')
			.removeClass('layout-selector-visible');

		$(document).unbind('mousedown', hideLayoutSelector);
		Padma.iframe.contents().unbind('mousedown', hideLayoutSelector);
		
		return $('div#layout-selector-select');

	}

	toggleLayoutSelector = function() {
		
		if ( $('div#layout-selector-select').hasClass('layout-selector-visible') ) {
			hideLayoutSelector(false);
		} else {
			showLayoutSelector();
		}

	}

	switchToLayout = function (selectedLayout, showSwitchNotification, selectedLayoutName) {

		var layout, layoutNode, layoutID, layoutURL, layoutName;

		if ( typeof selectedLayout == 'object' && !selectedLayout.hasClass('layout') ) {
			layoutNode = selectedLayout.find('> span.layout');
		} else {
			layoutNode = selectedLayout;
		}

		if ( typeof selectedLayout == 'string' ) {

			if ( !isNaN(selectedLayout) ) {
				selectedLayout = 'template-' + selectedLayout;
			}

			layoutNode = $('div#layout-selector span.layout[data-layout-id="' + selectedLayout + '"]');

		}

		if ( typeof selectedLayout !== 'string' && layoutNode.length !== 1 ) {
			return false;
		}
				
		changeTitle('Visual Editor: Loading');
		startTitleActivityIndicator();

		if ( layoutNode.length ) {

			layout = layoutNode;
			layoutID = layout.attr('data-layout-id');
			layoutURL = Padma.mode == 'grid' ? Padma.homeURL : layout.attr('data-layout-url');
			layoutName = layout.find('strong').text();

		} else {

			layout = $();
			layoutID = selectedLayout;
			layoutURL = Padma.homeURL;
			layoutName = selectedLayoutName;

		}

		//Set global variables, these will be used in the next function to switch the iframe
		Padma.viewModels.layoutSelector.currentLayout(layoutID);
		Padma.viewModels.layoutSelector.currentLayoutName(layoutName);
		Padma.viewModels.layoutSelector.currentLayoutInUse(null);
		Padma.viewModels.layoutSelector.currentLayoutInUseName(null);

		//Set global variable to tell designEditor.switchLayout that this layout was switched to and not initial load
		Padma.switchedToLayout = true;

		//Check if layout is customized
		Padma.viewModels.layoutSelector.currentLayoutCustomized(layout.parents('li.layout-item').first().hasClass('layout-item-customized') || layout.parents('#layout-selector-templates-container').length);

		//Figure out layout in use based off of hierachy
		if ( Padma.viewModels.layoutSelector.currentLayoutCustomized() ) {

			Padma.viewModels.layoutSelector.currentLayoutInUse(layoutID);
			Padma.viewModels.layoutSelector.currentLayoutInUseName(layoutName);

		} else {

			layout.parents('li.layout-item').each(function() {

				var layoutNodeData = ko.dataFor(this);

				if ( layoutNodeData.customized() ) {

					Padma.viewModels.layoutSelector.currentLayoutInUse(layoutNodeData.id);
					Padma.viewModels.layoutSelector.currentLayoutInUseName(layoutNodeData.name);

					return false;

				} else if ( layoutNodeData.template() ) {

					Padma.viewModels.layoutSelector.currentLayoutInUse(layoutNodeData.template());
					Padma.viewModels.layoutSelector.currentLayoutInUseName(layoutNodeData.templateName());

					return false;

				}

			});

			/* If no parent is found to be customized check the top level layouts */
			if ( !Padma.viewModels.layoutSelector.currentLayoutInUse() ) {

				$('ul#layout-selector-pages-content').children('li.layout-item').each(function() {

					var layoutNodeData = ko.dataFor(this);

					if ( layoutNodeData.customized() ) {

						Padma.viewModels.layoutSelector.currentLayoutInUse(layoutNodeData.id);
						Padma.viewModels.layoutSelector.currentLayoutInUseName(layoutNodeData.name);

						return false;

					} else if ( layoutNodeData.template() ) {

						Padma.viewModels.layoutSelector.currentLayoutInUse(layoutNodeData.template());
						Padma.viewModels.layoutSelector.currentLayoutInUseName(layoutNodeData.templateName());

						return false;

					}

				});

				/* If no match is still found then there are no customized layouts and the current layout in use is the current layout */
				if ( !Padma.viewModels.layoutSelector.currentLayoutInUse() ) {

					Padma.viewModels.layoutSelector.currentLayoutInUse(layoutID);
					Padma.viewModels.layoutSelector.currentLayoutInUseName(layoutName);

				}

			}

		}

		//Check if the layout node has a template assigned to it.  
			var possibleTemplateID = layout.find('.status-template').data('template-id');
							
			if ( typeof possibleTemplateID != 'undefined' && possibleTemplateID != 'none' ) {

				Padma.viewModels.layoutSelector.currentLayoutTemplate(possibleTemplateID);
				Padma.viewModels.layoutSelector.currentLayoutTemplateName($('span.layout[data-layout-id="template-' + possibleTemplateID + '"]').find('.template-name').text());

			} else {
				Padma.viewModels.layoutSelector.currentLayoutTemplate(false);
			}


		/* Push new layout ID to the URL */
		window.history.pushState("", "", Padma.homeURL + "/?visual-editor=true&visual-editor-mode=" + Padma.mode + "&ve-layout=" + encodeURIComponent(Padma.viewModels.layoutSelector.currentLayout()));
		
		//Reload iframe and new layout right away
		if ( typeof showSwitchNotification == 'undefined' || showSwitchNotification == true )
			padmaIframeLoadNotification = 'Switched to <em>' + Padma.viewModels.layoutSelector.currentLayoutName() + '</em>';

		loadIframe(Padma.instance.iframeCallback, layoutURL);

		return true;
		
	}


	unassignSharedLayout = function(layoutID, layoutNode, layoutName) {

		var layoutData;

		if ( typeof layoutNode != 'undefined' && layoutNode.length ) {

			layoutData = ko.dataFor(layoutNode.get(0));
			layoutName = layoutNode.find('> span.layout strong').text();

		} else {

			var layoutNode = $('span.layout[data-layout-id="' + layoutID + '"]');

			if ( layoutNode.length ) {
				layoutData = ko.dataFor(layoutNode.get(0));
			}

		}

		if ( !confirm('Are you sure you want to remove the shared layout from ' + layoutName + '?') )
			return false;

		//Do the AJAX request to assign the template
		$.post(Padma.ajaxURL, {
			security: Padma.security,
			action: 'padma_visual_editor',
			method: 'remove_template_from_layout',
			layout: layoutID
		}, function (response) {

			if ( typeof response === 'undefined' || response == 'failure' ) {
				showErrorNotification({
					id: 'error-could-not-remove-template-from-layout',
					message: 'Error: Could not remove shared layout from layout.'
				});

				return false;
			}

			if ( layoutData ) {
				layoutData.template(false);
				layoutData.templateName(false);
			}

			//If the current layout is the one with the template that we're unassigning, we need to reload the iframe.
			if ( layoutID == Padma.viewModels.layoutSelector.currentLayout() ) {

				showIframeLoadingOverlay();

				//Change title to loading
				changeTitle('Visual Editor: Removing Shared Layout From Layout');
				startTitleActivityIndicator();

				Padma.viewModels.layoutSelector.currentLayoutTemplate(false);

				//Reload iframe and new layout
				padmaIframeLoadNotification = 'Shared Layout removed from layout successfully!';

				loadIframe(Padma.instance.iframeCallback);

				return true;

			} else {
				showNotification({
					id: 'shared-layout-removed-from-layout',
					message: 'Shared Layout removed from layout successfully!'
				});
			}

			//We're all good!
			return true;

		});

	}


	var layoutSelector = {

		init: function() {
			layoutSelector.setupViewModel();
			layoutSelector.bind();
		},

		setupViewModel: function() {

			Padma.viewModels.layoutSelector = {
				currentLayout: ko.observable(Padma.currentLayout),
				currentLayoutName: ko.observable(Padma.currentLayoutName),
				currentLayoutInUse: ko.observable(Padma.currentLayoutInUse),
				currentLayoutInUseName: ko.observable(Padma.currentLayoutInUseName),
				currentLayoutTemplate: ko.observable(Padma.currentLayoutTemplate),
				currentLayoutTemplateName: ko.observable(Padma.currentLayoutTemplateName),
				currentLayoutCustomized: ko.observable(Padma.currentLayoutCustomized),
				pages: layoutSelector.mapArrayToLayoutModel(Padma.layouts.pages),
				search: ko.observableArray([]),
				searching: ko.observable(false),
				shared: layoutSelector.mapArrayToLayoutModel(Padma.layouts.shared)
			};

			$(document).ready(function () {
				ko.applyBindings(Padma.viewModels.layoutSelector, $('#layout-selector-pages-container').get(0));
				ko.applyBindings(Padma.viewModels.layoutSelector, $('#layout-selector-templates-container').get(0));
			});

		},

		layoutModel: function (layout) {

			this.id = layout.id;
			this.name = layout.name;
			this.url = layout.url;
			this.template = ko.observable(layout.template);
			this.templateName = ko.observable(layout.templateName);
			this.customized = ko.observable(layout.customized);
			this.postStatus = ko.observable(layout.postStatus);

			this.ajaxChildren = ko.observable(layout.ajaxChildren);

			this.ajaxLoading = ko.observable(false);
			this.ajaxLoaded = ko.observable(false);
			this.ajaxShowMore = ko.observable(false);
			this.ajaxLoadOffset = ko.observable(0);

			this.noEdit = ko.observable(typeof layout.noEdit != 'undefined' ? layout.noEdit : false);

			this.children = layoutSelector.mapArrayToLayoutModel(layout.children);

			return this;

		},

		mapArrayToLayoutModel: function(layouts) {

			var normalizedData = [];

			$.each(layouts, function (index, data) {
				normalizedData.push(new layoutSelector.layoutModel(data));
			});

			return ko.observableArray(normalizedData);

		},

		loadLayouts: function(layoutData, layoutContext, $element, loadingMore) {

			var loadingMore = loadingMore || false;

			if ( layoutData.ajaxLoading() ) {
				return false;
			}

			layoutData.ajaxLoading(true);

			var $loadingIndicator = $('<li class="layout-item layout-loading-children"><span class="dashicons dashicons-update"></span> Loading...</li>');
			$loadingIndicator.insertAfter($element.parent());

			return $.ajax(Padma.ajaxURL, {
				type   : 'POST',
				async  : true,
				data   : {
					action  : 'padma_visual_editor',
					method  : 'get_layout_children',
					security: Padma.security,
					layout  : layoutData.id,
					offset  : layoutData.ajaxLoadOffset,
					mode    : Padma.mode
				},
				success: function (data, textStatus) {

					$loadingIndicator.remove();
					layoutData.ajaxLoading(false);

					if ( (!_.isArray(data) || !data.length) && !loadingMore ) {
						layoutContext.$data.ajaxChildren(false);
						layoutContext.$data.children([]);

						return $(self).removeClass('layout-open');
					}

					if ( !_.isArray(layoutContext.$data.children()) ) {
						layoutContext.$data.children(ko.utils.unwrapObservable(layoutSelector.mapArrayToLayoutModel(data)));
					} else {

						$.each(ko.utils.unwrapObservable(layoutSelector.mapArrayToLayoutModel(data)), function(index, data) {
							layoutContext.$data.children.push(data);
						});

					}

					layoutContext.$data.ajaxLoaded(true);
					layoutContext.$data.ajaxLoadOffset(layoutContext.$data.ajaxLoadOffset() + data.length);

					if ( data.length == 30 ) {
						layoutContext.$data.ajaxShowMore(true);
					} else {
						layoutContext.$data.ajaxShowMore(false);
					}

				}
			});

		},

		searchLayouts: function(query) {

			Padma.viewModels.layoutSelector.searching(true);

			return $.ajax(Padma.ajaxURL, {
				type   : 'POST',
				async  : true,
				data   : {
					action  : 'padma_visual_editor',
					method  : 'query_layouts',
					security: Padma.security,
					query  : query
				},
				success: function (data, textStatus) {

					Padma.viewModels.layoutSelector.searching(false);

					if ( !_.isArray(data) || !data.length ) {
						return;
					}

					return Padma.viewModels.layoutSelector.search(ko.utils.unwrapObservable(layoutSelector.mapArrayToLayoutModel(data)));

				}
			});

		},

		bind: function() {

            var layoutSelectorEl = $('div#layout-selector');

			/* Make open do cool stuff */
			$('div#layout-selector-select-content').on('click', function(){

				toggleLayoutSelector();

				return false;

			});

			/* Search */
			var layoutSelectorSearchForm = $("#layout-search-input-container form");
			var layoutSelectorSearchInput = layoutSelectorSearchForm.find('input#layout-search-input');

			layoutSelectorSearchInput.on('search', function(event) {
				layoutSelectorSearchForm.trigger('submit');
			});

			layoutSelectorSearchInput.on('keyup', function (event) {

				if ( $(this).val().length === 0 ) {
					layoutSelectorSearchForm.trigger('submit');
				}

			});

			var layoutSelectorSearchFormSubmit = function (event) {

				var query = $('#layout-search-input').val();

				if ( query.length === 0 ) {
					Padma.viewModels.layoutSelector.search([]);
					event.preventDefault();
					return false;
				}

				layoutSelector.searchLayouts(query);

				event.preventDefault();

			};

			$('#layout-search-submit').on('click', layoutSelectorSearchFormSubmit);
			layoutSelectorSearchForm.on('submit', layoutSelectorSearchFormSubmit);
			
			/* Tabs */
            layoutSelectorEl.tabs();

			/* Make buttons work */
            layoutSelectorEl.delegate('span.edit', 'click', function(event){


				if ( typeof allowVECloseSwitch !== 'undefined' && allowVECloseSwitch === false ) {

					if ( !confirm('You have unsaved changes, are you sure you want to switch layouts?') ) {
						return false;
					}

				}

				showIframeLoadingOverlay();

				//Switch layouts
				switchToLayout($(this).parents('span.layout'));

				/* Hide layout selector */
				hideLayoutSelector();

				event.preventDefault();

                return $(this).parents('span.layout');

			});
         
            layoutSelectorEl.delegate('span.revert', 'click', function(event){

				if ( !confirm('Are you sure you wish to reset this layout?  All blocks and content will be removed from this layout.\n\nPlease note: Any block that is mirroring a block on this layout will also lose its settings.') ) {
					return false;
				}

				var revertedLayout = $(this).parents('span.layout');
				var revertedLayoutID = revertedLayout.attr('data-layout-id');
				var revertedLayoutName = revertedLayout.find('strong').text();

				/* Add loading indicators */
				showIframeLoadingOverlay();

				changeTitle('Visual Editor: Reverting ' + revertedLayoutName);
				startTitleActivityIndicator();

				/* Remove customized status from current layout */
				revertedLayout.parent().removeClass('layout-item-customized');

				/* Find the layout that's customized above this one */
				var parentCustomizedLayout = $(revertedLayout.parents('.layout-item-customized:not(.layout-selected)')[0]);
				var parentCustomizedLayoutID = parentCustomizedLayout.find('> span.layout').attr('data-layout-id');

				var topLevelCustomized = $($('div#layout-selector-pages > ul > li.layout-item-customized')[0]);
				var topLevelCustomizedID = topLevelCustomized.find('> span.layout').attr('data-layout-id');

				var selectedLayout = parentCustomizedLayoutID ? parentCustomizedLayout : topLevelCustomized;
				var selectedLayoutID = parentCustomizedLayoutID ? parentCustomizedLayoutID : topLevelCustomizedID;

				/* If the user gets on a revert frenzy and reverts all pages, then it should fall back to the blog index or front page (if active) */
				if ( typeof selectedLayoutID == 'undefined' || !selectedLayoutID ) {

					selectedLayoutID = Padma.frontPage == 'posts' ? 'index' : 'front_page';
					selectedLayout = $('div#layout-selector-pages > ul > li > span[data-layout-id="' + selectedLayoutID + '"]').parent();

				}

				/* Switch to the next higher-up layout */
				switchToLayout(selectedLayout, false);

				/* Delete everything from the reverted layout */
				$.post(Padma.ajaxURL, {
					security: Padma.security,
					action: 'padma_visual_editor',
					method: 'revert_layout',
					layout_to_revert: revertedLayoutID
				}, function(response) {

					if ( response === 'success' ) {

						var revertedLayoutData = ko.dataFor(revertedLayout.get(0));

						revertedLayoutData.customized(false);

						showNotification({
							id: 'layout-reverted',
							message: '<em>' + revertedLayoutName + '</em> successfully reverted!',
							success: true
						});

					} else {
						showErrorNotification({
							id: 'error-could-not-revert-layout',
							message: 'Error: Could not revert layout.'
						});
					}

				});

				return false;

			});
            
            layoutSelectorEl.delegate('span#add-template', 'click', function(event) {

				var templateName = $('#template-name-input').val();

				//Do the AJAX request for the new template
				$.post(Padma.ajaxURL, {
					security: Padma.security,
					action: 'padma_visual_editor',
					method: 'add_template',
					layout: Padma.viewModels.layoutSelector.currentLayout(),
					template_name: templateName
				}, function(response) {

					if ( typeof response === 'undefined' || !response ) {
						showErrorNotification({
							id: 'error-could-not-add-template',
							message: 'Error: Could not add shared layout.'
						});

						return false;
					}

					//Need to add the new template BEFORE the add button
					var newTemplateNode = $('<li class="layout-item">\
						<span data-layout-id="template-' + response.id + '" class="layout layout-template">\
							<strong class="template-name">' + response.name + '</strong>\
							\
							<span class="delete-template" title="Delete Shared Layout">Delete</span>\
							\
							<span class="status status-currently-editing">Currently Editing</span>\
							\
							\
							<span class="rename-template button layout-selector-button" title="Rename Shared Layout">Rename</span>\
							<span class="assign-template button layout-selector-button">Use Layout</span>\
							<span class="edit button layout-selector-button">Edit</span>\
						</span>\
					</li>');

					newTemplateNode.appendTo('div#layout-selector-templates ul');

					//Hide the no templates warning if it's visible
					$('li#no-templates:visible', 'div#layout-selector').hide();

					//We're all good!
					showNotification({
						id: 'template-added',
						message: 'Shared layout added!',
						success: true
					});

					//Clear template name input value
					$('#template-name-input').val('');

				}, 'json');

				return false;

			});
          
            layoutSelectorEl.delegate('span.delete-template', 'click', function(event){

				var templateLi = $($(this).parents('li')[0]);
				var templateSpan = $(this).parent();
				var template = templateSpan.attr('data-layout-id');
				var templateID = template.replace('template-', '');
				var templateName = templateSpan.find('strong').text();

				if ( !confirm('Are you sure you wish to delete this Shared Layout?') )
					return false;

				//Do the AJAX request for the new template
				$.post(Padma.ajaxURL, {
					security: Padma.security,
					action: 'padma_visual_editor',
					method: 'delete_template',
					template_to_delete: templateID
				}, function(response) {

					if ( typeof response === 'undefined' || response == 'failure' || response != 'success' ) {
						showErrorNotification({
							id: 'error-could-not-deleted-template',
							message: 'Error: Could not delete shared layout.'
						});

						return false;
					}

					//Delete the template from DOM
					templateLi.remove();

					//Show the no templates message if there are no more templates
					if ( $('span.layout-template', 'div#layout-selector').length === 0 ) {
						$('li#no-templates', 'div#layout-selector').show();
					}

					//We're all good!
					showNotification({
						id: 'template-deleted',
						message: 'Shared Layout: <em>' + templateName + '</em> successfully deleted!',
						success: true
					});

					//If the template that was removed was the current one, then send the user back to the blog index or front page
					if ( template === Padma.viewModels.layoutSelector.currentLayout() ) {

						var defaultLayout = Padma.frontPage == 'posts' ? 'index' : 'front_page';

						switchToLayout($('div#layout-selector span.layout[data-layout-id="' + defaultLayout + '"]'), false);

					}

				});

				return false;

			});
            
            layoutSelectorEl.delegate('span.assign-template', 'click', function(event){

				var templateNode = $($(this).parents('li')[0]);
				var template = $(this).parent().attr('data-layout-id').replace('template-', '');

				var layoutData = ko.dataFor($('li.layout-selected').get(0));

				//If the current layout being edited is a template trigger an error.
				if ( Padma.viewModels.layoutSelector.currentLayout().indexOf('template-') === 0 ) {
					alert('You cannot assign a shared layout to another shared layout.');

					return false;
				}

				//Do the AJAX request to assign the template
				$.post(Padma.ajaxURL, {
					security: Padma.security,
					action: 'padma_visual_editor',
					method: 'assign_template',
					template: template,
					layout: Padma.viewModels.layoutSelector.currentLayout()
				}, function(response) {

					if ( typeof response === 'undefined' || response == 'failure' ) {
						showErrorNotification({
							id: 'error-could-not-assign-template',
							message: 'Error: Could not assign shared layout.'
						});

						return false;
					}

					$('li.layout-selected', 'div#layout-selector').removeClass('layout-item-customized');
					$('li.layout-selected', 'div#layout-selector').addClass('layout-item-template-used');

					$('li.layout-selected > span.status-template', 'div#layout-selector').text(response);

					/* Update Knockout */
					layoutData.template(template);
					layoutData.templateName(response);

					//Reload iframe

						showIframeLoadingOverlay();

						//Change title to loading
						changeTitle('Visual Editor: Assigning Shared Layout');
						startTitleActivityIndicator();

						Padma.viewModels.layoutSelector.currentLayoutTemplate('template-' + template);
						Padma.viewModels.layoutSelector.currentLayoutTemplateName($('span.layout[data-layout-id="template-' + template + '"]').find('.template-name').text());

						//Reload iframe and new layout
						padmaIframeLoadNotification = 'Shared layout assigned successfully!';

						loadIframe(Padma.instance.iframeCallback);

					//End reload iframe

				});

				return false;

			});
            
			layoutSelectorEl.delegate('span.rename-template', 'click', function (event) {

				var layoutNode = $($(this).parents('li')[0]);
				var layoutID = $(this).parent().attr('data-layout-id');

				var nameEl = $(this).siblings('.template-name');
				var currentName = nameEl.text();
				var newName = prompt('Please enter new Shared Layout name', currentName);

				//Do the AJAX request to assign the template
				$.post(Padma.ajaxURL, {
					security: Padma.security,
					action: 'padma_visual_editor',
					method: 'rename_layout_template',
					layout: layoutID,
					newName: newName,
				}, function (response) {

					if (typeof response === 'undefined' || response == 'failure') {
						showErrorNotification({
							id: 'error-could-not-rename-layout-template',
							message: 'Error: Could not rename shared layout.'
						});

						return false;
					}

					nameEl.text(newName);

					//We're all good!
					return true;

				});

				return false;

			});
			
			layoutSelectorEl.delegate('span.remove-template', 'click', function(event){

				var layoutNode = $($(this).parents('li')[0]);
				var layoutID = $(this).parent().attr('data-layout-id');

				var layoutData = ko.dataFor(layoutNode.get(0));

				return unassignSharedLayout(layoutID, layoutNode);

			});

			/* Handle Collapsing Stuff */
			
            layoutSelectorEl.delegate('span.layout', 'click', function(event) {

				var self = this;

				var layoutData = ko.dataFor(this);
				var layoutContext = ko.contextFor(this);

				if ( !$(this).parent().hasClass('has-children') ) {
					return;
				}

				$(this).toggleClass('layout-open');

				if ( $(this).parent().hasClass('has-ajax-children') && !layoutContext.$data.ajaxLoaded() ) {

					layoutSelector.loadLayouts(layoutData, layoutContext, $(this));

				}

			});

			/* Handle Collapsing Stuff */
			
			layoutSelectorEl.delegate('span.load-more-layouts', 'click', function (event) {

				var self = this;

				var layoutData = ko.dataFor(this);
				var layoutContext = ko.contextFor(this);

				$(self)
					.text('Load More...')
					.attr('disabled', 'disabled');

				$.when(layoutSelector.loadLayouts(layoutData, layoutContext, $(this), true)).done(function() {
					$(self)
						.text('Load More...')
						.attr('disabled', '');
				});

			});

		}

	}

	return layoutSelector;

});