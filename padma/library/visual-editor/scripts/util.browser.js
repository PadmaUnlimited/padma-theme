define([], function($) {

	/**
	 *
	 * Initialize
	 *
	 */
	return {
		
		is_explorer: function(){
			return (navigator.userAgent.indexOf('MSIE') > -1);
		},

		is_chrome: function(){
			var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;			
			var is_opera = navigator.userAgent.toLowerCase().indexOf("op") > -1;
			if ((is_chrome)&&(is_opera)) { is_chrome = false; }
			return is_chrome;	
		},

		is_safari: function(){
			var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
			var is_safari = navigator.userAgent.indexOf("Safari") > -1;
			var is_midori = navigator.userAgent.indexOf("Midori") > -1;
			
			if ((is_chrome)&&(is_safari)) { is_safari = false; }
			if ((is_midori)&&(is_safari)) { is_safari = false; }

			return is_safari;
		},

		is_opera: function(){
			return (navigator.userAgent.toLowerCase().indexOf("op") > -1);
		},

		is_firefox: function(){
			return (navigator.userAgent.indexOf('Firefox') > -1);
		},

		is_midori: function(){
			return (navigator.userAgent.indexOf("Midori") > -1);
		}
	}
});