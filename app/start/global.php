<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',
	app_path().'/API/v1/controllers',
));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(HiddenProfileException $exception, $code)
{
	$data = [
		'title'   => Lang::get('errors.hiddenProfileTitle'),
		'content' => Lang::get('errors.hiddenProfileBody', ['login' => Route::input('user_id')]),
	];

	return Response::view('errors.default', $data, 401);
});

// Catch QuoteNotFoundException, UserNotFoundException, TokenNotFoundException
App::error(function(TQNotFoundException $exception, $code)
{
	$resourceName = strtolower(str_replace("NotFoundException", "", get_class($exception)));

	if (in_array($resourceName, ['quote', 'user', 'token', 'story'])) {
		$data = [
			'content' => Lang::get('errors.defaultNotFound', ['resource' => Lang::get('errors.'.$resourceName.'Text')]),
			'title' => Lang::get('errors.'.$resourceName.'NotFoundTitle')
		];

		return Response::view('errors.default', $data, 404);
	}
});

// Handle 404
App::missing(function($exception)
{
	$data = [
		'content' => Lang::get('errors.defaultNotFound', ['resource' => Lang::get('errors.pageText')]),
		'title' => Lang::get('errors.pageNotFoundTitle')
	];

	return Response::view('errors.default', $data, 404);
});

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

/*
|--------------------------------------------------------------------------
| Additional routes
|--------------------------------------------------------------------------
|
*/
require app_path().'/API/v1/routes.php';


/*
|--------------------------------------------------------------------------
| View composers
|--------------------------------------------------------------------------
|
*/
// Usage: 'ClassComposer' => array('view.name.1', 'view.name.2'),
View::composers([
	// Show a user's profile
	'TeenQuotes\Composers\Users\ProfileComposer'     => ['users.show'],
	// Self edit user's profile
	'TeenQuotes\Composers\Users\ProfileEditComposer' => ['users.edit'],
	// Reset a password with a token
	'TeenQuotes\Composers\Password\ResetComposer'    => ['password.reset'],
	// Associated URLs: ['home', 'contact', 'apps', 'signin', 'legal', 'signup', 'password/remind', 'random', 'addquote'],
	'TeenQuotes\Composers\Pages\SimplePageComposer'  => ['quotes.index', 'contact.show', 'apps.download', 'auth.signin', 'legal.show', 'auth.signup', 'password.remind', 'quotes.addquote'],

]);