function post (url, data) {
	var res = false;
		$.ajax({
			type: "POST",
			url: url,
			data: data,
			async: false,
			success: function (answer) {
				if (!answer) {
					MSG.error();
					res = false;
					return;
				}
				answer = JSON.parse(answer);
				if (!answer) {
					MSG.error();
					res = false;
					return;
				}
				if (answer.result != "success") {
					MSG.error(answer.error);
					res = false;
					return;
				}
				if (typeof answer.data == "undefined" || answer.data === null) {
					res = true;
					return;
				}
				res = answer.data;
				return;
			},
			error: function (jqXHR, textStatus, errorThrown) {
				MSG.error(errorThrown);
			},
		});
	return res;
}