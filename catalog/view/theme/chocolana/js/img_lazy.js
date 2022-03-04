var $ = jQuery.noConflict();



$(window).on('load', function () {
	$('img.js-img_lazy').each(function () {
		var img_src = $(this).attr('data-img-src');

		$(this).removeClass('js-img_lazy').attr( 'src', img_src );
	});
});


