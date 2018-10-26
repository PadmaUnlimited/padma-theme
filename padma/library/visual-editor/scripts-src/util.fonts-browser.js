(function($) {

/*!
jQuery quicksearch 
*/
(function($,window,document,undefined){$.fn.quicksearch=function(target,opt){var timeout,cache,rowcache,jq_results,val="",e=this,options=$.extend({delay:100,selector:null,stripeRows:null,loader:null,noResults:"",matchedResultsCount:0,bind:"keyup",onBefore:function(){return},onAfter:function(){return},show:function(){this.style.display=""},hide:function(){this.style.display="none"},prepareQuery:function(val){return val.toLowerCase().split(" ")},testQuery:function(query,txt,_row){for(var i=0;i<query.length;i+=
1)if(txt.indexOf(query[i])===-1)return false;return true}},opt);this.go=function(){var i=0,numMatchedRows=0,noresults=true,query=options.prepareQuery(val),val_empty=val.replace(" ","").length===0;for(var i=0,len=rowcache.length;i<len;i++)if(val_empty||options.testQuery(query,cache[i],rowcache[i])){options.show.apply(rowcache[i]);noresults=false;numMatchedRows++}else options.hide.apply(rowcache[i]);if(noresults)this.results(false);else{this.results(true);this.stripe()}this.matchedResultsCount=numMatchedRows;
this.loader(false);options.onAfter();return this};this.search=function(submittedVal){val=submittedVal;e.trigger()};this.currentMatchedResults=function(){return this.matchedResultsCount};this.stripe=function(){if(typeof options.stripeRows==="object"&&options.stripeRows!==null){var joined=options.stripeRows.join(" ");var stripeRows_length=options.stripeRows.length;jq_results.not(":hidden").each(function(i){$(this).removeClass(joined).addClass(options.stripeRows[i%stripeRows_length])})}return this};
this.strip_html=function(input){var output=input.replace(new RegExp("<[^<]+>","g"),"");output=$.trim(output.toLowerCase());return output};this.results=function(bool){if(typeof options.noResults==="string"&&options.noResults!=="")if(bool)$(options.noResults).hide();else $(options.noResults).show();return this};this.loader=function(bool){if(typeof options.loader==="string"&&options.loader!=="")bool?$(options.loader).show():$(options.loader).hide();return this};this.cache=function(){jq_results=$(target);
if(typeof options.noResults==="string"&&options.noResults!=="")jq_results=jq_results.not(options.noResults);var t=typeof options.selector==="string"?jq_results.find(options.selector):$(target).not(options.noResults);cache=t.map(function(){return e.strip_html(this.innerHTML)});rowcache=jq_results.map(function(){return this});val=val||this.val()||"";return true};this.trigger=function(){this.loader(true);options.onBefore();window.clearTimeout(timeout);timeout=window.setTimeout(function(){e.go()},
options.delay);return this};this.cache();this.results(true);this.stripe();this.loader(false);return this.each(function(){$(this).bind(options.bind,function(){val=$(this).val();e.trigger()})})}})(jQuery,this,document);



/* Fonts input object */
	function fontBrowserObj(browser) {

		this.browser = browser;

		this.propertyInput = browser.parents('.property-font-family-select');

		this.hiddenInput = this.propertyInput.find('input.property-hidden-input');

		this.setup = function() {

			var self = this;

			this.browser.find('.tab-content').each(function() {

				var fontsList = $(this).find('.fonts-list ul');

				var scrollWebFontLoaderDebounced = _.debounce(function() {
					self.scrollWebFontLoader(fontsList);
				}, 100);

				fontsList.bind('scroll', scrollWebFontLoaderDebounced);

				self.initQuickSearch($(this));
				self.initPreview($(this));
				self.initSorting($(this));

				fontsList.delegate('.use-font', 'click', function() {

					var li 					= $(this).parents('li').first();
					/* Determine value to save to DB */
					var webfontProvider 	= $(this).parents('.tab-content').data('font-webfont-provider');
					var fontID 				= li.data('value');
					var fontName 			= $(this).siblings('.font-family').text();
					var fontFamily 			= li.css('font-family');
					var fontVariants 		= li.data('variants');
					var variantsStr 		= '';

					if ( fontVariants && fontVariants.indexOf('regular') === -1 )
						variantsStr = '|' + fontVariants.join(',');

					for (var i = fontVariants.length - 1; i >= 0; i--) {
						var variant = fontVariants[i];
							variant.replace('300italic','i');

						variantsStr += variant;

						if (i > 0){
							variantsStr += ',';
						}
					}					

					var value = webfontProvider != false ? webfontProvider + '|' + fontID + ':' + variantsStr : fontID;
					/* Change readout */

					var fontNameReadout = self.propertyInput.find('.font-name');

					fontNameReadout.css('font-family', fontFamily);
					fontNameReadout.text(fontName);

					/* Change selected font */
					self.browser.find('.selected-font').removeClass('selected-font');
					li.addClass('selected-font');

					/* Close font panel */
					fontBrowserClose({
						data: {
							fontBrowser: self.browser
						}
					});	

					dataHandleDesignEditorInput({hiddenInput: self.hiddenInput, value: value, stack: fontFamily});

				});

			});

			this.browser.tabs({
				selected: 0,
				activate: function(event, ui) {

					var $newPanel = $(ui.newPanel);

					if ( $newPanel.data('fonts-loaded') )
						return;

					self.retrieveRemoteFonts($newPanel, 'popularity', true, true);

				}
			});

			this.changeToSelectedFontProviderTab();

		}

		this.retrieveRemoteFonts = function(context, sortBy, resetTransient, firstLoad) {

			if ( !context.data('font-load-with-ajax') )
				return;

			var self = this;

			createCog(context.find('.fonts-loading'), true);

			context.find('.fonts-list ul').fadeOut(300);
			context.find('.fonts-loading').fadeIn(300);

			/* Lock search until it has finished loading */
			context.find('.fonts-filter').attr('disabled', 'disabled');

			$.post(Padma.ajaxURL, {
				security: Padma.security,
				action: 'padma_visual_editor',
				method: 'fonts_list',
				sortby: sortBy,
				provider: context.data('font-webfont-provider')
			}, function(response) {

				context.find('.fonts-loading').fadeOut(300);
				context.find('ul').hide().html(response).fadeIn(300, function() {

					/* Force fonts to load before user scrolls */
					self.scrollWebFontLoader(context.find('ul'));

				});

				/* Refresh quick search cache */
				context.find('.fonts-filter').val('');
				context.data('quicksearch').cache();

				/* Allow quick search again */
				context.find('.fonts-filter').removeAttr('disabled');

				/* Scroll to selected item if first time loading tab otherwise scroll to top */
				if ( typeof firstLoad != 'undefined' && firstLoad && self.hiddenInput.val().match(/\|/g) ) {

					var selectedFont = context.find('li[data-value="' + self.hiddenInput.val().split('|')[1] + '"]');

					if ( selectedFont.length ) {

						selectedFont.addClass('selected-font');
						context.find('.fonts-list ul').scrollTop(selectedFont.position().top);

					}

				} else {

					context.find('.fonts-list ul').scrollTop(0);

				}

				context.data('fonts-loaded', true);

			});

		}

		this.scrollWebFontLoader = function(fontList) {

			var fontListContainer = $(fontList.parents('.tab-content').get(0));
			var fontListProvider = fontListContainer.data('font-webfont-provider');

			if ( fontList.parents('.font-provider-tab-content.ui-tabs-hide').length || !fontListProvider )
				return;

			var fontsToLoad = [];

			/* Find Visible Fonts that need to be loaded */
				var viewportTop = fontList.scrollTop();
				var viewportBottom = viewportTop + fontList.outerHeight();

				fontList.find('li').each(function() {

					var fontTop = $(this).position().top + fontList.scrollTop();
					var fontBottom = fontTop + $(this).outerHeight();

					if ( !$(this).is(':visible') || $(this).data('loadedFont') )
						return;

					if ( !(fontTop <= viewportBottom) )
						return;

					if ( !(fontBottom >= viewportTop) )
						return;

					if ( fontBottom > viewportBottom )
						return false;

					var variants = '';

					if ( $(this).data('variants').indexOf('regular') === -1 )
						variants = ':' + $(this).data('variants').join(',');

					fontsToLoad.push($(this).data('value') + variants);

				});

			/* Load fonts via WebFont API */
				if ( fontsToLoad.length ) {

					var args = {};
					var googleFontsQueryString = '';

					_.each(fontsToLoad, function(fontToLoad) {

						
						// Font name
						var fontNode = fontList.find('li[data-value="' + fontToLoad + '"]');
						fontNode.data('loadedFont', true);
						

						googleFontsQueryString += fontToLoad.replace(' ', '+');
						
						var variants = fontNode.data('variants');
						if(variants != undefined){
							if(variants.length > 0){
							
								googleFontsQueryString += ':';
								
								// Font variants
								variantsQueryString = '';
								for (var i = 0; i < variants.length; i++) {
									
									var variantName = variants[i];									
										variantName = variantName.replace('100italic','100i');
										variantName = variantName.replace('200italic','200i');
										variantName = variantName.replace('300italic','300i');
										variantName = variantName.replace('400italic','400i');
										variantName = variantName.replace('500italic','500i');
										variantName = variantName.replace('600italic','600i');
										variantName = variantName.replace('700italic','700i');
										variantName = variantName.replace('800italic','800i');
										variantName = variantName.replace('900italic','900i');
										variantName = variantName.replace('italic','400i');
										variantName = variantName.replace('regular','400');

									variantsQueryString +=  variantName + ',';
								}
								variantsQueryString = variantsQueryString.substr(0, variantsQueryString.length-1);
							}
							googleFontsQueryString += variantsQueryString + '|';
						}else{
							googleFontsQueryString += '|';
						}
					});


					if(googleFontsQueryString.substr(0, googleFontsQueryString.length-1) !== 'undefined'){

						$('<link>')
							.attr('type', 'text/css')
							.attr('rel', 'stylesheet')
							.attr('href', '//fonts.googleapis.com/css?family=' + googleFontsQueryString.substr(0, googleFontsQueryString.length-1))
							.appendTo('head')
							.bind('load', function() {

								_.each(fontsToLoad, function(fontToLoad) {
									var fontNode = fontList.find('li[data-value="' + fontToLoad.split(':')[0] + '"]');
									fontNode.find('span.font-family, span.font-preview-text').show().css('opacity', 1);

								});

							});
					}


				}

		}

		this.initQuickSearch = function(context) {

			var id = context.attr('id');

			var quicksearch = context.find('.fonts-filter').quicksearch('#' + id + ' .fonts-list ul li', {
				delay: 750,
				noResults: '#' + id + ' .fonts-list .fonts-noresults',
				loader: '#' + id + ' .fonts-list .fonts-loading',
				bind: 'keyup',
				onBefore: function() {
					context.find('.fonts-list ul').fadeOut(100);
				},
				onAfter: function() {
					/* Force fonts to be loaded */
					context.find('.fonts-list ul')
						.trigger('scroll')
						.fadeIn(100);
				},
				prepareQuery: function (val) {
				    return new RegExp(val, "i");
				},
			    testQuery: function (query, txt, _row) {
			        return query.test(jQuery.trim(txt.replace('the quick brown fox jumps over the lazy dog.', '')));
			    }
			});

			/* Attach quicksearch object to element that way the cache can be refreshed */
			context.data('quicksearch', quicksearch);

		}

		this.initPreview = function(context) {

			var self = this;

			/* fonts preview overlay */
			previewHtml = $('<div class="font-preview-overlay" style="display:none;">' +
					'<span class="close-preview"></span>' +
					'<header>' +
						'<h4></h4>' +
						'<p><i class="icon-edit">&nbsp;</i><strong>click anywhere</strong> in preview text to edit and add your own</p>' +
					'</header>' +
					'<div class="editable allow-backspace-key" contenteditable="true"></div>' +
					'<footer>' +
						'<div class="tools">' +
							'<span title="Reset Preview Text" class="reset-preview"></span>' +
							'<span title="Decrease Preview Size" class="size-down"></span>' +
							'<span title="Increase Preview Size" class="size-up"></span>' +
							'<span title="Use This Font" class="use-font"></span>' +
						'</div>' +
					'</footer>' +
				'</div>');

			context.find('.fonts-list').after(previewHtml);

		    /* preview functions */
		    this.defaultPreviewText = 'The quick brown fox jumps over the lazy dog.';
		    this.defaultPreviewSize = '24px';

		    this.previewResize = function(preview, resizeBy) {

		    	var editable = preview.find('.editable');

				var originalSize = editable.css('font-size');
				var newSize = parseFloat(originalSize, 10) * resizeBy;

				editable.css('font-size', newSize);

				localStorage.fontPreviewSize = editable.css('font-size');

			}
		    
		    this.previewLoadFromStorage = function(preview) {

		    	var editable = preview.find('.editable');

		    	/* set preview text */
		    	if ( !localStorage.getItem('fontPreviewText') ) {
				 	editable.html(self.defaultPreviewText);
				} else {
					editable.html(localStorage.fontPreviewText);
				}

				/* set font size */
				if ( localStorage.getItem('fontPreviewSize') ) {
					editable.css('font-size', localStorage.fontPreviewSize);
				}

		    }

		    this.previewSaveText = function() {

		    	localStorage.fontPreviewText = $(this).text();

			}

			this.previewReset = function(preview) {

				preview.find('.editable').html(self.defaultPreviewText);
				preview.find('.editable').css('font-size', self.defaultPreviewSize);

				localStorage.fontPreviewText = self.defaultPreviewText;
				localStorage.fontPreviewSize = self.defaultPreviewSize;

			}

			/* Bind the preview buttons to the preview can be opened */
			context.find('.fonts-list ul').delegate('li .preview-font', 'click', function() {

				var fontID 		= $(this).parents('li').data('value');
		    	var fontFamily 	= $(this).parents('li').css('font-family');
		    	var fontPreview = $(this).parents('.fonts-list').siblings('.font-preview-overlay');

		    	fontPreview.data('font-value', fontID);
		    	fontPreview.data('font-name', $(this).parent().find('.font-family').text());
		    	fontPreview.data('font-variants', $(this).parents('li').data('variants'));

		    	fontPreview.fadeIn(750);
		    	fontPreview.css('font-family', fontFamily);
		       	fontPreview.find('h4').html($(this).parent().find('.font-family').text() + ' <span>(Preview)</span>');

		        self.previewLoadFromStorage(fontPreview);

		    });

			/* increase */
			context.find('.font-preview-overlay .size-up').on('click', function() {
				self.previewResize($(this).parents('.font-preview-overlay'), 1.1);
			});

			/* decrease */
			context.find('.font-preview-overlay .size-down').on('click', function() {
				self.previewResize($(this).parents('.font-preview-overlay'), 0.9);
			});

			/* reset preview text */
			context.find('.font-preview-overlay .reset-preview').on('click', function() {
		    	self.previewReset($(this).parents('.font-preview-overlay'));
		    });

		    /* close preview */
		    context.find('.font-preview-overlay .close-preview').on('click', function() {
		    	$(this).parents('.font-preview-overlay').fadeOut(750);
		    });

		    /* save changes to local storage */
			context.find('.font-preview-overlay .editable').on('blur', this.previewSaveText);

			/* bind use font button */
			context.find('.font-preview-overlay .use-font').on('click', function() {



				/* Determine value to save to DB */
				var webfontProvider = $(this).parents('.tab-content').data('font-webfont-provider');
				var fontID 			= $(this).parents('.font-preview-overlay').data('font-value');
				var fontName 		= $(this).parents('.font-preview-overlay').data('font-name');
				var fontFamily 		= $(this).parents('.font-preview-overlay').css('font-family');
				var variants 		= $(this).parents('.font-preview-overlay').data('font-variants');

				var variantsStr 	= '';

				if ( variants && variants.indexOf('regular') === -1 )
					variantsStr = '|' + variants.join(',');

				var value = webfontProvider != false ? webfontProvider + '|' + fontID + variantsStr : fontID;

				/* Change readout */
				var fontNameReadout = self.propertyInput.find('.font-name');

				fontNameReadout.css('font-family', fontFamily);
				fontNameReadout.text(fontName);

				/* Change selected font */
				self.browser.find('.selected-font').removeClass('selected-font');
				self.browser.find('li[data-value="' + fontID + '"]').addClass('selected-font');

				/* Close font panel */
				fontBrowserClose({
					data: {
						fontBrowser: self.browser
					}
				});

				/* Save value */
				dataHandleDesignEditorInput({hiddenInput: self.hiddenInput, value: value, stack: fontFamily});

			});

		}

		this.initSorting = function(context) {

			var self = this;

			context.find('.fonts-search select').bind('change', function() {

				var sortBy = $(this).val();
				self.retrieveRemoteFonts($(this).parents('.tab-content'), sortBy, true);
		        
			});

		}

		this.changeToSelectedFontProviderTab = function() {

			var value = this.hiddenInput.val();

			/* Traditional font  */
			if ( !value || !value.match(/\|/g) ) {

				var tab = this.browser.find('#traditional-fonts');
				var selectedFont = tab.find('li[data-value="' + value + '"]');

				if ( selectedFont.length ) {

					selectedFont.addClass('selected-font');

					/* For some reason the selectedFont element isn't visible immediately so the position is wrong thus the use of timeout */
					setTimeout(function() {
						tab.find('.fonts-list ul').scrollTop(selectedFont.position().top);
					}, 100);

				}

			/* Web Font */
			} else {

				var fragments = value.split('|');

				selectTab(+ fragments[0] + '-fonts', this.browser);

			}

		}

	}
/* End fonts input object */

/* Opening and closing */
	fontBrowserOpen = function(event) {

		var fontBrowser = $(this).siblings('.font-browser');	
		var inputContainerOffset = $(this).parents('.design-editor-property-font-family').offset();
		
		fontBrowser.css({
			top: inputContainerOffset.top - fontBrowser.outerHeight(true),
			left: inputContainerOffset.left
		});

		/* Check that font browser isn't bleeding over right--if it is, fix it */
		var fontBrowserLeftOffset = parseInt(fontBrowser.css('left').replace('px', ''));
		var fontBrowserRightPos = fontBrowserLeftOffset + fontBrowser.outerWidth(true);

		if ( fontBrowserRightPos > $(window).width() ) {

			var fontBrowserRightOverflow = $(window).width() - fontBrowserRightPos;

			fontBrowser.css({
				left: fontBrowserLeftOffset + fontBrowserRightOverflow - 20
			});

		}
		
		/* Keep the sub tabs content container from scrolling */
		$('div.sub-tabs-content-container').css('overflow-y', 'hidden');
		
		/* Setup browser */
			if ( fontBrowser.data('setup') !== true ) {

				fontBrowser.data('obj', new fontBrowserObj(fontBrowser));
				fontBrowser.data('obj').setup();

				fontBrowser.data('setup', true);

			}

		/* Show browser */
			if ( fontBrowser.data('visible') !== true ) {
			
				/* Show the font browser */
				fontBrowser.fadeIn(150);
				fontBrowser.data('visible', true);
			
				/* Bind the document close */
				$(document).bind('mousedown', {fontBrowser: fontBrowser}, fontBrowserClose);
				Padma.iframe.contents().bind('mousedown', {fontBrowser: fontBrowser}, fontBrowserClose);
				
				$(window).bind('resize', {fontBrowser: fontBrowser}, fontBrowserClose);
			
		/* Hide browser */
			} else {
				
				/* Hide the font browser */
				fontBrowser.fadeOut(150);
				fontBrowser.data('visible', false);
				
				/* Allow sub tabs content container to scroll again */
				$('div.sub-tabs-content-container').css('overflow-y', 'auto');

				/* Remove the events */
				$(document).unbind('mousedown', fontBrowserClose);
				Padma.iframe.contents().unbind('mousedown', fontBrowserClose);
				
				$(window).unbind('resize', fontBrowserClose);
				
			}

	}

	fontBrowserClose = function(event) {
				
		/* Do not trigger this if they're clicking the same button that they used to open the multi-select */
		if ( $(event.target).parents('.design-editor-property-font-family').length === 1 )
			return;
		
		var fontBrowser = event.data.fontBrowser;
		
		/* Hide the font browser */
		fontBrowser.fadeOut(150);
		fontBrowser.data('visible', false);
		
		/* Allow sub tabs content container to scroll again */
		$('div.sub-tabs-content-container').css('overflow-y', 'auto');
		
		/* Remove the events */
		$(document).unbind('mousedown', fontBrowserClose);
		Padma.iframe.contents().unbind('mousedown', fontBrowserClose);
		
		$(window).unbind('resize', fontBrowserClose);
		
	}
/* End opening and closing functions */

/* Web Font Quick load for loading just one font */
	webFontQuickLoad = function(font) {

		/* Not a web font */
		if ( !font.match(/\|/g) )
			return;

		var fragments 		= font.split('|');
		var fontOriginal 	= font;
		var provider 		= fragments[0];
		var font 			= fragments[1];
		var variants 		= '';

		if ( typeof fragments[2] != 'undefined' && fragments[2] )
			var variants = ':' + fragments[2];

		var args = {
			fontactive: function(fontFamily, fontDescription) {
				jQuery("span.font-name[data-webfont-value='" + fontOriginal + "']").animate({opacity: 1});
			}
		};

		args[provider] = {
			families: [font + variants]
		};

		return WebFont.load(args);

	}
/* End quick load */


})(jQuery);