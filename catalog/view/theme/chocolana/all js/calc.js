var $ = jQuery.noConflict();



$(window).on('load', function () {
	// check if input not empty at page load
	calc__input_check();

	// on calc form submit
	calc__form__submit();

	// check current box type & calc price
	current_box_type();



	// make buttons draggable [START]
	// mobile
	if ( isMobile ) {}
	// desktop
	// else {
		$('.calc__btns').find('> span').draggable({
			zIndex: 99,
			helper: 'clone',
			stop: function(event, ui) {
				$(ui.helper).clone(true).removeClass('box ui-draggable ui-draggable-dragging').addClass('box-clone').appendTo('body').remove();
			}
		});
	// }
	// make buttons draggable [END]



	// make box droppable
	$('.calc__box').droppable({
		drop: function(event, ui) {
			var data_btn = ui.draggable.attr('data-btn');

			$(this).attr('data-type', data_btn);

			$(this).addClass('calc__box_full').removeClass('calc__input_full error');

			// change input value to background img number
			$(this).find('input').val('data_btn__' + data_btn);



			check__calc_box__val();
		}
	});



	$('.calc__btns').find('> span').click(function () {
		var data_sweet_link = $(this).attr('data-sweet-link');

		$('.assortment__list').find('.assortment__item[data-sweet="' + data_sweet_link + '"]').click();
	});



	// focus on click on box, not only on input
	$('.calc__box').click(function () {
		$(this).find('input').focus();
	});



	// remove choosen item \ empty input
	$('.calc').on('click', '.calc__empty', function() {
		// clear background
		$(this).parent().removeClass('calc__box_full').attr('data-type','');

		// clear input value
		$(this).parent().removeClass('calc__input_full').find('input').val('');

		check__calc_box__val();
	});

	$('.calc').on('click', '.js-calc__clear', function() {
		// clear background
		$('.calc__box').removeClass('calc__box_full calc__input_full').attr('data-type','').find('input').val('');

		check__calc_box__val();
	});



	// choose box type: 5x5 or 7x2
	$('.calc__box_type').find('label').click(function () {
		setTimeout(function() {
			current_box_type();

			check__calc_box__val();
		}, 100);
	});



	// allow only cyrillic letters
	$('.calc__layout').find('input').on('blur keyup paste', function (e) {
		// if backspace pressed
		if ( e.keyCode == 8 || e.keyCode == 46) {
			var current_input       = $(this);
			var current_input_val   = current_input.val();

			if ( current_input_val.length ) {
				current_input.parent().find('.calc__empty').click();

				current_input.parent().find('input').focus();
			}
			else {
				var prev_input          = $(this).parent().prev().find('input');
				var prev_input_val      = prev_input.val();

				if ( prev_input.length && prev_input_val.length ) {
					prev_input.parent().find('.calc__empty').click();
				}
			}
		}
		else {
			// allow only cyrillic letters
			var node = $(this);
			node.val(node.val().replace(/[^А-ЯЁа-яё0-9]/g, '') );



			// focus on next input
			if ( $(this).val().length > 0 ) {
				calc__input_check();

				// check if next input got background
				$(this).parent().nextAll().not('.calc__box_full').first().find('input').focus();
			}
		}
	});
});



