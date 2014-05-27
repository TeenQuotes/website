<?php
/*
|--------------------------------------------------------------------------
| Patterns
|--------------------------------------------------------------------------
|
*/
Route::pattern('quote_id', '[0-9]+');
Route::pattern('country_id', '[0-9]+');
Route::pattern('user_id', '[a-zA-Z0-9_]+');
Route::pattern('decision', 'approve|unapprove');
Route::pattern('fav', 'fav');
Route::pattern('random', 'random');

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
*/
Route::group(['domain' => Config::get('app.domain')], function()
{
	Route::get('/', ['as' => 'home', 'uses' => 'QuotesController@index']);

	/* --- AUTH --- */
	Route::get('signin', ['as' => 'signin', 'uses' => 'AuthController@getSignin']);
	Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);
	Route::post('signin', 'AuthController@postSignin');

	/* --- SEARCH --- */
	Route::get('/search', ['as' => 'search.form', 'uses' => 'SearchController@showForm']);
	Route::post('/search', ['as' => 'search.dispatcher', 'uses' => 'SearchController@dispatcher']);
	Route::get('/search/{query}', ['as' => 'search.results', 'uses' => 'SearchController@getResults']);

	/* --- USERS --- */
	Route::get("/signup", ["as" => "signup", "before" => "guest", "uses" => "UsersController@getSignup"]);
	Route::get('/users/{user_id}/{fav?}', ['as' => 'users.show', 'uses' => 'UsersController@show']);
	Route::put('/users/{user_id}/password', ['as' => 'users.password', 'uses' => 'UsersController@putPassword']);
	Route::put('/users/{user_id}/avatar', ['as' => 'users.avatar', 'uses' => 'UsersController@putAvatar']);
	Route::put('/users/{user_id}/settings', ['as' => 'users.settings', 'uses' => 'UsersController@putSettings']);
	Route::resource('users', 'UsersController', ['only' => ['index', 'store', 'edit', 'update']]);

	/* --- PASSWORD REMINDER --- */
	Route::get('/password/remind', ['as' => 'passwordReminder', 'before' => 'guest', 'uses' => 'RemindersController@getRemind']);
	Route::controller('password', 'RemindersController');

	/* --- QUOTES --- */
	Route::get('/random', ['as' => 'random', 'uses' => 'QuotesController@index']);
	Route::get('/addquote', ['as' => 'addquote', 'before' => 'auth', 'uses' => 'QuotesController@getAddQuote']);
	Route::get('/quote-{quote_id}', function($id)
	{
		return Redirect::route('quotes.show', array($id), 301);
	});
	Route::resource('quotes', 'QuotesController', ['only' => ['index', 'show', 'store']]);

	/* --- COMMENTS --- */
	Route::resource('comments', 'CommentsController', ['only' => ['store']]);

	/* --- FAVORITE --- */
	Route::post('/favorite/{quote_id}', ['as' => 'favorite', 'before' => 'auth', 'uses' => 'FavoritesController@store']);
	Route::post('/unfavorite/{quote_id}', ['as' => 'unfavorite', 'before' => 'auth', 'uses' => 'FavoritesController@destroy']);
});

/* --- ADMIN --- */
Route::group(['before' => 'admin', 'prefix' => 'admin'], function()
{
	// Index
	Route::get('/', ['uses' => 'QuotesAdminController@index', 'as' => 'admin.quotes.index']);
	// Edit
	Route::get('/edit/{quote_id}', ['uses' => 'QuotesAdminController@edit', 'as' => 'admin.quotes.edit']);
	// Update
	Route::put('/update/{quote_id}', ['uses' => 'QuotesAdminController@update', 'as' => 'admin.quotes.update']);
	// Moderation
	Route::post('/moderate/{quote_id}/{decision}', ['uses' => 'QuotesAdminController@postModerate', 'as' => 'admin.quotes.moderate']);
});


Route::group(['domain' => Config::get('app.domainAPI'), 'before' => 'session.remove'], function()
{

	// OAUTH
	Route::post('oauth', function()
	{
	    return AuthorizationServer::performAccessTokenFlow();
	});

	if (!App::environment('local'))
		Route::get('/', ['uses' => 'APIv1Controller@showWelcome']);

	// API v1
	Route::group(['domain' => Config::get('app.domainAPI'), 'before' => 'oauth', 'prefix' => 'v1'], function()
	{
		// COUNTRIES
		Route::get('countries/{country_id?}', ['uses' => 'APIv1Controller@getCountry']);		

		// FAVORITE QUOTES
		Route::post('favorites/{quote_id}', ['uses' => 'APIv1Controller@postFavorite']);
		Route::delete('favorites/{quote_id}', ['uses' => 'APIv1Controller@deleteFavorite']);

		// QUOTES
		Route::get('quotes/{quote_id}', ['uses' => 'APIv1Controller@getSingleQuote']);
		Route::get('quotes/{random?}', ['uses' => 'APIv1Controller@indexQuotes']);
		Route::post('quotes', ['uses' => 'APIv1Controller@postStoreQuote']);

		// USERS
		Route::post('users', ['uses' => 'APIv1Controller@postUsers']);
		Route::get('users/{user_id}', ['uses' => 'APIv1Controller@getSingleUser']);
		Route::put('users/password', ['uses' => 'APIv1Controller@putPassword']);
	});
});