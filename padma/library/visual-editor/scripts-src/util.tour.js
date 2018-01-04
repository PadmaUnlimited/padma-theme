define(['jquery', 'util.tooltips', 'helper.boxes', 'modules/panel'], function($, tooltips, panel) {

	/* Grid */
	tourStepsGrid = [
		{
			beginning: true,
			title: 'Welcome to the Blox Visual Editor!',
			content: '<p>If this is your first time in the Blox Visual Editor, <strong>we recommend following this tour so you can get the most out of Blox</strong>.</p><p>Or, if you\'re experienced or want to dive in right away, just click the close button in the top right at any time.</p>'
		},

		{
			target: $('li#mode-grid'),
			title: 'Mode Selector',
			content: '<p>The Blox Visual Editor is split up into 2 modes.</p><p><ul><li><strong>Grid</strong> &ndash; Build your layouts</li><li><strong>Design</strong> &ndash; Add colors, customize fonts, and more!</li></ul></p>',
			position: {
				my: 'top left',
				at: 'bottom center'
			}
		},

		{
			target: $('#layout-selector-select-content'),
			title: 'Layout Selector',
			content: '<p style="font-size:12px;">Since you may not want every page to be the same, you may use the Layout Selector to select which page, post, or archive to edit.</p><p style="font-size:12px;">The Layout Selector is based off of inheritance.  For example, you can customize the "Page" layout and all pages will follow that layout.  Plus, you can customize a specific page and it\'ll be different than all other pages.</p><p style="font-size:12px;">The layout selector will allow you to be as precise or broad as you wish.  It\'s completely up to you!</p>',
			position: {
				my: 'top center',
				at: 'bottom center'
			}
		},

		{
			target: $('div#box-grid-wizard'),
			title: 'The Blox Grid',
			content: '<p>Now we\'re ready to get started with the Blox Grid.  In other words, the good stuff.</p><p>To build your first layout, please select a preset to the right to pre-populate the grid.  Or, you may select "Use Empty Grid" to start with a completely blank grid.</p><p>Once you have a preset selected, click "Finish".</p>',
			position: {
				my: 'right top',
				at: 'left center'
			},
			nextHandler: {
				showButton: false,
				clickElement: '#grid-wizard-button-preset-use-preset, span.grid-wizard-use-empty-grid',
				message: 'Please click <strong>"Finish"</strong> or <strong>"Use Empty Grid"</strong> to continue.'
			}
		},

		{
			iframeTarget: 'div.grid-container',
			title: 'Adding Blocks',
			content: '<p>To add a block, simply place your mouse into the grid then click at where you\'d like the top-left point of the block to be.</p><p>Drag your mouse and the block will appear!  Once the block appears, you may choose the block type.</p><p>Hint: Don\'t worry about being precise, you may always move or resize the block.</p>',
			position: {
				my: 'right top',
				at: 'left top',
				adjustY: 100
			},
			maxWidth: 280
		},

		{
			iframeTarget: 'div.grid-container',
			title: 'Modifying Blocks',
			content: '\
					<p style="font-size:12px;">After you\'ve added the desired blocks to your layout, you may move, resize, delete, or change the options of the block at any time.</p>\
					<ul style="font-size:12px;">\
						<li><strong>Moving Blocks</strong> &ndash; Click and drag the block.  If you wish to move multiple blocks simultaneously, double-click on a block to enter <em>Mass Block Selection Mode</em>.</li>\
						<li><strong>Resizing Blocks</strong> &ndash; Grab the border or corner of the block and drag your mouse.</li>\
						<li><strong>Block Options (e.g. header image)</strong> &ndash; Hover over the block then click the block options icon in the top-right.</li>\
						<li><strong>Deleting Blocks</strong> &ndash; Move your mouse over the desired block, then click the <em>X</em> icon in the top-right.</li>\
					</ul>',
			position: {
				my: 'right top',
				at: 'left top',
				adjustY: 100
			},
			maxWidth: 280
		},

		{
			target: $('#save-button-container'),
			title: 'Saving',
			content: '<p>Now that you hopefully have a few changes to be saved, you can save using this spiffy Save button.</p><p>For those of you who like hotkeys, use <strong>Ctrl + S</strong> to save.</p>',
			position: {
				my: 'top right',
				at: 'bottom center'
			},
			tip: 'top right'
		},

		{
			target: $('li#mode-design a'),
			title: 'Design Mode',
			content: '<p>Thanks for sticking with us!</p><p>Now that you have an understanding of the Grid Mode, we hope you stick with us and head on over to the Design Mode.</p>',
			position: {
				my: 'top left',
				at: 'bottom center',
				adjustY: 5
			},
			tip: 'top left',
			buttonText: 'Continue to Design Mode',
			buttonCallback: function () {

				$.post(Blox.ajaxURL, {
					security: Blox.security,
					action: 'padma_visual_editor',
					method: 'ran_tour',
					mode: 'grid',
					complete: function () {

						Blox.ranTour['grid'] = true;

						/* Advance to Design Editor */
						$('li#mode-design a').trigger('click');
						window.location = $('li#mode-design a').attr('href');

					}
				});

			}
		}
	];

	/* Design */
	tourStepsDesign = [
		{
			beginning: true,
			title: 'Welcome to the Blox Design Editor!',
			content: "<p>In the <strong>Design Editor</strong>, you can style your elements however you'd like.</p><p>Whether it's fonts, colors, padding, borders, shadows, or rounded corners, you can use the design editor.</p><p>Stick around to learn more!</p>"
		},

		{
			target: '#side-panel-top',
			title: 'Element Selector',
			content: '<p>The element selector allows you choose which element to edit.</p>',
			position: {
				my: 'right top',
				at: 'left center'
			},
			callback: function () {
				$('li#element-block-header > span.element-name').trigger('click');
			}
		},

		{
			target: '#toggle-inspector',
			title: 'Inspector',
			content: "\
					<p>Instead of using the <em>Element Selector</em>, let the Inspector do the work for you.</p>\
					<p><strong>Try it out!</strong> Point and right-click on the element you wish to edit and it will become selected!</p>\
				",
			position: {
				my: 'top right',
				at: 'bottom center',
				adjustX: 10,
				adjustY: 5
			}
		},

		{
			target: 'window',
			title: 'Have fun building with Blox!',
			content: '<p>We hope you find Blox to the most powerful and easy-to-use WordPress framework around.</p><p>If you have any questions, please don\'t hesitate to visit the <a href="http://support.bloxtheme.com/?utm_source=visualeditor&utm_medium=blox&utm_campaign=tour" target="_blank">support forums</a>.</p>',
			end: true
		}
	];

	return {
		start: function () {

			if ( Blox.mode == 'grid' ) {

				var steps = tourStepsGrid;

				hidePanel();
				openBox('grid-wizard');

			} else if ( Blox.mode == 'design' ) {

				var steps = tourStepsDesign;

				showPanel();

				require(['modules/design/mode-design'], function() {
					showDesignEditor();
				});

				if ( typeof $('div#panel').data('ui-tabs') != 'undefined' )
					selectTab('editor-tab', $('div#panel'));

			} else {

				return;

			}

			$('<div class="black-overlay"></div>')
				.hide()
				.attr('id', 'black-overlay-tour')
				.css('zIndex', 15)
				.appendTo('body')
				.fadeIn(500);

			$(document.body).qtip({
				id: 'tour', // Give it an ID of qtip-tour so we an identify it easily
				content: {
					text: steps[0].content + '<div id="tour-next-container"><span id="tour-next" class="tour-button button button-blue">Continue Tour <span class="arrow">&rsaquo;</span></span></div>',
					title: {
						text: steps[0].title, // ...and title
						button: 'Skip Tour'
					}
				},
				style: {
					classes: 'qtip-tour',
					tip: {
						width: 18,
						height: 10,
						mimic: 'center',
						offset: 10
					}
				},
				position: {
					my: 'center',
					at: 'center',
					target: $(window), // Also use first steps position target...
					viewport: $(window), // ...and make sure it stays on-screen if possible
					adjust: {
						y: 5,
						method: 'shift shift'
					}
				},
				show: {
					event: false, // Only show when show() is called manually
					ready: true, // Also show on page load,
					effect: function () {
						$(this).fadeIn(500);
					}
				},
				hide: false, // Don't hide unless we call hide()
				events: {
					render: function (event, api) {

						$('#iframe-notice').remove();
						hideIframeOverlay();

						openBox('grid-wizard');

						// Grab tooltip element
						var tooltip = api.elements.tooltip;

						// Track the current step in the API
						api.step = 0;

						// Bind custom custom events we can fire to step forward/back
						tooltip.bind('next', function (event) {

							/* For some reason trigger window resizing helps tooltip positioning */
							$(window).trigger('resize');

							// Increase/decrease step depending on the event fired
							api.step += 1;
							api.step = Math.min(steps.length - 1, Math.max(0, api.step));

							// Set new step properties
							currentTourStep = steps[api.step];

							$('div#black-overlay-tour').fadeOut(100, function () {
								$(this).remove();
							});

							//Run the callback if it exists
							if ( typeof currentTourStep.callback === 'function' ) {
								currentTourStep.callback.apply(api);
							}

							if ( currentTourStep.target == 'window' ) {
								currentTourStep.target = $(window);
							} else if ( typeof currentTourStep.target == 'string' ) {
								currentTourStep.target = $(currentTourStep.target);
							} else if ( typeof currentTourStep.iframeTarget == 'string' ) {
								currentTourStep.target = $i(currentTourStep.iframeTarget).first();
							}

							api.set('position.target', currentTourStep.target);

							if ( typeof currentTourStep.maxWidth !== 'undefined' && window.innerWidth < 1440 ) {
								$('.qtip-tour').css('maxWidth', currentTourStep.maxWidth);
							} else {
								$('.qtip-tour').css('maxWidth', 350);
							}

							/* Set up button */
							var buttonText = 'Next';

							if ( typeof currentTourStep.buttonText == 'string' )
								var buttonText = currentTourStep.buttonText;

							if ( typeof currentTourStep.end !== 'undefined' && currentTourStep.end === true ) {
								var button = '<div id="tour-next-container"><span id="tour-finish" class="tour-button button button-blue">Close Tour <span class="arrow">&rsaquo;</span></div>';
							} else if ( typeof currentTourStep.nextHandler === 'undefined' || currentTourStep.nextHandler.showButton ) {
								var button = '<div id="tour-next-container"><span id="tour-next" class="tour-button button button-blue">' + buttonText + ' <span class="arrow">&rsaquo;</span></div>';
							} else {
								var button = '<div id="tour-next-container"><p>' + currentTourStep.nextHandler.message + '</p></div>';
							}

							/* Next Handler Callback... Be able to use something other than the button */
							if ( typeof currentTourStep.nextHandler !== 'undefined' && $(currentTourStep.nextHandler.clickElement) ) {

								var nextHandlerCallback = function (event) {

									$('.qtip-tour').triggerHandler('next');
									event.preventDefault();

									$(this).unbind('click', nextHandlerCallback);

								}

								$(currentTourStep.nextHandler.clickElement).bind('click', nextHandlerCallback);

							}

							/* Set the Content */
							api.set('content.text', currentTourStep.content + button);
							api.set('content.title', currentTourStep.title);

							if ( typeof currentTourStep.end === 'undefined' ) {

								/* Position */
								if ( typeof currentTourStep.position !== 'undefined' ) {

									api.set('position.my', currentTourStep.position.my);
									api.set('position.at', currentTourStep.position.at);

									/* Offset/Adjust */
									if ( typeof currentTourStep.position.adjustX !== 'undefined' ) {
										api.set('position.adjust.x', currentTourStep.position.adjustX);
									} else {
										api.set('position.adjust.x', 0);
									}

									if ( typeof currentTourStep.position.adjustY !== 'undefined' ) {
										api.set('position.adjust.y', currentTourStep.position.adjustY);
									} else {
										api.set('position.adjust.y', 0);
									}

								} else {

									api.set('position.my', 'top center');
									api.set('position.at', 'bottom center');

								}

								if ( typeof currentTourStep.tip !== 'undefined' )
									api.set('style.tip.corner', currentTourStep.tip);

							} else {

								api.set('position.my', 'center');
								api.set('position.at', 'center');

							}


						});

						/* Tour Button Bindings */
						$('div.qtip-tour').on('click', 'span#tour-next', function (event) {

							/* Callback that fires upon button click... Used for advancing to Design Editor */
							if ( typeof currentTourStep == 'object' && typeof currentTourStep.buttonCallback == 'function' )
								currentTourStep.buttonCallback.call();

							$('.qtip-tour').triggerHandler('next');
							event.preventDefault();

						});

						$('div.qtip-tour').on('click', 'span#tour-finish', function (event) {

							$('.qtip-tour').qtip('hide');

						});


					},

					// Destroy the tooltip after it hides as its no longer needed
					hide: function (event, api) {

						$('div#tour-overlay').remove();

						$('div#black-overlay-tour').fadeOut(100, function () {
							$(this).remove();
						});

						$('.qtip-tour').fadeOut(100, function () {
							$(this).qtip('destroy');
						});

						//Tell the DB that the tour has been ran
						if ( Blox.ranTour[Blox.mode] == false && Blox.ranTour.legacy != true ) {

							$.post(Blox.ajaxURL, {
								security: Blox.security,
								action: 'padma_visual_editor',
								method: 'ran_tour',
								mode: Blox.mode
							});

							Blox.ranTour[Blox.mode] = true;

						}

					}
				}
			});

		}
	}

});