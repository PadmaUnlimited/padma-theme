/*!
CSS3 ColorPicker (https://github.com/gruppler/CSS3-Colorpicker)
v1.3.2
Copyright (c) 2011 Craig Laparo (https://plus.google.com/114746898337682206892)
Based on "PhotoShop-like JavaScript Color Picker"
Copyright (c) 2007 John Dyer (http://johndyer.name)
MIT style license

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
*/

(function($){

var PROP_NAME = 'css3colorpicker';
var mainDivId = 'css3colorpicker-div';
var colorDivClass = 'color';
var cpDiv = $('<div id="' + mainDivId + '"></div>');
cpDiv.swatchContainer = $('<div id="' + mainDivId + '-swatchContainer"></div>');
cpDiv.swatches = $('<div id="' + mainDivId + '-swatches"></div>');
cpDiv.addSwatchButton = $('<div id="' + mainDivId + '-add-swatch-button" title="Add Current Color to Swatches" class="tooltip"></div>');
cpDiv.colorDiv = $('<div id="' + mainDivId + '-color" title="Choose Color and Close Color Picker" class="tooltip"></div>');
cpDiv.oldColorDiv = $('<div id="' + mainDivId + '-colorOld" title="Revert to Previous Color" class="tooltip"></div>');
cpDiv.d1Div = $('<div id="' + mainDivId + '-1d"></div>');
cpDiv.d1Div.control = $('<div id="' + mainDivId + '-1dControl"></div>');
cpDiv.d1Div.colorDiv = $('<div id="' + mainDivId + '-1dColor"></div>');
cpDiv.d1Div.gradientDiv = $('<div id="' + mainDivId + '-1dGradient"></div>');
cpDiv.d2Div = $('<div id="' + mainDivId + '-2d"></div>');
cpDiv.d2Div.control = $('<div id="' + mainDivId + '-2dControl"></div>');
cpDiv.d2Div.colorDiv = $('<div id="' + mainDivId + '-2dColor"></div>');
cpDiv.d2Div.gradientDiv = $('<div id="' + mainDivId + '-2dGradient"></div>');
cpDiv.alphaDiv = $('<div id="' + mainDivId + '-alpha"></div>');
cpDiv.alphaDiv.control = $('<div id="' + mainDivId + '-alphaControl"></div>');
cpDiv.inputContainerHSB = $('<ul id="' + mainDivId + '-inputContainer-hsv" class="css3colorpicker-inputContainer"></ul>');
cpDiv.inputContainerRGBA = $('<ul id="' + mainDivId + '-inputContainer-rgba" class="css3colorpicker-inputContainer"></ul>');
cpDiv.inputContainerHex = $('<ul id="' + mainDivId + '-inputContainer-hex" class="css3colorpicker-inputContainer"></ul>');
cpDiv.inputs = {
	h: $('<input type="text" data-mode="h" id="' + mainDivId + '-h"/>'),
	s: $('<input type="text" data-mode="s" id="' + mainDivId + '-s"/>'),
	v: $('<input type="text" data-mode="v" id="' + mainDivId + '-v"/>'),
	r: $('<input type="text" id="' + mainDivId + '-r"/>'),
	g: $('<input type="text" id="' + mainDivId + '-g"/>'),
	b: $('<input type="text" id="' + mainDivId + '-b"/>'),
	a: $('<input type="text" id="' + mainDivId + '-a"/>'),
	hex: $('<input type="text" id="' + mainDivId + '-hex" maxlength="8" />')
}
cpDiv.append(
	$('<div id="' + mainDivId + '-container"></div>').append(
		$('<div id="' + mainDivId + '-colorContainer"></div>').append(
			cpDiv.colorDiv,
			cpDiv.oldColorDiv
		),
		cpDiv.d1Div.append(cpDiv.d1Div.colorDiv, cpDiv.d1Div.gradientDiv, cpDiv.d1Div.control),
		cpDiv.d2Div.append(cpDiv.d2Div.colorDiv, cpDiv.d2Div.gradientDiv, cpDiv.d2Div.control),
		cpDiv.alphaDiv.append(cpDiv.alphaDiv.control),
		cpDiv.inputContainerHSB.append(
			$('<li>H <span>&deg;</span></li>').append(cpDiv.inputs.h),
			$('<li>S <span>%</span></li>').append(cpDiv.inputs.s),
			$('<li>B <span>%</span></li>').append(cpDiv.inputs.v)
		),
		cpDiv.inputContainerRGBA.append(
			$('<li>R </li>').append(cpDiv.inputs.r),
			$('<li>G </li>').append(cpDiv.inputs.g),
			$('<li>B </li>').append(cpDiv.inputs.b),
			$('<li class="alpha">A <span>%</span></li>').append(cpDiv.inputs.a)
		),
		cpDiv.inputContainerHex.append(
			$('<li># </li>').append(cpDiv.inputs.hex)
		)
	),
	cpDiv.swatchContainer.append(cpDiv.swatches, cpDiv.addSwatchButton)
);

function Colorpicker(){
	this._mainDivId = mainDivId;
	this._colorDivClass = colorDivClass;

	this._defaults = {
		// Options
		showAnim: true,			// Fade in/out
		duration: 200,			// Fade duration
		color: 'FFFFFF',		// Default color
		allowNull: false,		// Allow an empty color value; otherwise default
								//   to the default color
		realtime: true,			// Update instantly
		invertControls: true,	// Invert color of mouse controls based on luminance
		controlStyle: 'simple',	// Mouse control theme [simple|raised|inset];
								//   separate multiple themes with a space
		swatches: true,			// [true|false] to enable/disable,
								//   or an array of hex codes to pre-fill
		alpha: false,			// [true|false] to enable/disable alpha
		alphaHex: false,		// [true|false] to enable/disable 4-byte hex
								//   in the format '#AARRGGBB'

		// Events
		beforeShow: null,		// Fired before the color picker is shown
		onClose: null,			// Fired when the color picker is hidden
		onSelect: null,			// Fired when the color is set
		onAddSwatch: null,		// Fired when a new color swatch is added
		onDeleteSwatch: null
	};
};

$.extend(Colorpicker.prototype, {
	cpDiv: cpDiv,
	mode: 'h',	// [h|s|v]
	markerClassName: 'hasColorpicker',
	controlsClassPrefix: 'controls-',
	minLum: 50,
	swatches: [],
	swatchLimit: 15,


	setDefaults: function(settings){
		extendRemove(this._defaults, settings || {});
		return this;
	},

	_setMode: function(mode){
		if(['h','s','v'].indexOf(mode) >= 0){
			this.cpDiv.removeClass('mode-' + this.mode);
			this.cpDiv.addClass('mode-' + mode);
			this.mode = mode;
			$.colorpicker._updateMaps();
			$.colorpicker._updateControls();
		}
	},

	refresh: function(){
		this._updateColorpicker(true);
		return this;
	},

	color: function(args){
		this.r = 0;
		this.g = 0;
		this.b = 0;
		this.a = 1;

		this.h = 0;
		this.s = 0;
		this.v = 0;
		this.l = 0;

		this.hex = '';
		this.hexa = '';
		this.rgb = 'rgb(0,0,0)';
		this.rgba = 'rgba(0,0,0,1)';

		// Floating-point
		this._a = 1;
		this._h = 0;
		this._s = 0;
		this._v = 0;

		this.setRgb = function(r, g, b, a){
			this.isNull = false;

			this.r = Math.max(0, Math.min(255, Math.round(r)));
			this.g = Math.max(0, Math.min(255, Math.round(g)));
			this.b = Math.max(0, Math.min(255, Math.round(b)));
			this._a = !isset(a) ? this._a || 100 : Math.max(0, Math.min(100, parseFloat(a)));
			this.a = Math.round(this._a);

			var newHsv = $.colorpicker.rgbToHsv(this);
			this._h = newHsv.h;
			this._s = newHsv.s;
			this._v = newHsv.v;
			this.h = Math.round(newHsv.h);
			this.s = Math.round(newHsv.s);
			this.v = Math.round(newHsv.v);
			this.l = $.colorpicker.rgbToLum(this);

			this.hex = $.colorpicker.rgbToHex(this);
			this.hexa = $.colorpicker.rgbToHex(this);
			this.rgb = 'rgb('+this.r+','+this.g+','+this.b+')';
			this.rgba = 'rgba('+this.r+','+this.g+','+this.b+','+this.a/100+')';
		};

		this.setHsv = function(h, s, v, a){
			this.isNull = false;

			this._h = Math.max(0, Math.min(360, parseFloat(h)));
			this._s = Math.max(0, Math.min(100, parseFloat(s)));
			this._v = Math.max(0, Math.min(100, parseFloat(v)));
			this.h = Math.round(this._h);
			this.s = Math.round(this._s);
			this.v = Math.round(this._v);
			this._a = !isset(a) ? this._a || 100 : Math.max(0, Math.min(100, parseFloat(a)));
			this.a = Math.round(this._a);

			var newRgb = $.colorpicker.hsvToRgb(this);
			this.r = Math.round(newRgb.r);
			this.g = Math.round(newRgb.g);
			this.b = Math.round(newRgb.b);
			this.l = $.colorpicker.rgbToLum(this);

			this.hex = $.colorpicker.rgbToHex(newRgb);
			this.hexa = $.colorpicker.rgbToHex(newRgb);
			this.rgb = 'rgb('+this.r+','+this.g+','+this.b+')';
			this.rgba = 'rgba('+this.r+','+this.g+','+this.b+','+this.a/100+')';
		};

		this.setHex = function(hex){
			this.isNull = false;

			this.hexa = $.colorpicker.validateHex(hex);
			this.hex = $.colorpicker.validateHex(hex);

			var newRgb = $.colorpicker.hexToRgb(this.hexa);
			this.r = newRgb.r;
			this.g = newRgb.g;
			this.b = newRgb.b;
			this._a = newRgb.a;
			this.a = Math.round(newRgb.a);

			var newHsv = $.colorpicker.rgbToHsv(newRgb);
			this._h = newHsv.h;
			this._s = newHsv.s;
			this._v = newHsv.v;
			this.h = Math.round(newHsv.h);
			this.s = Math.round(newHsv.s);
			this.v = Math.round(newHsv.v);
			this.l = $.colorpicker.rgbToLum(this);

			this.rgb = 'rgb('+this.r+','+this.g+','+this.b+')';
			this.rgba = 'rgba('+this.r+','+this.g+','+this.b+','+this.a/100+')';
		};

		if(args){
			if('hexa' in args){
				this.setHex(args.hexa);
			}else if('hex' in args){
				this.setHex(args.hex);
			}else if('rgb' in args){
				var rgb = args.rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(0?\.?\d+))?\)/i);
				if(rgb){
					var a = 100;
					if(typeof(rgb[4]) !== 'undefined'){
						a *= rgb[4];
					}
					this.setRgb(rgb[1], rgb[2], rgb[3], a);
				}
			}else if('r' in args){
				this.setRgb(args.r, args.g, args.b, args.a);
			}else if('h' in args){
				this.setHsv(args.h, args.s, args.v, args.a);
			}
		}

		return this;
	},

	_attachColorpicker: function(target, settings){
		var input = $(target);
		if(input.hasClass(this.markerClassName)){
			return;
		}
		input.addClass(this._colorDivClass).addClass(this.markerClassName);
		if(!target.id){
			this.uuid += 1;
			target.id = 'cp' + this.uuid;
		}

		// check for settings on the control itself - in namespace 'color:'
		var inlineSettings = null;
		for(var attrName in this._defaults){
			var attrValue = target.getAttribute('color:' + attrName);
			if(attrValue){
				inlineSettings = inlineSettings || {};
				try{
					inlineSettings[attrName] = eval(attrValue);
				}catch(err){
					inlineSettings[attrName] = attrValue;
				}
			}
		}

		var inst = this._newInst(input);
		inst.settings = $.extend({}, settings || {}, inlineSettings || {});
		if(this._get(inst, 'alpha')){
			input.addClass('alpha')[inst.settings.alphaHex ? 'addClass' : 'removeClass']('alphaHex');
		}else{
			input.removeClass('alpha').removeClass('alphaHex');
		}
		this._setColor(inst, input.val() || input.data('color') || this._get(inst, 'color'), true);

		var swatches = this._get(inst, 'swatches');
		if(swatches){
			this.addSwatch(swatches);
			//this.addSwatch(inst.settings.color, true);
		}

		if(input.is("input")){
			input.focus(function(){
				$.colorpicker._showColorpicker(target);
			}).keyup(function(){
				$.colorpicker._setColor(inst, this.value);
				$.colorpicker._updateColorpicker();
			}).bind("setData.colorpicker", function(e, key, value) {
				inst.settings[key] = value;
			}).bind("getData.colorpicker", function(e, key) {
				return $.colorpicker._get(inst, key);
			});
		}

		input.click(function(){
			$.colorpicker._showColorpicker(target);
		}).bind("refresh", function(){
			var $this = $(this);
			var inst = $.colorpicker._getInst(this);
			$.colorpicker._setColor(inst, input.val() || input.data('color') || $.colorpicker._get(inst, 'color').hexa, true);
			$.colorpicker._updateColorpicker();
		});
	},

	_setColor: function(inst, color, force){
		if(!color || color.isNull){
			color = new $.colorpicker.color({hex:this._defaults.color});
			color.isNull = this._get(inst, 'allowNull');
		}
		if(typeof(color) == 'string' || typeof(color) == 'number'){
			if (color.match(/^rgb/)) {
				color = new $.colorpicker.color({rgb:color});
			} else {
				color = new $.colorpicker.color({hex:color});
			}
		}

		inst.settings.color = new $.colorpicker.color({hex:color.hexa});
		if(!this._isDragging){
			inst.color = new $.colorpicker.color({hex:color.hexa});
		}
		inst.color.isNull = inst.settings.color.isNull = color.isNull;
		this._updateTarget(inst, force);

		if(!this._isLastColor(color)){
			inst.lastColor = color.isNull ? null : color.hexa;
			var onSelect = this._get(inst, 'onSelect');
			if(typeof(onSelect) == 'function'){
				onSelect(color.isNull ? null : color, inst);
			}
		}
	},

	_newInst: function(target){
		var id = target[0].id.replace(/([^F-Za-z0-9_-])/g, '\\\\$1');
		var inst = {
			id: id,
			input: target,
			cpDiv: cpDiv,
			color: new $.colorpicker.color(),
			lastColor: null
		};
		target.data(PROP_NAME, inst);
		return inst;
	},

	_checkExternalClick: function(e){
		if(!$.colorpicker._curInst){
			return;
		}
		var $target = $(e.target);
		if($target[0].id != $.colorpicker._mainDivId &&
			!$target.is('label[for="'+$.colorpicker._curInst.input[0].id+'"]') &&
			$target.parents('#' + $.colorpicker._mainDivId).length == 0 &&
			!$target.hasClass($.colorpicker.markerClassName)
		){
			$.colorpicker._hideColorpicker();
		}
	},

	_optionColorpicker: function(target, name, value){
		var inst = this._getInst(target);
		var show = false;
		if(arguments.length == 2 && typeof name == 'string'){
			return (name == 'defaults' ? $.extend({}, $.colorpicker._defaults) :
				(inst ? (name == 'all' ? $.extend({}, inst.settings) :
				this._get(inst, name)) : null));
		}
		if(inst && this._curInst == inst){
			this._hideColorpicker(target, true);
			show = true;
		}
		var settings = name || {};
		if(typeof name == 'string'){
			settings = {};
			if(inst && name == 'color' && isset(value)){
				var color = value ? new this.color({hex:value}) : new this.color({hex:this._defaults.color});
				color.isNull = !value && this._get(inst, 'allowNull');
				value = color;
				this._setColor(inst, value, true);
				this.addSwatch(value, true);
			}
			if(name == 'swatches' && value){
				this.addSwatch(value);
			}
			settings[name] = value;
		}
		if(inst){
			extendRemove(inst.settings, settings);
		}
		if(show){
			this._showColorpicker(target, true);
		}
	},

	_showColorpicker: function(input, noAnim){
		input = input.target || input;
		var $input = $(input);
		if(input.disabled){
			return;
		}
		var inst = $.colorpicker._getInst(input);
		if(this._curInst && this._curInst != inst){
			this._triggerOnClose();
			this.cpDiv.stop(true, true);
		}
		this._curInst = inst;
		inst.lastColor = inst.color.isNull ? null : inst.color.hexa;
		var a = this._get(inst, 'alpha');
		$.colorpicker._updateColorpicker();
		inst.input.addClass('selected');

		var showAnim = !noAnim && this._get(inst, 'showAnim');
		var duration = this._get(inst, 'duration');
		var postProcess = function(){
			$.colorpicker.cpDiv.addClass('visible');
		};

		var styles = $.colorpicker._get(inst, 'controlStyle').split(/\s+/);
		for(var i = 0; i < styles.length; i++){
			cpDiv.addClass(this.controlsClassPrefix + styles[i]);
		}
		cpDiv[a?'addClass':'removeClass']('alphaOn');
		cpDiv[this._get(inst, 'swatches')?'addClass':'removeClass']('swatchesOn');
		this.cpDiv.oldColorDiv.data('color', inst.color.isNull ? null : inst.color.hexa).css('background-color', inst.color[a?'rgba':'rgb']);

		var beforeShow = this._get(inst, 'beforeShow');
		if(typeof(beforeShow) == 'function'){
			beforeShow(inst.input, inst);
		}

		this._colorpickerShowing = true;
		this.cpDiv[showAnim ? 'fadeIn' : 'show']((showAnim ? duration : null), postProcess);
		if(!showAnim){
			postProcess();
		}

		this._positionColorpicker();
	},

	_positionColorpicker: function(){
		var $input = $.colorpicker._curInst.input;
		var cpDiv = this.cpDiv;
		var offset = $input.offset();
		var width = cpDiv.outerWidth();
		var height = cpDiv.defaultHeight + (this._get(this._getInst($input), 'alpha') ? cpDiv.alphaHeight : 0) + (this._get(this._getInst($input), 'swatches') ? cpDiv.swatchHeight : 0);
		var winWidth = $(window).width();
		var winHeight = $(window).height();
		var margin = 10;
		height += margin;
		offset.left += $input.outerWidth() + margin;
		if(offset.top + height > winHeight){
			offset.top = winHeight - height;
		}
		if(offset.left + width > winWidth){
			offset.left = winWidth - width;
		}
		cpDiv.css({top:0, left:0}).offset(offset);
	},

	_hideColorpicker: function(input, noAnim){
		var inst = this._curInst;
		if(!inst || (input && inst != this._getInst(input))){
			return;
		}
		var postProcess = function(){
			$.colorpicker._triggerOnClose();
			$.colorpicker._curInst = null;
		};
		if(this._colorpickerShowing){
			var showAnim = !noAnim && this._get(inst, 'showAnim');
			var duration = this._get(inst, 'duration');
			this.cpDiv[showAnim ? 'fadeOut' : 'hide']((showAnim ? duration : null), postProcess);
			if(!showAnim){
				postProcess();
			}
			this.cpDiv.removeClass('visible');
			this._colorpickerShowing = false;

			var onClose = this._get(inst, 'onClose');
			if(typeof(onClose) == 'function'){
				onClose(inst.color, inst);
			}
		}else{
			postProcess();
		}
	},

	_triggerOnClose: function(){
		var inst = this._curInst;
		if(!inst){
			return;
		}
		inst.input.removeClass('selected');
		$.colorpicker.addSwatch(inst.color, true);
		this._setColor(inst, inst.color);
		cpDiv.removeClass(this.controlsClassPrefix+'invert');

		var styles = $.colorpicker._get(inst, 'controlStyle').split(/\s+/);
		for(var i = 0; i < styles.length; i++){
			cpDiv.removeClass(this.controlsClassPrefix + styles[i]);
		}
	},

	_updateColorpicker: function(force){
		var inst = this._curInst;
		if(!inst){
			return;
		}
		var a = this._get(inst, 'alpha');
		this.cpDiv.colorDiv.data('color', inst.color.isNull ? null : inst.color.hexa).css('background-color', inst.color[a?'rgba':'rgb']);
		cpDiv.d2Div.control.css('background-color', inst.color.rgb);
		$.colorpicker._updateInputs(force);
		$.colorpicker._updateMaps();
		$.colorpicker._updateControls();
		if(this._get(inst, 'realtime')){
			this._setColor(inst, inst.color, force);
		}
	},

	_updateTarget: function(inst, force){
		var a = this._get(inst, 'alpha');
		var ah = this._get(inst, 'alphaHex');
		if(inst.color.isNull){
			inst.input.parent().addClass('color-null');
		}else{
			inst.input.css({
				backgroundColor: inst.color[a?'rgba':'rgb'],
				color: (inst.color.l < $.colorpicker.minLum) ? '#fff' : '#000'
			}).parent().removeClass('color-null');
		}

		inst.input.data('color', inst.color.isNull ? null : inst.color.hexa);
		if(force || !inst.input.is(':focus')){
			var val = inst.input.val() || '';
			if(inst.color.isNull){
				inst.input.val('');
			}else{
				inst.input.val(
					val.indexOf('#') >= 0 ?
					'#' + inst.color[ah?'hexa':'hex'] :
					inst.color[ah?'hexa':'hex']
				)
			}
			if(val != inst.input.val()){
				inst.input.trigger('change');
			}
		}
	},

	_updateInputs: function(force){
		var inst = this._curInst;
		if(!inst){
			return;
		}
		var ah = this._get(inst, 'alphaHex');
		for(var i in this.cpDiv.inputs){
			if(i && isset(inst.color[i])){
				if(force || !this.cpDiv.inputs[i].is(':focus')){
					if(i == 'hex'){
						this.cpDiv.inputs[i].val(inst.color.isNull ? '' : inst.color[ah ? 'hexa' : i]);
					}else{
						this.cpDiv.inputs[i].val(inst.color[i]);
					}
				}
			}
		}
	},

	_updateMaps: function(){
		var inst = this._curInst;
		if(!inst){
			return;
		}
		this.cpDiv.alphaDiv.css('background-color', inst.color.rgb);
		switch(this.mode){
			case 'h':
				this.cpDiv.d1Div.gradientDiv.css('background', '');
				this.cpDiv.d2Div.colorDiv.css('background-color', new this.color({
					h: inst.color.h,
					s: 100,
					v: 100
				}).rgb);
				this.cpDiv.d1Div.gradientDiv.css('opacity', 1 - inst.color.v/100);
				this.cpDiv.d1Div.colorDiv.css('opacity', inst.color.s/100);
				this.cpDiv.d2Div.colorDiv.css('opacity', 1);
				this.cpDiv.d2Div.gradientDiv.css('opacity', 1);
			break;

			case 's':
				this.cpDiv.d1Div.colorDiv.css('background-color', new this.color({
					h: inst.color.h,
					s: 100,
					v: 100
				}).rgb);
				this.cpDiv.d1Div.gradientDiv.css('opacity', 1 - inst.color.v/100);
				this.cpDiv.d1Div.colorDiv.css('opacity', 1);
				this.cpDiv.d2Div.colorDiv.css('opacity', inst.color.s/100);
				this.cpDiv.d2Div.gradientDiv.css('opacity', 1);
			break;

			case 'v':
				this.cpDiv.d1Div.gradientDiv.css('background', '');
				this.cpDiv.d1Div.colorDiv.css('background-color', new this.color({
					h: inst.color.h,
					s: inst.color.s,
					v: 100
				}).rgb);
				this.cpDiv.d1Div.gradientDiv.css('opacity', 1);
				this.cpDiv.d1Div.colorDiv.css('opacity', 1);
				this.cpDiv.d2Div.colorDiv.css('opacity', 1);
				this.cpDiv.d2Div.gradientDiv.css('opacity', 1 - inst.color.v/100);
			break;
		}
		$.colorpicker._updateControl();
	},

	_updateControls: function(){
		if(!this._curInst || this._isDragging){
			return;
		}
		var inst = this._curInst;

		var x, y, z, a;
		switch(this.mode){
			case 'h':
				x = inst.color._s*255/100;
				y = 255 - inst.color._v*255/100;
				z = 255 - inst.color._h*255/360;
				a = inst.color._a*255/100;
			break;

			case 's':
				x = inst.color._h*255/360;
				y = 255 - inst.color._v*255/100;
				z = 255 - inst.color._s*255/100;
				a = inst.color._a*255/100;
			break;

			case 'v':
				x = inst.color._h*255/360;
				y = 255 - inst.color._s*255/100;
				z = 255 - inst.color._v*255/100;
				a = inst.color._a*255/100;
			break;
		}

		$.colorpicker._moveControl1d(z, true);
		$.colorpicker._moveControl2d(x, y, true);
		$.colorpicker._moveControlAlpha(a, true);
	},

	_moveControl1d: function(z, moveOnly){
		if(!$.colorpicker._curInst) return;
		var inst = $.colorpicker._curInst;

		cpDiv.d1Div.control.css({top: Math.max(0, Math.min(255, Math.round(z)))+'px'});

		if(!moveOnly){
			switch($.colorpicker.mode){
				case 'h':
					z = 360 - z*360/256;
				break;

				case 's':
				case 'v':
					z = 100 - z*100/256;
				break;
			}

			inst.color['_'+$.colorpicker.mode] = z;
			inst.color.setHsv(inst.color._h, inst.color._s, inst.color._v, inst.color.a);
			$.colorpicker._updateColorpicker(true);
		}
	},

	_moveControl2d: function(x, y, moveOnly){
		if(!$.colorpicker._curInst) return;
		var inst = $.colorpicker._curInst;

		cpDiv.d2Div.control.css({
			left: Math.max(0, Math.min(255, Math.round(x)))+'px',
			top: Math.max(0, Math.min(255, Math.round(y)))+'px'
		});

		if(!moveOnly){
			switch($.colorpicker.mode){
				case 'h':
					x = x*100/256;
					y = 100 - y*100/256;
					inst.color._s = x;
					inst.color._v = y;
				break;

				case 's':
				case 'v':
					x = x*360/256;
					y = 100 - y*100/256;
					inst.color._h = x;
					inst.color[$.colorpicker.mode == 's' ? '_v' : '_s'] = y;
				break;
			}

			inst.color.setHsv(inst.color._h, inst.color._s, inst.color._v, inst.color.a);
			$.colorpicker._updateColorpicker(true);
		}
	},

	_moveControlAlpha: function(a, moveOnly){
		if(!$.colorpicker._curInst) return;
		var inst = $.colorpicker._curInst;

		cpDiv.alphaDiv.control.css({left: Math.max(0, Math.min(255, parseInt(a)))+'px'});

		if(!moveOnly){
			a *= 100/256;

			inst.color.a = a;
			inst.color.setHsv(inst.color._h, inst.color._s, inst.color._v, inst.color.a);
			$.colorpicker._updateColorpicker(true);
		}
	},

	_mousemoveControl1d: function(e){
		$.colorpicker._moveControl1d(
			e.pageY - $.colorpicker.cpDiv.d1Div.offset().top
		);
		e.preventDefault();
		return false;
	},

	_mousemoveControl2d: function(e){
		var offset = $.colorpicker.cpDiv.d2Div.offset();
		$.colorpicker._moveControl2d(
			e.pageX - offset.left,
			e.pageY - offset.top
		);
		e.preventDefault();
		return false;
	},

	_mousemoveControlAlpha: function(e){
		$.colorpicker._moveControlAlpha(
			e.pageX - $.colorpicker.cpDiv.alphaDiv.offset().left
		);
		e.preventDefault();
		return false;
	},

	_updateControl: function(){
		if(!this._curInst || !$.colorpicker._get(this._curInst, 'invertControls')){
			return false;
		}

		if(this._curInst.color.l < $.colorpicker.minLum){
			cpDiv.addClass(this.controlsClassPrefix+'invert');
		}else{
			cpDiv.removeClass(this.controlsClassPrefix+'invert');
		}
	},

	_submit: function(color, isSwatch){
		var inst = this._curInst;
		if(!inst){
			return;
		}

		if(!isset(color)){
			var color = inst.color,
				isNull = color.isNull && this._get(inst, 'allowNull');
		}else if(!color){
			var isNull = this._get(inst, 'allowNull');
			color = this._defaults.color;
		}

		if(typeof(color) == 'string' || typeof(color) == 'number'){
			color = new this.color({hex:color});
			if(!this._get(inst, 'alpha')){
				color.a = color._a = 100;
			}
		}else{
			color = new this.color({hex:color[this._get(inst, 'alpha') ? 'hexa' : 'hex']});
		}
		color.isNull = isNull;

		if(this._isCurrentColor(color)){
			$.colorpicker._hideColorpicker();
			isSwatch = true;
		}else{
			$.colorpicker._setColor(inst, color, true);
			$.colorpicker._updateColorpicker(true);
		}

		$.colorpicker.addSwatch(color, isSwatch);
	},

	_isCurrentColor: function(color){
		var inst = this._curInst;
		if(!inst){
			return;
		}
		if(!color || color.isNull){
			return inst.settings.color.isNull && inst.color.isNull;
		}
		if(typeof(color) == 'string' || typeof(color) == 'number'){
			color = new this.color({hex:color});
		}
		return this._get(inst, 'alpha') ? (
			color.hexa == inst.settings.color.hexa &&
			color.hexa == inst.color.hexa
		) : (
			color.hex == inst.settings.color.hex &&
			color.hex == inst.color.hex
		);
	},

	_isLastColor: function(color){
		var inst = this._curInst;
		if(!inst){
			return;
		}
		if(!color || color.isNull){
			return inst.lastColor === null;
		}
		var lastColor = new this.color({hex: inst.lastColor});

		if(typeof(color) == 'string' || typeof(color) == 'number'){
			color = new this.color({hex:color});
		}
		return this._get(inst, 'alpha')
			? color.hexa == lastColor.hexa
			: color.hex == lastColor.hex;
	},

	addSwatch: function(color, newOnly){
		var inst = this._curInst;
		if(inst && !this._get(inst, 'swatches') || !color || color.isNull){
			return false;
		}
		if(typeof(color) == 'string' || typeof(color) == 'number'){
			color = new this.color({hex:color});
		}
		if(color.hexa){
			var index = this.swatches.indexOf(color.hexa);
			if(index < 0){
				this.swatches.unshift(color.hexa);
				var swatch = $('<div/>')
					.addClass('swatch')
					.attr('title', 'Right-click to delete swatch')
					.data('color', color.hexa)
					.css({
						backgroundColor: color.rgb,
						width: 0,
						opacity: 0
					})
					.append($('<div/>').css('background', color.rgba));
				window.setTimeout(function(){
					swatch.css({
						width: '',
						opacity: 1
					});
				},0);
				this.cpDiv.swatches.prepend(swatch);
			}else{
				if(newOnly){
					return false;
				}
				this.swatches.splice(index, 1);
				this.swatches.unshift(color.hexa);
				this.cpDiv.swatches.prepend(this.cpDiv.swatches.children().eq(index));
			}
			if(this.swatchLimit){
				this.swatches = this.swatches.slice(0, this.swatchLimit);
			}

			var onAddSwatch = inst ?
				this._get(inst, 'onAddSwatch'):
				this._defaults.onAddSwatch;
			if(typeof(onAddSwatch) == 'function'){
				onAddSwatch(color, this.swatches);
			}
		}else if(color.length && color[0]){
			for(var i = color.length - 1; i >= 0; i--){
				this.addSwatch(color[i]);
			}
		}
		return this;
	},

	deleteSwatch: function(swatch) {

		var swatch = $(swatch);

		if ( typeof swatch != 'object' || !swatch || !swatch.length )
			return false;

		this.swatches.splice(this.swatches.indexOf(swatch.data('color')), 1);

		/* Fire callback */
		var inst = this._curInst;

		var onDeleteSwatch = inst ?
			this._get(inst, 'onDeleteSwatch'):
			this._defaults.onDeleteSwatch;

		if ( typeof(onDeleteSwatch) == 'function' )
			onDeleteSwatch(swatch.data('color'), this.swatches);

		/* Remove swatch element */
		swatch.remove();

	},

	clearSwatches: function(){
		this.swatches = [];
		this.cpDiv.swatches.empty();
		return this;
	},

	_useSwatch: function(e){
		$.colorpicker._setColor($.colorpicker._curInst, $(this).data('color'));
		$.colorpicker._updateColorpicker();
		e.preventDefault();
		return false;
	},

	_get: function(inst, key){
		return isset(inst.settings[key]) ? inst.settings[key] : this._defaults[key];
	},

	_getInst: function(target){
		try{
			return $(target).data(PROP_NAME);
		}catch(e){
			throw 'Missing instance data for this colorpicker';
		}
	},

	// Color functions

	hexToRgb: function(hex){
		hex = this.validateHex(hex);

		var r='00', g='00', b='00';

		if(hex.length == 6){
			a = 'FF';
			r = hex.substring(0,2);
			g = hex.substring(2,4);
			b = hex.substring(4,6);
		}
		if(hex.length == 8){
			a = hex.substring(0,2);
			r = hex.substring(2,4);
			g = hex.substring(4,6);
			b = hex.substring(6,8);
		}

		return { r:this.hexToInt(r), g:this.hexToInt(g), b:this.hexToInt(b), a:(100*this.hexToInt(a)/255) };
	},

	_hexRegExp: /[a-f0-9]{0,2}([a-f0-9]{6})|[a-f0-9]?([a-f0-9]{3})/i,
	_hexaRegExp: /([a-f0-9]{8}|[a-f0-9]{6}|[a-f0-9]{4}|[a-f0-9]{3})/i,

	validateHex: function(hex){
		if(!hex) return false;
		hex = (''+hex).match(this._hexaRegExp);
		hex = hex ? (hex[1] || hex[2]) : ('00000000');

		hex = hex.toUpperCase();
		if(hex.length == 3){
			hex = hex.split('');
			hex = [hex[0],hex[0],hex[1],hex[1],hex[2],hex[2]].join('');
		}
		if(hex.length == 4){
			hex = hex.split('');
			hex = [hex[0],hex[0],hex[1],hex[1],hex[2],hex[2],hex[3],hex[3]].join('');
		}
		if(hex.length != 8){
			hex = 'FF'+hex;
		}

		/* If 100% alpha then remove the FF from beginning of hex to normalize things */
		if ( hex.substring(0, 2) == 'FF' )
			hex = hex.substring(2, 8);

		return hex;
	},

	rgbToHex: function(rgb){

		var alphaHex = (this.intToHex(Math.round(rgb.a*255/100)));

		/* If 100% alpha then don't make it an rgba hex, just KISS (http://en.wikipedia.org/wiki/KISS_principle), yo */
		if ( alphaHex == 'FF' )
			alphaHex = '';

		var rgbHex = this.intToHex(rgb.r) + this.intToHex(rgb.g) + this.intToHex(rgb.b);

		return alphaHex + rgbHex;
	},

	intToHex: function(dec){
		var result = (parseInt(dec).toString(16));
		if(result.length == 1)
			result = ("0" + result);
		return result.toUpperCase();
	},

	hexToInt: function(hex){
		return(parseInt(hex,16));
	},

	rgbToLum: function(rgb){
		return Math.abs(Math.round((0.2126*rgb.r + 0.7152*rgb.g + 0.0722*rgb.b)/2.55));
	},

	rgbToHsv: function(rgb){
		var r = rgb.r / 255;
		var g = rgb.g / 255;
		var b = rgb.b / 255;

		hsv = {h:0, s:0, v:0, a:(isset(rgb._a) ? rgb._a : rgb.a)};

		var min = 0
		var max = 0;

		if(r >= g && r >= b){
			max = r;
			min = (g > b) ? b : g;
		}else if(g >= b && g >= r){
			max = g;
			min = (r > b) ? b : r;
		}else{
			max = b;
			min = (g > r) ? r : g;
		}

		hsv.v = max;
		hsv.s = (max) ? ((max - min) / max) : 0;

		if(!hsv.s){
			hsv.h = 0;
		}else{
			delta = max - min;
			if(r == max){
				hsv.h = (g - b) / delta;
			}else if(g == max){
				hsv.h = 2 + (b - r) / delta;
			}else{
				hsv.h = 4 + (r - g) / delta;
			}

			hsv.h = hsv.h * 60;
			if(hsv.h < 0){
				hsv.h += 360;
			}
		}

		hsv.s = Math.abs(hsv.s * 100);
		hsv.v = Math.abs(hsv.v * 100);

		return hsv;
	},

	hsvToRgb: function(hsv){

		rgb = {r:0, g:0, b:0, a:(isset(hsv._a) ? hsv._a : hsv.a)};

		var h = isset(hsv._h) ? hsv._h : hsv.h;
		var s = isset(hsv._s) ? hsv._s : hsv.s;
		var v = isset(hsv._v) ? hsv._v : hsv.v;

		if(s == 0){
			if(v == 0){
				rgb.r = rgb.g = rgb.b = 0;
			}else{
				rgb.r = rgb.g = rgb.b = Math.abs(v * 255 / 100);
			}
		}else{
			if(h == 360){
				h = 0;
			}
			h /= 60;

			// 100 scale
			s = s/100;
			v = v/100;

			var i = parseInt(h);
			var f = h - i;
			var p = v * (1 - s);
			var q = v * (1 - (s * f));
			var t = v * (1 - (s * (1 - f)));
			switch (i){
				case 0:
					rgb.r = v;
					rgb.g = t;
					rgb.b = p;
					break;
				case 1:
					rgb.r = q;
					rgb.g = v;
					rgb.b = p;
					break;
				case 2:
					rgb.r = p;
					rgb.g = v;
					rgb.b = t;
					break;
				case 3:
					rgb.r = p;
					rgb.g = q;
					rgb.b = v;
					break;
				case 4:
					rgb.r = t;
					rgb.g = p;
					rgb.b = v;
					break;
				case 5:
					rgb.r = v;
					rgb.g = p;
					rgb.b = q;
					break;
			}

			rgb.r = Math.abs(Math.round(rgb.r * 255));
			rgb.g = Math.abs(Math.round(rgb.g * 255));
			rgb.b = Math.abs(Math.round(rgb.b * 255));
		}

		return rgb;
	}
});

