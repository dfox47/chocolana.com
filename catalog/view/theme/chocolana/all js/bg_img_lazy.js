var $ = jQuery.noConflict();



$(window).on('load', function () {
	$('.js-bg_img_lazy').each(function () {
		var bg_img_src = $(this).attr('data-img-src');

		$(this).removeClass('js-bg_img_lazy').addClass( bg_img_src );
	});
});


