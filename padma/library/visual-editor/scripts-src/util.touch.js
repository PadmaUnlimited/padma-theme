
/* TOUCH OPTIMIZATION */
	/* Keep Menu From Making the Whole VE From Bouncing on iPad */
	$('#menu').on('touchmove', function(event) {
		event.preventDefault();
	})
/* END TOUCH OPTIMIZATION */