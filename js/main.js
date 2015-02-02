// MENU
$(document).ready(function(){
	$('.menu li').each(function(){
		if ($(this).children('ul').length == 0)
			return;
		$(this).children('a').attr('class', 'folder_down');
	});
    $('.menu li').click(function(e){
    	if ($(this).children('ul').length == 0)
    		return;
    	console.log(e);
		$(this).children('ul').slideToggle('slow');
		if ($(this).children('a').attr('class') == 'folder_down') {
			$(this).children('a').attr('class', 'folder_up');
			$(this).css('background', 'rgba(0,0,0,0.05)');
		}
		else {
			$(this).children('a').attr('class', 'folder_down');
			$(this).css('background', 'white');
		}
	});
});

$.fn.ajaxLink = function (callback) {
	$(this).each(function(){
		var url = $(this).attr('href');
		$(this).removeAttr('href');
		$(this).attr('onclick', "$.get('" + url + "', " + callback + ");");
	});
}

var MSG = {
		curtain: function () {
			var curtain = $('<div class = "curtain" oncontextmenu = "return false;"></div>');
			$('body').append(curtain);
			curtain.hide().fadeIn('fast');
			$('input').blur();
			$('select').blur();
			$('textarea').blur();
			return curtain;
		},
		msgbox: function (arg1, arg2, arg3) {
			if (typeof arg1 === 'object') {
				var type = 'error';
				var title = '#' + arg1.code;
				var text = arg1.msg;
			}
			else {
				var type = arg1;
				var title = arg2;
				var text = arg3;
			}
			var url;
			var curt = MSG.curtain();
			var box = $('<table class = "msgbox ' + type + '"><tr><td></td><td><h1>' + title + '</h1><p>' + text + '</p></td></tr></table>');
			curt.append(box);
			center(box);
			box.hide().slideDown('fast');
			box.click(function(){
				curt.fadeOut('fast');
				box.fadeOut('fast');
			});
			curt.click(function(){
				curt.fadeOut('fast');
				box.fadeOut('fast');
			});
			$(document).keypress(function(e){
				if (e.keyCode == 13) {
					curt.fadeOut('fast');
					box.fadeOut('fast');
				}
			});
			$(document).keyup(function(e){
				if (e.keyCode == 27) {
					curt.fadeOut('fast');
					box.fadeOut('fast');
				}
			});
		},
		error: function (arg1, arg2) {
			if (typeof arg1 === 'object')
				return MSG.msgbox(arg1);
			var title = arg2 ? arg1 : 'Ошибка';
			var text = arg2 ? arg2 : arg1 ? arg1 : 'Неизвестная ошибка';
			return MSG.msgbox('error', title, text);
		},
		success: function (arg1, arg2) {
			if (typeof arg1 === 'object')
				return MSG.msgbox(arg1);
			var title = arg2 ? arg1 : 'Выполнено успешно';
			var text = arg2 ? arg2 : arg1 ? arg1 : '';
			return MSG.msgbox('success', title, text);
		},
}

function serializePost (data) {
	var res = {};
	data.forEach(function(item){
		res[item.name] = item.value;
	});
	return res;
}

