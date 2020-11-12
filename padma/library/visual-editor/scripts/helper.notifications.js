/* NOTIFICATIONS */
	showNotification = function(args) {

		var args = $.extend({}, {
			id: null,
			message: null,
			error: false,
			success: false,
			closeTimer: 3000,
			closable: false,
			closeOnEscKey: false,
			closeCallback: function() {},
			closeConfirmMessage: null,
			fadeInDuration: 350,
			timerFadeOutDuration: 1500,
			closeFadeOutDuration: 350,
			doNotShowAgain: false,
			overwriteExisting: false,
			opacity: 1
		}, args);
		
		/* If doNotShowAgain is true and the cookie exists, don't show the notification */
			if ( args.doNotShowAgain && $.cookie('padma-hide-notification-' + args.id) )
				return;

		/* If notification already exists, delete it. */
			if ( $('#notification-' + args.id).length ) {

				if ( !args.overwriteExisting )
					return $('#notification-' + args.id).fadeIn(args.fadeInDuration);
				
				hideNotification(args.id);	

			}
		
		/* Set up notification */
			var notification = $('<div class="notification"><p>' + args.message + '</p></div>');
			
			/* Add attributes */
				/* ID */
					notification.attr('id', 'notification-' + args.id);

				/* Classes */
					if ( args.error )
						notification.addClass('notification-error');

					if ( args.success )
						notification.addClass('notification-success');

					if ( args.closable )
						notification.addClass('notification-closable');

				/* Styling */
					notification
						.css('opacity', args.notification)
						.hide();
			/* End attributes */

			/* Send these args to the notification's data that way callbacks, etc can be used when hide is called */
				notification.data('notification-args', args);
		/* Set up close button and bind it */
			if ( args.closable ) {

				var notificationCloseButton = $('<span class="close">Close</span>');

				var notificationClose = function() {

					if ( args.closeConfirmMessage && !confirm(args.closeConfirmMessage) )
						return false;

					hideNotification(args.id);

					$(document).unbind('.notification_' + args.id);
					$i('html').unbind('.notification_' + args.id);

				}

				notificationCloseButton.appendTo(notification);
				notificationCloseButton.on('click', notificationClose);

				if ( args.closeOnEscKey ) {

					$(document).bind('keyup.notification_' + args.id, notificationClose);
					$i('html').bind('keyup.notification_' + args.id, notificationClose);

				}

			}
		/* End setting up close button */

		/* If there's a close timer, set the timeout up */				
			if ( args.closeTimer ) {

				setTimeout(function() {
					notification.fadeOut(args.timerFadeOutDuration, function() {
						$(this).remove();
					});
				}, args.closeTimer);

			}

		/* Move notification into notification center and make it visible */
			notification
				.appendTo('#notification-center')
				.fadeIn(350);
		
		/* All done, return the notification object */
		return notification;
		
	}


	showErrorNotification = function(args) {

		var args = $.extend({}, {
			error: true,
			closeTimer: 6000
		}, args);

		return showNotification(args);

	}


	hideNotification = function(id, fade) {

		var notification = $('#notification-' + id);

		if ( !notification || !notification.length )
			return false;

		var args = notification.data('notification-args');

		if ( typeof args.closeCallback == 'function' )
			args.closeCallback.apply(notification);

		/* Fade or not */
			if ( typeof fade == 'undefined' || fade ) {

				notification.fadeOut(args.closeFadeOutDuration, function() {

					$(this).remove();

					if ( args.doNotShowAgain )
						$.cookie('padma-hide-notification-' + args.id, true);

				});

			} else {

				notification.remove();

				if ( args.doNotShowAgain )
					$.cookie('padma-hide-notification-' + args.id, true);

			}

		return notification;
		
	}

	updateNotification = function(id, content) {

		var notification = $('#notification-' + id);

		return notification.find('p').html(content);

	}
/* END NOTIFICATIONS */