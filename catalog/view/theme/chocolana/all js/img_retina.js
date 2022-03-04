var $ = jQuery.noConflict();



$(window).on('load', function () {
	var retina = window.devicePixelRatio > 1;

	if (retina) {
		var img_to_replace = $('img.js-retina').get();

		for (var i=0,l=img_to_replace.length; i<l; i++) {
			var src = img_to_replace[i].src;

			src = src.replace(/\.(png|jpg|gif)+$/i, '_retina.$1');

			img_to_replace[i].src = src;
		}
	}
});


