var $ = jQuery.noConflict();



$(window).on('load', function () {
	$('.js-scroll_to').click(function () {
		var link = $(this).attr('data-link');
		var scroll_to = $('*[data-scroll="' + link +'"]');

		if ( scroll_to.length ) {
			$('body, html').animate({
				// scrollTop: scroll_to.offset().top - 100
				scrollTop: scroll_to.offset().top
			}, 700);
		}
		else {
			console.log('link not exist: ' + link);
		}
	});
});


