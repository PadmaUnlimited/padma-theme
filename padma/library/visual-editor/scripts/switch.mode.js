define(['jquery'], function($) {

	mode: 'light',

	/**
	 *
	 * Apply night mode
	 *
	 */	
	applyNight = function (){
		this.mode = 'night';
		$( "body" ).addClass( "night" );
		$( "iframe" ).contents().find("body").addClass("night");
	}
	

	/**
	 *
	 * Apply light mode
	 *
	 */	
	applyDay = function () {
		this.mode = 'light';
		$( "body" ).removeClass( "night" );
		$( "iframe" ).contents().find("body").removeClass("night");
	}


	/**
	 *
	 * Create cookie
	 *
	 */	
	createCookie = function (name,value,days) {
	    var expires = "";
	    if (days) {
	        var date = new Date();
	        date.setTime(date.getTime() + (days*24*60*60*1000));
	        expires = "; expires=" + date.toUTCString();
	    }
	    document.cookie = name + "=" + value + expires + "; path=/";
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
				$('#switch-navigation').parent().find('.slider').addClass('on');
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
			        $(this).parent().find('.slider').addClass('on');
			    } else {
			        applyDay();
			        eraseCookie("night")
			        $(this).parent().find('.slider').removeClass('on');
			    }
			});
			
		},

		mode: function(){
			return readCookie("night");
		}

	}
});