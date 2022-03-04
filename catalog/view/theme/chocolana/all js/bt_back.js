var $ = jQuery.noConflict();



$(window).on('load', function () {
	$('.bt_back').click(function() {
		history.back(1);
	});
});