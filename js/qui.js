function genRandomString () {
	return (Math.random() + 1).toString(36).substring(2);
}

var CurtainClass = function () {
	this.obj;
	var self;
	this.init = function () {
		this.obj = $('<div class = "curtain" oncontextmenu = "return false;"></div>');
		$('body').append(this.obj);
		this.obj.hide();
		return;
	}
	this.show = function () {
		this.obj.fadeIn('fast');
		$('input').blur();
		$('select').blur();
		$('textarea').blur();
		return;
	}
	this.hide = function () {
		this.obj.fadeOut('fast');
		this.obj.children().remove();
	}
	this.init();
}

var Curtain = new CurtainClass();

(function($){
	
	function QSpinner (element, options) {
		var self = this;
		this.element = element;
		this.options = $.extend({
			min: false,
			max: false,
			step: 1,
		}, options);
		this.timeout;
		this.callback;
		this.id;
		this.init = function () {
			this.id = Math.random().toString(36).substring(7);
			var box = $('<div class = "qui-spinner" id = "' + this.id + '"></div>');
			var boxUp = $('<div class = "qui-spinner-up"></div>');
			var boxDown = $('<div class = "qui-spinner-down"></div>');
			this.element.after(box);
			box.append(this.element);
			box.append(boxDown);
			box.append(boxUp);
			$('html').on('mousewheel', '.qui-spinner#' + this.id + ' > input', function(e, delta){
				(delta > 0) ? self.up() : self.down();
				return;
			});
			$('html').on('change', '.qui-spinner#' + this.id + ' > input', function(){
				self.element.val(self.reduce(self.element.val()));
				if (self.callback)
					self.callback();
				return;
			});
			$('html').on('mousedown', '.qui-spinner#' + this.id + ' > .qui-spinner-up', function(e){
				self.timeout = setInterval(function(){
					self.up();
				}, 50);
				return;
			});
			$('html').on('mouseup', '.qui-spinner#' + this.id + ' > .qui-spinner-up', function(){
				clearInterval(self.timeout);
				return;
			})
			$('html').on('mousedown', '.qui-spinner#' + this.id + ' > .qui-spinner-down', function(e){
				self.timeout = setInterval(function(){
					self.down();
				}, 50);
				return;
			});
			$('html').on('mouseup', '.qui-spinner#' + this.id + ' > .qui-spinner-down', function(){
				clearInterval(self.timeout);
				return;
			});
			return;
		}
		this.reduce = function (val) {
			var res = parseInt(val);
			res = res ? res : 0;
			res = (this.options.min !== false && res < this.options.min) ? this.options.min : res;
			res = (this.options.max !== false && res > this.options.max) ? this.options.max : res;
			return res;
		}
		this.up = function () {
			var val = this.reduce(this.element.val());
			val += this.options.step * 1;
			val = this.reduce(val);
			this.element.val(val);
			if (this.callback)
				this.callback();
			return;
		}
		this.down = function () {
			var val = this.reduce(this.element.val());
			val -= this.options.step * 1;
			val = this.reduce(val);
			this.element.val(val);
			if (this.callback)
				this.callback();
			return;
		}
		this.init();
	}
	
	function QTree (element, options) {
		var self = this;
		this.element = element;
		this.options = $.extend({
			collapse: true,
		}, options);
		this.data = [];
		this.init = function () {
			this.element.addClass('qui-tree');
			this.element.children('tbody').children('tr').each(function(){
				self.data.push({
					id: $(this).attr('tree-id'),
					parent: $(this).attr('tree-parent-id'),
					element: $(this),
					collapsed: false,
				});
			});
			this.sort();
			this.initExpanders();
			$('html').on('click', 'table.qui-tree span.expand', function(){
				self.collapsar($(this).parent().parent().attr('tree-id'), 'toggle');
			});
		}
		this.sort = function () {
			for (var i = 0; i < this.data.length; i++) {
				if (this.data[i].parent) {
					this.data[i].element.remove();
                    this.getNode(this.data[i].parent).element.after(this.data[i].element);
				}
			}
			return;
		}
		this.initExpanders = function () {
			for (var i = 0; i < this.data.length; i++) {
				if (this.data[i].parent && this.options.collapse) {
					this.data[i].collapsed = true;
					this.data[i].element.hide();
				}
				if (this.hasChildren(this.data[i].id)) {
					if (this.options.collapse)
						this.data[i].element.children().eq(0).prepend($('<span class = "expand plus"></span>'));
					else
						this.data[i].element.children().eq(0).prepend($('<span class = "expand minus"></span>'));
				}
				this.data[i].element.children().eq(0).css('padding-left', 10 + this.getLevel(this.data[i].id) * 21 + 'px');
			}
		}
		this.getNode = function (id) {
			for (var i = 0; i < this.data.length; i++)
				if (this.data[i].id == id)
					return this.data[i];
		}
		this.hasChildren = function (id) {
			for (var i = 0; i < this.data.length; i++)
				if (this.data[i].parent == id)
					return true;
			return false;
		}
		this.getChildren = function (id) {
			var res = [];
			for (var i = 0; i < this.data.length; i++)
				if (this.data[i].parent == id)
					res.push(this.data[i]);
			return res;
		}
		this.getLevel = function (id) {
			var node = this.getNode(id);
			if (!node.parent)
				return 0;
			return 1 + this.getLevel(node.parent);
		}
		this.collapsar = function (id, type, recursive) {
			for (var i = 0; i < this.data.length; i++) {
				if (this.data[i].parent == id) {
					switch (type) {
					case 'collapse':
						this.getNode(this.data[i].parent).element.children().eq(0).children('.expand').attr('class', 'expand plus');
						this.data[i].element.hide();
						this.data[i].collapsed = true;
						recursive = true;
						break;
					case 'uncollapse':
						this.getNode(this.data[i].parent).element.children().eq(0).children('.expand').attr('class', 'expand minus');
						this.data[i].element.show();
						this.data[i].collapsed = false;
						break;
					case 'toggle':
						this.data[i].collapsed = !this.data[i].collapsed;
						if (this.data[i].collapsed) {
							this.getNode(this.data[i].parent).element.children().eq(0).children('.expand').attr('class', 'expand plus');
							this.data[i].element.hide()
							type = 'collapse';
							recursive = true;
						}
						else {
							this.getNode(this.data[i].parent).element.children().eq(0).children('.expand').attr('class', 'expand minus');
							this.data[i].element.show();
						}
						break;
					}
					if (recursive)
						this.collapsar(this.data[i].id, type, recursive);
				}
			}
			return;
		}
		this.init();
	}
	
	$.fn.ajaxFormNew = function(callbackSuccess){
		return this.each(function(){
			$(this).ajaxForm({
				dataType: "json",
				success: function(answer){
					if (answer.result == "success" && !answer.data)
						return callbackSuccess(true);
					if (answer.result == "success" && answer.data)
						return callbackSuccess(answer.data);
					MSG.error(answer.error);
				},
			});
		});
	}
	
	$.fn.ajaxFormLoaderNew = function(callbackSuccess){
		return this.each(function(){
			$(this).ajaxForm({
				dataType: "json",
				beforeSubmit: function(){
					ModalProgress.start();
				},
				success: function(answer){
					ModalProgress.stop();
					if (answer.result == "success" && !answer.data)
						return callbackSuccess(true);
					if (answer.result == "success" && answer.data)
						return callbackSuccess(answer.data);
					MSG.error(answer.error);
				},
				error: function(){
					ModalProgress.stop();
					MSG.error();
				}
			});
		});
	}
	
	$.fn.qtabs = function () {
		return this.each(function(){
			var self = this;
			this.href;
			this.id = 0;
			this.tabs = $(this).children('div');
			this.links = $(this).children('ul').eq(0).children('li');
			this.init = function () {
				this.href = $(location).attr('href');
				var pos = this.href.indexOf('#');
				if (pos >= 0) {
					var id = this.href.substring(pos);
					if (id == '#')
						id = '#' + this.tabs.eq(0).attr('id');
					this.href = this.href.substring(0, pos);
					this.id = this.tabs.filter(id).index() - 1;
				}
				$(this).attr('class', 'qui-tabs');
				this.closeTabs();
				this.open(this.id);
				this.links.children().click(function(e){
					e.preventDefault();
					self.open($(this).parent().index());
				});
			}
			this.closeTabs = function () {
				this.tabs.hide();
				this.links.children().removeAttr('class');
				return;
			}
			this.open = function (index) {
				this.closeTabs();
				var id = this.links.eq(index).children().attr('href');
				$(location).attr('href', this.href + id);
				this.tabs.filter(id).show();
				this.links.eq(index).children().attr('class', 'active');
			}
			this.init();
		});
	};
	$.fn.qspinner = function () {
		var args = arguments;
		return this.each(function(){
			var methods = {
					onstop: function (callback) {
						if (!$(this).data('qspinner'))
							return;
						$(this).data('qspinner').callback = callback;
						return;
					}
			};
			var options = [];
			var method = null;
			if (typeof args[0] === 'object')
				options = args[0];
			else
				method = args[0];
			args = Array.prototype.slice.call(args, 1);
			if (!$(this).data('qspinner'))
				$(this).data('qspinner', new QSpinner($(this), options));
			if (method) {
				if (!methods[method]) {
					console.warn("QSpinner." + method + ' method is undefined');
					return;
				}
				return methods[method].apply(this, args);
			}
		});
	}
	$.fn.qaccordion = function () {
		return this.each(function(){
			var self = this;
			this.current;
			this.init = function () {
				$(this).attr('class', 'qui-accordion');
				$(this).children('div').hide();
				this.open(0);
				$(this).children('h3').click(function(){
					var index = $(this).index() / 2;
					self.open(index);
				});
				return;
			}
			this.open = function (num) {
				if (num == this.current)
					return;
				this.current = num;
				$(this).children('div').slideUp('fast');
				$(this).children().eq(num * 2 + 1).slideDown('fast');
				return;
			}
			this.init();
		});
	};
	
	$.fn.center = function (offsetX, offsetY) {
		return this.each(function(){
			this.offsetX = offsetX ? offsetX : '50%';
			this.offsetY = offsetY ? offsetY : '50%';
			this.init = function () {
				var width = $(this).outerWidth();
				var height = $(this).outerHeight();
				var centerX = Math.round($(window).width() / 2);
				var centerY = Math.round($(window).height() / 2);
				$(this).css({
					position: 'absolute',
					left: centerX - Math.round(width / 2),
					top: centerY - Math.round(width / 2),
				});
				return;
			}
			this.init();
		});
	};
	
	$.fn.window = function (s) {
		return this.each(function(){
			var self = this;
			this.drag = false;
			this.dragOffsetX;
			this.dragOffsetY;
			this.head;
			this.close;
			this.init = function () {
				$(this).addClass('qui-window');
				this.head = $("<div class = 'head'></div>");
				this.close = $("<div class = 'close'></div>");
				var hh = $(this).children('h1');
				this.head.append(hh);
				$(this).prepend(this.head);
				$(this).prepend(this.close);
				$(this).center();
				if (s == 'hide')
					$(this).hide();
				this.close.click(function(){
					self.hide();
				});
				this.head.mousedown(function(e){
					self.drag = true;
					self.dragOffsetX = e.pageX - self.head.offset().left;
					self.dragOffsetY = e.pageY - self.head.offset().top;
				});
				$(document).mouseup(function(){
					self.drag = false;
				});
				$(document).mousemove(function(e){
					if (!self.drag)
						return;
					$(self).css({
						left: (e.pageX - self.dragOffsetX) + "px",
						top: (e.pageY - self.dragOffsetY) + "px",
					});
				});
				return;
			}
			this.show = function () {
				$(this).show('fast');
				return;
			}
			this.hide = function () {
				$(this).hide('fast');
				return;
			}
			this.init();
		});
	};
	$.fn.qtree = function () {
		var args = arguments;
		return this.each(function(){
			var options = [];
			var method = null;
			if (typeof args[0] === 'object')
				options = args[0];
			else
				method = args[0];
			args = Array.prototype.slice.call(args, 1);
			if (!$(this).data('qtree'))
				$(this).data('qtree', new QTree ($(this), options));
			if (method) {
				if (!methods[method]) {
					console.warn("QTree." + method + ' method is undefined');
					return;
				}
				return methods[method].apply(this, args);
			}
		});
	}
	
})(jQuery);