function setCenter (obj, offsetX, offsetY) {
	offsetX = offsetX ? offsetX : '50%';
	offsetY = offsetY ? offsetY : '50%';
	obj.css({
		position: 'absolute',
		left: offsetX,
		top: offsetY,
		'margin-top': '-' + Math.round(obj.outerHeight() / 2) + 'px',
		'margin-left': '-' + Math.round(obj.outerWidth() / 2) + 'px',
	});
	return;
};
function center (obj, offsetX, offsetY) {
	setCenter(obj, offsetX, offsetY);
	$(window).resize(function(){
		setCenter(obj, offsetX, offsetY);
	});
};
$(window).ready(function() {
	function positionFooter() {
		var footer = $('footer');
		if (!footer.length)
			return;
		footer.css('position', 'static');
		var leftSide = $('#LeftSide'),
			footerPosition = footer.css('position');
			footerHeight = footer.height(),
			footerTop = (leftSide[0].scrollHeight - footerHeight) + "px",
			leftSideHeight = leftSide[0].scrollHeight,
			windowHeight = $(window).height();
		if (leftSideHeight > windowHeight)
			footer.css('position', 'static');
		else
			footer.css('position', 'absolute');
	}
	positionFooter();
	$(window)
		.scroll(positionFooter)
		.resize(positionFooter)
});
// Processing position on the screen
function footer_c2() {
    var wh = $(window).height();

    var hh = $('html').height();

    var fh = $('.footer').outerHeight(true);

var h_top_h = $('.header_top').outerHeight(true);
    var sub_wrapper_h = $('.sub_wrapper').outerHeight(true);
    var res = hh - h_top_h - sub_wrapper_h - fh;
    
    if ( res > 0 ) {
        $('.footer').css('top',res);        
    } else {
        $('.footer').css('top','0px');        
    }
};
$(document).ready(function () {
	$(".context-menu-with-button").each(function(){
		var button = $("<span class = 'context-menu-button'></span>");
		var account = $(this);
		$(this).children().eq(0).prepend(button);
		button.click(function(e){
			var mx = e.clientX || e.pageX;
			var my = e.clientY || e.pageY;
			var bx = $(this).offset().left;
			var by = $(this).offset().top + $(this).height();
			console.log(bx, by);
			account.contextMenu({x: bx, y: by});
		});
	});
	$("input[type=submit]").button();
	$("input[type=button]").button();
	$("button").button();
	$("button.search").button({icons: {primary: "ui-icon-search"}});
	$("button.save").button({icons: {primary: "ui-icon-check"}});
	$("button.check").button({icons: {primary: "ui-icon-check"}});
	$("button.close").button({icons: {primary: "ui-icon-close"}});
	$("button.block").button({icons: {primary: "ui-icon-locked"}});
	$("button.unblock").button({icons: {primary: "ui-icon-unlocked"}});
	$("button.cart").button({icons: {primary: "ui-icon-cart"}});
	$("button.transfer").button({icons: {primary: "ui-icon-transferthick-e-w"}});
	$("button.clock").button({icons: {primary: "ui-icon-clock"}});
	$("button.link").button({icons: {primary: "ui-icon-link"}});
	$("textarea.tinymce").tinymce({
		script_url: '/outsrc/tinymce/tinymce.min.js',
		menu: {},
		statusbar: false,
	});
    $(".tabs").tabs();
	$( ".accordion" ).accordion({
		heightStyle: "content",
	});
	$( "#tabs" ).tabs({
		beforeLoad: function( event, ui ) {
			ui.jqXHR.error(function() {
				ui.panel.html("Проблемы при загрузке данных");
			});
		}
	});
    $('.datepicker').datepicker({ dateFormat: 'dd.mm.yy' });
    $('.datepicker_birthdate').datepicker({
    	dateFormat: 'dd.mm.yy',
    	changeMonth: true,
    	changeYear: true,
    	yearRange: "1900:2015",
    });
    $('.datepicker_today').datepicker({dateFormat: 'dd.mm.yy', minDate: 0});
    $('.spinner').spinner();
    $('.spinner1').spinner({min: 1});
    $('.spinner0').spinner({min: 0});
    $('select').select2({ dropdownAutoWidth : true });
    $(".toggleWindow .body").hide();
    $(".toggleWindow .head").click(function(){
    	$(this).parent().children(".body").slideToggle("fast");
    });
  $(function() {
    $( ".column" ).sortable({
      connectWith: ".column",
      handle: ".portlet-header",
      cancel: ".portlet-toggle",
      placeholder: "portlet-placeholder ui-corner-all"
    });

    $( ".portlet" )
      .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
      .find( ".portlet-header" )
        .addClass( "ui-widget-header ui-corner-all" )
        .prepend( "<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");

    $( ".portlet-toggle" ).click(function() {
      var icon = $( this );
      icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
      icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
    });
  });
});