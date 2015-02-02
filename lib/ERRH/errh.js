$(document).ready(function(){
	$('.ERRH').prependTo($('body'));
	$('.ERRH ol').hide();
	$('.ERRH div').click(function(){
		$(this).parent().children('ol').slideToggle('slow');
	});
});