(function($){

	if ( typeof PadmaShrinkWrappers != 'undefined' ) {		
		
		$.each(PadmaShrinkWrappers, function (selector, options) {
			$(selector).addClass('shrink');
		})

		window.onscroll = function(){

			jQuery('#wrapper-wpm5dbcb291581b1').height()
			
			$.each(PadmaShrinkWrappers, function (selector, options) {

				var height = $(selector).attr('data-org-height');				
				if( typeof height == 'undefined' ){
					height = $(selector).height();
					$(selector).attr('data-org-height',height);					
				}

				var offset = $(selector).offset().top - $(window).scrollTop();
				var ratio = options.shrink_ratio;
				var shrink_images = options.shrink_images;
				var shrink_elements = options.shrink_elements;				
				var total = (height * ( ratio / 100));

				if( ! $(selector).hasClass('is_stuck') ){

					$(selector).css('height','');
					$('#spacer-'+selector.replace('#','')).css('height','');
					if(shrink_images){
						$(selector).find('img').css('height','');
					}
					if(shrink_elements){
						//$(selector).find('a, p, li, span, h1, h2, h3, h4, h5, h6').css('font-size','');	
						$(selector).find('a, p, li, span, h1, h2, h3, h4, h5, h6').removeClass('is_shrinked');					
						$(selector).find('nav').css('max-height','');
					}
					$(selector).removeClass('is_shrinked');
					return;

				}
								

				if( $('#wpadminbar').length > 0  ){
					offset = offset - 32;
				}

				if( $(window).scrollTop() > 0 ){

					$(selector).css('max-height',total);
					$('#spacer-'+selector.replace('#','')).css('height',total);
				
					padding = parseFloat($(selector).css('padding-top').replace('px',''));
					padding = padding + parseFloat($(selector).css('padding-bottom').replace('px',''));

					// If shrink-contained-images is on
					if(shrink_images){

						// images
						$(selector).find('img').each(function(){
							var img_height = $(this).attr('data-org-imgheight');
							if( typeof img_height == 'undefined' ){
								img_height = $(this).css('height').replace('px','');
								$(this).attr('data-org-imgheight',img_height);					
							}
							img_height = img_height - padding;												
							$(this).addClass('is_shrinked');
						});

					}

					// If shrink-contained-elements is on
					if(shrink_elements){

						// Text
						$(selector).find('a, p, li, span, h1, h2, h3, h4, h5, h6').each(function(){
							/*
							var font_size = $(this).attr('data-org-fontsize');
							if( typeof font_size == 'undefined' ){
								font_size = $(this).css('font-size').replace('px','');
								$(this).attr('data-org-fontsize',font_size);					
							}
							$(this).css('font-size', (font_size * ( ratio / 100)) + 'px' );
							*/
							$(this).addClass('is_shrinked');
						});

						// Navs
						$(selector).find('nav').each(function(){
							var nav_height = $(this).attr('data-org-navheight');
							if( typeof nav_height == 'undefined' ){
								nav_height = $(this).css('height').replace('px','');
								$(this).attr('data-org-navheight',nav_height);					
							}
							nav_height = nav_height - padding;
							$(this).css('max-height', (nav_height * ( ratio / 100)) + 'px' )	;						
						});

					}

					$(selector).addClass('is_shrinked');

				}else{

					$(selector).css('max-height','');
					$('#spacer-'+selector.replace('#','')).css('height','');
					if(shrink_images){
						//$(selector).find('img').css('max-height','');
						$(selector).find('img').removeClass('is_shrinked');
					}

					if(shrink_elements){
						//$(selector).find('a, p, li, span, h1, h2, h3, h4, h5, h6').css('font-size','');
						$(selector).find('nav').css('max-height','');
					}
					$(selector).removeClass('is_shrinked');
				}

			});
		}
	}

})(jQuery);