$.fn.colorpicker = function(options){
	if(!this.length){
		return this;
	}

	if(!$.colorpicker.initialized){
		$(document).mousedown($.colorpicker._checkExternalClick)
			.find('body').append($.colorpicker.cpDiv.hide())
			.find('#'+mainDivId+'-'+$.colorpicker.mode).closest('li').addClass('selected');

		for(var i in cpDiv.inputs){
			if(i){
				var $input = $(cpDiv.inputs[i]);
				if($input.data('mode')){
					$input.focus(function(){
						var $this = $(this);
						$this.closest('li').addClass('selected')
							.siblings('.selected').removeClass('selected');
						$.colorpicker._setMode($this.data('mode'));
					}).closest('li').click(function(){
						$(this).find('input').focus();
					});
				}

				$input.blur(function(){
					$.colorpicker._updateInputs();
				});

				switch(i){
					case 'h':
					case 's':
					case 'v':
					case 'a':
						$input.keydown(function(e){
							if(!$.colorpicker._curInst) return;
							var $this = $(this);
							var inst = $.colorpicker._curInst;
							switch(e.keyCode){
								case 38:
								case 40:
									$this.val(parseInt($this.val()) + (e.shiftKey ? 10 : 1) * (e.keyCode == 40 ? -1 : 1));
									inst.color.setHsv(cpDiv.inputs.h.val(), cpDiv.inputs.s.val(), cpDiv.inputs.v.val(), cpDiv.inputs.a.val());
									$.colorpicker._updateColorpicker(true);
								break;

								case 13:
									$.colorpicker._submit();
								break;

								default:
									return;
							}
						}).keyup(function(){
							if(!$.colorpicker._curInst) return;
							$.colorpicker._curInst.color.setHsv(cpDiv.inputs.h.val(), cpDiv.inputs.s.val(), cpDiv.inputs.v.val(), cpDiv.inputs.a.val());
							$.colorpicker._updateColorpicker();
						});
					break;

					case 'r':
					case 'g':
					case 'b':
						$input.keydown(function(e){
							if(!$.colorpicker._curInst) return;
							var $this = $(this);
							var inst = $.colorpicker._curInst;
							switch(e.keyCode){
								case 38:
								case 40:
									$this.val(parseInt($this.val()) + (e.shiftKey ? 10 : 1) * (e.keyCode == 40 ? -1 : 1));
									inst.color.setRgb(cpDiv.inputs.r.val(), cpDiv.inputs.g.val(), cpDiv.inputs.b.val(), cpDiv.inputs.a.val());
									$.colorpicker._updateColorpicker(true);
								break;

								case 13:
									$.colorpicker._submit();
								break;

								default:
									return;
							}
						}).keyup(function(){
							if(!$.colorpicker._curInst) return;
							$.colorpicker._curInst.color.setRgb(cpDiv.inputs.r.val(), cpDiv.inputs.g.val(), cpDiv.inputs.b.val(), cpDiv.inputs.a.val());
							$.colorpicker._updateColorpicker();
						});
					break;

					case 'hex':
						$input.keydown(function(e){
							if(!$.colorpicker._curInst) return;
							switch(e.keyCode){
								case 13:
									$.colorpicker._submit();
								break;

								default:
									return;
							}
						}).keyup(function(){
							var inst = $.colorpicker._curInst;
							if(!inst) return;
							if(!cpDiv.inputs.hex.val()){
								inst.color = new $.colorpicker.color({hex: $.colorpicker._defaults.color});
								inst.color.isNull = $.colorpicker._get(inst, 'allowNull');
								$.colorpicker._updateColorpicker();
							}else{
								inst.color.setHex(cpDiv.inputs.hex.val());
								$.colorpicker._updateColorpicker();
							}
						});
					break;
				}
			}
		}


		/* Swatches */
			/* Add Swatch */
			cpDiv.addSwatchButton.mousedown(function(e) {
				$.colorpicker.addSwatch($.colorpicker._curInst.color);
			});

			/* Use swatch */
			
			cpDiv.swatches.delegate('.swatch', 'click', function(e) {

				/* Proxy that way useSwatch has the right this object */
				var useSwatch = $.proxy($.colorpicker._useSwatch, this);
				useSwatch(e);

				e.preventDefault();

			});

			/* Delete swatch */
			

			cpDiv.swatches.delegate('.swatch', 'contextmenu', function(e) {

				if ( confirm('Are you sure you wish to delete this swatch?') )
					$.colorpicker.deleteSwatch(this);

				e.preventDefault();
				return false;

			});

		

		cpDiv.oldColorDiv.mousedown($.colorpicker._useSwatch);
		cpDiv.colorDiv.mousedown(function(e){
			$.colorpicker._submit($(this).data('color'));
			e.preventDefault();
			return false;
		});

		cpDiv.defaultHeight = cpDiv.outerHeight();
		cpDiv.addClass('swatchesOn');
		cpDiv.swatchHeight = cpDiv.outerHeight() - cpDiv.defaultHeight;
		cpDiv.removeClass('swatchesOn').addClass('alphaOn');
		cpDiv.alphaHeight = cpDiv.outerHeight() - cpDiv.defaultHeight;
		cpDiv.removeClass('alphaOn');

		cpDiv.d1Div.mousedown(function(e){
			$.colorpicker._isDragging = true;
			$.colorpicker._mousemoveControl1d(e);
			$(document).bind('mousemove', $.colorpicker._mousemoveControl1d);
			return false;
		})
		cpDiv.d2Div.mousedown(function(e){
			$.colorpicker._isDragging = true;
			$.colorpicker._mousemoveControl2d(e);
			$(document).bind('mousemove', $.colorpicker._mousemoveControl2d);
			return false;
		})
		cpDiv.alphaDiv.mousedown(function(e){
			$.colorpicker._isDragging = true;
			$.colorpicker._mousemoveControlAlpha(e);
			$(document).bind('mousemove', $.colorpicker._mousemoveControlAlpha);
			return false;
		})
		$(document).mouseup(function(){
			$(document)
				.unbind('mousemove', $.colorpicker._mousemoveControl1d)
				.unbind('mousemove', $.colorpicker._mousemoveControl2d)
				.unbind('mousemove', $.colorpicker._mousemoveControlAlpha);
			$.colorpicker._isDragging = false;
			return false;
		});
		$(window).resize(function(){
			if($.colorpicker._colorpickerShowing){
				$.colorpicker._positionColorpicker();
			}
		})

		$.colorpicker._setMode($.colorpicker.mode);

		$.colorpicker.initialized = true;
	}

	var otherArgs = Array.prototype.slice.call(arguments, 1);
	if(options == 'option' && arguments.length == 2 && typeof arguments[1] == 'string'){
		return $.colorpicker['_' + options + 'Colorpicker'].
			apply($.colorpicker, [this[0]].concat(otherArgs));
	}
	return this.each(function(){
		typeof options == 'string' ?
			$.colorpicker['_' + options + 'Colorpicker'].
				apply($.colorpicker, [this].concat(otherArgs)) :
			$.colorpicker._attachColorpicker(this, options);
	});
};

function extendRemove(target, props) {
	$.extend(target, props);
	for(var name in props){
		if(props[name] == null || props[name] == undefined){
			target[name] = props[name];
		}
	}
	return target;
};

function isset(x){
	return x !== undefined;
}

$.colorpicker = new Colorpicker();
$.colorpicker.initialized = false;
$.colorpicker.uuid = new Date().getTime();

})(jQuery);