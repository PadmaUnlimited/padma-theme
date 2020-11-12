define(['jquery'], function($) {

	rules = [];


	addRule = function(key,value){

		var rule = [];
			rule[0] = key;
			rule[1] = value;

		this.rules.push(rule);
	}

	getRules = function(){
		return this.rules;
	}

	initialRules = function(){
		this.addRule('-webkit-animation-duration','1s');
		this.addRule('-webkit-animation-fill-mode','both');
		this.addRule('animation-fill-mode','both');
	}


	animationBounce = function(){
		
	}


	return {
		bounce : function(){			
			initialRules();
			animationBounce();
			return getRules();
		}
	}

});