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