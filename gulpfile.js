var elixir = require('laravel-elixir');
var clean = require('gulp-clean');
var gulp = require('gulp');
require('laravel-elixir-compass');

var inputJSDir = 'ressources/js',
	inputSASSDir = 'ressources/sass/',
	outputCSS = 'public/assets/css',
	inputCSS = 'public/assets/css',
	outputJS = 'public/assets/js/';

elixir(function(mix) {
	mix.compass('screen.scss', outputCSS, {
			sass: inputSASSDir
		})
		.styles([
			'cosmo.css',
			'animate.css',
			'font-awesome.css',
			'flags.css',
			'screen.css'
		], outputCSS + '/styles.min.css', inputCSS)
		.scripts([
			'jquery-2.1.0.min.js',
			'bootstrap.min.js',
			'wow.min.js',
			'mailgun-validator.js',
			'app.js'
		], outputJS + 'scripts.min.js', inputJSDir)
		.version([
			outputCSS + '/styles.min.css',
			outputJS + '/scripts.min.js'
		]);
});
