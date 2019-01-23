
/* TOUCH OPTIMIZATION */
	/* Keep Menu From Making the Whole VE From Bouncing on iPad */
	$('#menu').bind('touchmove', function(event) {
		event.preventDefault();
	})
/* END TOUCH OPTIMIZATION */