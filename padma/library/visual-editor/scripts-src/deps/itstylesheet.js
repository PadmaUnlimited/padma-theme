/*
	JS Stylesheet Library
	Created by Chris Jean
	Licensed under GPL v2
	
	Version 0.1
*/
function ITStylesheet(args, action) {
				
	if('undefined' !== typeof args.document) {
		this.document = args.document;
		delete args.document;
	}			
	
	this.property_dom_names = {};
	this.property_standard_names = {};
	this.converted_rgb_values = {};
	
	this.args = ('undefined' !== typeof args) ? args : {};
	this.action = ('undefined' !== typeof action) ? action : 'load';
			
	this.init = function() {
		if('find' === this.action)
			this._find_stylesheet();
		else
			this._load_stylesheet();
	}
	
	
	this._load_stylesheet = function() {
		args = this.args;
		
		var new_style_node;
		
		if('undefined' !== typeof args.href) {
			new_style_node = this.document.createElement('link');
			new_style_node.href = args.href;
			
			this.type = 'link';
		}
		else {
			new_style_node = this.document.createElement('style');
			
			this.type = 'style';
		}
		
		new_style_node.type = 'text/css';
		
		if('undefined' !== typeof args.title)
			new_style_node.title = args.title;
		if('undefined' !== typeof args.rel)
			new_style_node.rel = args.rel;
		if('undefined' !== typeof args.media)
			new_style_node.media = args.media;
		
		if(('undefined' !== typeof args.href) && ('undefined' === typeof args.rel))
			new_style_node.rel = 'stylesheet';
		
		
		var content = '';
		
		if('undefined' !== typeof args.content) {
			content = args.content;
			delete args.content;
		}

		/* Give stylesheet a random ID that way we can find it later */ 
			var randomID = Math.floor((Math.random() * 1000)) + 1;
		
		/* Create stylesheet/link */
			this.$stylesheet_node = jQuery(new_style_node).insertBefore($i('style#live-css-holder')).addClass('ITStylesheet').attr('id', 'itstylesheet-' + randomID);
			this.stylesheet_node = this.$stylesheet_node[0];

		/* Loop through this.document.styleSheets to find the newly added stylesheet */
			var self = this;

			jQuery.each(this.document.styleSheets, function(index, CSSStylesheet) {

				if ( typeof CSSStylesheet.ownerNode.id == 'undefined' || !CSSStylesheet.ownerNode.id || CSSStylesheet.ownerNode.id != 'itstylesheet-' + randomID )
					return;

				self.stylesheet = CSSStylesheet;

				return false;

			});
		
		this._find_rules();
		
		if('' !== content)
			this.set_rules(content);
	}
	
	this._find_stylesheet = function() {
		args = this.args;
				
		for(var i = 0; i < this.document.styleSheets.length; i++) {
            
			if(('undefined' !== typeof args.href) && ((typeof this.document.styleSheets[i].href == 'string' && this.document.styleSheets[i].href.indexOf(args.href) === -1) || !this.document.styleSheets[i].href) )
				continue;
			if(('undefined' !== typeof args.title) && (args.title !== this.document.styleSheets[i].title))
				continue;
			if(('undefined' !== typeof args.rel) && (args.rel !== this.document.styleSheets[i].rel))
				continue;
			if(('undefined' !== typeof args.media) && (args.media !== this.document.styleSheets[i].media))
				continue;
			if(('undefined' !== typeof args.type) && (args.type !== this.document.styleSheets[i].type))
				continue;
			if(('undefined' !== typeof args.disabled) && (args.disabled !== this.document.styleSheets[i].disabled))
				continue;
				
			this.type = 'link';
						
			this.stylesheet = this.document.styleSheets[i];
			this._find_rules();
			break;
		}
	}
	
	this._find_rules = function() {
		
		if(('undefined' === typeof this.stylesheet))
			return;
		
		if(this.stylesheet.cssRules)
			this.rules = this.stylesheet.cssRules;
		else
			this.rules = this.stylesheet.rules;
			
	}
	
	this._get_style_from_declarations = function(declarations) {
		var style = '';
		
		for(property in declarations)
			style += property + ':' + declarations[property] + '; ';
		
		return style;
	}
	
	this._get_rules_obj_from_string = function(rules_string) {
		var rules = {};
		
		var rule_matches = rules_string.match(/\s*[^{;]+\s*{\s*[^{}]+\s*}/g);
		if(-1 === rule_matches)
			return rules;
		
		for(var i = 0; i < rule_matches.length; i++) {
			var rule_parts = rule_matches[i].match(/\s*([^{;]+)\s*{\s*([^{}]+)\s*}/);
			rules[rule_parts[1]] = rule_parts[2];
		}
		
		return rules;
	}
	
	this._get_property_dom_name = function(css_property) {
		if('undefined' !== typeof this.property_dom_names[css_property])
			return this.property_dom_names[css_property];
		
		var property_parts = css_property.split('-');
		
		var property = property_parts.shift();
		
		while(property_parts.length > 0) {
			var part = property_parts.shift();
			part = part.charAt(0).toUpperCase() + part.substr(1);
			
			property += part;
		}
		
		this.property_dom_names[css_property] = property;
		
		return property;
	}
	
	this._get_property_standard_name = function(css_property) {
		if('undefined' !== typeof this.property_standard_names[css_property])
			return this.property_standard_names[css_property];
		
		var property = css_property;
		
		if('padding-right-value' === css_property)
			property = 'padding-right';
		else if('padding-left-value' === css_property)
			property = 'padding-left';
		else if('margin-right-value' === css_property)
			property = 'margin-right';
		else if('margin-left-value' === css_property)
			property = 'margin-left';
		
		this.property_standard_names[css_property] = property;
		
		return property;
	}
	
	this._delete_rule_at_index = function(index) {
		if(this.stylesheet.deleteRule)
			this.stylesheet.deleteRule(index);
		else
			this.stylesheet.removeRule(index);
	}
	
	this._get_stylesheet_rules = function(stylesheet) {
		if(stylesheet.cssRules)
			return stylesheet.cssRules;
		return stylesheet.rules;
	}
	
	this._get_stylesheet_rules_object = function(stylesheet) {
		var raw_rules = this._get_stylesheet_rules(stylesheet);
		
		var declarations = {};
		var selectors = [];
		
		for(var i = 0; i < raw_rules.length; i++) {
			declarations[raw_rules[i].selectorText] = this._get_rule_declarations_object(raw_rules[i]);
			selectors.push(raw_rules[i].selectorText);
		}
		
		selectors.sort();
		
		var rules = {};
		
		for(var i = 0; i < selectors.length; i++)
			rules[selectors[i]] = declarations[selectors[i]];
		
		return rules;
	}
	
	this._get_rule_declarations_object = function(rule_or_node) {
		var declarations = {};
		
		var style_obj;
		
		if(rule_or_node.style)
			style_obj = rule_or_node.style;
		else
			style_obj = rule_or_node;
		
		var properties = [];
		for(var i = 0; i < style_obj.length; i++)
			properties.push(style_obj[i]);
		properties.sort();
		
		for(var i = 0; i < properties.length; i++) {
			var property = this._get_property_standard_name(properties[i]);
			
			if('undefined' !== typeof style_obj[property])
				declarations[property] = style_obj[property];
			else
				declarations[property] = style_obj[this._get_property_dom_name(property)];
		}
		
		return declarations;
	}
	
	
	this.get_rule_index = function(selector) {
		if('undefined' === typeof selector)
			return false;
			
		indexes = new Array();
		
		if(!this.rules)
			this._find_rules();
		if(!this.rules)
			return false;
		
		if('undefined' !== typeof this.rules[selector])
			return selector;

		
		for(var i = 0; i < this.rules.length; i++) {
			if(typeof this.rules[i].selectorText == 'string' && this.rules[i].selectorText.toLowerCase() == selector.toLowerCase())
				indexes.push(i);
		}
				
		if(indexes.length !== 0){
			return indexes[indexes.length-1];
		}
		
		return false;
	}
	
	this.get_rule = function(selector) {
		if('undefined' === typeof selector)
			return false;
		
		var index = this.get_rule_index(selector);
		
		if((false === index) || ('undefined' === typeof this.rules[index]))
			return false;
		
		return this.rules[index];
	}
	
	this.add_rule = function(selector, declarations) {
		return this.update_rule(selector, declarations);
	}
	
	this.update_rule = function(selectors_raw, declarations, split_selectors) {		
		if(('undefined' === typeof this.rules) || ('undefined' === typeof selectors_raw))
			return false;
		if('undefined' === typeof declarations )
			declarations = {};
		if('undefined' === typeof split_selectors)
			split_selectors = false;
		
		if(split_selectors){
			var selectors = selectors_raw.split(',');
		} else {
			var selectors = new Array(selectors_raw);
		}
		
		var rules = [];
		
		for(var i = 0; i < selectors.length; i++) {
			var selector = selectors[i];
			
			if('undefined' === typeof selector)
				continue;
			
			var rule = this.get_rule(selector);
			
			try {
				if(false === rule) {
					var rule_index = this.rules.length;
					string_declarations = ('string' === typeof declarations) ? declarations : this._get_style_from_declarations(declarations);
					
					if(this.stylesheet.addRule)
						this.stylesheet.addRule(selector, string_declarations, rule_index);
					else
						this.stylesheet.insertRule(selector + ' {' + string_declarations + '}', rule_index);
					
					rule = this.rules[rule_index];
				}
				else {
					for(property in declarations) {
						if(rule.style.setAttribute)
							rule.style.setAttribute(property, declarations[property]);
						else
							rule.style.setProperty(property, declarations[property], null);
					}
				}
				
				rules.push(rule);
			}
			catch(error) {}
		}
		
		return rules;
	}
	
	this.delete_all_rules = function() {
		while(this.rules.length > 0)
			this._delete_rule_at_index(0);
	}
	
	this.delete_rule = function(selector) {
		var index = this.get_rule_index(selector);
		
		if(false === index)
			return false;
		
		this._delete_rule_at_index(index);
		
		return true;
	}
	
	this.delete_rule_property = function(selector, property) {
		var tempObject = {};
		tempObject[property] = null;
		
		this.update_rule(selector, tempObject);
	}
	
	this._convert_rgb_to_hex = function(rgb) {
		if('undefined' !== typeof this.converted_rgb_values[rgb])
			return this.converted_rgb_values[rgb];
		
		var digits = /rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/.exec(rgb);
		
		var red = parseInt(digits[1]);
		var green = parseInt(digits[2]);
		var blue = parseInt(digits[3]);
		
		var hex_raw = blue | (green << 8) | (red << 16);
		
		hex = hex_raw.toString(16).toUpperCase();
		
		while(hex.length < 6)
			hex = '0' + hex;
		
		this.converted_rgb_values[rgb] = '#' + hex;
		
		return '#' + hex;
	}
	
	this.get_stylesheet_text = function() {
		var rules = this._get_stylesheet_rules_object(this.stylesheet);
		
		var stylesheet = '';
		var rgb_regex = /^rgb\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*\)/;
		
		for(selector in rules) {
			var properties = '';
			for(property in rules[selector]) {
				var value = rules[selector][property];
				
				if('undefined' === typeof value)
					continue;
				
				if(rgb_regex.test(value))
					value = this._convert_rgb_to_hex(value);
				
				properties += "\t" + property + ": " + value + ";\n";
			}
			
			if('' === properties)
				continue;
			
			if('' !== stylesheet)
				stylesheet += "\n";
			stylesheet += selector + " {\n" + properties + '}';
		}
		
		return stylesheet;
	}
	
	this.get_computed_style = function(node) {
		if(window.getComputedStyle)
			return window.getComputedStyle(node, '');
		return node.currentStyle;
	}
	
	this.set_rules = function(new_style_rules) {
		this.delete_all_rules();
		
		if('string' === typeof new_style_rules)
			new_style_rules = this._get_rules_obj_from_string(new_style_rules);
		
		for(selector in new_style_rules)
			this.update_rule(selector, new_style_rules[selector]);
	}
	
	
	this.init();
	
	return true;
}