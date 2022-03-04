var $ = jQuery.noConflict();



$(window).on('load', function () {
	$('.about_us').find('.about_us__item').each(function () {
		// var about_us__bg = $(this).attr('data-about-us');

		// $(this).addClass( about_us__bg );
	});



	// copy default code before carousel loaded
	var about_us__html = $('.about_us').html();

	window_resize();

	$(window).resize(function() {
		window_resize();
	});



	function window_resize() {
		// mobile
		if ( $(window).width() < 980 ) {
			if ( $('.about_us').find('.owl-carousel').hasClass('owl-loaded') ) {
				$('.about_us').find('.owl-carousel').remove();

				$('.about_us').append( about_us__html );
			}

			if ( !$('.about_us').find('.owl-carousel').length ) {
				$('.about_us').append( about_us__html );
			}
		}
		//desktop
		else {
			if ( !$('.about_us').find('.owl-carousel').hasClass('owl-loaded') ) {
				$('.about_us').find('.owl-carousel').owlCarousel({
					// autoplay:               true,
					autoplayTimeout:        4500,
					// autoplayHoverPause:     true,
					items:                  1,
					loop:                   true,
					nav:                    true,
					navText:                ['<span></span><span></span>', '<span></span><span></span>'],
					// navText:                ['<img src="/image/icons/prev.svg" alt="<" />', '<img src="/image/icons/next.svg" alt=">" />'],
				});
			}
		}
	}
});


