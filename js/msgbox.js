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

$(document).ready(function(){
});