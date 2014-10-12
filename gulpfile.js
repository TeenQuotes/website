var gulp = require('gulp');

var jshint = require('gulp-jshint');
var compass = require('gulp-compass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var minifyCSS = require('gulp-minify-css');
var rename = require('gulp-rename');

var inputJSDir = 'ressources/js/';
var inputSASSDir = 'ressources/sass/';

var outputCSS = 'public/assets/css';
var inputCSS = 'public/assets/css/';
var outputJS = 'public/assets/js';

// Lint Task
gulp.task('lint', function() {
	return gulp.src(inputJSDir + 'app.js')
		.pipe(jshint())
		.pipe(jshint.reporter('default'));
});

// Compile SASS
gulp.task('compass', function() {
	return gulp.src(inputSASSDir + '*.scss')
	.pipe(compass({
		config_file: './config.rb',
		sass: inputSASSDir,
		css: outputCSS
	}))
	.pipe(gulp.dest(outputCSS));
});

// Concatenate and minify JS
gulp.task('scripts', function() {
	return gulp.src([
			inputJSDir + 'jquery-2.1.0.min.js',
			inputJSDir + 'wow.min.js',
			inputJSDir + 'bootstrap.min.js',
			inputJSDir + 'mailgun-validator.js',
			inputJSDir + 'app.js'
		])
		.pipe(concat('scripts.min.js'))
		.pipe(gulp.dest(outputJS))
		.pipe(uglify())
		.pipe(gulp.dest(outputJS));
});

// Concatenate and minify CSS
gulp.task('css', function() {
	return gulp.src([
			inputCSS + 'cosmo.css',
			inputCSS + 'animate.css',
			inputCSS + 'font-awesome.css',
			inputCSS + 'screen.css'
		])
		.pipe(concat('styles.min.css'))
		.pipe(gulp.dest(outputCSS))
		.pipe(minifyCSS())
		.pipe(gulp.dest(outputCSS));
});

// Watch files for changes
gulp.task('watch', function() {
	gulp.watch(inputJSDir + '*.js', ['lint', 'scripts']);
	gulp.watch(inputSASSDir + '*.scss', ['compass']);
	gulp.watch(inputCSS + '*.css', ['css']);
});

// Default Task
gulp.task('default', ['lint', 'compass', 'scripts', 'css', 'watch']);