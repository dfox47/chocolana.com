const fs            = require('fs')
const concat        = require('gulp-concat')
const config        = JSON.parse(fs.readFileSync('../../config.json'))
const cssMinify     = require('gulp-csso')
const ftp           = require('vinyl-ftp')
const gulp          = require('gulp')
const gutil         = require('gulp-util')
const rename        = require('gulp-rename')
const sass          = require('gulp-sass')(require('sass'))
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
const remoteExtension   = remoteTheme + 'template/extension/'
const remoteInformation = remoteTheme + 'template/information/'
const remoteLang        = '/public_html/catalog/language/'
const remoteLangBg      = remoteLang + 'bg-bg/'
const remoteLangEn      = remoteLang + 'en-gb/'
const remoteLangRu      = remoteLang + 'ru-ru/'
const remoteMail        = remoteTheme + 'template/mail/'
const remoteProduct     = remoteTheme + 'template/product/'
const remoteCss         = remoteTheme + 'css/'
const remoteJs          = remoteTheme + 'js/'

const localTheme        = 'catalog/view/theme/chocolana/'
const localAccount      = localTheme + 'template/account/'
const localAffiliate    = localTheme + 'template/affiliate/'
const localCheckout     = localTheme + 'template/checkout/'
const localCommon       = localTheme + 'template/common/'
const localError        = localTheme + 'template/error/'
const localExtension    = localTheme + 'template/extension/'
const localInformation  = localTheme + 'template/information/'
const localLang         = 'catalog/language/'
const localLangBg       = localLang + 'bg-bg/'
const localLangEn       = localLang + 'en-gb/'
const localLangRu       = localLang + 'ru-ru/'
const localMail         = localTheme + 'template/mail/'
const localProduct      = localTheme + 'template/product/'
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
	return gulp.src(localAccount + '**/*')
		.pipe(conn.dest(remoteAccount))
})

gulp.task('html_affiliate', function () {
	return gulp.src(localAffiliate + '**/*')
		.pipe(conn.dest(remoteAffiliate))
})

gulp.task('html_checkout', function () {
	return gulp.src(localCheckout + '**/*')
		.pipe(conn.dest(remoteCheckout))
})

gulp.task('html_common', function () {
	return gulp.src(localCommon + '**/*')
		.pipe(conn.dest(remoteCommon))
})

gulp.task('html_error', function () {
	return gulp.src(localError + '**/*')
		.pipe(conn.dest(remoteError));
})

gulp.task('html_extension', function () {
	return gulp.src(localExtension + '**/*')
		.pipe(conn.dest(remoteExtension))
})

gulp.task('html_information', function () {
	return gulp.src(localInformation + '**/*')
		.pipe(conn.dest(remoteInformation))
})

gulp.task('html_mail', function () {
	return gulp.src(localMail + '**/*')
		.pipe(conn.dest(remoteMail))
})

gulp.task('html_product', function () {
	return gulp.src(localProduct + '**/*')
		.pipe(conn.dest(remoteProduct))
})

gulp.task('lang_bg', function () {
	return gulp.src(localLangBg + '**/*')
		.pipe(conn.dest(remoteLangBg))
})

gulp.task('lang_en', function () {
	return gulp.src(localLangEn + '**/*')
		.pipe(conn.dest(remoteLangEn))
})

gulp.task('lang_ru', function () {
	return gulp.src(localLangRu + '**/*')
		.pipe(conn.dest(remoteLangRu))
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
	gulp.watch(localLangBg + '**/*',        gulp.series('lang_bg'))
	gulp.watch(localLangEn + '**/*',        gulp.series('lang_en'))
	gulp.watch(localLangRu + '**/*',        gulp.series('lang_ru'))
	gulp.watch(localTheme + '*.php',        gulp.series('copy_php'))
	gulp.watch(localAccount + '**/*',       gulp.series('html_account'))
	gulp.watch(localAffiliate + '**/*',     gulp.series('html_affiliate'))
	gulp.watch(localCheckout + '**/*',      gulp.series('html_checkout'))
	gulp.watch(localCommon + '**/*',        gulp.series('html_common'))
	gulp.watch(localError + '**/*',         gulp.series('html_error'))
	gulp.watch(localExtension + '**/*',     gulp.series('html_extension'))
	gulp.watch(localInformation + '**/*',   gulp.series('html_information'))
	gulp.watch(localMail + '**/*',          gulp.series('html_mail'))
	gulp.watch(localProduct + '**/*',       gulp.series('html_product'))
	gulp.watch(localCss + '**/*',           gulp.series('css', 'copy_css'))
	gulp.watch(localJs + '**/*',            gulp.series('js', 'copy_js'))
})

gulp.task('default', gulp.series('watch'))