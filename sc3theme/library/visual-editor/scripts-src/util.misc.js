define(['jquery'], function($) {

	/* MISCELLANEOUS FUNCTIONS */
		/* Add query string parameter :: http://stackoverflow.com/a/6021027 */
		updateQueryStringParameter = function(uri, key, value) {

			var re = new RegExp("([?|&])" + key + "=.*?(&|$)", "i");
			var separator = uri.indexOf('?') !== -1 ? "&" : "?";

			if (uri.match(re)) {
				return uri.replace(re, '$1' + key + "=" + value + '$2');
			} else {
				return uri + separator + key + "=" + value;
			}

		}

		/* Reversing jQuery results */
		jQuery.fn.reverse = [].reverse;

		/* Simple rounding function */
		Number.prototype.toNearest = function(num){
			return Math.round(this/num)*num;
		}

		/* Add precision to Math.round */
		Math._round = Math.round;

		Math.round = function(number, precision) {

			precision = Math.abs(parseInt(precision)) || 0;

			var coefficient = Math.pow(10, precision);

			return Math._round(number * coefficient) / coefficient;

		}



		/* Nifty little function to repeat a string n times */
		String.prototype.repeatStr = function(n) {
			if ( n <= 0 ) {
				return '';
			}

		    return Array.prototype.join.call({length:n+1}, this);
		};


		/* Function to capitalize every word in string */
		String.prototype.capitalize = function(){
			return this.replace( /(^|\s)([a-z])/g , function(m,p1,p2){ return p1+p2.toUpperCase(); } );
		}


		btBoolean = function(value) {

			/* boolean to boolean */
			if ( typeof value == 'boolean' ) {

				return value;

			/* Undefined to boolean */
			} else if ( typeof value == 'undefined' ) {

				return false;

			/* number to boolean */
			} else if ( typeof value == 'number' ) {

				if ( value === 1 ) {

					return true;

				} else if ( value === 0  ) {

					return false;

				} else {

					return null;

				}

			/* everything else: null to boolean and string to boolean */
			} else {

				if ( value === null ) {

					return false;

				} else if ( typeof value == 'string' ) {

					var string = value.split(/\b/g);

					if ( string[0] === '1' || string[0] === 'true' ) {

						return true;

					} else if ( string[0] === '0' || string[0] === 'false' ) {

						return false;

					} else {

						return null;

					}

				} else {

					return null;

				}

			}

		}


		/* Change integer 1 and integer 0 to boolean values */
		Number.prototype.toBool = function(){

			if ( this === 1 ) {

				return true;

			} else if ( this === 0  ) {

				return false;

			} else {

				return null;

			}

		}


		/* Change string 1, 0, true, and false to boolean values */
		String.prototype.toBool = function(){

			/* I'm still confused about this, but this changes the weird object of letters into an array of words */
			var string = this.split(/\b/g);

			if ( string[0] === '1' || string[0] === 'true' ) {

				return true;

			} else if ( string[0] === '0' || string[0] === 'false' ) {

				return false;

			} else {

				return null;

			}

		}

		/* Escape HTMl */
		String.prototype.escapeHTML = function() {

			return this
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');

		}
	/* END MISCELLANEOUS FUNCTIONS */

});