var $ = jQuery.noConflict();

$('.gotop').click(function () {
	$('body, html').animate({
		scrollTop: 0
	}, 700);

	return false;
});


