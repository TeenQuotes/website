<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::pattern('quote_id', '[0-9]+');
Route::pattern('user_id', '[a-zA-Z0-9_]+');
Route::pattern('decision', 'approve|unapprove');
Route::pattern('fav', 'fav');

Route::get('/', ['as' => 'home', 'uses' => 'QuotesController@index']);

/* --- AUTH --- */
Route::get('signin', ['as' => 'signin', 'uses' => 'AuthController@getSignin']);
Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);
Route::post('signin', 'AuthController@postSignin');

/* --- USERS --- */
Route::get("/signup", ["as" => "signup", "before" => "guest", "uses" => "UsersController@getSignup"]);
Route::get('/users/{user_id}/{fav?}', ['as' => 'users.show', 'uses' => 'UsersController@show']);
Route::put('/users/{user_id}/password', ['as' => 'users.password', 'uses' => 'UsersController@putPassword']);
Route::put('/users/{user_id}/avatar', ['as' => 'users.avatar', 'uses' => 'UsersController@putAvatar']);
Route::put('/users/{user_id}/settings', ['as' => 'users.settings', 'uses' => 'UsersController@putSettings']);
Route::resource('users', 'UsersController', ['only' => ['index', 'store', 'edit', 'update']]);

/* --- PASSWORD REMINDER --- */
// Adding this route just to have a named route to the password reminder page
Route::get('/password/remind', ['as' => 'passwordReminder', 'before' => 'guest', 'uses' => 'RemindersController@getRemind']);
Route::controller('password', 'RemindersController');

/* --- QUOTES --- */
Route::get('/random', ['as' => 'random', 'uses' => 'QuotesController@index']);
Route::get('/addquote', ['as' => 'addquote', 'before' => 'auth', 'uses' => 'QuotesController@getAddQuote']);
Route::resource('quotes', 'QuotesController', ['only' => ['index', 'show', 'store']]);

/* --- COMMENTS --- */
Route::resource('comments', 'CommentsController', ['only' => ['store']]);

/* --- FAVORITE --- */
Route::post('/favorite/{quote_id}', ['as' => 'favorite', 'before' => 'auth', 'uses' => 'FavoritesController@store']);
Route::post('/unfavorite/{quote_id}', ['as' => 'unfavorite', 'before' => 'auth', 'uses' => 'FavoritesController@destroy']);

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
