var $ = jQuery.noConflict();



$(window).on('load', function () {
	img_scroll();

	$(window).on('resize scroll', function() {
		img_scroll();
	});
});

function img_scroll() {
	$('img.js-img_scroll').each(function () {
		var top_of_element      = $(this).offset().top;
		var bottom_of_element   = $(this).offset().top + $(this).outerHeight();
		var bottom_of_screen    = $(window).scrollTop() + $(window).innerHeight();
		var top_of_screen       = $(window).scrollTop();

		if ( (bottom_of_screen > top_of_element) && (top_of_screen < bottom_of_element) ) {
			var img_src = $(this).attr('data-img-src');

			$(this).removeClass('js-img_scroll').attr( 'src', img_src );
		}
	});
}
