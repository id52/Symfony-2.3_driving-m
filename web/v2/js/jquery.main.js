/**
 * jQuery simplebox v2.0.3
 * Copyright (c) 2013 JetCoders
 * email: yuriy.shpak@jetcoders.com
 * www: JetCoders.com
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 **/
;(function(e,t,n){"use strict";var r,i;e.uaMatch=function(e){e=e.toLowerCase();var t=/(opr)[\/]([\w.]+)/.exec(e)||/(chrome)[ \/]([\w.]+)/.exec(e)||/(version)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/.exec(e)||/(webkit)[ \/]([\w.]+)/.exec(e)||/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(e)||/(msie) ([\w.]+)/.exec(e)||e.indexOf("trident")>=0&&/(rv)(?::| )([\w.]+)/.exec(e)||e.indexOf("compatible")<0&&/(mozilla)(?:.*? rv:([\w.]+)|)/.exec(e)||[];var n=/(ipad)/.exec(e)||/(iphone)/.exec(e)||/(android)/.exec(e)||/(windows phone)/.exec(e)||/(win)/.exec(e)||/(mac)/.exec(e)||/(linux)/.exec(e)||[];var r=/(ipad)/.exec(e)||/(iphone)/.exec(e)||/(android)/.exec(e)||/(windows phone)/.exec(e)||[];return{browser:t[3]||t[1]||"",version:t[2]||"0",platform:n[0]||"",mobile:r}};r=e.uaMatch(t.navigator.userAgent);i={};if(r.browser){i[r.browser]=true;i.version=r.version;i.versionNumber=parseFloat(r.version,10)}if(r.platform){i[r.platform]=true}i.mobile=r.mobile.length?true:false;if(i.chrome||i.opr||i.safari){i.webkit=true}if(i.rv){var s="msie";r.browser=s;i[s]=true}if(i.opr){var o="opera";r.browser=o;i[o]=true}i.name=r.browser;i.platform=r.platform;e.browser=i})(jQuery,window);
;(function($){var _condition=function(id,options){if($.simplebox.modal){var data=$.simplebox.modal.data('simplebox');data.onClose($.simplebox.modal);$.simplebox.modal.fadeOut(data.duration,function(){$.simplebox.modal.css({left:'-9999px',top:'-9999px'}).show();data.afterClose($.simplebox.modal);$.simplebox.modal.removeData('simplebox');$.simplebox.modal=false;_toPrepare(id,options);});}else _toPrepare(id,options);},_calcWinWidth=function(){return($(document).width()>$('body').width())?$(document).width():jQuery('body').width();},_toPrepare=function(id,options){$.simplebox.modal=$(id);$.simplebox.modal.data('simplebox',options);var data=$.simplebox.modal.data('simplebox');data.btnClose=$.simplebox.modal.find(data.linkClose);var popupTop=$(window).scrollTop()+($(window).height()/2)-$.simplebox.modal.outerHeight(true)/2;if($(window).scrollTop()>popupTop)popupTop=$(window).scrollTop();if(popupTop+$.simplebox.modal.outerHeight(true)>$(document).height())popupTop=$(document).height()-$.simplebox.modal.outerHeight(true);if(popupTop<0)popupTop=0;if(!data.positionFrom){$.simplebox.modal.css({zIndex:1000,top:popupTop,left:_calcWinWidth()/2-$.simplebox.modal.outerWidth(true)/2}).hide();}else{$.simplebox.modal.css({zIndex:1000,top:$(data.positionFrom).offset().top+$(data.positionFrom).outerHeight(true),left:$(data.positionFrom).offset().left}).hide();}_initAnimate(data);_closeEvent(data,data.btnClose);if(data.overlay.closeClick)_closeEvent(data,$.simplebox.overlay);},_initAnimate=function(data){data.onOpen($.simplebox.modal);if(data.overlay){$.simplebox.overlay.css({background:data.overlay.color,opacity:data.overlay.opacity}).fadeIn(data.duration,function(){$.simplebox.modal.fadeIn(data.duration,function(){$.simplebox.busy=false;data.afterOpen($.simplebox.modal);if($(window).scrollTop()>$.simplebox.modal.offset().top)$('html, body').animate({scrollTop:$.simplebox.modal.offset().top},500);});});}else{$.simplebox.overlay.fadeOut(data.duration);$.simplebox.modal.fadeIn(data.duration,function(){$.simplebox.busy=false;data.afterOpen($.simplebox.modal);if($(window).scrollTop()>$.simplebox.modal.offset().top)$('html, body').animate({scrollTop:$.simplebox.modal.offset().top},500);});}},_closeEvent=function(data,element){element.unbind('click.simplebox').bind('click.simplebox',function(){if(!$.simplebox.busy){$.simplebox.busy=true;data.onClose($.simplebox.modal);$.simplebox.modal.fadeOut(data.duration,function(){$.simplebox.modal.css({left:'-9999px',top:'-9999px'}).show();$.simplebox.overlay.fadeOut(data.duration,function(){data.afterClose($.simplebox.modal);$.simplebox.modal.removeData('simplebox');$.simplebox.modal=false;$.simplebox.busy=false;});});}return false;});},methods={init:function(options){$(this).unbind('click.simplebox').bind('click.simplebox',function(){var data=$(this).data('simplebox');if(!$(this).hasClass(defaults.disableClass)&&!$.simplebox.busy){$.simplebox.busy=true;_condition($(this).attr('href')?$(this).attr('href'):$(this).data('href'),jQuery.extend(true,{},defaults,options));}return false;});return this;},option:function(name,set){if(set){return this.each(function(){var data=$(this).data('simplebox');if(data)data[name]=set;});}else{var ar=[];this.each(function(){var data=$(this).data('simplebox');if(data)ar.push(data[name]);});if(ar.length>1)return ar;else return ar[0];}}},defaults={duration:300,linkClose:'.close, .btn-close',disableClass:'disabled',overlay:{box:'simplebox-overlay',color:'black',closeClick:true,opacity:0.3},positionFrom:false,onOpen:function(){},afterOpen:function(){},onClose:function(){},afterClose:function(){}};$.fn.simplebox=function(method){if(methods[method]){return methods[method].apply(this,Array.prototype.slice.call(arguments,1));}else{if(typeof method==='object'||!method){return methods.init.apply(this,arguments);}else{$.error('Method '+method+' does not exist on jQuery.simplebox');}}};$.simplebox=function(id,options){if(!$.simplebox.busy){$.simplebox.busy=true;_condition(id,jQuery.extend(true,{},defaults,options));}};$.simplebox.init=function(){if(!$.simplebox.overlay){$.simplebox.overlay=jQuery('<div class="'+defaults.overlay.box+'"></div>');jQuery('body').append($.simplebox.overlay);$.simplebox.overlay.css({position:'fixed',zIndex:999,left:0,top:0,width:'100%',height:'100%',background:defaults.overlay.color,opacity:defaults.overlay.opacity}).hide();}$(window).bind('resize.simplebox',function(){if($.simplebox.modal&&$.simplebox.modal.is(':visible')){var data=$.simplebox.modal.data('simplebox');if(!data.positionFrom){$.simplebox.modal.animate({left:_calcWinWidth()/2-$.simplebox.modal.outerWidth(true)/2},{queue:false,duration:$.simplebox.modal.data('simplebox').duration});}else{$.simplebox.modal.animate({top:$(data.positionFrom).offset().top+$(data.positionFrom).outerHeight(true),left:$(data.positionFrom).offset().left},{queue:false,duration:$.simplebox.modal.data('simplebox').duration});}}});};$.simplebox.close=function(){if($.simplebox.modal&&!$.simplebox.busy){var data=$.simplebox.modal.data('simplebox');$.simplebox.busy=true;data.onClose($.simplebox.modal);$.simplebox.modal.fadeOut(data.duration,function(){$.simplebox.modal.css({left:'-9999px',top:'-9999px'}).show();if($.simplebox.overlay)$.simplebox.overlay.fadeOut(data.duration,function(){data.afterClose($.simplebox.modal);$.simplebox.modal.removeData('simplebox');$.simplebox.modal=false;$.simplebox.busy=false;});else{data.afterClose($.simplebox.modal);$.simplebox.modal.removeData('simplebox');$.simplebox.modal=false;$.simplebox.busy=false;}});}};$(document).ready(function(){$.simplebox.init();});})(jQuery);

