var $ = jQuery

$(window).on('load', function () {
	const $html = $('html')

	// show menu on hover at menu button
	// only on desktop, cause in other way click will not work
	if ( !isMobile ) {
		$('.menu__toggle').mouseover(function() {
			$('html').addClass('topmenu__active')
		})
	}

	$(window).resize()

	$(window).scroll(function() {
		menu_top__show_on_mouse_scroll()
	})

	// show\hide menu
	$('.menu__toggle, .menu_slider__close, .page__fade, .topmenu__close').click(function () {
		$html.toggleClass('topmenu__active')

		return false
	})

	// for mobile
	$('.menu_top__toggle_wrap').click(function() {
		$html.toggleClass('menu_top__active')

		// for mobile special
		$('.topmenu__wrap').focus()
	})

	// when menu opened and user leaving menu
	$('.topmenu__wrap2').mouseleave(function() {
		$html.removeClass('topmenu__active')
	})

	// menu_top__show_on_mouse_scroll [START]
	let iScrollPos = 0

	function menu_top__show_on_mouse_scroll() {
		let iCurScrollPos = $(this).scrollTop()
		const $html = $('html')

		// scroll down
		if (iCurScrollPos > iScrollPos && $(window).scrollTop() > 80 ) {
			$html.addClass('menu_top__hide')
		}
		// scroll up
		else {
			if ($html.hasClass('menu_top__hide')) {
				$html.removeClass('menu_top__hide')
			}
		}

		iScrollPos = iCurScrollPos
	}
	// menu_top__show_on_mouse_scroll [END]
})