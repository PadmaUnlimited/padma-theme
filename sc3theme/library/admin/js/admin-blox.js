(function($) {
$(document).ready(function() {

    /* Notice */
    $('[data-blox-notice]').on('click', '.blox-dismiss-notice, .notice-dismiss', function() {

        var $notice = $(this).closest('.notice');
        var $noticeDismissWP = $notice.find('.notice-dismiss');
        var $noticeDismissBT = $notice.find('.blox-dismiss-notice');

        var noticeID = $notice.data('blox-notice');

        if ($(this).hasClass('blox-dismiss-notice') && $noticeDismissWP.length) {
            $noticeDismissWP.trigger('click');
        } else if ($(this).hasClass('blox-dismiss-notice')) {
            $(this).fadeTo(100, 0, function () {
                $(this).slideUp(100, function () {
                    $(this).remove();
                });
            });
        }

        return $.ajax({
            url: window.ajaxurl,
            type: 'post',
            timeout: 10000, // throw an error if not completed after 30 sec.
            data: {
                'action': 'blox_dismiss_admin_notice',
                'notice-to-dismiss': noticeID
            }
        });

    });

    /* Responsive Grid Notice */
    $('#blox-responsive-grid-notice').on('click', '.button-primary', function() {

        var $notice = $(this).closest('.notice');
        var message = 'Please note: If you run into issues with the Responsive Grid you can disable it under the Grid mode in the Visual Editor.';

        if ( !confirm(message) ) {
            return false;
        }

        $notice.fadeTo(100, 0, function () {
            $(this).slideUp(100, function () {
                $(this).remove();
            });
        });

        return $.ajax({
            url: window.ajaxurl,
            type: 'post',
            timeout: 10000, // throw an error if not completed after 30 sec.
            data: {
                'action': 'blox_enable_responsive_grid'
            }
        });

    });


	/* Big Tabs */
		setupBigTabs = function() {
		
			if ( $('h2.big-tabs-tabs').length === 0 )
				return;
			
			//Bind tab buttons	
			$('h2.big-tabs-tabs a.nav-tab').on('click', function(event){

				var tabID = $(this).attr('href').replace('#tab-', '');
			
				//Stop all other animations
				$('div.big-tabs-container div.big-tab, div.hr-submit, p.submit').stop(true, true);
			
				//Check to make sure tab exists
				if ( $('div.big-tabs-container div#tab-' + tabID + '-content').length === 0 )
					return false;

				//Set tab as active
				$(this).siblings('.nav-tab-active').removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active');
			
				//Hide the submit button so it can be faded in later
				$('div.hr-submit, p.submit').hide();
				
				//Hide/show the tabs accordingly
				$('div.big-tabs-container div.big-tab-visible')
					.removeClass('big-tab-visible')
					.hide();
										
				$('div.big-tabs-container div#tab-' + tabID + '-content')
					.addClass('big-tab-visible')
					.addClass('big-tab-fading')
					.fadeIn(200, function() {
						
						$(this).removeClass('big-tab-fading');

						$('div.hr-submit, p.submit').fadeIn(200);
				
					});

			});

			//Setup display for tabs and tab containers
			if ( window.location.hash.indexOf('tab-') !== -1 && $('div.big-tabs-container div#tab-' + window.location.hash.replace('#tab-', '') + '-content').length === 1 ) {
			
				var tabID = window.location.hash.replace('#tab-', '');
				var tab = $('h2.big-tabs-tabs a[href="#tab-' + tabID + '"]');
			
				//Set tab as active
				tab.addClass('nav-tab-active');
			
				//Show tab's container
				$('div.big-tabs-container div#tab-' + tabID + '-content').fadeIn(200, function() {
					$(this).addClass('big-tab-visible');
				});
			
			} else {
			
				var firstTab = $('h2.big-tabs-tabs a.nav-tab:first');
				var tabID = firstTab.attr('href').replace('#tab-', '');
			
				//Set the tab as active
				firstTab.addClass('nav-tab-active');
			
				//Show first tab's container			
				$('div.big-tabs-container div#tab-' + tabID + '-content').fadeIn(200, function() {
					$(this).addClass('big-tab-visible');
				});
			
			}
			
			//Show the tabs
			$('h2.big-tabs-tabs').animate({opacity: 1}, 200);
			
			//Show the submit HR and submit button
			setTimeout(function(){
				$('div.hr-submit, p.submit').fadeIn(200);
			}, 300);
		
		}
	
		//Call the function now
		setupBigTabs();
	/* End Big Tabs */


	/* Tooltips */
		if ( typeof $().qtip === 'function' ) {
			
			$('label span.label-tooltip').qtip({
				style: {
					classes: 'qtip-blox'
				},
				position: {
					my: 'bottom left',
					at: 'top right'
				}
			});
			
		}
	/* End Tooltips */
	
	
	/* Textareas */
	if ( $('textarea.allow-tabbing').length > 0 )
		$('textarea.allow-tabbing').tabby();
	
	
	/* System Info */
	if ( $('textarea#system-info-textarea').length > 0 ) {
		
		$('textarea#system-info-textarea').qtip({
			style: {
				classes: 'qtip-blox'
			},
			position: {
				my: 'bottom center',
				at: 'top center'
			}
		});
	
		$('textarea#system-info-textarea').bind('mouseup', function() {
		
			$(this)
				.focus()
				.select();
			
		});
		
	}
	
	
	/* SEO Templates */
		if ( $('div#seo-templates').length === 1 ) {
			
			fetchSEOTemplateValues = function(currentPage) {

				seoInputs.each(function() {

					var value = $('input#seo-' + currentPage + '-' + $(this).attr('id')).val();

					/*
					Since checkboxes and traditional inputs are handled differently we have to either
					set the value of regular inputs or set the checkbox as checked.
					*/
					if ( $(this).attr('type') != 'checkbox' ) {

						$(this).val(value);

					} else {

						if ( value == 1 ) {
							$(this).attr('checked', true);
						} else {
							$(this).attr('checked', false);
						}

					}

				});

			}
			
			/* Set Up Initial Values */
			var currentPage = $('div#seo-templates-header select').val();
			var seoInputs = $('div#seo-templates-inputs input, div#seo-templates-inputs textarea');
			
			fetchSEOTemplateValues(currentPage);
			
			/* Bind the page select */
			$('div#seo-templates-header select').bind('change', function() {
				
				currentPage = $(this).val();
				
				fetchSEOTemplateValues(currentPage);
				
			});
			
			/* Bind the inputs */
			seoInputs.bind('click blur', function() {
			
				var hidden = $('input#seo-' + currentPage + '-' + $(this).attr('id'));
				
				/*
				Since checkboxes and traditional inputs are handled differently we have to either
				set the value of regular inputs or set the checkbox as checked.
				*/
				if ( $(this).attr('type') != 'checkbox' ) {
					
					hidden.val($(this).val());
					
				} else {
					
					if ( $(this).is(':checked') ) {
						hidden.val('1');
					} else {
						hidden.val('0');
					}
					
				}
				
			});
			
			/* Bind the advanced options toggle */
			$('h3#seo-templates-advanced-options-title span').bind('click', function(event) {
				
				if ( !$(this).hasClass('seo-advanced-visible') ) {
					
					$('div#seo-templates-advanced-options').fadeIn(250);
					$(this).html('Hide &uarr;').addClass('seo-advanced-visible');
					
					jQuery.scrollTo($('h3#seo-templates-advanced-options-title'), 500, {
						easing: 'swing',
						offset: {top:-10}
					});
										
				} else {
					
					$('div#seo-templates-advanced-options').fadeOut(200);
					$(this).html('Show &darr;').removeClass('seo-advanced-visible');
					
					jQuery.scrollTo($('div#seo-templates'), 300, {
						easing: 'swing',
						offset: {top:-40}
					});
					
				}
				
			});
			
		}
	/* End SEO Templates */
	
});
})(jQuery);