$(document).ready(function(){
	initFooter();
	jQuery('form').customForm({
		select: {
			elements: 'select.customSelect',
			structure: '<div class="selectArea"><div class="selectIn"><div class="selectText"></div></div></div>',
			text: '.selectText',
			btn: '.selectIn',
			optStructure: '<div class="selectSub"><div class="customScroll"><ul></ul></div><div class="bind"></div></div>',
			maxHeight: 406
		},
		radio: {
			elements: 'input.customRadio'
		},
		checkbox: {
			elements: 'input.customCheckbox'
		}
	});
	$('div.tabs').each(function(){
		$(this).tabs();
	});
	initTimer();
	initRestore();
	$('form').validation({
		onAddClass: function(el, name){
			el.parent().addClass(name);
		}
	});
	if($('.autostart-modal').length > 0){
		$.simplebox('.autostart-modal', {
			overlay:{
				opacity: 0.5
			}
		});
	}
	initQu();
	initTickets();
	initStat();
	$('.simplebox').simplebox({
		overlay:{
			opacity: 0.5
		}
	});
	$('.customScroll').customScrollV();
	initOpen();
});

function initOpen(){
	$('.pop-form').each(function(){
		var hold = $(this);
		var link = hold.find('.toggle');
		var close = hold.find('.close');
		var box = hold.find('> .pop');
		
		link.click(function(){
			if(!hold.hasClass('open')){
				hold.addClass('open');
				box.css({display: 'none'}).slideDown(300);
			}
			else{
				box.slideUp(300, function(){
					hold.removeClass('open');
				});
			}
			return false;
		});
		
		close.click(function(){
			box.slideUp(300, function(){
				hold.removeClass('open');
			});
			return false;
		});
	});
}

function initStat(){
	$('.stat2').each(function(){
		var hold = $(this);
		var scroll = hold.find('.scroll-line .slider');
		var wrapW = hold.find('.scroll').outerWidth();
		var wrap = hold.find('.scroll .holder');
		var w = 0;
		var max = 0;
		
		wrap.each(function(){
			w = $(this).find('.elem:last').position().left + $(this).find('.elem:last').outerWidth(true);
			if(w > max) max = w;
		});
		
		max = max - wrapW;
		
		scroll.slider({
			range: "min",
			min: 0,
			max: 1000,
			value: 0,
			slide: function( event, ui ) {
				$( "#amount" ).val( ui.value );
				wrap.css({left: -(max*(ui.value/1000))});
			}
		});
	});
}

function initTickets(){
	$('.tickets').each(function(){
		var hold = $(this);
		var link = hold.find('.check.all input:checkbox');
		var all = hold.find('.list input:checkbox');
		
		link.change(function (){
			if(link.is(':checked')){
				all.prop('checked', true).customForm('refresh');
				all.parent().parent().addClass('checked');
			}
			else{
				all.prop('checked', false).customForm('refresh');
				all.parent().parent().removeClass('checked');
			}
		});
		all.change(function (){
			if($(this).is(':checked')){
				$(this).parent().parent().addClass('checked');
			}
			else{
				$(this).parent().parent().removeClass('checked');
			}
		});
	});
}

function initQu(){
	$('.hold > .qu').each(function(){
		var hold = $(this);
		var link = hold.find('> .toggle');
		var box = hold.find('> .in');
		var close = hold.find('.close');
		
		link.click(function(){
			if(!hold.hasClass('open')){
				hold.addClass('open');
				box.slideDown(300);
			}
			else{
				box.slideUp(300, function(){
					hold.removeClass('open');
				});
			}
			return false;
		});
		
		close.click(function(){
			box.slideUp(300, function(){
				hold.removeClass('open');
			});
			return false;
		});
		
		$(document).bind('click touchstart mousedown', function(e){
			if(!($(e.target).parents().index(box) != -1 || $(e.target).index(box) != -1)){
				box.slideUp(300, function(){
					hold.removeClass('open');
				});
			}
		});
	});
}

function initRestore(){
	$('.auth-modal .restore').parent().each(function(){
		var hold = $(this);
		var link = hold.find('.pass-toggle');
		var box = hold.find('.restore');
		var close = hold.find('.close-toggle > span');
		
		link.click(function(){
			if(!hold.hasClass('open')){
				hold.addClass('open');
				box.slideDown(300);
			}
			else{
				box.slideUp(300, function(){
					hold.removeClass('open');
				});
			}
			return false;
		});
		close.click(function(){
			box.slideUp(300, function(){
				hold.removeClass('open');
			});
			return false;
		});
	});
}

