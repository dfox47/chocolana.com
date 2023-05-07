var $ = jQuery

$(window).bind('load', function() {
	// Replace all SVG images with inline SVG if class .svg
	$('img.js-svg').each(function() {
		const $img      = $(this)
		const imgID     = $img.attr('id')
		const imgClass  = $img.attr('class')
		const imgURL    = $img.attr('src')

		$.get (imgURL, function(data) {
			// Get the SVG tag, ignore the rest
			let $svg = $(data).find('svg')

			// Add replaced image's ID to the new SVG
			if (typeof imgID !== 'undefined') {
				$svg = $svg.attr('id', imgID)
			}

			// Add replaced image's classes to the new SVG
			if (typeof imgClass !== 'undefined') {
				$svg = $svg.attr('class', imgClass+' js-svg-replaced')
			}

			// Remove any invalid XML tags as per http://validator.w3.org
			$svg = $svg.removeAttr('xmlns:a')

			// Replace image with new SVG
			$img.replaceWith($svg)
		}, 'xml')
	})
})