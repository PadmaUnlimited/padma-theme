(function($){

	function is_visible(elem){
		var data = elem.getBoundingClientRect();

		if (
			data.top >= 0 &&
			data.left >= 0 &&
			data.right <= (window.innerWidth || document.documentElement.clientWidth) &&
			data.bottom <= (window.innerHeight || document.documentElement.clientHeight)
		) {
			return true;				
		} else {
			return false;
		}
	}

	if ( typeof PadmaAnimationRulesSelectors != 'undefined' ) {		

		$.each(PadmaAnimationRulesSelectors, function (selector, rule) {			
			$(selector).addClass('animate-' + rule);
		});

		// Always animate
		$('.animate-always').css('animation-play-state','running');

		// Animate only when mouse over		
		$('.animate-on-mouse-over').mouseover(function(){			
			$(this).css('animation-play-state','running');
		});		
		$('.animate-on-mouse-over').mouseleave(function(){			
			$(this).css('animation-play-state','paused');
		});

		// Animate when is in viewport
		$('.animate-when-visible').each(function(){
			if( is_visible( $(this)[0] ) ){
				$(this).css('animation-play-state','running');
			}else{
				$(this).css('animation-play-state','paused');
			}
		});

		$(window).on('scroll',function(){
			$('.animate-when-visible').each(function(){
				if( is_visible( $(this)[0] ) ){
					$(this).css('animation-play-state','running');
				}else{
					$(this).css('animation-play-state','paused');
				}
			});
		});

		
	}

})(jQuery);