function initTimer(){
	$('div.timer').each(function(){
		var hold = $(this);
		var day = hold.find('div.num:eq(0)');
		var hours = hold.find('div.num:eq(1)');
		var min = hold.find('div.num:eq(2)');
		var sec = hold.find('div.num:eq(3)');
		var date = (new Date()).getTime();
		var timer = {
			d: day.text()/1,
			h: hours.text()/1,
			m: min.text()/1,
			s: sec.text()/1
		};
		var all = timer.d*24*60*60*1000 + timer.h*60*60*1000 + timer.m*60*1000 + timer.s*1000;
		var now;
		
		function set(){
			day.empty().text(timer.d);
			hours.empty().text(timer.h);
			min.empty().text(timer.m);
			sec.empty().text(timer.s);
		}
		function update(time){
			timer.d = (Math.floor(time/1000/60/60/24)).toString();
			timer.h = (Math.floor(time/1000/60/60) - timer.d*24).toString();
			timer.m = (Math.floor(time/1000/60) - (timer.d*24*60 + timer.h*60)).toString();
			timer.s = (Math.floor(time/1000) - (timer.d*24*60*60 + timer.h*60*60 + timer.m*60)).toString();
			
			if(timer.d.length < 2) timer.d = '0'+timer.d;
			if(timer.h.length < 2) timer.h = '0'+timer.h;
			if(timer.m.length < 2) timer.m = '0'+timer.m;
			if(timer.s.length < 2) timer.s = '0'+timer.s;
		}
		
		setTimeout(function(){
			now = (new Date()).getTime() - date;
			update(all-now);
			set();
			if(all-now >= 0) setTimeout(arguments.callee, 1000);
		}, 900);
	});
}

function initFooter(){
	var footer = $('footer.footer');
	var footerPlace = $('div.footer-place');
	var h = footer.outerHeight();
	
	footer.css({marginTop:-h});
	footerPlace.css({height:h});
}

/**
 * jQuery tabs min v1.0.0
 * Copyright (c) 2011 JetCoders
 * email: yuriy.shpak@jetcoders.com
 * www: JetCoders.com
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 **/

jQuery.fn.tabs=function(options){return new Tabs(this.get(0),options);};function Tabs(context,options){this.init(context,options);}Tabs.prototype={options:{},init:function(context,options){this.options=jQuery.extend({listOfTabs:'a.tab',active:'active',event:'click'},options||{});this.btn=jQuery(context).find(this.options.listOfTabs);this.last=this.btn.index(this.btn.filter('.'+this.options.active));if(this.last==-1)this.last=0;this.btn.removeClass(this.options.active).eq(this.last).addClass(this.options.active);var _this=this;this.btn.each(function(i){if(_this.last==i)jQuery($(this).attr('href')).show();else jQuery($(this).attr('href')).hide();});this.initEvent(this,this.btn);},initEvent:function($this,el){el.bind(this.options.event,function(){if($this.last!=el.index(jQuery(this)))$this.changeTab(el.index(jQuery(this)));return false;});},changeTab:function(ind){jQuery(this.btn.eq(this.last).attr('href')).hide();jQuery(this.btn.eq(ind).attr('href')).show();this.btn.eq(this.last).removeClass(this.options.active);this.btn.eq(ind).addClass(this.options.active);this.last=ind;}}

/**
 * jQuery validation v1.0.6
 * Copyright (c) 2013 JetCoders
 * email: yuriy.shpak@jetcoders.com
 * www: JetCoders.com
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 **/

