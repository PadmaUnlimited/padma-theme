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
	 * Read cookie
	 *
	 */	
	readCookie = function(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
		    var c = ca[i];
		    while (c.charAt(0)==' ') c = c.substring(1,c.length);
		    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}

	/**
	 *
	 * Erease cookie
	 *
	 */	
	eraseCookie = function(name) {
		createCookie(name,"",-1);
	}

	/**
	 *
	 * Initialize
	 *
	 */
	return {
		init: function(){

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
	}
});