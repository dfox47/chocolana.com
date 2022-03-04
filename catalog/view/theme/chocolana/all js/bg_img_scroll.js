var $ = jQuery.noConflict();



$(window).on('load', function () {
	bg_img_scroll();

	$(window).on('resize scroll', function() {
		bg_img_scroll();
	});
});

function bg_img_scroll() {
	$('.js-bg_img_scroll').each(function () {
		var top_of_element      = $(this).offset().top;
		var bottom_of_element   = $(this).offset().top + $(this).outerHeight();
		var bottom_of_screen    = $(window).scrollTop() + $(window).innerHeight();
		var top_of_screen       = $(window).scrollTop();

		if ( (bottom_of_screen > top_of_element) && (top_of_screen < bottom_of_element) ) {
			var img_src = $(this).attr('data-img-src');

			$(this).removeClass('js-bg_img_scroll').addClass( img_src );
		}
	});
}
