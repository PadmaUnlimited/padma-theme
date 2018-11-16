define(['jquery'], function($) {


	/**
	 *
	 * Devices actions on clic
	 *
	 */
	devicesMenu = function(){
		$(document).on('click','.devices-wrapper button',function(){
			applyMode($(this).data('device'));
		});
	}
	

	/**
	 *
	 * Apply mode
	 *
	 */	
	applyMode = function(mode) {
		
		/**
		 *
		 * Remove classes
		 *
		 */		
		$('.devices-wrapper button').removeClass('active');
		$('#customize-preview').removeClass('preview-desktop preview-tablet preview-mobile');

		/**
		 *
		 * Apply classes
		 *
		 */		
		$('.preview-' + mode ).addClass('active');
		$('#customize-preview').addClass('preview-' + mode);

		// set preference
		localStorage['visual-editor-preview-mode'] = mode;

		/**
		 *
		 * Set iframe size
		 *
		 */
		 if(mode == 'tablet'){
		 	$('#customize-preview iframe#content').attr('width',720);
		 }else if (mode == 'mobile') {
		 	$('#customize-preview iframe#content').attr('width',320);
		 }else{
		 	$('#customize-preview iframe#content').removeAttr('width');
		 }
		
		 
	}

	/**
	 *
	 * Initialize
	 *
	 */
	return {
		init: function(){
			var mode = localStorage['visual-editor-preview-mode'];
			if(mode == undefined){
				mode = 'desktop';
			}
			applyMode(mode);

			// Add actions to devices menu
			devicesMenu()
		},

	}
});