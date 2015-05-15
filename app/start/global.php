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

ClassLoader::addDirectories([

    app_path().'/commands',
    app_path().'/database/seeds',
]);

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

App::error(function (TeenQuotes\Exceptions\HiddenProfileException $exception, $code) {
    $userLogin = Route::input('user_id');

    $data = [
        'title'   => Lang::get('errors.hiddenProfileTitle'),
        'content' => Lang::get('errors.hiddenProfileBody', ['login' => $userLogin]),
    ];

    // Send event to Google Analytics
    JavaScript::put([
        'eventCategory' => 'profile-hidden',
        'eventAction'   => $userLogin,
        'eventLabel'    => URL::current(),
    ]);

    return Response::view('errors.default', $data, 401);
});

App::error(function (Laracasts\Validation\FormValidationException $e, $code) {
    // In the API
    if (Request::wantsJson()) {
        $failedKey = array_keys($e->getErrors()->getMessages())[0];

        return Response::json([
            'status' => 'wrong_'.$failedKey,
            'error'  => $e->getErrors()->first($failedKey),
        ], 400);
    }

    return Redirect::back()
        ->withInput(Input::except(['password', 'avatar']))
        ->withErrors($e->getErrors());
});

App::error(function (TeenQuotes\Exceptions\TQNotFoundException $exception, $code) {
    $resourceName = strtolower(str_replace('NotFoundException', '', class_basename(get_class($exception))));

    if (in_array($resourceName, ['quote', 'user', 'tag', 'token', 'story', 'country'])) {
        $data = [
            'content'   => Lang::get('errors.defaultNotFound', ['resource' => Lang::get('errors.'.$resourceName.'Text')]),
            'title'     => Lang::get('errors.'.$resourceName.'NotFoundTitle'),
            'pageTitle' => Lang::get('errors.'.$resourceName.'NotFoundPageTitle'),
        ];

        // Send event to Google Analytics
        JavaScript::put([
            'eventCategory' => '404',
            'eventAction'   => $resourceName,
            'eventLabel'    => URL::current(),
        ]);

        return Response::view('errors.default', $data, 404);
    }
});

// Handle 404
App::missing(function ($exception) {
    $data = [
        'content' => Lang::get('errors.defaultNotFound', ['resource' => Lang::get('errors.pageText')]),
        'title'   => Lang::get('errors.pageNotFoundTitle'),
    ];

    // Send event to Google Analytics
    JavaScript::put([
        'eventCategory' => '404',
        'eventAction'   => 'unknow',
        'eventLabel'    => URL::current(),
    ]);

    return Response::view('errors.default', $data, 404);
});

// This error handler will be at the end of the stack
App::pushError(function (Exception $exception, $code) {
    Log::error($exception);

    // Show a custom view
    if (App::environment() != 'local') {
        return Response::view('errors.500', ['pageTitle' => 'Oops, something is wrong!'], $code);
    }
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

App::down(function () {
    return Response::view('errors.maintenance', ['pageTitle' => 'Be right back!'], 503);
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
