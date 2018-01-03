define(['jquery', 'switch-mode'], function($) {

	/**
	 *
	 * Apply night mode
	 *
	 */	
	applyNight = function (){
		$( "body" ).addClass( "night" );
	}
	

	/**
	 *
	 * Apply light mode
	 *
	 */	
	applyDay = function () {
	  $( "body" ).removeClass( "night" );
	}

	/**
	 *
	 * Initialize
	 *
	 */
	init = function(){

		/**
		 *
		 * Read cookie
		 *
		 */
		if (readCookie("night") == "true") {
			applyNight();
			$('#switch-style').prop('checked', true);
		} else {
			applyDay();
			$('#switch-style').prop('checked', false);
		}

		/**
		 *
		 * Add listener 
		 *
		 */
		$('#switch-style').change(function() {
		    if ($(this).is(':checked')) {
		        applyNight();
		        createCookie("night",true,999)
		    } else {
		        applyDay();
		        eraseCookie("night")
		    }
		});
		
	}
});