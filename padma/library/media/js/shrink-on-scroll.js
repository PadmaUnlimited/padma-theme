(function($){

	if ( typeof PadmaShrinkWrappers != 'undefined' ) {		
		
		$.each(PadmaShrinkWrappers, function (selector, options) {
			$(selector).addClass('shrink');
		})

		window.onscroll = function(){
			
			$.each(PadmaShrinkWrappers, function (selector, options) {
				
				var height = $(selector).attr('data-org-height');
				
				if( typeof height == 'undefined' ){
					height = $(selector).height();
					$(selector).attr('data-org-height',height);					
				}

				var offset = $(selector).offset().top - $(window).scrollTop();
				var ratio = options.shrink_ratio;
				var elements = options.shrink_elements;
				var total = (height * ( ratio / 100));

				console.log(height);


				if( $('#wpadminbar').length > 0  ){
					offset = offset - 32;
				}

				if( $(selector).hasClass('wrapper-first') ){
					if( $(window).scrollTop() > 0 ){
						$(selector).css('height',total);
						if(elements){
							$(selector).find('a, p, li, span, h1, h2, h3, h4, h5, h6').each(function(){
								var font_size = $(this).attr('data-org-fontsize');
								if( typeof font_size == 'undefined' ){
									font_size = $(this).css('font-size').replace('px','');
									$(this).attr('data-org-fontsize',font_size);					
								}
								$(this).css('font-size', (font_size * ( ratio / 100)) + 'px' )	;						
							});
						}
						$(selector).addClass('is_shrinked');	
					}else{
						$(selector).css('height',$(selector).attr('data-org-height'));
						if(elements){
							$(selector).find('a, p, li, span, h1, h2, h3, h4, h5, h6').css('font-size','');
						}
						$(selector).removeClass('is_shrinked');
					}
				}else{

					if( offset < 0){
						$(selector).css('height',total);
						if(elements){
							$(selector).find('a, p, li, span, h1, h2, h3, h4, h5, h6').each(function(){
								var font_size = $(this).attr('data-org-fontsize');
								if( typeof font_size == 'undefined' ){
									font_size = $(this).css('font-size').replace('px','');
									$(this).attr('data-org-fontsize',font_size);					
								}
								$(this).css('font-size', (font_size * ( ratio / 100)) + 'px' )	;						
							});
						}
						$(selector).addClass('is_shrinked');						
					}else{
						$(selector).css('height',$(selector).attr('data-org-height'));
						if(elements){
							$(selector).find('a, p, li, span, h1, h2, h3, h4, h5, h6').css('font-size','');
						}
						$(selector).removeClass('is_shrinked');					
					}

				}

			});
		}
	}

})(jQuery);