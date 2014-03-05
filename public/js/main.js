$(function(){

	// Set global highcharts options
	Highcharts.setOptions({
		global : {
			timezoneOffset: (new Date()).getTimezoneOffset()
		}
	});

	// Set some alerify defaults
	alertify.set({ delay: 20000 });

	// Setup a click event for any spinner button
	$('.has-spinner').click(function(){
		$(this).prop('disabled', true);
		$(this).toggleClass('spinning');
	});
});