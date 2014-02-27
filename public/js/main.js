$(function(){
	$('.has-spinner').click(function(){
		$(this).prop('disabled', true);
		$(this).toggleClass('spinning');
	});
});