var $ = jQuery.noConflict();



$(window).on('load', function () {
	// $('.lang ul img').on('click', function(e) {
	$('.lang ul span').on('click', function(e) {
		e.preventDefault();

		$('.lang input[name=\'code\']').val($(this).attr('data-name'));

		$('.lang').submit();
	});
});


