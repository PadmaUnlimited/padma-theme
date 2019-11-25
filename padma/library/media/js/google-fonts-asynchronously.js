(function($){

	if ( typeof fontsToUse != 'undefined' ) {		
		
		var fonts = fontsToUse.split('|');
		var last = fonts.length - 1;
		var newFonts = '';

		fonts[ last ] = fonts[ last ] + '&display=swap';

		for (var i = 0; i < fonts.length; i++) {
			newFonts += fonts[i];
		}

		WebFontConfig = {
		    google: { 
		    	families: [ newFonts ],		    	
		    }
		};
		
	    var wf = document.createElement('script');
	    wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
	    wf.type = 'text/javascript';
	    wf.async = 'true';
	    var s = document.getElementsByTagName('script')[0];
	    s.parentNode.insertBefore(wf, s);
		
	}

})(jQuery);