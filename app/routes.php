<?php
/*
|--------------------------------------------------------------------------
| Patterns
|--------------------------------------------------------------------------
|
*/
Route::pattern('decision', 'approve|unapprove|alert');
Route::pattern('display_type', 'favorites|comments');

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
*/

/* --- MAIN WEBSITE --- */
Route::group(['domain' => Config::get('app.domain')], function()
{
	Route::get('/', ['as' => 'home', 'uses' => 'QuotesController@index']);
	
	/* --- AUTH --- */
	Route::get('signin', ['as' => 'signin', 'uses' => 'AuthController@getSignin']);
	Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);
	Route::post('signin', 'AuthController@postSignin');

	/* --- SEARCH --- */
	Route::get('search', ['as' => 'search.form', 'uses' => 'SearchController@showForm']);
	Route::post('search', ['as' => 'search.dispatcher', 'uses' => 'SearchController@dispatcher']);
	Route::get('search/{query}', ['as' => 'search.results', 'uses' => 'SearchController@getResults']);

	/* --- USERS --- */
	Route::delete('users', ['as' => 'users.delete', 'before' => 'auth', 'uses' => 'UsersController@destroy']);
	Route::get("signup", ["as" => "signup", "before" => "guest", "uses" => "UsersController@getSignup"]);
	Route::get('users/{user_id}/{display_type?}', ['as' => 'users.show', 'uses' => 'UsersController@show']);
	Route::put('users/{user_id}/password', ['as' => 'users.password', 'uses' => 'UsersController@putPassword']);
	Route::put('users/{user_id}/avatar', ['as' => 'users.avatar', 'uses' => 'UsersController@putAvatar']);
	Route::put('users/{user_id}/settings', ['as' => 'users.settings', 'uses' => 'UsersController@putSettings']);
	Route::post('users/loginvalidator', ['as' => 'users.loginValidator', 'uses' => 'UsersController@postLoginValidator']);
	Route::resource('users', 'UsersController', ['only' => ['index', 'store', 'edit', 'update']]);

	/* --- PASSWORD REMINDER --- */
	Route::get('password/remind', ['as' => 'passwordReminder', 'before' => 'guest', 'uses' => 'RemindersController@getRemind']);
	Route::controller('password', 'RemindersController');

	/* --- QUOTES --- */
	Route::get('random', ['as' => 'random', 'uses' => 'QuotesController@index']);
	Route::get('addquote', ['as' => 'addquote', 'before' => 'auth', 'uses' => 'QuotesController@create']);
	Route::get('quote-{quote_id}', ['uses' => 'QuotesController@redirectOldUrl']);
	Route::post('quotes/favorites-info', ['as' => 'quotes.favoritesInfo', 'uses' => 'QuotesController@getDataFavoritesInfo']);
	Route::resource('quotes', 'QuotesController', ['only' => ['index', 'show', 'store']]);

	/* --- COMMENTS --- */
	Route::resource('comments', 'CommentsController', ['only' => ['store', 'destroy']]);

	/* --- FAVORITE --- */
	Route::post('favorite/{quote_id}', ['as' => 'favorite', 'before' => 'auth', 'uses' => 'QuotesFavoriteController@store']);
	Route::post('unfavorite/{quote_id}', ['as' => 'unfavorite', 'before' => 'auth', 'uses' => 'QuotesFavoriteController@destroy']);
	
});

/* --- ADMIN --- */
Route::group(['domain' => Config::get('app.domainAdmin'), 'before' => 'admin'], function()
{
	// Index
	Route::get('/', ['uses' => 'QuotesAdminController@index', 'as' => 'admin.quotes.index']);
	// Edit
	Route::get('edit/{quote_id}', ['uses' => 'QuotesAdminController@edit', 'as' => 'admin.quotes.edit']);
	// Update
	Route::put('update/{quote_id}', ['uses' => 'QuotesAdminController@update', 'as' => 'admin.quotes.update']);
	// Moderation
	Route::post('moderate/{quote_id}/{decision}', ['uses' => 'QuotesAdminController@postModerate', 'as' => 'admin.quotes.moderate']);
});

/* --- API --- */
Route::group(['domain' => Config::get('app.domainAPI'), 'before' => 'session.remove', 'namespace' => 'TeenQuotes\Api\V1\Controllers'], function()
{
	// OAuth
	Route::post('oauth', ['uses' => 'APIGlobalController@postOauth']);

	// Welcome page
	Route::get('/', ['uses' => 'APIGlobalController@showWelcome']);
});