var $ = jQuery.noConflict()

$(function() {
	const $html                     = $('html')
	const form_subscribe            = $('.form_subscribe')
	const form_subscribe__email     = form_subscribe.find('input[type="email"]')
	const location                  = window.location.href

	// close modal
	$('.modal_new__bg, .modal_new__close').click(function () {
		$html.removeClass('modal_new__active')
	})

	// close modal success
	$('.form_subscribe__success').click(function () {
		$html.removeClass('form_subscribe__success__active')
	})

	form_subscribe.submit(function(e) {
		e.preventDefault()

		// check if email is wrong
		if ( !isEmail( form_subscribe__email.val() )) {
			form_subscribe.find('input').removeClass('error')

			form_subscribe__email.addClass('error')
		}
		else {
			form_subscribe.find('input').removeClass('error')

			$.post(
				"/api/subscribe.php",
				{
					email:      form_subscribe__email.val(),
					location:   location
				},
				form_subscribe__success
			)
		}
	})

	function isEmail(email) {
		let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/
		return regex.test(email)
	}

	function form_subscribe__email_validate() {
		// email not valid
		if( !isEmail( form_subscribe__email.val() )) {
			form_subscribe__email.addClass('error')
		}
		// email valid
		else {
			form_subscribe__email.removeClass('error')
		}
	}

	function form_subscribe__success() {
		$html.addClass('form_subscribe__success__active')
	}
})