;(function( $ ){
	
	/**
	 * Private methods 
	 */
	var _checkFields = function(data, withClass){
		data.valid = true;
		if(withClass) data.form.find('.'+data.errorClass, '.'+data.validClass).removeClass(data.errorClass+' '+data.validClass);
		data.elements.each(function(){
			if($(this).data('required') == 'checkbox'){
				if (!$(this).is(':checked')) _addError($(this), data, withClass);
				else _addValid($(this), data, withClass);
			}
			else if($(this).data('required') == 'equal'){
				var $this = $(this);
				var $equal = $('#' + $(this).data('equal'));
				if (!data.reg.empty.test($this.val()) || !data.reg.empty.test($equal.val()) || $this.val() != $equal.val()) _addError($this.add($equal), data, withClass);
				else _addValid($this.add($equal), data, withClass);
			}
			else if($(this).data('required') == 'radio'){
				var $this = $('input:radio[name='+$(this).attr('name')+']');
				var flag = false;
				$this.each(function(){
					if ($(this).is(':checked')) flag = true;
				});
				if(!flag) _addError($this, data, withClass);
				else _addValid($this, data, withClass);
			}
			else{
				if($(this).data('customForm')){
					if($(this).data('customForm').create.eq(0).find('.selectText').text() == $(this).data('placeholder')) _addError($(this), data, withClass);
					else{
						if (!data.reg[$(this).data('required')].test($(this).val()) || $(this).val() == $(this).attr('placeholder')) _addError($(this), data, withClass);
						else _addValid($(this), data, withClass);
					}
				}
				else{
					if (!data.reg[$(this).data('required')].test($(this).val()) || $(this).val() == $(this).attr('placeholder')) _addError($(this), data, withClass);
					else _addValid($(this), data, withClass);
				}
			}
		});
		return !data.valid;
	},
	
	_addError = function(el, data, withClass){
		data.valid = false;
		if (withClass) {
			el.addClass(data.errorClass);
			data.onAddClass(el, data.errorClass);
		}
	},
	
	_addValid = function(el, data, withClass){
		if (withClass) {
			el.addClass(data.validClass);
			data.onAddClass(el, data.validClass);
		}
	},
	
	_test = function(data){
		if (_checkFields(data, false)) {
			data.submit.addClass(data.dasableClass);
		}
		else {
			data.submit.removeClass(data.dasableClass);
		}
	},
	
	_initEvent = function(data){
		data.elements.each(function(){
			if($(this).data('required') == 'checkbox'){
				$(this).bind('change.validation', function(){
					$(this).parent().removeClass(data.errorClass+' '+data.validClass);
					if (!$(this).is(':checked')) _addError($(this), data, true);
					else _addValid($(this), data, true);
					_test(data);
				});
			}
			else if($(this).data('required') == 'radio'){
				var $this = $('input:radio[name='+$(this).attr('name')+']');
				$this.bind('change.validation', function(){
					$this.parent().removeClass(data.errorClass+' '+data.validClass);
					var flag = false;
					$this.each(function(){
						if ($(this).is(':checked')) flag = true;
					});
					if(!flag) _addError($this, data, true);
					else _addValid($this, data, true);
					_test(data);
				});
			}
			else if($(this).data('required') == 'select'){
				$(this).bind('change.validation', function(){
					$(this).parent().removeClass(data.errorClass+' '+data.validClass);
					if (!data.reg[$(this).data('required')].test($(this).val())) _addError($(this), data, true);
					else _addValid($(this), data, true);
					_test(data);
				});
			}
			else if($(this).data('required') == 'equal'){
				var $this = $(this);
				var $equal = $('#' + $(this).data('equal'));
				
				$this.add($equal).each(function(){
					$(this).bind('keyup.validation', function(){
						$this.parent().removeClass(data.errorClass+' '+data.validClass);
						$equal.parent().removeClass(data.errorClass+' '+data.validClass);
						if (!data.reg.empty.test($this.val()) || $this.val() != $equal.val()) _addError($this.add($equal), data, true);
						else _addValid($this.add($equal), data, true);
						_test(data);
					});
				});
			}
			else{
				$(this).bind('keyup.validation', function(){
					$(this).parent().removeClass(data.errorClass+' '+data.validClass);
					if (!data.reg[$(this).data('required')].test($(this).val())) _addError($(this), data, true);
					else _addValid($(this), data, true);
					_test(data);
				});
			}
		});
	},

	/**
	 * Public methods 
	 */
	
	methods = {
		init : function( options ) {
			return this.each(function(){
				var $this = $(this);
				$this.data('validation', jQuery.extend(true, {}, defaults, options));
				var data = $this.data('validation');
				data.reg = data.reg;
				data.form = $this;
				data.elements = data.form.find('[data-required]');
				data.submit = data.form.find(data.submitBtn);
				
				if (data.realTime) {
					_initEvent(data);
					_test(data);
				}
				
				data.submit.click(function(){
					if(_checkFields(data, true)) {
						return data.onError(data);
					}
					return data.onValid(data);
				});
			});
		},
		option: function(name, element){
			if(typeof element != 'object') element = this.eq(0);
			var $this = this.filter(element),
			data = $this.data('validation');
			if(!data) return this;
			
			return data[name];
		},
		destroy : function( ) {
			return this.each(function(){
				var $this = $(this),
				data = $this.data('validation');
				
				data.form.find('*').unbind('.validation');
				data.validation.remove();
				$this.removeData('validation');
			});
		}
	},
	
	/**
	 * Param:
	 * 
	 * data-required="checkbox"
	 * data-required="radio"
	 * data-required="equal" data-equal="id"
	 */
	
	defaults = {
		errorClass: 'error',
		validClass: 'valid',
		dasableClass: 'disabled',
		realTime: false,
		submitBtn: 'input[type=submit], button[type=submit]',
		reg: {
			empty: /\S/,
			email: /^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,4}$/,
			phone: /^([0-9][\-\.\s]{0,1}){7,}$/,
			number: /^[0-9]+$/,
			select: /([^\-]{1})$/,
			card: /^[0-9]{4}[\-\.\s]{0,1}[0-9]{4}[\-\.\s]{0,1}[0-9]{4}[\-\.\s]{0,1}[0-9]{4}$/
		},
		onAddClass: function(){},
		onValid: function(){},
		onError: function(){return false;}
	};
	
	$.fn.validation = function( method ) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else {
			if ( typeof method === 'object' || ! method ) {
				return methods.init.apply( this, arguments );
			} else {
				$.error( 'Method ' +  method + ' does not exist on jQuery.gallery' );
			}
		}
	};
	
})( jQuery );

/**
 * jQuery Custom Form min v1.2.2
 * Copyright (c) 2012 JetCoders
 * email: yuriy.shpak@jetcoders.com
 * www: JetCoders.com
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 **/

