$.fn.ajaxLink = function (callback) {
	$(this).each(function(){
		var url = $(this).attr('href');
		$(this).removeAttr('href');
		$(this).attr('onclick', "$.get('" + url + "', " + callback + ");");
	});
}