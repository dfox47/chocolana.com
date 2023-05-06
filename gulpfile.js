const fs            = require('fs')
const concat        = require('gulp-concat')
const config        = JSON.parse(fs.readFileSync('../../config.json'))
const cssMinify     = require('gulp-csso')
const ftp           = require('vinyl-ftp')
const gulp          = require('gulp')
const gutil         = require('gulp-util')
const rename        = require('gulp-rename')
const sass          = require('gulp-sass')
const uglify        = require('gulp-uglify')

// FTP config
const host          = config.host
const password      = config.password
const port          = config.port
const user          = config.user

const remoteTheme       = '/public_html/catalog/view/theme/chocolana/'
const remoteAccount     = remoteTheme + 'template/account/'
const remoteAffiliate   = remoteTheme + 'template/affiliate/'
const remoteCheckout    = remoteTheme + 'template/checkout/'
const remoteCommon      = remoteTheme + 'template/common/'
const remoteError       = remoteTheme + 'template/error/'
const remoteCss         = remoteTheme + 'css/'
const remoteJs          = remoteTheme + 'js/'

const localTheme        = 'www/public_html/catalog/view/theme/chocolana/'
const localAccount      = localTheme + 'template/account/'
const localAffiliate    = localTheme + 'template/affiliate/'
const localCheckout     = localTheme + 'template/checkout/'
const localCommon       = localTheme + 'template/common/'
const localError        = localTheme + 'template/error/'
const localCss          = localTheme + 'css/'
const localJs           = localTheme + 'js/'

function getFtpConnection() {
	return ftp.create({
		host:           host,
		log:            gutil.log,
		password:       password,
		parallel:       3,
		port:           port,
		user:           user
	});
}

const conn = getFtpConnection()



gulp.task('css', function () {
	return gulp.src(localCss + 'styles.scss')
		.pipe(sass())
		.pipe(cssMinify())
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(conn.dest(remoteTheme))
})

gulp.task('html_account', function () {
	return gulp.src(localAccount + '*.twig')
		.pipe(conn.dest(remoteAccount))
})

gulp.task('html_affiliate', function () {
	return gulp.src(localAffiliate + '*.twig')
		.pipe(conn.dest(remoteAffiliate))
})

gulp.task('html_checkout', function () {
	return gulp.src(localCheckout + '*.twig')
		.pipe(conn.dest(remoteCheckout))
})

gulp.task('html_common', function () {
	return gulp.src(localCommon + '*.twig')
		.pipe(conn.dest(remoteCommon))
})

gulp.task('html_error', function () {
	return gulp.src(localError + '*.twig')
		.pipe(conn.dest(remoteError));
})

gulp.task('html_extension', function () {
	const remoteFolder = '/chocolana.com/public_html/catalog/view/theme/chocolana/template/extension/'

	return gulp.src('www/public_html/catalog/view/theme/chocolana/template/extension/**/*.twig')
		.pipe(conn.dest(remoteFolder))
})

gulp.task('html_information', function () {
	const remoteFolder = '/chocolana.com/public_html/catalog/view/theme/chocolana/template/information/'

	return gulp.src('www/public_html/catalog/view/theme/chocolana/template/information/*.twig')
		.pipe(conn.dest(remoteFolder))
})

gulp.task('html_mail', function () {
	const remoteFolder = '/chocolana.com/public_html/catalog/view/theme/chocolana/template/mail/'

	return gulp.src('www/public_html/catalog/view/theme/chocolana/template/mail/*.twig')
		.pipe(conn.dest(remoteFolder))
})

gulp.task('html_product', function () {
	const remoteFolder = '/chocolana.com/public_html/catalog/view/theme/chocolana/template/product/'

	return gulp.src('www/public_html/catalog/view/theme/chocolana/template/product/*.twig')
		.pipe(conn.dest(remoteFolder))
})

gulp.task('lang_bg', function () {
	const remoteFolder = '/chocolana.com/public_html/catalog/language/bg-bg/'

	return gulp.src('www/public_html/catalog/language/bg-bg/*.php')
		.pipe(conn.dest(remoteFolder))
})

gulp.task('lang_en', function () {
	const remoteFolder = '/chocolana.com/public_html/catalog/language/en-gb/'

	return gulp.src('www/public_html/catalog/language/en-gb/*.php')
		.pipe(conn.dest(remoteFolder))
})

gulp.task('lang_ru', function () {
	const remoteFolder = '/chocolana.com/public_html/catalog/language/ru-ru/'

	return gulp.src('www/public_html/catalog/language/ru-ru/*.php')
		.pipe(conn.dest(remoteFolder))
})



gulp.task('copy_css', function () {
	return gulp.src(localCss + '**/*')
		.pipe(conn.dest(remoteCss))
})

gulp.task('copy_js', function () {
	return gulp.src(localJs + '**/*')
		.pipe(conn.dest(remoteJs))
})

gulp.task('copy_php', function () {
	return gulp.src(localTheme + '*.php')
		.pipe(conn.dest(remoteTheme))
})

gulp.task('js', function () {
	return gulp.src([
		localJs + 'jquery-3.3.1.js',
		localJs + 'jquery-ui.js',
		localJs + 'jquery.ui.touch-punch.min.js',
		localJs + 'jquery.mask.js',
		localJs + 'owl.carousel.min.js',
		localJs + 'cart.js',
		localJs + '**/*.js'
	])
		.pipe(concat('all.js'))
		.pipe(uglify())
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(conn.dest(remoteTheme))
})

gulp.task('watch', function() {
	gulp.watch('www/public_html/catalog/language/bg-bg/**/*',       gulp.series('lang_bg'))
	gulp.watch('www/public_html/catalog/language/en-gb/**/*',       gulp.series('lang_en'))
	gulp.watch('www/public_html/catalog/language/ru-ru/**/*',       gulp.series('lang_ru'))
	gulp.watch(localTheme + '*.php',                                gulp.series('copy_php'))
	gulp.watch(localAccount + '*.twig',                             gulp.series('html_account'))
	gulp.watch(localAffiliate + '*.twig',                           gulp.series('html_affiliate'))
	gulp.watch(localTheme + 'template/checkout/*.twig',             gulp.series('html_checkout'))
	gulp.watch(localTheme + 'template/common/*.twig',               gulp.series('html_common'))
	gulp.watch(localTheme + 'template/error/*.twig',                gulp.series('html_error'))
	gulp.watch(localTheme + 'template/extension/**/*.twig',         gulp.series('html_extension'))
	gulp.watch(localTheme + 'template/information/*.twig',          gulp.series('html_information'))
	gulp.watch(localTheme + 'template/mail/*.twig',                 gulp.series('html_mail'))
	gulp.watch(localTheme + 'template/product/*.twig',              gulp.series('html_product'))
	gulp.watch(localCss + '**/*',                                   gulp.series('css', 'copy_css'))
	gulp.watch(localJs + '**/*',                                    gulp.series('js', 'copy_js'))
})

gulp.task('default', gulp.series('watch'))