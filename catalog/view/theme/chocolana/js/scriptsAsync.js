// async scripts
const $asyncFolder = '/catalog/view/theme/chocolana/js/async/'

const scripts = [
	$asyncFolder + 'facebookPixel.js',
	$asyncFolder + 'googleTag.js',
	$asyncFolder + 'yandexMetrika.js',
	// 'https://www.googletagmanager.com/gtag/js?id=UA-129076025-1'
]

document.addEventListener('DOMContentLoaded', () => {
	setTimeout(function() {
		for (let i = 0; i < scripts.length; i++) {
			const $script = document.createElement('script')
			$script.src = scripts[i]
			document.head.appendChild($script)
		}
	}, 5000)
})