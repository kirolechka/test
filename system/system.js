$(document).ready(function(){
	$('#CacheRemove').click(function(){
		$.post('/processing/system/cache/clear/', function(ans){
			ans = JSON.parse(ans);
			if (ans.result == 'success')
				MSG.success();
			else
				MSG.error(ans.error);
		}).fail(function(e){ MSG.error(e.statusText); });
	});
});