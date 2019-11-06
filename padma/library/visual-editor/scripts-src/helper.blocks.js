define(['modules/panel.inputs', 'helper.history'], function(panelInputs, history) {

	getBlockByID = function(id) {

		var id = id.toString().replace('block-', '');

		return $i('.block[data-id="' + id + '"]');

	}


	getBlock = function(element) {
		//If invalid selector, do not go any further
		if ( $(element).length === 0 )
			return $();
		
		//Find the actual block node
		if ( $(element).hasClass('block') ) {
			block = $(element);
		} else if ( $(element).parents('.block').length === 1 ) {
			block = $(element).parents('.block');
		} else {
			block = false;
		}
		
		return block;
	}


	getBlockID = function(element) {

		var block = getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		//Pull out ID
		return block.data('id');

	}


	getBlockWrapper = function(element) {

		var block = getBlock(element);

		return block.closest('.wrapper');

	}


	getBlockType = function(element) {
		
		var block = getBlock(element);
		
		if ( !block ) {
			return false;
		}

		return block.data('type');
	}

	getBlockInlineEditableFields = function(element) {
		
		var block = getBlock(element);
		
		if ( !block ) {
			return false;
		}

		return block.data('inline-editable');
	}

	getBlockInlineEditableFieldValue = function(field,element) {

		var block = getBlock(element);
		
		if ( !block ) {
			return false;
		}

		var blockID = getBlockID(element);
		var content = $.ajax(Padma.ajaxURL, {
			async: false,
			cache: false,
			type: 'POST',
			dataType: 'text',
			data:{
				security: Padma.security,
				action: 'padma_visual_editor',
				method: 'load_block_editable_field_content',
				field: field,
				block_id: blockID,
			},
			success: function(data) {
				
			}
		}).done(function(data) {
			
			return data;
		});

		return content.responseText;

	}

	saveBlockInlineEditableFieldValue = function(blockID, field, content_to_save) {

		var block = getBlockByID(blockID);
		
		if ( !block ) {
			return false;
		}

		var content = $.ajax(Padma.ajaxURL, {
			async: false,
			cache: false,
			type: 'POST',
			dataType: 'text',
			data:{
				security: Padma.security,
				action: 'padma_visual_editor',
				method: 'save_block_editable_field_content',
				field: field,
				block_id: blockID,
				content: content_to_save,
			},
			success: function(data) {
				
			}
		}).done(function(data) {
			
			return data;
		});

		return content.responseText;

	}


	getBlockTypeNice = function(type) {
		
		if ( typeof type != 'string' ) {
			return false;
		}
		
		return getBlockTypeObject(type).name;
		
	}


	getBlockTypeIcon = function(blockType, blockInfo) {
		
		if ( typeof blockInfo == 'undefined' )
			blockInfo = false;
			
		if ( typeof Padma.allBlockTypes[blockType] != 'object' )
			return null;
			
		if ( blockInfo === true )
			return Padma.blockTypeURLs[blockType] + '/icon-white.svg';
			
		return Padma.blockTypeURLs[blockType] + '/icon.svg';
		
	}


	getBlockTypeObject = function(blockType) {
		
		var blockTypes = Padma.allBlockTypes;
		
		if ( typeof blockTypes[blockType] === 'undefined' )
			return {'fixed-height': false};
		
		return blockTypes[blockType];
		
	}


	getBlockGridWidth = function(element) {
		
		var block = getBlock(element);
		
		if ( !block ) {
			return false;
		}
			    		
		return block.attr('data-width');
		
	}


	setBlockGridWidth = function(element, gridWidth) {

		var block = getBlock(element);

		if ( !block ) {
			return false;
		}

		var previousGridWidth = block.attr('data-width');

		/* Remove previous grid width */
		if ( previousGridWidth )
			block.removeClass('grid-width-' + previousGridWidth);

		/* Set new grid width */
			block.css('width', '');
			block.addClass('grid-width-' + gridWidth);
			
			block.attr('data-width', String(gridWidth).replace('grid-width-', ''));
		
		return block;

	}


	getBlockGridLeft = function(element) {
		
		var block = getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		return block.attr('data-grid-left');
		
	}


	setBlockGridLeft = function(element, gridLeft) {

		var block = getBlock(element);

		if ( !block ) {
			return false;
		}

		var previousGridLeft = getBlockGridLeft(block);

		/* Remove previous grid left */
			if ( previousGridLeft )
				block.removeClass('grid-left-' + previousGridLeft);

		/* Set new grid left */
			block.addClass('grid-left-' + gridLeft);
			block.attr('data-grid-left', String(gridLeft).replace('grid-left-', ''));

		return block;

	}


	getBlockDimensions = function(element) {
		
		var block = getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		return {
			width: getBlockGridWidth(block),
			height: block.attr('data-height')
		}
		
	}


	getBlockDimensionsPixels = function(element) {
		
		var block = getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		return {
			width: block.width(),
			height: block.height()
		}
		
	}


	getBlockPosition = function(element) {
		
		var block = getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		return {
			left: getBlockGridLeft(block),
			top: block.attr('data-grid-top')
		}
		
	}


	getBlockPositionPixels = function(element) {

		var block = getBlock(element);
		
		if ( !block ) {
			return false;
		}
		
		return {
			left: block.position().left,
			top: block.position().top
		}

	}


	isBlockMirrored = function(element) {
		
		var block = getBlock(element);
		
		return block.hasClass('block-mirrored');
		
	}


	getBlockMirrorOrigin = function(element) {
		
		var block = getBlock(element);
		
		if ( !isBlockMirrored(block) )
			return false;
			
		return block.data('block-mirror');
		
	}


	getBlockMirrorLayoutName = function(element) {

		var block = getBlock(element);
		
		if ( !isBlockMirrored(block) )
			return false;
			
		return block.data('block-mirror-layout-name');

	}


	updateBlockContentCover = function(block) {

		if ( Padma.mode != 'grid' )
			return false;

		/* Delete previous block content cover if it exists. */
		block.children('.block-content-cover').remove();

		var blockType = getBlockType(block);
		var blockTypeNice = getBlockTypeNice(blockType);
		var blockID = '';

		if ( block.data('temp-id') )
			blockID = ' <span>Unsaved</span>';

		if ( !blockTypeNice )
			blockTypeNice = 'Select Block Type';

		if ( block.data('alias') )
			blockTypeNice = block.data('alias') + ' - ' + blockTypeNice;

		/* If grid mode then add a layer that makes sure the dragging still works as expected */
		block.append('\
				<div class="block-content-cover">\
					<svg xmlns="http://www.w3.org/2000/svg" version="1.1">\
						<line class="x-line" x1="0" y1="0" x2="100%" y2="100%" />\
						<line class="x-line" x1="0" y1="100%" x2="100%" y2="0" />\
					</svg>\
					\
					<span class="block-type-and-id">' + blockTypeNice + blockID + '</span>\
				</div>\
			');
		
		return block.find('div.block-content-cover');

	}


	loadBlockContent = function(args) {
		
		var settings = {};
		
		var defaults = {
			blockElement: false,
			blockSettings: {},
			blockOrigin: false,
			blockDefault: false,
			callback: function(args){},
			callbackArgs: null
		};
		
		$.extend(settings, defaults, args);
				
		var blockContent = settings.blockElement.find('div.block-content');
		var blockType = getBlockType(settings.blockElement);

		if ( Padma.gridSafeMode == 1 )
			return blockContent.html('<div class="alert alert-red block-safe-mode"><p>Grid Safe mode enabled.  Block content not outputted.</p></div>');
		
		if ( Padma.mode == 'grid' && !getBlockTypeObject(blockType)['show-content-in-grid'] ) {

			if ( typeof settings.callback == 'function' )
				settings.callback(settings.callbackArgs);

			return blockContent.html('');

		}	
			
		createCog(blockContent, true, true, Padma.iframe.contents(), 1);

		return $.ajax({
			url: Padma.ajaxURL,
			cache: false,
			type: 'POST',
			dataType: 'text',
			data: {
				security: Padma.security,
				action: 'padma_visual_editor',
				method: 'load_block_content',
				unsaved_block_settings: settings.blockSettings,
				block_origin: settings.blockOrigin,
				block_default: settings.blockDefault,
				layout: Padma.viewModels.layoutSelector.currentLayout(),
				mode: Padma.mode,
				wpQueryVars: typeof Padma.iframe[0].contentWindow.PADMA_WP_Query_Vars != 'undefined' ? Padma.iframe[0].contentWindow.PADMA_WP_Query_Vars : null
			}
		}).done(function(data) {
			
			if ( typeof settings.callback == 'function' )
				settings.callback(settings.callbackArgs);

			/* Remove script tags from Grid mode */
				if ( Padma.mode == 'grid' ) {

					var data = data.replace(/script/g, 'SCRIPTTOCHECK');

					var content = $($.parseHTML(data));
					
					content.find('SCRIPTTOCHECK').remove();

				} else {

					var content = $(data);

				}
			/* End removing script tags from grid mode */

			if ( typeof window.frames['content'].jQuery != 'undefined' && window.frames['content'].jQuery('#block-' + getBlockID(settings.blockElement)).html(content).length ) {

				if ( Padma.mode == 'design' )
					refreshInspector();

				return window.frames['content'].jQuery('#block-' + getBlockID(settings.blockElement));

			}

			/* Re-initiate inspector to make sure the block elements are still editable */
			blockContent.html(content);

			if ( Padma.mode == 'design' )
				refreshInspector();

			return blockContent;

		});
		
	}


	refreshBlockContent = function(blockID, callback, args, throttled) {

		if ( typeof blockID == 'undefined' || !blockID )
			return false;

		if ( typeof throttled == 'undefined' )
			var throttled = true;

		/* Setup throttledFunction */
			var throttledFunction = function() {


				var blockElement 		= $i('.block[data-id="' + blockID + '"]');
				if(typeof GLOBALunsavedValues !== 'undefined'){
					var newBlockSettings 	= GLOBALunsavedValues['blocks'][blockID]['settings'];					
				}				
				var blockOrigin 		= blockElement.data('duplicateOf') ? blockElement.data('duplicateOf') : blockID;
				
				/* Update the block content */
				loadBlockContent({
					blockElement: blockElement,
					blockSettings: {
						settings: newBlockSettings,
						dimensions: getBlockDimensions(blockElement),
						position: getBlockPosition(blockElement)
					},
					blockOrigin: blockOrigin,
					blockDefault: {
						type: getBlockType(blockElement),
						id: 0,
						layout: Padma.viewModels.layoutSelector.currentLayout()
					},
					callback: callback,
					callbackArgs: args
				});

			}

			if ( !throttled ) {
				return throttledFunction();
			}

		/* Flood Control */
			if ( typeof updateBlockContentFloodTimeoutAfter != 'undefined' )
				clearTimeout(updateBlockContentFloodTimeoutAfter);

			if ( typeof updateBlockContentFloodTimeout == 'undefined' ) {

				throttledFunction.call();

				updateBlockContentFloodTimeout = setTimeout(function() {
					
					delete updateBlockContentFloodTimeout;
					
				}, 500);

			} else {

				updateBlockContentFloodTimeoutAfter = setTimeout(function() {

					throttledFunction.call();

					delete updateBlockContentFloodTimeoutAfter;

				}, 600);
				
			}
			
	}


	setupBlockContextMenu = function(showDelete) {

		if ( typeof showDelete == 'undefined' )
			var showDelete = true;

		setupContextMenu({
			id: 'block',
			elements: '.block:visible',
			title: function(event) {

				var block 				= getBlock(event.currentTarget);
				var blockID 			= getBlockID(block);
				var blockType 			= getBlockType(block);	
				var blockTypeNice 		= blockType ? getBlockTypeNice(blockType) + ' ' : '';
				var blockTypeIconURL 	= getBlockTypeIcon(blockType, true);
				var blockTypeIconStyle 	= blockTypeIconURL ? ' style="background-image:url(' + blockTypeIconURL + ');"' : null;
			
				return '<span class="type type-' + blockType + '" ' + blockTypeIconStyle + '></span>' + blockTypeNice + 'Block';

			},
			contentsCallback: function(event) {

				var contextMenu 			= $(this);
				var block 					= getBlock(event.currentTarget);
				var blockID 				= getBlockID(block);
				var blockType 				= getBlockType(block);	
				var blockTypeNice 			= getBlockTypeNice(blockType);
				var contextMenuClickEvent 	= !Padma.touch ? 'click' : 'touchstart';

				/* Block options */
					$('<li class="context-menu-block-options"><span>Open Block Options</span></li>').appendTo(contextMenu).on(contextMenuClickEvent, function() {
						openBlockOptions(block);
					});

				/* Switch block type */
					if ( Padma.mode == 'grid' ) {

						$('<li class="context-menu-block-switch-type"><span>Switch Block Type</span></li>').appendTo(contextMenu).on(contextMenuClickEvent, function() {
							openBlockTypeSelector(block);
						});

					}

				/* Duplicate block type */
					if ( Padma.mode == 'grid' ) {

						$('<li class="context-menu-block-duplicate"><span>Duplicate Block</span></li>').appendTo(contextMenu).on(contextMenuClickEvent, function() {
							duplicateBlock(block);
						});

					}

				/* Set Block Alias */
					$('<li class="context-menu-set-alias"><span>Set Block Alias</span></li>').appendTo(contextMenu).on(contextMenuClickEvent, function() {

						var blockAlias = prompt('Please enter the desired block alias.', block.data('alias'));

						if ( !blockAlias )
							return;

						dataSetBlockOption(getBlockID(block), 'alias', blockAlias);
						block.data('alias', blockAlias)
						updateBlockContentCover(block);

					});

				/* Unmirror Block */
					if ( isBlockMirrored(block) ) {

						$('<li class="context-menu-block-unmirror"><span>Unmirror Block</span></li>').appendTo(contextMenu).on(contextMenuClickEvent, function() {
							if ( !confirm("Are you sure you want to unmirror this block?\n\nIt will no longer copy the options and styling from the block it is currently mirroring.") )
								return;

							updateBlockMirrorStatus(false, block, '');
							dataSetBlockOption(getBlockID(block), 'mirror-block', '');
							reloadBlockOptions(getBlockID(block));

						});

					}

				/* Delete block */
					if ( Padma.mode == 'grid' ) {

						$('<li class="context-menu-block-delete"><span>Delete Block</span></li>').appendTo(contextMenu).on(contextMenuClickEvent, function(event) {
							
							if ( !confirm('Are you sure you want to delete this block?') )
								return false;

							deleteBlock(block);

						});

					}

			}
		});

	}

		
		bindBlockInlineEditor = function() {

			if ( Padma.touch )
				return false;
			
			//return false;
			$i('body').delegate('.block', 'dblclick', function(event) {

				// clean all editors
				$i('.dynamic-inline-edit .cancel-edit').click();

				var element = $(event.target).closest('.inspector-element');				
				var clases = element.attr('class').split(' ');
				var editableFields = getBlockInlineEditableFields(this.closest('.block')).split(',');
				var should_exit = false;


				if(editableFields.length < 1){
					return false;
				}				

				// target contains a editable item class 
				if( ! editableFields.some(r=> clases.includes(r)) ){

					var element = $(event.target);
					var clases = element.attr('class').split(' ');

					if( ! editableFields.some(r=> clases.includes(r)) ){
						should_exit = true;
					}
				}

				var blockType 	= getBlock(element)[0].dataset.type;
				if(blockType == 'content'){
					showNotification({
						id: 'open-content-editor',
						message: 'To edit Pages or Post content please right click the content block and use the option "Edit Content"',
						closeTimer: 3000
					});
				}


				if(should_exit)
					return;


				var field = editableFields.filter(o=> clases.includes(o))[0];					
				var blockID = getBlockID(this);
				var editableFieldValue = getBlockInlineEditableFieldValue(field,this);				

				
				var style = 'font-size: ' + element.css('font-size') + ';';
					style += 'color: ' + element.css('color') + ';';
					style += 'height: ' + element.css('height') + ';';
					style += 'width: ' + element.css('width') + ';';
					style += 'text-align: ' + element.css('text-align') + ';';
					style += 'background-color: ' + element.css('background-color') + ';';

				window.element = element;

				var html = '<div class="dynamic-inline-edit">';
					html += '<textarea rows="1" id="dynamic-inline-edit-'+blockID+'" style="'+style+'">'+editableFieldValue+'</textarea>';
					html += '<a class="cancel-edit" data-cancel="'+blockID+':'+field+'">❌</a>';
					html += '<a class="finish-edit" data-save="'+blockID+':'+field+'">✔</a>';
					html += '</div>';

				element.hide();
				element.after(html);
				$i('#dynamic-inline-edit-'+blockID).focus();
				
			});

			$i('body').delegate('.block .dynamic-inline-edit a.cancel-edit', 'click', function(event) {
				
				var blockID = getBlockID(this);				
				var field = $i(this).data('cancel').split(':')[1];
				var element = $i(this).closest('.' + blockID + ' .'+field);
				
				element.show();
				$i('#dynamic-inline-edit-'+blockID).remove();
				refreshBlockContent(blockID);

			});

			$i('body').delegate('.block .dynamic-inline-edit a.finish-edit', 'click', function(event) {
								
				var blockID = getBlockID(this);
				var field = $i(this).attr('data-save').split(':')[1];
				var content = $i('#dynamic-inline-edit-'+blockID).val();				
				
				saveBlockInlineEditableFieldValue( blockID, field, content );
				$i('#dynamic-inline-edit-'+blockID).remove();
				refreshBlockContent(blockID);
				reloadBlockOptions(blockID);
			});


			$i('body').delegate('.dynamic-inline-edit textarea','keydown',function(event){				
				var id = $i(this).attr('id').split('-')[3];
				if (event.keyCode == 13) {
					$i('a[data-save*="'+id+'"]').click();
				}else{
					if (event.keyCode == 27) {
						$i('a[data-cancel*="'+id+'"]').click();
					}
				}
			});


		}


		bindBlockDimensionsTooltip = function() {

			if ( Padma.touch )
				return false;
			
			$i('body').delegate('.block', 'mouseenter', function(event) {
					
				var self = this;	
				var firstSetup = typeof $(this).data('qtip') == 'undefined' ? true : false;

				if ( typeof Padma.disableBlockDimensions !== 'undefined' && Padma.disableBlockDimensions )
					return false;
					
				if ( firstSetup ) {

					addBlockDimensionsTooltip($(this));
					
					$(this).data('hoverWaitTimeout', setTimeout(function() {

						$(self).qtip('reposition');
						$(self).qtip('show');

						if ( typeof $(self).data('qtip') != 'undefined' )
							$i('#qtip-' + $(self).data('qtip').id).show();

					}, 300));
					
				}
							
			});
			
			$i('body').delegate('.block', 'mouseleave', function(event) {
				
				clearTimeout($(this).data('hoverWaitTimeout'));
							
			});

		}


		addBlockDimensionsTooltip = function(block) {

			if ( Padma.touch )
				return false;

			$(block).qtip({
				style: {
					classes: 'qtip-padma qtip-block-dimensions'
				},
				position: {
					my: 'top center',
					at: 'bottom center',
					container: $i('body'),
					viewport: $i('#padma-tooltip-container'),
					effect: false
				},
				show: {
					delay: 300,
					solo: true,
					effect: false
				},
				hide: {
					delay: 25,
					effect: false
				},
				content: {
					text: blockDimensionsTooltipContent
				}
			});

			return $(block).qtip('api');
						
		}


			blockDimensionsTooltipContent = function(api) {

				var block = getBlock(this);
				var blockID = getBlockID(block);

				var blockWidth = getBlockDimensionsPixels(block).width;	
				var blockHeight = getBlockDimensionsPixels(block).height;					
				var blockType = getBlockType(block);
				
				/* Block Info (only if existing block) */
					if ( typeof blockType != 'undefined' ) {

						var blockTypeNice = getBlockTypeNice(blockType);
						var blockTypeIconURL = getBlockTypeIcon(blockType, true);
						var blockTypeIconStyle = blockTypeIconURL ? ' style="background-image:url(' + blockTypeIconURL + ');"' : null;

						var blockAlias = block.data('alias') ? ': ' + block.data('alias') : '';
						var blockMirroredLayoutName = getBlockMirrorLayoutName(block) ? ' from ' + getBlockMirrorLayoutName(block) : '';

						var blockMirrored = isBlockMirrored(block) ? '<span class="block-info-mirroring">Mirroring' + blockMirroredLayoutName + '</span>' : '';
						var mainBlockInfoClass = isBlockMirrored(block) ? 'main-block-info main-block-info-mirrored' : 'main-block-info';

						var blockInfo = '<div class="block-info">' +
								'<span class="block-info-type" ' + blockTypeIconStyle + '></span>' +
								'<span class="' + mainBlockInfoClass + '">' +
									blockTypeNice + ' ' + blockAlias +
								'</span>' + 
								blockMirrored + 
							'</div>';

					} else {

						var blockInfo = '';

					}

				/* Block Dimensions */
					if ( getBlockTypeObject(blockType)['fixed-height'] ) {
					
						var blockHeight = blockHeight;
						var heightText = 'Height';
					
					} else {
					
						var blockHeight = Padma.mode == 'grid' ? blockHeight : block.css('minHeight').replace('px', '');
						var heightText = 'Min. Height';
					
					}
				
					var height = '<span class="block-height"><strong>' + heightText + ':</strong> ' + blockHeight + '<small>px</small></span>';
					var width = '<span class="block-width"><strong>Width:</strong> ' + blockWidth + '<small>px</small></span>';

					//Show different width info if it's responsive
					if ( $('#input-enable-responsive-grid label.checkbox-checked').length == 1 || (Padma.mode != 'grid' && Padma.responsiveGrid) )
						var width = '<span class="block-width"><strong>Max Width:</strong> <small>~</small>' + blockWidth + '<small>px</small></span>';

					var fluidMessage = !getBlockTypeObject(blockType)['fixed-height'] ? '<span class="block-fluid-height-message">Height will auto-expand</span>' : '';

				/* Output */
				return blockInfo + width + ' <span class="block-dimensions-separator">&#9747;</span> ' + height + fluidMessage + '<span class="right-click-message">Right-click to open block options</span>' ;

			}

		currentBlockInfo = function(block){			

			var blockType 	= getBlockType(block);
			var target 		= $('.current-block-info');
			
			var blockTypeNice 		= getBlockTypeNice(blockType);
			var blockTypeIconURL 	= getBlockTypeIcon(blockType, true);
			var blockTypeIconStyle 	= blockTypeIconURL ? ' style="background-image:url(' + blockTypeIconURL + ');"' : null;
			var mainBlockInfoClass 	= isBlockMirrored(block) ? 'main-block-info main-block-info-mirrored' : 'main-block-info';
			var blockAlias 			= block.data('alias') ? ': ' + block.data('alias') : '';					
			var blockInfo 			= '<span class="block-info-type" ' + blockTypeIconStyle + '></span>' +
										'<span class="' + mainBlockInfoClass + '">' +
											blockTypeNice + ' ' + blockAlias +
										'</span>';

			target.empty().append(blockInfo);
		

		}

		deleteSelectedBlock =  function(){
			
			var blocks = $i('body').find('.currently-selected');

			if(blocks.length > 0 ){
				if(blocks.length == 1 ){
					var block = getBlock(blocks[0]);
					if(confirm('Are you sure you want to delete this block?')){
						deleteBlock(block);
					}
				}else{
					if(confirm('Are you sure you want to delete all these blocks?')){
						blocks.each(function(){
							var block = getBlock($(this))
							deleteBlock(block);
						});
					}
				}			
			}
		}

	openBlockOptions = function(block, subTab) {

		if ( typeof block.target != 'undefined' || !block )
			var block = getBlock(this);

		if ( typeof subTab == 'undefined' )
			var subTab = null;

		if ( !block || block.hasClass('block-type-unknown') )
			return false;

		var blockID 		= getBlockID(block);		    
		var blockType 		= getBlockType(block);		
		var blockTypeName 	= getBlockTypeNice(blockType);


		var readyTabs = function() {
			
			var tab = $('div#block-' + blockID + '-tab');
			
			/* Ready tab, sliders, and inputs */
			tab.tabs();
			panelInputs.bind('div#block-' + blockID + '-tab');
			
			/* Refresh tooltips */
			setupTooltips();
			
			/* Call the open callback for the box panel */
			var openJsCallback 	= tab.find('ul.sub-tabs').attr('data-open-js-callback');
			var callback 		= null

			try{
				callback = eval(openJsCallback);				
			}catch(e){				
				callback = openJsCallback;
			}finally{
				callback = function(){}
			}
			if ( typeof callback == 'function' ) {
				callback({
					block: block,
					blockID: blockID,
					blockType: blockType
				});
			}

			/* Show and hide elements based on toggle options */
			handleInputTogglesInContainer(tab.find('div.sub-tabs-content'));

			/* If subTab is defined, switch to that subTab */
			if ( subTab )
				selectTab(subTab, $('div#block-' + blockID + '-tab'));
			
			/* If it's a mirrored block, then hide the other tabs */
			if ( $('div#block-' + blockID + '-tab').find('select#input-' + blockID + '-mirror-block').val() != '' ) {
				
				$('div#block-' + blockID + '-tab ul.sub-tabs li:not(#sub-tab-config)').hide();
				selectTab('sub-tab-config', $('div#block-' + blockID + '-tab'));
				
			}

			if( Padma.touch ){
				$('div#block-' + blockID + '-tab ul.sub-tabs').addClass('options-on-mobile')
				$('div#block-' + blockID + '-tab ul.sub-tabs').prepend('<li id="sub-tab-mobile-menu" class="touch-option ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="-1" ><a class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-6">Options</a></li>');
				$('div#block-' + blockID + '-tab ul.sub-tabs li:not(.touch-option)').removeClass('ui-state-active');
				
				$(document).on('click','ul.options-on-mobile li#sub-tab-mobile-menu',function(){
					$(this).parent().toggleClass('open');
				});

				$(document).on('click','ul.options-on-mobile li:not(#sub-tab-mobile-menu)',function(){
					$('ul.options-on-mobile').toggleClass('open');
				});
			}
			
		}

		var blockTypeIconURL 	= getBlockTypeIcon(blockType, true);
		var blockTypeIconStyle 	= blockTypeIconURL ? 'background-image:url(' + blockTypeIconURL + ');' : null;
		var blockTabName 		= blockTypeName + ' Block';

		if ( block.data('alias') && block.data('alias').length ) {
			blockTabName = block.data('alias');
		}

		addPanelTab('block-' + blockID, '<span class="block-type-icon" style="' + blockTypeIconStyle + '"></span>' + blockTabName, {
			url: Padma.ajaxURL, 
			data: {
				security: Padma.security,
				action: 'padma_visual_editor',
				method: 'load_block_options',
				block_type: blockType,
				block_id: blockID,
				duplicate_of: block.data('duplicateOf'),
				unsaved_block_options: getUnsavedBlockOptionValues(blockID),
				layout: Padma.viewModels.layoutSelector.currentLayout()
			}, 
			callback: function(){
				readyTabs();
			}
		}, true, true, 'block-type-' + blockType);

		$('div#panel').tabs('option', 'active', $('#panel-top').children('li[role="tab"]').index($('[aria-controls="block-' + blockID + '-tab"]')));

	}


		reloadBlockOptions = function(blockID, subTab) {


			/* Make sure the block options are open to begin with */
			if ( !$('ul#panel-top').find('[aria-controls="block-' + blockID + '-tab"]').length )
				return;

			/* If block ID isn't provided then just pull it from the active tab */ 
			if ( typeof blockID == 'undefined' || !blockID )
				var blockID = $('ul#panel-top > li.ui-state-active').attr('aria-controls').replace('block-', '').replace('-tab', '');

			/* Make sure we have a real block ID and it's not something like setup tab trying to be reloaded */
			if ( $('ul#panel-top > li.ui-state-active').attr('aria-controls').indexOf('block-') !== 0 )
				return false;

			if ( typeof subTab == 'undefined' || !subTab )
				var subTab = $('div#block-' + blockID + '-tab ul.sub-tabs .ui-state-active a').attr('href').replace('#', '');

			removePanelTab('block-' + blockID);
			
			return openBlockOptions(getBlockByID(blockID), subTab);

		}


		getUnsavedBlockOptionValues = function(blockID) {
					
			if ( 
				typeof GLOBALunsavedValues == 'object' && 
				typeof GLOBALunsavedValues['blocks'] == 'object' &&
				typeof GLOBALunsavedValues['blocks'][blockID] == 'object' &&
				typeof GLOBALunsavedValues['blocks'][blockID]['settings'] == 'object' 
			)
				var unsavedBlockSettings = GLOBALunsavedValues['blocks'][blockID]['settings'];
				
			return (typeof unsavedBlockSettings == 'object' && Object.keys(unsavedBlockSettings).length > 0) ? unsavedBlockSettings : null;
			
		}
		

	openBlockTypeSelector = function(block) {

		var blockID = getBlockID(block);

		/* Create blank panel */
			removePanelTab('block-' + blockID);
			addPanelTab('block-' + blockID, 'Select Block Type', '', true, true);

			var tab = $('#block-' + blockID + '-tab');

		/* Clone block type selector in and bind it */
			var blockTypeSelector = $('.block-type-selector-original').clone()
				.removeClass('block-type-selector-original')
				.appendTo(tab)
				.show();

			blockTypeSelector.find('div.block-type').addClass('tooltip');
			setupTooltips();

			blockTypeSelector.find('div.block-type:not(#get-more-blocks)').bind('click', function(event) {	

				var blockType = $(this).attr('id').replace('block-type-', '');

				/* If new block then create it */
					if ( block.hasClass('blank-block') ) {
						
						block.parents('.wrapper').padmaGrid('setupBlankBlock', blockType);
					
				/* Otherwise we're switching an existing block's type */
					} else if ( confirm('Are you sure you wish to switch block types?  All settings for this block will be lost.') ) {
						
						var switchedBlockTypeBlockID = switchBlockType(block, blockType);

						blockID = switchedBlockTypeBlockID;
						
					}

				/* Hide all tooltips that way the tooltip doesn't continue showing for the hovered block type */
				$('.qtip').qtip('hide');

				/* Open options now */
				removePanelTab('block-' + blockID);
				openBlockOptions(getBlockByID(blockID));

			});
			/*
			blockTypeSelector.find('div.block-type#get-more-blocks').bind('click', function(event) {
				location.assign('https://www.padmaunlimited.com/how-to-get-more-blocks/');
			});*/



		/* Bind unfocus events */
			if ( block.hasClass('blank-block') ) {

				$('.wrapper').bind('mousedown', {block: block}, hideBlankBlockTypeSelector);

				$(document).bind('keyup.esc', {block: block}, hideBlankBlockTypeSelector);
				$i('html').bind('keyup.esc', {block: block}, hideBlankBlockTypeSelector);

				/* Make sure that when closing the block type selector with the tab close button on a blank block that the blank block is also removed. */
				$('ul#panel-top li a[href="#block-' + blockID + '-tab"]').siblings('span.close').bind('mouseup', {block: block}, hideBlankBlockTypeSelector);

			}		

		/* Select the tab */
			$('div#panel').tabs('option', 'active', $('#panel-top').children('li[role="tab"]').index($('[aria-controls="block-' + blockID + '-tab"]')));


		/*	Filter reset	*/

			$('.block-type-selector .block-type').show();
			$('.block-type-selector-filter-categories li a').removeClass('active');
			$('.block-type-selector-filter-categories li:first-child a').addClass('active');
			$('#block-type-selector-filter-text').focus();
		
		return;
		
	}


		hideBlankBlockTypeSelector = function(event) {

			if(event.type == 'keyup')
				return;

			var block = event.data.block;

			/* If blank block then unbind things and delete it.  Make sure that the block isn't being clicked inside of. */
				if ( block.hasClass('blank-block') && $(event.target).parents('.block').first().get(0) != $(block).get(0) ) {

					removePanelTab('block-' + getBlockID(block));

					block.remove();

					$('.wrapper').unbind('mousedown', hideBlankBlockTypeSelector);

					$(document).unbind('keyup.esc', hideBlankBlockTypeSelector);
					$i('html').unbind('keyup.esc', hideBlankBlockTypeSelector);

				}

			return true;
			
		}


		switchBlockType = function(block, blockType, loadContent) {
			
			var blockTypeIconURL 	= getBlockTypeIcon(blockType, true);			
			var oldType 			= getBlockType(block);
			var blockID 			= getBlockID(block);
			
			block.removeClass('block-type-' + oldType);
			block.addClass('block-type-' + blockType);
			block.data('type', blockType);

			if ( typeof loadContent == 'undefined' || loadContent ) {

				loadBlockContent({
					blockElement: block,
					blockOrigin: {
						type: blockType,
						id: 0,
						layout: Padma.viewModels.layoutSelector.currentLayout()
					},
					blockSettings: {
						dimensions: getBlockDimensions(block),
						position: getBlockPosition(block)
					},
				});

			}			

			//Set the fluid/fixed height class so the fluid height message is shown correctly
			if ( getBlockTypeObject(blockType)['fixed-height'] === true ) {
				
				block.removeClass('block-fluid-height');
				block.addClass('block-fixed-height');

				if ( block.css('min-height').replace('px', '') != '0' ) {

					block.css({
						height: block.css('min-height')
					});

				}
				
			} else {
				
				block.removeClass('block-fixed-height');
				block.addClass('block-fluid-height');

				if ( block.css('height').replace('px', '') != 'auto' ) {

					block.css({
						height: block.css('height')
					});

				}
				
			}
			
			//Set the hide-content-in-grid depending on the block type
			if ( !getBlockTypeObject(blockType)['show-content-in-grid'] ) {
				
				block.addClass('hide-content-in-grid');
				
			} else {
				
				block.removeClass('hide-content-in-grid');
				
			}

			//Remove block type unknown class
			block.removeClass('block-type-unknown');
			
			//Prepare for hiddens
			oldBlockID = blockID;
			var temporaryID = Math.ceil(Math.random() * 1000000000);

			//Update the ID on the block
			block
				.attr('id', 'block-' + temporaryID)
				.attr('data-id', temporaryID)
				.attr('data-temp-id', temporaryID)
				.data('id', temporaryID)
				.data('temp-id', temporaryID);

			//Delete the old block optiosn tab if it exists
			removePanelTab('block-' + oldBlockID);

			//Add hiddens to delete old block and add new block in its place
			dataDeleteBlock(oldBlockID);
			dataAddBlock(block);
			dataSetBlockPosition(temporaryID, getBlockPosition(block));
			dataSetBlockDimensions(temporaryID, getBlockDimensions(block));
			dataSetBlockWrapper(temporaryID, getBlockWrapper(block).attr('id'));

			//Update content overlay
			updateBlockContentCover(block);

			//Update mirroring status
			updateBlockMirrorStatus(false, block, '', false);
			
			//Allow saving now that the type has been switched
			allowSaving();

			/* Hide all tooltips that way the tooltip doesn't continue showing for the hovered block type */
			$('.qtip').qtip('hide');
			
			return temporaryID;
			
		}


	duplicateBlock = function(originalBlock) {

		if ( !$(originalBlock).length )
			return false;

		var wrapper = getBlockWrapper(originalBlock);

		var blockPosition = getBlockPosition(originalBlock);
		var blockDimensions = getBlockDimensions(originalBlock);

		var duplicateAlias = originalBlock.data('alias') ? originalBlock.data('alias') + ' Copy' : '';

		var duplicateOf = originalBlock.data('duplicateOf') ? originalBlock.data('duplicateOf') : getBlockID(originalBlock);

		var newBlockArgs = {
			type: getBlockType(originalBlock),
			top: parseInt(blockPosition.top) + 20,
			left: blockPosition.left,
			width: blockDimensions.width,
			height: blockDimensions.height,
			settings: {
				duplicateOf: duplicateOf,
				alias: duplicateAlias
			}
		};

		var newBlock = wrapper.data('ui-padmaGrid').addBlock(newBlockArgs);

		/* Send block to top */
		wrapper.data('ui-padmaGrid').sendBlockToTop(newBlock);

		/* Show alias immediately */
		if ( duplicateAlias ) {
			newBlock.data('alias', duplicateAlias);
			updateBlockContentCover(newBlock);
		}

		return newBlock;

	}


	duplicateBlockBetweenWrapper = function(originalBlock,wrapper) {

		if ( !$(originalBlock).length )
			return false;

		var blockPosition = getBlockPosition(originalBlock);
		var blockDimensions = getBlockDimensions(originalBlock);

		var duplicateAlias = originalBlock.data('alias') ? originalBlock.data('alias') + ' Copy' : '';

		var duplicateOf = originalBlock.data('duplicateOf') ? originalBlock.data('duplicateOf') : getBlockID(originalBlock);

		var newBlockArgs = {
			type: getBlockType(originalBlock),
			top: parseInt(blockPosition.top) + 20,
			left: blockPosition.left,
			width: blockDimensions.width,
			height: blockDimensions.height,
			settings: {
				duplicateOf: duplicateOf,
				alias: duplicateAlias
			}
		};

		var newBlock = wrapper.data('ui-padmaGrid').addBlock(newBlockArgs);

		/* Send block to top */
		wrapper.data('ui-padmaGrid').sendBlockToTop(newBlock);

		/* Show alias immediately */
		if ( duplicateAlias ) {
			newBlock.data('alias', duplicateAlias);
			updateBlockContentCover(newBlock);
		}

		return newBlock;
	}


	blockIntersectCheck = function(originBlock, container) {

		if ( typeof container == 'undefined' || !container )
			var container = block.parents('.grid-container').first();
		
		var intersectors = blockIntersectCheckCallback(originBlock, container.find('.block'));

		//If there are two elements in the intersection array (the original one will be included since we're doing a general '.block' search), then we throw an error
		if ( intersectors.length > 1 ) {	
			
			intersectors.addClass('block-error');

			var output = false;
			
		} else {
			
			//Set up variable for next loop
			var blockErrorCount = 0;

			//Since there could still be errors after this one if fixed, we must loop through all other blocks that have errors
			container.find('.block-error').each(function(){
				var intersectors = blockIntersectCheckCallback(this, container.find('.block'));

				if ( intersectors.length === 1 || !intersectors ) {
					$(this).removeClass('block-error');
				} else {
					blockErrorCount++;
				}
			});

			//If there aren't any touching blocks, then we can save.  Otherwise, we cannot.
			var output = ( blockErrorCount === 0 ) ? true : false;
			
		}

		/* If there are overlapping blocks, then show a red notice */
		if ( !output ) {

			Padma.overlappingBlocks = true;

			showErrorNotification({
				id: 'overlapping-blocks',
				message: 'There are <strong>overlapping blocks</strong>.<br />Please separate them before saving.',
				closeTimer: false
			});

		} else {

			Padma.overlappingBlocks = false;
			hideNotification('overlapping-blocks');

		}

		return output;

	}


		blockIntersectCheckCallback = function(targetSelector, intersectorsSelector) {
			
			if ( targetSelector == false || intersectorsSelector == false || !$(targetSelector).is(':visible') ) {
				return false;
			}
			
		    var intersectors = [];
		    var xTolerance = 5; /* Tolerance for when gutter width is very little */

		    var $target = $(targetSelector);
		    var tAxis = $target.offset();
		    var t_x = [tAxis.left, tAxis.left + $target.outerWidth()];
		    var t_y = [tAxis.top, tAxis.top + $target.outerHeight()];

		    $(intersectorsSelector).each(function() {

		          var $this = $(this);

		          if ( !$this.is(':visible') )
		          	return;

		          var thisPos = $this.offset();
		          var i_x = [thisPos.left, thisPos.left + $this.outerWidth()]
		          var i_y = [thisPos.top, thisPos.top + $this.outerHeight()];

		          if ( (t_x[0] + xTolerance) < i_x[1] && (t_x[1] - xTolerance) > i_x[0] &&
		               t_y[0] < i_y[1] && (t_y[1]) > i_y[0]) {
		              intersectors.push(this);
		          }

		    });
		
		    return $(intersectors);
		
		}


	deleteBlock = function(element) {

		if ( typeof element != 'object' )
			var element = $i('.block[data-id="' + element + '"]');

		var deleteBlockID = getBlockID(element);
		var deleteBlock = getBlock(element);
		var deleteBlockContainer = deleteBlock.parents('.grid-container');
		
		Padma.history.add({
			description: 'Deleted block',
			up: function() {

				//Get the container for the block intersect check

				//Remove the block!
				deleteBlock.hide();
				
				//Remove block options tab from panel
				removePanelTab('block-' + deleteBlockID);
				
				//Add the hidden input flag
				dataDeleteBlock(deleteBlockID);
				
				//Set block to false for the intersect check
				blockIntersectCheck(false, deleteBlockContainer);

			},
			down: function() {

				deleteBlock.show();

				if ( typeof GLOBALunsavedValues['blocks'][getBlockID(deleteBlock)]['delete'] != 'undefined' ) {
					delete GLOBALunsavedValues['blocks'][getBlockID(deleteBlock)]['delete'];
				}
				
				blockIntersectCheck(deleteBlock, deleteBlockContainer);

			}
		});
		
		allowSaving();	
		
	}

	exportBlockSettingsButtonCallback = function(args) {

		var params = {
			'security': Padma.security,
			'action': 'padma_visual_editor',
			'method': 'export_block_settings',
			'block-id': args.blockID
		}

		var exportURL = Padma.ajaxURL + '?' + $.param(params);

		return window.open(exportURL);

	}

	getBlockSettings = function(blockID,callback) {

		var params = {
			'security': Padma.security,
			'action': 'padma_visual_editor',
			'method': 'export_block_settings',
			'block-id': blockID
		}

		$.get(Padma.ajaxURL, params, function(block) {			
			if ( typeof callback == 'function' ){
				callback(block.settings);
			}
		});

	}


	initiateBlockSettingsImport = function(args) {

		var input = args.input;
		var blockID = args.blockID;
		var fileInput = $(input).parents('.ui-tabs-panel').first().find('input[name="block-import-settings-file"]');

		var importOptions = puBoolean($(input).parents('.ui-tabs-panel').first().find('input[name="block-import-settings-include-options"]').val());
		var importDesign = puBoolean($(input).parents('.ui-tabs-panel').first().find('input[name="block-import-settings-include-design"]').val());

		if ( !fileInput.val() )
			return alert('You must select a block settings export file before importing.');

		if ( !importOptions && !importDesign )
			return alert('You must import at least the options or design when importing block settings.');

		var blockSettingsFile = fileInput.get(0).files[0];

		if ( blockSettingsFile && typeof blockSettingsFile.name != 'undefined' && typeof blockSettingsFile.type != 'undefined' ) {

			var blockSettingsReader = new FileReader();

			blockSettingsReader.onload = function(e) { 

				var contents = e.target.result;
				var blockSettingsImportArray = JSON.parse(contents);

				/* Check to be sure that the JSON file is a block settings export file */
					if ( blockSettingsImportArray['data-type'] != 'block-settings' )
						return alert('Cannot load block settings.  Please insure that the block settings are a proper Padma block settings export.');

				/* Make sure block type matches */
					if ( getBlockType(getBlockByID(blockID)) != blockSettingsImportArray['type'] )
						return alert('Block type mismatch.  Be sure that the block settings export is the same type of block type that you\'re importing to.');

				/* Handle the fun stuff */
					if ( typeof blockSettingsImportArray['image-definitions'] != 'undefined' && Object.keys(blockSettingsImportArray['image-definitions']).length ) {

						showNotification({
							id: 'importing-images',
							message: 'Currently importing images.',
							closeTimer: 10000
						});

						$.post(Padma.ajaxURL, {
							security: Padma.security,
							action: 'padma_visual_editor',
							method: 'import_images',
							importFile: blockSettingsImportArray
						}, function(response) {
								
							var blockSettings = response;

							/* If there's an error when sideloading images, then hault import. */
							if ( typeof blockSettings['error'] != 'undefined' )
								return alert('Error while importing images for block: ' + blockSettings['error']);
								
							importBlockSettingsAJAXCallback(blockID, blockSettings, importOptions, importDesign);

						});

					} else {

						importBlockSettingsAJAXCallback(blockID, blockSettingsImportArray, importOptions, importDesign);

					}

			}; /* end blockSettingsReader.onload */

			blockSettingsReader.readAsText(blockSettingsFile);

		} else {

			alert('Cannot load block settings.  Please insure that the block settings are a proper Padma block settings export.');

		}

	}


		importBlockSettingsAJAXCallback = function(blockID, block, importOptions, importDesign) {

			/* Import block options */
				if ( importOptions ) {

					/* Delete existing block and re-add it so it has fresh settings */
					var blockID = switchBlockType(getBlockByID(blockID), getBlockType(getBlockByID(blockID)));

					/* Import block settings */
					importBlockSettings(block['settings'], blockID);

					/* Reload block settings */
					removePanelTab('block-' + blockID);
					openBlockOptions(getBlockByID(blockID));

				}

			/* Import block design */
				if ( importDesign && typeof block['styling'] != 'undefined' && typeof block['id'] != 'undefined' ) {

					dataPrepareDesignEditor();

					$.each(block['styling'], function(instanceID, instanceInfo) {

						/* Replace the block ID instance ID of the correct block ID */
						var oldBlockID = block['id'];
						var newBlockID = blockID;

						var instanceID = instanceID.replace('block-' + oldBlockID, 'block-' + newBlockID);

						$.each(instanceInfo.properties, function(property, value) {

							dataSetDesignEditorProperty({
								element: instanceInfo.element, 
								property: property, 
								value: (value !== null ? value.toString() : null), 
								specialElementType: "instance", 
								specialElementMeta: instanceID
							});

						});

					});

					showNotification({
						id: 'block-design-imported-' + blockID,
						message: 'Block design successfully imported',
						closeTimer: 6000,
						success: true
					});

				}

			/* All done, allow saving */
				allowSaving();

		}


		importBlockSettings = function(importBlockSettings, blockID) {

			/* Send the block settings data to the unsaved data */
				dataPrepareBlock(blockID);

				GLOBALunsavedValues['blocks'][blockID]['settings'] = importBlockSettings;
			
			/* Force reload block content */
				refreshBlockContent(blockID);

			/* Show notification */
				showNotification({
					id: 'block-settings-imported-' + blockID,
					message: 'Block settings successfully imported',
					closeTimer: 6000,
					success: true
				});

		}


	updateBlockMirrorStatus = function(input, block, value, updateTooltips) {
		
		/* If there is no input provided, then create an empty jQuery so no errors show up */
		if ( typeof input == 'undefined' || input == false )
			input = $();
			
		if ( typeof updateTooltips == 'undefined' )
			updateTooltips = true;

		if ( typeof block != 'object' )
			var block = getBlock($i('.block[data-id="' + block + '"]'));
		
		if ( typeof value == 'undefined' || value == '' ) {
										
			input.parents(".panel").find("ul.sub-tabs li:not(#sub-tab-config)").show();

			/* Change ID attribute to the block's real ID */
			block.attr('id', 'block-' + block.data('id'));

			/* Get rid of data-block-mirror */
			block.data('block-mirror', false);

			/* Remove mirrored class */
			block.removeClass('block-mirrored');
			
		} else { 
			
			input.parents(".panel").find("ul.sub-tabs li:not(#sub-tab-config)").hide();

			/* Update ID attribute to the mirrored block ID */
			block.attr('id', 'block-' + value);

			/* Update data-block-mirror */
			block.data('block-mirror', value);

			/* Add class */
			block.addClass('block-mirrored');
			
		}
		
	}

	updateBlockCustomClasses = function(input, block, value) {

		if ( Padma.mode != 'design' )
			return false;

		if ( typeof block != 'object' ) {
			block = getBlock($i('.block[data-id="' + block + '"]'));
		}

		if ( !block.length ) {
			return false;
		}

		/* Remove existing custom classes on block */
		block.removeClass(block.data('custom-classes'));

		/* Add new custom classes */
		block.data('custom-classes', value);
		block.addClass(value);

		return block;

	}

	updateBlockAnimationClasses = function(block, value) {

		if ( Padma.mode != 'design' )
			return false;

		if ( typeof block != 'object' ) {
			block = getBlock($i('.block[data-id="' + block + '"]'));
		}

		if ( !block.length ) {
			return false;
		}

		/* Remove existing custom classes on block */
		block.removeClass(block.data('custom-classes'));

		/* Add new custom classes */
		block.data('custom-classes', value);
		block.addClass('animated');
		block.addClass(value);
		return block;

	}

});