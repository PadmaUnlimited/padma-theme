define(['jquery'], function($) {

	/**
	 *
	 * Create localstorage
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
	 * Read localstorage
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
	 * Erease localstorage
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
			 * Read localstorage
			 *
			 */			
			if ( localStorage['visual-editor-enable-navigation'] == 'true') {				
				$('#switch-navigation').prop('checked', true);
				$('#switch-navigation').parent().find('.slider').addClass('on');
			} else {				
				$('#switch-navigation').prop('checked', false);				
			}

			/**
			 *
			 * Add listener 
			 *
			 */
			$('#switch-navigation').change(function() {				
			    if ($(this).is(':checked')) {			        
			        localStorage['visual-editor-enable-navigation'] = true;
			        $(this).parent().find('.slider').addClass('on');
			    
			    } else {
			        localStorage['visual-editor-enable-navigation'] = false;
			        $(this).parent().find('.slider').removeClass('on');
			    }
			    //loadIframe(function(){});
			});
			
		},

		status: function(){
			return localStorage['visual-editor-enable-navigation'];
		}

	}
});