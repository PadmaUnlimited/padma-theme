define(['jquery', 'knockout', 'underscore', 'jqueryUI'], function($, ko, _) {

	
	showContentSelector = function() {

		$('div#content-selector-select')
			.addClass('content-selector-visible');

		// Move content selector into correct position below the content selector select
		$('div#content-selector').css({
			left: $('div#content-selector-select-content').offset().left
		});

		$(document).bind('mousedown', hideContentSelector);
		Padma.iframe.contents().bind('mousedown', hideContentSelector);

		return $('div#content-selector-select');

	}

	hideContentSelector = function(event) {

		if ( event && ($(event.target).is('#content-selector-select') || $(event.target).parents('#content-selector-select').length === 1 ))
			return;

		$('div#content-selector-select')
			.removeClass('content-selector-visible');

		$(document).unbind('mousedown', hideContentSelector);
		Padma.iframe.contents().unbind('mousedown', hideContentSelector);
		
		return $('div#content-selector-select');

	}
	
	toggleContentSelector = function() {
		
		if ( $('div#content-selector-select').hasClass('content-selector-visible') ) {
			hideContentSelector(false);
		} else {
			showContentSelector();
		}

	}
	
	switchToContent = function (selectedContent, showSwitchNotification, selectedContentName) {

		console.log(selectedContent);
		console.log(showSwitchNotification);
		console.log(selectedContentName);
		console.log('Por revisar: switchToContent');

		/*
		var content, contentNode, contentID, contentName;

		if ( typeof selectedContent == 'object' && !selectedContent.hasClass('content') ) {
			contentNode = selectedContent.find('> span.content');
		} else {
			contentNode = selectedContent;
		}

		if ( typeof selectedContent == 'string' ) {

			if ( !isNaN(selectedContent) ) {
				selectedContent = 'template-' + selectedContent;
			}

			contentNode = $('div#content-selector span.content[data-content-id="' + selectedContent + '"]');

		}

		if ( typeof selectedContent !== 'string' && contentNode.length !== 1 ) {
			return false;
		}
				
		changeTitle('Visual Editor: Loading');
		startTitleActivityIndicator();

		if ( contentNode.length ) {

			content = contentNode;
			contentID = content.attr('data-content-id');			
			contentName = content.find('strong').text();

		} else {

			content = $();
			contentID = selectedContent;			
			contentName = selectedContentName;

		}

		//Set global variables, these will be used in the next function to switch the iframe
		Padma.viewModels.contentSelector.currentContent(contentID);
		Padma.viewModels.contentSelector.currentContentName(contentName);
		Padma.viewModels.contentSelector.currentContentInUse(null);
		Padma.viewModels.contentSelector.currentContentInUseName(null);

		//Set global variable to tell designEditor.switchContent that this content was switched to and not initial load
		Padma.switchedToContent = true;

		//Check if content is customized
		Padma.viewModels.contentSelector.currentContentCustomized(content.parents('li.content-item').first().hasClass('content-item-customized') || content.parents('#content-selector-templates-container').length);

		//Figure out content in use based off of hierachy
		if ( Padma.viewModels.contentSelector.currentContentCustomized() ) {

			Padma.viewModels.contentSelector.currentContentInUse(contentID);
			Padma.viewModels.contentSelector.currentContentInUseName(contentName);

		} else {

			content.parents('li.content-item').each(function() {

				var contentNodeData = ko.dataFor(this);

				if ( contentNodeData.customized() ) {

					Padma.viewModels.contentSelector.currentContentInUse(contentNodeData.id);
					Padma.viewModels.contentSelector.currentContentInUseName(contentNodeData.name);

					return false;

				} else if ( contentNodeData.template() ) {

					Padma.viewModels.contentSelector.currentContentInUse(contentNodeData.template());
					Padma.viewModels.contentSelector.currentContentInUseName(contentNodeData.templateName());

					return false;

				}

			});

			// If no parent is found to be customized check the top level contents
			if ( !Padma.viewModels.contentSelector.currentContentInUse() ) {

				$('ul#content-selector-pages-content').children('li.content-item').each(function() {

					var contentNodeData = ko.dataFor(this);

					if ( contentNodeData.customized() ) {

						Padma.viewModels.contentSelector.currentContentInUse(contentNodeData.id);
						Padma.viewModels.contentSelector.currentContentInUseName(contentNodeData.name);

						return false;

					} else if ( contentNodeData.template() ) {

						Padma.viewModels.contentSelector.currentContentInUse(contentNodeData.template());
						Padma.viewModels.contentSelector.currentContentInUseName(contentNodeData.templateName());

						return false;

					}

				});

				// If no match is still found then there are no customized contents and the current content in use is the current content 
				if ( !Padma.viewModels.contentSelector.currentContentInUse() ) {

					Padma.viewModels.contentSelector.currentContentInUse(contentID);
					Padma.viewModels.contentSelector.currentContentInUseName(contentName);

				}

			}

		}

		//Check if the content node has a template assigned to it.  
			var possibleTemplateID = content.find('.status-template').data('template-id');
							
			if ( typeof possibleTemplateID != 'undefined' && possibleTemplateID != 'none' ) {

				Padma.viewModels.contentSelector.currentContentTemplate(possibleTemplateID);
				Padma.viewModels.contentSelector.currentContentTemplateName($('span.content[data-content-id="template-' + possibleTemplateID + '"]').find('.template-name').text());

			} else {
				Padma.viewModels.contentSelector.currentContentTemplate(false);
			}


		// Push new content ID to the URL
		window.history.pushState("", "", Padma.homeURL + "/?visual-editor=true&visual-editor-mode=" + Padma.mode + "&ve-content=" + encodeURIComponent(Padma.viewModels.contentSelector.currentContent()));
		
		//Reload iframe and new content right away
		if ( typeof showSwitchNotification == 'undefined' || showSwitchNotification == true )
			padmaIframeLoadNotification = 'Switched to <em>' + Padma.viewModels.contentSelector.currentContentName() + '</em>';

		loadIframe(Padma.instance.iframeCallback, contentURL);
		*/

		hideIframeOverlay();
		return true;
		
	}

	var contentSelector = {

		init: function() {
			contentSelector.setupViewModel();
			contentSelector.bind();
		},
		
		setupViewModel: function() {
			Padma.viewModels.contentSelector = {
				/*
				currentContent: ko.observable(Padma.currentContent),
				currentContentName: ko.observable(Padma.currentContentName),
				currentContentInUse: ko.observable(Padma.currentContentInUse),
				currentContentInUseName: ko.observable(Padma.currentContentInUseName),
				currentContentTemplate: ko.observable(Padma.currentContentTemplate),
				currentContentTemplateName: ko.observable(Padma.currentContentTemplateName),
				currentContentCustomized: ko.observable(Padma.currentContentCustomized),
				*/				
				pages: contentSelector.mapArrayToContentModel(Padma.layouts.pages),
				search: ko.observableArray([]),				
				searching: ko.observable(false),
				//shared: contentSelector.mapArrayToContentModel(Padma.contents.shared)
			};
			
			$(document).ready(function () {				
				ko.applyBindings(Padma.viewModels.contentSelector, $('#content-selector-pages-container').get(0));				
			});

		},
		
		contentModel: function (content) {

			this.id = content.id;
			this.name = content.name;		

			this.url = content.url;
			this.template = ko.observable(content.id);
			this.templateName = ko.observable(content.post_title);
			this.postStatus = ko.observable(content.id);
			this.customized = true;
			
			this.ajaxChildren = ko.observable(content.ajaxChildren);

			this.ajaxLoading = ko.observable(false);
			this.ajaxLoaded = ko.observable(false);
			this.ajaxShowMore = ko.observable(false);
			this.ajaxLoadOffset = ko.observable(0);

			//this.noEdit = ko.observable(typeof content.noEdit != 'undefined' ? content.noEdit : false);

			this.children = contentSelector.mapArrayToContentModel(content.children);

			return this;

		},
		
		mapArrayToContentModel: function(contents) {

			var normalizedData = [];

			$.each(contents, function (index, data) {
				normalizedData.push(new contentSelector.contentModel(data));
			});

			return ko.observableArray(normalizedData);

		},
	
		loadContents: function(contentData, contentContext, $element, loadingMore) {

			var loadingMore = loadingMore || false;

			if ( contentData.ajaxLoading() ) {
				return false;
			}

			contentData.ajaxLoading(true);


			var $loadingIndicator = $('<li class="content-item content-loading-children"><span class="dashicons dashicons-update"></span> Loading...</li>');
			$loadingIndicator.insertAfter($element.parent());

			return $.ajax(Padma.ajaxURL, {
				type   : 'POST',
				async  : true,
				data   : {
					action  : 'padma_visual_editor',
					method  : 'get_content_children',
					security: Padma.security,
					content  : contentData.id,
					offset  : contentData.ajaxLoadOffset,
					mode    : Padma.mode
				},
				success: function (data, textStatus) {
				

					$loadingIndicator.remove();
					contentData.ajaxLoading(false);

					if ( false && (!_.isArray(data) || !data.length) && !loadingMore ) {
						contentContext.$data.ajaxChildren(false);
						contentContext.$data.children([]);

						return $(self).removeClass('content-open');
					}

					if ( !_.isArray(contentContext.$data.children()) ) {
						contentContext.$data.children(ko.utils.unwrapObservable(contentSelector.mapArrayToContentModel(data)));
					} else {
						
						$.each(ko.utils.unwrapObservable(contentSelector.mapArrayToContentModel(data)), function(index, data) {							
						
							contentContext.$data.children.push(data);
						
						});

					}

					contentContext.$data.ajaxLoaded(true);
					contentContext.$data.ajaxLoadOffset(contentContext.$data.ajaxLoadOffset() + data.length);

					if ( data.length == 30 ) {
						contentContext.$data.ajaxShowMore(true);
					} else {
						contentContext.$data.ajaxShowMore(false);
					}

				}
			});

		},
		
		searchContents: function(query) {

			Padma.viewModels.contentSelector.searching(true);

			return $.ajax(Padma.ajaxURL, {
				type   : 'POST',
				async  : true,
				data   : {
					action  : 'padma_visual_editor',
					method  : 'query_posts',
					security: Padma.security,
					query  : query
				},
				success: function (data, textStatus) {

					Padma.viewModels.contentSelector.searching(false);

					if ( !_.isArray(data) || !data.length ) {
						return;
					}

					return Padma.viewModels.contentSelector.search(ko.utils.unwrapObservable(contentSelector.mapArrayToContentModel(data)));

				}
			});

		},
		

		bind: function() {

            var contentSelectorEl = $('div#content-selector');

			// Make open do cool stuff
			$('div#content-selector-select-content').on('click', function(){

				toggleContentSelector();

				return false;

			});

			//Search
			var contentSelectorSearchForm = $("#content-search-input-container form");
			var contentSelectorSearchInput = contentSelectorSearchForm.find('input#content-search-input');

			contentSelectorSearchInput.on('search', function(event) {
				contentSelectorSearchForm.trigger('submit');
			});

			contentSelectorSearchInput.on('keyup', function (event) {

				if ( $(this).val().length === 0 ) {
					contentSelectorSearchForm.trigger('submit');
				}

			});

			var contentSelectorSearchFormSubmit = function (event) {

				var query = $('#content-search-input').val();

				if ( query.length === 0 ) {
					Padma.viewModels.contentSelector.search([]);
					event.preventDefault();
					return false;
				}

				contentSelector.searchContents(query);

				event.preventDefault();

			};

			$('#content-search-submit').on('click', contentSelectorSearchFormSubmit);
			contentSelectorSearchForm.on('submit', contentSelectorSearchFormSubmit);
			
			// Make buttons work
            contentSelectorEl.delegate('span.show-this', 'click', function(event){

				if ( typeof allowVECloseSwitch !== 'undefined' && allowVECloseSwitch === false ) {

					if ( !confirm('You have unsaved changes, are you sure you want to switch contents?') ) {
						return false;
					}

				}

				showIframeLoadingOverlay();

				//Switch contents
				switchToContent($(this).parents('span.content'));

				// Hide content selector
				hideContentSelector();

				//Hide Overlay
				hideIframeOverlay();

				event.preventDefault();

                return $(this).parents('span.content');

			});

			// Handle Collapsing Stuff
            contentSelectorEl.delegate('span.content', 'click', function(event) {

				var self = this;

				var contentData = ko.dataFor(this);
				var contentContext = ko.contextFor(this);

				if ( !$(this).parent().hasClass('has-children') ) {
					return;
				}

				$(this).toggleClass('content-open');

				if ( $(this).parent().hasClass('has-ajax-children') && !contentContext.$data.ajaxLoaded() ) {

					contentSelector.loadContents(contentData, contentContext, $(this));

				}

			});

			// Handle Collapsing Stuff			
			contentSelectorEl.delegate('span.load-more-contents', 'click', function (event) {

				var self = this;

				var contentData = ko.dataFor(this);
				var contentContext = ko.contextFor(this);

				$(self)
					.text('Load More...')
					.attr('disabled', 'disabled');

				$.when(contentSelector.loadContents(contentData, contentContext, $(this), true)).done(function() {
					$(self)
						.text('Load More...')
						.attr('disabled', '');
				});

			});

		}

	}

	return contentSelector;

});