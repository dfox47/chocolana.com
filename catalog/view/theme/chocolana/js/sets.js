var $ = jQuery.noConflict();



$(window).on('load', function () {
	// carousel from sets
	$('.sets__list').find('.owl-carousel').owlCarousel({
		items:      4,
		loop:       true,
		nav:        true,
		navText:    ['<span></span><span></span>', '<span></span><span></span>'],
		responsive:{
			0:{
				items: 1
			},
			600:{
				items: 2
			},
			1000:{
				items: 3
			},
			1200:{
				items: 4
			}
		}
	});



	// show popup with selected set
	$('.sets__item').find('img').click(function () {
		var set_content     = $(this).parent().html();
		var set_img_data    = $(this).attr('data-img');

		// append content
		$('.sets__popup__content').empty().append( set_content );

		// append big img as background
		// $('.sets__popup__content').find('img').attr('src', set_img_data );
		$('.sets__popup__content').find('img').remove();
		$('.sets__popup__content').prepend('<div class="sets__popup__content__bg" style="background-image: url(' + set_img_data + ');"></div>');



		$('html').toggleClass('sets__popup__active');
	});



	$(document).on('click', '.js-sets__popup', function () {
		$('html').toggleClass('sets__popup__active');
	});



	// add to cart selected set
	$(document).on('click', '.js-set__add_to_cart', function () {
		// get inputs for selected set
		var set_inputs = $(this).parent().find('input');

		$.ajax({
			url:        'index.php?route=checkout/cart/add',
			type:       'post',
			data:       set_inputs,
			dataType:   'json',
			success: function(json) {
				if (json['error']) {
					if (json['error']['option']) {
						for (i in json['error']['option']) {
							var element = $('#input-option' + i.replace('_', '-'));

							if (element.parent().hasClass('input-group')) {
								element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
							}
							else {
								element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
							}

							console.log('error__option__' + i);
						}
					}

					if (json['error']['recurring']) {
						$('select[name="recurring_id"]').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
					}
				}

				if (json['success']) {
					$('html').toggleClass('product_added_to_cart__active');

					cart_update();
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
});


