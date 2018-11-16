define(['jquery', 'deps/offline'], function($,offline) {

	/**
	 *
	 * Initialize
	 *
	 */
	return {
		init: function(){

			Offline.options = {
				checks: {
					xhr: {
						url: 'admin-ajax.php'
					}
				}
			};


			Offline.on('confirmed-down', function(){
				showErrorNotification({
					id: 'offline-confirmed-down',
					message: 'Internet connection lost. Please check out your connection before continue editing.',
					closeTimer: false
				});
			});

			Offline.on('confirmed-up', function(){
				hideNotification('offline-confirmed-down');
			})
			
		},

	}
});