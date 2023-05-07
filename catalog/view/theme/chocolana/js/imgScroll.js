const changeImgSrc = () => {
	document.querySelectorAll('.js-img-scroll').forEach((e) => {
		if (window.pageYOffset + window.innerHeight > e.offsetTop) {
			e.classList.remove('js-img-scroll')
			const imgSrc = e.dataset.src
			if (imgSrc) e.src = imgSrc
		}
	})
}

changeImgSrc()

window.addEventListener('resize', function() {
	changeImgSrc()
})

window.addEventListener('scroll', function() {
	changeImgSrc()
})
