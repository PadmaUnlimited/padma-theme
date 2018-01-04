define(['util.custommouse', 'qtip', 'helper.data', 'modules/grid/wrapper-inputs'], function() {

	setupWrapperSortables = function() {

		/* Wrapper Sorting */
		return $i('#whitewrap').sortable({
			items: 'div.wrapper',
			handle: 'div.wrapper-drag-handle',
			axis: 'y',
			tolerance: 'intersect',
			placeholder: 'wrapper-sortable-placeholder',
			start: function(event, ui) {

				/* Store previous heights of wrappers that way they can be added back after sorting */
				$i('.wrapper').each(function() {
					$(this).data('current-height', $(this).height());
				});


				/* Center fixed wrappers with absolute positioning because sortables doesn't like margin: 0 auto; */
				if ( $(ui.item).hasClass('wrapper-fixed-grid') ) {

					$(ui.item).css({
						left: '50%',
						marginLeft: '-' + ($(ui.item).outerWidth() / 2) + 'px'
					});

				}
			
				/* Update placeholder size */
				ui.placeholder.css({
					width: ui.item.outerWidth(),
					height: ui.item.outerHeight(),
					marginTop: ui.item.css('marginTop'),
					marginBottom: ui.item.css('marginBottom')
				});

				/* Keep track of original document height for maximum scrollTop */
				Padma.iframe.data('maximumScrollTop', Padma.iframe.contents().height());

				/* Refresh sortable since heights changed */
				$(this).sortable('refreshPositions');

			},
			sort: function(event, ui) {

				/* Automatically scroll up */
				if ( event.clientY < 100 ) {

					if ( typeof wrapperDraggableScrollDownInterval == 'number' ) {
						clearInterval(wrapperDraggableScrollDownInterval);
						delete wrapperDraggableScrollDownInterval;
					}

					if ( typeof wrapperDraggableScrollUpInterval == 'undefined' ) {

						wrapperDraggableScrollUpInterval = setInterval(function() {
							Padma.iframe.contents().scrollTop(Padma.iframe.contents().scrollTop() - 8);
						}, 5);

					}

				/* Automatically scroll down */
				} else if ( (Padma.iframe.height() - event.clientY) < 100 ) {

					if ( typeof wrapperDraggableScrollUpInterval == 'number' ) {
						clearInterval(wrapperDraggableScrollUpInterval);
						delete wrapperDraggableScrollUpInterval;
					}

					if ( typeof wrapperDraggableScrollDownInterval == 'undefined' ) {

						wrapperDraggableScrollDownInterval = setInterval(function() {

							var newScrollTop = Padma.iframe.contents().scrollTop() + 8;

							/* Do not allow scrollTop to exceed the document height */
							if ( Padma.iframe.height() + newScrollTop >= Padma.iframe.data('maximumScrollTop') ) {
								newScrollTop = Padma.iframe.data('maximumScrollTop') - Padma.iframe.height();
							}

							Padma.iframe.contents().scrollTop(newScrollTop);

						}, 5);

					}

				} else {

					if ( typeof wrapperDraggableScrollDownInterval == 'number' ) {
						clearInterval(wrapperDraggableScrollDownInterval);
						delete wrapperDraggableScrollDownInterval;
					}

					if ( typeof wrapperDraggableScrollUpInterval == 'number' ) {
						clearInterval(wrapperDraggableScrollUpInterval);
						delete wrapperDraggableScrollUpInterval;
					}

				}

			},
			stop: function(event, ui) {
				
				/* Un-absolute-center fixed wrappers */
				$i('.wrapper').css({
					marginLeft: '',
					left: ''
				});

				/* Reset grid container heights */
				$i('.wrapper').each(function() {
					$(this).padmaGrid('updateGridContainerHeight');
				});

				/* Stop scrolling intervals if they still exist */
				if ( typeof wrapperDraggableScrollDownInterval == 'number' ) {
					clearInterval(wrapperDraggableScrollDownInterval);
					delete wrapperDraggableScrollDownInterval;
				}

				if ( typeof wrapperDraggableScrollUpInterval == 'number' ) {
					clearInterval(wrapperDraggableScrollUpInterval);
					delete wrapperDraggableScrollUpInterval;
				}

				dataSortWrappers();

			}
		});

	}

	setupWrapperResizable = function(wrappers) {

		if ( typeof wrappers == 'undefined' )
			var wrappers = $i('.wrapper');

		wrappers.each(function() {

			var wrapperMinHeight = parseInt($(this).css('minHeight').replace('px', '')) ;

			$(this).resizable({
				handles: 'n, s',
				grid: 5,
				minHeight: wrapperMinHeight,

				start: function(event, ui) {

					if ( $(event.toElement).hasClass('ui-resizable-n') ) {
						$(this).data('resizing-position', 'n');
					} else {
						$(this).data('resizing-position', 's');
					}

					/* Set minHeight depending on the location and height of the lowest block */
					if ( $(this).find('.block').length ) {

						var bottomToUse = 0;
						var topToUse = null;

						$(this).find('.block:visible').each(function() {

							var blockTop = $(this).position().top;
							var blockBottom = $(this).outerHeight() + blockTop;

							if ( blockBottom > bottomToUse )
								bottomToUse = blockBottom;

							if ( blockTop < topToUse || topToUse === null )
								topToUse = blockTop;

							/* Store the block's original block top */
							$(this).data('resize-original-block-top', $(this).position().top);

						});

						/* If the wrapper is being resized from the top, then we can subtract the topToUse (the highest block position) from the min height that way wrapper height can be reduced from the top */
						if ( $(this).data('resizing-position') == 'n' ) {
							var minHeight = bottomToUse - topToUse;
						} else {
							var minHeight = bottomToUse;
						}

					} else {

						var minHeight = wrapperMinHeight;

					}

					$(this).resizable('option', 'minHeight', minHeight);

				},
				resize: function(event, ui) {

					var heightChange = ui.originalSize.height - ui.size.height;
					var wrapperHeight = ui.size.height;

					$(this).find('.grid-container').height(wrapperHeight);

					/* Cancel out top and height added to wrapper since the grid container height will dictate the wrapper height */
					$(this).css({
						top: '',
						height: ''
					});

					if ( $(this).data('resizing-position') == 'n' ) {

						/* Insure that the resulting on any of the block tops isn't negative.  If so, stop ALL block top changing */
							var negativeTop = false;

							$(this).find('.block').each(function() {

								if ( $(this).data('resize-original-block-top') - heightChange < 0 ) {

									negativeTop = true;
									return false;

								}

							});

						/* Change block tops if the test is passed */
							if ( !negativeTop ) {

								$(this).find('.block').each(function() {

									$(this).css({
										top: $(this).data('resize-original-block-top') - heightChange
									});

								});

							}

					}

				},
				stop: function() {

					/* Update the position of all of the blocks in the wrapper */
					$(this).find('.block').each(function() {

						var block = $(this);
						var blockID = getBlockID(block);

						block.attr('data-grid-top', block.position().top);

						dataSetBlockPosition(blockID, getBlockPosition(block));

					});


					$(this).data('resizing-position', null);

				}
			});

		});

	}	

		stopWrapperResizable = function(wrapper) {

			if ( !wrapper.length || !wrapper.resizable )
				return false;

			wrapper.resizable('destroy');

			setupWrapperResizable(wrapper);

		}

	addEdgeInsertWrapperButtons = function() {

		var buttons = '<div class="add-wrapper-button-fixed tooltip" title="Add Wrapper">+</div>';

		$('<div class="add-wrapper-buttons add-wrapper-buttons-top">' + buttons + '</div>')
			.data('position', 'top')
			.prependTo($i('body'));

		$('<div class="add-wrapper-buttons add-wrapper-buttons-bottom">' + buttons + '</div>')
			.data('position', 'bottom')
			.appendTo($i('body'));

	}

	setupWrapperContextMenu = function() {

		setupContextMenu({
			id: 'wrapper',
			elements: '.wrapper',
			title: function(event) {

				var wrapper = $(event.currentTarget);
				var wrapperID = getWrapperID(wrapper);

				return 'Wrapper';

			},
			contentsCallback: function(event) {

				var contextMenu = $(this);
				var wrapper = $(event.currentTarget);
				var wrapperID = getWrapperID(wrapper);

				/* Wrapper options */
					$('<li class="context-menu-wrapper-options"><span>Open Wrapper Options</span></li>').appendTo(contextMenu).on('click', function() {

						openWrapperOptions(wrapperID);

					});

				/* Wrapper type changing */
				if ( wrapper.hasClass('wrapper-fluid') && Padma.mode == 'grid' ) {

					$('<li class="context-menu-wrapper-to-fixed"><span>Change Wrapper to Fixed</span></li>').appendTo(contextMenu).find('span').on('click', function() {

						wrapper.removeClass('wrapper-fluid');
						wrapper.removeClass('wrapper-fluid-grid');

						wrapper.addClass('wrapper-fixed');
						wrapper.addClass('wrapper-fixed-grid');

						dataSetWrapperWidth(getWrapperID(wrapper), 'fixed');
						dataSetWrapperGridWidth(getWrapperID(wrapper), 'fixed');

						wrapper.data('ui-padmaGrid').resetGridCalculations();
						wrapper.data('ui-padmaGrid').alignAllBlocksWithGuides();
						wrapper.data('ui-padmaGrid').updateGridContainerHeight();

					});

					if ( wrapper.hasClass('wrapper-fixed-grid') ) {

						$('<li class="context-menu-wrapper-grid-to-fluid"><span>Change Grid to Fluid</span></li>').appendTo(contextMenu).find('span').on('click', function() {

							wrapper.removeClass('wrapper-fixed-grid');
							wrapper.addClass('wrapper-fluid-grid');

							dataSetWrapperWidth(getWrapperID(wrapper), 'fluid');
							dataSetWrapperGridWidth(getWrapperID(wrapper), 'fluid');

							wrapper.data('ui-padmaGrid').resetGridCalculations();
							wrapper.data('ui-padmaGrid').alignAllBlocksWithGuides();
							wrapper.data('ui-padmaGrid').updateGridContainerHeight();

						});

					} else if ( wrapper.hasClass('wrapper-fluid-grid') ) {

						$('<li class="context-menu-wrapper-grid-to-fixed"><span>Change Grid to Fixed</span></li>').appendTo(contextMenu).find('span').on('click', function() {

							wrapper.removeClass('wrapper-fluid-grid');
							wrapper.addClass('wrapper-fixed-grid');

							dataSetWrapperWidth(getWrapperID(wrapper), 'fluid');
							dataSetWrapperGridWidth(getWrapperID(wrapper), 'fixed');

							wrapper.data('ui-padmaGrid').resetGridCalculations();
							wrapper.data('ui-padmaGrid').alignAllBlocksWithGuides();
							wrapper.data('ui-padmaGrid').updateGridContainerHeight();

						});

					}

				} else if ( wrapper.hasClass('wrapper-fixed') && Padma.mode == 'grid' ) {

					$('<li class="context-menu-wrapper-to-fluid"><span>Change Wrapper to Fluid</span></li>').appendTo(contextMenu).on('click', function() {

						wrapper.removeClass('wrapper-fixed');

						wrapper.addClass('wrapper-fluid');
						wrapper.addClass('wrapper-fixed-grid');

						dataSetWrapperWidth(getWrapperID(wrapper), 'fluid');
						dataSetWrapperGridWidth(getWrapperID(wrapper), 'fixed');

						wrapper.data('ui-padmaGrid').resetGridCalculations();
						wrapper.data('ui-padmaGrid').alignAllBlocksWithGuides();
						wrapper.data('ui-padmaGrid').updateGridContainerHeight();

					});

				}


				/* Wrapper Alias */
				$('<li class="context-menu-set-alias"><span>Set Wrapper Alias</span></li>').appendTo(contextMenu).on('click', function() {

					var wrapperAlias = prompt('Please enter the desired wrapper alias.', wrapper.data('alias'));

					if ( !wrapperAlias )
						return;

					dataSetWrapperOption(getWrapperID(wrapper), 'alias', wrapperAlias);
					wrapper.data('alias', wrapperAlias);

				});

				/* Delete wrapper.  Do not allow it to be deleted if it's the last one. */
				if ( $i('.wrapper:visible').length >= 2 && Padma.mode == 'grid' ) {

					$('<li class="context-menu-wrapper-delete"><span>Delete Wrapper</span></li>').appendTo(contextMenu).on('click', function() {

						deleteWrapper(wrapperID);

					});

				}

			}
		});

	}

	bindWrapperButtons = function() {

		/* Add Wrapper Buttons */
			$i('body').delegate('.add-wrapper-button-fixed', 'click', function() {

				return addWrapper($(this).parents('.add-wrapper-buttons').data('position'), {
					'fluid': false
				});

			});

			$i('body').delegate('.add-wrapper-fluid-fixed-grid', 'click', function() {

				return addWrapper($(this).parents('.add-wrapper-buttons').data('position'), {
					'fluid': true
				});
			});

			$i('body').delegate('.add-wrapper-fluid-fluid-grid', 'click', function() {

				return addWrapper($(this).parents('.add-wrapper-buttons').data('position'), {
					'fluid': true,
					'fluid-grid': true
				});

			});

		/* Wrapper Buttons */
			$i('body').delegate('.wrapper-buttons .wrapper-options', 'click', function() {

				return openWrapperOptions(getWrapperID($(this).closest('.wrapper')));

			});

			bindWrapperMarginButtons($i('.wrapper-buttons .wrapper-margin-handle'));


	}

		addWrapper = function(position, wrapperSettings, suppressNotice) {

			if ( typeof wrapperSettings.id != 'undefined' )
				delete wrapperSettings.id;

			var wrapperSettings = $.extend({}, {
				'fluid': false,
				'fluid-grid': false,
				'use-independent-grid': false
			}, wrapperSettings);

			if ( typeof wrapperSettings['fluid'] != 'boolean' ) {
				wrapperSettings['fluid'] = btBoolean(wrapperSettings['fluid']);
			}

			if ( typeof wrapperSettings['fluid-grid'] != 'boolean' ) {
				wrapperSettings['fluid-grid'] = btBoolean(wrapperSettings['fluid-grid']);
			}

            if ( typeof wrapperSettings['enable-sticky-positioning'] != 'boolean' ) {
                wrapperSettings['enable-sticky-positioning'] = btBoolean(wrapperSettings['enable-sticky-positioning']);
            }

			/* Generate the wrapper */
				var temporaryID = Math.ceil(Math.random() * 1000000000);
				var wrapper = $('<div class="wrapper"><div class="grid-container"></div></div>');

				wrapper
					.attr('id', 'wrapper-' + temporaryID)
					.attr('data-id', temporaryID)
					.attr('data-temp-id', temporaryID)
					.attr('data-desired-id', wrapperSettings.id ? wrapperSettings.id : null)
					.data('id', temporaryID)
					.data('temp-id', temporaryID)
					.data('desired-id', wrapperSettings.id ? wrapperSettings.id : null);

			/* Add wrapper mirror notice/overlay */
					wrapper.prepend('<div class="wrapper-mirror-overlay"></div>');
					
					wrapper.find('.grid-container').append('\
						<div class="wrapper-mirror-notice">\
							<div>\
							<h2>Wrapper Mirrored</h2>\
							<p>This wrapper is mirroring blocks from another wrapper.</p>\
							<small>Mirroring can be disabled via Wrapper Options in the right-click menu</small>\
							</div>\
						</div><!-- .wrapper-mirror-notice -->\
					');

				/* Add wrapper buttons */
					addWrapperButtons(wrapper);

				/* Classes */
					if ( wrapperSettings['fluid'] ) {
						wrapper.addClass('wrapper-fluid');
					} else {
						wrapper.addClass('wrapper-fixed');
					}

					if ( wrapperSettings['fluid-grid'] ) {
						wrapper.addClass('wrapper-fluid-grid');
					} else {
						wrapper.addClass('wrapper-fixed-grid');
					}
			/* End wrapper generation */


			/* Position the wrapper and place it into the document */
				switch ( position ) {

					case 'top':
						wrapper.prependTo($i('#whitewrap'));
					break;

					case 'bottom':
						wrapper.insertBefore($i('#wrapper-buttons-template'));
					break;

				} 

			/* Top/Bottom Margins for Fluid Wrappers */
				/* This will change the margin top on fluid wrappers that touch the top to 0 and margin bottoms to 0 on fluid wrappers that are the last wrapper  */
					if ( wrapperSettings['fluid'] ) {

						wrapper.css('margin' + position.capitalize(), 0);

						dataSetDesignEditorProperty({
							element: 'wrapper', 
							property: 'margin-' + position, 
							value: 0, 
							specialElementType: 'instance', 
							specialElementMeta: 'wrapper-' + temporaryID
						});

					}

			/* Add the hidden flag so it saves*/
				dataAddWrapper(wrapper, wrapperSettings, $i('.wrapper').index(wrapper));

				allowSaving();

			/* Set height on Grid to 100px */
				wrapper.find('.grid-container').height(100);

			/* Initiate Padma Grid on new wrapper */
				wrapper.data('wrapper-settings', wrapperSettings);

				wrapper.padmaGrid();
				setupWrapperResizable(wrapper);

				bindWrapperMarginButtons(wrapper.find('.wrapper-margin-handle'));

			/* Show notification */
				var wrapperType = wrapperSettings['fluid'] ? 'Fluid' : 'Fixed';

				if ( typeof suppressNotice == 'undefined' || !suppressNotice ) {
					showNotification({
						id: 'wrapper-created-' + temporaryID,
						message: wrapperType + ' wrapper created.',
						closable: true,
						closeTimer: 5000
					});
				}
					
			/* Refresh tooltips */
				setupTooltips('iframe');

			return wrapper;

		}

		deleteWrapper = function(wrapperID, force) {

			var wrapper = $i('.wrapper[data-id="' + wrapperID + '"]');

			if ( wrapper.length && (force || confirm('Are you sure you want to remove this wrapper?  All blocks inside the wrapper will be deleted as well.')) ) {

				dataDeleteWrapper(wrapperID);

				wrapper.find('.block').each(function() {
					deleteBlock($(this));
				});

				return wrapper.remove();;

			} else {

				return false;

			}

		}

		addWrapperButtons = function(wrappers) {

			wrappers.each(function() {

				/* Don't add the buttons again */
				if ( $(this).find('.wrapper-buttons').length )
					return;

				var wrapperButtons = $i('#wrapper-buttons-template').first()
					.clone()
					.attr('id', '')
					.addClass('wrapper-buttons');

				return wrapperButtons.prependTo($(this));

			});

		}

		bindWrapperMarginButtons = function(elements) {

			var tooltipContentCallback = function(api) {

				var handle = $(api.target);

				if ( !handle.length ) {
					handle = $i('.wrapper-handle[data-dragging]').first();
				}

				var wrapper = handle.closest('.wrapper');
				var marginPosition = handle.hasClass('wrapper-top-margin-handle') ? 'Top' : 'Bottom';

				var currentMargin = '<span style="opacity: .8;">' + marginPosition + ' Margin:</span> ' + wrapper.css('margin' + marginPosition);
				var tooltipHelp = !handle.data('dragging') ? 'Drag to change wrapper\'s <strong>' + marginPosition.toLowerCase() + ' margin</strong><br />' : '';

				return tooltipHelp + currentMargin;

			}

			elements.qtip({
				content: {
					text: tooltipContentCallback
				},
				style: {
					classes: 'qtip-padma'
				},
				show: {
					delay: 10,
					event: 'mouseenter'
				},
				position: {
					my: 'right center',
					at: 'left center',
					container: Padma.iframe.contents().find('body'),
					viewport: $('#iframe-container'),
					effect: false
				}
			})

			elements.custommouse({
				mouseStart: function(e) {

					this.handle = $(e.currentTarget).hasClass('wrapper-margin-handle') ? $(e.currentTarget) : $(e.currentTarget).parents('.wrapper-margin-handle').first();
					this.dragStart = { left: e.pageX, top: e.pageY };
					this.marginToChange = this.handle.hasClass('wrapper-top-margin-handle') ? 'marginTop' : 'marginBottom';

					this.wrapper = $(e.currentTarget).closest('.wrapper');
					this.originalWrapperMargin = parseInt(this.wrapper.css(this.marginToChange).replace('px', ''));

					/* Disable sibling tooltips */
					this.handle.siblings('.wrapper-handle[data-hasqtip], .wrapper-options[data-hasqtip]').each(function() {

						var api = $(this).qtip('api');

						if ( typeof api != 'undefined' && api.rendered ) {
							api.disable();
							api.hide();
						}

					});

					/* Add wrapper drag class to keep buttons from hiding */
					this.wrapper.addClass('wrapper-handle-in-use');

				},

				mouseDrag: function(e) {

					/* Get amount that mouse has dragged */
					var yValue = e.pageY - this.dragStart.top;

					/* Calculate amount to change margin by.  We'll use intervals of 2 that way it's not so touchy when dragging */
					var interval = 2;
					var marginChange = Math.round(yValue / interval);

					var newMargin = this.originalWrapperMargin + marginChange;

					/* If newMargin is negative then make it 0 */
					if ( newMargin < 0 ) {
						newMargin = 0;
					}

					/* Make sure tooltip is showing and set dragging flag that way it doesn't show the drag to change margin part */
						this.handle.attr('data-dragging', true);
						this.handle.qtip('show');
						this.handle.qtip('reposition');
						this.handle.qtip('option', 'content.text', tooltipContentCallback);

					/* Apply the margin */
					this.wrapper.css(this.marginToChange, newMargin);

					/* Send value to DB */
					dataSetDesignEditorProperty({
						element: "wrapper", 
						property: "margin-" + this.marginToChange.replace('margin', '').toLowerCase(), 
						value: newMargin.toString(), 
						specialElementType: "instance", 
						specialElementMeta: "wrapper-" + getWrapperID(this.wrapper)
					});

				},

				mouseStop: function(e) {

					/* Change tooltip flag back */
						this.handle.removeAttr('data-dragging');
						this.handle.data('dragging', false);

					/* Insure tooltip is hidden */
						var qtipAPI = this.handle.qtip('api');

						qtipAPI.hide();
						$i('#qtip-' + qtipAPI.id).hide();

					/* Re-enable sibling tooltips */
					this.handle.siblings('.wrapper-handle, .wrapper-options').qtip('enable');

					/* Remove wrapper drag class to make buttons hide again */
					this.wrapper.removeClass('wrapper-handle-in-use');

				}
			});

		}

	populateWrapperMirrorNotice = function(wrapper) {

		var wrapperMirrorID = getWrapperMirror(getWrapperID(wrapper));

		if ( !wrapperMirrorID )
			return;

		wrapper.find('.wrapper-mirror-notice-id').text(wrapperMirrorID.replace('wrapper-', ''));

		/* Hide the layout of the wrapper being mirrored.  Todo: don't do this. */
		wrapper.find('.wrapper-mirror-notice-layout').hide();

	}

	assignDefaultWrapperID = function() {

		if ( $i('#wrapper-default').length ) {

			var temporaryID = Math.ceil(Math.random() * 1000000000);
			var defaultWrapper = $i('#wrapper-default');

			/* Change the actual element ID */
			defaultWrapper
				.attr('id', 'wrapper-' + temporaryID)
				.attr('data-id', temporaryID)
				.attr('data-temp-id', temporaryID)
				.attr('data-desired-id', null)
				.data('id', temporaryID)
				.data('temp-id', temporaryID)
				.data('desired-id', null);

			/* Create a hidden that way the new wrapper is saved to the DB */
			dataAddWrapper(defaultWrapper, {
				'fluid': false,
				'fluid-grid': false
			}, $i('.wrapper').index(defaultWrapper));

			/* Change all of the blocks inside of the default wrapper to use the new temporary ID */
			defaultWrapper.find('.block').each(function() {

				dataSetBlockWrapper(getBlockID($(this)), temporaryID);

			});

		}

	}

});