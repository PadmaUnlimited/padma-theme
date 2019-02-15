define(['jquery', 'helper.history', 'helper.data'], function($, history) {

	$.widget("ui.padmaGrid", $.ui.mouse, {

		options: {
			useIndependentGrid: false,
			columns: Padma.defaultGridColumnCount,
			columnWidth: Padma.globalGridColumnWidth,
			gutterWidth: Padma.globalGridGutterWidth,
			yGridInterval: 5,
			minBlockHeight: 10,
			selectedBlocksContainerClass: 'selected-blocks-container',
			defaultBlockClass: 'block',
			defaultBlockContentClass: 'block-content'
		},
		
		_create: function() {

			this.wrapper 	= this.element;
			this.container 	= this.wrapper.find('.grid-container');
			this.iframe 	= $(Padma.iframe);
			this.contents 	= $(this.iframe).contents();
			this.document 	= $(this.iframe).contents();
			
			/* Populate Grid Options from the Wrapper Settings.  This is primarily used for generating the CSS for the Grid so the right ratios and percentages can be made */
			this.options.useIndependentGrid = this.wrapper.data('wrapper-settings')['use-independent-grid'];

			this.options.columns = this.wrapper.data('wrapper-settings')['columns'] ? this.wrapper.data('wrapper-settings')['columns'] : Padma.defaultGridColumnCount;
			this.options.columnWidth = this.wrapper.data('wrapper-settings')['column-width'] ? this.wrapper.data('wrapper-settings')['column-width'] : Padma.globalGridColumnWidth;
			this.options.gutterWidth = this.wrapper.data('wrapper-settings')['gutter-width'] ? this.wrapper.data('wrapper-settings')['gutter-width'] : Padma.globalGridGutterWidth;

			this.addColumnGuides();

			/* Initialize CSS for this Grid */
			this.updateGridCSS();

			this.helperTemplate = $('<div class="ui-grid-helper block"></div>');
			this.offset = this.container.offset();
									
			this.wrapper.addClass('ui-padma-grid');
			this.wrapper.disableSelection();

			/* Binding */	
				this._mouseInit();

				/* Click on wrapper to delete blank/new blocks if clicking and not resizing or dragging */
					this._on(this.wrapper, {
						mousedown: 'wrapperMouseDown'
					});

				/* Focus/unfocus mechanism */
					this._on(this.contents, {
						mousedown: 'iframeMouseDown',
						mouseup: 'iframeMouseUp'
					});

					this._on(this.iframe, {
						mouseleave: 'iframeMouseLeave'
					});

				/* z-index focusing on block hover */
					this._on(this.contents, {
						mousemove: 'iframeMouseMove'
					});

				/* Window resize must reset the values of columnWidth and gutterWidth */
					this._on($(window), {
						resize: 'resetGridCalculations'
					});

					this._on($(window), {
						resize: 'alignAllBlocksWithGuides'
					});

				/*	Detect click on blocks	*/
					this._on(this.contents, {
						click: 'selectBlock'
					});
			/* End binding */

			this.initResizable(this.container.children('.' + this.options.defaultBlockClass.replace('.', '')));
			this.initDraggable(this.container.children('.' + this.options.defaultBlockClass.replace('.', '')));			

			/* Update Container Height */
			this.updateGridContainerHeight();

			/* Bind Double Click */	
				this.container.delegate('.' + this.options.defaultBlockClass.replace('.', ''), 'dblclick', function(event) {
					
					var grid = $(this).parents('.ui-padma-grid').data('ui-padmaGrid');
					
					//If there's only one grouped block and it's being toggled off, remove all grouping
					if ( $(this).hasClass('grouped-block') && grid.container.find('.grouped-block').length === 1 ) {
						
						$(this).removeClass('grouped-block');
						$(this).removeClass('currently-selected');
						grid.container.removeClass('grouping-active');
						
						hideNotification('mass-block-selection');
						
					//Else if the block is grouped, remove its class only
					} else if ( $(this).hasClass('grouped-block') ) {
						
						$(this).removeClass('grouped-block');
						$(this).removeClass('currently-selected');
					
					//Else there's no grouping and we need to start it	
					} else {
					
						$(this).addClass('grouped-block');						
						$(this).addClass('currently-selected');

						grid.container.addClass('grouping-active');

						showNotification({
							id: 'mass-block-selection',
							message: 'Mass Block Selection Mode',
							closable: true,
							closeOnEscKey: true,
							closeTimer: false,
							opacity: .8,
							closeCallback: function() {
								$i('.grouped-block').removeClass('grouped-block');
								$i('.grouped-block').removeClass('currently-selected');
								grid.container.removeClass('grouping-active');
							}
						});
					
					}
					
				});
			/* End Binding Double Click */

			/* Insure that blocks are aligned with guides */
			this.alignAllBlocksWithGuides();

		},

			resetGridCalculations: function() {

				this.grid = {
					columns: this.container.find('.grid-guide').length,
					columnWidth: parseInt(this.container.find('.grid-guide:eq(1)').outerWidth()),
					gutterWidth: parseInt(this.container.find('.grid-guide:eq(1)').css('marginLeft').replace('px', ''))
				};

				var self = this;

				/* Reset all resizable and draggable grid intervals */
				this.wrapper.find('.block:visible').each(function() {

					if ( $(this).data('plugin_pep') ) {
						$(this).data('plugin_pep').options.grid = [self.grid.columnWidth + self.grid.gutterWidth, self.options.yGridInterval];
					}

					if ( $(this).data('ui-resizable') ) {						
						$(this).resizable('option', 'grid', [self.grid.columnWidth + self.grid.gutterWidth, self.options.yGridInterval]);
						$(this).resizable('option', 'maxWidth', self.grid.columns * (self.grid.columnWidth + self.grid.gutterWidth));
					}

				});

			},
		
		_destroy: function() {

			/* Destroy wrapper resizable and droppable */
			this.element.resizable('destroy');
			//this.element.find('.ui-droppable').droppable('destroy');
			
			/* Destroy grid */
			this.element
				.removeClass("ui-grid ui-grid-disabled")
				.removeData("grid")
				.unbind(".grid");
			this._mouseDestroy();

			/* Destroy block resizing and draggables */
			this.element.find('.ui-resizable').resizable('destroy');
			//this.element.find('.ui-draggable').draggable('destroy');
							
			return this;
			
		},

		disable: function() {

			this.element.resizable('disable');
			//this.element.find('.ui-droppable').droppable('disable');
			this.element.find('.ui-resizable').resizable('disable');
			//this.element.find('.ui-draggable').draggable('disable');

		},

		enable: function() {

			this.element.resizable('enable');
			this.element.find('.ui-resizable').resizable('enable');

		},

		iframeMouseDown: function(event) {

			this._iframeMouseDownEvent = event;
			this._iframeMouseDownEventElement = $(event.originalEvent.target);

		},

		iframeMouseUp: function(event) {

			delete this._iframeMouseDownEvent;
			delete this._iframeMouseDownEventElement;

		},

		iframeMouseLeave: function(event) {

			$iDocument().trigger('mouseup');
			
		},

		iframeMouseMove: function(event) {

			if ( typeof this._iframeMouseDownEvent != 'undefined' || typeof this._doingHoverBlockToTop != 'undefined' || !Padma.touch )
				return;

			this._doingHoverBlockToTop = true;

			setTimeout($.proxy(function() {

				/* Retrieve the blocks that the mouse is inside */
				var hoverBlocks = [];

				var mouseX = event.pageX;
				var mouseY = event.pageY;

				$(this.container).find('.block').each(function() {

					var $this = $(this);
					var $thisOffset = $this.offset();

					var x1 = $thisOffset.left;
					var y1 = $thisOffset.top;

					var x2 = x1 + $this.width();
					var y2 = y1 + $this.height();

					if ( mouseX < x1 || mouseX > x2 )
						return;

					if ( mouseY < y1 || mouseY > y2 )
						return;

					hoverBlocks.push($this);

				});

				/* Get the block with boundaries closest to mouse */
				hoverBlocks.sort(function(a, b) {

					if ( b.width() * b.height() > a.width() * a.height() )
						return 1;

					return 0;

				});

				this.sendBlockToTop($(hoverBlocks.pop()));

				delete this._doingHoverBlockToTop;

			}, this), 50);

		},

		wrapperMouseDown: function(event) {

			if ( !event || this.container.hasClass('grouping-active') || getBlock(event.target) )
				return;

			/* Delete any blank blocks that may exist */
			$i('.blank-block').each(function() {
				removePanelTab('block-' + getBlockID($(this)));
				$(this).remove();
			});

		},

		_mouseStart: function(event) {

			this.mouseStartPosition = [event.pageX - this.container.offset().left, event.pageY - this.container.offset().top];

			var $eventTarget = $(event.target);
			
			if ( 
				!event 
				|| event.ctrlKey
				|| this.container.hasClass('grouping-active') 
				|| getBlock(event.target) 
				|| ($eventTarget.hasClass('wrapper-handle') || $eventTarget.parents('.wrapper-handle').length) 
				|| ($eventTarget.hasClass('ui-resizable-handle') && $eventTarget.parent().hasClass('wrapper'))
				|| this.wrapper.hasClass('wrapper-mirrored')
			)
				return true;
									
			this._trigger("start", event);


			this.helper = $(this.helperTemplate).clone().appendTo(this.container);
				
			//Add the minimum column width to the helper	
			this.helper.css({
				width: this.grid.columnWidth,
				height: 0,
				top: 0,
				left: 0,
				display: 'none'
			});

			/* Hide wrapper handles on drag */
			if ( this.wrapper.hasClass('wrapper-fluid') )
				this.wrapper.find('.wrapper-buttons').hide();

			//Set dragging flag
			this.draggingOnWrapper = true;

			//Show the dimensions tooltip
			addBlockDimensionsTooltip(this.helper);

			return true;
			
		},

		_mouseDrag: function(event) {


			var $eventTarget = $(event.target);

			//console.log(this.helper)

			if ( 
				!event 
				|| !this.helper
				|| event.ctrlKey
				|| this.container.hasClass('grouping-active') 
				|| (!this.helper && getBlock(event.target)) 
				|| ($eventTarget.hasClass('wrapper-handle') || $eventTarget.parents('.wrapper-handle').length) 
				|| ($eventTarget.hasClass('ui-resizable-handle') && $eventTarget.parent().hasClass('wrapper'))
				|| (!this.draggingOnWrapper || typeof this.draggingOnWrapper == 'undefined') 
				|| this.wrapper.hasClass('wrapper-mirrored')
			)
				return;

			var $thisContainer = $(this.container);
			var $thisContainerOffset = $thisContainer.offset();
				
			var x1 = this.mouseStartPosition[0];
			var y1 = this.mouseStartPosition[1];
			
			var x2 = event.pageX - $thisContainerOffset.left;
			var y2 = event.pageY - $thisContainerOffset.top;
			
			if (x1 > x2) { var tmp = x2; x2 = x1; x1 = tmp; }
			if (y1 > y2) { var tmp = y2; y2 = y1; y1 = tmp; }
			
			var containerLeft = $thisContainerOffset.left;
			var containerTop = $thisContainerOffset.top;
			var containerHeight = $thisContainer.height();	
			var containerWidth = $thisContainer.width();
				
			//console.log(event);

			/* Handle Padding */
				
				/* If both start and end points of block are inside right padding, don't draw the block. */
				if ( x2 >= containerWidth && x1 >= containerWidth )
					return;

				/* If both start and end points of block are inside bottom padding, don't draw the block. */
				if ( y2 >= containerHeight && y1 >= containerHeight )
					return;
							
				/* If they're starting the drag from the wrapper left padding, start at 0. */
				if ( x1 < 0 )
					x1 = 0;
					
				/* If they're starting the drag from the wrapper top padding, start at 0. */
				if ( y1 < 0 )
					y1 = 0;
					
				/* If start point is inside bottom padding, move it to absolute bottom */			
				if ( y2 > containerHeight ) {
					y2 = containerHeight;
				}			

			/* End Padding Conditionals */
			
			var blockLeft = x1.toNearest(this.grid.columnWidth + this.grid.gutterWidth);
			var blockTop = y1.toNearest(this.options.yGridInterval);
			var blockWidth = x2.toNearest(this.grid.columnWidth + this.grid.gutterWidth) - blockLeft - this.grid.gutterWidth;
			var blockHeight = y2.toNearest(this.options.yGridInterval) - y1.toNearest(this.options.yGridInterval);	
					
			Padma.blankBlockOptions = {
				display: 'block',
				left: blockLeft, 
				top: blockTop, 
				width: blockWidth,
				height: blockHeight
			};	
					
			/* Maxes */
			
				/* Width Max */
				if ( blockLeft + blockWidth > (this.grid.columns * (this.grid.columnWidth + this.grid.gutterWidth)) )
					Padma.blankBlockOptions.width = containerWidth - Padma.blankBlockOptions.left;

				/* If block bleeds out bottom, put a damper there. */
				if ( event.pageY > (containerTop + containerHeight)  ) {
					Padma.blankBlockOptions.height = containerHeight - blockTop;
				}
				
			/* End Maxes */
			
			/* Apply the CSS */
				this.helper.css(Padma.blankBlockOptions);

			/* Change width/left to classes to keep finicky bullshit to a minimum */
				var widthGridNum = Math.round((Padma.blankBlockOptions.width + this.grid.gutterWidth)/(this.grid.columnWidth + this.grid.gutterWidth));
				var leftGridNum = Math.round(Padma.blankBlockOptions.left/(this.grid.columnWidth + this.grid.gutterWidth));

				if ( widthGridNum == 0 )
					widthGridNum = 1;

				if ( widthGridNum ) {
					setBlockGridWidth(this.helper, widthGridNum);
				}

				if ( leftGridNum ) {
					setBlockGridLeft(this.helper, leftGridNum);
				}

				this.alignBlockWithGuides(this.helper);
			/* End adding classes */

			/* Make block red if it is not big enough */
			if ( Padma.blankBlockOptions.height < this.options.minBlockHeight ) {
				this.helper.addClass('block-error');
			} else if ( this.helper.hasClass('block-error') ) {
				this.helper.removeClass('block-error');
			}

			/* Handle dimensions tooltip */
			if ( !$.support.touch ) {

				this.helper.qtip('option', 'hide.delay', 10000);
				this.helper.qtip('option', 'show.delay', 10);
				this.helper.qtip('show');
				this.helper.qtip('option', 'content.text', blockDimensionsTooltipContent);
				this.helper.qtip('reposition');

			}
					
			this._trigger("drag", event);

			this.draggingOnWrapper = true;
			
			return false;
			
		},

		_mouseStop: function(event) {

			if ( 
				!event
				|| event.ctrlKey
				|| this.container.hasClass('grouping-active')
				|| !this.helper
				|| this.wrapper.hasClass('wrapper-mirrored') 
			)
				return;

			this._trigger("stop", event);
			
			Padma.blankBlockOptions = {
				width: getBlockGridWidth(this.helper),
				left: getBlockGridLeft(this.helper),
				pixelWidth: this.helper.width(),
				pixelLeft: this.helper.position().left,
				height: this.helper.height(),
				top: this.helper.position().top,
			}

			if ( this.helper.qtip('api') )
				this.helper.qtip('api').destroy(true);

			/* Re-show wrapper handles */
			this.wrapper.find('.wrapper-buttons').show();

			this.helper.remove();
			delete this.helper;
			
			//Check to make sure the block is big enough
			if ( Padma.blankBlockOptions.pixelWidth < this.grid.columnWidth || Padma.blankBlockOptions.height < this.options.minBlockHeight )
				return false;

			this.addBlankBlock(Padma.blankBlockOptions);
									
			this.mouseStartPosition = false;

			delete this.draggingOnWrapper;
			
			return false;
			
		},

		initResizable: function(element) {
					
			if ( typeof element == 'string' ) {
				var element = $(element);
			}

			element.resizable({
				handles: 'n, e, s, w, ne, se, sw, nw',
				grid:[this.grid.columnWidth + this.grid.gutterWidth, this.options.yGridInterval], 
				containment: this.container,
				minHeight: this.options.minBlockHeight, 
				maxWidth: this.grid.columns * (this.grid.columnWidth + this.grid.gutterWidth),
				start: this.resizableStart,
				resize: this.resizableResize,
				stop: this.resizableStop
			});
			
		},
			
			resizableStart: function(event, ui) {
				
				//this variable refers to resizabable
				
				var block = getBlock(ui.element);
				var grid = block.parents('.ui-padma-grid').data('ui-padmaGrid');
				
				var minBlockHeight = parseInt(block.css('minHeight').replace('px', ''));
				var height = block.height();
						
				//Remove min-height
				if ( minBlockHeight <= height ) {			
					block.css('minHeight', 0);
				}
				
				//Add the block hover class that keeps the controls, info, and glow visible during resizing
				block.addClass('block-hover');
				
				//Show the dimensions tooltip
				block.qtip('option', 'hide.delay', 10000);

				block.qtip('show');
				block.qtip('reposition');	

				/* Set originals */
				$(block).data('old-position', getBlockPosition(block));
				$(block).data('old-position-pixels', getBlockPositionPixels(block));

				$(block).data('old-dimensions', getBlockDimensions(block));
				$(block).data('old-dimensions-pixels', getBlockDimensionsPixels(block));

				/* Hide wrapper handles */
				if ( grid.wrapper.hasClass('wrapper-fluid') )
					grid.wrapper.find('.wrapper-buttons').hide();	
				
			},
			
			resizableResize: function(event, ui) {
				
				var block = getBlock(ui.element);
				var grid = block.parents('.ui-padma-grid').data('ui-padmaGrid');
				
				/* Set classes to get rid of finicky-ness and to make the block stay in line with guides */
				var widthGridNum = Math.round((block.width() + grid.grid.gutterWidth)/(grid.grid.columnWidth + grid.grid.gutterWidth));
				var leftGridNum = Math.round(block.position().left/(grid.grid.columnWidth + grid.grid.gutterWidth));

				setBlockGridWidth(block, widthGridNum);
				setBlockGridLeft(block, leftGridNum);

				grid.alignBlockWithGuides(block);

				//Update the dimensions tooltip
				var qtip = block.data('qtip');

				$i('#qtip-' + qtip.id).remove();
				qtip.rendered = false;
				qtip.render();
				qtip.show();
				
			},
			
			resizableStop: function(event, ui) {

				//this variable refers to resizable
				var block = getBlock(ui.element);				
				var grid = block.parents('.ui-padma-grid').data('ui-padmaGrid');

				/* Setup variables for undo/redo */
					var oldBlockPosition = block.data('old-position');
					var oldBlockDimensions = block.data('old-dimensions');

					var newBlockPosition = {
						left: Math.round(block.position().left / (grid.grid.columnWidth + grid.grid.gutterWidth)),
						top: getBlockPositionPixels(block).top
					}

					var newBlockDimensions = {
						width: Math.ceil(block.width() / (grid.grid.columnWidth + grid.grid.gutterWidth)),
						height: getBlockDimensionsPixels(block).height
					}
			
				/* Re-show wrapper handles */
				grid.wrapper.find('.wrapper-buttons').show();	

				var handleBlockResize = function(dimensions, position) {

					//Update classes and CSS
					setBlockGridWidth(block, dimensions.width);
					setBlockGridLeft(block, position.left);

					//Update height
					block.height(dimensions.height);

					//Update top position
					block.css('top', position.top + 'px');

					block.attr({
						'data-grid-top': position.top,
						'data-height': dimensions.height
					});	

					finishBlockResize();

				}

				var finishBlockResize = function() {

					block.css('width', '');
					block.css('left', '');	

					grid.alignBlockWithGuides(block);	
					
					//Add hidden input
					dataSetBlockDimensions(getBlockID(block), getBlockDimensions(block));
					dataSetBlockPosition(getBlockID(block), getBlockPosition(block));
					
					//Check for intersectors and allow saving if possible
					blockIntersectCheck(block) ? allowSaving() : disallowSaving();
							
					//Show the dimensions tooltip
					block.qtip('option', 'show.delay', 300);
					block.qtip('option', 'hide.delay', 25);

					block.qtip('show');
					block.qtip('reposition');
					
					//Remove the block hover class that keeps the controls, info, and glow visible during resizing
					block.removeClass('block-hover');	

				}

				Padma.history.add({
					description: 'Resized block',
					up: function() {

						handleBlockResize(newBlockDimensions, newBlockPosition);

					},
					down: function() {

						handleBlockResize(oldBlockDimensions, oldBlockPosition);

					}
				});
								
			},
		
		initDraggable: function(element) {
			
			if ( typeof element == 'string' ) {
				element = $(element);
			}
						
			element.css('cursor', 'move').pep({
				grid: [this.grid.columnWidth + this.grid.gutterWidth, this.options.yGridInterval],
				constrainTo: 'parent',
				shouldEase: false,
				start: this.draggableStart,
				stop: this.draggableStop,
				drag: this.draggableDrag
			});
			
		},
		
			draggableStart: function(event, ui) {

				/* If control key used to right-click, then stop this draggable */
				if ( event.ctrlKey )
					return false;

				if ( $(event.target).hasClass('ui-resizable-handle') ) {
					$(ui.el).trigger('stop');
					return false;
				}
				
				var block = ui.el;
				var grid = getBlock(block).parents('.ui-padma-grid').data('ui-padmaGrid');
							
				//Grouping Code
				blockGroupingOriginals = {};

				blockGroupingOriginals[getBlockID(block)] = {
					top: getBlockPositionPixels(block).top,
					left: getBlockPositionPixels(block).left
				}
				
				//If it's a grouped block, move group, otherwise reset group
				if ( $(block).hasClass('grouped-block') ) {

					$(block).data('plugin_pep').$el = grid.container.find('.grouped-block');

					grid.container.find('.grouped-block').each(function(i) {

						$(this).data('old-position', getBlockPosition($(this)));
						$(this).data('old-position-pixels', getBlockPositionPixels($(this)));
														
					});

					//Bring the blocks to the top
					grid.sendBlockToTop(grid.container.find('.grouped-block'));
					
				} else {
					
					grid.container.removeClass('grouping-active');
					grid.container.find('.grouped-block').removeClass('grouped-block');

					hideNotification('mass-block-selection');

					grid.sendBlockToTop($(block));

					$(block).data('plugin_pep').$el = $(block);
					$(block).data('old-position', getBlockPosition(block));
					$(block).data('old-position-pixels', getBlockPositionPixels(block));
					
				}
				//End Grouping Code

				/* Hide wrapper handles */
				if ( grid.wrapper.hasClass('wrapper-fluid') )
					grid.wrapper.find('.wrapper-buttons').hide();	

				//Hide dimensions tooltip	
				$(getBlock(ui.el)).qtip('hide');
                $(getBlock(ui.el)).qtip('disable');
				
			},
			
			draggableDrag: function(event, ui) {


				if ( $(ui.startEvent.target).hasClass('ui-resizable-handle') ) {
					return false;
				}

				var block = ui.el;				
				var $this = $(block);
				var isOverlapping;

				var container = getBlock($(block)).parents('.grid-container');
				var grid = getBlock($(block)).parents('.ui-padma-grid').data('ui-padmaGrid');
				var wrapper = getBlock($(block)).parents('.wrapper');

				/* Check if mouse coordinates are overlapping another wrapper.  If they are then show droppable effect. */
				if ( $i('.grid-container').length > 1 && !grid.container.find('.grouped-block').length ) {

					$i('.grid-container').not(container).each(function() {

						var wrapperBounding = $(this).closest('.wrapper')[0].getBoundingClientRect();
						var containerBounding = $(this)[0].getBoundingClientRect();

						isOverlapping =  (  event.pageX    > wrapperBounding.left  &&
				                                event.pageX < wrapperBounding.right &&
				                                event.pageY     > wrapperBounding.top + $iDocument().scrollTop()  &&
												event.pageY  < wrapperBounding.bottom + $iDocument().scrollTop() );

						isOverlapping ? $(this).addClass('ui-state-hover') : $(this).removeClass('ui-state-hover');

						if ( isOverlapping ) {

							var clone = $(block).data('wrapper-droppable-clone');
							var overlapOffset = {
								top: event.pageY - containerBounding.top - $iDocument().scrollTop(),
								left: event.pageX - containerBounding.left
							};

							/* Clone the block and move it into the possible destination wrapper */
							if ( !clone ) {

								var clone = $(block).clone()
									.css({
										'transform': '',
										'-webkit-transform': '',
										'-moz-transform': '',
										'-ms-transform': '',
										'-o-transform': '',
										left: $(block).position().left.toNearest(grid.grid.columnWidth + grid.grid.gutterWidth)
									})
									.appendTo($(this));

								/* Calculate initial difference upon entry so the block left is correct */
									clone.data('left-difference', overlapOffset.left - $(block).position().left.toNearest(grid.grid.columnWidth + grid.grid.gutterWidth));

								/* Ghost the original block */
									$(block)
										.data('ghost-position', $(block).position())
										.css({
											'transform': '',
											'-webkit-transform': '',
											'-moz-transform': '',
											'-ms-transform': '',
											'-o-transform': ''
										})
										.addClass('block-ghost');

								$(block).data('wrapper-droppable-clone', clone);

								delete $(block).data('plugin_pep').translation;
								delete $(block).data('plugin_pep').cssX;
								delete $(block).data('plugin_pep').cssY;

							} else {

								/* Make sure clone is in the correct wrapper */
								if ( !$(clone).closest($(this)).length ) {
									$(clone).appendTo($(this));
								}


							}

							/* Make sure limits aren't met with width and heights */
								var blockTopPosition = parseInt(overlapOffset.top).toNearest(grid.options.yGridInterval);
								var blockLeftPosition = parseInt(overlapOffset.left - clone.data('left-difference')).toNearest(grid.grid.columnWidth + grid.grid.gutterWidth);

							/* Do not left the left + width and top + height exceed the grid container width or height */
								if ( parseInt(blockLeftPosition) + parseInt($(block).width()) > $(this).width() ) {
									blockLeftPosition = $(this).width() - $(block).width();
								}

								if ( $(block).height() + blockTopPosition > $(this).height() ) {
									blockTopPosition = $(this).height() - $(block).height();
								}

							/* Do not let block top position be less than 0... In other words, not outside the top of the container */
								if ( blockTopPosition < 0 )
									blockTopPosition = 0;

							/* Do not allow the left position to be less than zero */
								if ( blockLeftPosition < 0 )
									blockLeftPosition = 0;

							/* Adjust height of wrapper accordingly */
								if ( $(block).height() > $(this).height() ) {

									if ( !$(this).attr('data-original-height') ) {
										$(this).attr('data-original-height', $(this).height());
									}

									$(this).height($(block).height());

								}

							/* Move the clone accordingly */
								$(clone).css({
									'transform': '',
									'-webkit-transform': '',
									'-moz-transform': '',
									'-ms-transform': '',
									'-o-transform': '',
									top: parseInt(blockTopPosition).toNearest(grid.options.yGridInterval),
									left: parseInt(blockLeftPosition).toNearest(grid.grid.columnWidth + grid.grid.gutterWidth)
								});

							/* If an overlap is found don't check the other wrappers */
							return false;

						}

					});


					/* If there are no overlaps then remove the clones and unghost the block */
					if ( !isOverlapping ) {

						$i('.block-ghost').each(function() {

							$(this).data('wrapper-droppable-clone').remove();
							$(this).removeData('wrapper-droppable-clone');

							$(this).removeClass('block-ghost');

							$(this).css({
								left: $(this).data('ghost-position').left,
								top: $(this).data('ghost-position').top,
								'transform': '',
								'-webkit-transform': '',
								'-moz-transform': '',
								'-ms-transform': '',
								'-o-transform': ''
							});

						});

						/* Change container heights back */
						$i('[data-original-height]').each(function() {

							$(this).height($(this).data('original-height'));
							$(this).removeAttr('data-original-height');

						});

					}

				}

				/* If the block is overlapping another wrapper, keep it from moving in the current wrapper */
				if ( isOverlapping ) {
					return false;
				}

				//Hide dimensions tooltip	
				$(getBlock(ui.helper)).qtip('hide');

			},
			
			draggableStop: function(event, ui) {

				
				if ( $(ui.startEvent.target).hasClass('ui-resizable-handle') ) {
					return false;
				}
				
				//this variable refers to draggable
				var block = $(ui.el);
				var container = getBlock($(block)).parents('.grid-container');
				var grid = getBlock($(block)).parents('.ui-padma-grid').data('ui-padmaGrid');
				var isOverlapping;
				var destinationContainer;

				/* Handle moving block from one wrapper to another */
					if ( $i('.grid-container').length > 1 && !grid.container.find('.grouped-block').length ) {

						$i('.grid-container').not(container).each(function() {

							var wrapperBounding = $(this).closest('.wrapper')[0].getBoundingClientRect();
							isOverlapping =  (  event.pageX    > wrapperBounding.left  &&
					                                event.pageX < wrapperBounding.right &&
					                                event.pageY     > wrapperBounding.top + $iDocument().scrollTop()  &&
													event.pageY  < wrapperBounding.bottom + $iDocument().scrollTop() );

							/* Second conditional is for triggering isOverlapping when the cursor triggers the mouseup by leaving the iframe */
							if ( isOverlapping || (!event.originalEvent && $(this).find($(block).data('wrapper-droppable-clone')).length) ) {
								isOverlapping = true;
								destinationContainer = $(this);
								return false;
							} else {
								destinationContainer = false;
							}

						});

					}

					/* Remove all .ui-state-hover classes from grid containers */
					$i('.grid-container.ui-state-hover').removeClass('ui-state-hover');

					if ( isOverlapping && $i('.grid-container').length > 1 ) {

						var destinationContainerPadmaGrid = destinationContainer.parents('.wrapper').data('ui-padmaGrid');
						var originalContainer = block.parents('.grid-container');

						/* Move block to destination container */
							destinationContainer.append(block);

						/* Copy position from clone */
							$(block).css($(block).data('wrapper-droppable-clone').position());
							$(block).css('transform', '');

						/* Delete clone */
							$(block).data('wrapper-droppable-clone').remove();

							$(block)
								.removeClass('block-ghost')
								.removeData('wrapper-droppable-clone');

							setBlockGridLeft(block, Math.round($(block).position().left / (grid.grid.columnWidth + grid.grid.gutterWidth)));

							$i('[data-original-height]').removeAttr('data-original-height');

						/* Change draggable/resizable containment on block to destination */
							block.resizable('destroy');
							destinationContainer.parents('.wrapper').padmaGrid('initResizable', block);

							$.pep.unbind(block);
							destinationContainer.parents('.wrapper').padmaGrid('initDraggable', block);

						/* Do block intersect check */
							blockIntersectCheck(false, originalContainer);
							blockIntersectCheck(false, destinationContainer);

						/* Set data */
							dataSetBlockPosition(getBlockID(block), getBlockPosition(block));

						/* Change the wrapper ID for the block and queue it for saving */
							dataSetBlockWrapper(getBlockID(block), getBlockWrapper(block).attr('id'));

						/* Re-align this block with the new wrapper */
							block.parents('.ui-padma-grid').first().data('ui-padmaGrid').alignBlockWithGuides(block);

					}
				/* End droppable handling */

				//Build the list of blocks that need to be updated, if there are grouped blocks then update them (which will include the one dragged)
				if ( grid.container.find('.grouped-block').length ) {
					
					var blocks = grid.container.find('.grouped-block');
					
				//Else we just have the one block to update
				} else {
					
					var blocks = getBlock(ui.el);
					
				}

				/* Re-show wrapper handles */
				grid.wrapper.find('.wrapper-buttons').show();	

				//Move the blocks and set hiddens //
					var blocksToMove = [];
					
					//Loop through each block now and build the todo array
					if ( blocks.length === 1 ) {
						var description = 'Moved block';
					} else {
						var description = 'Mass Moved Blocks: ';
					}

					blocks.each(function(){

						blocksToMove.push({
							block: $(this),

							newPosition: {
								left: Math.round($(this).position().left / (grid.grid.columnWidth + grid.grid.gutterWidth)),
								top: getBlockPositionPixels($(this)).top
							},
							newPositionPixels: getBlockPositionPixels($(this)),

							oldPosition: $(this).data('old-position'),
							oldPositionPixels: $(this).data('old-position-pixels')
						});

						if ( blocks.length > 1 )
							description += ', ';
						
					});

					if ( blocks.length > 1 )
						description = description.substring(0, description.length - 2);

					//Loop through todo array and log history
					var handleBlockDrag = function(options, reverse) {

						var block = options.block;

						var targetGridLeft = options.newPosition.left;
						var targetGridTop = options.newPosition.top;
						var targetPixelsLeft = options.newPositionPixels.left;

						if ( reverse ) {

							targetGridLeft = options.oldPosition.left;
							targetGridTop = options.oldPosition.top;
							targetPixelsLeft = options.oldPositionPixels.left;

						}

						//Update classes and CSS
						setBlockGridLeft(block, targetGridLeft);

						block.attr({
							'data-grid-top': targetGridTop
						});

						/* Remove Hardware acceleration transform and switch it to regular top/left and reset Pep */
							block.css('top', targetGridTop + 'px');
							block.css('left', targetPixelsLeft + 'px'); 

							block.css({
								'transform': '',
								'-webkit-transform': '',
								'-moz-transform': '',
								'-ms-transform': '',
								'-o-transform': ''
							});

							delete block.data('plugin_pep').translation;
							delete block.data('plugin_pep').cssX;
							delete block.data('plugin_pep').cssY;

						//Add hidden inputs
						dataSetBlockPosition(getBlockID(block), getBlockPosition(block));
						dataSetBlockWrapper(getBlockID(block), getBlockWrapper(block).attr('id'));

						//Check for intersectors and allow saving if possible		
						if ( blockIntersectCheck(block) ) {
							allowSaving();
						} else {
							disallowSaving();
						}

					}

					/* Do the action */
					Padma.history.add({
						description: description,
						up: function() {

							jQuery.each(blocksToMove, function(index, options) {
								handleBlockDrag(options, false);
							});

						},
						down: function() {

							jQuery.each(blocksToMove, function(index, options) {
								handleBlockDrag(options, true);
							});

						}
					});
				//End setting hiddens

				//Make sure document is focused
				$(document).focus();

                //Re-enable tooltip
                $(getBlock(ui.el)).qtip('enable');
                $(getBlock(ui.el)).qtip('show');

                //Reposition dimensions tooltip
				$(block).data('hoverWaitTimeout', setTimeout(function() {
					
					$(getBlock(ui.el)).qtip('reposition');
					$(getBlock(ui.el)).qtip('show');
					
				}, 300));
				
			},

		addBlankBlock: function(args, usingAddBlock) {
			
			var defaults = {
				top: 0,
				left: 0,
				width: 140,
				height: this.options.minBlockHeight,
				id: null
			}
			
			args = $.extend({}, defaults, args);
			
			if ( typeof usePixels == 'undefined' )
				var usePixels = true;
				
			if ( typeof usingAddBlock == 'undefined' )
				usingAddBlock = false;

			var temporaryID = Math.ceil(Math.random() * 1000000000);

			Padma.blankBlock = $('<div><div class="block-content-fade block-content"></div></div>')
				.attr('id', 'block-' + temporaryID)
				.attr('data-id', temporaryID)
				.attr('data-temp-id', temporaryID)
				.attr('data-desired-id', args.id ? args.id : null)
				.data('id', temporaryID)
				.data('temp-id', temporaryID)
				.data('desired-id', args.id ? args.id : null)
				.addClass(this.options.defaultBlockClass.replace('.', ''))
				.addClass('blank-block')
				.addClass('hide-content-in-grid');

			updateBlockContentCover(Padma.blankBlock);

			var block = Padma.blankBlock;

			/* Setup block in DOM */
				block.css({
					height: parseInt(args.height),
					top: parseInt(args.top),
					position: 'absolute',
					visibility: 'hidden',
					left: '',
					width: ''
				});

				setBlockGridLeft(block, args.left);
				setBlockGridWidth(block, args.width);
				
				block.attr({
					'data-height': parseInt(args.height),
					'data-grid-top': parseInt(args.top)
				});

				block.appendTo(this.container);

				this.alignBlockWithGuides(block);
				block.css('visibility', 'visible');

			//Add this conditional in so addBlock doesn't take as long
			if ( usingAddBlock == false ) {
				openBlockTypeSelector($(Padma.blankBlock));
			}

			//Initiate stuff
			this.initResizable(block);
			this.initDraggable(block);
			
			//Show the red right off the bat if the block is touching/overlapping other blocks
			blockIntersectCheck(block);
							
			return block;
			
		},

		setupBlankBlock: function(blockType, usingAddBlock, loadContent) {

			if ( typeof loadContent == 'undefined' )
				var loadContent = true;

			if ( typeof usingAddBlock == 'undefined' )
				var usingAddBlock = false;

			Padma.blankBlock.removeClass('blank-block');
			Padma.blankBlock.addClass('block-type-' + blockType);
			Padma.blankBlock.data('type', blockType);

			if ( loadContent ) {

				loadBlockContent({
					blockElement: Padma.blankBlock,
					blockOrigin: {
						type: blockType,
						id: 0,
						layout: Padma.viewModels.layoutSelector.currentLayout()
					},
					blockSettings: {
						dimensions: getBlockDimensions(Padma.blankBlock),
						position: getBlockPosition(Padma.blankBlock)
					},
				});

			}

			//Set the fluid/fixed height class so the fluid height message is shown correctly
			if ( getBlockTypeObject(blockType)['fixed-height'] === true ) {

				Padma.blankBlock.addClass('block-fixed-height');

			} else {

				Padma.blankBlock.addClass('block-fluid-height');

			}

			//Set the hide-content-in-grid depending on the block type
			if ( getBlockTypeObject(blockType)['show-content-in-grid'] )
				Padma.blankBlock.removeClass('hide-content-in-grid');

			//Add the hidden input flag
			dataAddBlock(Padma.blankBlock);
			dataSetBlockPosition(getBlockID(Padma.blankBlock), getBlockPosition(Padma.blankBlock));
			dataSetBlockDimensions(getBlockID(Padma.blankBlock), getBlockDimensions(Padma.blankBlock));
			dataSetBlockWrapper(getBlockID(Padma.blankBlock), Padma.blankBlock.closest('.wrapper').attr('id'));

			//Check for intersectors and allow saving if possible
			if ( blockIntersectCheck(Padma.blankBlock) ) {
				allowSaving();
			} else {
				disallowSaving();
			}

			updateBlockContentCover(Padma.blankBlock);

			//Save block variable to return it at the end
			var block = Padma.blankBlock;

			//Make this undoable/redoable
			Padma.history.add({
				description: 'Added ' + getBlockTypeNice(blockType) + ' block',
				up: function() {

					//Reshow the block
					block.show();

					if ( typeof GLOBALunsavedValues['blocks'][getBlockID(block)]['delete'] != 'undefined' ) {
						delete GLOBALunsavedValues['blocks'][getBlockID(block)]['delete'];
					}

					blockIntersectCheck(block, block.parents('.grid-container'));

				},
				down: function() {

					//Remove the block!
					block.hide();

					//Remove block options tab from panel
					removePanelTab('block-' + getBlockID(block));

					//Add the hidden input flag
					dataDeleteBlock(getBlockID(block));

					//Set block to false for the intersect check
					blockIntersectCheck(false, block.parents('.grid-container'));

				}
			});


			//Clear variable
			delete Padma.blankBlock;
			delete Padma.blankBlockOptions;

			return block;

		},
		
		addBlock: function(args) {
			
			var defaults = {
				top: 0,
				left: 0,
				width: 1,
				height: this.options.minBlockHeight,
				type: null,
				id: null,
				settings: []
			}
			
			var args = $.extend({}, defaults, args);
			
			if ( this.addBlankBlock(args, true) ) {
				
				var block = this.setupBlankBlock(args.type, true, false);
				var blockID = getBlockID(block);
										
				$.each(args.settings, function(key, value) {
								
					dataSetBlockOption(blockID, key, value);
					
					if ( key == 'mirror-block' ) {
						updateBlockMirrorStatus(false, block, value, false);
					}
					
				});

				if ( typeof args.settings.duplicateOf != 'undefined' ) {
					block.data('duplicateOf', args.settings.duplicateOf);
				}

				refreshBlockContent(blockID, false, false, false);

				this.updateGridContainerHeight();

				return block;
				
			} else {
				
				return false;
				
			}
			
		},

		sendBlockToTop: function(block) {

			if ( typeof block == 'string' )
				var block = getBlock(block);

			if ( !block || !block.length )
				return;

			$i('.block').css('zIndex', 1);
			block.css('zIndex', 2);

		},

		updateGridContainerHeight: function() {

			var container = this.container;
			var wrapper = this.wrapper;

			/* Reset container height */
			container.css('height', this.wrapper.height());

			/* Resize container to fit the lowest block bottom (block top + block height) */
			if ( container.find('.block:visible').length ) {

				var bottomToUse = 0;

				container.find('.block:visible').each(function() {

					var blockBottom = $(this).outerHeight() + $(this).position().top;

					if ( blockBottom > bottomToUse )
						bottomToUse = blockBottom;

				});

				container.height(bottomToUse);

			}

		},

		addColumnGuides: function() {

			var gridContainer = this.container;

			/* Remove existing grid guides container */
			gridContainer.find('.grid-guides').remove();

			var gridGuidesContainer = $('<div class="grid-guides grid-guides-grey"></div><!-- #grid -->');

			for ( i = 1; i <= this.options.columns; i++ )
				gridGuidesContainer.append('<div class="grid-guide grid-width-1 grid-guide-' + i + '"></div>');
			
			gridGuidesContainer.prependTo(gridContainer);

		},

		alignBlockWithGuides: function(block) {

			/* SET GRID LEFT TO THE LEFT EDGE OF THE GRID GUIDE AND SET WIDTH TO THE END OF THE OTHER GRID GUIDE TO COMPENSATE FOR PERCENTAGE MISCALCULATIONS */
			var gridGuidesContainer = this.container.children('.grid-guides:visible');

			if ( !gridGuidesContainer.length )
				return;

			var leftGridNum = parseInt(getBlockGridLeft(block));
			var widthGridNum = parseInt(getBlockGridWidth(block));

				if ( typeof leftGridNum == 'undefined' || isNaN(leftGridNum) || leftGridNum < 0 )
					leftGridNum = 0;

				if ( typeof widthGridNum == 'undefined' || isNaN(widthGridNum) || widthGridNum == 0 )
					widthGridNum = 1;

			var leftGridGuideNum = ((leftGridNum + 1) < this.grid.columns) ? (leftGridNum + 1) : this.grid.columns;
				var leftGridGuide = gridGuidesContainer.find('.grid-guide-' + leftGridGuideNum);

			/* Do not search for a right grid guide that exceeds column count */
			var rightGridGuideNum = ((leftGridNum + widthGridNum) < this.grid.columns) ? (leftGridNum + widthGridNum) : this.grid.columns;
				var rightGridGuide = gridGuidesContainer.find('.grid-guide-' + rightGridGuideNum);

			if ( !rightGridGuide.length )
				rightGridGuide = leftGridGuide;

			if ( (leftGridNum + 1) <= (leftGridNum + widthGridNum) ) {

				var leftGridGuideMargin = parseInt(leftGridGuide.css('marginLeft').replace('px', ''));
				var rightGridGuideMargin = parseInt(rightGridGuide.css('marginLeft').replace('px', ''));

				var leftAmount = leftGridGuide.position().left + leftGridGuideMargin;
				var rightAmount = (rightGridGuide.position().left + rightGridGuide.outerWidth()) - (leftGridGuide.position().left) - 1;

				if ( leftGridNum == 0 )
					rightAmount = rightAmount + rightGridGuideMargin;

				block.css({
					top: $(block).position().top,
					transform: '',
					left: Math.ceil(leftAmount) + 'px',
					width: Math.ceil(rightAmount) + 'px'
				});

			}

		},

			alignAllBlocksWithGuides: function() {

				var grid = this;

				this.container.find('.block:visible').each(function() {

					grid.alignBlockWithGuides($(this));

				});

			},

		selectBlock: function(event){
			var block 		= getBlock(event.target);
			if(block){
				var grid = block.parents('.ui-padma-grid').data('ui-padmaGrid');
				if(!grid.container.hasClass('grouping-active')){
					$(this.document).find('.currently-selected').each(function(){
						$(this).removeClass('currently-selected')
					});
				}
				block.addClass('currently-selected');
				currentBlockInfo(block);
			}
		},

		/**
		 * Used to recalculate all CSS for a wrapper and its grid.  Can simply be fed column count, columnWidth, and gutterWidth and it will do the heavy lifting
		**/
		updateGridCSS: function() {

			var self = this;

			var wrapper = this.wrapper;
			var gridWidthInputContext = $('div#wrapper-' + getWrapperID(wrapper) + '-tab');

			var wrapperSelector = 'div#' + wrapper.attr('id');

			if ( wrapper.attr('data-temp-id') )
				wrapperSelector = 'div.wrapper[data-temp-id="' + wrapper.attr('data-temp-id') + '"]';

			if (
				typeof this.options.useIndependentGrid != 'undefined' &&
				this.options.useIndependentGrid === true ||
				( typeof this.options.useIndependentGrid == 'string' && puBoolean(this.options.useIndependentGrid) )
			) {
				updateGridCSS(wrapperSelector, this.options.columns, this.options.columnWidth, this.options.gutterWidth, gridWidthInputContext);
			} else {
				updateGridCSS(wrapperSelector, this.options.columns, Padma.globalGridColumnWidth, Padma.globalGridGutterWidth, gridWidthInputContext);
			}

			/* Reset Grid Calculations */
				this.resetGridCalculations();

			/* Align all blocks with guides */
				$($iDocument()).ready(function() {
					self.alignAllBlocksWithGuides();
				});

			return wrapper;

		}
		
	});

	$.extend($.ui.padmaGrid, {
		version: "2.1"
	});


});