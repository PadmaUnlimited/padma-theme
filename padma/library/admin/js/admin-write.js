(function($) {
$(document).ready(function() {

	/* SEO Live Preview */
		if ( $('div#seo-preview').length == 1 ) {

			//Insert content into preview
			if ( $('textarea#padma-admin-meta-box-seo-description').val().length > 0 ) {
				
				var description = $('textarea#padma-admin-meta-box-seo-description').val();

				if(description.length > 150){
					description = description.substr(0, 150) + ' ...';
				}

				$('div#seo-preview p#seo-preview-description span#text').text(description);
				
			} else {
				
				var excerpt = $('textarea#content').val().replace(/(<([^>]+)>)/ig, '');

				if ( excerpt.length > 150 ) {
					excerpt = excerpt.substr(0, 150) + ' ...';
				}

				$('div#seo-preview p#seo-preview-description span#text').text(excerpt);
				
			}

			if ( $('input#padma-admin-meta-box-seo-title').val().length > 0 ) {
				
				$('div#seo-preview h4').text($('input#padma-admin-meta-box-seo-title').val());
				
			} else if ( $('div#titlediv input#title').val().length > 0 ) {
				
				$('div#seo-preview h4').text($('input#title-seo-template').val().replace('%title%', $('div#titlediv input#title').val()));

			}

			if ( $('span#seo-preview-url').text().length == 0 ) {
				
				$('span#seo-preview-url').text($('span#sample-permalink').text().replace('http://', ''));
				
			}

			//Bind Inputs
			$('input#padma-admin-meta-box-seo-title').bind('keyup blur', function() {
																	
				if ( $(this).val().length > 0 ) {
					
					$('div#seo-preview h4').text($(this).val());
						
				} else {
					
					$('div#seo-preview h4').text($('input#title-seo-template').val().replace('%title%', $('div#titlediv input#title').val()));
					
				}
				
			});

			$('textarea#padma-admin-meta-box-seo-description').bind('keyup blur', function() {
				
				var description = $(this).val();

				if ( description.length > 150 )
					description = description.substr(0, 150) + ' ...';

				if ( $(this).val().length == 0 ) {
					
					var description = $('textarea#content').val().replace(/(<([^>]+)>)/ig, '');

					if ( excerpt.length > 150 ) {
						description = excerpt.substr(0, 150) + ' ...';
					}

				}
				
				$('div#seo-preview p#seo-preview-description span#text').text(description);
				
			});

			$('div#titlediv input#title').bind('keyup blur', function() {
													
				if ( $('input#padma-admin-meta-box-seo-title').val().length == 0 ) {					
					
					$('div#seo-preview h4').text($('input#title-seo-template').val().replace('%title%', $(this).val()));
					
				}
				
			});

			//Periodically check for updates in the content
			setInterval(function() {
				
				if ( $('textarea#padma-admin-meta-box-seo-description').val().length > 0 ) 
					return false;

				if ( $('textarea#content').val() != undefined ){
				  var excerpt = $('textarea#content').val().replace(/(<([^>]+)>)/ig, '');
				} else {
				  var excerpt = $('textarea#excerpt').val();
				}

				if ( excerpt.length > 150 )
					excerpt = excerpt.substr(0, 150) + ' ...';

				$('div#seo-preview p#seo-preview-description span#text').text(excerpt);
				
			}, 4000);

			//Bind Clickables
			$('div#seo-preview h4').bind('click', function(event) {
				
				if ( $('input#padma-admin-meta-box-seo-title').val().length == 0 )
					$('input#padma-admin-meta-box-seo-title').val($(this).text());

				$('input#padma-admin-meta-box-seo-title')
					.focus()
					.css({backgroundColor: '#FFF6BF'})
					.animate({backgroundColor: '#fff'}, 400);

				event.preventDefault();
				
			});

			$('div#seo-preview p#seo-preview-description').on('click', function(event) {
				
				$('textarea#padma-admin-meta-box-seo-description')
					.focus()
					.css({backgroundColor: '#FFF6BF'})
					.animate({backgroundColor: '#fff'}, 400);

				event.preventDefault();
				
			});
		}
	/* End SEO Live Preview */
	
});
})(jQuery);