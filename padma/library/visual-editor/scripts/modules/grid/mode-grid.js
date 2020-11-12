define(['jquery', 'modules/grid/grid', 'deps/itstylesheet', 'modules/grid/wrappers', 'modules/grid/grid-manager', 'deps/jquery.pep', 'helper.blocks', 'helper.wrappers'], function($, puGrid, wrappers) {

	var modeGrid = {
		init: function() {	
			
			bindGridManager();


			/**
			 *
			 * Filter and categories for select block type
			 *
			 */
			 var blockTypeSelectorFilterReset = function(){
				$('.block-type-selector .block-type').show();
				$('.block-type-selector-filter-categories li a').removeClass('active');
				$('.block-type-selector-filter-categories li:first-child a').addClass('active');
				$('#block-type-selector-filter-text').val('');
				$('#block-type-selector-filter-text').focus();
			}

			/*
				filter Reset
			*/
			$(document).on('click','.block-type-selector-filter-reset',function(){
				blockTypeSelectorFilterReset();
			});

			/**
			 *
			 * Filter per categorie
			 *
			 */
			 $(document).on('click','.block-type-selector-filter-categories li a',function(){
				var categorie = $(this).data('filter');
				if(categorie == 'all'){
					blockTypeSelectorFilterReset();
				}else{
					$('.block-type-selector .block-type:not(#get-more-blocks)').hide();
					$('.block-type-selector .block-type.filter-' + categorie).show();
				}
				$('.block-type-selector-filter-categories li a').removeClass('active');
				$(this).addClass('active');
			});



			/*
				Select block type: search + filter
			*/
			$(document).on('keyup','#block-type-selector-filter-text',function(){

				var string  = $(this).val();				
				var options = $('.block-type-selector .block-type');

				if(string.length == 0){
					blockTypeSelectorFilterReset();
					return;
				}
				
				options.each(function(index){
					
					var property_id = $(this).attr('id').toString();
					var target 		= $(this);					

					if( property_id == undefined  || property_id == 'get-more-blocks'){
						//console.log(options[index]);

					}else{
						
						if ( property_id.indexOf(string) !== -1) {

							target.removeClass('hidden');
							target.show();

						}else{
							target.addClass('hidden');
							target.hide();
						}
					}

				});

			
			});

			

		},

		iframeCallback: function() {

			/* Load block content */
			$i('.block').each(function() {

				updateBlockContentCover($(this));

				loadBlockContent({
					blockElement: $(this),
					blockOrigin: getBlockID($(this))
				});

			});
			
			/* Initialize Grid Stylesheet */
			gridStylesheet = new ITStylesheet({document: Padma.iframe.contents()[0], href: '/?padma-trigger=compiler&file=ve-iframe-grid-dynamic'}, 'find');

			addEdgeInsertWrapperButtons();

			addWrapperButtons($i('div.wrapper'));
			bindWrapperButtons();

			setupWrapperSortables();
			setupWrapperResizable();
			setupWrapperContextMenu();

			assignDefaultWrapperID();

			/* If this is a new layout and there are no blocks, then set the Grid Container on the (only) wrapper to 500px */
				if ( $i('.grid-container').length === 1 && !$i('.block').length ) {
					$i('.grid-container').height(500);
				}

			/* Initiate Padma Grid */
			$i('div.wrapper').padmaGrid();

				/* Disable Grid on mirrored wrappers */
				$i('div.wrapper-mirrored').padmaGrid('disable');

			/* Update Default Grid Width Input */
				updateGridWidthInput('#sub-tab-grid-content');

			setupBlockContextMenu();
			bindBlockDimensionsTooltip();

		},

	}

	updateGridCSS = function(wrapperCSSSelector, columns, columnWidth, gutterWidth, gridWidthInputContext) {
		
		/* Calculate Grid Width */
			if ( gutterWidth > 0 ) {
				var gridWidth = (columnWidth * columns) + ((columns - 1) * gutterWidth);
			} else {
				var gridWidth = (columnWidth * columns);
			}

		/* Calculate percentages for column widths and margins */		
			var ratioColumnWidth = (columnWidth * columns) / gridWidth;
			var ratioGutterWidth = (gutterWidth * columns) / gridWidth;

			var singlePercentageColumnWidth = (100 / columns) * ratioColumnWidth;

			if ( ratioGutterWidth > 0 ) {
				var singlePercentageGutterWidth = (100 / columns) * ratioGutterWidth;
			} else {
				var singlePercentageGutterWidth = 0;
			}

		/* Define round precision in one place so it can be changed if necessary */
			var roundPrecision = 9;

		/* Wrapper CSS Prefix that way these changes don't modify other wrappers */
			var wrapperCSSPrefix = wrapperCSSSelector + ' ';

		/* Send calculated percentages to CSS */
			/* Grid Guides */
				gridStylesheet.update_rule(wrapperCSSPrefix + '.grid-guides .grid-guide', {margin: '0 0 0 ' + Math.round(singlePercentageGutterWidth, roundPrecision) + '%'});

			/* Grid Width/Grid Left Classes */
				for ( i = 1; i <= columns; i++ ) {

					gridStylesheet.update_rule(wrapperCSSPrefix + '.grid-width-' + i, {width: Math.round((singlePercentageColumnWidth * i + ((i - 1) * singlePercentageGutterWidth)), roundPrecision) + '%'});
					gridStylesheet.update_rule(wrapperCSSPrefix + '.grid-left-' + i, {left: '0 0 0 ' + Math.round(((singlePercentageColumnWidth + singlePercentageGutterWidth) * i), roundPrecision) + '%'});
					
				}

			/* Grid Container */
				gridStylesheet.update_rule(wrapperCSSPrefix + 'div.grid-container', {width: (gridWidth + 1) + 'px'});

			/* Wrapper */
				gridStylesheet.update_rule(wrapperCSSSelector + '.wrapper-fixed', {width: (gridWidth) + 'px'});

		/* Update Grid Width Read-Only Input If Present */
			if ( typeof gridWidthInputContext != 'undefined' && gridWidthInputContext.length )
				updateGridWidthInput(gridWidthInputContext);

	}

	return modeGrid;

});