;jQuery.fn.customForm = jQuery.customForm = function (_options) {
	var _this = this;
	var methods = {
		destroy: function () {
			var elements;
			if (typeof this === 'function') {
				elements = $('select, input:radio, input:checkbox');
			} else {
				elements = this.add(this.find('select, input:radio, input:checkbox'));
			}
			elements.each(function () {
				var data = $(this).data('customForm');
				if (data) {
					$(this).removeClass('outtaHere');
					if (data['events']) data['events'].unbind('.customForm');
					if (data['create']) data['create'].remove();
					if (data['resizeElement']) data.resizeElement = false;
					$(this).unbind('.customForm');
				}
			});
		},
		refresh: function () {
			if (typeof this === 'function') $('select, input:radio, input:checkbox').trigger('refresh');
			else this.trigger('refresh');
		}
	};
	if (typeof _options === 'object' || !_options) {
		if (typeof _this == 'function') _this = $(document);
		var options = jQuery.extend(true, {
			select: {
				elements: 'select.customSelect',
				structure: '<div class="selectArea"><a tabindex="-1" href="#" class="selectButton"><span class="center"></span><span class="right">&nbsp;</span></a><div class="disabled"></div></div>',
				text: '.center',
				btn: '.selectButton',
				optStructure: '<div class="selectOptions"><ul></ul></div>',
				maxHeight: false,
				topClass: 'position-top',
				optList: 'ul'
			},
			radio: {
				elements: 'input.customRadio',
				structure: '<div></div>',
				defaultArea: 'radioArea',
				checked: 'radioAreaChecked'
			},
			checkbox: {
				elements: 'input.customCheckbox',
				structure: '<div></div>',
				defaultArea: 'checkboxArea',
				checked: 'checkboxAreaChecked'
			},
			disabled: 'disabled',
			hoverClass: 'hover'
		}, _options);
		return _this.each(function () {
			var hold = jQuery(this);
			var reset = jQuery();
			if (this !== document) reset = hold.find('input:reset, button[type=reset]');
			initSelect(hold.find(options.select.elements), hold, reset);
			initRadio(hold.find(options.radio.elements), hold, reset);
			initCheckbox(hold.find(options.checkbox.elements), hold, reset);
		});
	} else {
		if (methods[_options]) {
			methods[_options].apply(this);
		}
	}

	function initSelect(elements, form, reset) {
		elements.not('.outtaHere').each(function () {
			var select = $(this);
			var replaced = jQuery(options.select.structure);
			var selectText = replaced.find(options.select.text);
			var selectBtn = replaced.find(options.select.btn);
			var selectDisabled = replaced.find('.' + options.disabled).hide();
			var optHolder = jQuery(options.select.optStructure);
			var optList = optHolder.find(options.select.optList);
			var html = '';
			var optTimer;
			if (select.prop('disabled')) selectDisabled.show();

			function createStructure() {
				html = '';
				if(select.find('optgroup').length > 0){
					
					select.find('optgroup').each(function(){
						var opt = jQuery(this);
						
						html += '<li><span class="group">'+opt.attr('label')+'</span><ul>';
						
						opt.find('option').each(function () {
							var selOpt = jQuery(this);
							if(selOpt.data('img')){
								if (selOpt.prop('selected')) selectText.html(selOpt.html()+ '<img src="'+selOpt.data('img')+'" />');
								html += '<li data-value="' + selOpt.val() + '" ' + (selOpt.prop('selected') ? 'class="selected"' : '') + '>' + 
										(selOpt.prop('disabled') ? '<span>' : '<a href="#">') + selOpt.html() + '<img src="'+selOpt.data('img')+'" />' + (selOpt.prop('disabled') ? '</span>' : '</a>') + '</li>';
							}
							else{
								if (selOpt.prop('selected')) selectText.html(selOpt.html());
								html += '<li data-value="' + selOpt.val() + '" ' + (selOpt.prop('selected') ? 'class="selected"' : '') + '>' + 
										(selOpt.prop('disabled') ? '<span>' : '<a href="#">') + selOpt.html() + (selOpt.prop('disabled') ? '</span>' : '</a>') + '</li>';
							}
						});
						
						html += '</ul></li>';
					});
				}
				else{
					select.find('option').each(function () {
						var selOpt = jQuery(this);
						if(selOpt.data('img')){
							if (selOpt.prop('selected')) selectText.html(selOpt.html()+ '<img src="'+selOpt.data('img')+'" />');
							html += '<li data-value="' + selOpt.val() + '" ' + (selOpt.prop('selected') ? 'class="selected"' : '') + '>' + 
									(selOpt.prop('disabled') ? '<span>' : '<a href="#">') + selOpt.html() + '<img src="'+selOpt.data('img')+'" />' + (selOpt.prop('disabled') ? '</span>' : '</a>') + '</li>';
						}
						else{
							if (selOpt.prop('selected')) selectText.html(selOpt.html());
							html += '<li data-value="' + selOpt.val() + '" ' + (selOpt.prop('selected') ? 'class="selected"' : '') + '>' + 
									(selOpt.prop('disabled') ? '<span>' : '<a href="#">') + selOpt.html() + (selOpt.prop('disabled') ? '</span>' : '</a>') + '</li>';
						}
					});
				}
				
				if (select.data('placeholder') !== undefined) {
					selectText.html(select.data('placeholder'));
					replaced.addClass('placeholder');
				}
				optList.append(html).find('a').click(function () {
					replaced.removeClass('placeholder');
					optList.find('li').removeClass('selected');
					jQuery(this).parent().addClass('selected');
					select.val(jQuery(this).parent().data('value'));
					selectText.html(jQuery(this).html());
					select.change();
					replaced.removeClass(options.hoverClass);
					optHolder.css({
						left: -9999,
						top: -9999
					});
					return false;
				});
			}
			createStructure();
			replaced.width(select.outerWidth());
			replaced.insertBefore(select);
			replaced.addClass(select.attr('class'));
			optHolder.css({
				width: select.outerWidth(),
				position: 'absolute',
				left: -9999,
				top: -9999
			});
			optHolder.addClass(select.attr('class'));
			jQuery(document.body).append(optHolder);
			select.bind('refresh', function () {
				optList.empty();
				createStructure();
			});
			replaced.hover(function () {
				if (optTimer) clearTimeout(optTimer);
			}, function () {
				optTimer = setTimeout(function () {
					replaced.removeClass(options.hoverClass);
					optHolder.css({
						left: -9999,
						top: -9999
					});
				}, 200);
			});
			optHolder.hover(function () {
				if (optTimer) clearTimeout(optTimer);
			}, function () {
				optTimer = setTimeout(function () {
					replaced.removeClass(options.hoverClass);
					optHolder.css({
						left: -9999,
						top: -9999
					});
				}, 200);
			});
			selectBtn.click(function () {
				if(optHolder.offset().left > 0) {
					replaced.removeClass(options.hoverClass);
					optHolder.css({
						left:-9999,
						top:-9999
					});
				}
				else{
					replaced.addClass(options.hoverClass);
					select.removeClass('outtaHere');
					optHolder.css({
						width: select.outerWidth(),
						top: -9999
					});
					select.addClass('outtaHere');
					if (options.select.maxHeight && optHolder.children().height() > options.select.maxHeight) {
						optHolder.children().css({
							height:options.select.maxHeight, 
							overflow:'auto'
						});
					}
					
					if($(document).height() > optHolder.outerHeight(true) + replaced.offset().top + replaced.outerHeight()){
						optHolder.removeClass(options.select.topClass).css({
							top: replaced.offset().top + replaced.outerHeight(),
							left: replaced.offset().left
						});
						replaced.removeClass(options.select.topClass);
					}
					else{
						optHolder.addClass(options.select.topClass).css({
							top: replaced.offset().top - optHolder.outerHeight(true),
							left: replaced.offset().left
						});
						replaced.addClass(options.select.topClass);
					}
					
					replaced.focus();
					
				}
				return false;
			});
			reset.click(function () {
				setTimeout(function () {
					select.find('option').each(function (i) {
						var selOpt = jQuery(this);
						if (selOpt.val() == select.val()) {
							selectText.html(selOpt.html());
							optList.find('li').removeClass('selected');
							optList.find('li').eq(i).addClass('selected');
						}
					});
				}, 10);
			});
			select.bind('change.customForm', function () {
				if (optHolder.is(':hidden')) {
					select.find('option').each(function (i) {
						var selOpt = jQuery(this);
						if (selOpt.val() == select.val()) {
							selectText.html(selOpt.html());
							optList.find('li').removeClass('selected');
							optList.find('li').eq(i).addClass('selected');
						}
					});
				}
			});
			select.bind('focus.customForm', function () {
				replaced.addClass('focus');
			}).bind('blur.customForm', function () {
				replaced.removeClass('focus');
			});
			select.data('customForm', {
				'resizeElement': function () {
					select.removeClass('outtaHere');
					replaced.width(Math.floor(select.outerWidth()));
					select.addClass('outtaHere');
				},
				'create': replaced.add(optHolder)
			});
			$(window).bind('resize.customForm', function () {
				if (select.data('customForm')['resizeElement']) select.data('customForm').resizeElement();
			});
		}).addClass('outtaHere');
	}

	function initRadio(elements, form, reset) {
		elements.each(function () {
			var radio = $(this);
			if (!radio.hasClass('outtaHere') && radio.is(':radio')) {
				radio.data('customRadio', {
					radio: radio,
					name: radio.attr('name'),
					label: $('label[for=' + radio.attr('id') + ']').length ? $('label[for=' + radio.attr('id') + ']') : radio.parents('label'),
					replaced: jQuery(options.radio.structure, {
						'class': radio.attr('class')
					})
				});
				var data = radio.data('customRadio');
				if (radio.is(':disabled')) {
					data.replaced.addClass(options.disabled);
					if (radio.is(':checked')) data.replaced.addClass('disabledChecked');
				} else if (radio.is(':checked')) {
					data.replaced.addClass(options.radio.checked);
					data.label.addClass('checked');
				} else {
					data.replaced.addClass(options.radio.defaultArea);
					data.label.removeClass('checked');
				}
				data.replaced.click(function () {
					if (jQuery(this).hasClass(options.radio.defaultArea)) {
						radio.change();
						radio.prop('checked', true);
						changeRadio(data);
					}
				});
				reset.click(function () {
					setTimeout(function () {
						if (radio.is(':checked')) data.replaced.removeClass(options.radio.defaultArea + ' ' + options.radio.checked).addClass(options.radio.checked);
						else data.replaced.removeClass(options.radio.defaultArea + ' ' + options.radio.checked).addClass(options.radio.defaultArea);
					}, 10);
				});
				radio.bind('refresh', function () {
					if (radio.is(':checked')) {
						data.replaced.removeClass(options.radio.defaultArea + ' ' + options.radio.checked).addClass(options.radio.checked);
						data.label.addClass('checked');
					} else {
						data.replaced.removeClass(options.radio.defaultArea + ' ' + options.radio.checked).addClass(options.radio.defaultArea);
						data.label.removeClass('checked');
					}
				});
				radio.bind('click.customForm', function () {
					changeRadio(data);
				});
				radio.bind('focus.customForm', function () {
					data.replaced.addClass('focus');
				}).bind('blur.customForm', function () {
					data.replaced.removeClass('focus');
				});
				data.replaced.insertBefore(radio);
				radio.addClass('outtaHere');
				radio.data('customForm', {
					'create': data.replaced
				});
			}
		});
	}

	function changeRadio(data) {
		jQuery('input:radio[name="' + data.name + '"]').not(data.radio).each(function () {
			var _data = $(this).data('customRadio');
			if (_data.replaced && !jQuery(this).is(':disabled')) {
				_data.replaced.removeClass(options.radio.defaultArea + ' ' + options.radio.checked).addClass(options.radio.defaultArea);
				_data.label.removeClass('checked');
			}
		});
		data.replaced.removeClass(options.radio.defaultArea + ' ' + options.radio.checked).addClass(options.radio.checked);
		data.label.addClass('checked');
		data.radio.trigger('change');
	}

	function initCheckbox(elements, form, reset) {
		elements.each(function () {
			var checkbox = $(this);
			if (!checkbox.hasClass('outtaHere') && checkbox.is(':checkbox')) {
				checkbox.data('customCheckbox', {
					checkbox: checkbox,
					label: $('label[for=' + checkbox.attr('id') + ']').length ? $('label[for=' + checkbox.attr('id') + ']') : checkbox.parents('label'),
					replaced: jQuery(options.checkbox.structure, {
						'class': checkbox.attr('class')
					})
				});
				var data = checkbox.data('customCheckbox');
				if (checkbox.is(':disabled')) {
					data.replaced.addClass(options.disabled);
					if (checkbox.is(':checked')) data.replaced.addClass('disabledChecked');
				} else if (checkbox.is(':checked')) {
					data.replaced.addClass(options.checkbox.checked);
					data.label.addClass('checked');
				} else {
					data.replaced.addClass(options.checkbox.defaultArea);
					data.label.removeClass('checked');
				}
				data.replaced.click(function () {
					if (!data.replaced.hasClass('disabled') && !data.replaced.parents('label').length) {
						if (checkbox.is(':checked')) checkbox.prop('checked', false);
						else checkbox.prop('checked', true);
						changeCheckbox(data);
					}
				});
				reset.click(function () {
					setTimeout(function () {
						changeCheckbox(data);
					}, 10);
				});
				checkbox.bind('refresh', function () {
					if (checkbox.is(':checked')) {
						data.replaced.removeClass(options.checkbox.defaultArea + ' ' + options.checkbox.defaultArea).addClass(options.checkbox.checked);
						data.label.addClass('checked');
					} else {
						data.replaced.removeClass(options.checkbox.defaultArea + ' ' + options.checkbox.checked).addClass(options.checkbox.defaultArea);
						data.label.removeClass('checked');
					}
				});
				checkbox.bind('click.customForm', function () {
					changeCheckbox(data);
				});
				checkbox.bind('focus.customForm', function () {
					data.replaced.addClass('focus');
				}).bind('blur.customForm', function () {
					data.replaced.removeClass('focus');
				});
				data.replaced.insertBefore(checkbox);
				checkbox.addClass('outtaHere');
				data.replaced.parents('label').bind('click.customForm', function () {
					if (!data.replaced.hasClass('disabled')) {
						if (checkbox.is(':checked')) checkbox.prop('checked', false);
						else checkbox.prop('checked', true);
						changeCheckbox(data);
					}
					return false;
				});
				checkbox.data('customForm', {
					'create': data.replaced,
					'events': data.replaced.parents('label')
				});
			}
		});
	}

	function changeCheckbox(data) {
		if (data.checkbox.is(':checked')) {
			data.replaced.removeClass(options.checkbox.defaultArea + ' ' + options.checkbox.defaultArea).addClass(options.checkbox.checked);
			data.label.addClass('checked');
		} else {
			data.replaced.removeClass(options.checkbox.defaultArea + ' ' + options.checkbox.checked).addClass(options.checkbox.defaultArea);
			data.label.removeClass('checked');
		}
		data.checkbox.trigger('change');
	}
};