// AJAX calc form
function calc__form__submit() {
	$('.calc').submit(calc__submit);



	function calc__submit() {
		console.log('submit');
		var calc__form          = $(this);
		var calc__box_type      = parseInt( $('.calc__box_type').find('input:checked').parent().attr('data-box-type') );
		var calc__next_step     = 0;

		// to get info only from selected layout type: 5x5 or 7x2
		var calc__layout        = '.calc__layout_' + calc__box_type;



		// check only active layout
		// check if all boxes are filled with text or background-image
		$('.calc__layout').find('> div.active').find('.calc__box').each(function () {
			var calc__input_val     = $(this).find('input').val();
			var calc__box_type      = $(this).attr('data-type');

			if ( $(this).hasClass('calc__input_full') && parseInt(calc__input_val) > 1 ) {
				console.log( calc__input_val );

				var calc__input_val_sliced = $(this).find('input').val( calc__input_val.slice(0,1) );


				console.log( calc__input_val_sliced );
				// $("#about").animate({right: "-700px"}, 2000);
			}



			// if value or background exist
			if ( calc__input_val.length || calc__box_type.length ) {
				$(this).removeClass('error');

				calc__next_step = 1;
			}
			else {
				$(this).addClass('error');

				$('body, html').animate({
					scrollTop: $(this).offset().top - 200
				}, 700);

				calc__next_step = 0;

				return false;
			}
		});



		if ( calc__next_step > 0 ) {
			// add to cart [START]
			$.ajax({
				url:        'index.php?route=checkout/cart/add',
				type:       'post',
				data:       $(calc__layout + ' input[type="text"],' + calc__layout + ' input[type="hidden"],' + calc__layout + ' input[type="radio"]:checked,' + calc__layout + ' input[type="checkbox"]:checked,' + calc__layout + ' select,' + calc__layout + ' textarea'),
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
			// add to cart [END]
		}



		return false;
	}
}



// check if input not empty
function calc__input_check() {
	if ( $('.calc__box').find('input').length ) {
		$('.calc__box').find('input').each(function () {
			var input_val = $(this).val();

			// if input is background img
			if ( input_val.indexOf('data_btn__') != -1 ) {
				$(this).parent().removeClass('calc__input_full error').addClass('calc__box_full');

				// $(this).parent().attr('data-type', parseInt(input_val, 10));
				$(this).parent().attr('data-type', input_val.replace('data_btn__' ,''));
			}
			// if input is letter
			else if ( input_val.length ) {
				$(this).parent().addClass('calc__input_full');
				$(this).parent().removeClass('error');
			}
		});
	}
}



// check if value or background exist
function calc__box_check() {
	if ( $('.calc__box').find('input').length ) {
		$('.calc__box').find('input').each(function () {
			var input_val = $(this).val();

			if ( input_val.length ) {
				$(this).parent().addClass('calc__input_full');
			}
			else {
				$(this).parent().removeClass('calc__input_full');
			}
		});
	}
}



// do not let user add more than 4 mendal or cherry sweets
function check__calc_box__val() {
	var box_with_mendal     = 0;
	var calc__box_type      = $('.calc__box_type').find('input:checked').val();
	var max_sweets          = 2;

	// big box
	if ( calc__box_type == '1' ) {
		max_sweets = 3;
	}
	// small box
	else {
		max_sweets = 1;
	}

	$('.calc__layout__wrap.active').find('.calc__box').each(function () {
		var input_val = $(this).find('input').val();

		if (
			input_val == 'data_btn__4' ||
			input_val == 'data_btn__7'
		) {
			box_with_mendal++;
		}
	});



	// count boxes with special sweets
	$('.calc__boxX').each(function () {
		var input_val = $(this).find('input').val();

		if (
			input_val == 'data_btn__4' ||
			input_val == 'data_btn__7'
		) {
			box_with_mendal++;
		}
	});



	if ( box_with_mendal > max_sweets ) {
		$('.calc__btns').find('span[data-btn="4"]').hide();
		$('.calc__btns').find('span[data-btn="7"]').hide();

		$('.calc__btns__helper').addClass('active');
	}
	else {
		$('.calc__btns').find('span[data-btn="4"]').show();
		$('.calc__btns').find('span[data-btn="7"]').show();

		$('.calc__btns__helper').removeClass('active');
	}
}



// check current box type [START]
function current_box_type() {
	var calc__box_type = parseInt( $('.calc__box_type').find('input:checked').parent().attr('data-box-type') );

	$('.calc__layout').find('> div').removeClass('active');

	$('.calc__layout').find('> div[data-box-type="' + calc__box_type + '"]').addClass('active');

	if ( calc__box_type == 1 ) {
		$('.calc__price').find('span').text('25');
	}
	else {
		$('.calc__price').find('span').text('18');
	}
}
// check current box type [END]


