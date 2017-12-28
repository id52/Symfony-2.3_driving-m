$(document).ready(function(){
	jQuery('form').customForm({
		select: {
			elements: 'select.customSelect',
			structure: '<div class="selectArea"><div class="selectIn"><div class="selectText"></div></div></div>',
			text: '.selectText',
			btn: '.selectIn',
			optStructure: '<div class="selectSub"><ul></ul></div>'
		},
		radio: {
			elements: 'input.customRadio'
		},
		checkbox: {
			elements: 'input.customCheckbox'
		}
	});
});


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
								if (selOpt.prop('selected')) selectText.html('<img src="'+selOpt.data('img')+'" />' + selOpt.html());
								html += '<li data-value="' + selOpt.val() + '" ' + (selOpt.prop('selected') ? 'class="selected"' : '') + '>' + 
										(selOpt.prop('disabled') ? '<span>' : '<a href="#">') + '<img src="'+selOpt.data('img')+'" />' + selOpt.html() + (selOpt.prop('disabled') ? '</span>' : '</a>') + '</li>';
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
							if (selOpt.prop('selected')) selectText.html('<img src="'+selOpt.data('img')+'" />' + selOpt.html());
							html += '<li data-value="' + selOpt.val() + '" ' + (selOpt.prop('selected') ? 'class="selected"' : '') + '>' + 
									(selOpt.prop('disabled') ? '<span>' : '<a href="#">') + '<img src="'+selOpt.data('img')+'" />' + selOpt.html() + (selOpt.prop('disabled') ? '</span>' : '</a>') + '</li>';
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