/**
 * jQuery Vertical Custom Scroll v1.0.0
 * Copyright (c) 2013 JetCoders
 * email: yuriy.shpak@jetcoders.com
 * www: JetCoders.com
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 **/

var types=['DOMMouseScroll','mousewheel'];if($.event.fixHooks){for(var i=types.length;i;){$.event.fixHooks[types[--i]]=$.event.mouseHooks;}}$.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var i=types.length;i;){this.addEventListener(types[--i],handler,false);}}else{this.onmousewheel=handler;}},teardown:function(){if(this.removeEventListener){for(var i=types.length;i;){this.removeEventListener(types[--i],handler,false);}}else{this.onmousewheel=null;}}};$.fn.extend({mousewheel:function(fn){return fn?this.bind("mousewheel",fn):this.trigger("mousewheel");},unmousewheel:function(fn){return this.unbind("mousewheel",fn);}});
function handler(event){var orgEvent=event||window.event,args=[].slice.call(arguments,1),delta=0,returnValue=true,deltaX=0,deltaY=0;event=$.event.fix(orgEvent);event.type="mousewheel";if(orgEvent.wheelDelta){delta=orgEvent.wheelDelta/120;}if(orgEvent.detail){delta=-orgEvent.detail/3;}deltaY=delta;if(orgEvent.axis!==undefined&&orgEvent.axis===orgEvent.HORIZONTAL_AXIS){deltaY=0;deltaX=-1*delta;}if(orgEvent.wheelDeltaY!==undefined){deltaY=orgEvent.wheelDeltaY/120;}if(orgEvent.wheelDeltaX!==undefined){deltaX=-1*orgEvent.wheelDeltaX/120;}args.unshift(event,delta,deltaX,deltaY);return($.event.dispatch||$.event.handle).apply(this,args);}
jQuery.easing['jswing']=jQuery.easing['swing'];jQuery.extend(jQuery.easing,{def:'easeOutQuad',swing:function(x,t,b,c,d){return jQuery.easing[jQuery.easing.def](x,t,b,c,d);},easeOutQuad:function(x,t,b,c,d){return-c*(t/=d)*(t-2)+b;},easeOutCirc:function(x,t,b,c,d){return c*Math.sqrt(1-(t=t/d-1)*t)+b;}});
jQuery.fn.customScrollV = function(_options){
var _options = jQuery.extend({
	lineWidth: 5 /* this parameter sets the width of the scroll*/
}, _options);
return this.each(function(){
	var _box = jQuery(this);
	if(_box.is(':visible')){
		if(_box.children('.scroll-content').length == 0){
			var line_w = _options.lineWidth;
			/*--- init part ---*/
			var scrollBar = jQuery(	'<div class="vscroll-bar">'+
									'	<div class="scroll-up"></div>'+
									'	<div class="scroll-line">'+
									'		<div class="scroll-slider">'+
									'			<div class="scroll-slider-c"></div>'+
									'		</div>'+
									'	</div>'+
									'	<div class="scroll-down"></div>'+
									'</div>');
			_box.wrapInner('<div class="scroll-content"><div class="scroll-hold"></div></div>').append(scrollBar);
			var scrollContent = _box.children('.scroll-content');
			var scrollSlider = scrollBar.find('.scroll-slider');
			var scrollSliderH = scrollSlider.parent();
			var scrollUp = scrollBar.find('.scroll-up');
			var scrollDown = scrollBar.find('.scroll-down');
			/*--- different variables ---*/
			var box_h = _box.height();
			var slider_h = 0;
			var slider_f = 0;
			var cont_h = scrollContent.height();
			var _f = false;
			var _f1 = false;
			var _f2 = true;
			var _t1, _t2, _s1, _s2;
			/* for touch */
			var kkk = 0, start = 0, _time, flag = true;
			/*--- set styles ---*/
			_box.css({
				position: 'relative',
				overflow: 'hidden',
				height: box_h
			});
			scrollContent.css({
				position: 'absolute',
				top: 0,
				left: 0,
				zIndex: 1,
				height: 'auto'
			});
			scrollBar.css({
				position: 'absolute',
				top: 0,
				right: 0,
				zIndex:2,
				width: line_w,
				height: box_h,
				overflow: 'hidden'
			});
			scrollUp.css({
				width: line_w,
				height: line_w,
				overflow: 'hidden',
				cursor: 'pointer'
			});
			scrollDown.css({
				width: line_w,
				height: line_w,
				overflow: 'hidden',
				cursor: 'pointer'
			});
			slider_h = scrollBar.height();
			if(scrollUp.is(':visible')) slider_h -= scrollUp.height();
			if(scrollDown.is(':visible')) slider_h -= scrollDown.height();
			scrollSliderH.css({
				position: 'relative',
				width: line_w,
				height: slider_h,
				overflow: 'hidden'
			});
			slider_h = 0;
			scrollSlider.css({
				position: 'absolute',
				top: 0,
				left: 0,
				width: line_w,
				height: slider_h,
				overflow: 'hidden',
				cursor: 'pointer'
			});
			box_h = _box.height();
			cont_h = scrollContent.height();
			if(box_h < cont_h){
				_f = true;
				slider_h = Math.round(box_h/cont_h*scrollSliderH.height());
				if(slider_h < 5) slider_h = 5;
				scrollSlider.height(slider_h);
				slider_h = scrollSlider.outerHeight();
				slider_f = (cont_h - box_h)/(scrollSliderH.height() - slider_h);
				_s1 = (scrollSliderH.height() - slider_h)/15;
				_s2 = (scrollSliderH.height() - slider_h)/3;
				scrollContent.children('.scroll-hold').css('padding-right', scrollSliderH.width());
			}
			else{
				_f = false;
				scrollBar.hide();
				scrollContent.css({width: _box.width(), top: 0, left:0});
				scrollContent.children('.scroll-hold').css('padding-right', 0);
			};
			var _top = 0;
			/*--- element's events ---*/
			scrollUp.bind('mousedown', function(){
				_top -= _s1;
				scrollCont();
				_t1 = setTimeout(function(){
					_t2 = setInterval(function(){
						_top -= 4/slider_f;
						scrollCont();
					}, 20);
				}, 500);
				return false;
			}).mouseup(function(){
				if(_t1) clearTimeout(_t1);
				if(_t2) clearInterval(_t2);
			}).mouseleave(function(){
				if(_t1) clearTimeout(_t1);
				if(_t2) clearInterval(_t2);
			});
			scrollDown.bind('mousedown', function(){
				_top += _s1;
				scrollCont();
				_t1 = setTimeout(function(){
					_t2 = setInterval(function(){
						_top += 4/slider_f;
						scrollCont();
					}, 20);
				}, 500);
				return false;
			}).mouseup(function(){
				if(_t1) clearTimeout(_t1);
				if(_t2) clearInterval(_t2);
			}).mouseleave(function(){
				if(_t1) clearTimeout(_t1);
				if(_t2) clearInterval(_t2);
			});
			scrollSliderH.click(function(e){
				if(_f2){
					_top = e.pageY - scrollSliderH.offset().top - scrollSlider.outerHeight()/2;
					scrollCont();
				}
				else{
					_f2 = true;
				};
			});
			var t_y = 0;
			var tttt_f = (jQuery.browser.msie)?(true):(false);
			scrollSlider.mousedown(function(e){
				t_y = e.pageY - jQuery(this).position().top;
				_f1 = true;
				return false;
			}).mouseup(function(){
				_f1 = false;
			});
			jQuery('body').bind('mousemove', function(e){
				if(_f1){
					 _f2 = false;
					 _top = e.pageY - t_y;
					 if(tttt_f) document.selection.empty();
					 scrollCont();
				}
			}).mouseup(function(){
				_f1 = false;
			});
			
			/* touch event start */
			scrollSlider.bind('touchstart', function(e){
				if(_time) clearTimeout(_time);
				scrollSlider.stop();
				scrollContent.stop();
				kkk = e.originalEvent.pageY;
				e.preventDefault();
				e.stopPropagation();
				return false;
			}).bind('touchmove', function(e){
				if(_f){
					_f = false;
					if(kkk > e.originalEvent.pageY) _top -=1*Math.abs(e.originalEvent.pageY - kkk);
					else _top -=-1*Math.abs(e.originalEvent.pageY - kkk);
					scrollCont();
					kkk = e.originalEvent.pageY;
					_f = true;
					if((_top > 0) && (_top+slider_h < scrollSliderH.height())) return false;
				}
				e.preventDefault();
				e.stopPropagation();
				return false;
			}).bind('touchend', function(e){
				e.preventDefault();
				e.stopPropagation();
				return false;
			});
			_box.bind('touchstart', function(e){
				if(_time) clearTimeout(_time);
				scrollSlider.stop();
				scrollContent.stop();
				kkk = e.originalEvent.pageY;
				start = kkk;
				flag = true;
				e.preventDefault();
				e.stopPropagation();
				return false;
			}).bind('touchend', function(e){
				if(flag && Math.abs(start - kkk) > 80){
					_top += (start - kkk)/3;
					if(_top < 0) _top = 0;
					else if(_top+slider_h > scrollSliderH.height()) _top = scrollSliderH.height() - slider_h;
					scrollSlider.animate({top: _top}, {queue:false, easing: 'easeOutCirc', duration: 300*Math.abs(start - kkk)/40});
					scrollContent.animate({top: -_top*slider_f}, {queue:false, easing: 'easeOutCirc', duration: 300*Math.abs(start - kkk)/40});
				}
				e.preventDefault();
				e.stopPropagation();
				return false;
			}).bind('touchmove', function(e){
				if(_f){
					_f = false;
					if(kkk > e.originalEvent.pageY) _top -=-1*Math.abs(e.originalEvent.pageY - kkk)/(cont_h/box_h);
					else _top -=1*Math.abs(e.originalEvent.pageY - kkk)/(cont_h/box_h);
					scrollCont();
					kkk = e.originalEvent.pageY;
					_f = true;
					_time = setTimeout(function(){
						flag = false;
					}, 200);
					if((_top > 0) && (_top+slider_h < scrollSliderH.height())) return false;
				}
				
				e.preventDefault();
				e.stopPropagation();
				return false;
			});
			scrollUp.bind('touchstart', function(){
				_top -= _s1;
				scrollCont();
				e.preventDefault();
				e.stopPropagation();
				return false;
			}).bind('touchend', function(e){
				e.preventDefault();
				e.stopPropagation();
				return false;
			}).bind('touchmove', function(e){
				e.preventDefault();
				e.stopPropagation();
				return false;
			});
			scrollDown.bind('touchstart', function(){
				_top += _s1;
				scrollCont();
				e.preventDefault();
				e.stopPropagation();
				return false;
			}).bind('touchend', function(e){
				e.preventDefault();
				e.stopPropagation();
				return false;
			}).bind('touchmove', function(e){
				e.preventDefault();
				e.stopPropagation();
				return false;
			});
			/* touch event end */
			
			document.body.onselectstart = function(){
				if(_f1) return false;
			};
			if(!_box.hasClass('not-scroll')){
				_box.bind('mousewheel', function(event, delta){
					if(_f){
						_top -=delta*_s1;
						scrollCont();
						if((_top > 0) && (_top+slider_h < scrollSliderH.height())) return false;
					}
				});
			};
			function scrollCont(){
				if(_top < 0) _top = 0;
				else if(_top+slider_h > scrollSliderH.height()) _top = scrollSliderH.height() - slider_h;
				scrollSlider.css('top', _top);
				scrollContent.css('top', -_top*slider_f);
			};
			this.scrollResize = function(){
				box_h = _box.height();
				cont_h = scrollContent.height();
				if(box_h < cont_h){
					_f = true;
					scrollBar.show();
					scrollBar.height(box_h);
					slider_h = scrollBar.height();
					
					if(scrollUp.is(':visible')) slider_h -= scrollUp.height();
					if(scrollDown.is(':visible')) slider_h -= scrollDown.height();
					scrollSliderH.height(slider_h);
					slider_h = Math.round(box_h/cont_h*scrollSliderH.height());
					if(slider_h < 5) slider_h = 5;
					scrollSlider.height(slider_h);
					slider_h = scrollSlider.outerHeight();
					slider_f = (cont_h - box_h)/(scrollSliderH.height() - slider_h);
					if(cont_h + scrollContent.position().top < box_h) scrollContent.css('top', -(cont_h - box_h));
					_top = - scrollContent.position().top/slider_f;
					scrollSlider.css('top', _top);
					_s1 = (scrollSliderH.height() - slider_h)/15;
					_s2 = (scrollSliderH.height() - slider_h)/3;
					scrollContent.children('.scroll-hold').css('padding-right', scrollSliderH.width());
				}
				else{
					_f = false;
					scrollBar.hide();
					scrollContent.css({top: 0, left:0});
					scrollContent.children('.scroll-hold').css('padding-right', 0);
				};
			};
			
			setInterval(function(){
				if(_box.is(':visible') && cont_h != scrollContent.height()) _box.get(0).scrollResize();
			}, 200);
			
		}
		else{
			this.scrollResize();
		};
	};
})};